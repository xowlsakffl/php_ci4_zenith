<?php

namespace App\Controllers\Api;

use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class ApiCompanyController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    public function __construct()
    {
        $this->company = model(CompanyModel::class);
    }

    public function get($id = NULL)
    {
        if (strtolower($this->request->getMethod()) === 'get') {
            if ($id) {
                $data['result'] = $this->company->find($id);
            } else {
                $param = $this->request->getGet();

                $builder = $this->company;

                if(isset($param['limit'])){
                    $limit = $param['limit'];
                }else{
                    $limit = 10;
                }

                if(isset($param['search'])){
                    $searchText = $param['search'];
                    $builder = $builder->select('*')
                    ->orLike('companyType', $searchText)
                    ->orLike('companyName', $searchText)
                    ->orLike('companyTel', $searchText);
                }

                
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
        if (!empty($this->data)) {
            $data = [
                'board_title' => $this->data['board_title'],
                'board_description' => $this->data['board_description'],
            ];
            $this->board->update($id, $data);
            $ret = true;
        };

        return $this->respond($ret);
    }

    public function post($id = NULL)
    {
        $ret = false;
        if (!empty($this->data)) {
            $this->validation = \Config\Services::validation();
            $this->validation->setRules($this->company->validationRules);
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

        return $this->respond($ret);
    }

    protected function delete($id = false)
    {
        $ret = false;

        
        return $this->respond($ret);
    }

    public function _remap(...$params) {
        $method = $this->request->getMethod();
        $params = [($params[0] !== 'get' ? $params[0] : false)];
        $this->data = $this->request->getRawInput();

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
