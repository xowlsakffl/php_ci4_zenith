<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class GuestController extends BaseController
{
    public function index()
    {
        return view('guest/guest');
    }
}
