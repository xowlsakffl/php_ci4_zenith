<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class BlackListModel extends Model
{
    public function getBlackLists($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('event_blacklist');
        $builder->select('*');

        if(!empty($srch['stx'])){
            $builder->like('data', $srch['stx']);
            $builder->orLike('memo', $srch['stx']);
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
        $orderBy[] = "datetime desc";
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
        $builder->where('seq', $data);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createBlackList($data)
    {
        $this->db->transStart(); 
        $builder = $this->db->table('event_blacklist');
        $builder->insert($data);
        $result = $this->db->transComplete();
        return $result;
    }

    public function deleteBlackList($seq)
    {
        $this->db->transStart(); 
        $builder = $this->db->table('event_blacklist');
        $builder->where('seq', $seq);
        $builder->delete();
        $result = $this->db->transComplete();
        return $result;
    }
}
