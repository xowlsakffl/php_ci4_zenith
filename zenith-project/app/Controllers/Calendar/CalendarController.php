<?php

namespace App\Controllers\Calendar;

use App\Controllers\BaseController;
use App\ThirdParty\googleclient\GoogleCalendar;

class CalendarController extends BaseController
{
    protected $googleCalender;

    public function __construct()
    {
        $this->googleCalender = new GoogleCalendar();
    }

    public function index()
    {
        $list = $this->googleCalender->list();

        return view('calendar/calendar', $list);
    }
}
