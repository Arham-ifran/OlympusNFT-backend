<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Stores;
use App\Models\Categories;
use Storage;
use Image;
use DB;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\UserStoresResource;
use App\Http\Resources\StoreDetailResource;

class StoreController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getStoreDetail']]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $data = CategoriesResource::collection(Categories::where('is_active', 1)->orderBy('order_by', 'ASC')
            ->get());
        return response()
            ->json(['data' => ['categories' => $data], 'message' => trans('api.Offer Listing', array(), $app_language), 'status' => 1]);
    }


    /**
     * CREATE Store
     *
    
     * @param  [string] user_id
     * @param  [string] category_id
     * @return [string] store_title
     * @return [string] sub_title
     * @return [string] store_tags
     * @return [file] image
     * @return [string] description
     * @return \Illuminate\Http\JsonResponse
     */
    public function createStore(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        $validation_rules = array(
            'user_id' => ['required'],
            'category_id' => ['required', 'int'],
            'store_title' => ['required', 'string', 'max:100'],
            'sub_title' => [
                'required',
                'string',
                'max:100'
            ],
            'store_tags' => ['required'],
            'image' => [
                'file',
                'image', 'mimes:jpg,jpeg,png,svg'
            ],
            'description' => ['required']

        );

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
                    //$store_your_data= $request->input('store_your_data')!=""?$request->input('store_your_data'):0;
                    $royalty_amount = $request->input('royalty_amount') != "" ? $request->input('royalty_amount') : 0;
                    $increase_batch_minting = $request->input('increase_batch_minting') != "" ? $request->input('increase_batch_minting') : 0;

                    $store = Stores::create(['user_id' => $user->id, 'category_id' => $request->input('category_id'), 'store_title' => $request->input('store_title'), 'sub_title' => $request->input('sub_title'), 'store_tags' => $request->input('store_tags'), 'description' => $request->input('description'), 'store_your_data' => 1, 'royalty_amount' => $royalty_amount, 'increase_batch_minting' => $increase_batch_minting, 'is_active' => 1,]);

                    if ($request->hasFile('image')) {
                        $path = 'uploads/stores/' . $store->id;
                        if (!Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))
                            ->exists($path)) {
                            Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->makeDirectory($path);
                        }
                        $destinationPath = 'uploads/stores/' . $store->id;
                        $file = $request->image;
                        try {
                            $img = Image::make($file->getRealPath())->resize(1920, 1280, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        } catch (NotReadableException $e) {
                            return response()->json(['status' => 1, 'message' => "File Type Not Supported"], 200, ['Content-Type' => 'application/json']);
                        }

                        $image_name = 'store_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                        Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))
                            ->put($destinationPath . '/' . $image_name, $img->stream()
                                ->__toString());
                        $store->image = $image_name;
                        $store->save();
                    }
                    DB::commit();
                    return response()
                        ->json([

                            'status' => 1, 'message' => trans('api.Your store has been created successfully.', array(), $app_language)
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
    /**
     *Get user Stores
     * @param  [integer] user_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStoresByUser(Request $request)
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
                $stores = Stores::where('is_active', 1)->where('user_id', $user->id);
                $total_stores =  $stores->count();
                if ($request->has('search_store') && $request->search_store <> '') {
                    $stores->where('store_title', 'LIKE', '%' . $request->search_store . '%');
                }
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $stores = $stores->offset($offset)->limit($limit)->get();
                if (!$stores->isEmpty()) {
                    return UserStoresResource::collection($stores)->additional(
                        [
                            'total_records' => $total_stores,
                            'message' => trans('api.Stores Listing', array(), $app_language),
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
     *Get user Stores list
     * @param  [integer] user_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUserStoreslist(Request $request)
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
                $stores = Stores::where('is_active', 1)->where('user_id', $user->id)->orwhere('id', 0);
                $total_stores =  $stores->count();
                if ($request->has('search_store') && $request->search_store <> '') {
                    $stores->where('store_title', 'LIKE', '%' . $request->search_store . '%');
                }
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset = $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $stores = $stores->offset($offset)->limit($limit)->get();

                if (!$stores->isEmpty()) {
                    return UserStoresResource::collection($stores)->additional(
                        [
                            'total_records' => $total_stores,
                            'message' => trans('api.Stores Listing', array(), $app_language),
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
     *Get  Store Details
     * @param  [integer] user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoreDetail($id, Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (decodeApiIds($id) != 0) {
            $store = Stores::where('id', decodeApiIds($id))->first();
            if ($store) {
                return (new StoreDetailResource($store))
                    ->additional([
                        // 'additional' => ,
                        'message' => trans('api.Store Detail', array(), $app_language),
                        'status'  => 1
                    ]);
            } else {
                return response()->json(['status' => 0, 'message' => trans('api.No record found', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        }
    }
}
