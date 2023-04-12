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
        $builder->select("
        info.seq as info_seq, 
        adv.seq AS adv_seq, 
        adv.name AS advertiser, 
        med.seq as media_seq, 
        med.media, adv.is_stop, 
        info.description AS tab_name, 
        dec_data(el.phone) as dec_phone, 
        el.*
        ");
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left'); 
        $builder->where('el.is_deleted', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['adv_seq'])){
            $builder->whereIn('adv.seq', $data['adv_seq']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.seq', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.seq', $data['event']);
        }
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        $builder->orderBy('el.seq', 'DESC');
        $builder->limit($data['length'], $data['start']);

        // 결과 반환
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getEventLeadCount($data)
    {
        $builder = $this->zenith->table('event_information as info');
        $builder->select("
        adv.seq as adv_seq, 
        med.seq as med_seq, 
        info.seq as info_seq, 
        count(el.seq) as countAll
        ");
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left'); 
        $builder->where('el.is_deleted', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['adv_seq'])){
            $builder->whereIn('adv.seq', $data['adv_seq']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.seq', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.seq', $data['event']);
        }
        $builder->groupBy(['adv.seq', 'med.seq', 'info.seq']);
        $result = $builder->get()->getResultArray();
        
        return $result;
    }
    
    public function getAdvertiser($data)
    {
        $builder = $this->zenith->table('event_advertiser adv');
        $builder->select('adv.seq as seq, adv.name as advertiser, COUNT(el.seq) as total');
        $builder->join('event_information info', 'info.advertiser = adv.seq AND adv.is_stop = 0', 'left');
        $builder->join('event_media med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('advertiser !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);
        $builder->groupBy('advertiser');
        $builder->orderBy('advertiser');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getMedia($data)
    {
        $builder = $this->zenith->table('event_media med');
        $builder->select('med.seq as media_seq, med.media as media_name, COUNT(el.seq) as total');
        $builder->join('event_information info', 'info.media = med.seq', 'left');
        $builder->join('event_advertiser adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('med.media !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);
        $builder->groupBy('med.media');
        $builder->orderBy('med.media');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getEvent($data)
    {
        $builder = $this->zenith->table('event_information info');
        $builder->select('info.seq as info_seq, info.description as event, COUNT(el.seq) as total');
        $builder->join('event_advertiser adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('info.description !=', '');
        $builder->where('el.status !=', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);
        $builder->groupBy('info.description');
        $builder->orderBy('info.description');
        $builder->distinct();
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getStatusCount($data)
    {
        $builder = $this->zenith->table('event_information as info');
        $builder->select("
        COUNT(CASE WHEN el.status=1 then 1 end) as 인정, 
        COUNT(CASE WHEN el.status=2 then 1 end) as 중복, 
        COUNT(CASE WHEN el.status=3 then 1 end) as 성별불량, 
        COUNT(CASE WHEN el.status=4 then 1 end) as 나이불량, 
        COUNT(CASE WHEN el.status=5 then 1 end) as 콜불량, 
        COUNT(CASE WHEN el.status=6 then 1 end) as 번호불량, 
        COUNT(CASE WHEN el.status=7 then 1 end) as 테스트, 
        COUNT(CASE WHEN el.status=8 then 1 end) as 이름불량, 
        COUNT(CASE WHEN el.status=9 then 1 end) as 지역불량, 
        COUNT(CASE WHEN el.status=10 then 1 end) as 업체불량, 
        COUNT(CASE WHEN el.status=11 then 1 end) as 미성년자, 
        COUNT(CASE WHEN el.status=12 then 1 end) as 본인아님, 
        COUNT(CASE WHEN el.status=99 then 1 end) as 확인");
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as el', 'el.event_seq = info.seq', 'left'); 
        $builder->where('el.is_deleted', 0);
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $data['stx']);
            $builder->orLike('info.seq', $data['stx']);
            $builder->orLike('med.media', $data['stx']);
            $builder->orLike('info.description', $data['stx']);
            $builder->orLike('el.name', $data['stx']);
            $builder->orLike('el.branch', $data['stx']);
            $builder->orLike('el.add1', $data['stx']);
            $builder->orLike('el.add2', $data['stx']);
            $builder->orLike('el.add3', $data['stx']);
            $builder->orLike('el.add4', $data['stx']);
            $builder->orLike('el.add5', $data['stx']);
            $builder->groupEnd();
        }
        
        if(!empty($data['adv_seq'])){
            $builder->whereIn('adv.seq', $data['adv_seq']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.seq', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.seq', $data['event']);
        }
        
        $builder->orderBy('el.seq', 'DESC');
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
