<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Form;
use Alert;
use DataTables;
use Carbon\Carbon;
use App\Models\Bids;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products;

class BiddingHistoryController extends Controller
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
        } else if (!Auth::user()->can('View Bidding History')) {
            return abort(401, 'You don\'t have permission to access this page. Please contact you admin.');
        }
        $data = [];
        $product_id = '';



        if ($request->has('product') && !empty($request->product)) {

            $product_id = ($request->product);
        }

        $data['product'] = $product_id;

        if ($request->ajax()) {


            $db_record = Bids::where('is_active', 1);
            $db_record = $db_record->orderByDesc('id')->get();
            if ($request->has('product') && !empty($request->product)) {
                $product = decode($request->product);
                $db_record = $db_record->where('product_id', $product);
            }
            if ($request->has('bidder') && !empty($request->bidder)) {
                $db_record = $db_record->where('bidder_id', $request->bidder);
            }
            if ($request->has('prdt') && !empty($request->prdt)) {
                $db_record = $db_record->where('product_id', $request->prdt);
            }
            $datatable = DataTables::of($db_record);
            $datatable = $datatable->editColumn('bidder', function ($row) {
                return $row->bidder->username;
            });
            $datatable = $datatable->editColumn('product', function ($row) {
                return empty($row->product->title) ? '' :  '<a href="' . url('admin/products/' . encode($row->product->id)) . '">' . $row->product->title . '</a>';
            });

            $datatable = $datatable->editColumn('price', function ($row) {
                return  '$' . $row->price;
            });
            $datatable = $datatable->editColumn('is_winner_bid', function ($row) {
                // if ($row->is_winner_bid == 1) {
                //     return '<label>Yes</label>';
                // } else {
                //     return '<label>NO</label>';
                // }
                $actions = '';
                // if(Auth::user()->can('Edit Ad')){
                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/bidding-history/set-wining-bid'],
                    'style' => 'display:table;margin-right:10px;',
                    'class' => 'float-sm-left',
                    'id' => 'statusForm' . $row->id
                ]);
                $actions .= Form::hidden('id', encode($row->id));
                $actions .= Form::hidden('product_id', encode($row->product_id));
                $actions .= Form::select('is_wining_bid', [
                    '0' => 'NO',
                    '1' => 'Yes'
                ], $row->is_winner_bid, ['class' => 'form-control', 'onchange' => '$(form).submit();']);
                $actions .= Form::close();
                return $actions;
                // }
            });
            $datatable = $datatable->editColumn('auction_time', function ($row) {
                if ($row->product->auction_time != "") {
                    if (Carbon::now()->timestamp <= $row->product->auction_time) {
                        return Carbon::parse($row->product->auction_time);
                    } else {
                        return "Expired";
                    }
                } else {
                    return $row->product->auction_time = "Expired";
                }
            });

            // $datatable = $datatable->editColumn('status', function ($row) {
            //     if ($row->is_active == 1) {
            //         return '<label class="badge badge-success">Active</label>';
            //     } else {
            //         return '<label class="badge badge-warning">InActive</label>';
            //     }
            // });


            // $datatable->addColumn('action', function ($row) {
            //$actions = '';

            // $actions .= Form::open([
            //     'method'=>'POST',
            //     'url'=> ['admin/bidding-history/update-status'],
            //     'style' => 'display:table;margin-right:10px;',
            //     'class' => 'float-sm-right',
            //     'id' => 'statusForm' . $row->id
            // ]);
            // $actions .= Form::hidden('id',encode($row->id));
            // $actions .= Form::select('is_active',[
            //     '0' => 'Inactive',
            //     '1' => 'Active'
            // ], $row->is_active,['class' => 'form-control', 'onchange'=>'$(form).submit();']);
            // $actions .= Form::close();

            // $actions .= '&nbsp;<a class="btn btn-primary btn-icon"' . url("admin/bidding-history/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';

            // $actions .= '&nbsp;' . Form::open([
            //     'method' => 'DELETE',
            //     'url' => ['admin/bidding-history', encode($row->id)],
            //     'style' => 'display:inline'
            // ]);

            // // $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete History"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
            // $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

            // $actions .= Form::close();
            // return $actions;
            // });

            $datatable = $datatable->rawColumns(['product', 'bidder', 'price', 'is_winner_bid', 'auction_time', 'status', 'action']);
            return $datatable->make(true);
        }
        $data['bidders'] = User::where('is_active',1)->orderBy('username','ASC')->get();
        $data['products'] = Products::where('is_active',1)->orderBy('title','ASC')->get();
        return view('admin.bidding_history.index')->with($data);
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
        //
    }


    public function update_bid_status(Request $request)
    {
        $input = $request->all();

        $is_wining_bid = $input['is_wining_bid'];
        $id = decode($input['id']);

        if ($is_wining_bid <> '' && $is_wining_bid == '1' && $id <> '') {

            $won_bid = Bids::where('product_id', decode($input['product_id']))->where('is_winner_bid', 1)->first();
            if (!$won_bid) {
                $data = array(
                    'is_winner_bid' => $is_wining_bid,
                );

                Bids::whereId($id)->update($data);

                //EVENT LOG START
                \App\Events\UserEvents::dispatch('admin', 37, '', ['item_id' => $id]);
                Alert::success('Success', 'Bid updated successfully!')->persistent('Close')->autoclose(5000);
            }
            else{
                Alert::error('Error', 'Sorry Already won Bid set for this product')->persistent('Close')->autoclose(5000);
            }
        } else {
            Alert::error('Error', 'Error occured. Bid  not updated!')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
}
