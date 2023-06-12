<?php

namespace App\ThirdParty\googleclient\GoogleCalendar;

require_once __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

class GoogleCalendar
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName("Zenith_Client_Library");
        $this->client->setAuthConfig('/credentials.json');
        $this->client->addScope(Drive::DRIVE);
    }
}
