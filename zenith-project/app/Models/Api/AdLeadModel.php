<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class AdLeadModel extends Model
{
    protected $facebook, $google, $kakao, $zenith;

    public function __construct()
    {
        $this->facebook = \Config\Database::connect('facebook');
        $this->google = \Config\Database::connect('google');
        $this->kakao = \Config\Database::connect('kakao');
        $this->zenith = \Config\Database::connect();
    }

    public function getBizFormUserResponse()
    {
        $sql = "SELECT ur.*, mc.name, mc.id
                FROM mm_creative AS mc
                JOIN mm_bizform_user_response AS ur ON mc.id = ur.creative_id
                WHERE ur.send_time IS NULL
                ORDER BY ur.create_time ASC";

        $result = $this->kakao->query($sql);

        return $result;
    }

    public function getFBAdLead()
    {
        $sql = "SELECT * FROM fb_ad AS ad
		JOIN fb_ad_lead as LE on ad.ad_id = LE.ad_id
		WHERE LE.created_time >= '2018-05-03 10:20:00' and ad.ad_name REGEXP '#[0-9]+' and LE.send_time='0000-00-00 00:00:00'
		ORDER BY LE.created_time ";
                
        $result = $this->facebook->query($sql);

        return $result;
    }

    public function getGoogleAdLead()
    {
        $sql = "";
        
        $result = $this->google->query($sql);

        return $result;
    }

    public function insertEventLead($data)
    {
        foreach ($data as $key => $row) {
            $query = "";
            if ($row['event_seq']) {
                $is_added = false;
                $sql = "SELECT * FROM event_leads WHERE event_seq = '{$row['event_seq']}' AND site='{$row['site']}' AND name='{$row['name']}' AND gender='{$row['gender']}' AND age='{$row['age']}' AND phone=enc_data('{$row['phone']}') AND add1='{$row['add1']}' AND add2='{$row['add2']}' AND add3='{$row['add3']}' AND add4='{$row['add4']}' AND add5='{$row['add5']}' AND addr='{$row['addr']}' AND lead_id='{$row['lead_id']}'";
                $result = $this->zenith->query($sql);
                $is_added = count($result->getResultArray());
                dd($is_added);
                if (!$is_added) {
                    $row['full_name'] = $this->zenith->escape($row['full_name']);
                    if (preg_match('/^evt_/', $row['group_id'])) $query = ", event_seq='{$row['event_id']}'";
                    $sql = "INSERT INTO event_leads SET event_seq = '{$row['event_seq']}'{$query}, site='{$row['site']}', name='{$row['name']}', gender='{$row['gender']}', age='{$row['age']}', phone=ENC_DATA('{$row['phone']}'), add1='{$row['add1']}', add2='{$row['add2']}', add3='{$row['add3']}', add4='{$row['add4']}', add5='{$row['add5']}', addr='{$row['addr']}', reg_date='{$row['reg_date']}', deleted=0, fb_ad_lead_id='{$row['ad_id']}', enc_status=1";

                    $result = $this->zenith->query($sql);
                    // echo $sql."<br/>";
                    if ($result) {
                        $sql = "update fb_ad_lead set change_time=now() where id='{$row['id']}'";
                        $result = $this->db_query($sql);
                    } else {
                        echo $this->g5db->error;
                    }
                }
            }
        }
    }
}
