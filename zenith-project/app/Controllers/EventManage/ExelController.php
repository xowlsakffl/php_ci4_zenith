<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class ExelController extends BaseController
{
    public function exel()
    {
        return view('events/exelUpload/exel');
    }
}
