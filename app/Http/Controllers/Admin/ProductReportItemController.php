<?php

namespace App\Http\Controllers\Admin;

use Form;
use App\Models\ReportItem;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
class ProductReportItemController extends Controller
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
        }else if (!Auth::user()->can('View Product Report Items')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $data =  ReportItem::get();

            $datatable = Datatables::of($data);


            $datatable->addColumn('users', function ($row) {

                    return $row->users->username ;
            })
                ->addColumn('product', function ($row) {

                    $product = '';
                    $product .= '&nbsp;<a  href="' . url("admin/products/" . encode( $row->products->id)) . '" title="product" target="_blank">'.$row->products->title.'</a>';

                    return $product;
                })
                ->addColumn('product_report_abuses', function ($row) {

                    return empty($row->product_report_abuses->title) ? '' : $row->product_report_abuses->title;
                });

                //->addColumn('action', function ($row) {
                //     $actions = '';
                //     //$actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/artists/" . encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                //     if(Auth::user()->can('Delete Product Report Item')){
                //     $actions .= '&nbsp;' . Form::open([
                //         'method' => 'DELETE',
                //         'url' => ['admin/product-report-items', encode($row->id)],
                //         'style' => 'display:inline'
                //     ]);

                //     $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Report"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                //     $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                //     $actions .= Form::close();
                //     }
                //     return $actions;
                // });

            $datatable = $datatable->rawColumns(['reason', 'report item', 'product', 'users', 'action']);

            return $datatable->make(true);
        }

        return view('admin.product_report_item.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        }else if (!Auth::user()->can('Delete Product Report Items')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        ReportItem::destroy($id);

        Alert::success('Success', 'Report Item deleted successfully!')->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
}
