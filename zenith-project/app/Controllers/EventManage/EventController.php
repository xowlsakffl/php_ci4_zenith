<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class EventController extends BaseController
{
    public function event()
    {
        return view('events/event/event');
    }
}
