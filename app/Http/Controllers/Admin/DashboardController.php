<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Form;
use Alert;
use Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
    }
    /**
     * Show Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data = [];
        if (Auth::user()->hasRole('Super Admin') || Auth::user()->can('View Dashboard Latest Orders')) {

            if ($request->ajax()) {
                $data =  Orders::orderByDesc('id')->limit(config('constants.DEFAULT_LIMIT'))->get();

                $datatable = Datatables::of($data);

                $datatable = $datatable->editColumn('product_name', function ($row) {
                    return $row->product->title;
                });
                $datatable = $datatable->editColumn('buyer', function ($row) {
                    return $row->buyer->username;
                });
                $datatable = $datatable->editColumn('price_usd', function ($row) {
                    return '$'.number_format($row->price_usd,2,'.','');
                });
                $datatable = $datatable->editColumn('total', function ($row) {
                    return '$'.number_format($row->total,2,'.','');
                });
                $datatable = $datatable->editColumn('is_auction_product', function ($row) {
                    if ($row->is_auction_product == 1)
                        return '<label>Yes</label>';
                    else
                        return '<label>NO</label>';
                });
                // $datatable = $datatable->editColumn('order_id', function ($row) {
                //     return number_format(rand(), 0, '', $row->id);
                // });
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
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/dashboard/" . encode($row->id)) . '" title="show"><i class="fa fa-eye"></i></a>';

                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/dashboard', encode($row->id)],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title="Delete ads"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);

                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                    return $actions;
                });



                $datatable = $datatable->rawColumns(['price_usd','total','product_name', 'is_auction_product', 'buyer', 'order_status', 'action']);

                return $datatable->make(true);
            }
        }
        $data['admin_users'] = \App\Models\Admin::count();
        $data['cmspages'] = \App\Models\CmsPages::count();
        $data['investors'] = User::where('user_type','1')->count();
        $data['artists'] =   User::where('user_type','2')->count();
        $data['musicians'] = User::where('user_type','3')->count();
        $data['products'] = \App\Models\Products::count();
        $data['orders'] = \App\Models\Orders::count();
        $data['categories'] = \App\Models\Categories::count();
        $data['blogs'] = \App\Models\Blogs::count();
        $data['stores'] = \App\Models\Stores::count();
        $data['ads'] = \App\Models\Ads::count();
        return view('admin.dashboard.dashboard')->with($data);
    }
}
