<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CmsPages;
use Illuminate\Http\Request;
use DB;
use App\Models\Templates;
use Mail;
use View;
use App\Mail\MasterMail;
use App\Models\ContactUs;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * index
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function index(Request $request)
	{

		$data = [];
		echo 'HOME PAGE';
		// return view('frontend.home.view', $data);
	}
	/**
	 * Subscribe us
	 *
	 * @return void
	 */
	public function subscribe_us(Request $request)
	{
		$input = $request->all();
		$json = array('response' => 0);
		if (!empty($input) && filter_var($input['subscribe_email'], FILTER_VALIDATE_EMAIL)) {
			//check if already exists
			$Check = DB::table('newslettter_subscribers')->where('email', $input['subscribe_email'])->first();
			if ($Check) {
				//update status
				$update['status'] = 1;
				$update['name'] = isset($input['name']) ? $input['name'] : '';
				$update['updated_at'] = date('Y-m-d H:i:s');
				DB::table('newslettter_subscribers')->where('id', $Check->id)->update($update);
				$json = array('response' => 1);
			} else {
				//insert email
				$insert['name'] = isset($input['name']) ? $input['name'] : '';
				$insert['email'] = $input['subscribe_email'];
				$insert['status'] = 1;
				$insert['created_at'] = date('Y-m-d H:i:s');
				$insert['updated_at'] = date('Y-m-d H:i:s');
				DB::table('newslettter_subscribers')->insert($insert);
				$json = array('response' => 1);
			}
		}
		return $json;
	}

	/**
	 * cms_pages
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function cms_pages(Request $request)
	{
		$data = array();
		$slug = $request->segment(1);
		if ($slug <> "") {
			$data['cmsPage'] = $cmsPage = CmsPages::select('*')
				->where('seo_url', $slug)
				->where('is_active', 1)
				->first();

			if ($cmsPage) {
				$data['meta_title'] = $cmsPage->meta_title;
				$data['meta_keywords'] = $cmsPage->meta_keywords;
				$data['meta_descrition'] = $cmsPage->meta_description;
			} else {
				$data['meta_title'] = '';
				$data['meta_keywords'] = '';
				$data['meta_descrition'] = '';
			}
		}

		return view("frontend.layouts.pages")->with($data);
	}

	public function contact_us(Request $request)
	{

		$data = [];

		$data['cmsPage'] = $cmsPage = CmsPages::select('*')
			->where('seo_url', 'contact-us')
			->where('is_active', 1)
			->first();

		if ($cmsPage) {
			$data['meta_title'] = $cmsPage->meta_title;
			$data['meta_keywords'] = $cmsPage->meta_keywords;
			$data['meta_descrition'] = $cmsPage->meta_description;
		}

		if ($request->all()) {

			$validation = $request->validate([
				'fullname' => ['required', 'max:20'],
				'email' => ['required', 'email', 'max:50'],
				'subject' => ['required', 'string',  'max:100'],
				'message' => ['required', 'string',  'max:500']
			]);

			DB::beginTransaction();
			try {
				$input = $request->all();

				ContactUs::create($input);

				$template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'contact_us')->first();
				if ($template != '') {

					$subject = $template->subject;
					$to_replace = ['[NAME]', '[PHONE]', '[EMAIL]', '[SUBJECT]', '[MESSAGE]'];
					$with_replace = [$input['fullname'], $input['phone'], $input['email'], $input['subject'], nl2br(removeUrls(removeHtml($input['message'])))];
					$header = $template->header;
					$footer = $template->footer;
					$content = $template->content;
					$html_header = str_replace($to_replace, $with_replace, $header);
					$html_footer = str_replace($to_replace, $with_replace, $footer);
					$html_body = str_replace($to_replace, $with_replace, $content);

					$mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();
					Mail::queue(new MasterMail(INQUIRY_EMAIL, SITE_NAME, NO_REPLY_EMAIL, $subject, $mailContents));
				}

				DB::commit();

				$request->session()->flash('success_message', 'Your contact us inquiry has been successfully submitted.');

				return redirect()->back();
			} catch (\Exception $e) {
				DB::rollback();
				$request->session()->flash('error_message', $e->getMessage());

				return redirect()->back()->withErrors($validation)->withInput();
			}
		}

		return view("frontend.home.contact_us")->with($data);
	}
}
