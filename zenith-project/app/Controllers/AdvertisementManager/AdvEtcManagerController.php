<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;

class AdvEtcManagerController extends BaseController
{
    public function index()
    {
        return view('advertisements/etc/etc');
    }
}
