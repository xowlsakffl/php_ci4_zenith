<?php

namespace App\Controllers\Calendar;

use App\Controllers\BaseController;
use App\ThirdParty\googleclient\GoogleCalendar\GoogleCalendar;

class CalendarController extends BaseController
{
    protected $googleCalender;

    public function __construct()
    {
        $this->googleCalender = new GoogleCalendar();
    }

    public function index()
    {
        return view('calendar/calendar');
    }
}
