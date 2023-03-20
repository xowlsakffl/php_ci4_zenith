<?php
require_once __DIR__ . '/kakao-db.php';
set_time_limit(0);
ini_set('memory_limit', '-1');

use CodeIgniter\CLI\CLI;
class ChainsawKMBF
{
    private $bizformId = '6590', $api_key = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456';
    public function __construct()
    {
        $this->db = new KMDB();
        $this->updateBizform();
    }

    private function updateBizform()
    {
        $bizforms = $this->db->getBizformUpdateList();
        $step = 1;
        $total = count($bizforms);
        CLI::write("[".date("Y-m-d H:i:s")."]"."비즈폼 수신을 시작합니다.", "light_red");
        foreach ($bizforms as $row) {
            CLI::showProgress($step++, $total); 
            if (!$row['bizFormId'] || !$row['bizFormApiKey']) continue;
            $info = $this->getBizformInfo($row['bizFormId'], $row['bizFormApiKey']);
            $count = @count($info['userResponse']['data']['content']);
            echo "{$row['bizFormId']} - {$count}건 업데이트" . PHP_EOL;
            // echo '<pre>' . print_r($info, 1) . '</pre>';
            $this->db->updateBizform($info['bizform']);
            $this->db->updateBizformUserResponse($row['id'], $row['bizFormId'], $info['userResponse']['data']['content']);
        }
    }

    private function getBizformInfo($bizformId, $api_key)
    {
        $this->bizformId = $bizformId;
        $this->api_key = $api_key;
        $bizform = $this->getBizform();
        $userResponseDist = $this->getUserResponseDist();
        $userResponse = $this->getUserResponse();
        $data = [
            'bizform' => $bizform,
            'userResponseDist' => $userResponseDist,
            'userResponse' => $userResponse
        ];

        return $data;
    }

    private function getBizform()
    { //폼 정보 조회
        $url = "https://open-api-talk-biz-form.kakao.com/api/v1/open-api/bizforms/{$this->bizformId}";
        return $this->curl($url, $this->api_key);
    }

    private function getUserResponseDist()
    { //문항별 결과 조회
        $url = "https://open-api-talk-biz-form.kakao.com/api/v1/open-api/bizforms/{$this->bizformId}/user-responses-dist";
        return $this->curl($url, $this->api_key);
    }

    private function getUserResponse()
    { //응답자별 결과
        $url = "https://open-api-talk-biz-form.kakao.com/api/v1/open-api/bizforms/{$this->bizformId}/user-responses";
        $page = 0;
        $result = $this->curl($url, $this->api_key, ['page' => $page]);
        if (isset($result['data']) && $result['data']['totalPages'] > 1) {
            for ($i = 1; $i <= $result['data']['totalPages']; $i++) {
                $response = $this->curl($url, $this->api_key, ['page' => $i]);
                $result['data']['content'] = array_merge($result['data']['content'], $response['data']['content']);
            }
        }
        if(isset($result['data']['content'])) {
            foreach ($result['data']['content'] as $idx => $row) {
                $result['data']['content'][$idx]['response'] = json_encode($row['responses']);
            }
        }
        return $result;
    }


    private function curl($url, $api_key, $data = [], $type = "GET", $getError = false)
    {
        if ($api_key != NULL)
            $headers = array("X-BIZFORM-API-KEY: {$api_key}");
        else
            $headers = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                $url .= '?' . http_build_query($data);
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
        // echo '<pre>headers:' . print_r($headers, 1) . '</pre>';
        // echo '<pre>data:' . print_r($data, 1) . '</pre>';
        // echo '<pre>info:' . print_r($info, 1) . '</pre>';
        // echo '<pre>result:' . print_r($result, 1) . '</pre>';
        curl_close($ch);

        return $result;
    }
}
