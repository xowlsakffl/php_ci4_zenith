<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class ChangeModel extends Model
{
    public function getChanges($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('event_conversion');
        $builder->select('*, COUNT(id) AS total');

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('id', $srch['stx']);
            $builder->orLike('name', $srch['stx']);
            $builder->groupEnd();
        }
        $builder->groupBy('id');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "ec_datetime desc";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getChange($id)
    {
        $builder = $this->db->table('event_conversion');
        $builder->select('*');
        $builder->where('id', $id);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createChange($data)
    {
        $builder = $this->db->table('event_conversion');
        $builder->insert($data);

        return true;
    }

    public function updateChange($data)
    {
        $builder = $this->db->table('event_conversion');
        $builder->set('id', $data['id']);
        $builder->set('name', $data['name']);
        $builder->set('token', $data['token']);
        if($data['old_id'] != $data['id']){
            $builder->where('id', $data['old_id']);
        }else{
            $builder->where('id', $data['id']);
        }
        $builder->update();
    
        return true;
    }
}
