<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Form;
use Auth;
class CategoriesController extends Controller
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
        }else if (!Auth::user()->can('View Category')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = array();
        if ($request->ajax()) {
            $db_record = Categories::orderByDesc('id');

            $datatable = DataTables::of($db_record);
            $datatable = $datatable->editColumn('url', function ($row) {
                return '<a href="' . config('constants.FRONT_BASE_URL').$row->url . '" target="_blank" >Go to Page</a>';
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

                if(Auth::user()->can('Edit Category')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/categories/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-left',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active'
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/categories/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                    $actions .= Form::close();
                }

                if(Auth::user()->can('Delete Category')){
                    $actions .= Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/categories', encode($row->id)],
                        'style' => 'display:inline'
                    ]);
                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Category"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }


                $actions .= '';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['url', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.categories.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Category')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data['action'] = "Add";
        return view('admin.categories.edit')->with($data);
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
        }else if (!Auth::user()->can('Edit Category')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['action'] = "Edit";
        $data['category'] = Categories::findOrFail($id);
        return view('admin.categories.edit')->with($data);
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
            $CmsPages = Categories::findOrFail($input['id']);

            $sqlChk = Categories::whereRaw('url = "' . $input['url'] . '" AND id <>  ' . $input['id'])->first();
            if ($sqlChk) {
                $input['url'] = $input['url'] . '-' . rand(1, 99999);
            }
            $CmsPages->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 6, '', ['item_id' => $CmsPages->id]);
            Alert::success('Success', 'Category updated successfully!')->persistent('Close')->autoclose(5000);
        } else {

            $sqlChk = Categories::where('url', $input['url'])->first();
            if ($sqlChk) {
                $input['url'] = $input['url'] . '-' . rand(1, 99999);
            }

            $CmsPages = Categories::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 5, '', ['item_id' => $CmsPages->id]);
            Alert::success('Success', 'Category added successfully!')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/categories');
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
        }else if (!Auth::user()->can('Delete Category')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $category = Categories::findOrFail(decode($id));

        $category->delete();
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 7, '', ['item_id' => $id]);
        Alert::success('Success', 'Category deleted successfully!')->persistent('Close')->autoclose(5000);
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

            Categories::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 8, '', ['item_id' => $id]);
            Alert::success('Success', 'Category status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Category status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
