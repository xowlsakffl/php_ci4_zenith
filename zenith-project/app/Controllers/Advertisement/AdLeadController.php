<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\Models\Api\AdLeadModel;
use CodeIgniter\CLI\CLI;

class AdLeadController extends BaseController
{
    protected $adlead;

    public function __construct()
    {
        $this->adlead = model(AdLeadModel::class); 
    }

    public function sendToEventLead()
    {
        CLI::clearScreen();
        CLI::write("잠재고객 업데이트를 시작합니다.", "yellow");
        $this->sendToEventLeadFromKakao();
        $this->sendToEventLeadFromFacebook();
        //$this->sendToEventLeadFromGoogle();
        CLI::write("잠재고객 업데이트가 완료되었습니다.", "yellow");
    }

    private function sendToEventLeadFromKakao()
    {
        $moment_ads = $this->adlead->getBizFormUserResponse();
        $step = 1;
        $total = count($moment_ads);
        if(!$total){
            return null;
        }
        CLI::write("카카오 모먼트 잠재고객 업데이트를 시작합니다.", "yellow");
        foreach($moment_ads as $row){  
            CLI::showProgress($step++, $total);
            $landing = $this->landingGroupKakao($row);
            if(is_null($landing)) {
                CLI::print('비즈폼 매칭 오류 발생 : ' . $row . '');
                continue;
            }

            //전화번호
            $phone = str_replace("+82010", "010", $row['phoneNumber']);
            $phone = str_replace("+8210", "010", $phone);
            $phone = preg_replace("/^8210(.+)$/", "010$1", $phone);
            $phone = str_replace("+82 10", "010", $phone);
            $phone = str_replace("-", "", $phone);
            if ($row['email'] == '없음') $row['email'] = '';

            //추가질문
            $questions = [];
            $add = [];
            $responses = json_decode($row['responses'], 1);
            $acnt = 1;

            $add1 = '';
            $add2 = '';
            $add3 = '';
            $add4 = '';
            $add5 = '';
            foreach ($responses as $response) {
                $qs = $this->adlead->getBizformQuestion($row['bizFormId'], $response['bizformItemId']);
                if (!key_exists($qs['id'], $questions))
                    $questions[$qs['id']] = $qs['title'];
                $add[] = ${'add' . $acnt} = $questions[$response['bizformItemId']] . '::' . $response['response'];
                $acnt++;
            }
            $result = [];
            if ($landing['event_id']) {
                $result['event_seq'] = $landing['event_id'];
                $result['site'] = $landing['site'];
                $result['name'] = addslashes($row['nickname']);
                $result['phone'] = $phone;
                $result['add1'] = addslashes($add1);
                $result['add2'] = addslashes($add2);
                $result['add3'] = addslashes($add3);
                $result['add4'] = addslashes($add4);
                $result['add5'] = addslashes($add5);
                $result['reg_date'] = $row['create_time'];
                $result['id'] = $row['seq'];   
                $result['encUserId'] = $row['encUserId'];
                $result['bizFormId'] = $row['bizFormId'];         
            }
            
            if (is_array($result)) {
                $this->adlead->insertEventLeadKakao($result);
            }
        }
    }

