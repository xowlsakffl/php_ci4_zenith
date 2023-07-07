<?php
namespace App\ThirdParty\slack_api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\CLI\CLI;

class SlackChat extends BaseController
{
    use ResponseTrait;

    private $token = 'xoxb-633137239556-5358898316181-xwEUhVz6wE99kINHlrvZY9Qs';

    public function channelList()
    {
        if(strtolower($this->request->getMethod()) === 'get'){
            $url = 'https://slack.com/api/conversations.list';
            $response = $this->curl($url, $this->token, NULL);
            return $this->respond($response); 
        }else{
            return $this->fail('잘못된 요청'); 
        }
    }

    public function sendMessage()
    {
        if(strtolower($this->request->getMethod()) === 'post'){
            $data = $this->request->getPost();
            $url = 'https://slack.com/api/chat.postMessage';
            $data = [
                'channel' => $data['channel'],
                'text' => $data['text'],
                /* 'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'new request'
                        ]
                    ]
                ] */
            ];
            $response = $this->curl($url, $this->token, json_encode($data), 'POST');
            return $this->respond($response);
        }else{
            return $this->fail('잘못된 요청'); 
        }
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
