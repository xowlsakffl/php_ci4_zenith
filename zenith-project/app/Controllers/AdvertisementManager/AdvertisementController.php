<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;

class AdvertisementController extends BaseController
{
    public function facebook()
    {
        return view('advertisements/facebook/facebook');
    }

    public function kakao()
    {
        return view('advertisements/kakao/kakao');
    }

    public function google()
    {
        return view('advertisements/google/google');
    }

    public function etc()
    {
        return view('advertisements/etc/etc');
    }
}
