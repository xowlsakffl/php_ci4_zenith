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
            if ($match[1][0]) {
                $add1 = $match[1][0];
            } else {
                $add1 = "";
            }
            if ($match[1][1]) {
                $add2 = $match[1][1];
            } else {
                $add2 = "";
            }
            if ($match[1][2]) {
                $add3 = $match[1][2];
            } else {
                $add3 = "";
            }
            if ($match[1][3]) {
                $add4 = $match[1][3];
            } else {
                $add4 = "";
            }
            if ($match[1][4]) {
                $add5 = $match[1][4];
            } else {
                $add5 = "";
            }
            if ($match[1][5]) {
                $add6 = $match[1][5];
            } else {
                $add6 = "";
            }
            if ($match[1][6]) {
                $add6 .=  "/" . $match[1][6];
            } else {
                $add6 = $add6;
            }
            if ($match[1][7]) {
                $add6 .=  "/" . $match[1][7];
            } else {
                $add6 = $add6;
            }
            if ($match[1][8]) {
                $add6 .=  "/" . $match[1][8];
            } else {
                $add6 = $add6;
            }
            if ($match[1][9]) {
                $add6 .=  "/" . $match[1][9];
            } else {
                $add6 = $add6;
            }
            if ($match[1][10]) {
                $add6 .=  "/" . $match[1][10];
            } else {
                $add6 = $add6;
            }


            if ($landing['media']) {
                $result[$i]['group_id'] = $landing['app_id'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['full_name'] = $this->db->real_escape_string($full_name);
                $result[$i]['gender'] = $this->db->real_escape_string($gender);
                $result[$i]['age'] = $this->db->real_escape_string($age);
                $result[$i]['phone'] = $this->db->real_escape_string($phone);
                $result[$i]['add1'] = $this->db->real_escape_string($add1);
                $result[$i]['add2'] = $this->db->real_escape_string($add2);
                $result[$i]['add3'] = $this->db->real_escape_string($add3);
                $result[$i]['add4'] = $this->db->real_escape_string($add4);
                $result[$i]['add5'] = $this->db->real_escape_string($add5);
                $result[$i]['add6'] = $this->db->real_escape_string($add6);
                $result[$i]['addr'] = $this->db->real_escape_string($addr);
                $result[$i]['reg_date'] = $row['created_time'];
                $result[$i]['ad_id'] = $row['ad_id'];
                $result[$i]['id'] = $row['id'];
                
                //              if($result[$i]['add2']){
                //                  echo "group_id:".$result[$i]['group_id'] ."/"."site:".$landing['site']."/"."first_name:".$result[$i]['full_name']."/"."gender:".$result[$i]['gender']."/"."age:".$result[$i]['age']."/"."phone:".$result[$i]['phone']."/".$result[$i]['add1']."/".$result[$i]['add2']."/".$result[$i]['add3']."/".$result[$i]['add4']."/".$result[$i]['add5']."/".$result[$i]['add6']."<br/>";
                //              }
                $i++;
            }
        }

        //$kakao_ads = $this->adlead->getBizFormUserResponse();


    }
}
