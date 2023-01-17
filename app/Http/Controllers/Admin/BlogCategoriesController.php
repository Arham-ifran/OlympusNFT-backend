<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategories;
use Illuminate\Http\Request;
use Alert;
use Image;
use Hash;
use File;
use View;
use DataTables;
use Form;
use Auth;
use Illuminate\Support\Facades\Validator;

class BlogCategoriesController extends Controller
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
        }else if (!Auth::user()->can('View Blog Categories')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        if ($request->ajax()) {
            $data = BlogCategories::get();
            $datatable = Datatables::of($data);
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';

            });


            $datatable->addColumn('action', function ($row) {
                $actions = '';
                 if(Auth::user()->can('Edit Blog Categories')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/blog-categories/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-right',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active',
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/blog-categories/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                    $actions .= Form::close();
                }

                if(Auth::user()->can('Delete Blog Categories')){
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/blog-categories', encode($row->id)],
                        'style' => 'display:inline'
                    ]);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Blog Category"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::close();

                }
                $actions .= '';
                return $actions;
            });



            $datatable = $datatable->rawColumns(['status', 'action']);
            return $datatable->make(true);
        }

        return view('admin.blogCategories.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Blog Categories')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $data['action'] = "Add";


        if (old()) {
            $data['blogCategory'] = old();
        }

        return view('admin.blogCategories.form')->with($data);
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
                'title' => ['required', 'string','max:255']
              ]);

            $blogCategories = BlogCategories::findOrFail($input['id']);

            $blogCategories->update($input);

            \App\Events\UserEvents::dispatch('admin', 53, '', ['item_id' => $blogCategories->id]);
            Alert::success('Success', 'Blog Category updated successfully.')->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'title' => ['required', 'string', 'max:255']

            ]);


            $blogCategories = BlogCategories::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 52, '', ['item_id' => $blogCategories->id]);

            Alert::success('Success', 'Blog Category added successfully.')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/blog-categories');
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
        }else if (!Auth::user()->can('Edit Blog Categories')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['blogCategory'] = BlogCategories::findOrFail($id);
        $data['action'] = "Edit";
        return view('admin.blogCategories.form')->with($data);
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
        }else if (!Auth::user()->can('Delete Blog Categories')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        BlogCategories::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 54, '', ['item_id' => $id]);
        Alert::success('Success', 'Blog Category deleted successfully!')->persistent('Close')->autoclose(5000);
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

            BlogCategories::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 55, '', ['item_id' => $id]);
            Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }


}