    private function sendToEventLeadFromFacebook()
    {
        $facebook_ads = $this->adlead->getFBAdLead();
        $step = 1;
        $total = count($facebook_ads);
        if(!$total){
            return null;
        }
        CLI::write("페이스북 잠재고객 업데이트를 시작합니다.", "yellow");
        foreach($facebook_ads as $row){  
            CLI::showProgress($step++, $total);
            $landing = $this->landingGroupFacebook($row['ad_name']);
            //이름
            $full_name = $row['full_name'];
            if (!$full_name || $full_name == null) {
                $full_name = trim($row['first_name'] . ' ' . $row['last_name']);
            }

            //성별
            if ($row['gender'] == "female") {
                $gender = "여자";
            } elseif ($row['gender'] == "male") {
                $gender = "남자";
            } else {
                $gender = $row['gender'];
            }

            //나이
            if ($row['date_of_birth']) {
                $birthyear = date("Y", strtotime($row['date_of_birth']));
                $nowyear = date("Y");
                $age = $nowyear - $birthyear + 1;
            } else {
                $age = "";
            }

            //전화번호
            $phone = str_replace("+82010", "010", $row['phone_number']);
            $phone = str_replace("+8210", "010", $phone);
            $phone = str_replace("-", "", $phone);


            //주소
            if ($row['street_address']) {
                $addr = $row['street_address'];
            } else {
                $addr = "";
            }

            //추가질문
            preg_match_all("/0 => \'(.*)\',/iU", $row['field_data'], $match);
            if (isset($match[1][0])) {
                $add1 = $match[1][0];
            } else {
                $add1 = "";
            }
            if (isset($match[1][1])) {
                $add2 = $match[1][1];
            } else {
                $add2 = "";
            }
            if (isset($match[1][2])) {
                $add3 = $match[1][2];
            } else {
                $add3 = "";
            }
            if (isset($match[1][3])) {
                $add4 = $match[1][3];
            } else {
                $add4 = "";
            }
            if (isset($match[1][4])) {
                $add5 = $match[1][4];
            } else {
                $add5 = "";
            }

            if ($landing['event_id']) {
                $result['event_seq'] = $landing['event_id'];
                $result['site'] = $landing['site'];
                $result['name'] = addslashes($full_name);
                $result['gender'] = $gender;
                $result['age'] = $age;
                $result['phone'] = $phone;
                $result['add1'] = addslashes($add1);
                $result['add2'] = addslashes($add2);
                $result['add3'] = addslashes($add3);
                $result['add4'] = addslashes($add4);
                $result['add5'] = addslashes($add5);
                $result['addr'] = $addr;
                $result['reg_date'] = $row['created_time'];
                $result['id'] = $row['id'];            
            }

            if (is_array($result)) {
                $this->adlead->insertEventLeadFacebook($result);
            }
        }
    }

    private function sendToEventLeadFromGoogle()
    {
    }

    private function landingGroupFacebook($title)
    {
        if (!$title) {
            return null;
        }
        preg_match_all('/.+\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        $db_prefix = '';

        $result = array(
            'name' => '', 'event_id' => '', 'site' => '', 'db_price' => 0, 'period_ad' => ''
        );

        $result['name']         = $matches[0][0];
        $result['event_id']     = $matches[1][0];
        $result['site']         = $matches[3][0];
        $result['db_price']     = $matches[6][0];
        $result['period_ad']    = $matches[12][0];
        return $result;
    }

    private function landingGroupKakao($data)
    {
        if (!$data['name']) {
            return null;
        }
        preg_match_all('/(.+)?\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $data['name'], $matches);
        // $data['landingUrl'] = 'http://hotevent.hotblood.co.kr/event_spin/event_62.php';

        if(isset($data['landingUrl'])){
            if (preg_match("/hotblood\.co\.kr/i", $data['landingUrl'])) {
                $urls = parse_url($data['landingUrl']);
                parse_str($urls['query'], $urls['qs']);
                $event_id = @array_pop(explode('/', $urls['path']));
                $site = @$urls['qs']['site'];

                if($urls['qs']['site'] != $matches[4][0]) //제목 site값 우선
                $site = $matches[4][0];
            }
        } else {
            $event_id = $matches[2][0];
            $site = $matches[4][0];
        }
        
        $result = array(
            'name' => '', 'event_id' => '', 'site' => '', 'db_price' => 0, 'period_ad' => ''
        );
        $result['name']         = $matches[0][0];
        $result['event_id']     = $event_id;
        $result['site']         = $site;
        $result['db_price']     = $matches[7][0];
        $result['period_ad']    = $matches[13][0];
        $result['url']          = @$data['landingUrl'];
        return $result;
    }

    private function landingGroupGoogle($data)
    {
    }
}
