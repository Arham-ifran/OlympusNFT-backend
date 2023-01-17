<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\AuctionLength;
use Alert;
use App\Models\ProductMediaFiles;
use Image;
use Hash;
use File;
use View;
use DataTables;
use App\Models\User;
use Form;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Stores;

class AuctionProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $store_id = '';
        if ($request->has('store') && !empty($request->store)) {
            $store_id = ($request->store);
        }

        $data['store'] = $store_id;

        if ($request->ajax()) {
            $db_record =  Products::where(function ($query)  {
                $query->where('price_type',  1)->orwhere('price_type', 2);
            });
            if ($request->has('store_id') && !empty($request->store_id)  || ($request->store_id) === '0') {
               
                $db_record = $db_record->where('store_id',$request->store_id);

            }
            if ($request->has('user_id') && !empty($request->user_id)) {
                $db_record = $db_record->where('user_id',$request->user_id);
            }
           
            $db_record = $db_record->orderByDesc('id')->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->editColumn('user', function ($row) {
                return empty($row->user->username) ? '' : $row->user->username;
            });
            $datatable = $datatable->editColumn('store', function ($row) {
                return empty($row->store->store_title) ? 'OlympusNFT' : $row->store->store_title;
            });

            $datatable = $datatable->editColumn('category', function ($row) {
                return empty($row->category->title) ? '' : $row->category->title;
            });

            $datatable = $datatable->editColumn('title', function ($row) {
                return empty($row->title) ? '' : $row->title;
            });

            $datatable = $datatable->editColumn('downloadable_file', function ($row) {
                return ($row->downloadable_file == 1) ? '<label>Yes</label>' : '<label>NO</label>';
            });
            $datatable = $datatable->editColumn('price_usd', function ($row) {
                return  empty('$' . $row->price_usd) ? '' :  '$' . $row->price_usd;
            });

            $datatable = $datatable->editColumn('auction_end_time', function ($row) {

                if ($row->auction_length_id) {
                    if ($row->auction_time != "") {
                        if (\Carbon\Carbon::now()->timestamp <= $row->auction_time)
                            $auction_time =  Carbon::createFromTimestamp($row->auction_time);
                        else {
                            $auction_time = "Expired";
                        }
                    } else {
                        $auction_time = "Expired";
                    }
                }
                return  $auction_time;
            });
            $datatable = $datatable->editColumn('total_bids', function ($row) {
                if (count($row->bids) > 0)
                    return '<a href="' . url("admin/bidding-history?product=" . encode($row->id))  . '"><label class="badge badge-success">' . count($row->bids) . '</label></a>';

                else
                    return '<label class="badge badge-light">' . count($row->bids) . '</label>';
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';

                $actions .= Form::open([
                    'method' => 'POST',
                    'url' => ['admin/auction-products/update-status'],
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

                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/auction-products/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';
                if (count($row->bids) == 0 && $row->is_sold== 0) {
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/auction-products', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete Product"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);

                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['price_usd', 'store', 'category', 'title' ,'downloadable_file', 'auction_end_time', 'total_bids', 'action']);

            return $datatable->make(true);
        }
        $data['stores'] = Stores::where('is_active',1)->orderBy('store_title','ASC')->get();
        $data['users'] = User::where('is_active',1)->orderBy('username','ASC')->get();
        return view('admin.auction_products.index')->with($data);
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
        $id = decode($id);
        $data['product']  = Products::where('id', $id)->first();

        return view('admin.auction_products.show')->with($data);
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
        }else if (!Auth::user()->can('Delete Product')) {
            return abort(401,'You don\'t have permission to access this page. Please contact you admin.');
        }
        $id = decode($id);

        $data = array(
            'is_active' => 2,
        );

        Products::whereId($id)->delete($data);

        //EVENT LOG START
        \App\Events\UserEvents::dispatch('admin', 31, '', ['item_id' => $id]);
        Alert::success('Success', 'Product deleted successfully!')->persistent('Close')->autoclose(5000);
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
 
             Products::whereId($id)->update($data);
             //EVENT LOG START
             \App\Events\UserEvents::dispatch('admin', 73, '', ['item_id' => $id]);
             Alert::success('Success', 'status updated successfully!')->persistent('Close')->autoclose(5000);
         } else {
             Alert::error('Error', 'Error occured. Status not updated!')->persistent('Close')->autoclose(5000);
         }
         return redirect()->back();
     }
}
