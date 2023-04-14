<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;

class AdvKakaoManagerController extends BaseController
{
    public function index()
    {
        return view('advertisements/kakao/kakao');
    }
}
