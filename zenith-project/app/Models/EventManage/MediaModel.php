<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class MediaModel extends Model
{
    public function getMedias($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('event_media AS med');
        $builder->select('med.*, COUNT(info.seq) AS total');
        $builder->join('event_information AS info', 'info.media = med.seq', 'left');

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('med.media', $srch['stx']);
            $builder->orLike('med.target', $srch['stx']);
            $builder->groupEnd();
        }
        $builder->groupBy('med.seq');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "med.seq desc";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getMedia($data)
    {
        $builder = $this->db->table('event_media');
        $builder->select('*');
        $builder->where('seq', $data['seq']);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createMedia($data)
    {
        $builder = $this->db->table('event_media');
        $builder->insert($data);

        return true;
    }

    public function updateMedia($data, $seq)
    {
        $builder = $this->db->table('event_media');
        $builder->where('seq', $seq);
        $builder->update($data);

        return true;
    }
}
