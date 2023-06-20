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

    public function test()
    {
        //$list = $this->googleCalender->eventList();

        /* $data['startDate'] = '2023-06-24';
        $data['endDate'] = '2023-06-30';
        $data['summary'] = 'test6';
        $this->googleCalender->createEvent($data); */

        /* $data['startDate'] = '2023-06-20';
        $data['endDate'] = '2023-06-30';
        $data['summary'] = '테스트2';
        $event_id = '0iaudoh97jkqnp8vg1mbg5d4fg';
        $this->googleCalender->updateEvent($data, $event_id); */

        /* $event_id = '3pp2lt2r5iuc9k4r13ampehhvt';
        $this->googleCalender->deleteEvent($event_id); */
    }
}
