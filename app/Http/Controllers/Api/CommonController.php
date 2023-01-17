<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\CmsPages;
use App\Models\Languages;
use App\Models\Categories;
use App\Models\ReportItem;
use App\Models\VideoGuides;
use App\Models\Review;
use App\Models\Blogs;
use App\Models\BlogCategories;
use Illuminate\Http\Request;
use App\Models\FaqCategories;
use App\Models\SiteSettings;
use App\Models\NewsletterSubscribers;
use App\Models\ProductReportAbuse;
use App\Models\ContactUs;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Banners;
use Newsletter;
use App\Http\Resources\HomePageBannerResource;
use App\Http\Resources\BlogsResource;
use Illuminate\Support\Facades\Validator;
use Storage;
use Mail;
use App\Mail\MasterMail;
use App\Models\Templates;
use View;

class CommonController extends Controller
{

    /*=========Video Guides function=========*/
    public function getAllVideoGuides(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $all_video_guides = VideoGuides::where('is_active', 1)->get();
        if (!$all_video_guides->isEmpty()) {
            return response()->json(
                [
                    "data" => $all_video_guides,
                    'status' => 1,
                    "message" => trans('api.get all records successfully', array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {
            return response()->json([
                'status' => 0,
                'message' => trans('api.Video guide Not Found', array(), $app_language)
            ], 200);
        }
    }


    /*=========Categories And Languages function==========*/
    public function getAllCategoriesAndLanguages(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $categories = Categories::where('is_active', 1)->orderBy('order_by', 'ASC')->get();
        $languages = Languages::where('is_active', 1)->get();

        if (!$categories->isEmpty() || !$languages->isEmpty()) {
            return response()->json(
                [
                    "data" => [
                        "categories" => $categories,
                        "languages" => $languages
                    ],
                    'status' => 1,
                    "message" => trans('api.get all records successfully', array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.data Not Found', array(), $app_language)
            ], 404);
        }
    }



    /*=========get All Categories function==========*/
    public function getMainCategories(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $categories = Categories::where('is_active', 1)->orderBy('order_by', 'ASC')->offset(0)->limit(config('constants.MAIN_CATEGORIES_LIMIT'))->get();

        if (!$categories->isEmpty()) {
            return response()->json(
                [
                    "data" => [
                        "categories" => $categories,

                    ],
                    'status' => 1,
                    "message" => trans("api.get all Categories successfully", array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.data Not Found', array(), $app_language)
            ], 404);
        }
    }


    /*=========Manage Faq function==========*/
    public function faqs(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $data['faq_categories'] = FaqCategories::select('id', DB::raw('title as categoryTitle'))->with(['faqs' => function ($q) {
            $q->select('id', 'category_id', 'title', 'description');
            $q->where('is_active', 1);
        }])->where('is_active', 1)->get();

        if (!$data['faq_categories']->isEmpty()) {
            return response()->json(
                [
                    "data" => $data['faq_categories'],
                    'status' => 1,
                    "message" => trans('api.manage faq all records successfully', array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.Manage Faq data Not Found', array(), $app_language)
            ], 404);
        }
    }


    /*=========Manage Faq by id function==========*/
    // public function ManageFaqById($id, Request $request)
    // {
    //     $app_language = $request['language'] <> '' ? $request['language'] : 'en';
    //     $faq_id = DB::table('faqs')->select('id', 'description', 'is_active')->where([
    //         'id' => $id,
    //         'is_active' => 1
    //     ])->get();

    //     if (!$faq_id->isEmpty()) {
    //         return response()->json(
    //             [
    //                 "data" => $faq_id,
    //                 'status' => 1,
    //                 "message" => trans('api.manage faq record by id successfully', array(), $app_language)
    //             ],
    //             200,
    //             ['Content-Type' => 'application/json']
    //         );
    //     } else {

    //         return response()->json([
    //             'status' => 0,
    //             'message' => trans('api.Manage Faq data Not Found', array(), $app_language)
    //         ], 404);
    //     }
    // }

    /*=========Report Item function==========*/
    public function ReportItem(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'let_us_know_why'      => 'required|string',
            'user_id'       =>  'required',
            'product_id'      => 'required',
            'product_report_abuse_id'       =>  'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->all()], 400, ['Content-Type' => 'application/json']);
        }


        if (decodeApiIds($input["user_id"]) != 0 &&  decodeApiIds($input["product_id"]) != 0) {
            $product_id = decodeApiIds($input["product_id"]);
            $user = User::where('id', decodeApiIds($input["user_id"]))->where("is_active", 1)->first();
            if ($user) {
                DB::beginTransaction();
                try {
                    $report_item = ReportItem::create([
                        'let_us_know_why' => $request->input('let_us_know_why'),
                        'user_id'     =>  $user->id,
                        'product_id' => $product_id,
                        'product_report_abuse_id' => $request->input('product_report_abuse_id'),

                    ]);
                    if ($report_item) {
                        DB::commit();
                        return response()->json(
                            [
                                'status' => 1,
                                'message' => trans('api.Your product report has been successfully.', array(), $app_language)
                            ],
                            200,
                            ['Content-Type' => 'application/json']
                        );
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'status' => 0,
                            'message' => trans('api.Oops something went wrong.', array(), $app_language)
                        ],
                        500,
                        ['Content-Type' => 'application/json']
                    );
                }
            } else {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => trans('api.The user you are trying to access is inactive.', array(), $app_language)
                    ],
                    402,
                    ['Content-Type' => 'application/json']
                );
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => trans('api.Unauthorized You are not logged in', array(), $app_language)
            ], 403);
        }
    }

    public function productReportAbuses(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $data['product_reports_abuse'] = ProductReportAbuse::select('id', 'title', 'description')->where('is_active', 1)->get();

        if (!$data['product_reports_abuse']->isEmpty()) {
            return response()->json(
                [
                    "data" => $data['product_reports_abuse'],
                    'status' => 1,
                    "message" => trans('api.Product Report Abuse all records successfully', array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {

            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => trans('api.Product Report Abuse Not Found', array(), $app_language)
            ], 404);
        }
    }



    /*=========get CmsPage function==========*/
    public function getCmsPages(Request $request)
    {

        $app_language = $request['language'] <> '' ? $request['language'] : 'en';

        $cms_pages = CmsPages::where('is_active', 1)
            ->where('show_in_footer', 1)->limit(config('constants.CMS_PAGES_LIMIT'))->get();

        if (!$cms_pages->isEmpty()) {

            return response()->json(
                [
                    'data' => $cms_pages,
                    'status' => 1,
                    'message' => trans('api.Cms pages all records successfully.', array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        } else {

            return response()->json(
                [
                    'status' => 0,
                    'data' => [],
                    'message' => trans('api.The cms page you are trying to access is inactive.', array(), $app_language)
                ],
                402,
                ['Content-Type' => 'application/json']
            );
        }
    }
    /*=========get CmsPage by SEOUrl function==========*/
    public function getCmsPageByUrl($slug, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $cms_page = CmsPages::where('seo_url', $slug)
            ->where('is_active', 1)->first();

        if ($cms_page) {

            return response()->json(
                [
                    'data' => $cms_page,
                    'status' => 1,
                    'message' => trans('api.CMS Page record.', array(), $app_language),
                ],
                200,
                ['Content-Type' => 'application/json']

            );
        } else {
            return response()->json(
                [
                    'status' => 0,
                    'message' => trans('api.Cms Page Not Found.', array(), $app_language)
                ],
                404,
                ['Content-Type' => 'application/json']
            );
        }
    }

    /*=========get blogs function==========*/
    public function getBlogs($categoryslug, Request $request)
    {


        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $blogCategories = BlogCategories::Select('title', 'slug')->where('is_active', 1)->orderby('title', 'ASC')->get();
        $resentBlog = Blogs::Select('title', 'slug')->where('is_active', 1)->orderby('created_at', 'DESC')->limit(config('constants.RECENT_BLOG_POST_LIMIT'))->get();

        $blogs = Blogs::with('blogCategory')->whereHas('blogCategory', function ($q) use ($categoryslug) {
            $q->where('slug', $categoryslug);
        })->where('is_active', 1);

        if ($request->has('search_blog') && $request->search_blog <> '') {
            $search_blog = $request->search_blog;
            $blogs->where(function ($query) use ($search_blog) {
                $query->where('title', 'LIKE', '%' . $search_blog . '%');
                $query->orwhere('description', 'LIKE', '%' . $search_blog . '%');
            });
        }

        $total_blogs =  $blogs->count();
        $limit = $request->limit;
        $offset = $request->offset;

        $limit = $limit == 0 || $limit == "" ? config('constants.DEFAULT_LIMIT') : $limit;

        $offset =  $offset == 0 || $offset == "" ? 0 : $offset;
        $offset = $limit * $offset;
        $blogs = $blogs->offset($offset)->limit($limit)->get();

        if (!$blogCategories->isEmpty()) {
            return response()->json(
                [
                    'data' => [
                        'blogs' => BlogsResource::collection($blogs),
                        'total_records' => $total_blogs,
                        'recentBlogs' => $resentBlog,
                        'blogCategories' => $blogCategories
                    ],
                    'status' => 1,
                    'message' => trans('api.Blog Page record.', array(), $app_language),
                ],
                200,
                ['Content-Type' => 'application/json']

            );
        } else {

            return response()->json(
                [
                    'status' => 0,
                    'data' => [],
                    'message' => trans('api.The cms page you are trying to access is inactive.', array(), $app_language)
                ],
                402,
                ['Content-Type' => 'application/json']
            );
        }
    }
    /*=========get Blog by Url function==========*/
    public function getBlogByUrl($slug, Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $blog = Blogs::where('slug', $slug)
            ->where('is_active', 1)->first();

        if ($blog) {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/blogs/' . $blog->id . '/' . $blog->image)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/blogs/' . $blog->id . '/' . $blog->image);
            } else {
                $image = asset('backend/images/no_image.jpg');
            }
            return response()->json(
                [
                    'data' => [
                        'categoryTitle' => $blog->blogCategory->title,
                        'categorySlug' => $blog->blogCategory->slug,
                        'title' => $blog->title,
                        'description' => $blog->description,
                        'image' => $image,
                        'slug' => $blog->slug

                    ],
                    'status' => 1,
                    'message' => trans('api.Blog Detail record.', array(), $app_language),
                ],
                200,
                ['Content-Type' => 'application/json']

            );
        } else {
            return response()->json(
                [
                    'status' => 0,
                    'message' => trans('api.Cms Page Not Found.', array(), $app_language)
                ],
                404,
                ['Content-Type' => 'application/json']
            );
        }
    }
    /*=========settings function==========*/
    public function settings(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $settings = SiteSettings::first();
        if ($settings->launch_time != "")
            $settings->launch_time = strtotime($settings->launch_time);
        return response()->json([
            'data' => $settings,
            'status' => 1,
            'message' => trans('api.Settings', array(), $app_language)
        ], 200, ['Content-Type' => 'application/json']);
    }



    /**
     * Send Newsletter
     *
     * @param  [string] email
     * @return \Illuminate\Http\JsonResponse
     */

    public function subscribeUs(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'email' => 'required|email|unique:newslettter_subscribers|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',

        );



        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }
        Newsletter::subscribe($input["email"]);

        if (Newsletter::lastActionSucceeded()) {
            DB::beginTransaction();
            try {
                $input["is_active"]  = 1;
                $newletter = NewsletterSubscribers::create($input);
                DB::commit();
                return response()->json(
                    [
                        'status' => 1,
                        'message' => trans('api.you are subscribe successfully .', array(), $app_language),
                    ],
                    200,
                    ['Content-Type' => 'application/json']

                );
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 403,
                    'message' =>   $e->getMessage()
                ], 200, ['Content-Type' => 'application/json']);
            }
        } else {

            return response()->json(
                [
                    'status' => 403,
                    'message' => trans('api.' . Newsletter::getLastError(), array(), $app_language)
                ],
                200,
                ['Content-Type' => 'application/json']
            );
        }
    }


    /*=========get Homepage Banner Images function==========*/
    public function getHomePageBannerImages(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $banners = Banners::where("is_active", 1)
            ->orderBy('id', 'ASC')->get();

        if (!$banners->isEmpty()) {

            return HomePageBannerResource::collection($banners)->additional(
                [
                    'message' => trans('api.Banner Images', array(), $app_language),
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
    }

    // public function getAdsProduct(Request $request)
    // {

    //     $app_language = $request['language'] <> '' ? $request['language'] : 'en';
    //     $current_date = Carbon::now()->timestamp;
    //     $active_ads_products = AdProducts::inRandomOrder();
    //     $active_ads_products = $active_ads_products->WhereHas('ad', function ($q) use ($current_date) {

    //         $q = $q->where('is_active', 1);
    //         $q = $q->where('start_date', '<=', $current_date);
    //         $q = $q->where('end_date', '>=', $current_date);
    //         $q = $q->whereColumn('total_spent', '<', 'total_budget');
    //     })->limit(config('constants.ADS_PRODUCT_LIMIT'))->get();

    //     if (!$active_ads_products->isEmpty()) {

    //         return ActiveAdsProductsResource::collection($active_ads_products)->additional(
    //             [
    //                 'message' => trans('api.Ads Products', array(), $app_language),
    //                 'status' => 1
    //             ]
    //         );
    //     } else {
    //         return ActiveAdsProductsResource::collection($active_ads_products)->additional(
    //             [
    //                 'message' => trans('api.Ads Products', array(), $app_language),
    //                 'status' => 1
    //             ]
    //         );

    //         return response()
    //             ->json([
    //                 'status' => 0,
    //                 'data' => [],
    //                 'message' => trans('api.No record found', array(), $app_language)
    //             ]);
    //     }
    // }


    /*=========contact us function==========*/
    public function contactUs(Request $request)
    {
        $app_language = $request['language'] <> '' ? $request['language'] : 'en';
        $input = $request->all();
        $validation_rules = array(
            'name'      => 'required',
            'message'       =>  'required',
            'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',

        );

        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()
                ->json(['status' => 0, 'message' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $input["is_active"]  = 1;
            ContactUs::create($input);

            $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'contact_us')->first();
            if ($template && SITE_EMAIL) {
                // [NAME]&nbsp;[PHONE]&nbsp;[EMAIL]&nbsp;[SUBJECT]&nbsp;[MESSAGE]
                $subject = $template->subject;
                $to_replace = ['[NAME]', '[EMAIL]', '[MESSAGE]', '[SITE_NAME]', '[SITE_URL]'];
                $with_replace = [$request->name, $request->email, $request->message, SITE_NAME, env("FRONT_BASE_URL")];
                $header = $template->header;
                $footer = $template->footer;
                $content = $template->content;

                $html_header = str_replace($to_replace, $with_replace, $header);
                $html_footer = str_replace($to_replace, $with_replace, $footer);
                $html_body = str_replace($to_replace, $with_replace, $content);

                $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
                Mail::queue(new MasterMail(SITE_EMAIL, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
            }

            DB::commit();

            return response()->json(
                [
                    'status' => 1,
                    'message' => 'Thankyou for contacting us, we will respond soon.',
                ],
                200,
                ['Content-Type' => 'application/json']

            );
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 403,
                'message' =>   $e->getMessage()
            ], 200, ['Content-Type' => 'application/json']);
        }
    }
}
