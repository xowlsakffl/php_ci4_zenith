<?php
namespace App\Libraries\slack_api;
use App\Controllers\BaseController;
class SlackChat extends BaseController
{

    private $client;
    public $config;

    public function __construct() {
        $this->config = parse_ini_file(APPPATH.'Libraries/slack_api/config.ini', true);
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

    public function channelMembers($channel)
    {
        $response = $this->client->request('POST', 'https://slack.com/api/conversations.members', [
            'headers' => [
                "Authorization" => "Bearer {$this->config['token']}",
            ],
            'body' => "limit=1000&channel=".$this->getChannelId($channel)
        ]);
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
        if(isset($body->members)) $body = $body->members;
        $result = $this->sendMessage(['channel'=>'U0539HPGY4U','text'=>'DM테스트']);
        dd($result);
        return $body;
    }

    public function channelMsgs($channel)
    {
        $response = $this->client->request('POST', 'https://slack.com/api/conversations.history', [
            'headers' => [
                "Authorization" => "Bearer {$this->config['token']}",
            ],
            'body' => "limit=1000&channel=".$this->getChannelId($channel)
        ]);
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
        dd($body);
        return $body;
    }

    public function getSubscriptions($request) {
        log_message('info', $request);
    }

    private function getChannelId($name) {
        if(preg_match('/^[0-9a-z]+$/i', $name)) return $name;
        return $this->config['ChannelID'][$name];
    }

    public function profileSet($data) {
        $response = $this->client->request('POST', 'https://slack.com/api/users.profile:write', [
            'headers' => ["Authorization" => "Bearer {$this->config['token']}"],
            'json' => $params
                /*
                [
                    'channel' => $this->getChannelId($data['channel']),
                    'text' => $data['text'],
                    'blocks' => $data['blocks']
                ]
                */
            
        ]);
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
    }

    public function sendMessage($data)
    {
        $params = [];
        if(isset($data['channel']))
            $params['channel'] = $this->getChannelId($data['channel']);
        if(isset($data['text']))
            $params['text'] = $data['text'];
        if(isset($data['blocks']))
            $params['blocks'] = $data['blocks'];
        /*
        [
            'channel' => $this->getChannelId($data['channel']),
            'text' => $data['text'],
            'blocks' => $data['blocks']
        ]
        */
        $response = $this->client->request('POST', 'https://slack.com/api/chat.postMessage', [
            'headers' => ["Authorization" => "Bearer {$this->config['token']}"],
            'json' => $params
        ]);
        
        $body = $response->getBody();
        if (strpos($response->header('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }
        
        return $body;
    }
}
