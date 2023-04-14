<?php

namespace App\Models\Integrate;

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
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        info.seq as info_seq, 
        adv.name AS advertiser, 
        med.media, adv.is_stop, 
        info.description AS tab_name, 
        dec_data(el.phone) as dec_phone, 
        el.name,
        el.reg_date,
        el.gender,
        el.age,
        el.branch,
        el.addr,
        el.email,
        el.site,
        el.add1,
        el.add2,
        el.add3,
        el.add4,
        el.add5
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
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

        if(!empty($data['adv'])){
            $builder->whereIn('adv.name', $data['adv']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.description', $data['event']);
        }

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        $builder->orderBy('el.reg_date', 'DESC');
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
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        adv.name as adv_name,
        med.media as med_name,
        info.description as event,
        count(el.seq) as countAll,
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('adv.name !=', '');
        $builder->where('med.media !=', '');
        $builder->where('info.description !=', '');
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

        if(!empty($data['adv'])){
            $builder->whereIn('adv.name', $data['adv']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.description', $data['event']);
        }

        $builder->groupBy(['adv.name', 'med.media', 'info.description']);
        $result = $builder->get()->getResultArray();
        
        return $result;
    }

    public function getFirstLeadCount($data)
    {
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        adv.name as adv_name,
        med.media as med_name,
        info.description as event,
        count(el.seq) as countAll,
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('adv.name !=', '');
        $builder->where('med.media !=', '');
        $builder->where('info.description !=', '');
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);

        $builder->groupBy(['adv.name', 'med.media', 'info.description']);
        $result = $builder->get()->getResultArray();
        
        return $result;
    }

    public function getStatusCount($data)
    {
        $builder = $this->zenith->table('event_leads as el');
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
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
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

        if(!empty($data['adv'])){
            $builder->whereIn('adv.name', $data['adv']);
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', $data['media']);
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.description', $data['event']);
        }
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
