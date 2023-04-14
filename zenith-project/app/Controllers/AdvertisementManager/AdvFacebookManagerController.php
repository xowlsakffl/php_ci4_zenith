<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;

class AdvFacebookManagerController extends BaseController
{
    public function index()
    {
        return view('advertisements/facebook/facebook');
    }
}
