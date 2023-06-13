<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class MediaController extends BaseController
{
    public function index()
    {
        return view('/events/media/media');
    }
}
