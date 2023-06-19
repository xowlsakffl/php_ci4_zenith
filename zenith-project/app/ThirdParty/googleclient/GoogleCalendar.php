<?php

namespace App\ThirdParty\googleclient;

require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

class GoogleCalendar
{
    protected $client, $redirect_uri, $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName("Zenith_Client_Library");
        $this->client->setAuthConfig(__DIR__.'/credentials.json');
        $this->client->addScope(Drive::DRIVE);
        $this->redirect_uri = 'https://local.vrzenith.com/calendar/oauth';
        $this->client->setRedirectUri($this->redirect_uri);

        if (!isset($_GET['code'])) {
            $authUrl = $this->client->createAuthUrl();
            header('Location: ' . $authUrl);
            exit;
        } else {
            $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->accessToken = $this->client->getAccessToken();
        }
    }
}
