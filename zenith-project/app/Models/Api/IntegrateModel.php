<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class IntegrateModel extends Model
{
    protected $zenith;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getEventLead($data)
    {
        $offset = ($data['page'] - 1) * $data['limit'];

        $builder = $this->zenith->table('event_information as info');
        $builder->select("CONCAT('evt_', info.seq) AS seq, adv.name AS advertiser, med.media, adv.is_stop, info.description AS tab_name, el.*");
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left'); 
        $builder->where('el.is_deleted', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <=', $data['edate']);
        $builder->orderBy('el.seq', 'DESC');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        $builder->limit($data['limit'], $offset);

        // 결과 반환
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->get()->getResultArray();
        return [
            'data' => $result,
            'dataNoLimit' => $resultNoLimit
        ];
    }
    
    /* public function getEventLead()
    {
        $sql = "SELECT
        CONCAT( 'evt_', info.seq ) AS seq,
        adv.NAME AS advertiser,
        med.media,
        adv.is_stop,
        info.description AS tab_name,
        a.*
    FROM
        event_information AS info
        LEFT JOIN event_advertiser AS adv ON info.advertiser = adv.seq AND adv.is_stop = 0
        LEFT JOIN event_media AS med ON info.media = med.seq 
        LEFT JOIN event_leads AS a ON a.event_seq = info.seq
    WHERE
        a.is_deleted = 0 
        AND adv.NAME = '그라클레스'
    ORDER BY
        a.seq DESC;";

        $result = $this->zenith->query($sql);
        $this->zenith->close();
        return $result;
    } */
}
