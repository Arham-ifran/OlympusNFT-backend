<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategories;
use App\Models\Blogs;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use File;
use Image;
use Auth;
class BlogsController extends Controller
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Blog')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = Blogs::orderByDesc('id');

            $datatable = DataTables::of($db_record);


            $datatable->editColumn('category', function ($row) {
                return $row->blogCategory->title;
            });
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Blog')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/blogs/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-left',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active'
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= Form::close();



                    $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/blogs/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';

                }
                if(Auth::user()->can('Delete Blog')){
                    $actions .= Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/blogs', encode($row->id)],
                        'style' => 'display:inline'
                    ]);
                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Faq"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }


                $actions .= '';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['category', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.blogs.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Blog')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data['action'] = "Add";
        if (old()) {
            $data['blog'] = old();
        }
        $data['blogCategories'] = BlogCategories::where('is_active', 1)->get();
        return view('admin.blogs.form')->with($data);
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Blog')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['action'] = "Edit";
        $data['blogCategories'] = blogCategories::where('is_active', 1)->get();
        $data['blog'] = Blogs::findOrFail($id);
        return view('admin.blogs.form')->with($data);
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
            $blog = Blogs::findOrFail($input['id']);
            $this->validate($request, [
                'category_id' => 'required',
                'title' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'required',
            ]);


            $blog->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 57, '', ['item_id' => $blog->id]);
            Alert::success('Success', 'Blog updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'category_id' => 'required',
                'title' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'required',
            ]);

            $blog = Blogs::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 56, '', ['item_id' => $blog->id]);
            Alert::success('Success', 'Blog added successfully!')->persistent('Close')->autoclose(5000);
        }
        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/blogs/' . $blog->id;
        if (!File::exists(public_path() . '/storage/uploads/blogs/' . $blog->id)) {

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

                $file_temp_name = 'blog-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/blogs/' . $blog->id . '/' . $blog->image;

                if (file_exists($old_file) && !empty($blog->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/blogs/') . $blog->id . '/' . $file_temp_name;

                if ($type != 'svg') {
                    if ($size_mbs >= 2) {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    } else {
                        $img = Image::make($file)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    }
                } else {
                    $file->move($path, $file_temp_name);
                }

                $image['image'] = $file_temp_name;
                $blog->update($image);
            }
        }
        return redirect('admin/blogs');
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Blog')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        Blogs::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 58, '', ['item_id' => $id]);
        Alert::success('Success', 'Blog deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Blogs::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 59, '', ['item_id' => $id]);
            Alert::success('Success', 'Blog Page status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. CMS Page status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
