<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class ChangeController extends BaseController
{
    public function index()
    {
        return view('/events/change/change');
    }
}
