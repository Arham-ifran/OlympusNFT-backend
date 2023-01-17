<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Products;
use App\Models\Categories;
use App\Models\AuctionLength;
use App\Models\Transactions;
use App\Models\ProductMediaFiles;
use Storage;
use Image;
use App\Jobs\SetAuctionWinnerBid;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListingResource;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ItemsResource;
use DB;
use Carbon\Carbon;
use App\Models\AdProducts;
use App\Models\ProductViews;
use App\Models\Orders;
use App\Http\Resources\ActiveAdsProductsResource;
use Intervention\Image\Exception\NotReadableException;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['Allproducts', 'ProductsbyCategory', 'ProductById', 'mostWatchProducts', 'getUserPublicProducts', 'IncreaseProductViewCounter','topSellingProducts']]);
    }

    public function product(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $categories = Categories::select('id', 'title', 'is_active')->where('is_active', 1)->orderBy('order_by', 'ASC')->get();
        $auction_length = AuctionLength::select('id', 'title', 'is_active')->where('is_active', 1)->orderBy('id', 'DESC')->get();
        return response()->json(
            [
                'data' => [
                    'categories' => $categories,
                    'auction_length' => $auction_length,
                ],
                'message' => trans('api.Create item data.', array(), $app_language),
                'status' => 1
            ]
        );
    }



    /**
     * CREATE PRODUCT
     *
     * @param  [integer] user_id
     * @param  [integer] category_id
     * @param  [string] title
     * @param  [string] sub_title
     * @return [string] description
     * @return [file] files
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_item(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        $validation_rules = array(
            'user_id'       => 'required',
            'category_id'      => 'required',
            'title' => 'required|min:4',
            'sub_title' => 'required|min:4',
            'description'  => 'required|string',
            'original_creator' =>    'required',
            'transaction_hash' =>    'required',
            'public_files' =>    'required',
            // 'price_paid' =>    'required',

        );

        $price_type = $request->has('price_type') ? $request->price_type : 0;
        $price_paid = $request->has('price_paid') ? $request->price_paid : 0;
        if ($price_type == 0) {

            $validation_rules['price_usd'] = 'required';
            $messages = [
                'price_usd.required' => "Price USD is required.",

            ];
        } elseif ($price_type == 1) {

            $validation_rules['bid_price_usd'] = 'required';
            $validation_rules['auction_length_id'] = 'required';
            $aution_Length = AuctionLength::where('id', $input['auction_length_id'])->first();
            $input['auction_time'] = getAution($aution_Length->title)->timestamp;
            $messages = [
                'bid_price_usd.required' => "Bid Price USD is required.",
                'auction_length_id.required' => "Aunction length is required.",
            ];
        } else {

            $validation_rules['price_usd'] = 'required';
            $validation_rules['bid_price_usd'] = 'required';
            $validation_rules['auction_length_id'] = 'required';
            $aution_Length = AuctionLength::where('id', $input['auction_length_id'])->first();
            $input['auction_time'] = getAution($aution_Length->title)->timestamp;
            $messages = [
                'price_usd.required' => "Price USD is required.",
                'bid_price_usd.required' => "Bid Price USD is required",
                'auction_length_id.required' => "Aunction length is required.",
            ];
        }

        $messages = [
            'user_id.required' => "User is required.",
            'category_id.required' => "Category is required.",
            'title.required' => "Title is required",
            'title.min' => "Title most be more than 3 characters.",
            'sub_title.required' => "Subtitle is required.",
            'sub_title.min' => "SUb title most be more than 3 characters.",
            'description.required' => "Description is required.",
            'original_creator.required' => "Original creator is required.",
            'transaction_hash.required' => "Transaction hash is required.",
            'public_files.required' => "Files are required.",
            // 'price_paid.required' => "Price paid is required",
        ];

        $validator = Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["user_id"]) != 0) {
            $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
            if ($user) {
                DB::beginTransaction();
                try {

                    $input["user_id"]  = $user->id;
                    if ($request->has('store_id')) {
                        $input["store_id"]  = decodeApiIds($input["store_id"]);
                    } else {
                        $input["store_id"] = 0;
                    }

                    $input["is_active"]  = 1;
                    $input["contract_address"]  = $request->contract;
                    if ($request->has('royalty_percentage') && $input["royalty_percentage"] > 0) {
                        $input["royalty_address"] = $input["original_creator"];
                    } else {
                        $input["royalty_address"] = '0x0000000000000000000000000000000000000000';
                        $input["royalty_percentage"] = 0;
                    }
                    $input['current_owner'] =  $input['original_creator'];
                    if ($request->has('quantity')  && $request->quantity > 0) {
                        $input['quantity'] =  $input['quantity'];
                        $input['available_quantity'] =  $input['quantity'];
                    } else {
                        $input['quantity'] =  1;
                        $input['available_quantity'] =  1;
                    }

                    $uniqueId =  (string) Str::uuid();

                    /** replicating the products on base of quantity token ids*/
                    $productsTokens = json_decode($request->token_id, true);
                    if (count($productsTokens) > 0) {
                        $otherProds = null;
                        foreach ($productsTokens as $key =>  $tok) {
                            $input['token_id'] = $tok;
                            $product = Products::create($input);
                            if ($key == 0) {
                                $otherProds = $product;
                                $product->parent_product_id = encode($otherProds->id) . '-' . $uniqueId;
                                $product->available_quantity = 1;

                                $product->save();
                            }
                            if ($key > 0) {
                                $product->parent_product_id = encode($otherProds->id) . '-' . $uniqueId;
                                $product->available_quantity = 1;

                                $product->save();
                            }
                            $public_files = json_decode($input["public_files"], true);
                            $count = 0;

                            foreach ($public_files as $value) {
                                $is_token_image =  $count == 0 ? 1 : 0;
                                $product->mediaFiles()->create([
                                    'product_id ' => $product->id,
                                    'ipfs_image_hash' => json_encode($value),
                                    'is_token_image' => $is_token_image,
                                    'is_active' => 1
                                ]);
                                $count++;
                            }
                            if ($request->has('is_private_files') && $request->is_private_files == 1) {
                                $private_files = json_decode($input["private_files"], true);
                                foreach ($private_files as $key =>  $value) {
                                    $product->mediaFiles()->create([
                                        'product_id ' => $product->id,
                                        'is_private_file' => 1,
                                        'ipfs_image_hash' => json_encode($value),
                                        'is_active' => 1
                                    ]);
                                }
                            }
                            if ($product) {
                                Transactions::create([
                                    'user_id' => $user->id,
                                    'transaction_of' => 1,
                                    'product_id' => $product->id,
                                    'type' => 0,
                                    'to_address' => $product->contract_address,
                                    'from_address' => $product->original_creator,
                                    'transaction_hash' => $product->transaction_hash,
                                    'paid_price' => $price_paid,
                                    'transaction_status' => 1,
                                    'is_active' => 1
                                ]);
                            }
                            /**job**/
                            if ($price_type != 0) {
                                // SetAuctionWinnerBid::dispatch($product->id)->delay(now()->addMinutes(1));
                                SetAuctionWinnerBid::dispatch($product->id)->delay(getAution($aution_Length->title));
                            }
                        }
                    } else {
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' =>   'Invalid data provided.'
                        ], 200, ['Content-Type' => 'application/json']);
                    }

                    DB::commit();
                    /** **/
                    return response()->json([
                        'status' => 1,
                        'message' => trans('api.Your item  has been created successfully.', array(), $app_language),
                    ], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' =>   $e->getMessage()
                    ], 200, ['Content-Type' => 'application/json']);
                }
            } else {
                return response()->json(['status' => 0, 'message' =>  trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid User id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }


    /**
     * GET PRODUCT BY ID FOR EDIT
     * @param  [integer] product_id
     * @return \Illuminate\Http\JsonResponse
     */


    public function edit_item(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [

            'product_id' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }
        if (decodeApiIds($input["product_id"]) != 0) {

            $product = Products::where('id', decodeApiIds($input["product_id"]))->first();

            if ($product) {

                return (new ProductResource($product))
                    ->additional([
                        'message' =>  trans('api.Product Details', array(), $app_language),
                        'status' => 1
                    ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' =>  trans('api.Sorry no record found', array(), $app_language)
                ], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>  trans('api.Invalid Product id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }


    /**
     * UPDATE PRODUCT
     *
     * @param  [integer] user_id
     * @param  [integer] category_id
     * @param  [string] title
     * @param  [string] sub_title
     * @return [string] description
     * @return [file] files
     * @return \Illuminate\Http\JsonResponse
     */



    public function update_item(Request $request)
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
            'listing_tag'  => 'required|string',
            'description'  => 'required|string|min:8',
            //'public_files' =>    'required'

        );

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()]);
        }

        if (decodeApiIds($input["product_id"]) != 0) {
            $product = Products::findOrFail(decodeApiIds($input["product_id"]));

            if ($request->has('store_id')) {
                $input["store_id"]  = decodeApiIds($input["store_id"]);
            }


            if ($product) {
                $user = User::where('id', decodeApiIds($input["user_id"]))->where('is_active', 1)->first();
                if ($user) {
                    DB::beginTransaction();
                    try {
                        $product->store_id = $input["store_id"];
                        $product->category_id = $request->category_id;
                        $product->title = $request->title;
                        $product->sub_title = $request->sub_title;
                        $product->description = $request->description;
                        $product->listing_tag = $request->listing_tag;
                        $product->bid_price_usd = $request->bid_price_usd;
                        if ($price_type == 0) {
                            $product->auction_length_id = $request->auction_length_id;
                        }
                        $product->price_type = $request->price_type;
                        $product->price_usd = $request->price_usd;


                        if ($request->has('relist_item') && $request->relist_item == 1) {

                            $product->is_sold = 0;
                            $product->quantity = 1;
                            $product->available_quantity = 1;
                            $product->is_relisted_product = 1;
                            $product->parent_product_id = encode($product->id) . '-' . (string) Str::uuid();
                            if ($price_type == 1 || $price_type == 2) {
                                $product->bids()->delete();
                            }
                        }
                        unset($input["user_id"]);
                        $product->save();
                        // $images = $request->file('files');
                        // $path = 'uploads/products/' . $product->id . '/preview-files';
                        // if ($request->hasfile('files')) {
                        //     $this->uploadFile($product, $images, $path, $is_private = 0, $action = "update");
                        // }
                        if ($request->has('public_files')) {
                            $public_files = json_decode($input["public_files"], true);
                            $count = 0;
                            //ProductMediaFiles::where('product_id', $product->id)->delete();
                            if ($public_files) {
                                foreach ($public_files as $value) {
                                    // $is_token_image =  $count == 0 ? 1 : 0;
                                    $product->mediaFiles()->create([
                                        'product_id ' => $product->id,
                                        'ipfs_image_hash' => json_encode($value),
                                        'is_token_image' => 0,
                                        'is_active' => 1
                                    ]);
                                    $count++;
                                }
                            }
                        }

                        if ($product) {
                            Transactions::create([
                                'user_id' => $user->id,
                                'transaction_of' => 1,
                                'product_id' => $product->id,
                                'type' => 0,
                                'to_address' => $product->contract_address,
                                'from_address' => $product->current_owner,
                                'transaction_hash' => $product->transaction_hash,
                                'paid_price' => 0,
                                'transaction_status' => 1,
                                'is_active' => 1
                            ]);
                        }

                        DB::commit();
                        return response()->json([
                            'status' => 1,
                            'message' =>  trans('api.Your item  has been updated successfully.', array(), $app_language)
                        ], 200, ['Content-Type' => 'application/json']);
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => 0,
                            'message' =>  $e->getMessage()
                        ], 200, ['Content-Type' => 'application/json']);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' =>  trans('api.User not Exists.', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
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


    /**
     * DELETE MEDIA FILE
     *
     * @param  [integer] file_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function delete_file(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|int',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }

        $media_file = ProductMediaFiles::where('id', $request->file_id)->first();

        if ($media_file) {
            DB::beginTransaction();
            try {
                ProductMediaFiles::destroy($request->file_id);
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => "file Deleted successfully"
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
    }


    /**
     * GET USER PRODUCTS
     *
     * @param  [integer] user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_products(Request $request)
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
            $user = User::where(['id' => decodeApiIds($input["user_id"])])->where('is_active', 1)->first();

            if ($user) {

                $products = Products::where(['is_active' => 1, 'current_owner' => $user->wallet_address, 'is_relisted_product' => 0])
                    ->where('is_sold', 0);


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

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $products = $products
                    ->offset($offset)->limit($limit)
                    ->orderby("id", "DESC")->get();

                if (!$products->isEmpty()) {
                    return ProductListingResource::collection($products)
                        ->additional([
                            'total_records' => $total_products,
                            'message' => trans('api.Product Listing', array(), $app_language),
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
     * GET ALL PRODUCT OR GET PRODUCT BY FILTERS
     * @return \Illuminate\Http\JsonResponse
     */
    public function Allproducts(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';



        /** AD Products**/
        $active_ad_product = [];
        $current_date = Carbon::now()->timestamp;
        $active_ads_products = AdProducts::inRandomOrder();
        $active_ads_products = $active_ads_products->WhereHas('ad', function ($q) use ($current_date, $request) {

            $q = $q->where('is_active', 1);
            $q = $q->where('start_date', '<=', $current_date);
            $q = $q->where('end_date', '>=', $current_date);
            $q = $q->whereColumn('total_spent', '<', 'total_budget');
        })
            ->when($request->has('category'), function ($q) use ($request) {

                $q->whereHas('product.category', function ($qu) use ($request) {

                    $qu->where('url', $request->category);
                });
            })
            ->limit(config('constants.ADS_PRODUCT_LIMIT'))->get();
        if (!$active_ads_products->isEmpty()) {
            $active_ad_product = ActiveAdsProductsResource::collection($active_ads_products);
        }




        $plucked = $active_ads_products->pluck('product_id');

        $ad_product_ids = $plucked->all();
        $products = Products::select('*')
            ->addSelect(DB::raw('IF(price_type=1, bid_price_usd, price_usd ) AS price'))
            ->where('is_active', 1)
            ->where('is_sold', 0)

            ->when($request->has('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('url', $request->category);
                });
            })
            ->when($request->has('transfer_copyright'), function ($query) use ($request) {
                $query->where('transfer_copyright_when_purchased', 1);
            })
            ->when($request->has('resellable'), function ($query) use ($request) {
                $query->where('is_allow_buyer_to_resell', 1);
            })
            ->when($request->has('only_auction'), function ($query) use ($request) {
                $query->where(function ($q) {
                    $q->where('price_type', 1)
                        ->orWhere('price_type', 2);
                });
            })

            ->when($request->has('min') && $request->has('max'), function ($query) use ($request) {

                $min_price = $request->min;
                $max_price = $request->max;
                $query->where(function ($q) use ($min_price, $max_price) {
                    $q->whereBetween('price_usd', [$min_price, $max_price]);
                    $q->orWhereBetween('bid_price_usd', [$min_price, $max_price]);
                });
            })
            /** Order By **/
            ->when($request->has('order_by'), function ($query) use ($request) {
                if ($request->order_by == "newest") {
                    return $query->orderBy('created_at', 'DESC');
                }
                if ($request->order_by == "oldest") {
                    return $query->orderBy('created_at', 'ASC');
                } elseif ($request->order_by == "higher") {
                    return $query->orderBy('price', 'Desc');
                } elseif ($request->order_by == "lower") {
                    return $query->orderBy('price', 'Asc');
                }
            }, function ($query) {
                return $query->orderBy('created_at', 'DESC');
            });

        $products = $products->whereNotIn('id', $ad_product_ids);

        $total_products =  $products->groupBy('parent_product_id')->count();
        $limit = $request->limit;
        $offset = $request->offset;

        $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

        $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
        $offset = $limit * $offset;
        $products = $products->offset($offset)->limit($limit)->get();

        if (!$products->isEmpty()) {


            return ProductsResource::collection($products)
                ->additional([
                    'boosted_products' =>  $active_ad_product,
                    'total_records' => $total_products,
                    'message' =>  trans('api.Product Listing', array(), $app_language),
                    'status'  => 1
                ]);
        } else {

            return response()->json(
                [
                    'status' => 0,
                    'data' => [],
                    'message' =>  trans('api.No record found', array(), $app_language)
                ]
            );
        }
    }



    /**
     * GET  PRODUCTS BY CATEGORY ID
     *
     * @param  [integer] id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ProductsbyCategory($id = null, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (!$id) {
            $categories = Categories::where('is_active', 1)->orderBy('order_by','ASC')->first();
            $id = $categories->id; 
        }
        $products = Products::where('category_id', $id)->where('is_active', 1)->groupBy('parent_product_id')->orderBy('id', 'DESC')->limit(config('constants.PRODUCT_BY_CATEGORY_LIMIT'))->get();
        if (!$products->isEmpty()) {
            return ProductsResource::collection($products)
                ->additional([
                    'total_records' => count($products),
                    'message' => trans('api.Product Listing', array(), $app_language),
                    'status'  => 1
                ]);
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.No record found', array(), $app_language)
            ]);
        }
    }


    /**
     * GET MOST WATCH PRODUCTS
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function mostWatchProducts(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $products = Products::select('id', 'store_id', 'user_id', 'title', 'sub_title', 'slug', 'is_active', 'current_owner', 'quantity','price_usd','bid_price_usd')->where('is_active', 1)->groupBy('parent_product_id')->orderBy('view_count', 'desc')->limit(config('constants.MOST_WATCH_PRODUCT_LIMIT'))->get();
        if (!$products->isEmpty()) {
            return ProductListingResource::collection($products)
                ->additional([

                    'message' => trans('api.Product Listing', array(), $app_language),
                    'status'  => 1
                ]);
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.No record found', array(), $app_language)
            ]);
        }
    }


    /**
     * GET PRODUCT BY ID
     *
     * @param  [integer] id
     * @return \Illuminate\Http\JsonResponse
     */

    public function ProductById($id, Request $request)
    {


        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if (decodeApiIds($id) != 0) {
            $product = Products::where('id', decodeApiIds($id))->where('is_active', 1)->first();
            if ($product) {
                $ip = request()->ip();
                $is_viewed = 0;
                $productView = ProductViews::where('ip_address', $ip)->where('product_id', decodeApiIds($id));
                $productView = $productView->when($request->has('user_id'), function ($query) use ($request) {
                    $query->where('user_id', decodeApiIds($request->user_id));
                })->first();
                if ($productView)  $is_viewed = 1;
                return (new ProductDetailResource($product, $is_viewed))
                    ->additional([
                        'message' => trans('api.Product Details', array(), $app_language),
                        'status' => 1
                    ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => trans('api.Sorry no record found', array(), $app_language)
                ], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => trans('api.Invalid Product id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }



    /**
     *CUSTOM FUNTION FOR UPLOAD PUBLIC AND PRIVATE FILES
     *
     * @param  [integer] product_id
     * @param  [file] images
     * @param  [string] upload_path
     * @param  [boolean] is_private
     * @return [string] action
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request, $product, $images, $upload_path, $is_private, $action)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        if ($action == "create" && $is_private == 0)
            $is_token_image = 1;
        else if ($action == "edit" && $product->mediaFiles->isEmpty())
            $is_token_image = 1;

        else
            $is_token_image = 0;


        foreach ($images as $image) {

            $type      = strtolower($image->getClientOriginalExtension());
            $destinationPath = $upload_path;
            $file = $image;
            $image_name      = 'product_' . time() . rand() . '.' . $file->getClientOriginalExtension();

            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                try {
                    $img = Image::make($file->getRealPath())->resize(1920, 1280, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } catch (NotReadableException $e) {

                    return response()->json([
                        'status' => 1,
                        'message' => trans('api.File Type Not Supported', array(), $app_language)
                    ], 200, ['Content-Type' => 'application/json']);
                }
                Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->put($destinationPath . '/' . $image_name, $img->stream()->__toString());
            } else {

                Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->put($destinationPath . '/' . $image_name, 'Contents');
            }
            DB::beginTransaction();
            try {
                $product->mediaFiles()->create([
                    'product_id ' => $product->id,
                    'name' => $image_name,
                    'is_token_image' => $is_token_image,
                    'is_private_file' => $is_private,
                    'is_active' => 1
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => 0, 'message' => $e->getMessage()], 200, ['Content-Type' => 'application/json']);
            }

            $is_token_image = 0;
        }
    }


    /**
     * getUserPublicProducts
     *
     * @param  mixed $username
     * @param  mixed $request
     * @return void
     */
    public function getUserPublicProducts($username, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();

        if ($username) {
            $user = User::select('id', 'is_active', 'wallet_address')->where(['username' => $username, 'is_active' => 1])->first();

            if ($user) {
                $products = Products::Select('*')->addSelect(DB::raw('IF(price_type=1, bid_price_usd, price_usd ) AS price'))->where('is_active', 1)->where('current_owner', $user->wallet_address);

                if ($request->has('search_product') && $request->search_product <> '') {
                    $search_product = $request->search_product;
                    $products->where(function ($query) use ($search_product) {
                        $query->where('title', 'LIKE', '%' . $search_product . '%');
                        $query->orwhere('sub_title', 'LIKE', '%' . $search_product . '%');
                        $query->orwhere('description', 'LIKE', '%' . $search_product . '%');
                    });
                }


                $products->when($request->has('category'), function ($query) use ($request) {
                    $query->whereHas('category', function ($q) use ($request) {
                        $q->where('url', $request->category);
                    });
                });
                $products->when($request->has('item_type'), function ($query) use ($request) {
                    if ($request->item_type == "sold-items") {
                        $query->where('is_sold', 1);
                    }
                });

                /** Order By **/
                $products->when($request->has('order_by'), function ($query) use ($request) {
                    if ($request->order_by == "newest") {
                        return $query->orderBy('created_at', 'DESC');
                    }
                    if ($request->order_by == "oldest") {
                        return $query->orderBy('created_at', 'ASC');
                    } elseif ($request->order_by == "higher") {
                        return $query->orderBy('price', 'Desc');
                    } elseif ($request->order_by == "lower") {
                        return $query->orderBy('price', 'Asc');
                    }
                }, function ($query) {
                    return $query->orderBy('created_at', 'DESC');
                });


                $total_products =  $products->count();
                $limit = $request->limit;
                $offset = $request->offset;

                $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

                $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
                $offset = $limit * $offset;
                $products = $products->offset($offset)->limit($limit)->get();
                if (!$products->isEmpty()) {

                    return ItemsResource::collection($products)
                        ->additional([
                            'total_records' => $total_products,
                            'message' => trans('api.Product Listing', array(), $app_language),
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
     * DELETE Product
     *
     * @param  [integer] file_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteProduct(Request $request)
    {
        $input = $request->all();
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'productId' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }
        if (decodeApiIds($input["productId"]) != 0) {
            $product = Products::where('id', decodeApiIds($input["productId"]))->first();

            if ($product) {
                DB::beginTransaction();
                try {
                    $product->update(['is_active' => 2]);
                    DB::commit();
                    return response()->json([
                        'status' => 1,
                        'message' => "Product Deleted successfully"
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
                ->json(['status' => 0, 'message' => trans('api.Invalid Order id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Update View Counter
     *
     * @param  [integer] file_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function IncreaseProductViewCounter(Request $request)
    {
        $input = $request->all();
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'productId' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400, ['Content-Type' => 'application/json']);
        }

        $user_id = $request->has('userId') ? decodeApiIds($request->userId) : NULL;
        $ip = request()->ip();
        if (decodeApiIds($input["productId"]) != 0) {
            $product = Products::where('id', decodeApiIds($input["productId"]))->first();

            if ($product) {
                DB::beginTransaction();
                try {
                    $product->view_count++;
                    $product->save();
                    ProductViews::create([
                        'user_id' => $user_id,
                        'product_id' => decodeApiIds($input["productId"]),
                        'ip_address' => $ip,
                        'is_view' => 1,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status' => 1,
                        'message' => "Product view counter increase successfully"
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
                ->json(['status' => 0, 'message' => trans('api.Invalid Product id', array(), $app_language)], 403, ['Content-Type' => 'application/json']);
        }
    }



    public function topSellingProducts(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $ids = Orders::select('product_id', DB::raw('count(*) as total'))
            ->inRandomOrder()
            ->groupBy('product_id')
            ->orderByRaw('count(*) DESC')
            ->limit(config('constants.TOP_SELLING_PRODUCT_LIMIT'))
            ->pluck('product_id');
      
        $products = Products::whereIn('id', $ids)->get();
       
        if (!$products->isEmpty()) {
            return ProductListingResource::collection($products)
                ->additional([

                    'message' => trans('api.Product Listing', array(), $app_language),
                    'status'  => 1
                ]);
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.No record found', array(), $app_language)
            ]);
        }
    }
}
