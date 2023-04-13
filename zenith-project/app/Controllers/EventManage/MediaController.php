<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class MediaController extends BaseController
{
    public function media()
    {
        return view('/events/media/media');
    }
}
