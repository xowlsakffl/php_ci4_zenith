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
                    $originTime = $list[$i]['term'];
                    $dateTime = new DateTime($originTime);
                    $list[$i]['term'] = $dateTime->format('Y년 m월 d일 H시 i분까지');
                }
                
                if((empty($list[$i]['term']) && $list[$i]['forever'] == 1)){
                    $list[$i]['term'] = '영구차단';
                }
                
                if(!empty($list[$i]['ip']) && empty($list[$i]['username'])){
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
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            $data = $this->request->getGet();
            $sliceSeq = explode("_", $data['seq']);
            if($sliceSeq[0] == 'ip'){
                $result = $this->blacklist->getBlacklist($sliceSeq[1]);
                $result['type'] = 'ip';
            }else{
                $result = $this->blacklist->getBlacklistPhone($sliceSeq[1]);
                $result['type'] = 'phone';
            }

            if(!empty($result['term'])){
                $originTime = $result['term'];
                $dateTime = new DateTime($originTime);
                $result['term'] = $dateTime->format('Y년 m월 d일 H시 i분까지');
            }
            
            if((empty($result['term']) && $result['forever'] == 1)){
                $result['term'] = '영구차단';
            }
            

            if(!empty($result['ip']) && empty($result['username'])){
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
            
            //유효성 검사 start
            $validation = \Config\Services::validation();
            $validationRules = [
                $arg['type'] == 'ip' ? 'ip' : 'phone' => 'required',
                'term' => 'required',
            ];
            $validationMessages = [
                $arg['type'] == 'ip' ? 'ip' : 'phone' => [
                    'required' => $arg['type'] == 'ip' ? '아이피는 필수 입력 사항입니다.' : '전화번호는 필수 입력 사항입니다.',
                ],
                'term' => [
                    'required' => '차단 기간을 선택해주세요.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($arg)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            //유효성 검사 end

            $data = [
                'username' => auth()->user()->username,
				'term' => $arg['term'],
                'memo' => $arg['memo']
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
                    $data['term'] = null;
                    $data['forever'] = 1;
                    break;
                default:
                    return false;
            }

            if ($arg['type'] == 'ip') {
                $data['ip'] = $arg['ip'];
                $checkRow = $this->blacklist->getBlackListByIp($data['ip']);
            } else {
                $data['phone'] = $arg['phone'];
                if (strpos($arg['phone'], '-') !== false) {
                    $data['phone'] = str_replace("-", "", $arg['phone']);
                } else {
                    $data['phone'] = $arg['phone'];
                }
                $checkRow = $this->blacklist->getBlackListByPhone($data['phone']);
            }
            
            if ($checkRow) {
                if (($checkRow['term'] > $data['term'] && !isset($data['forever'])) || $checkRow['forever']) {
                    if ($checkRow['forever']) {
                        $message = '영구차단';
                    } else {
                        $message = $checkRow['term'] . '까지';
                    }
            
                    return $this->failValidationErrors(['term' => '이미 존재하는 ' . ($arg['type'] == 'ip' ? 'IP' : '전화번호') . '입니다. ' . $message]);
                } else {
                    $data['seq'] = $checkRow['seq'];
                    $result = ($arg['type'] == 'ip') ? $this->blacklist->updateBlackList($data) : $this->blacklist->updateBlackListPhone($data);
                    return $this->respond($result);
                }
            }
            
            $result = ($arg['type'] == 'ip') ? $this->blacklist->createBlackList($data) : $this->blacklist->createBlackListPhone($data);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteBlackList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete'){
            $data = $this->request->getRawInput();
            $sliceSeq = explode("_", $data['seq']);
            
            if($sliceSeq[0] == 'ip'){
                $result = $this->blacklist->deleteBlackList($sliceSeq[1]);
            }else{
                $result = $this->blacklist->deleteBlackListPhone($sliceSeq[1]);
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
