<?php
namespace App\ThirdParty\botman;

require_once __DIR__.'/vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\CodeIgniterCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Exceptions\Base\BotManException;

class ChatBot {
    private $botman;
    private $credentials = [
        'app_id' => 'A057ZJSCU5A',
        'client_id' => '633137239556.5271638436180',
        'client_secret' => 'b5f4688cc4b85ad81a34bfcf6fb07348',
    ];
    
    public $token = 'xoxp-633137239556-5111601338212-5527075430960-f7596bbe7643406c33b62ccd2b09b19e';
    private $redirectUrl = 'https://local.vrzenith.com/auth/slack/callback';
    private $userId = 'U05AJSE9A5B';
    private $botId = 'B05B0E0BVNV';

    public function __construct() 
    {   
        /* DriverManager::loadDriver(\BotMan\Drivers\Slack\SlackDriver::class);
        
        $config = [
            'slack' => [
                'token' => $this->token
            ],
        ];
        $this->botman = BotManFactory::create($config); */
    }

    public function get_code()
    {
        $data = array(
            'client_id' => $this->credentials['client_id'],
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'users:read, channels:read, chat:write, groups:read, im:read, mpim:read',
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
        $response = $this->curl($url, $this->token, NULL); */

        //메세지
        $data = array(
            'text' => '테스트',
            'channel' => 'C052ZKG6UQH',
        );
        $url = 'https://slack.com/api/chat.postMessage';
        $response = $this->curl($url, $this->token, $data, 'POST');
        dd($response);
    }

    public function sendMessage($message)
    {
        $channel = 'C052ZKG6UQH';
        try {
            $response = $this->botman->say($message, $channel);

        } catch (BotManException $e) {
            dd($e);
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