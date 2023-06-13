<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class ExelController extends BaseController
{
    public function index()
    {
        return view('events/exelUpload/exel');
    }
}
