<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banners;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use Auth;
use File;
use Image;

class BannerController extends Controller
{


    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('View Banner')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = Banners::orderByDesc('id');

            $datatable = DataTables::of($db_record);

            $datatable = $datatable->editColumn('image', function ($row) {

                return '<img src="' . checkImage(asset('storage/uploads/banners/' . $row->id . '/' . $row->image)) . '" class="image-display " id="image" style="width:  80px;border:  1px solid #ccc;" />';;
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<i class="badge badge-primary"></i>';
                if ($row->is_active == 1) {
                    $status = ' <label class="badge badge-success">Active</label>';
                } else if ($row->is_active == 0) {
                    $status = '<label class="badge badge-warning">Inactive</label>';
                }

                return $status;
            });

            $datatable = $datatable->addColumn('action', function ($row) {

                $actions = '';


                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/banners/update-status'],
                    'style' => 'display:table;margin-right:10px;',
                    'class' => 'float-sm-left',
                    'id' => 'statusForm' . $row->id
                ]);
                $actions .= Form::hidden('id', encode($row->id));
                $actions .= Form::select('is_active', [
                    '0' => 'Inactive',
                    '1' => 'Active'
                ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/banners/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                $actions .= Form::close();

                $actions .= Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/banners', encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Banner"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                $actions .= Form::close();



                $actions .= '';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['image', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.banners.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Add Banner')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data['action'] = "Add";
        return view('admin.banners.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Edit Banner')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['action'] = "Edit";
        $data['banner'] = Banners::findOrFail($id);
        return view('admin.banners.form')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $input = $request->all();

        if ($input['action'] == 'Edit') {
            $this->validate($request, [
                // 'image' => 'required|file|image|mimes:jpg,jpeg,png,svg',
            ]);

            $banner = Banners::findOrFail($input['id']);
            $banner->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 6, '', ['item_id' => $banner->id]);
            Alert::success('Success', 'Category updated successfully!')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'image' => 'required|file|image|mimes:jpg,jpeg,png,svg',
            ]);

            $banner = Banners::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 5, '', ['item_id' => $banner->id]);
            Alert::success('Success', 'Category added successfully!')->persistent('Close')->autoclose(5000);
        }
        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/banners/' . $banner->id;
        if (!File::exists(public_path() . '/storage/uploads/banners/' . $banner->id)) {

            File::makeDirectory($upload_path, 0777, true);
        }

        if (!empty($request->files) && $request->hasFile('image')) {

            $file      = $request->file('image');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'banner-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/banners/' . $banner->id . '/' . $banner->image;

                if (file_exists($old_file) && !empty($banner->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/banners/') . $banner->id . '/' . $file_temp_name;

                if ($type != 'svg') {
                    if ($size_mbs >= 2) {
                        $img = Image::make($file)->save($path);
                    } else {
                        $img = Image::make($file)->save($path);
                    }
                } else {
                    $file->move($path, $file_temp_name);
                }

                $image['image'] = $file_temp_name;
                $banner->update($image);
            }
        }
        return redirect('admin/banners');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {

        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('Delete Banner')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $category = Banners::findOrFail(decode($id));

        $category->delete();
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 7, '', ['item_id' => $id]);
        Alert::success('Success', 'Banner deleted successfully!')->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }

    // Update Status
    public function update_status(Request $request)
    {
        $input = $request->all();

        $is_active = $input['is_active'];
        $id = decode($input['id']);

        if ($is_active <> '' && $id <> '') {
            $data = array(
                'is_active' => $is_active,
            );

            Banners::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 8, '', ['item_id' => $id]);
            Alert::success('Success', 'Banner status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Banner status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
