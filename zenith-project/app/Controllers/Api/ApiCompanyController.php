<?php

namespace App\Controllers\Api;

use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class ApiCompanyController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    protected $data, $company, $validation;

    public function __construct()
    {
        $this->company = model(CompanyModel::class);
    }

    public function getCompanies()
    {
        if (/* $this->request->isAJAX() && */ strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();

            
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
    }

    public function get($id = NULL)
    {
        if (/* $this->request->isAJAX() && */ strtolower($this->request->getMethod()) === 'get') {

            if ($id) {
                $data['result'] = $this->company->getCompanies($id);

                $data['result']->user_id = explode(',', $data['result']->user_id);
                $data['result']->username = explode(',', $data['result']->username);

                $data['result']->users = array();
                foreach($data['result']->user_id as $key => $user_id) {
                    $data['result']->users[$key]['id'] = $user_id;
                    $data['result']->users[$key]['username'] = $data['result']->username[$key];
                }
                
                unset($data['result']->user_id);
                unset($data['result']->username);
            } else {
                $param = $this->request->getGet();

                $builder = $this->company->select('c.*, ci.parent_cdx as parent, parent_c.companyName as parent_company_name');
                $builder->from('companies as c');
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
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
    }

    public function put($id = false)
    {
        $ret = false;
        if (strtolower($this->request->getMethod()) === 'put') {
            if (!empty($this->data)) {
                $this->validation = \Config\Services::validation();
                $this->validation->setRules($this->company->validationRules, $this->company->validationMessages);
                if (!$this->validation->run($this->data)) {
                    $errors = $this->validation->getErrors();
                    return $this->failValidationErrors($errors);
                }
    
                $data = [
                    'companyType' => $this->data['companyType'],
                    'companyName' => $this->data['companyName'],
                    'companyTel' => $this->data['companyTel'],
                ];
    
                $this->company->update($id, $data);
                $ret = true;
            };
        }else{
            return $this->fail("잘못된 요청");
        }
    
        return $this->respond($ret);
    }

    public function post()
    {
        $ret = false;
        if (strtolower($this->request->getMethod()) === 'post') {
            if (!empty($this->data)) {
                $this->validation = \Config\Services::validation();
                $this->validation->setRules($this->company->validationRules, $this->company->validationMessages);
                if (!$this->validation->run($this->data)) {
                    $errors = $this->validation->getErrors();
                    return $this->failValidationErrors($errors);
                }
    
                $data = [
                    'companyType' => $this->data['companyType'],
                    'companyName' => $this->data['companyName'],
                    'companyTel' => $this->data['companyTel'],
                ];
    
                $this->company->save($data);
                $ret = true;
            };
        }else{
            return $this->fail("잘못된 요청");
        }
        
        return $this->respond($ret);
    }

    protected function delete($id = false)
    {
        $ret = false;
        if (strtolower($this->request->getMethod()) === 'delete') {
            if ($id) {
                $ret = true;
                $this->company->delete($id);
            }
        }else{
            return $this->fail("잘못된 요청");
        }
        
        return $this->respond($ret);
    }
}
