<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\EventModel;
use CodeIgniter\API\ResponseTrait;

class EventController extends BaseController
{
    use ResponseTrait;
    
    protected $event;
    public function __construct() 
    {
        $this->event = model(EventModel::class);
    }
    
    public function index()
    {
        return view('events/event/event');
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->event->getInformation($arg);
            $ads = $this->event->getEnabledAds();
            $issues = $this->event->getIssuesFromMantis();
            $list = $result['data'];
            for ($i = 0; $i < count($list); $i++) {   
                if($list[$i]['is_stop']){
                    $list[$i]['is_stop'] = '사용중지';
                }else{
                    $list[$i]['is_stop'] = '사용중';
                }

                if($list[$i]['interlock']){
                    $list[$i]['interlock'] = 'O';
                }else{
                    $list[$i]['interlock'] = 'X';
                }

                if($list[$i]['lead']){
                    $lead_array = array("0" => $list[$i]['title'], "1" => "잠재고객", "2" => "엑셀업로드", "3" => "API 수신", "4" => "카카오 비즈폼");
                    $list[$i]['title'] = $lead_array[$list[$i]['lead']];
                }

                $list[$i]['mantis'] = [];
                if ($issues[$list[$i]['seq']]['id']) {
                    $list[$i]['mantis']['id'] = $issues[$list[$i]['seq']]['id'];
                    if ($issues[$list[$i]['seq']]['designer'])
                        $list[$i]['mantis']['designer'] = $issues[$list[$i]['seq']]['designer'];
                    if ($issues[$list[$i]['seq']]['developer'])
                        $list[$i]['mantis']['developer'] = $issues[$list[$i]['seq']]['developer'];
                }

                if(preg_match('/(카카오|GDN|페이스북|잠재|유튜브)/', $list[$i]['media_name'])) {
                    $is_enabledAds = false;
                    $list[$i]['config'] = 'disabled';
                    if(in_array($list[$i]['seq'], $ads)){
                        $is_enabledAds = true;
                    }
                    if($is_enabledAds) {
                        $list[$i]['config'] = 'enabled';
                    }
                }

                $list[$i]['db_price'] = number_format($list[$i]['db_price']);
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

    public function getAdv()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->event->getAdv($arg['stx']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getMedia()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->event->getMedia($arg['stx']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createEvent()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            $data = $this->setArg($arg);
            $data['advertiser'] = $arg['advertiser'];
            $data['keyword'] = $arg['keyword'];
            $data['ei_datetime'] = date('Y-m-d H:i:s');
            $validation = \Config\Services::validation();
            $validationRules      = [
                'advertiser' => 'required',
                'media' => 'required',
            ];
            $validationMessages   = [
                'advertiser' => [
                    'required' => '광고주가 입력되지 않았습니다.',
                ],
                'media' => [
                    'required' => '매체가 입력되지 않았습니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $result = $this->event->createEvent($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateEvent()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $data = $this->setArg($arg);
            $data['keyword'] = $arg['keyword'];
            $data['ei_updatetime'] = date('Y-m-d H:i:s');

            $validation = \Config\Services::validation();
            $validationRules      = [
                'media' => 'required',
            ];
            $validationMessages   = [
                'media' => [
                    'required' => '매체가 입력되지 않았습니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $result = $this->event->updateEvent($data, $arg['seq']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function copyEvent()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $data = $this->request->getRawInput();
            if(empty($data['seq'])){
                return $this->fail("잘못된 요청");
            }
            $result = $this->event->copyEvent($data['seq']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteEvent()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'delete'){
            $data = $this->request->getRawInput();
            if(empty($data['seq'])){
                return $this->fail("잘못된 요청");
            }
            $url = "https://event.hotblood.co.kr/filecheck/".$data['seq'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) {
                $result = $this->event->deleteEvent($data['seq']);
                return $this->respond($result);
            } else {
                $result = $this->event->deleteEvent($data['seq']);
                return $this->respond($result);
                return $this->fail("삭제할 수 없는 이벤트입니다.");
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getEvent()
    {
        if(/* $this->request->isAJAX() && */ strtolower($this->request->getMethod()) === 'get'){
            $seq = $this->request->getGet('seq');
            $result = $this->event->getEvent($seq);
            $result['keywords'] = explode(',', $result['keywords']);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function setArg($arg){
        $data = [
            'media' => $arg['media'],
            'description' => $arg['description'],
            'db_price' => $arg['db_price'],
            'interlock' => $arg['interlock'],
            'partner_id' => $arg['partner_id'],
            'partner_name' => $arg['partner_name'],
            'paper_code' => $arg['paper_code'],
            'paper_name' => $arg['paper_name'],
            'lead' => $arg['lead'],
            'creative_id' => $arg['creative_id'],
            'bizform_apikey' => $arg['bizform_apikey'],
            'custom' => $arg['custom'],
            'title' => $arg['title'],
            'subtitle' => $arg['subtitle'],
            'object' => $arg['object'],
            'object_items' => $arg['object_items'],
            'pixel_id' => trim($arg['pixel_id']),
            'view_script' => $arg['view_script'],
            'done_script' => $arg['done_script'],
            'check_gender' => $arg['check_gender'],
            'check_age_min' => $arg['check_age_min'],
            'check_age_max' => $arg['check_age_max'],
            'duplicate_term' => $arg['duplicate_term'],
            'check_phone' => $arg['check_phone'],
            'check_name' => $arg['check_name'],
            'check_cookie' => $arg['check_cookie'],
            'duplicate_precheck' => $arg['duplicate_precheck'],
            'username' => auth()->user()->username,
        ];

        return $data;
    }
}
