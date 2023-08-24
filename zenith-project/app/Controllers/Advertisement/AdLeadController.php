<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\Models\Api\AdLeadModel;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\moment_api\ZenithKM;
use CodeIgniter\CLI\CLI;

class AdLeadController extends BaseController
{
    protected $adlead, $facebook, $google, $kakao;

    public function __construct()
    {
        $this->adlead = model(AdLeadModel::class); 
        $this->facebook = new ZenithFB();
        $this->google = new ZenithGG();
        $this->kakao = new ZenithKM();
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
        $total = $moment_ads->getNumRows();
        if(!$total){
            return null;
        }
        CLI::write("카카오 모먼트 잠재고객 업데이트를 시작합니다.", "yellow");
        foreach($moment_ads->getResultArray() as $row){  
            CLI::showProgress($step++, $total);
            $landing = $this->kakao->landingGroup($row);
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

            $addr = $add1 = $add2 = $add3 = $add4 = $add5 = null;
            foreach ($responses as $response) {
                $qs = $this->adlead->getBizformQuestion($row['bizFormId'], $response['bizformItemId']);
                if(is_null($qs)) continue;
                if (!key_exists($qs['id'], $questions))
                    $questions[$qs['id']] = $qs['title'];
                $add[] = ${'add' . $acnt} = $questions[$response['bizformItemId']] . '::' . $response['response'];
                $acnt++;
            }

            $result = [];
            if ($landing['event_seq']) {
                $result['event_seq'] = $landing['event_seq'];
                $result['site'] = $landing['site']??null;
                $result['name'] = $row['nickname'];
                $result['email'] = $row['email']??'';
                $result['gender'] = $row['gender']??null;
                $result['age'] = $row['age']??null;
                $result['phone'] = $phone;
                $result['add1'] = $add1;
                $result['add2'] = $add2;
                $result['add3'] = $add3;
                $result['add4'] = $add4;
                $result['add5'] = $add5;
                $result['addr'] = $addr;
                $result['reg_timestamp'] = strtotime($row['submitAt']);
                $result['lead_id'] = $row['id']??null;
                $result['encUserId'] = $row['encUserId'];
                $result['bizFormId'] = $row['bizFormId'];
            }

            if (is_array($result) && count($result)) {
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
            if (!empty($row['code'])) {
                $title = trim($row['code']);
            }else{
                $title = $row['ad_name'];
            }
            $landing = $this->facebook->landingGroup($row['ad_name']);
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

            if ($landing['event_seq']) {
                $result['event_seq'] = $landing['event_seq'];
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
                
                if (is_array($result)) {
                    $this->adlead->insertEventLeadFacebook($result);
                }
            }
        }
    }

    private function sendToEventLeadFromGoogle()
    {
    }
}
