<?php

namespace App\ThirdParty\googleclient;

require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;


class GoogleCalendar
{
    protected $client, $redirect_uri, $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName("Zenith_Client_Library");
        $this->client->setAuthConfig(__DIR__.'\carelabs-ams-a78a293c2f50.json');
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->fetchAccessTokenWithAssertion();    
        $this->accessToken = $this->client->getAccessToken();
        dd($this->accessToken);
    }

    public function list()
    {
        $service = new Calendar($this->client);
        
        $calendarList = $service->calendarList->listCalendarList();
        $list = $calendarList->getItems();
        dd($list);
        return $list;
    }

    public function createEvent($calendarId, $summary, $startDateTime, $endDateTime)
    {
        $service = new Calendar($this->client);

        $event = new Event([
            'summary' => $summary,
            'start' => [
                'dateTime' => $startDateTime,
                'timeZone' => 'Asia/Seoul',
            ],
            'end' => [
                'dateTime' => $endDateTime,
                'timeZone' => 'Asia/Seoul',
            ],
        ]);

        $event = $service->events->insert($calendarId, $event);
        dd($event);
        return $event;
    }
}
