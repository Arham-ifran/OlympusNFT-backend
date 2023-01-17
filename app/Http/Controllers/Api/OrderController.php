<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Products;
use App\Models\Orders;
use App\Models\Templates;
use App\Models\Transactions;
use View;
use Mail;
use App\Mail\MasterMail;
use App\Http\Resources\OrderResource;
use App\Http\Resources\WonAuctionResource;
use App\Http\Resources\MyEarningResource;
use Carbon\Carbon;
use Hashids;

use DB;

class OrderController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * user_sold_products
     *
     * @param  mixed $request
     * @return void
     */
    public function userSoldProducts(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                $orders = Orders::where('is_active', 1)->where('seller_id', $user->id);

                if ($request->has('search_product') && $request->search_product <> '') {
                    $search_date =  $input["search_product"];
                    $orders = $orders::WhereHas('product', function ($q) use ($search_date) {
                        $q = $q->where('title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('sub_title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('description', 'LIKE', '%' . $search_date . '%');
                    });
                }


                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $orders = $orders->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$orders->isEmpty()) {
                    $total_orders =  $orders->count();
                    return OrderResource::collection($orders)
                        ->additional([
                            'total_records' => $total_orders,
                            'message' => trans('api.Order Listing', array(), $app_language),
                            'status'  => 1
                        ]);
                } else {

                    return response()->json(
                        [
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]
                    );
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Create Order
     *
     * @param  mixed $request
     * @return void
     */
    public function createOrder(Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        if (!empty($input['order_id']) && $input['order_id'] != "") {

            $order = Orders::where('id', decodeApiIds($input["order_id"]))->first();
            if ($order) {
                $order->update(['order_status_id' => 3]);

                /****send Email to buyer*****/
                if ($order->buyer->email_notification == 1) {
                    $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_nft_creation')->first();
                    if ($template != '') {

                        $subject = $template->subject;
                        $link = env("FRONT_BASE_URL") . 'my-orders';
                        $order_time = Carbon::parse($order->created_at)->format('d M, Y');
                        $to_replace = ['[PRODUCTNAME]', '[BUYER]', '[SELLER]', '[TRANSACTIONHASH]', '[PRICEPAID]', '[TYPEOFTRANSACTION]', '[TIMESPENT]', '[LINK]', '[SITE_NAME]', '[SITE_URL]'];
                        $with_replace = [$order->product->title, $order->buyer->username, $order->seller->username, $order->transaction_hash, $order->total, "Purchase", $order_time, $link, SITE_NAME, env("FRONT_BASE_URL")];
                        $header = $template->header;
                        $footer = $template->footer;
                        $content = $template->content;

                        $html_header = str_replace($to_replace, $with_replace, $header);
                        $html_footer = str_replace($to_replace, $with_replace, $footer);
                        $html_body = str_replace($to_replace, $with_replace, $content);

                        $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                        Mail::queue(new MasterMail($order->buyer->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                    }
                }
                /****end****/


                return response()->json([
                    'status' => 1,
                    'message' => trans('api.Your order  has been completed successfully.', array(), $app_language),
                ], 200, ['Content-Type' => 'application/json']);
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.order id not exist', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            $validation_rules = array(
                'product_id'    => 'required',
                'user_id'      => 'required',
                'price_usd' => 'required',
                'total'  => 'required',
                'order_status'  => 'required',
                'transaction_hash' => 'required',
                'from_address' => 'required',
                'to_address' => 'required',
                'earned_price' => 'required',

            );

            $validator = Validator::make($request->all(), $validation_rules);
            $earned_price = $request->has('earned_price') ? $request->earned_price : 0;
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()]);
            }
            if (decodeApiIds($input["user_id"]) != 0) {
                $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
                if ($user) {
                    $product = Products::where('id', decodeApiIds($input["product_id"]))->first();

                    if ($product) {
                        if (empty($product->order->id)) {
                            DB::beginTransaction();
                            try {
                                $data["product_id"]  = $product->id;
                                $data["seller_id"]  = $product->user_id;
                                $data["buyer_id"]  = $user->id;
                                $data["seller_id"]  = $product->user_id;
                                $data["price_usd"]  = $product->price_usd;
                                $data["total"]  =  $input["total"];
                                $data["transaction_hash"]  =  $input["transaction_hash"];
                                $data["from_address"]  =  $input["from_address"];
                                $data["to_address"]  =  $input["to_address"];
                                $data["order_status_id"]  = 3;
                                $data["is_active"]  = 1;
                                if ($request->has('bid_id') && $request->bid_id <> '') {
                                    $data["is_auction_product"]  =  1;
                                    $data["bid_id "]  = Hashids::decode($input["bid_id"]);
                                }


                                $order = Orders::create($data);

                                if ($order) {
                                    Transactions::create([
                                        'user_id' => $product->user_id,
                                        'transaction_of' => 2,
                                        'order_id' => $order->id,
                                        'product_id' => $product->id,
                                        'type' => 1,
                                        'from_address' => $order->from_address,
                                        'to_address' => $order->to_address,
                                        'transaction_hash' => $order->transaction_hash,
                                        'earned_price' => $earned_price,
                                        'transaction_status' => 3,
                                        'paid_price' => $input["total"],
                                        'is_active' => 1
                                    ]);
                                }

                                Products::where(['parent_product_id' => $product->parent_product_id, 'is_relisted_product' => 0])->update(['quantity' => DB::raw('quantity - 1')]);

                                $product->update(['is_sold' => 1, 'current_owner' => $order->to_address, 'available_quantity' => 0]);

                                DB::commit();

                                /****send Email to seller*****/
                                if ($product->user->email_notification == 1) {
                                    $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_sold')->first();
                                    if ($template != '') {

                                        $subject = $template->subject;

                                        $link = env("FRONT_BASE_URL") . 'sold-item-list';
                                        $order_time = Carbon::parse($order->created_at)->format('d M, Y');
                                        $to_replace = ['[PRODUCTNAME]', '[SELLER]', '[BUYER]', '[TRANSACTIONHASH]', '[EARNED]', '[TYPEOFTRANSACTION]', '[TIMESPENT]', '[LINK]', '[SITE_NAME]', '[SITE_URL]'];
                                        $with_replace = [$product->title, $product->user->username, $user->username, $input["transaction_hash"], $earned_price, "Purchase", $order_time, $link, SITE_NAME, env("FRONT_BASE_URL")];
                                        $header = $template->header;
                                        $footer = $template->footer;
                                        $content = $template->content;

                                        $html_header = str_replace($to_replace, $with_replace, $header);
                                        $html_footer = str_replace($to_replace, $with_replace, $footer);
                                        $html_body = str_replace($to_replace, $with_replace, $content);

                                        $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                                        Mail::queue(new MasterMail($product->user->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                                    }
                                }
                                /****end****/

                                return response()->json([
                                    'status' => 1,
                                    'message' => trans('api.Your order  has been created successfully.', array(), $app_language),
                                ], 200, ['Content-Type' => 'application/json']);
                            } catch (\Exception $e) {
                                DB::rollback();
                                return response()->json([
                                    'status' => 0,
                                    'message' =>   $e->getMessage()
                                ], 200, ['Content-Type' => 'application/json']);
                            }
                        } else {
                            return response()
                                ->json(['status' => 0, 'message' => trans('api.Sorry  product not available already ordered.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }
                    } else {
                        return response()
                            ->json(['status' => 0, 'message' => trans('api.Sorry product not exits.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' =>  trans('api.User not Exists or In Active.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        }
    }


    /**
     * user_buyer_products
     *
     * @param  mixed $request
     * @return void
     */
    public function myBoughtItems(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                $orders = Orders::where('is_active', 1)->where('buyer_id', $user->id);

                if ($request->has('search_product') && $request->search_product <> '') {
                    $search_date =  $input["search_product"];
                    $orders = $orders::WhereHas('product', function ($q) use ($search_date) {
                        $q = $q->where('title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('sub_title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('description', 'LIKE', '%' . $search_date . '%');
                    });
                }


                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $orders = $orders->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$orders->isEmpty()) {
                    $total_orders =  $orders->count();
                    return OrderResource::collection($orders)
                        ->additional([
                            'total_records' => $total_orders,
                            'message' => trans('api.Order Listing', array(), $app_language),
                            'status'  => 1
                        ]);
                } else {

                    return response()->json(
                        [
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]
                    );
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * user_Won Auctions
     *
     * @param  mixed $request
     * @return void
     */
    public function wonAuctions(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                $orders = Orders::where('is_active', 1)->where('buyer_id', $user->id)->where('is_auction_product', 1);

                if ($request->has('search_product') && $request->search_product <> '') {
                    $search_date =  $input["search_product"];
                    $orders = $orders::WhereHas('product', function ($q) use ($search_date) {
                        $q = $q->where('title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('sub_title', 'LIKE', '%' . $search_date . '%');
                        $q = $q->orwhere('description', 'LIKE', '%' . $search_date . '%');
                    });
                }


                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $orders = $orders->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$orders->isEmpty()) {
                    $total_orders =  $orders->count();
                    return WonAuctionResource::collection($orders)
                        ->additional([
                            'total_records' => $total_orders,
                            'message' => trans('api.Won Auction Listing ', array(), $app_language),
                            'status'  => 1
                        ]);
                } else {

                    return response()->json(
                        [
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]
                    );
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * My Earning 
     *
     * @param  mixed $request
     * @return void
     *
     */
    public function myEarning(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {
                $today = Carbon::today();
                $weekly_sales = Orders::where('is_active', 1)->where('seller_id', $user->id)
                    ->where('created_at', '>=', $today->subDays(7))
                    ->select(DB::raw('DATE(created_at) AS created'), DB::raw('sum(total) as total_sales'))
                    ->groupBy('created')
                    ->orderby("created_at", "ASC")->get();


                $monthtly_sale = Orders::where('is_active', 1)->where('seller_id', $user->id)
                    ->whereYear('created_at',  Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->sum('total');


                if (!$weekly_sales->isEmpty()) {
                    return response()->json(
                        [
                            'status' => 1,
                            'data' => [
                                "weekly_sales" => MyEarningResource::collection($weekly_sales),
                                "monthly_sales" => [
                                    "month" => Carbon::now()->format('M'),
                                    "sales" => $monthtly_sale,
                                ]
                            ],
                            'message' => trans('api.My Earning', array(), $app_language)
                        ]
                    );
                } else {

                    return response()->json(
                        [
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]
                    );
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
}
