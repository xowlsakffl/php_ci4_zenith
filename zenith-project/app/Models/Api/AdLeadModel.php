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

        $result = $this->kakao->query($sql)->getResultArray();

        return $result;
    }

    public function getFBAdLead()
    {
        $sql = "SELECT * FROM fb_ad AS ad
		JOIN fb_ad_lead as LE on ad.ad_id = LE.ad_id
		WHERE LE.created_time >= '2018-05-03 10:20:00' and ad.ad_name REGEXP '#[0-9]+' and LE.send_time='0000-00-00 00:00:00'
		ORDER BY LE.created_time ";
                
        $result = $this->facebook->query($sql)->getResultArray();

        return $result;
    }

    public function getGoogleAdLead()
    {
    }

    public function insertEventLeadFacebook($row)
    {
        $is_added = false;
        $sql = "SELECT * FROM event_leads WHERE event_seq = '{$row['event_seq']}' AND site='{$row['site']}' AND name='{$row['name']}' AND gender='{$row['gender']}' AND age='{$row['age']}' AND phone=enc_data('{$row['phone']}') AND add1='{$row['add1']}' AND add2='{$row['add2']}' AND add3='{$row['add3']}' AND add4='{$row['add4']}' AND add5='{$row['add5']}' AND addr='{$row['addr']}' AND lead_id='{$row['id']}'";
        $result = $this->zenith->query($sql);
        $is_added = count($result->getResultArray());
        if (!$is_added) {
            $row['name'] = $this->zenith->escape($row['name']);
            $sql = "INSERT INTO event_leads SET event_seq = '{$row['event_seq']}', site='{$row['site']}', name={$row['name']}, gender='{$row['gender']}', age='{$row['age']}', phone=ENC_DATA('{$row['phone']}'), add1='{$row['add1']}', add2='{$row['add2']}', add3='{$row['add3']}', add4='{$row['add4']}', add5='{$row['add5']}', addr='{$row['addr']}', reg_date='{$row['reg_date']}', is_deleted=0, lead_id='{$row['id']}', status=1";
            $result = $this->zenith->query($sql);
            if ($result) {
                $sql = "UPDATE fb_ad_lead SET send_time=now() WHERE id='{$row['id']}'";
                $result = $this->facebook->query($sql);
            }
        }
    }

    public function insertEventLeadKakao($row)
    {
        $row['name'] = $this->kakao->escape($row['name']);
        $sql = "INSERT INTO event_leads(event_seq, site, name, phone, add1, add2, add3, add4, add5, reg_date, is_deleted, lead_id, status)
                VALUES('{$row['event_seq']}', '{$row['site']}', {$row['name']}, ENC_DATA('{$row['phone']}'), '{$row['add1']}', '{$row['add2']}', '{$row['add3']}', '{$row['add4']}', '{$row['add5']}', '{$row['reg_date']}', 0, '{$row['id']}', 1)
                ON DUPLICATE KEY
                UPDATE event_seq='{$row['event_seq']}', site='{$row['site']}', name={$row['name']}, phone=ENC_DATA('{$row['phone']}'), add1='{$row['add1']}', add2='{$row['add2']}', add3='{$row['add3']}', add4='{$row['add4']}', add5='{$row['add5']}', reg_date='{$row['reg_date']}', lead_id='{$row['id']}'";
        $result = $this->zenith->query($sql);
        if ($result) {
            $sql = "UPDATE mm_bizform_user_response SET send_time=now() WHERE encUserId='{$row['encUserId']}' AND bizFormId='{$row['bizFormId']}'";
            $result = $this->kakao->query($sql);
        }
    }

    public function insertEventLeadGoogle($row)
    {
    }

    public function getBizformQuestion($bizformId, $itemId)
    {
        $sql = "SELECT bizform_id, id, title FROM mm_bizform_items WHERE bizform_id = '{$bizformId}' AND id = '{$itemId}'";
        $result = $this->kakao->query($sql)->getRowArray();
        if (!$result) return null;
        return $result;
    }
}
