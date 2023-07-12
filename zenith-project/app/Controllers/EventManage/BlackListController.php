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
            for ($i = 0; $i < count($list); $i++) {   
                if($list[$i]['term']){
                    if($list[$i]['term'] != '0000-00-00 00:00:00'){
                        $originTime = $list[$i]['term'];
                        $dateTime = new DateTime($originTime);
                        $list[$i]['term'] = $dateTime->format('Y년 m월 d일 H시 i분까지');
                    }
                    
                    if($list[$i]['term'] == '0000-00-00 00:00:00' && $list[$i]['forever']){
                        $list[$i]['term'] = '영구차단';
                    }
                }

                if(empty($list[$i]['username'])){
                    $list[$i]['username'] = '시스템';
                }
            }
            
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
            $arg = $this->request->getGet();
            $result = $this->blacklist->getBlacklist($arg);

            if($result['term']){
                if($result['term'] != '0000-00-00 00:00:00'){
                    $originTime = $result['term'];
                    $dateTime = new DateTime($originTime);
                    $result['term'] = $dateTime->format('Y년 m월 d일 H시 i분까지');
                }
                
                if($result['term'] == '0000-00-00 00:00:00' && $result['forever']){
                    $result['term'] = '영구차단';
                }
            }

            if(empty($result['username'])){
                $result['username'] = '시스템';
            }
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            $data = [
                'ip' => $arg['ip'],
                'username' => auth()->user()->username,
				'term' => $arg['term'],
            ];
            $data['reg_date'] = date('Y-m-d H:i:s');
            switch($data['term']){
                case '1d':
                    $data['term'] = date('Y-m-d H:i:s', strtotime('+1 days'));
                    break;
                case '7d':
                    $data['term'] = date('Y-m-d H:i:s', strtotime('+1 week'));
                    break;
                case '14d':
                    $data['term'] = date('Y-m-d H:i:s', strtotime('+2 week'));
                    break;
                case '1m':
                    $data['term'] = date('Y-m-d H:i:s', strtotime('+1 months'));
                    break;
                case '3m':
                    $data['term'] = date('Y-m-d H:i:s', strtotime('+3 months'));
                    break;
                case 'forever':
                    $data['term'] = '0000-00-00 00:00:00';
                    $data['forever'] = 1;
                    break;
                default:
                    return false;
            }
            $checkRow = $this->blacklist->getBlackListByIp($data['ip']);

            if($checkRow){
                if(($checkRow['term'] > $data['term'] && !isset($data['forever'])) || $checkRow['forever']){
                    if($checkRow['forever']){
                        $message = '영구차단';
                    }else{
                        $message = $checkRow['term'].'까지';
                    }

                    return $this->failValidationErrors(['term'=>'이미 존재하는 ip입니다. '.$message]);
                }else{
                    $result = $this->blacklist->updateBlackList($data);
                    return $this->respond($result);
                }           
            };

            $validation = \Config\Services::validation();
            $validationRules      = [
                'ip' => 'required',
                'term' => 'required',
            ];
            $validationMessages   = [
                'ip' => [
                    'required' => '아이피는 필수 입력 사항입니다.',
                ],
                'term' => [
                    'required' => '차단 기간을 선택해주세요.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
    
            $result = $this->blacklist->createBlackList($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete'){
            $arg = $this->request->getRawInput();

            $result = $this->blacklist->deleteBlackList($arg['seq']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
