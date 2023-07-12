<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\ChangeModel;
use CodeIgniter\API\ResponseTrait;

class ChangeController extends BaseController
{
    use ResponseTrait;
    
    protected $change;
    public function __construct() 
    {
        $this->change = model(ChangeModel::class);
    }
    
    public function index()
    {
        return view('/events/change/change');
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->change->getChanges($arg);
            $list = $result['data'];
            
            $result = [
                'data' => $list,
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($arg['draw']),
            ];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getChange()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->change->getChange($arg['id']);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createChange()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            $data = [
                'id' => $arg['id'],
				'name' => $arg['name'],
                'token' => $arg['token'],
            ];
            $data['ec_datetime'] = date('Y-m-d H:i:s');
            $validation = \Config\Services::validation();
            $validationRules      = [
                'id' => 'required|is_unique[event_conversion.id]',
                'token' => 'required'
            ];
            $validationMessages   = [
                'id' => [
                    'required' => '전환ID는 필수 입력 사항입니다.',
                    'is_unique' => '이미 등록된 아이디입니다.'
                ],
                'token' => [
                    'required' => 'Access Token은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->change->createChange($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
    
    public function updateChange()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $data = [
                'old_id' => $arg['old_id'],
                'id' => $arg['id'],
				'name' => $arg['name'],
                'token' => $arg['token'],
            ];
            $data['ec_datetime'] = date('Y-m-d H:i:s');
            $validation = \Config\Services::validation();
            $validationRules      = [
                'id' => 'required',
                'token' => 'required'
            ];
            if($data['old_id'] != $data['id']){
                $validationRules['id'] = 'required|is_unique[event_conversion.id]';
            }
            $validationMessages   = [
                'id' => [
                    'required' => '전환ID는 필수 입력 사항입니다.',
                    'is_unique' => '이미 등록된 아이디입니다.'
                ],
                'token' => [
                    'required' => 'Access Token은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->change->updateChange($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
