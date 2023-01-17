<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReportAbuse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Form;
use Alert;
use Hashids;
use Auth;
class ProductReportAbuseController extends Controller
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
        }else if (!Auth::user()->can('View Product Report Abuse')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }

        $data = [];
        if ($request->ajax()) {
            $data = ProductReportAbuse::get();
            $datatable = DataTables::of($data);
            $datatable = $datatable->editColumn('status', function ($row) {
                if ($row->is_active == 1)
                    return '<label class="badge badge-success">Active</label>';
                else
                    return '<label class="badge badge-warning">Inactive</label>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(Auth::user()->can('Edit Product Report Abuse')){
                    $actions .= Form::open([
                        'method' => 'POST',
                        'url' => ['admin/product-report-abuses/update-status'],
                        'style' => 'display:table;margin-right:10px;',
                        'class' => 'float-sm-left',
                        'id' => 'statusForm' . $row->id
                    ]);
                    $actions .= Form::select('is_active', [
                        '0' => 'Inactive',
                        '1' => 'Active'
                    ], $row->is_active, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                    $actions .= Form::hidden('id', encode($row->id));
                    $actions .= Form::close();
                    $actions .= '<a class="btn btn-primary btn-icon" href="' . url("admin/product-report-abuses/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                }

                if(Auth::user()->can('Delete Product Report Abuse')){
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/product-report-abuses', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Product Report Abuse"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }

                return $actions;
            });
            $datatable = $datatable->rawColumns(['status', 'action']);

            return $datatable->make(true);
        }
        return view('admin.product_report_abuse.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Add Product Report Abuse')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data['action'] = "Add";
        return view('admin.product_report_abuse.form')->with($data);
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
            $product_report_abuse = ProductReportAbuse::findOrFail($input['id']);
            $this->validate($request, [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'min:0', 'string'],
                'short_desc' => ['required', 'min:0', 'string'],
                'is_active' => 'required',
            ]);

            $product_report_abuse->update($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 34, '', ['item_id' => $product_report_abuse->id]);
            Alert::success('Success', 'Product Report Abuse updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'min:0', 'string'],
                'short_desc' => ['required', 'min:0', 'string'],
                'is_active' => 'required',
            ]);

            $product_report_abuse = ProductReportAbuse::create($input);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 33, '', ['item_id' => $product_report_abuse->id]);
            Alert::success('Success', 'Product Report Abuse added successfully!')->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/product-report-abuses');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        }else if (!Auth::user()->can('Edit Product Report Abuse')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['product_report_abuse'] = ProductReportAbuse::findOrFail($id);
        $data['action'] = "Edit";
        return view('admin.product_report_abuse.form')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('Delete Product Report Abuse')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        ProductReportAbuse::destroy($id);
        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 35, '', ['item_id' => $id]);
        Alert::success('Success', 'Faq Category deleted successfully!')->persistent('Close')->autoclose(5000);
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

            ProductReportAbuse::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 36, '', ['item_id' => $id]);
            Alert::success('Success', 'Product Report Abuse status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Prduct Report Abuse status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
