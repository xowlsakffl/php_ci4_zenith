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
                $data['pager']['total'] = $this->board->pager->getTotal();
                $data['pager']['pageCount'] = $this->board->pager->getPageCount();
                $data['pager']['currentPage'] = $this->board->pager->getCurrentPage();
                $data['pager']['firstPage'] = $this->board->pager->getFirstPage();
                $data['pager']['lastPage'] = $this->board->pager->getLastPage();
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
            $this->validation->setRules([
                'board_title' => 'required',
                'board_description' => 'required',
            ],
            [   // Errors
                'board_title' => [
                    'required' => '제목은 필수 입력사항입니다.',
                ],
                'board_description' => [
                    'required' => '본문은 필수 입력사항입니다.',
                ],
            ]);
            if($this->validation->run($this->data)){
                $data = [
                    'board_title' => $this->data['board_title'],
                    'board_description' => $this->data['board_description'],
                ];
                $this->board->update($id, $data);
                $ret = true;
            }else{
                if($this->validation->hasError('file')){
                    $error = $this->validation->getError('file');
                }else if($this->validation->hasError('board_title')){
                    $error = $this->validation->getError('board_title');
                }else if($this->validation->hasError('board_description')){
                    $error = $this->validation->getError('board_description');
                }

                return $this->failValidationErrors($error);
            }
        };

        return $this->respond($ret);
    }

    public function post()
    {
        $ret = false;
        if (!empty($this->data)) {
            $this->validation = \Config\Services::validation();
            $this->validation->setRules([
                'board_title' => 'required',
                'board_description' => 'required',
            ],
            [   // Errors
                'board_title' => [
                    'required' => '제목은 필수 입력사항입니다.',
                ],
                'board_description' => [
                    'required' => '본문은 필수 입력사항입니다.',
                ],
            ]);
            if($this->validation->run($this->data)){
                $data = [
                    'board_title' => $this->data['board_title'],
                    'board_description' => $this->data['board_description'],
                ];
                $this->board->save($data);
                $ret = true;
            }else{
                if($this->validation->hasError('file')){
                    $error = $this->validation->getError('file');
                }else if($this->validation->hasError('board_title')){
                    $error = $this->validation->getError('board_title');
                }else if($this->validation->hasError('board_description')){
                    $error = $this->validation->getError('board_description');
                }

                return $this->failValidationErrors($error);
            }
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
