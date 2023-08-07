<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class BlackListModel extends Model
{
    public function getBlackLists($data)
    {
        $srch = $data['searchData'];
        $ipBuilder = $this->db->table('event_blacklist');
        $ipBuilder->select('CONCAT("ip_", seq) as seq, ip, "" AS phone, forever, username, term, reg_date');

        $phoneBuilder = $this->db->table('event_phone_blacklist');
        $phoneBuilder->select('CONCAT("phone_", seq) as seq, "" AS ip, phone, 0 AS forever, "" AS username, "" AS term, reg_date');
        $ipBuilder->unionAll($phoneBuilder);

        $resultQuery = $this->db->newQuery()->fromSubquery($ipBuilder, 'black');

        if(!empty($srch['stx'])){
            $resultQuery->like('ip', $srch['stx']);
            $resultQuery->orLike('phone', $srch['stx']);
        }

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $resultQuery;

        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "reg_date desc";
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if(isset($data['length']) && !isset($data['noLimit']) && ($data['length'] != -1)) $resultQuery->limit($data['length'], $data['start']);

        $result = $resultQuery->get()->getResultArray();
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

    public function getBlackListPhone($data)
    {
        $builder = $this->db->table('event_phone_blacklist');
        $builder->select('*');
        $builder->where('seq', $data);

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

    public function getBlackListByPhone($phone)
    {
        $builder = $this->db->table('event_phone_blacklist');
        $builder->select('*');
        $builder->where('phone', $phone);

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createBlackList($data)
    {
        $builder = $this->db->table('event_blacklist');
        $builder->insert($data);

        return true;
    }

    public function createBlackListPhone($data)
    {
        $builder = $this->db->table('event_phone_blacklist');
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

    public function deleteBlackListPhone($seq)
    {
        $builder = $this->db->table('event_phone_blacklist');
        $builder->where('seq', $seq);
        $builder->delete();
        return true;
    }
}
