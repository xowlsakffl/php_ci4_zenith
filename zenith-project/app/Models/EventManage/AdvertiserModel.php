<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class AdvertiserModel extends Model
{
    public function getAdvertisers($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('(SELECT info.seq AS info_seq, adv.*, SUM(db.db_count) as db_count, SUM(db.db_count)* info.db_price as price
            FROM event_advertiser AS adv
            LEFT JOIN event_information AS info ON info.advertiser = adv.seq
            LEFT JOIN event_leads_count AS db ON db.seq = info.seq AND db.date BETWEEN LAST_DAY(NOW() - interval 1 month) + interval 1 DAY AND LAST_DAY(NOW())
            WHERE 1
            GROUP BY adv.seq, info.seq, db.seq
            ORDER BY adv.seq DESC) AS advertiser');
        $builder->select('advertiser.*, COUNT(advertiser.info_seq) AS total, SUM(advertiser.db_count) AS sum_db, SUM(advertiser.price) AS sum_price, (advertiser.account_balance - SUM(advertiser.price)) as remain_balance');
        $builder->groupBy('advertiser.seq');

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('advertiser.name', $srch['stx']);
            $builder->orLike('advertiser.agent', $srch['stx']);
            $builder->groupEnd();
        }

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "advertiser.seq desc";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getAdvertiser($data)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->select('*');
        $builder->where('seq', $data['seq']);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getOverwatchByAdvertiser($seq)
    {
        $builder = $this->db->table('event_overwatch');
        $builder->select('*');
        $builder->where('advertiser', $seq);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getMedia($data)
    {
        $subBuilder = $this->db->table('event_information');
        $subBuilder->select('media');
        $subBuilder->where('advertiser', $data['seq']);
        $subResult = $subBuilder->getCompiledSelect();

        $builder = $this->db->table('event_media');
        $builder->select('*');
        $builder->where("seq IN($subResult)");

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function createAdv($data)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->insert($data);

        return true;
    }

    public function updateAdv($data, $seq)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->where('seq', $seq);
        $builder->update($data);

        return true;
    }
}
