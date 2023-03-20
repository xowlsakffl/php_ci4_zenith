<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdvertisementController extends BaseController
{
    public function facebook()
    {
        return view('advertisements/facebook');
    }
}
