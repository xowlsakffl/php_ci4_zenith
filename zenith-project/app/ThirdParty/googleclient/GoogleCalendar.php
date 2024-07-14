<?php

namespace App\ThirdParty\googleclient;

require_once __DIR__ . '/vendor/autoload.php';

use DateTime;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendar
{
    protected $client, $redirect_uri, $accessToken;
    protected $calendarId = '';

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName("Zenith_Client_Library");
        $this->client->setAuthConfig(__DIR__.'\carelabs-ams-c10d78f8d141.json');
        $this->client->addScope(Calendar::CALENDAR);
    }

    public function calenderList()
    {
        $service = new Calendar($this->client);
        $calendarList = $service->calendarList->listCalendarList();
        $calendars = $calendarList->getItems();

        return $calendars;
    }

    public function eventList()
    {
        $service = new Calendar($this->client);
        $events = $service->events->listEvents($this->calendarId);

        return $events;
    }

    public function createEvent($data)
    {
        $service = new Calendar($this->client);

        $startDateTime = new \DateTime($data['startDate']);
        $endDateTime = new \DateTime($data['endDate']);

        $event = new Event([
            'summary' => $data['summary'],
            'start' => new EventDateTime([
                'dateTime' => $startDateTime->format('Y-m-d\TH:i:sP'),
                'timeZone' => 'Asia/Seoul',
            ]),
            'end' => new EventDateTime([
                'dateTime' => $endDateTime->format('Y-m-d\TH:i:sP'),
                'timeZone' => 'Asia/Seoul',
            ]),
        ]);

        $event = $service->events->insert($this->calendarId, $event);
        return $event;
    }
    
    public function updateEvent($data, $event_id)
    {
        $service = new Calendar($this->client);

        $event = $service->events->get($this->calendarId, $event_id);

        $event->setSummary($data['summary']);
        $startDateTime = new \DateTime($data['startDate']);
        $endDateTime = new \DateTime($data['endDate']);

        $event->setStart(new EventDateTime([
            'dateTime' => $startDateTime->format('Y-m-d\TH:i:sP'),
            'timeZone' => 'Asia/Seoul',
        ]));

        $event->setEnd(new EventDateTime([
            'dateTime' => $endDateTime->format('Y-m-d\TH:i:sP'),
            'timeZone' => 'Asia/Seoul',
        ]));

        $updatedEvent = $service->events->update($this->calendarId, $event->getId(), $event);

        return $updatedEvent->getUpdated();
    }

    public function deleteEvent($event_id)
    {
        $service = new Calendar($this->client);

        $service->events->delete($this->calendarId, $event_id);

        return true;
    }
}
