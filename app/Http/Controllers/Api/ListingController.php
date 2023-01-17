<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ListingController extends Controller
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
}
