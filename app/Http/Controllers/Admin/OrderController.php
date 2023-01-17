<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Form;
use Alert;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use Carbon\Carbon;
use Auth;
class OrderController extends Controller
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
        }else if (!Auth::user()->can('View Orders')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];

        if ($request->ajax()) {
            $db_record =  new Orders ();

            if ($request->has('product') && !empty($request->product)) {
                $db_record = $db_record->where('product_id',$request->product);
            }
            if ($request->has('seller') && !empty($request->seller)) {
                $db_record = $db_record->where('seller_id',$request->seller);
            }
            if ($request->has('buyer') && !empty($request->buyer)) {
                $db_record = $db_record->where('buyer_id',$request->buyer);
            }

            $db_record = $db_record->orderByDesc('id')->get();
            $datatable = Datatables::of($db_record);

            $datatable = $datatable->editColumn('product', function ($row) {
                return $row->product->title;
            });
            $datatable = $datatable->editColumn('seller', function ($row) {
                return $row->seller->username;
            });
            $datatable = $datatable->editColumn('buyer', function ($row) {
                return $row->buyer->username;
            });


            $datatable = $datatable->editColumn('price_usd', function ($row) {
                return  '$'.number_format($row->price_usd,2,'.',',');
            });
            $datatable = $datatable->editColumn('total', function ($row) {
                return  '$'.number_format($row->total,2,'.',',');
            });
            $datatable = $datatable->editColumn('to_address', function ($row) {
                return empty($row->to_address) ? '' : $row->to_address;
            });
            $datatable = $datatable->editColumn('created_at', function ($row) {
                return  Carbon::parse($row->created_at)->format('d M, Y');
            });

            $datatable = $datatable->editColumn('order_status', function ($row) {
                if (!empty($row->order_status->title)) {
                    if ($row->order_status->title == "Completed") {
                        return '<label class="badge badge-success w-100 text-light p-1"><h6>Completed</h6></label>';
                    } elseif ($row->order_status->title == "In progress") {
                        return '<label class="badge badge-success w-100 text-light p-1"><h6>In progress</h6></label>';
                    } elseif ($row->order_status->title == "Pending") {
                        return '<label class="badge badge-warning w-100 text-light p-1"><h6>Pending</h6></label>';
                    } elseif ($row->order_status->title == "Cancel") {
                        return '<label class="badge badge-danger w-100 text-light p-1"><h6>Canceled</h6></label>';
                    }
                    } else {

                        return 'Order Status Not Found';
                    }
            });
            $datatable->addColumn('action', function ($row) {
            $actions = '';
            $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/orders/" . encode($row->id)) . '" title="order view"><i class="fa fa-eye"></i></a>';
            $actions .= Form::close();
            return $actions;
            });
            $datatable = $datatable->rawColumns(['product', 'seller', 'buyer','price_usd','total','from_address','to_address','created_at','order_status','action']);
            return $datatable->make(true);
        }
        $data['products'] = Products::where('is_active',1)->orderBy('title','ASC')->get();
        $data['users'] = User::where('is_active',1)->orderBy('username','ASC')->get();

        return view('admin.orders.index')->with($data);
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
        if(Auth::user()->hasRole('Super Admin')){
        }else if (!Auth::user()->can('View Orders')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['order'] = Orders::where('id', $id)->where('is_active', 1)->first();

        return view('admin.orders.show')->with($data);
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
        //
    }

    public function update_status(Request $request)
    {
        $input = $request->all();

        $is_active = $input['is_active'];
        $id = decode($input['id']);

        if ($is_active <> '' && $id <> '') {
            $data = array(
                'is_active' => $is_active,
            );

            Orders::whereId($id)->update($data);
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 22, '', ['item_id' => $id]);
            Alert::success('Success', 'Order status updated successfully!')->persistent('Close')->autoclose(5000);
        } else {
            Alert::error('Error', 'Error occured. Order status not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }

   
}
