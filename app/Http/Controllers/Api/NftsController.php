<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Products;
use DB;
use App\Http\Resources\ProductListingResource;

class NftsController extends Controller
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
     * FETCH NFT LISTING ITEMS
     *
     * @param  REQUEST DATA
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function item_list(Request $request)
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
                $products = Products::where(['current_owner' => $user->wallet_address, 'is_active' => 1]);

                if ($request->has('search_product') && $request->search_product <> '') {
                    $search_product = $request->search_product;
                    $products->where(function ($query) use ($search_product) {
                        $query->where('title', 'LIKE', '%' . $search_product . '%');
                        $query->orwhere('sub_title', 'LIKE', '%' . $search_product . '%');
                        $query->orwhere('description', 'LIKE', '%' . $search_product . '%');
                    });
                }
                $total_products =  $products->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $products = $products->offset($offset)->limit($limit)->get();

                if (!$products->isEmpty()) {

                    return ProductListingResource::collection($products)->additional(
                        [
                            'total_records' => $total_products,
                            'message' => trans('api.NFTs Listing', array(), $app_language),
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

    /**
     * update purchased NFT
     *
     * @param  [integer] file_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function resellItem(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $price_type = $request->has('price_type') ? $request->price_type : 0;
        if ($price_type == 0) {
            $request->request->remove('auction_length_id');
        }

        $input = $request->all();

        $validation_rules = array(
            'product_id' => 'required',
            'title' => 'required|min:4',
            'sub_title' => 'required|min:4',
            'description'  => 'required|string|min:8',


        );



        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["product_id"]) != 0) {
            $product = Products::findOrFail(decodeApiIds($input["product_id"]));

            if ($product) {
                DB::beginTransaction();
                try {

                    $input['is_relisted_product'] = 1;
                    $product->update($input);

                    DB::commit();
                    return response()->json([
                        'status' => 1,
                        'message' =>  trans('api.Your NFT  has been updated successfully.', array(), $app_language)
                    ], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 0,
                        'message' =>  $e->getMessage()
                    ], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => trans("api.Sorry no record found", array(), $app_language)
                ], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => trans('api.Invalid Product id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }
}
