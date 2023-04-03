<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class IntegrateController extends BaseController
{
    public function index()
    {
        return view('integrate/management');
    }
}
