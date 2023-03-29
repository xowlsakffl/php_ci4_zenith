<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\Models\Api\AdLeadModel;

class AdLeadController extends BaseController
{
    protected $adlead;

    public function __construct()
    {
        $this->adlead = model(AdLeadModel::class); 
    }

    public function sendToEventLead()
    {
        $facebook_ads = $this->adlead->getFBAdLead()->getResultArray();
        
        foreach($facebook_ads as $row){
            $landing = $this->landingGroup($row['ad_name']);
            $i = 0;
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
                $result[$i]['event_seq'] = $landing['event_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['name'] = $full_name;
                $result[$i]['gender'] = $gender;
                $result[$i]['age'] = $age;
                $result[$i]['phone'] = $phone;
                $result[$i]['add1'] = $add1;
                $result[$i]['add2'] = $add2;
                $result[$i]['add3'] = $add3;
                $result[$i]['add4'] = $add4;
                $result[$i]['add5'] = $add5;
                $result[$i]['addr'] = $addr;
                $result[$i]['reg_date'] = $row['created_time'];
                $result[$i]['lead_id'] = $row['ad_id'];            
                $i++;
            }
        }

        if (is_array($result)) {
            $this->adlead->insertEventLead($result);
        }

        //$kakao_ads = $this->adlead->getBizFormUserResponse();


    }

    public function landingGroup($title)
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
        
        return null;
    }
}
