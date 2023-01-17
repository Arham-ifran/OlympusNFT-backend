<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Bids;
use App\Models\Products;
use App\Models\Templates;
use Hashids;
use View;
use Mail;
use App\Mail\MasterMail;
use DB;
use App\Http\Resources\BidListingResource;

class BidController extends Controller
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
     * CREATE Bid
     *
     * @param  [integer] product_id
     * @param  [integer] bidder_id
     * @return [string] price
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBid(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        $validation_rules = array(
            'product_id' => ['required'],
            'bidder_id' => ['required'],
            'price' => ['required'],

        );


        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["bidder_id"]) != 0 && decodeApiIds($input["product_id"])) {
            $user = User::where('id', decodeApiIds($input["bidder_id"]))->where("is_active", 1)->first();
            if ($user) {

                $product = Products::where('id', decodeApiIds($input["product_id"]))->first();
                if ($product) {
                    $already_bid = Bids::where('product_id', $product->id)
                        ->where('bidder_id', $user->id)
                        ->first();

                    if (!$already_bid) {
                        if ($product->user_id != $user->id) {
                            DB::beginTransaction();
                            try {

                                $bid = Bids::create([
                                    'product_id' => $product->id,
                                    'seller_id' => $product->user_id,
                                    'bidder_id' => $user->id,
                                    'price' => $input['price'],
                                    'is_active' => 1,
                                ]);

                                DB::commit();
                                if ($product->user->email_notification == 1) {
                                    /****send Email*****/

                                    $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_bid')->first();
                                    if ($template != '') {

                                        $subject = $template->subject;


                                        $link = env("FRONT_BASE_URL") . 'product-detail/' . $product->slug . '/' . Hashids::encode($product->id);
                                        $to_replace = ['[SELLER]', '[PRODUCTNAME]', '[BIDDER]', '[PRICE]', '[LINK]', '[SITE_NAME]', '[SITE_URL]'];

                                        $with_replace = [$product->user->username, $product->title, $user->username, $input['price'], $link, SITE_NAME, env("FRONT_BASE_URL")];
                                        $header = $template->header;
                                        $footer = $template->footer;
                                        $content = $template->content;
                                        $html_header = str_replace($to_replace, $with_replace, $header);
                                        $html_footer = str_replace($to_replace, $with_replace, $footer);
                                        $html_body = str_replace($to_replace, $with_replace, $content);

                                        $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                                        Mail::queue(new MasterMail($product->user->email, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
                                    }
                                    /****end****/
                                }
                                return response()->json([

                                    'status' => 1, 'message' => trans('api.your Bid added successfully.', array(), $app_language)
                                ], 200, ['Content-Type' => 'application/json']);
                            } catch (\Exception $e) {
                                DB::rollback();
                                return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                            }
                        } else {
                            return response()->json(['status' => 0, 'message' => trans("api.Sorry, you can't give bid to your product.", array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }
                    } else {
                        return response()
                            ->json(['status' => 0, 'message' => trans('api.Sorry you already bid on this product.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    }
                } else {
                    return response()
                        ->json(['status' => 0, 'message' => trans('api.Sorry product not exits.', array(), $app_language)], 402, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id or product id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }


    public function myBids(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'user_id' => 'required',
        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }
        if (decodeApiIds($input["user_id"]) != 0) {

            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();

            if ($user) {

                $bids = Bids::where('bidder_id', $user->id);
                $total_bids =  $bids->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $bids = $bids->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$bids->isEmpty()) {

                    return BidListingResource::collection($bids)->additional(
                        [
                            'total_records' => $total_bids,
                            'message' => trans('api.Bid Listing', array(), $app_language),
                            'status' => 1
                        ]
                    );
                } else {

                    return response()
                        ->json([
                            'status' => 0,
                            'data' => [],
                            'message' => trans('api.No record found', array(), $app_language)
                        ]);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
}
