<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Products;
use App\Models\Bids;
use App\Models\Templates;
use View;
use Mail;
use Carbon\Carbon;
use App\Mail\MasterMail;
use Hashids;
class SetAuctionWinnerBid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $productId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sitesetting = sitesSetting('site_name,site_email');
        $product = Products::find($this->productId);
        if ($product) {
            $bid = Bids::where('product_id', $product->id)->first();
            if ($bid) {
               
                if ($product->auction_length_id && $product->auction_time != "") {
                    if (\Carbon\Carbon::now()->timestamp >= $product->auction_time) {
                        if ($product->is_sold == 0) {
                            if ($product->highest_bid && $product->highest_bid->is_winner_bid != 1) {
                                $product->highest_bid->update(['is_winner_bid' => 1]);
                                /****send Email*****/

                                $template = Templates::where('template_type', 1)->where('is_active', 1)->where('email_type', 'on_wining_bid')->first();
                                if ($template != '') {

                                    $subject = $template->subject;
                                    
                                    $link = url('product-detail/'.$product->slug.'/'. Hashids::encode($product->id));
                                    $to_replace = ['[BIDDER]', '[PRODUCTNAME]', '[PRICE]', '[LINK]','[SITE_NAME]','[SITE_URL]'];

                                    $with_replace = [$product->highest_bid->bidder->username, $product->title, $product->highest_bid->price, $link,$sitesetting->site_name,$font_base_url];
                                    $header = $template->header;
                                    $footer = $template->footer;
                                    $content = $template->content;
                                    $html_header = str_replace($to_replace, $with_replace, $header);
                                    $html_footer = str_replace($to_replace, $with_replace, $footer);
                                    $html_body = str_replace($to_replace, $with_replace, $content);

                                    $mailContents = View::make('email_templete.message', ["data" => $html_body, "header" => $html_header, "footer" => $html_footer])->render();

                                    Mail::queue(new MasterMail($product->user->email, $sitesetting->site_name, $sitesetting->site_email, $subject, $mailContents));
                                }
                                /****end****/
                            }
                        }
                    }
                }
            }
        }
    }
}
