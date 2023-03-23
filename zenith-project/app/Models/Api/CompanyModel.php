<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $DBGroup          = 'default';
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
    protected $afterDelete    = [];

    public function getCompanies($id)
    {
        $builder = $this->select('c.*, GROUP_CONCAT(DISTINCT u.id) as user_id, GROUP_CONCAT(DISTINCT u.username) as username, ci.parent_cdx as parent, parent_c.companyName as parent_company_name');
        $builder->from('companies as c');
        $builder->join('companies_users as cu', 'c.cdx = cu.company_id', 'left');
        $builder->join('users as u', 'cu.user_id = u.id', 'left');
        $builder->join('companies_idx as ci', 'c.cdx = ci.cdx', 'left');
        $builder->join('companies as parent_c', 'ci.parent_cdx = parent_c.cdx', 'left');
        $builder->where('c.cdx', $id);
        $builder->groupBy('c.cdx');
        $result = $builder->get()->getRow();

        return $result;
    }
}
