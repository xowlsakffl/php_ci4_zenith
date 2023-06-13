<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class EventController extends BaseController
{
    public function index()
    {
        return view('events/event/event');
    }
}
