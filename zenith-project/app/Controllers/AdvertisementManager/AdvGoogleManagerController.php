<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;

class AdvGoogleManagerController extends BaseController
{
    public function index()
    {
        return view('advertisements/google/google');
    }
}
