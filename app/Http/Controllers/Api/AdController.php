<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Ads;
use App\Models\AdProducts;
use App\Models\Transactions;
use DB;

use App\Http\Resources\AdsResource;

class AdController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['adBoastedProductClick']]);
    }

    /**
     * CREATE Ad
     *
     * @param  [string] title
     * @param  [date] start_date
     * @return [date] end_date
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUserAd(Request $request)
    {


        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        $validation_rules = array(
            'user_id' => ['required'],
            'title' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'bid_type' => ['required'],
            'total_budget' => ['required'],
            'product_ids' => ['required'],
            'from_address' => ['required'],
            'transaction_hash' => ['required'],
            'price_paid' => ['required'],

        );
        $bid_type = $request->has('bid_type') ? $request->bid_type : 0;
        $price_paid = $request->has('price_paid') ? $request->price_paid : 0;
        if ($bid_type == 1) {

            $validation_rules['cpc'] = 'required';
        } else {
            $input["cpc"] = sitesSetting("suggested_cpc_price")->suggested_cpc_price;
        }
        $validator = Validator::make($request->all(), $validation_rules);



        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {

                DB::beginTransaction();
                try {


                    $ad = new Ads();
                    $input["is_active"] = 1;
                    $input["user_id"] = decodeApiIds($input['user_id']);
                    $ad->fill($input);
                    $ad->save();
                    $product_ids = explode(',', $input['product_ids']);
                    foreach ($product_ids as  $value) {
                        $value = decodeApiIds($value);
                        AdProducts::create([
                            'ad_id' => $ad->id,
                            'product_id' => $value,
                            'is_active' => 1,
                        ]);
                    }

                    if ($ad) {
                        Transactions::create([
                            'user_id' => $user->id,
                            'transaction_of' => 0,
                            'ad_id' => $ad->id,
                            'type' => 0,
                            'to_address' => $input['contract'],
                            'from_address' => $input['from_address'],
                            'transaction_hash' => $input['transaction_hash'],
                            'transaction_status' => 1,
                            'paid_price' => $price_paid,
                            'is_active' => 1
                        ]);
                    }


                    DB::commit();
                    return response()->json([

                        'status' => 1, 'message' => trans('api.your Ad added successfully.', array(), $app_language)
                    ], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
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

    public function userAdsList(Request $request)
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
                $ads = Ads::where('user_id', $user->id);

                $total_ads =  $ads->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;

                $ads = $ads->offset($offset)->limit($limit)->orderby("id", "DESC")->get();

                if (!$ads->isEmpty()) {
                    return AdsResource::collection($ads)->additional(
                        [
                            'total_records' => $total_ads,
                            'message' => trans('api.Ads Listing', array(), $app_language),
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


    public function deleteAd(Request $request)
    {
        $input = $request->all();
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'adId' => 'required',
            'userId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }
        if (decodeApiIds($input["userId"]) != 0) {
            $user = User::where('id', decodeApiIds($input["userId"]))->where('is_active', 1)->first();

            if ($user) {
                if (decodeApiIds($input["adId"]) != 0) {
                    $ad = Ads::where('id', decodeApiIds($input["adId"]))->first();

                    if ($ad) {
                        DB::beginTransaction();
                        try {
                            Ads::destroy(decodeApiIds($input["adId"]));
                            DB::commit();
                            return response()->json([
                                'status' => 1,
                                'message' => "Ad Deleted successfully"
                            ], 200, ['Content-Type' => 'application/json']);
                        } catch (\Exception $e) {
                            DB::rollback();
                            return response()->json(['status' => 0, 'message' =>  $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => trans('api.Sorry no record found', array(), $app_language)
                        ], 200, ['Content-Type' => 'application/json']);
                    }
                } else {
                    return response()
                        ->json(['status' => 0, 'message' => trans('api.Invalid Ad id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
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


    public function adBoastedProductClick(Request $request)
    {
        $input = $request->all();
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'adId' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }

        if (decodeApiIds($input["adId"]) != 0) {
            $ad = Ads::where('id', decodeApiIds($input["adId"]))->first();

            if ($ad) {
                DB::beginTransaction();
                try {
                    $ad->total_spent += $ad->cpc;
                    $ad->save();
                    DB::commit();
                    return response()->json([
                        'status' => 1,
                        'message' => "Boasted product click successfully"
                    ], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 0, 'message' =>  $e->getMessage()], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => trans('api.Sorry no record found', array(), $app_language)
                ], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()
                ->json(['status' => 0, 'message' => trans('api.Invalid Ad id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
}
