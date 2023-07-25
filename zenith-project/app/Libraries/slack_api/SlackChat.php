<?php
namespace App\Libraries\slack_api;
use App\Controllers\BaseController;
use App\Libraries\slack_api\SlackChatModel;
class SlackChat extends BaseController
{

    private $client, $db;
    public $config;

    public function __construct() {
        $this->config = parse_ini_file(APPPATH.'Libraries/slack_api/config.ini', true);
        $this->client = \Config\Services::curlrequest();
        $this->db = new SlackChatModel();
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

    public function getSubscriptions() {
        header('Content-type: application/json');
        $_POST = json_decode(file_get_contents('php://input'), true);
        // $this->writeLog(file_get_contents('php://input'));
        $data = [];
        if(isset($_POST['challenge']))
            $data['challenge'] = $_POST['challenge'];
        if(isset($_POST['event']) && isset($_POST['event']['text'])) {
            $key = "";
            $keywords = $this->db->get_keyword();
            foreach($keywords as $row) {
                if(strpos($_POST['event']['text'], $row['keyword']) === false) continue;
                if($row['fr_channel'] == $_POST['event']['channel']) {
                    $message = [
                        'channel' => $row['to_channel'],
                        'blocks' => [
                            [
                                "type" => "section",
                                "text" => [
                                    "type" => "mrkdwn",
                                    "text" => "<#{$_POST['event']['channel']}>에서 발송한 메시지 입니다."
                                ]
                            ],
                            [
                                "type" => "section",
                                "text" => [
                                    "type" => "mrkdwn",
                                    "text" => "<@{$_POST['event']['user']}>\n{$_POST['event']['text']}"
                                ]
                            ]
                        ],
                    ];
                    $this->writeLog(json_encode($message));
                    $result = $this->sendMessage($message);
                }
            }
        }
    }

    public function getUserInfo($user) {
        $response = $this->client->request('POST', 'https://slack.com/api/users.info', [
            'headers' => ["Authorization" => "Bearer {$this->config['token']}"],
            'user' => $user
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
            $body = json_decode($body,true);
        }
        dd($body);
    }

    public function command() {
        // $post = '{"token":"DJBBcWCpMQwDUc4NUvJs5uO6","team_id":"TJM4171GC","team_domain":"carelabs-kr","channel_id":"C05G82NMG8G","channel_name":"dm_botman_test","user_id":"U0539HPGY4U","user_name":"jaybe","command":"\/키워드_추가","text":"C05JESYGJH2 안녕","api_app_id":"A057ZJSCU5A","is_enterprise_install":"false","response_url":"https:\/\/hooks.slack.com\/commands\/TJM4171GC\/5648179184736\/xr6iQnJZ103MKA27PeqBRu54","trigger_id":"5624363084931.633137239556.a8dbb761125f72afec791025da822f0d"}';
        // $_POST = json_decode($post,1);
        // $fp = fopen(WRITEPATH.'/logs/slack_log', 'a+');
        // $fw = fwrite($fp, json_encode($_POST,JSON_UNESCAPED_UNICODE).PHP_EOL);
        // fclose($fp);
        if(!isset($_POST['command']) && !$_POST['command']) return null;
        if(!isset($_POST['text']) && !$_POST['text']) return null;
        switch($_POST['command']) {
            case '/키워드' : $result = $this->add_keyword($_POST); break;
        }
        echo $result;
    }

    private function add_keyword($data) {
        preg_match_all("/^([a-z0-9]+)\s+(.+)$/i", $data['text'], $matches);
        $ch_id = $matches[1][0];
        $keyword = $matches[2][0];
        if(!$ch_id || !$keyword) return '"/키워드_추가 [알림전송할 채널ID] [키워드]" 자세한 등록 방법은 개발팀에 문의해주세요.';
        $result = [
            'fr_channel' => $data['channel_id'],
            'to_channel' => $ch_id,
            'keyword' => $keyword,
        ];
        $result = $this->db->add_keyword($result);
        if($result) return "등록되었습니다.";
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
            $body = json_decode($body,true);
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
            $body = json_decode($body,true);
        }
        if($body['ok'] == false) 
            $this->writeLog($body);
        return $body;
    }

    private function writeLog($log) {
        $fp = fopen(WRITEPATH.'/logs/slack_log', 'a+');
        $fw = fwrite($fp, print_r($log,true).PHP_EOL);
        fclose($fp);
    }
}
