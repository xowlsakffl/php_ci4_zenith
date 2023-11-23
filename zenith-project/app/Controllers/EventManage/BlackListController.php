<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\BlackListModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class BlackListController extends BaseController
{
    use ResponseTrait;
    
    protected $blacklist;
    public function __construct() 
    {
        $this->blacklist = model(BlackListModel::class);
    }
    
    public function index()
    {
        return view('/events/blacklist/blacklist');
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->blacklist->getBlackLists($arg);
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

    public function getBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $data = $this->request->getGet();
            $result = $this->blacklist->getBlacklist($data['seq']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            
            //유효성 검사 start
            $validation = \Config\Services::validation();
            $validationRules = [
                'data' => 'required|is_unique[event_blacklist.data]',
            ];
            $validationMessages = [
                'data' => [
                    'required' => '전화번호/아이피를 입력해주세요.',
                    'is_unique' => '이미 존재하는 전화번호/아이피입니다.'
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($arg)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            //유효성 검사 end

            $data = [
                'data' => $arg['data'],
                'memo' => $arg['memo'],
            ];
            
            $result = $this->blacklist->createBlackList($data);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete'){
            $data = $this->request->getRawInput();
            $result = $this->blacklist->deleteBlackList($data['seq']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
