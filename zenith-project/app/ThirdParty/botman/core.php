<?php
require_once __DIR__.'/vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Slack\SlackRTMDriver;
use BotMan\BotMan\Cache\CodeIgniterCache;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class ChatBot {
    private $botman;
    private $credentials = [
        'app_id' => 'A057ZJSCU5A',
        'client_id' => '633137239556.5271638436180',
        'client_secret' => 'b5f4688cc4b85ad81a34bfcf6fb07348',
    ];
    function __construct() {
        $config = [
            'slack' => [
                'token' => 'xoxb-633137239556-5358898316181-35WkFFREOK5IRnIznq4dib4H'
            ]
        ];
        $this->load->driver('cache');
        $this->botman = BotManFactory::create($config, new CodeIgniterCache($this->cache->file));
    }
    public function get_code()
    {
        $redirect_code_uri = urlencode("https://local.vrzenith.com/advertisement/moment/oauth");
        $param = "?client_id={$this->app_key}&redirect_uri={$redirect_code_uri}&response_type=code";
        $response = $this->curl($this->oauth_url . '/authorize' . $param, NULL, NULL);
    }
    public function oauth()
    {
        if ($_GET['code']) {
            $data = array(
                'client_id' => $this->app_key,
                'redirect_uri' => 'https://local.vrzenith.com/advertisement/moment/oauth',
                'code' => $_GET['code'], //get_code() 에서 받은 code 값
                //'client_secret' => $this->client_secret
            );
            $data = http_build_query($data);
            $response = $this->curl($this->oauth_url . '/token', NULL, $data, 'POST');
        }
    }

    public function sendMessage() {
        $this->botman->hears('image attachment', function (BotMan $bot) {
            // Create attachment
            $attachment = new Image('https://botman.io/img/logo.png');
        
            // Build message object
            $message = OutgoingMessage::create('This is my text')
                        ->withAttachment($attachment);
        
            // Reply message object
            $bot->reply($message);
        });
    }

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