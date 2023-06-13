<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class AdvertiserController extends BaseController
{
    public function index()
    {
        return view('/events/advertiser/advertiser');
    }
}
