<?php

namespace App\Models\Integrate;

use CodeIgniter\Model;

class IntegrateModel extends Model
{
    protected $zenith;
    private $leads_status = [
        "인정" => 1, 
        "중복" => 2, 
        "성별불량" => 3, 
        "나이불량" => 4, 
        "콜불량" => 5, 
        "번호불량" => 6, 
        "테스트" => 7, 
        "이름불량" => 8, 
        "지역불량" => 9, 
        "업체불량" => 10, 
        "미성년자" => 11, 
        "본인아님" => 12, 
        "쿠키중복" => 13, 
        "확인" => 99, 
    ];
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getEventLead($data)
    {
        $srch = $data['searchData'];
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        info.seq as info_seq, 
        adv.name AS advertiser, 
        med.media, adv.is_stop, 
        info.description AS tab_name, 
        dec_data(el.phone) as dec_phone, 
        el.seq,
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
        el.add5,
        el.status,
        count(lm.leads_seq) as memo_cnt
        ");
        $builder->join('event_information as info', "info.seq = el.event_seq", 'left');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads_memo as lm', 'el.seq = lm.leads_seq', 'left');
        $builder->where('el.is_deleted', 0);
        $builder->where('DATE(el.reg_date) >=', $srch['sdate']);
        $builder->where('DATE(el.reg_date) <=', $srch['edate']);

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('adv.name', $srch['stx']);
            $builder->orLike('info.seq', $srch['stx']);
            $builder->orLike('med.media', $srch['stx']);
            $builder->orLike('info.description', $srch['stx']);
            $builder->orLike('el.name', $srch['stx']);
            $builder->orLike('el.branch', $srch['stx']);
            $builder->orLike('el.add1', $srch['stx']);
            $builder->orLike('el.add2', $srch['stx']);
            $builder->orLike('el.add3', $srch['stx']);
            $builder->orLike('el.add4', $srch['stx']);
            $builder->orLike('el.add5', $srch['stx']);
            $builder->groupEnd();
        }

        if(!empty($srch['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$srch['advertiser']));
        }

        if(!empty($srch['media'])){
            $builder->whereIn('med.media', explode("|",$srch['media']));
        }

        if(!empty($srch['event'])){
            $builder->whereIn('info.description', explode("|",$srch['event']));
        }
        if(!empty($srch['status'])){
            $status = array_map(function($v){return $this->leads_status[$v];}, explode("|",$srch['status']));
            $builder->whereIn('el.status', $status);
        }

        // limit 적용한 쿼리
        $builder->groupBy(['el.seq','lm.leads_seq'], true);
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "seq DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit'])) $builder->limit($data['length'], $data['start']);
        // dd($builder->getCompiledSelect());
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
        $data = $data['searchData'];
        $builder = $this->zenith->table('event_leads as el');
        $builder->select("
        el.seq,
        adv.name as advertiser,
        med.media as media,
        info.description as event
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
        
        $filteredBuilder = clone $builder;

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

        if(!empty($data['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$data['advertiser']));
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', explode("|",$data['media']));
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.description', explode("|",$data['event']));
        }

        $result = [
            'filteredResult' => $builder->get()->getResultArray(),
            'noFilteredResult' => $filteredBuilder->get()->getResultArray()
        ];

        return $result;
    }


    public function getStatusCount($data)
    {
        $data = $data['searchData'];
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
        COUNT(CASE WHEN el.status=13 then 1 end) as 쿠키중복, 
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

        if(!empty($data['advertiser'])){
            $builder->whereIn('adv.name', explode("|",$data['advertiser']));
        }

        if(!empty($data['media'])){
            $builder->whereIn('med.media', explode("|",$data['media']));
        }

        if(!empty($data['event'])){
            $builder->whereIn('info.description', explode("|",$data['event']));
        }
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getMemo($data) 
    {
        $builder = $this->zenith->table('event_leads_memo');
        $builder->select("*");
        $builder->where("leads_seq", $data['seq']);
        $builder->orderBy("reg_date", "desc");
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function addMemo($data) {
        $builder = $this->zenith->table('event_leads');
        $builder->select("event_seq");
        $builder->where("seq", $data['leads_seq']);
        $row = $builder->get()->getRowArray();
        $data['event_seq'] = $row['event_seq'];
        if(!$data['event_seq']) return null;
        $data['reg_date'] = date('Y-m-d H:i:s');
        $data['username'] = auth()->user()->username;
        $this->zenith->transStart();
        $builder = $this->zenith->table('event_leads_memo');
        $builder->set('leads_seq', $data['leads_seq']);
        $builder->set('event_seq', $data['event_seq']);
        $builder->set('username', $data['username']);
        $builder->set('memo', $data['memo']);
        $builder->set('reg_date', $data['reg_date']);
        $result = $builder->insert();
        $insertId = $this->zenith->insertID();
        $this->zenith->transComplete();
        
        $result = [
            'result' => $result,
            'data' => [$data]
        ];
        return $result;
    }

    public function setStatus($data) {
        if(!$data['seq']) return null;
        $this->zenith->transStart();
        $builder = $this->zenith->table('event_leads');
        $builder->set('status', $data['status']);
        $builder->where('seq', $data['seq']);
        $result = $builder->update();
        $this->zenith->transComplete();
        
        $result = [
            'result' => $result,
            'data' => $data
        ];
        return $result;
    }
}
