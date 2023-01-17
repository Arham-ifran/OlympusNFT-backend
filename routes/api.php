<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::get('profile/{id}', 'AuthController@profile');
    Route::post('update-profile', 'AuthController@updateProfile');
    Route::post('update-wallet-address', 'AuthController@updateWalletAddress');
    //Route::get('auth/verify-account/{id}', 'AuthController@verifyAccount');
    //Route::post('auth/resend-verification-email', 'AuthController@resendVerificationEmail');
    Route::post('check-user', 'AuthController@checkUser');
    Route::post('upload-user-image', 'AuthController@uploadUserImage');
    Route::post('updated-password', 'AuthController@updatePassword');
    Route::get('get-user-transactions', 'AuthController@userTransactions');
    /**
     * PUBLIC PROFILE
     */
    Route::get('public-profile/{username}', 'AuthController@sellerProfile');

    Route::post('forgot-password', 'PasswordResetController@sendResetLink');
    Route::get('validate-reset-token/{token}', 'PasswordResetController@validateResetToken');
    Route::post('reset-password', 'PasswordResetController@reset');

    /*--------video guides route--------*/
    Route::get('video-guide', 'CommonController@getAllVideoGuides');

    Route::get('all-categories','CommonController@getAllCategories');

    /*------------setting route------------------*/
    Route::get('settings', 'CommonController@settings');
    /*-------------Categories---------------*/
    Route::get('all-categories', 'CommonController@getAllCategories');
    Route::get('main-categories', 'CommonController@getMainCategories');
    /*------------Product Route--------------*/

    Route::get('product', 'ProductController@product');
    Route::post('create-product', 'ProductController@create_item');
    Route::post('edit-product', 'ProductController@edit_item');
    Route::post('delete-file', 'ProductController@delete_file');
    Route::post('update-product', 'ProductController@update_item');

    Route::get('user-products', 'ProductController@user_products');
    

    Route::get('all-products', 'ProductController@Allproducts');

    Route::get('products-category/{id?}', 'ProductController@ProductsbyCategory');
    Route::get('product/{id}', 'ProductController@ProductById');
    Route::get('most-watched-products', 'ProductController@mostWatchProducts');
    Route::post('delete-product', 'ProductController@deleteProduct');
    Route::post('increase-product-view-counter', 'ProductController@IncreaseProductViewCounter');
    Route::get('top-selling-products', 'ProductController@topSellingProducts');
    /**
     * FOR PUBLIC PROFILE
     */
    Route::post('get-user-products/{username}', 'ProductController@getUserPublicProducts');

    /*--------categories languages route--------*/
    Route::get('categories-languages', 'CommonController@getAllCategoriesAndLanguages');

    /*--------Change password route--------*/


    /*--------Manage Faqs route--------*/
    Route::get('faqs', 'CommonController@faqs');
    // Route::get('manage-faqs/{id}', 'CommonController@ManageFaqById');

    /*--------report this item route--------*/
    Route::get('product_report_abuses', 'CommonController@productReportAbuses');
    Route::post('report-item', 'CommonController@ReportItem');

    /*--------store route--------*/
    Route::get('store', 'StoreController@store');
    Route::post('create-store', 'StoreController@createStore');
    Route::get('get-store-detail/{id}', 'StoreController@getStoreDetail');
    Route::get('get-user-stores', 'StoreController@getStoresByUser');
    Route::get('get-user-stores-list', 'StoreController@getUserStoreslist');

    /**--------NFTS route----------- **/

    Route::get('user-nfts-list', 'NftsController@item_list');
    
    Route::post('resell-item', 'NftsController@resellItem');
    /**--------Messages route----------- **/

    Route::get('get-users-for-message', 'MessageController@getUsersForMessage');
    Route::post('send-message', 'MessageController@sendMessage');
    Route::get('fetch-message-threads', 'MessageController@fetchMessageThreads');
    Route::get('fetch-messages', 'MessageController@fetchMessages');


    /**-----------Review Route-----------**/
    Route::post('create-review', 'ReviewController@createReview');
    Route::get('reviews/{username}', 'ReviewController@getReviews');
    Route::get('get-user-reviews/{user_id}', 'ReviewController@getUserReviews');
    Route::get('review-detail', 'ReviewController@reviewDetail');
    /**-----------Bid Route-----------**/
    Route::post('create-bid', 'BidController@createBid');
    Route::get('my-bids', 'BidController@myBids');

    /**-----------Ads Route-----------**/
    Route::post('create-user-ad', 'AdController@createUserAd');
    Route::get('user-ads', 'AdController@userAdsList');
    Route::post('delete-ad', 'AdController@deleteAd');
    Route::get('ad-boasted-product-click', 'AdController@adBoastedProductClick');
    /*-----------cms page route-----------*/
    Route::get('cms-pages', 'CommonController@getCmsPages');
    Route::get('cms-page/{seo_url}', 'CommonController@getCmsPageByUrl');

    /*-----------subscribe route----------*/
    Route::post('subscribe-us', 'CommonController@subscribeUs');

  /*-----------Order route----------*/
  Route::post('create-order', 'OrderController@createOrder');
  Route::get('user-sold-products', 'OrderController@userSoldProducts');
  Route::get('my-bought-items', 'OrderController@myBoughtItems');
  Route::get('won-auctions', 'OrderController@wonAuctions');
  Route::get('my-earning', 'OrderController@myEarning');

  /** homePage Banner **/
  Route::get('home-page-banner-images', 'CommonController@getHomePageBannerImages');
  Route::get('ads-products','CommonController@getAdsProduct');
 /*-----------cms page route-----------*/
 Route::get('blogs/{category_slug}', 'CommonController@getBlogs');
 Route::get('blog/{seo_url}', 'CommonController@getBlogByUrl');
/*-----------contact us route-----------*/
Route::post('contact-us', 'CommonController@contactUs');
    Route::any('{path}', function () {
        return response()->json([
            'message' => 'required parameter not found'
        ], 404);
    })->where('path', '.*');
});
