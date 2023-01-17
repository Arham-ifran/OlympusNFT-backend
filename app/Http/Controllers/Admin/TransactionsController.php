<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Transactions;
use App\Models\User;
use Auth;

class TransactionsController extends Controller
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
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('View Transactions')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        if ($request->ajax()) {

            $db_record =  Transactions::where ('is_active',1);
            if ($request->has('user') && !empty($request->user)) {
                $db_record = $db_record->where('user_id', $request->user);
            }
            if ($request->has('transaction_of') && !empty($request->transaction_of)) {
                $db_record = $db_record->where('transaction_of', $request->transaction_of);
            }

            $db_record = $db_record->orderByDesc('id')->get();
        
            $datatable = Datatables::of($db_record);
            $datatable->editColumn('user', function ($row) {
                return $row->user->username;
            });
           
            $datatable->editColumn('type', function ($row) {
                return ($row->type == 1) ? 'Mint' : 'Transfer';
            });

            $datatable->editColumn('transaction_of', function ($row) {
                $transaction_of = "";
                if ($row->transaction_of == 0 && !empty($row->transaction_of))
                    $transaction_of = "Ad";
                elseif ($row->transaction_of == 1)
                    $transaction_of = "Product";
                else if ($row->transaction_of == 2)
                    $transaction_of = "Order";
                else {
                }
                return $transaction_of;
            });

            $datatable->addColumn('ad', function ($row) {
                return empty($row->ad->title) ? '' : '<a href="' . url('admin/ads/' . encode($row->ad->id)) . '">' . $row->ad->title . '</a>';
            });

            $datatable->addColumn('product', function ($row) {
                return empty($row->product->title) ? '' : '<a href="' . url('admin/products/' . encode($row->product->id)) . '">' . $row->product->title . '</a>';
            });

            $datatable->addColumn('order', function ($row) {
                return empty($row->order->id) ? '' : '<a href="' . url('admin/orders/' . encode($row->order->id)) . '"><label class="badge badge-success">' . $row->order->id . '</label></a>';
            });

            $datatable = $datatable->editColumn('transaction_status', function ($row) {

                return ($row->transaction_status == 1) ? '<label class="badge badge-success w-40 text-light p-1"><span>Completed</span></label>' : '<label class="badge badge-warning w-40 text-light p-1"><span>Pending</span></label>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/transactions" . '/' . encode($row->id)) . '" title="transaction view"><i class="fa fa-eye"></i></a>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['user', 'type','ad','product','order', 'transaction_status', 'action']);
            return $datatable->make(true);
        }
        $data['users'] = User::where('is_active', 1)->orderBy('username', 'ASC')->get();
        return view('admin.transactions.index')->with($data);
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
        if (Auth::user()->hasRole('Super Admin')) {
        } else if (!Auth::user()->can('View Transactions')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);
        $data['transaction'] = Transactions::where('id', $id)->first();

        return view('admin.transactions.show')->with($data);
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
}
