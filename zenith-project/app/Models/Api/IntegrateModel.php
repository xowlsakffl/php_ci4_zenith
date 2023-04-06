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
        $builder = $this->zenith->table('event_information as info');
        $builder->select("CONCAT('evt_', info.seq) AS seq, adv.name AS advertiser, med.media, adv.is_stop, info.description AS tab_name, dec_data(el.phone) as dec_phone, el.*");
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
        $builder->limit($data['length'], $data['start']);

        // 결과 반환
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }
    
    public function getAdvertiser($data)
    {
        $builder = $this->zenith->table('event_advertiser adv');
        $builder->select('adv.name AS advertiser, COUNT(el.seq) as total');
        $builder->join('event_information info', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('advertiser !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <=', $data['edate']);
        $builder->groupBy('advertiser');
        $builder->orderBy('advertiser');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getMedia($data)
    {
        $builder = $this->zenith->table('event_media med');
        $builder->select('med.media as media_name, COUNT(el.seq) as total');
        $builder->join('event_information info', 'info.media = med.seq', 'left');
        $builder->join('event_advertiser adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('med.media !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <=', $data['edate']);
        $builder->groupBy('med.media');
        $builder->orderBy('med.media');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getEvent($data)
    {
        $builder = $this->zenith->table('event_information info');
        $builder->select('info.description as event, COUNT(el.seq) as total');
        $builder->join('event_advertiser adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('info.description !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('el.reg_date >=', $data['sdate']);
        $builder->where('el.reg_date <=', $data['edate']);
        $builder->groupBy('info.description');
        $builder->orderBy('info.description');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }
}
