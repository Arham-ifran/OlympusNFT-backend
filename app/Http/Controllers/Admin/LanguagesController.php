<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Languages;
use Illuminate\Http\Request;
use Alert;
use Image;
use Hash;
use File;
use View;
use DataTables;
use Form;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Auth;
class LanguagesController extends Controller
{

    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Language')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data = Languages::get();
            $datatable = Datatables::of($data);
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';

            });


            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Language')){
                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/languages/update-status'],
                    'style' => 'display:table;margin-right:10px;',
                    'class' => 'float-sm-right',
                    'id' => 'statusForm' . $row->id
                ]);
                $actions .= Form::select('is_active', [
                    '0' => 'Inactive',
                    '1' => 'Active',
                ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/languages/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                $actions .= Form::hidden('id', encode($row->id));
                $actions .= Form::close();
                }

                if(Auth::user()->can('Delete Language')){
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/languages', encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Category"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
                return $actions;
            });



            $datatable = $datatable->rawColumns(['status', 'action']);
            return $datatable->make(true);
        }

        return view('admin.languages.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Language')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";


        if (old()) {
            $data['language'] = old();
        }

        return view('admin.languages.form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $input = $request->all();

        if ($input['action'] == 'Edit') {

        $this->validate($request, [
                'name' => ['required','string','max:100',Rule::unique('languages')->ignore($input['id'])],
                'code' => ['required','max:3']
              ]);

            $language = Languages::findOrFail($input['id']);

            $language->update($input);

            \App\Events\UserEvents::dispatch('admin', 61, '', ['item_id' => $language->id]);
            Alert::success('Success', 'Faq Category updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => ['required','string','max:100',Rule::unique('languages')],
                'code' => ['required','max:3']
            ]);


            $language = Languages::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 60, '', ['item_id' => $language->id]);

            Alert::success('Success', 'Faq Category added successfully.')->persistent('Close')->autoclose(5000);
        }
        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/flag/' . $language->id;
        if (!File::exists(public_path() . '/storage/uploads/flag/' . $language->id)) {

            File::makeDirectory($upload_path, 0777, true);
        }

        if (!empty($request->files) && $request->hasFile('flag')) {

            $file      = $request->file('flag');
            $file_name = $file->getClientOriginalName();
            $type      = strtolower($file->getClientOriginalExtension());
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'])) {

                $file_temp_name = 'flag-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/flag/' . $language->id . '/' . $language->flag;

                if (file_exists($old_file) && !empty($language->flag)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/flag/') . $language->id . '/' . $file_temp_name;

                if ($type != 'svg') {
                    if ($size_mbs >= 2) {
                        $img = Image::make($file)->resize(50, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    } else {
                        $img = Image::make($file)->resize(50, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);
                    }
                } else {
                    $file->move($path, $file_temp_name);
                }

                $image['flag'] = $file_temp_name;
                $language->update($image);
            }
        }


        return redirect('admin/languages');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Edit Language')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['language'] = Languages::findOrFail($id);
        $data['action'] = "Edit";
        return view('admin.languages.form')->with($data);
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
        }else if (!Auth::user()->can('Delete Language')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }

        $id = decode($id);
        Languages::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 62, '', ['item_id' => $id]);
        Alert::success('Success', 'Language deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Languages::whereId($id)->update($data);
            //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 63, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }


}
