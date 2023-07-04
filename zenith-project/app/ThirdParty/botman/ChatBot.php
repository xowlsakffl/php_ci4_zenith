<?php
namespace App\ThirdParty\botman;

require_once __DIR__.'/vendor/autoload.php';

use App\Controllers\BaseController;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Exceptions\Base\BotManException;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\Drivers\Slack\SlackDriver;


class ChatBot extends BaseController{
    private $botman;
    private $credentials = [
        'app_id' => 'A057ZJSCU5A',
        'client_id' => '633137239556.5271638436180',
        'client_secret' => 'b5f4688cc4b85ad81a34bfcf6fb07348',
    ];
    
    private $token = 'xoxb-633137239556-5358898316181-xwEUhVz6wE99kINHlrvZY9Qs';
    private $redirectUrl = 'https://local.vrzenith.com/auth/slack/callback';

    public function __construct() 
    {   
        DriverManager::loadDriver(SlackDriver::class);
        $config = [
            'driver' => SlackDriver::class,
            'slack' => [
                'token' => $this->token,
            ],
        ];
        
        $this->botman = BotManFactory::create($config);
        log_message('error', 'Botman : '.print_r($this->botman->getDriver(), true));
    }
    
    public function get_code()
    {
        $data = array(
            'client_id' => $this->credentials['client_id'],
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'incoming-webhook,chat:write',
            'user_scope' => 'channels:read,chat:write,users:read'
        );
        $data = http_build_query($data);
        $redirectUrl = 'https://slack.com/oauth/v2/authorize'.'?'.$data;
        return $redirectUrl;
    }

    public function oauth($data)
    {
        if ($data['code']) {
            $data = array(
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'redirect_uri' => $this->redirectUrl,
                'code' => $data['code'],
            );
            
            $url = 'https://slack.com/api/oauth.v2.access';
            $response = $this->curl($url, NULL, $data, 'POST');
            return $response;
        }
    }

    public function test()
    {
        //유저 테스트
        /* $url = 'https://slack.com/api/auth.test';
        $response = $this->curl($url, $this->token, NULL);
        dd($response); */
        //채널 리스트
        /* $url = 'https://slack.com/api/conversations.list';
        $response = $this->curl($url, $this->token, NULL);
        dd($response); */

        //메세지
        /* $data = array(
            'text' => '테스트1',
            'channel' => 'C05ELGJ1JA2',
        );
        $url = 'https://slack.com/api/chat.postMessage';
        $response = $this->curl($url, $this->token, $data, 'POST');
        dd($response); */

        //봇 테스트
        /* $url = 'https://slack.com/api/bots.info';
        $response = $this->curl($url, $this->token, NULL);
        dd($response); */

        /* $this->botman->on('event', function($payload, $bot) {
            log_message('error', 'Slack : '.print_r($this->botman));
        });

        $this->botman->hears('hello', function (BotMan $bot) {
            log_message('error', 'Slack : '.print_r($this->botman));
            $bot->reply('Hello! How can I help you?');
        })->driver(SlackDriver::class); */

        //메세지 보내기 봇맨
        $this->sendMessage('안녕');

        //채널리스트 봇맨
        //$this->getChannelList();

        //웹훅 테스트
        //$this->curlSample();

        //$this->responseTest();
    }

    public function sendMessage($message)
    {
        $channel = 'C05ELGJ1JA2';

        $attachment = new Image('https://carezenith.co.kr/img/logo.png', [
            'custom_payload' => true,
        ]);
        $message = OutgoingMessage::create($message)->withAttachment($attachment);
        $response = $this->botman->say($message, $channel, SlackDriver::class);
    }

    public function getChannelList()
    {
        $url = 'https://slack.com/api/conversations.list';
        $response = $this->curl($url, $this->token, NULL);
        dd($response);
    }

    public function curlSample()
    {
        $url = 'https://hooks.slack.com/services/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';
        $data = [
            'text' => '테스트',
        ];
        $response = $this->curl($url, NULL, json_encode($data), 'POST');
        dd($response);
    }

    public function responseTest()
    {
        $payload = json_decode($this->request->getBody(), true);
        log_message('error', 'Slack Payload: '.print_r($payload));

        $type = $payload['type'];

        if ($type === 'url_verification') {
            $challenge = $payload['challenge'];
            return $this->response->setBody($challenge)->setContentType('text/plain');
        }
    }

    /* public function sendMessage() {
        $this->botman->hears('image attachment', function (BotMan $bot) {
            // Create attachment
            $attachment = new Image('https://botman.io/img/logo.png');
        
            // Build message object
            $message = OutgoingMessage::create('This is my text')
                        ->withAttachment($attachment);
        
            // Reply message object
            $bot->reply($message);
        });
    } */

    protected function curl($url, $api_key, $data, $type = "GET", $multipart = false, $getError = false)
    {
        if ($api_key != NULL)
            $headers = array("Authorization: Bearer {$api_key}");
        else
            $headers = array();

        if ($multipart == true && is_array($data)) {
            $headers[] = 'Content-type: multipart/form-data';
            $data['image'] = new CURLFile($data['image']['tmp_name'], $data['image']['type'], $data['image']['name']);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                break;
            case 'PUT':
                $headers[] = 'Content-type: application/json';
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = json_decode($result, true);
        
        // echo json_encode($data);
        // echo '<pre>headers:'.print_r($headers,1).'</pre>';
        // echo '<pre>data:'.print_r($data,1).'</pre>';
        // echo '<pre>info:'.print_r($info,1).'</pre>';
        // echo '<pre>result:'.print_r($result,1).'</pre>';
        curl_close($ch);

        return $result;
    }
}
?>