<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class AdvertiserController extends BaseController
{
    public function advertiser()
    {
        return view('/events/advertiser/advertiser');
    }
}
