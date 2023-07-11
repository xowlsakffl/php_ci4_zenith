<?php
namespace App\Libraries\slack_api;

class SlackChat
{

    private $config, $client;

    public function __construct() {
        $this->config = parse_ini_file(APPPATH.'Libraries\slack_api\config.ini', true);
        $this->client = \Config\Services::curlrequest();
    }

    public function channelList()
    {
        $response = $this->client->request('GET', 'https://slack.com/api/conversations.list', [
            'headers' => [
                "Authorization" => "Bearer {$this->config['token']}"
            ]
        ]);
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
        
        return $body;
    }

    public function sendMessage($data)
    {
        $response = $this->client->request('POST', 'https://slack.com/api/chat.postMessage', [
            'headers' => [
                "Authorization" => "Bearer {$this->config['token']}"
            ],
            'json' => [
                'channel' => $data['channel'],
                'text' => $data['text'],
            ]
        ]);
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
        
        return $body;
    }
}
