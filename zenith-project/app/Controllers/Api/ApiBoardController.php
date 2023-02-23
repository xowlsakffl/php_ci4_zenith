<?php

namespace App\Controllers\Api;

use App\Models\Api\BoardModel;
use CodeIgniter\API\ResponseTrait;

class ApiBoardController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    public function __construct()
    {
        $this->board = model(BoardModel::class);
    }

    public function get($id = NULL)
    {
        if (strtolower($this->request->getMethod()) === 'get') {
            if ($id) {
                $data['result'] = $this->board->find($id);
            } else {
                $param = $this->request->getGet();

                $builder = $this->board;

                if(isset($param['limit'])){
                    $limit = $param['limit'];
                }else{
                    $limit = 10;
                }

                if(isset($param['search'])){
                    $searchText = $param['search'];
                    $builder = $builder->select('*')
                    ->orLike('board_title', $searchText)
                    ->orLike('board_description', $searchText);
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
            $this->validation = \Config\Services::validation();
            $this->validation->setRules($this->board->validationRules);
            if (!$this->validation->run($this->data)) {
                $errors = $this->validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $data = [
                'board_title' => $this->data['board_title'],
                'board_description' => $this->data['board_description'],
            ];
            $this->board->update($id, $data);
            $ret = true;
        };

        return $this->respond($ret);
    }

    public function post()
    {
        $ret = false;
        if (!empty($this->data)) {
            $this->validation = \Config\Services::validation();
            $this->validation->setRules($this->board->validationRules);
            if (!$this->validation->run($this->data)) {
                $errors = $this->validation->getErrors();
                return $this->failValidationErrors($errors);
            }
                $data = [
                    'board_title' => $this->data['board_title'],
                    'board_description' => $this->data['board_description'],
                ];
                $this->board->save($data);
                $ret = true;
        };

        return $this->respond($ret);
    }

    protected function delete($id = false)
    {
        $ret = false;
        if (strtolower($this->request->getMethod()) === 'delete') {
            if ($id) {
                $ret = true;
                $this->board->delete($id);
            }
        }else{
            return $this->fail("잘못된 요청");
        }
        
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
