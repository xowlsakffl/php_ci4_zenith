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
        
        $result = $this->facebook->query($sql);

        return $result;
    }
}
