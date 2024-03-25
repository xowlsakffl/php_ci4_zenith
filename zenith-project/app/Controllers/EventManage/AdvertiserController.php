<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\AdvertiserModel;
use CodeIgniter\API\ResponseTrait;

class AdvertiserController extends BaseController
{
    use ResponseTrait;
    
    protected $advertiser;
    public function __construct() 
    {
        $this->advertiser = model(AdvertiserModel::class);
    }
    
    public function index()
    {
        return view('/events/advertiser/advertiser');
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->advertiser->getAdvertisers($arg);
            $list = $result['data'];
            for ($i = 0; $i < count($list); $i++) {   
                if($list[$i]['is_stop']){
                    $list[$i]['is_stop'] = '사용중지';
                }else{
                    $list[$i]['is_stop'] = '사용중';
                }

                if($list[$i]['interlock_url']){
                    $list[$i]['interlock_url'] = 'O';
                }

                if($list[$i]['agreement_url']){
                    $list[$i]['agreement_url_exist'] = 'O';
                }

                $list[$i]['remain_balance'] = ($list[$i]['sum_price'] == ($list[$i]['remain_balance'] * -1)) ? "" : $list[$i]['remain_balance'];

                if($list[$i]['sum_price']){
                    $list[$i]['sum_price'] = number_format($list[$i]['sum_price']);
                }
                
                if($list[$i]['remain_balance']){
                    $list[$i]['remain_balance'] = number_format($list[$i]['remain_balance']);
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

    public function getAdvertiser()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $adv = $this->advertiser->getAdvertiser($arg);
            $ow = $this->advertiser->getOverwatchByAdvertiser($arg);
            $wl = $this->advertiser->getMedia($arg);
            
            $result = [
                'advertiser' => $adv,
                'ow' => $ow,
                'wl' => $wl
            ];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getCompanies()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $stx = $this->request->getGet('stx');
            $result = $this->advertiser->getCompanies($stx);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createAdv()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            $data = [
                'company_seq' => $arg['company_id'],
                'name' => $arg['name'],
				'agent' => $arg['agent'],
				'username' => auth()->user()->username,
                'homepage_url' => $arg['homepage_url'],
				'interlock_url' => $arg['interlock_url'],
				'agreement_url' => $arg['agreement_url'],
				'account_balance' => $arg['account_balance'],
				'is_stop' => $arg['is_stop']
            ];
            $data['ea_datetime'] = date('Y-m-d H:i:s');

            $validation = \Config\Services::validation();
            $validationRules      = [
                'name' => 'required|is_unique[event_advertiser.name]',
            ];
            $validationMessages   = [
                'name' => [
                    'required' => '광고주명은 필수 입력 사항입니다.',
                    'is_unique' => '이미 등록된 광고주입니다.'
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            if(empty($arg['company_id'])){
                $advertiser = $this->advertiser->getAdvertiserByName($data);
                if(empty($advertiser)){
                    $error = ['advertiser' => '광고주명에 해당하는 소속 광고주가 존재하지 않습니다.'];
                    return $this->failValidationErrors($error);
                }
            }

            $result = $this->advertiser->createAdv($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAdv()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $validation = \Config\Services::validation();
            $validationRules      = [
                'name' => 'required',
            ];

            if($arg['sms_alert']){
                $validationRules['contact.0'] = 'required';
            }

            $validationMessages   = [
                'name' => [
                    'required' => '광고주명은 필수 입력 사항입니다.',
                ],
                'contact.0' => [
                    'required' => '연락처는 필수 입력 사항입니다.'
                ],
            ];

            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($arg)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $data = [
                'name' => $arg['name'],
				'agent' => $arg['agent'],
				'username' => auth()->user()->username,
                'homepage_url' => $arg['homepage_url'],
				'interlock_url' => $arg['interlock_url'],
				'agreement_url' => $arg['agreement_url'],
				'account_balance' => $arg['account_balance'],
				'is_stop' => $arg['is_stop'],
            ];
            $data['ea_datetime'] = date('Y-m-d H:i:s');
            $result = $this->advertiser->updateAdv($data, $arg['seq']);

            // OVERWATCH
            $db = \Config\Database::connect();
            $builder = $db->table('event_overwatch');
            $ow = $this->advertiser->getOverwatchByAdvertiser($arg['seq']);
            $contact = implode(';',$arg['contact']);
            // INSERT / UPDATE / DELETE
            if($arg['sms_alert'] && !$ow){
                $overwatch = [
                    'advertiser' => $arg['seq'],
                    'contact' => $contact,
                    'watch_list' => $arg['watch_list'],
                    'username' => auth()->user()->username
                ];
                $overwatch['eo_datetime'] = date('Y-m-d H:i:s');
                $builder->insert($overwatch);
            }else if($arg['sms_alert'] && $ow){
                $builder->set('contact', $contact);
                $builder->set('watch_list', $arg['watch_list']);
                $builder->where('seq', $ow['seq']);
                $builder->update();
            }else if(!$arg['sms_alert'] && $ow){
                $builder->where('seq', $ow['seq']);
                $builder->delete();
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
