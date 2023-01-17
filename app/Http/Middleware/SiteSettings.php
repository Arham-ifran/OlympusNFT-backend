<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Route;

class SiteSettings
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $site_settings = DB::table('site_settings')->first();
        if ($site_settings) {
            foreach ($site_settings as $key => $value) {
                if ($key == 'id') {
                    continue;
                } else {
                    if (!defined(strtoupper($key))) {
                        define(strtoupper($key), $value);
                    }
                }
            }
        }
        if (!defined(strtoupper('NO_REPLY_EMAIL'))) {
            define(strtoupper('NO_REPLY_EMAIL'), 'no-reply@nesuxart.com');
        }

        return $next($request);
    }
}
