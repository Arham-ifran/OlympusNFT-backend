<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Models\Stores;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['verify' => true]);

Route::get('/clear-cache', function () {
    Artisan::call('storage:link'); //
    Artisan::call('optimize:clear'); //storage:link
    dd('cache clear done.123');
});
Route::get('/migrate', function () {
    $re = Artisan::call('migrate');
    dd('migration done.123');
});
Route::get('/seeder/{seederclass}', function ($seederclass) {
    $re = Artisan::call('db:seed --class=' . $seederclass);
    dd($re);
});
Route::get('/job', function () {
    Artisan::call('queue:table');
    Artisan::call('queue:failed-table');
    dd('queue job');
});

// Route::get('/create-store-slug', function () {
//     $slugs =Stores::select('id','slug','store_title')->where('is_active', 1)->get();
//     $count = 1;
//     foreach ($slugs as $slug) {

//     if(empty($slug->slug)){

//          $slug_title= $slug->store_title."-".$count++;
//          $updated_slug = Str::slug($slug_title);

//       }
//       else{

//         $updated_slug = "{$slug->slug}-" . $count++;
//       }


//       $product_slug = Stores::where(['id' => $slug->id])
//             ->first();
//         $product_slug->update([
//             'slug' => $updated_slug,
//         ]);

//     }
//     dd('done');

//});
// Route::namespace('Auth')->group(function () {
//     //Login Routes
//     // Route::get('/', 'LoginController@showLoginForm')->name('login');
//     // Route::get('/login', 'LoginController@showLoginForm')->name('login');
//     // Route::post('/login', 'LoginController@login')->name('login.submit');
//     Route::get('/logout', 'LoginController@logout')->name('logout');
// });

// Route::group(['namespace' => 'Frontend'], function () {

//     Route::get('/', 'HomeController@index')->name('home');
//     Route::get('lang/{locale}', 'LocalizationController@lang');

//     Route::post('/subscribe-us', 'HomeController@subscribe_us');

//     $pages = \DB::table('cms_pages')->select('seo_url')->where('is_active', 1)->get();
//     foreach ($pages as $page) {
//         Route::get($page->seo_url, 'HomeController@cms_pages');
//     }
//     Route::get('contact-us', 'HomeController@contact_us');
//     Route::post('contact-us', 'HomeController@contact_us')->name('contact-us');
// });


Route::prefix('/admin')->name('admin.')->namespace('Admin')->group(function () {

    Route::namespace('Auth')->group(function () {
        //Login Routes
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::post('/login', 'LoginController@login')->name('login.submit');
        Route::get('/logout', 'LoginController@logout')->name('logout');
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'auth:admin'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/site-settings', 'SiteSettingsController@index');
    Route::post('/site-settings/update', 'SiteSettingsController@update')->name('site-settings.update');

    Route::resource('/roles', 'RolesController');
    Route::resource('/permissions', 'PermissionsController');
    //Admin Users
    Route::resource('/users', 'UsersController');
    Route::get('/profile', 'UsersController@profile');
    Route::post('/update-profile', 'UsersController@updateProfile');
    Route::post('users/update-status', 'UsersController@update_status');

    Route::resource('cms-pages', 'CmsPagesController');
    Route::post('cms-pages/update-status', 'CmsPagesController@update_status');
    //Investors
    Route::resource('investors', 'BuyersController');
    Route::post('investors/update-status', 'BuyersController@update_status');
    //Artists
    Route::resource('artists', 'ArtistsController');
    Route::post('artists/update-status', 'ArtistsController@update_status');
    //Musicians
    Route::resource('musicians', 'MusiciansController');
    Route::post('musicians/update-status', 'MusiciansController@update_status');
    //Stores

    Route::resource('stores', 'StoresController');
    Route::post('stores/update-status', 'StoresController@update_status');
    //Products
    Route::resource('products', '\App\Http\Controllers\Admin\ProductsController');
    Route::post('products/update-status', 'ProductsController@update_status');
    //Auction Products
    Route::resource('auction-products', 'AuctionProductController');
    Route::post('auction-products/update-status', 'AuctionProductController@update_status');
    //Orders
    Route::resource('orders', 'OrderController');
    Route::post('orders/update-status', 'OrderController@update_status');
    //Transactions
    Route::resource('transactions', 'TransactionsController');
    Route::post('transactions/update-status', 'TransactionsController@update_status');
    //Bidding History
    Route::get('bidding-history', 'BiddingHistoryController@index')->name('bidding-history.index');
    Route::post('bidding-history/update-status', 'BiddingHistoryController@update_status');
    Route::post('bidding-history/set-wining-bid', 'BiddingHistoryController@update_bid_status');
    //all users
    Route::resource('all-users', 'AllUsersController');
    Route::post('all-users/update-status', 'AllUsersController@update_status');
    //Ad
    Route::resource('ads', 'AdController');
    Route::post('ads/update-status', 'AdController@update_status');
    //Faq Categories
    Route::resource('faq-categories', 'FaqCategoriesController');
    Route::post('faq-categories/update-status', 'FaqCategoriesController@update_status');
    //Faq
    Route::resource('faqs', 'FaqsController');
    Route::post('faqs/update-status', 'FaqsController@update_status');
    //Languages
    Route::resource('languages', 'LanguagesController');
    Route::post('languages/update-status', 'LanguagesController@update_status');
    //Languages
    Route::resource('video-guides', 'VideoGuidesController');
    Route::post('video-guides/update-status', 'VideoGuidesController@update_status');
    //Categories
    Route::resource('categories', 'CategoriesController');
    Route::post('categories/update-status', 'CategoriesController@update_status');
    //templates
    Route::resource('templates', 'TemplatesController');
    Route::post('templates/update-status', 'TemplatesController@update_status');

    Route::get('/messages', 'MessagesController@index');
    Route::post('/messages', 'MessagesController@index')->name('messages.index');

    Route::get('/messages/{id?}/view', 'MessagesController@view');
    Route::post('/send-message', 'MessagesController@send_message')->name('messages.sendMessage');

    Route::get('/reviews', 'ReviewsController@index');
    Route::post('/reviews', 'ReviewsController@index')->name('reviews.index');

    Route::get('/logs', 'LogsController@index');
    Route::post('/logs', 'LogsController@index')->name('logs.index');

    Route::get('/contactus-log', 'ContactUsController@index');
    Route::post('/contactus-log', 'ContactUsController@index')->name('contactus-log.index');
    Route::get('/contactus-log/detail/{id?}', 'ContactUsController@detail');
    Route::post('/contactus-log/send_email', 'ContactUsController@reply')->name('contactus-log.send_email');
    //Product Report Abuse
    Route::resource('product-report-abuses', 'ProductReportAbuseController');
    Route::post('product-report-abuses/update-status', 'ProductReportAbuseController@update_status');
    //Product Report Item
    Route::resource('product-report-items', 'ProductReportItemController');
    //Faq Categories
    Route::resource('blog-categories', 'BlogCategoriesController');
    Route::post('blog-categories/update-status', 'BlogCategoriesController@update_status');
    //Faq
    Route::resource('blogs', 'BlogsController');
    Route::post('blogs/update-status', 'BlogsController@update_status');
    //Banner
    Route::resource('banners', 'BannerController');
    Route::post('banners/update-status', 'BannerController@update_status');
});

Route::get('/', function () {
    return view('app');
});

Route::get('/{url}/{slug?}', function ($url) {
    return view('app');
})->where('url', '(signup|category|product|dashboard|stores|create-store|ad-manager|create-item)');
