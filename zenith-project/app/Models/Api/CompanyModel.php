<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    /* protected $DBGroup          = 'default';
    protected $table            = 'companies';
    protected $primaryKey       = 'cdx';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['companyType', 'companyName', 'companyTel'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'companyType' => 'required',
        'companyName' => 'required',
        'companyTel' => 'required',
    ];
    protected $validationMessages   = [
        'companyType' => [
            'required' => '타입은 필수 입력사항입니다.',
        ],
        'companyName' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
        'companyTel' => [
            'required' => '전화번호는 필수 입력사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = []; */

    protected $zenith;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getCompanies($data)
    {
        $srch = $data['searchData'];
        $builder = $this->zenith->table('companies AS c');
        $builder->select('c.*, ci.company_parent_id as parent, parent_c.name as p_name');
        $builder->join('companies_idx AS ci', 'c.id = ci.company_id', 'left');
        $builder->join('companies AS parent_c', 'parent_c.id = ci.company_parent_id', 'left');

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

    public function getCompany()
    {
        $builder = $this->select('c.*, GROUP_CONCAT(DISTINCT u.id) as user_id, GROUP_CONCAT(DISTINCT u.username) as username, ci.parent_cdx as parent, parent_c.companyName as parent_company_name');
        $builder->from('companies as c');
        $builder->join('companies_users as cu', 'c.cdx = cu.company_id', 'left');
        $builder->join('users as u', 'cu.user_id = u.id', 'left');
        $builder->join('companies_idx as ci', 'c.cdx = ci.cdx', 'left');
        $builder->join('companies as parent_c', 'ci.parent_cdx = parent_c.cdx', 'left');
        if($id){
            $builder->where('c.cdx', $id);
        }
        $builder->groupBy('c.cdx');
        if($id){
            $result = $builder->get()->getRow();
        }else{
            $result = $builder->get()->getResultArray();
        }

        return $result;
    }

    public function getAgency()
    {
        $builder = $this->db->table('companies');
        $builder->select('*');
        $builder->where('companyType', '광고대행사');
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
