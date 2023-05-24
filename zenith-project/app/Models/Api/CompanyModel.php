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

    public function getCompanies()
    {
        $builder = $this->zenith->table('companies as c');//여기부터
        $builder = $builder->select('c.*, ci.parent_cdx as parent, parent_c.companyName as parent_company_name');
        $builder->join('companies_idx as ci', 'c.cdx = ci.cdx', 'left');
        $builder->join('companies as parent_c', 'ci.parent_cdx = parent_c.cdx', 'left');

        if(isset($param['limit'])){
            $limit = $param['limit'];
        }else{
            $limit = 10;
        }

        if(!empty($param['search'])){
            $searchText = $param['search'];
            $builder->groupStart();
                $builder->orlike('c.companyType', $searchText, 'both');
                $builder->orLike('c.companyName', $searchText, 'both');
                $builder->orLike('c.companyTel', $searchText, 'both');
            $builder->groupEnd();
            $data['pager']['search'] = $param['search'];
        }

        if(!empty($param['startDate']) && !empty($param['endDate'])){
            $builder->where('c.created_at >=', $param['startDate'].' 00:00:00');
            $builder->where('c.created_at <=', $param['endDate'].' 23:59:59');
            
            $data['pager']['startDate'] = $param['startDate'];
            $data['pager']['endDate'] = $param['endDate'];
        }

        if(!empty($param['sort'])){
            if($param['sort'] == 'old'){
                $builder->orderBy('c.cdx', 'asc');
            }else{
                $builder->orderBy('c.cdx', 'desc');
            }
            
            $data['pager']['sort'] = $param['sort'];
        }
        $builder->groupBy('c.cdx');

        $data['result'] = $builder->paginate($limit);

        $data['pager']['limit'] = intval($limit);
        $data['pager']['total'] = $builder->pager->getTotal();
        $data['pager']['pageCount'] = $builder->pager->getPageCount();
        $data['pager']['currentPage'] = $builder->pager->getCurrentPage();
        $data['pager']['firstPage'] = $builder->pager->getFirstPage();
        $data['pager']['lastPage'] = $builder->pager->getLastPage();
    }

    public function getCompanies()
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
