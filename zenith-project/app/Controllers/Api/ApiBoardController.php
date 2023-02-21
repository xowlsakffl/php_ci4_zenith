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
                $data = $this->board->find($id);
            } else {
                $getData = $this->request->getGet();
                $data = $this->board->getBoards($getData);
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
                //'file' => 'uploaded[file]|is_image[file]|max_size[file, 1024]',
                'board_title' => 'required',
                'board_description' => 'required',
            ],
            [   // Errors
                /* 'file' => [
                    'uploaded' => '업로드 에러.',
                    'is_image' => '이미지 타입 에러.',
                    'max_size' => '사이즈 에러.'
                ], */
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
                //'file' => 'uploaded[file]|is_image[file]|max_size[file, 1024]',
                'board_title' => 'required',
                'board_description' => 'required',
            ],
            [   // Errors
                /* 'file' => [
                    'uploaded' => '업로드 에러.',
                    'is_image' => '이미지 타입 에러.',
                    'max_size' => '사이즈 에러.'
                ], */
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
