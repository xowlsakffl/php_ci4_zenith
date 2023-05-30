<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $zenith;
    protected $validationRules      = [
        'p_name' => 'required',
        'name' => 'required',
        'tel' => 'required',
    ];
    protected $validationMessages   = [
        'p_name' => [
            'required' => '소속대행사는 필수 입력사항입니다.',
        ],
        'name' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
        'tel' => [
            'required' => '전화번호는 필수 입력사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getCompanies($data)
    {
        $srch = $data['searchData'];
        $builder = $this->zenith->table('companies AS c');
        $builder->select('c.*, ci.company_parent_id as p_id, parent_c.name as p_name');
        $builder->join('companies_idx AS ci', 'c.id = ci.company_id', 'left');
        $builder->join('companies AS parent_c', 'parent_c.id = ci.company_parent_id', 'left');
        $builder->where('c.status !=', 0);
        if(!empty($srch['sdate'] && $srch['edate'])){
            $builder->where('DATE(c.created_at) >=', $srch['sdate']);
            $builder->where('DATE(c.created_at) <=', $srch['edate']);
        }

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('c.type', $srch['stx']);
            $builder->orLike('c.name', $srch['stx']);
            $builder->orLike('c.tel', $srch['stx']);
            $builder->orLike('parent_c.name', $srch['stx']);
            $builder->groupEnd();
        }
        
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        $builder->groupBy('c.id');
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "id DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);

        if($data['length'] > 0){
            $builder->limit($data['length'], $data['start']);
        }
    
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getCompany($id)
    {
        $builder = $this->zenith->table('companies AS c');
        $builder->select('c.*, GROUP_CONCAT(DISTINCT u.username) as username, ci.company_parent_id as p_id, parent_c.name as p_name');
        $builder->join('companies_users as cu', 'c.id = cu.company_id', 'left');
        $builder->join('users as u', 'cu.user_id = u.id', 'left');
        $builder->join('companies_idx as ci', 'c.id = ci.company_id', 'left');
        $builder->join('companies as parent_c', 'ci.company_parent_id = parent_c.id', 'left');
        $builder->where('c.id', $id);
        $builder->where('c.status !=', 0);
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getAgencies($stx = NULL)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('type', '광고대행사');
        $builder->where('status !=', 0);
        if(!empty($stx)){
            $builder->like('name', $stx);
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getAgency($p_name)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('type', '광고대행사');
        $builder->where('status !=', 0);
        if(!empty($p_name)){
            $builder->where('name', $p_name);
        }

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function createCompany($data, $agency)
    {
        $builder_1 = $this->zenith->table('companies');
        $builder_1->set('type', $data['type']);
        $builder_1->set('name', $data['name']);
        $builder_1->set('tel', $data['tel']);
        $builder_1->set('status', 1);
        $result_1 = $builder_1->insert();
        $insertId = $this->zenith->insertID();
        $builder_2 = $this->zenith->table('companies_idx');
        $newRecord = [
            'company_id' => $insertId,
            'company_parent_id' => $agency['id']
        ];
        $builder_2->insert($newRecord);
        return true;
    }

    public function setCompany($data, $agency)
    {
        $builder_1 = $this->zenith->table('companies');
        $builder_1->set('name', $data['name']);
        $builder_1->set('tel', $data['tel']);
        $builder_1->where('id', $data['id']);
        $result_1 = $builder_1->update();

        $builder_2 = $this->zenith->table('companies_idx');
        $builder_2->where('company_id', $data['id']);
        $result = $builder_2->get()->getResult();

        if (empty($result)) {
            $newRecord = [
                'company_id' => $data['id'],
                'company_parent_id' => $agency['id']
            ];
            $result_2 = $builder_2->insert($newRecord);
        } else {
            $builder_2->set('company_parent_id', $agency['id']);
            $builder_2->where('company_id', $data['id']);
            $result_2 = $builder_2->update();
        }
        
        if ($result_1 && $result_2) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCompany($data)
    {
        $builder = $this->zenith->table('companies');
        $builder->set('status', 0);
        $builder->where('id', $data['id']);
        $result = $builder->update();

        return true;
    }
}
