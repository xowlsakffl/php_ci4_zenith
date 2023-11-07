<?php

namespace App\Controllers\AdvertisementManager\Automation;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvGoogleManagerModel;
use App\Models\Advertiser\AutomationModel;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\moment_api\ZenithKM;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use DateTime;
use Exception;

class AutomationController extends BaseController
{
    use ResponseTrait;
    
    protected $automation, $google;

    public function __construct() 
    {
        $this->automation = model(AutomationModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
    }

    public function index()
    {
        return view('automation/automation');
    }
    
    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->automation->getAutomationList($arg);
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

    public function getAutomation()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->automation->getAutomation($arg);
            if(!empty($result['aat_id'])){
                if($result['aat_status'] == 1 || $result['aat_status'] == 'ON' || $result['aat_status'] == 'ENABLED' || $result['aat_status'] == 'ACTIVE'){
                    $result['aat_status'] = '활성';
                }else{
                    $result['aat_status'] = '비활성';
                }
    
                switch ($result['aat_type']) {
                    case 'advertiser':
                        $result['aat_type'] = '광고주';
                        break;
                    case 'campaign':
                        $result['aat_type'] = '캠페인';
                        break;
                    case 'adgroup':
                        $result['aat_type'] = '광고그룹';
                        break;
                    case 'ad':
                        $result['aat_type'] = '광고';
                        break;
                    default:
                        break;
                }
    
                switch ($result['aat_media']) {
                    case 'company':
                        $result['aat_media'] = '광고주';
                        break;
                    case 'facebook':
                        $result['aat_media'] = '페이스북';
                        break;
                    case 'google':
                        $result['aat_media'] = '구글';
                        break;
                    case 'kakao':
                        $result['aat_media'] = '카카오';
                        break;
                    default:
                        break;
                }
            }
            
            foreach ($result['executions'] as &$execution) {
                switch ($execution['media']) {
                    case 'facebook':
                        $execution['media'] = '페이스북';
                        break;
                    case 'google':
                        $execution['media'] = '구글';
                        break;
                    case 'kakao':
                        $execution['media'] = '카카오';
                        break;
                    default:
                        break;
                }

                switch ($execution['type']) {
                    case 'campaign':
                        $execution['type'] = '캠페인';
                        break;
                    case 'adgroup':
                        $execution['type'] = '광고그룹';
                        break;
                    case 'ad':
                        $execution['type'] = '광고';
                        break;
                    default:
                        break;
                }

                if($execution['status'] == 1 || $execution['status'] == 'ON' || $execution['status'] == 'ENABLED' || $execution['status'] == 'ACTIVE'){
                    $execution['status'] = '활성';
                }else{
                    $execution['status'] = '비활성';
                }

                if($execution['exec_type'] == 'status'){
                    $execution['exec_type'] = '상태';
                }else if($execution['exec_type'] == 'budget'){
                    $execution['exec_type'] = '예산';
                }
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setStatus()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $data = [
                'seq' => $arg['seq'],
				'status' => $arg['status'],
            ];
            $result = $this->automation->setAutomationStatus($data);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getAdv()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();

            switch ($arg['tab']) {
                case 'advertiser':
                    $result = $this->automation->getSearchCompanies($arg, null);                
                    break;
                case 'campaign':
                    $result = $this->automation->getSearchCampaigns($arg, null);
                    break;
                case 'adgroup':
                    $result = $this->automation->getSearchAdsets($arg, null);
                    break;
                case 'ad':
                    $result = $this->automation->getSearchAds($arg, null);
                    break;
                default:
                    return $this->fail("잘못된 요청");
                    break;
            }
            
            foreach($result['data'] as &$row){
                if($row['status'] == 1 || $row['status'] == 'ON' || $row['status'] == 'ENABLED' || $row['status'] == 'ACTIVE'){
                    $row['status'] = '활성';
                }else{
                    $row['status'] = '비활성';
                }
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

    public function createAutomation()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $data = $this->request->getRawInput();
            $validationResult = $this->validationData($data);
            if($validationResult['result'] != true){
                return $this->failValidationErrors($validationResult);
            }

            if(!empty($data['target'])){
                switch ($data['target']['media']) {
                    case '광고주':
                        $data['target']['media'] = 'company';
                        break;
                    case '페이스북':
                        $data['target']['media'] = 'facebook';
                        break;
                    case '구글':
                        $data['target']['media'] = 'google';
                        break;
                    case '카카오':
                        $data['target']['media'] = 'kakao';
                        break;
                    default:
                        break;
                }

                switch ($data['target']['type']) {
                    case '광고주':
                        $data['target']['type'] = 'advertiser';
                        break;
                    case '캠페인':
                        $data['target']['type'] = 'campaign';
                        break;
                    case '광고그룹':
                        $data['target']['type'] = 'adgroup';
                        break;
                    case '광고':
                        $data['target']['type'] = 'ad';
                        break;
                    default:
                        break;
                }
            }

            if(!empty($data['execution'])){
                foreach ($data['execution'] as &$execution) {
                    switch ($execution['media']) {
                        case '광고주':
                            $execution['media'] = 'company';
                            break;
                        case '페이스북':
                            $execution['media'] = 'facebook';
                            break;
                        case '구글':
                            $execution['media'] = 'google';
                            break;
                        case '카카오':
                            $execution['media'] = 'kakao';
                            break;
                        default:
                            break;
                    }
    
                    switch ($execution['type']) {
                        case '광고주':
                            $execution['type'] = 'advertiser';
                            break;
                        case '캠페인':
                            $execution['type'] = 'campaign';
                            break;
                        case '광고그룹':
                            $execution['type'] = 'adgroup';
                            break;
                        case '광고':
                            $execution['type'] = 'ad';
                            break;
                        default:
                            break;
                    }

                    switch ($execution['exec_type']) {
                        case '상태':
                            $execution['exec_type'] = 'status';
                            break;
                        case '예산':
                            $execution['exec_type'] = 'budget';
                            break;
                        default:
                            break;
                    }
                }
                
            }
            
            $result = $this->automation->createAutomation($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function copyAutomation()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $data = $this->request->getRawInput();
            $result = $this->automation->copyAutomation($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomation()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $data = $this->request->getRawInput();
            if(empty($data['seq'])){
                return $this->fail("잘못된 요청");
            }
            $validationResult = $this->validationData($data);
            if($validationResult['result'] != true){
                return $this->failValidationErrors($validationResult);
            }
            
            if(!empty($data['target'])){
                switch ($data['target']['media']) {
                    case '광고주':
                        $data['target']['media'] = 'company';
                        break;
                    case '페이스북':
                        $data['target']['media'] = 'facebook';
                        break;
                    case '구글':
                        $data['target']['media'] = 'google';
                        break;
                    case '카카오':
                        $data['target']['media'] = 'kakao';
                        break;
                    default:
                        break;
                }

                switch ($data['target']['type']) {
                    case '광고주':
                        $data['target']['type'] = 'advertiser';
                        break;
                    case '캠페인':
                        $data['target']['type'] = 'campaign';
                        break;
                    case '광고그룹':
                        $data['target']['type'] = 'adgroup';
                        break;
                    case '광고':
                        $data['target']['type'] = 'ad';
                        break;
                    default:
                        break;
                }
            }

            if(!empty($data['execution'])){
                foreach ($data['execution'] as &$execution) {
                    switch ($execution['media']) {
                        case '광고주':
                            $execution['media'] = 'company';
                            break;
                        case '페이스북':
                            $execution['media'] = 'facebook';
                            break;
                        case '구글':
                            $execution['media'] = 'google';
                            break;
                        case '카카오':
                            $execution['media'] = 'kakao';
                            break;
                        default:
                            break;
                    }
    
                    switch ($execution['type']) {
                        case '광고주':
                            $execution['type'] = 'advertiser';
                            break;
                        case '캠페인':
                            $execution['type'] = 'campaign';
                            break;
                        case '광고그룹':
                            $execution['type'] = 'adgroup';
                            break;
                        case '광고':
                            $execution['type'] = 'ad';
                            break;
                        default:
                            break;
                    }

                    switch ($execution['exec_type']) {
                        case '상태':
                            $execution['exec_type'] = 'status';
                            break;
                        case '예산':
                            $execution['exec_type'] = 'budget';
                            break;
                        default:
                            break;
                    }
                }
                
            }

            $result = $this->automation->updateAutomation($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteAutomation()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete'){
            $data = $this->request->getRawInput();
            
            $result = $this->automation->deleteAutomation($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function validationData($data)
    {
        $validation = \Config\Services::validation();
        $validationRules      = [
            'schedule.type_value' => 'required',
        ];
        $validationMessages   = [
            'schedule.type_value' => [
                'required' => '시간 조건값은 필수 항목입니다.',
            ],
        ];

        if($data['schedule']['exec_type'] == 'day' || $data['schedule']['exec_type'] == 'week'){
            $validationRules['schedule.exec_time'] = 'required';
            $validationMessages['schedule.exec_time'] = ['required' => '시간은 필수 항목입니다.'];
        }
    
        if($data['schedule']['exec_type'] == 'week'){
            $validationRules['schedule.exec_week'] = ['required'];
            $validationMessages['schedule.exec_week'] = ['required' => "요일은 필수 항목입니다."];
        }

        if($data['schedule']['exec_type'] == 'month'){
            $validationRules['schedule.month_type'] = ['required'];
            $validationMessages['schedule.month_type'] = ['required' => "월 조건값은 필수 항목입니다."];

            if(isset($data['schedule']['month_type'])){
                if($data['schedule']['month_type'] == 'start_day' || $data['schedule']['month_type'] == 'end_day'){
                    $validationRules['schedule.exec_time'] = 'required';
                    $validationMessages['schedule.exec_time'] = ['required' => '시간은 필수 항목입니다.'];
                }
    
                if($data['schedule']['month_type'] == 'first' || $data['schedule']['month_type'] == 'last'){
                    $validationRules['schedule.month_week'] = 'required';
                    $validationMessages['schedule.month_week'] = ['required' => '월 조건 요일은 필수 항목입니다.'];
                }
    
                if($data['schedule']['month_type'] == 'day'){
                    $validationRules['schedule.month_day'] = 'required';
                    $validationMessages['schedule.month_day'] = ['required' => '월 조건 일자는 필수 항목입니다.'];
                }
            }
        }

        $validation->setRules($validationRules, $validationMessages);
        if (!$validation->run($data)) {
            $result = [
                'result' => false,
                'msg' => $validation->getErrors(),
            ];
            return $result;
        }

        $validation->reset();

        if(!empty($data['target'])){
            foreach ($data['condition'] as $condition) {
                $validationRules      = [
                    'order' => 'required',
                    'type' => 'required',
                    'type_value' => 'required',
                    'compare' => 'required',
                    'operation' => 'required',
                ];
                $validationMessages   = [
                    'order' => ['required' => '순서는 필수 항목입니다.'],
                    'type' => ['required' => '조건 항목은 필수 항목입니다.'],
                    'type_value' => ['required' => '조건 값은 필수 항목입니다.'],
                    'compare' => ['required' => '일치여부는 필수 항목입니다.'],
                    'operation' => ['required' => '연산조건은 필수 항목입니다.'],
                ];

                $validation->setRules($validationRules, $validationMessages);
                if (!$validation->run($condition)) {
                    $result = [
                        'result' => false,
                        'msg' => $validation->getErrors(),
                    ];
                    return $result;
                }
            }
            $validation->reset();
        }

        if(empty($data['execution'])){
            $validationRules['execution'] = 'required';
            $validationMessages['execution'] = ['required' => '실행항목을 추가해주세요.'];
        }else{
            foreach ($data['execution'] as $execution) {
                $validationRules      = [
                    'exec_type' => 'required',
                    'exec_value' => 'required',
                ];
                $validationMessages   = [
                    'exec_type' => ['required' => '실행항목은 필수 항목입니다.'],
                    'exec_value' => ['required' => '세부항목은 필수 항목입니다.'],
                ];
    
                $validation->setRules($validationRules, $validationMessages);
                if (!$validation->run($execution)) {
                    $result = [
                        'result' => false,
                        'msg' => $validation->getErrors(),
                    ];
                    return $result;
                }
            }
        }

        $validation->reset();

        $validationRules      = ['detail.subject' => 'required'];
        $validationMessages   = ['detail.subject' => ['required' => '제목은 필수 항목입니다.']];
        $validation->setRules($validationRules, $validationMessages);
        if (!$validation->run($data)) {
            $result = [
                'result' => false,
                'msg' => $validation->getErrors(),
            ];
            return $result;
        }

        return ['result' => true];
    }
    
    public function automation()
    {
        $automations = $this->automation->getAutomations();
        foreach ($automations as $automation) {
            $result = [];
            $schedulePassData = $this->checkAutomationSchedule($automation);
            $result['schedule'] = $schedulePassData;
            if($schedulePassData['result'] == false){
                $logIdx = $this->recordResult($schedulePassData);
                $this->recordLog($result, $logIdx);
                continue;
            }else{
                $seq = $schedulePassData['seq'];
                //대상 있을 시
                if(!empty($automation['aat_idx'])){
                    $targetData = $this->getAutomationTarget($seq);
                    $result['target'] = $targetData;
                    if($targetData['result'] == false){
                        $logIdx = $this->recordResult($targetData);
                        $this->recordLog($result, $logIdx);
                        continue;
                    }
                    
                    if($targetData['result'] == true && !empty($targetData['target'])){
                        $conditionPassData = $this->checkAutomationCondition($targetData);
                        $result['conditions'] = $conditionPassData;
                        if($conditionPassData['result'] == false){
                            $logIdx = $this->recordResult($conditionPassData);
                            $this->recordLog($result, $logIdx);
                            continue;
                        }
                        $seq = $conditionPassData['seq'];
                    }
                }
                $executionData = $this->automationExecution($seq);
                $logIdx = $this->recordResult($executionData['result']);
                $result['executions'] = $executionData['log'];
                $this->recordLog($result, $logIdx);
            }
        }
    }

    public function checkAutomationSchedule($automation)
    {
        $resultArray = [];
        $lastExecTime = Time::parse($automation['aar_exec_timestamp'] ?? $automation['aas_reg_datetime']);

        $ignoreStartTime = $automation['aas_ignore_start_time'] ?? null;
        $ignoreEndTime = $automation['aas_ignore_end_time'] ?? null;
        
        $currentDate = new Time('now');
        $currentTime = $currentDate->format('H:i');
        
        //제외시간
        if(!is_null($ignoreStartTime) && !is_null($ignoreEndTime)){
            $ignoreStartTime = Time::parse($ignoreStartTime);
            $ignoreEndTime = Time::parse($ignoreEndTime);
            if ($currentDate->isAfter($ignoreStartTime) && $currentDate->isBefore($ignoreEndTime)) {
                $resultArray = [
                    'result' => false,
                    'status' => 'not_execution',
                    'msg' => '제외시간',
                    'seq' => $automation['aa_seq'],
                ];
                return $resultArray;
            }
        }

        //매n시간 매n분
        if($automation['aas_exec_type'] === 'hour' || $automation['aas_exec_type'] === 'minute'){
            $diffTime = $lastExecTime->difference($currentDate);
            if($automation['aas_exec_type'] === 'hour'){
                $diffTime = $diffTime->getHours();                  
            }else{
                $diffTime = $diffTime->getMinutes();
            }
            if($diffTime >= $automation['aas_type_value']){
                $resultArray = [
                    'result' => true,
                    'status' => 'success',
                    'msg' => '설정 시간 일치',
                    'seq' => $automation['aa_seq'],
                ];
                return $resultArray;
            }
        }

        //매n일
        if($automation['aas_exec_type'] === 'day'){
            $diffTime = $lastExecTime->difference($currentDate);
            $diffTime = $diffTime->getDays();
            if($diffTime >= $automation['aas_type_value'] && $currentTime === $automation['aas_exec_time']){
                $resultArray = [
                    'result' => true,
                    'status' => 'success',
                    'msg' => '설정 시간 일치',
                    'seq' => $automation['aa_seq'],
                ];
                return $resultArray;
            }
        }

        //매n주
        if($automation['aas_exec_type'] === 'week'){
            $diffTime = $lastExecTime->difference($currentDate);
            $diffTime = $diffTime->getWeeks();
            $currentDoW = $currentDate->dayOfWeek;
            if($diffTime >= $automation['aas_type_value'] && $currentDoW === $automation['aas_exec_week'] && $currentTime === $automation['aas_exec_time']){
                $resultArray = [
                    'result' => true,
                    'status' => 'success',
                    'msg' => '설정 시간 일치',
                    'seq' => $automation['aa_seq'],
                ];
                return $resultArray;
            }
        }

        //매n월
        if($automation['aas_exec_type'] === 'month'){
            $diffTime = $lastExecTime->difference($currentDate);
            $diffTime = $diffTime->getMonths();
            $currentDoW = $currentDate->dayOfWeek;
            $currentDay = $currentDate->getDay();
            if($automation['aas_month_type'] === 'start_day'){
                if($diffTime >= $automation['aas_type_value'] && $currentDay === '1' && $currentTime === $automation['aas_exec_time']){
                    $resultArray = [
                        'result' => true,
                        'status' => 'success',
                        'msg' => '설정 시간 일치',
                        'seq' => $automation['aa_seq'],
                    ];
                    return $resultArray;
                }
            }else if($automation['aas_month_type'] === 'end_day'){
                $currentMonthLastDay = $currentDate->format('t');   
                if($diffTime >= $automation['aas_type_value'] && $currentDay === $currentMonthLastDay && $currentTime === $automation['aas_exec_time']){
                    $resultArray = [
                        'result' => true,
                        'status' => 'success',
                        'msg' => '설정 시간 일치',
                        'seq' => $automation['aa_seq'],
                    ];
                    return $resultArray;
                }
            }else if($automation['aas_month_type'] === 'first'){
                $firstDayMonth = $currentDate->setDay(1);
                while ($firstDayMonth->dayOfWeek != $automation['aas_month_week']) {
                    $firstDayMonth = $firstDayMonth->addDays(1);
                }
                if($diffTime >= $automation['aas_type_value'] && $firstDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                    $resultArray = [
                        'result' => true,
                        'status' => 'success',
                        'msg' => '설정 시간 일치',
                        'seq' => $automation['aa_seq'],
                    ];
                    return $resultArray;
                }
            }else if($automation['aas_month_type'] === 'last'){
                $lastDayMonth = $currentDate->setDay($currentDate->format('t'));
                while ($lastDayMonth->dayOfWeek != $automation['aas_month_week']) {
                    $lastDayMonth = $lastDayMonth->subDays(1);
                }
                if($diffTime >= $automation['aas_type_value'] && $lastDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                    $resultArray = [
                        'result' => true,
                        'status' => 'success',
                        'msg' => '설정 시간 일치',
                        'seq' => $automation['aa_seq'],
                    ];
                    return $resultArray;
                }
            }else if($automation['aas_month_type'] === 'day'){
                if($diffTime >= $automation['aas_type_value'] && $currentDay === $automation['aas_month_day'] && $currentTime === $automation['aas_exec_time']){
                    $resultArray = [
                        'result' => true,
                        'status' => 'success',
                        'msg' => '설정 시간 일치',
                        'seq' => $automation['aa_seq'],
                    ];
                    return $resultArray;
                }
            }
        }

        $resultArray = [
            'result' => false,
            'status' => 'not_execution',
            'msg' => '설정 시간 일치하지 않음',
            'seq' => $automation['aa_seq'],
        ];
        return $resultArray;
    }

    public function getAutomationTarget($seq)
    {
        $target = $this->automation->getTarget($seq);
        $types = ['advertiser', 'account', 'campaign', 'adgroup', 'ad'];
        $mediaTypes = ['company', 'facebook', 'google', 'kakao'];
        //값 별로 메소드 매칭
        if (in_array($target['aat_type'], $types) && in_array($target['aat_media'], $mediaTypes)) {
            $methodName = "getTarget" . ucfirst($target['aat_media']);
            if (method_exists($this->automation, $methodName)) {
                $data = $this->automation->$methodName($target);
                $data = $this->setData($data);
                return  [
                    'result' => true,
                    'msg' => '대상 일치',
                    'seq' => $seq,
                    'target' => $data,
                ];
            }
        }else{
            return [
                'result' => false,
                'status' => 'failed',
                'msg' => '대상 메소드 매칭 오류',
                'seq' => $seq,
            ];
        }
    }

    public function checkAutomationCondition($target)
    {
        $types = ['budget', 'dbcost', 'dbcount', 'cost', 'margin', 'margin_rate', 'sale', 'impression', 'click', 'cpc', 'ctr', 'conversion'];
        $conditions = $this->automation->getAutomationConditionBySeq($target['seq']);
        if(empty($conditions)){
            return [            
                "result" => false,
                'status' => 'failed',
                "msg" => '조건이 존재하지 않음',
                "seq" => $target['seq'],
            ];
        }
        //순서 재정렬
        usort($conditions, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        $isTargetMatched = false;
        $allConditionsMatched = true;
        $operation = $conditions[0]['operation'];
        foreach ($conditions as $condition) {       
            $conditionMatched = false;       
            $message = "일치하는 조건이 없습니다.";
            if ($condition['type'] === 'status') {//status 비교
                if ($target['target']['status'] == $condition['type_value']) {
                    $conditionMatched = true;
                    $message = 'status 일치';
                }else{
                    $message = 'status가 일치하지 않습니다.';
                }
            }else{//그 외 필드 비교
                foreach ($types as $type) {                     
                    if ($condition['type'] === $type) {
                        switch ($condition['compare']) {
                            case 'less':
                                $conditionMatched = $target['target'][$type] < $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값보다 큽니다.'."(".$condition['compare'].")";
                                break;
                            case 'greater':
                                $conditionMatched = $target['target'][$type] > $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값보다 작습니다.'."(".$condition['compare'].")";
                                break;
                            case 'less_equal':
                                $conditionMatched = $target['target'][$type] <= $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값보다 크거나 같지 않습니다.'."(".$condition['compare'].")";
                                break;
                            case 'greater_equal':
                                $conditionMatched = $target['target'][$type] >= $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값보다 작거나 같지 않습니다.'."(".$condition['compare'].")";
                                break;
                            case 'equal':
                                $conditionMatched = $target['target'][$type] == $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값과 일치하지 않습니다.'."(".$condition['compare'].")";
                                break;
                            case 'not_equal':
                                $conditionMatched = $target['target'][$type] != $condition['type_value'];
                                $message = $conditionMatched ? $type." ".$condition['compare'].' 조건 일치' : $type.'값이 조건값과 같습니다.'."(".$condition['compare'].")";
                                break;
                            default:
                                $conditionMatched = false;
                                break;
                        }
                    }
                }
            }

            if($operation == 'or'){
                if($conditionMatched){
                    $isTargetMatched = true;
                    break;
                }
            }

            if($operation == 'and'){
                if (!$conditionMatched) {
                    $allConditionsMatched = false;
                    break;
                }
            }
        }
        
        if(($operation == 'or' && $isTargetMatched) || ($operation == 'and' && $allConditionsMatched)){
            return [            
                "result" => true,
                'status' => 'success',
                "msg" => $message,
                "seq" => $target['seq'],
            ];
        }else{
            return [
                "result" => false,
                'status' => 'not_execution',
                "msg" => $message,
                "seq" => $target['seq'],
            ];
        }
    }
    
    public function automationExecution($seq)
    {
        $executions = $this->automation->getExecution($seq);
        if(empty($executions)){
            return [            
                "result" => false,
                'status' => 'failed',
                "msg" => '실행항목이 존재하지 않음',
                "seq" => $seq,
            ];
        }
        //순서 재정렬
        /* usort($executions, function($a, $b) {
            return $a['aae_order'] - $b['aae_order'];
        }); */
        $originalSettings = [];
        $result = [
            'result' => [],
            'log' => [],
        ];
        try {
            foreach ($executions as $execution) {
                $zenith = null;
                switch ($execution['media']) {
                    case 'facebook':
                        $zenith = new ZenithFB();
                        break;
                    case 'google':
                        $zenith = new ZenithGG();
                        break;
                    case 'kakao':
                        $zenith = new ZenithKM();
                        break;
                }
                if($zenith){ 
                    switch ($execution['type']) {
                        case 'campaign':
                            $customerId = null;
                            if ($execution['media'] === 'google') {
                                $customerId = $this->google->getCustomerById($execution['id'], 'campaign');
                            }
                            switch ($execution['exec_type']) {
                                case 'status':
                                    if ($execution['media'] === 'facebook') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = 'ACTIVE';
                                        }else{
                                            $status = 'PAUSED';
                                        }
                                        $originalData = $zenith->getCampaignStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setCampaignStatus($execution['id'], $status);
                                        }                                  
                                    } else if ($execution['media'] === 'google') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = ['status' => 'ENABLED'];
                                        }else{
                                            $status = ['status' => 'PAUSED'];
                                        }
                                        $originalData = $zenith->getCampaignStatusBudget($customerId['customerId'], $execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->updateCampaign($customerId['customerId'], $execution['id'], $status);
                                        }else{
                                            $return = false;
                                        }
                                    } else if ($execution['media'] === 'kakao') {
                                        $originalData = $zenith->getCampaignStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setCampaignOnOff($execution['id'], $execution['exec_value']);
                                        }else{
                                            $return = false;
                                        }
                                    }
                                    break;
                                case 'budget':     
                                    if ($execution['media'] === 'facebook') {
                                        $originalData = $zenith->getCampaignStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'budget' => $originalData['budget'],
                                            ];
                                            $data = [
                                                'id' => $execution['id'],
                                                'budget' => $execution['exec_value']
                                            ];
                                            $return = $zenith->updateCampaignBudget($data);
                                        }else{
                                            $return = false;
                                        }  
                                    } else if ($execution['media'] === 'google') {
                                        $originalData = $zenith->getCampaignStatusBudget($customerId['customerId'], $execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'budget' => $originalData['budget'],
                                            ];
                                            $return = $zenith->updateCampaignBudget($customerId['customerId'], $execution['id'], ['budget' => $execution['exec_value']]);
                                        }else{
                                            $return = false;
                                        }
                                    } else if ($execution['media'] === 'kakao') {
                                        $originalData = $zenith->getCampaignStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $data = [
                                                'type' => 'campaign',
                                                'id' => $execution['id'],
                                                'budget' => $execution['exec_value']
                                            ];
                                            $return = $zenith->setDailyBudgetAmount($data);
                                        }else{
                                            $return = false;
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case 'adgroup':
                            $customerId = null;
                            if ($execution['media'] === 'google') {
                                $customerId = $this->google->getCustomerById($execution['id'], 'adgroup');
                            }
                            switch ($execution['exec_type']) {
                                case 'status':
                                    if ($execution['media'] === 'facebook') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = 'ACTIVE';
                                        }else{
                                            $status = 'PAUSED';
                                        }
                                        $originalData = $zenith->getAdsetStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setAdsetStatus($execution['id'], $status);
                                        }else{
                                            $return = false;
                                        }  
                                    } else if ($execution['media'] === 'google') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = ['status' => 'ENABLED'];
                                        }else{
                                            $status = ['status' => 'PAUSED'];
                                        }
                                        $originalData = $zenith->getAdgroupStatus($customerId['customerId'], $execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->updateAdGroup($customerId['customerId'], $execution['id'], $status);
                                        }else{
                                            $return = false;
                                        }
                                    } else if ($execution['media'] === 'kakao') {
                                        $originalData = $zenith->getAdgroupStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setAdGroupOnOff($execution['id'], $execution['exec_value']);
                                        }else{
                                            $return = false;
                                        }
                                    }
                                    break;
                                case 'budget':
                                    if ($execution['media'] === 'facebook') {
                                        $originalData = $zenith->getAdsetStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'budget' => $originalData['budget'],
                                            ];
                                            $data = [
                                                'id' => $execution['id'],
                                                'budget' => $execution['exec_value']
                                            ];
                                            $return = $zenith->updateAdSetBudget($data);
                                        }else{
                                            $return = false;
                                        }  
                                    } else if ($execution['media'] === 'kakao') {
                                        $originalData = $zenith->getAdgroupStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'budget' => $originalData['budget'],
                                            ];
                                            $data = [
                                                'type' => 'adgroup',
                                                'id' => $execution['id'],
                                                'budget' => $execution['exec_value']
                                            ];
                                            $return = $zenith->setDailyBudgetAmount($data);
                                        }else{
                                            $return = false;
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case 'ad':
                            $customerId = null;                               
                            if ($execution['media'] === 'google') {
                                $customerId = $this->google->getCustomerById($execution['id'], 'ad');
                            }
                            switch ($execution['exec_type']) {
                                case 'status':
                                    if ($execution['media'] === 'facebook') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = 'ACTIVE';
                                        }else{
                                            $status = 'PAUSED';
                                        }
                                        $originalData = $zenith->getAdStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setAdStatus($execution['id'], $status);
                                        }else{
                                            $return = false;
                                        } 
                                    } else if ($execution['media'] === 'google') {
                                        if($execution['exec_value'] === "ON"){
                                            $status = ['status' => 'ENABLED'];
                                        }else{
                                            $status = ['status' => 'PAUSED'];
                                        }                                     
                                        $originalData = $zenith->getAdStatus($customerId['customerId'], $execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->updateAdGroupAd($customerId['customerId'], null, $execution['id'], $status);
                                        }else{
                                            $return = false;
                                        }
                                    } else if ($execution['media'] === 'kakao') {
                                        $originalData = $zenith->getAdStatusBudget($execution['id']);
                                        if(!empty($originalData)){
                                            $originalSettings[] = [
                                                'media' => $execution['media'],
                                                'type' => $execution['type'],
                                                'id' => $execution['id'],
                                                'status' => $originalData['status'],
                                            ];
                                            $return = $zenith->setCreativeOnOff($execution['id'], $execution['exec_value']);
                                        }else{
                                            $return = false;
                                        } 
                                    }
                                    break;
                            }
                            break;
                    }
                    
                    if($return == true || (isset($return['http_code']) && $return['http_code'] == 200) || isset($return['id'])){
                        $result['log'][] = [
                            "result" => true,
                            'status' => 'success',
                            "msg" => '실행 완료',
                            "seq" => $seq,
                            "data" => [
                                'media' => $execution['media'],
                                'type' => $execution['type'],
                                'id' => $execution['id'],
                                'exec_type' => $execution['exec_type'],
                                'exec_value' => $execution['exec_value'],
                            ]
                        ];
                    }
                }
            }
            $result['result'] = [
                "result" => true,
                'status' => 'success',
                "seq" => $seq,
            ];
            return $result;
        } catch (Exception $e) {
            foreach ($originalSettings as $original) {
                switch ($original['media']) {
                    case 'facebook':
                        $zenith = new ZenithFB();
                        break;
                    case 'google':
                        $zenith = new ZenithGG();
                        break;
                    case 'kakao':
                        $zenith = new ZenithKM();
                        break;
                }

                switch ($original['type']) {
                    case 'campaign':
                        $customerId = null;
                        if ($original['media'] === 'google') {
                            $customerId = $this->google->getCustomerById($original['id'], 'campaign');
                        }

                        if(isset($original['status'])){
                            if ($original['media'] === 'facebook') {
                                $zenith->setCampaignStatus($original['id'], $original['status']);                            
                            } else if ($original['media'] === 'google') {
                                $zenith->updateCampaign($customerId['customerId'], $original['id'], $original['status']);
                            } else if ($original['media'] === 'kakao') {
                                $zenith->setCampaignOnOff($original['id'], $original['status']);
                            }
                        }

                        if(isset($original['budget'])){
                            if ($original['media'] === 'facebook') {
                                $data = [
                                    'id' => $original['id'],
                                    'budget' => $original['budget']
                                ];
                                $zenith->updateCampaignBudget($data); 
                            } else if ($original['media'] === 'google') {
                                $zenith->updateCampaignBudget($customerId['customerId'], $original['id'], ['budget' => $original['budget']]);
                            } else if ($original['media'] === 'kakao') {
                                $data = [
                                    'type' => 'campaign',
                                    'id' => $original['id'],
                                    'budget' => $original['budget']
                                ];
                                $zenith->setDailyBudgetAmount($data);
                            }
                        }
                    case 'adgroup':
                        $customerId = null;
                        if ($original['media'] === 'google') {
                            $customerId = $this->google->getCustomerById($original['id'], 'adgroup');
                        }

                        if(isset($original['status'])){
                            if ($original['media'] === 'facebook') {
                                $zenith->setAdsetStatus($original['id'], $original['status']);                         
                            } else if ($original['media'] === 'google') {
                                $zenith->updateAdGroup($customerId['customerId'], $original['id'], $original['status']);
                            } else if ($original['media'] === 'kakao') {
                                $zenith->setAdGroupOnOff($original['id'], $original['exec_value']);
                            }
                        }

                        if(isset($original['budget'])){
                            if ($original['media'] === 'facebook') {
                                $data = [
                                    'id' => $original['id'],
                                    'budget' => $original['exec_value']
                                ];
                                $zenith->updateAdSetBudget($data);
                            } else if ($original['media'] === 'kakao') {
                                $data = [
                                    'type' => 'adgroup',
                                    'id' => $original['id'],
                                    'budget' => $original['exec_value']
                                ];
                                $return = $zenith->setDailyBudgetAmount($data);
                            }
                        }
                    case 'ad':
                        $customerId = null;                               
                        if ($original['media'] === 'google') {
                            $customerId = $this->google->getCustomerById($original['id'], 'ad');
                        }

                        if(isset($original['status'])){
                            if ($original['media'] === 'facebook') {
                                $zenith->setAdStatus($original['id'], $original['status']);                        
                            } else if ($original['media'] === 'google') {
                                $zenith->updateAdGroupAd($customerId['customerId'], null, $original['id'], $original['status']);
                            } else if ($original['media'] === 'kakao') {
                                $zenith->setCreativeOnOff($original['id'], $original['exec_value']);
                            }
                        }
                        break;
                }
            }

            $result['result'] = [
                "result" => false,
                'status' => 'failed',
                "seq" => $seq
            ];

            $result['log'] = [
                "result" => false,
                'status' => 'failed',
                'msg' => 'Api 오류 발생',
                "seq" => $seq,
            ];
            return $result;
        }
    }
    
    /* public function checkAutomationCondition($target)
    {
        $types = ['budget', 'dbcost', 'dbcount', 'cost', 'margin', 'margin_rate', 'sale', 'impression', 'click', 'cpc', 'ctr', 'conversion'];
        $conditions = $this->automation->getAutomationConditionBySeq($target['aa_seq']);
        if(empty($conditions)){return false;}
        //순서 재정렬
        usort($conditions, function($a, $b) {
            return $a['order'] - $b['order'];
        });

        $isTargetMatched = false;
        $allConditionsMatched = true;
        foreach ($conditions as $condition) {       
            $conditionMatched = false;       
            $message = "일치하는 조건이 존재하지 않습니다.";
            if ($condition['type'] === 'status') {//status 비교
                if ($target['status'] == $condition['type_value']) {
                    $conditionMatched = true;
                    $message = 'status 일치';
                }else{
                    $message = 'status가 일치하지 않습니다.';
                }
            }else{//그 외 필드 비교
                foreach ($types as $type) {                     
                    if ($condition['type'] === $type) {
                        switch ($condition['compare']) {
                            case 'less':
                                $conditionMatched = $target[$type] < $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값보다 큽니다.'."(".$condition['compare'].")";;
                                break;
                            case 'greater':
                                $conditionMatched = $target[$type] > $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값보다 작습니다.'."(".$condition['compare'].")";;
                                break;
                            case 'less_equal':
                                $conditionMatched = $target[$type] <= $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값보다 크거나 같지 않습니다.'."(".$condition['compare'].")";;
                                break;
                            case 'greater_equal':
                                $conditionMatched = $target[$type] >= $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값보다 작거나 같지 않습니다.'."(".$condition['compare'].")";;
                                break;
                            case 'equal':
                                $conditionMatched = $target[$type] == $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값과 일치하지 않습니다.'."(".$condition['compare'].")";;
                                break;
                            case 'not_equal':
                                $conditionMatched = $target[$type] != $condition['type_value'];
                                $message = $conditionMatched ? $condition['compare'].' 조건 일치' : $type.'값이 조건값과 같습니다.'."(".$condition['compare'].")";
                                break;
                            default:
                                $conditionMatched = false;
                                $message = '비교할 조건이 존재하지 않습니다.';
                                break;
                        }
                    }
                }
            }

            if($condition['operation'] == 'or'){
                if($conditionMatched){
                    $isTargetMatched = true;
                    break;
                }else{
                    //조건이 하나이고 조건에 해당되지 않을때
                    if(count($conditions) == 1){
                        $allConditionsMatched = false; 
                    }

                    //and 조건이 없을때
                    if (!in_array('and', array_column($conditions, 'operation'))) {
                        $allConditionsMatched = false; 
                    }
                }
            }

            if($condition['operation'] == 'and'){
                if(!$conditionMatched){
                    $allConditionsMatched = false; 
                    //뒤에 or 조건이 있을수 있어서 or 조건이 없을때만 break
                    $nextKey = array_search($condition, $conditions) + 1;
                    if ($nextKey < count($conditions)) {
                        if ($conditions[$nextKey]['operation'] != 'or') {
                            break;
                        }
                    } else {
                        break;
                    }
                }
            }
            
        }

        if($isTargetMatched || $allConditionsMatched){
            $matchedArray['match'][] = [
                'aa_seq' => $target['aa_seq'],
                'msg' => $message
            ];
        }else{
            $matchedArray['notMatch'][] = [
                'aa_seq' => $target['aa_seq'],
                'msg' => $message
            ];
        }

    return $matchedArray;
    } */

    private function setData($data)
    {
        $formatData = [];
        $formatData['budget'] = $formatData['impression'] = $formatData['click'] = $formatData['cost'] = $formatData['sale'] = $formatData['dbcount'] = $formatData['margin'] = $formatData['cpc'] = $formatData['ctr'] = $formatData['dbcost'] = $formatData['conversion']= $formatData['margin_rate'] = 0;
        foreach ($data as $d) {
            if(isset($d['status']) && in_array($d['status'], ['ON', '1', 'ACTIVE', 'ENABLED'])){
                $formatData['status'] = 'ON';
            }else{
                $formatData['status'] = 'OFF';
            }
            
            $formatData['budget'] += $d['budget'];
            $formatData['impression'] += $d['impressions'];
            $formatData['click'] += $d['click'];
            $formatData['cost'] += $d['spend'];
            $formatData['sale'] += $d['sales'];
            $formatData['dbcount'] += $d['unique_total'];
            $formatData['margin'] += $d['margin'];

            //CPC(Cost Per Click: 클릭당단가 (1회 클릭당 비용)) = 지출액/링크클릭
            if($formatData['click'] > 0){
                $formatData['cpc'] = round($formatData['cost'] / $formatData['click']);
            }

            //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
            
            if($formatData['impression'] > 0){
                $formatData['ctr'] = ($formatData['click'] / $formatData['impression']) * 100;
            }

            //CPA(Cost Per Action: 현재 DB단가(전환당 비용)) = 지출액/유효db
            if($formatData['dbcount'] > 0){
                $formatData['dbcost'] = round($formatData['cost'] / $formatData['dbcount']);
            }

            //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
            if ($formatData['click'] > 0) {
                $formatData['conversion'] = ($formatData['dbcount'] / $formatData['click']) * 100;
            }

            //수익률 = (수익/매출액)*100
            if ($formatData['sale'] > 0) {
                $formatData['margin_rate'] = round(($formatData['margin'] / $formatData['sale']) * 100);
            } else {
                $formatData['margin_rate'] = 0;
            } 
        }

        return $formatData;
    }

    private function recordResult($result)
    {
        $resultData = [
            'idx' => $result['seq'],
            'result' => $result['status'],
            'exec_timestamp' => date('Y-m-d H:i:s')
        ];

        $seq = $this->automation->recodeResult($resultData);
        return $seq;
    }

    private function recordLog($log, $seq)
    {
        $data = [];
        if(!empty($log['schedule'])){
            $data['idx'] = $seq;
            $data['schedule_desc'] = json_encode($log['schedule'], JSON_UNESCAPED_UNICODE);
        }

        if(isset($log['target'])) {
            $data['idx'] = $seq;
            $data['target_desc'] = json_encode($log['target'], JSON_UNESCAPED_UNICODE);
        }

        if(isset($log['conditions'])) {
            $data['idx'] = $seq;
            $data['conditions_desc'] = json_encode($log['conditions'], JSON_UNESCAPED_UNICODE);
        }

        if(isset($log['executions'])) {
            $data['executions_desc'] = json_encode($log['executions'], JSON_UNESCAPED_UNICODE);
        }

        if(!empty($data)){
            $this->automation->recodeLog($data);
        }
    }
}
