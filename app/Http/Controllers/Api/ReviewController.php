<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Review;
use App\Models\Products;
use App\Models\Orders;
use Hashids;
use DB;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getReviews']]);
    }

    /**
     * CREATE REVIEW
     *
     * @param  [integer] product_id
     * @param  [integer] buyer_id
     * @return [string] rating
     * @return [string] review
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReview(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        $validation_rules = array(
            'order_id' => ['required'],
            'product_id' => ['required'],
            'user_id' => ['required'],
            'rating' => ['required'],
            'review_title' => ['required'],
            'review' => ['required'],
        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0 && decodeApiIds($input["product_id"])) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {

                $product = Products::where('id', decodeApiIds($input["product_id"]))->first();
                if ($product) {

                    $already_review = Review::where('product_id', $product->id)
                        ->where('user_id', $user->id)
                        ->first();

                    if (!$already_review) {
                        if ($product->user_id != $user->id) {
                            DB::beginTransaction();
                            try {

                                $review = Review::create([
                                    'order_id' => decodeApiIds($input["order_id"]),
                                    'product_id' => $product->id,
                                    'seller_id' => $product->user_id,
                                    'user_id' => $user->id,
                                    'rating' => $input['rating'],
                                    'review_title' => $input['review_title'],
                                    'review' => $input['review'],
                                    'is_active' => 1
                                ]);

                                DB::commit();
                                return response()->json([

                                    'status' => 1, 'message' => trans('api.your Review added successfully.', array(), $app_language)
                                ], 200, ['Content-Type' => 'application/json']);
                            } catch (\Exception $e) {
                                DB::rollback();
                                return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                            }
                        } else {
                            return response()->json(['status' => 0, 'message' => trans("api.Sorry, you can't give reviews to yourself.", array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                        }
                    } else {
                        return response()
                            ->json(['status' => 0, 'message' => trans('api.Sorry you already given review for this order.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                    }
                } else {
                    return response()
                        ->json(['status' => 0, 'message' => trans('api.Sorry Order not exits.', array(), $app_language)], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()
                    ->json(['status' => 0, 'message' => trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid User id or Order id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * GET USER REVIEWS
     * @return [string] username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReviews($username, Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        if ($username) {
            $user = User::select('id', 'is_active')->where(['username' => $username, 'is_active' => 1])->first();

            if ($user) {
                $reviews = Review::where('seller_id', $user->id)
                    ->orderby('id', 'DESC')
                    ->limit(config('constants.DEFAULT_LIMIT'))->get();

                if (!$reviews->isEmpty()) {

                    return ReviewResource::collection($reviews)
                        ->additional([
                            'message' => 'Seller reviews',
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
                return response()->json(['status' => 0, 'message' => trans('api.User does not exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

   /**
     * GET User REVIEWS
     * @return [integer] User ID
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUserReviews($user_id, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (decodeApiIds($user_id) != 0) {
            $user = User::where('id', decodeApiIds($user_id))->where('is_active', 1)->first();
            if ($user) {
                $reviews = Review::where('is_active', 1)->where('seller_id', $user->id)->limit(config('constants.DEFAULT_LIMIT'))->get();
                if (!$reviews->isEmpty()) {
                    return ReviewResource::collection($reviews)
                        ->additional([
                            'message' => trans('api.Order reviews.', array(), $app_language),
                            'status'  => 1
                        ]);
                } else {

                    return response()->json(['status' => 0, 'message' => trans('api.No record found', array(), $app_language)]);
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

    /**
     * GET REVIEW DETAIL
     * @return [integer] User ID
     * @return [integer] Order ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewDetail(Request $request)
    {
        $input = $request->all();
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $validation_rules = array(
            'order_id'       => 'required',
            'user_id'       => 'required',
        );


        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {
                $order = Orders::find(decodeApiIds($input["order_id"]));
                if ($order) {
                    $review_count = Review::where('order_id', $order->id)->where('user_id', $user->id)->count();
                    return response()->json(
                        [

                            "data" => [
                                "productId" => Hashids::encode($order->product->id),
                                "productTitle" => $order->product->title,
                                "productSubtitle" => $order->product->sub_title,
                                'productMedia'    => $order->product->productMedia(),
                                "productSlug" => $order->product->slug,
                                "isReviewed" => $review_count,
                                "ipfsImageHash" =>  $order->product->mainImageHash()
                            ],
                            'status' => 1,
                            "message" => trans('api.get review successfully', array(), $app_language)
                        ],
                        200,
                        ['Content-Type' => 'application/json']
                    );
                } else {

                    return response()->json(['status' => 0, 'message' => trans('api.No record found', array(), $app_language)]);
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
