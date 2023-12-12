<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\AdvertiserModel;
use App\Models\EventManage\EventModel;
use CodeIgniter\API\ResponseTrait;

class EventController extends BaseController
{
    use ResponseTrait;
    
    protected $event, $advertiser;
    public function __construct() 
    {
        $this->event = model(EventModel::class);
        $this->advertiser = model(AdvertiserModel::class);
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
			if(empty($ads)){$ads = [];}
            foreach ($result['data'] as &$data) {   
                if($data['is_stop']){
                    $data['is_stop'] = '사용중지';
                }else{
                    $data['is_stop'] = '사용중';
                }

                if($data['interlock']){
                    $data['interlock'] = 'O';
                }else{
                    $data['interlock'] = '';
                }

                if($data['lead']){
                    $lead_array = array("0" => $data['title'], "1" => "잠재고객", "2" => "엑셀업로드", "3" => "API 수신", "4" => "카카오 비즈폼");
                    $data['title'] = $lead_array[$data['lead']];
                }

                if(preg_match('/(카카오|GDN|페이스북|잠재|유튜브)/', $data['media_name'])) {
                    $is_enabledAds = false;
                    $data['config'] = 'disabled';
                    if(in_array($data['seq'], $ads)){
                        $is_enabledAds = true;
                    }
                    if($is_enabledAds) {
                        $data['config'] = 'enabled';
                    }
                }

                if(!$data['impressions']){
                    $data['impressions'] = 0;
                }

				$data['event_url'] = getenv('EVENT_SERVER_URL').$data['seq'];
                $data['db_price'] = number_format($data['db_price']);
            }

            $result = [
                'data' => $result['data'],
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

            $adData = [
                'seq' => $arg['advertiser']
            ];
            $advertiser = $this->advertiser->getAdvertiser($adData);
            if(empty($advertiser['company_seq']) || $advertiser['company_seq'] == 0){
                $error = ['advertiser' => '소속이 지정되지 않은 광고주입니다.'];
                return $this->failValidationErrors($error);
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
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
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
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete'){
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
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $seq = $this->request->getGet('seq');
            $result = $this->event->getEvent($seq);
            $result['keywords'] = explode(',', $result['keywords']);
            $result['event_url'] = getenv('EVENT_SERVER_URL').$result['seq'];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getEventImpressions()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $seq = $this->request->getGet('seq');
            $result = $this->event->getEventImpressions($seq);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function setArg($arg){
        $data = [
            'media' => $arg['media'],
            'description' => $arg['description'],
            'db_price' => (integer)$arg['db_price'] ?? 0,
            'interlock' => (integer)$arg['interlock'] ?? 0,
            'partner_id' => $arg['partner_id'],
            'partner_name' => $arg['partner_name'],
            'paper_code' => $arg['paper_code'],
            'paper_name' => $arg['paper_name'],
            'lead' => (integer)$arg['lead'],
            'creative_id' => (integer)$arg['creative_id'] ?? 0,
            'bizform_apikey' => $arg['bizform_apikey'],
            'is_stop' => (integer)$arg['is_stop'] ?? 0,
            'custom' => $arg['custom'],
            'title' => $arg['title'],
            'subtitle' => $arg['subtitle'],
            'object' => $arg['object'],
            'object_items' => $arg['object_items'],
            'pixel_id' => trim($arg['pixel_id']),
            'view_script' => $arg['view_script'],
            'done_script' => $arg['done_script'],
            'check_gender' => $arg['check_gender'],
            'check_age_min' => (integer)$arg['check_age_min'] ?? 0,
            'check_age_max' => (integer)$arg['check_age_max'] ?? 0,
            'duplicate_term' => (integer)$arg['duplicate_term'] ?? 0,
            'check_phone' => $arg['check_phone'],
            'check_name' => $arg['check_name'],
            'check_cookie' => (integer)$arg['check_cookie'] ?? 0,
            'duplicate_precheck' => (integer)$arg['duplicate_precheck'] ?? 0,
            'username' => auth()->user()->nickname,
        ];

        return $data;
    }
}
