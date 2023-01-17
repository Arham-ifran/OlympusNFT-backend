<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Templates;
use Illuminate\Http\Request;
use Alert;
use File;
use DataTables;
use Form;
use Auth;
class TemplatesController extends Controller
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
        }else if (!Auth::user()->can('View Templates')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $user = \auth()->user();
        $data = array();
        if ($request->ajax()) {
            $db_record = Templates::orderByDesc('id');

            $datatable = DataTables::of($db_record);


            $datatable = $datatable->editColumn('template_type', function ($row) {
                return '<label class="badge badge-success">Email</label>';
            });


            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<i class="badge badge-primary"></i>';
                if ($row->is_active == 1) {
                    $status = ' <label class="badge badge-success">Active</label>';
                } else if ($row->is_active == 0) {
                    $status = '<label class="badge badge-warning">Inactive</label>';
                } else {
                    $status = '<label class="badge badge-danger">Deleted</label>';
                }

                return $status;
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Templates')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/templates/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-right',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active',
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= Form::close();
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/templates/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Templates')){

                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/templates', encode($row->id)],
                        'style' => 'display:inline'
                    ]);
                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Template"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['template_type', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.templates.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Templates')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";
        return view('admin.templates.edit')->with($data);
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
        }else if (!Auth::user()->can('Edit Templates')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $id = decode($id);
        $data['action'] = "Edit";

        $data['template'] = Templates::findOrFail($id);
        return view('admin.templates.edit')->with($data);
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

        if ($input['template_type'] == 2) {
            $input['content'] = $input['content_sms'];
        } else {
            $input['content'] = $input['content'];
        }
        unset($input['content_sms']);

        if ($input['action'] == 'Edit') {
            $template = Templates::findOrFail($input['id']);
            $template->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 69, '', ['item_id' => $template->id]);
            Alert::success('Success', 'Template updated successfully!')->persistent('Close')->autoclose(5000);
        } else {

            $template = Templates::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 68, '', ['item_id' => $template->id]);
            Alert::success('Success', 'Template added successfully!')->persistent('Close')->autoclose(5000);
        }

        if ($request->hasFile('attachment')) {
            $old_attachment = $input['old_attachment'];
            $destinationPath = 'uploads/templates'; // upload path
            $attachment = $request->file('attachment'); // file
            $extension = $attachment->getClientOriginalExtension() == 'jfif' ? 'png' : $attachment->getClientOriginalExtension(); // getting image extension
            $fileName = $template->id . '-' . time() . '.' . $extension; // renameing image
            $attachment->move($destinationPath, $fileName); // uploading file to given path
            //remove old image
            if ($old_attachment) {
                File::delete($destinationPath . '/' . $old_attachment);
            }
            //insert image record
            $temp_attachment['attachment'] = $fileName;
            $template->update($temp_attachment);
        }

        return redirect('admin/templates');
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
        }else if (!Auth::user()->can('Delete Templates')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }

        $data = array(
            'is_active' => 2,
        );
        $id = decode($id);
        Templates::whereId($id)->update($data);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 70, '', ['item_id' => $id]);
        //        Templates::destroy($id);
        Alert::success('Success', 'Template deleted successfully!')->persistent('Close')->autoclose(5000);

        return redirect('admin/templates');
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

            Templates::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 71, '', ['item_id' => $id]);
            Alert::success('Success', 'Template status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Template status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
