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
                'media' => $arg['media'],
				'target' => $arg['target'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'media' => 'required|is_unique[event_media.media]',
            ];
            $validationMessages   = [
                'media' => [
                    'required' => '매체명은 필수 입력 사항입니다.',
                    'is_unique' => '이미 등록된 매체입니다.'
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->media->createMedia($data);
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
                'media' => $arg['media'],
				'target' => $arg['target'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'media' => 'required',
            ];
            $validationMessages   = [
                'media' => [
                    'required' => '매체명은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $result = $this->media->updateMedia($data, $arg['seq']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
