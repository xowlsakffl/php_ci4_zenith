<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class BlackListModel extends Model
{
    public function getBlackLists($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('event_blacklist');
        $builder->select('*, COUNT(seq) as total');
        $builder->groupBy('seq');

        if(!empty($srch['stx'])){
            $builder->like('ip', $srch['stx']);
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
        $orderBy[] = "reg_date desc";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getBlackList($data)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->select('*');
        $builder->where('seq', $data['seq']);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getBlackListByIp($ip)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->select('*');
        $builder->where('ip', $ip);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createBlackList($data)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->insert($data);

        return true;
    }

    public function updateBlackList($data)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->where('ip', $data['ip']);
        $builder->replace($data);

        return true;
    }

    public function deleteBlackList($seq)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->where('seq', $seq);
        $builder->delete();

        return true;
    }
}
