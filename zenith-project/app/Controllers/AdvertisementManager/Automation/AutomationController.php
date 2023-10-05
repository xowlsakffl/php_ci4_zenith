<?php

namespace App\Controllers\AdvertisementManager\Automation;

use App\Controllers\BaseController;
use App\Models\Advertiser\AutomationModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use DateTime;

class AutomationController extends BaseController
{
    use ResponseTrait;
    
    protected $automation;

    public function __construct() 
    {
        $this->automation = model(AutomationModel::class);
    }

    public function index()
    {
        return view('automation/automation');
    }

    public function createAutomation()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            $data = [
                'subject' => $arg['subject'],
				'description' => $arg['description'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'subject' => 'required',
                'description' => 'required'
            ];
            $validationMessages   = [
                'subject' => [
                    'required' => '이름은 필수 입력 사항입니다.',
                ],
                'description' => [
                    'required' => '설명은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->automation->createAutomation($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createAutomationSchedule()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            
            if(empty($arg)){
                return $this->fail("잘못된 요청");
            }

            $data = [
                'idx' => $arg['idx'],
                'exec_type' => $arg['exec_type'] ?? '',
                'aas_type_value' => $arg['aas_type_value'] ?? '',
                'exec_time' => $arg['exec_time'] ?? null,
                'ignore_time' => $arg['ignore_time'] ?? null,
                'exec_week' => $arg['exec_week'] ?? null,
                'month_type' => $arg['month_type'] ?? null,
                'month_day' => $arg['month_day'] ?? null,
                'month_week' => $arg['month_week'] ?? null,
            ];
            
            $result = $this->automation->createAutomationSchedule($data);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createAutomationTarget()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            
            if(empty($arg)){
                return $this->fail("잘못된 요청");
            }

            $data = [
                'idx' => $arg['idx'],
                'type' => $arg['type'],
                'media' => $arg['media'],
                'id' => $arg['id'],
            ];
            
            $this->automation->createAutomationTarget($data);
            
            return $this->respond(true);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createAutomationCondition()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $args = $this->request->getPost();

            if(empty($args)){
                return $this->fail("잘못된 요청");
            }

            $result = $this->automation->createAutomationCondition($args);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createAutomationExecution()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'post'){
            $args = $this->request->getPost();

            if(empty($args)){
                return $this->fail("잘못된 요청");
            }
            
            $result = $this->automation->createAutomationExecution($args);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomation()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $seq = $arg['seq'];
            $data = [
                'subject' => $arg['subject'],
				'description' => $arg['description'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'subject' => 'required',
                'description' => 'required'
            ];
            $validationMessages   = [
                'subject' => [
                    'required' => '이름은 필수 입력 사항입니다.',
                ],
                'description' => [
                    'required' => '설명은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->automation->updateAutomation($data, $seq);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomationSchedule()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            
            if(empty($arg)){
                return $this->fail("잘못된 요청");
            }

            $idx = $arg['idx'];
            $data = [
                'exec_type' => $arg['exec_type'],
                'aas_type_value' => $arg['aas_type_value'],
                'exec_time' => $arg['exec_time'],
                'ignore_time' => $arg['ignore_time'],
                'exec_week' => $arg['exec_week'],
                'month_type' => $arg['month_type'],
                'month_day' => $arg['month_day'],
                'month_week' => $arg['month_week'],
            ];
            
            $result = $this->automation->updateAutomationSchedule($data, $idx);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomationTarget()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            
            if(empty($arg)){
                return $this->fail("잘못된 요청");
            }

            $idx = $arg['idx'];
            $data = [
                'type' => $arg['type'],
                'media' => $arg['media'],
                'id' => $arg['id'],
            ];
            
            $result = $this->automation->updateAutomationTarget($data, $idx);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomationCondition()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'put'){
            $args = $this->request->getRawInput();

            if(empty($args)){
                return $this->fail("잘못된 요청");
            }

            $idx = $args['idx'];
            $result = $this->automation->updateAutomationCondition($args, $idx);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAutomationExecution()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'put'){
            $args = $this->request->getRawInput();

            if(empty($args)){
                return $this->fail("잘못된 요청");
            }

            $idx = $args['idx'];
            $result = $this->automation->updateAutomationExecution($args, $idx);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteAutomation()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'delete'){
            $seq = $this->request->getRawInput('seq');
            
            $result = $this->automation->deleteAutomation($seq);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function checkAutomationSchedule()
    {
        $automations = $this->automation->getAutomations();
        $execArray = [];
        foreach ($automations as $automation) {
            if(empty($automation['aas_idx'])){continue;}
            $lastExecTime = $automation['aar_exec_timestamp'] ?? $automation['aas_reg_datetime'];
            $lastExecTime = Time::parse($lastExecTime);

            $ignoreStartTime = $automation['aas_ignore_start_time'] ?? null;
            $ignoreEndTime = $automation['aas_ignore_end_time'] ?? null;
            

            $currentDate = new Time('now');
            $currentTime = $currentDate->format('H:i');
            
            //제외시간
            if(!is_null($ignoreStartTime) && !is_null($ignoreEndTime)){
                $ignoreStartTime = Time::parse($ignoreStartTime);
                $ignoreEndTime = Time::parse($ignoreEndTime);
                if ($currentDate->isAfter($ignoreStartTime) && $currentDate->isBefore($ignoreEndTime)) {
                    continue;
                }
            }else{
                //매n시간 매n분
                if($automation['aas_exec_type'] === 'hour' || $automation['aas_exec_type'] === 'minute'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    if($automation['aas_exec_type'] === 'hour'){
                        $diffTime = $diffTime->getHours();
                    }else{
                        $diffTime = $diffTime->getMinutes();
                    }
                    if($diffTime >= $automation['aas_type_value']){
                        $execArray[] = $automation;
                        continue;
                    }
                }

                //매n일
                if($automation['aas_exec_type'] === 'day'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getDays();
                    if($diffTime >= $automation['aas_type_value'] && $currentTime === $automation['aas_exec_time']){
                        $execArray[] = $automation;
                        continue;
                    }
                }

                //매n주
                if($automation['aas_exec_type'] === 'week'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getWeeks();
                    $currentDoW = $currentDate->dayOfWeek;
                    if($diffTime >= $automation['aas_type_value'] && $currentDoW === $automation['aas_exec_week'] && $currentTime === $automation['aas_exec_time']){
                        $execArray[] = $automation;
                        continue;
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
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'end_day'){
                        $currentMonthLastDay = $currentDate->format('t');      
                        if($diffTime >= $automation['aas_type_value'] && $currentDay === $currentMonthLastDay && $currentTime === $automation['aas_exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'first'){
                        $firstDayMonth = $currentDate->setDay(1);
                        while ($firstDayMonth->dayOfWeek != $automation['aas_month_week']) {
                            $firstDayMonth = $firstDayMonth->addDays(1);
                        }
                        if($diffTime >= $automation['aas_type_value'] && $firstDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'last'){
                        $lastDayMonth = $currentDate->setDay($currentDate->format('t'));
                        while ($lastDayMonth->dayOfWeek != $automation['aas_month_week']) {
                            $lastDayMonth = $lastDayMonth->subDays(1);
                        }
                        if($diffTime >= $automation['aas_type_value'] && $lastDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'day'){
                        if($diffTime >= $automation['aas_type_value'] && $currentDay === $automation['aas_month_day'] && $currentTime === $automation['aas_exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }
                }
            }
        }

        return $execArray;
    }

    public function getAutomationTarget($automations)
    {
        if(empty($automations)){return false;}
        $execArray = [];
        $types = ['advertiser', 'account', 'campaign', 'adgroup', 'ad'];
        $mediaTypes = ['company', 'facebook', 'google', 'kakao'];
        foreach ($automations as $automation) {
            if (in_array($automation['aat_type'], $types) && in_array($automation['aat_media'], $mediaTypes)) {
                $methodName = "getTarget" . ucfirst($automation['aat_media']);
                if (method_exists($this->automation, $methodName)) {
                    $data = $this->automation->$methodName($automation);
                    $data = $this->setData($data);
                    $data['aa_seq'] = $automation['aa_seq'];
                    $execArray[] = $data;
                }
            }
        }

        return $execArray;
    }

    public function checkAutomationCondition($targets)
    {
        if(empty($targets)){return false;}
        foreach ($targets as $target) {
            $conditions = $this->automation->getAutomationConditionBySeq($target['aa_seq']);
            dd($conditions);
            /* switch ($conditions['type']) {
                case 'status':
                    if($target['status'] === $conditions['type_value']){
                        
                    }else{
                        continue;
                    }
                    break;
                
                default:
                    
                    break;
            } */
        }
    }

    public function execAutomation()
    {
        $checkSchedule = $this->checkAutomationSchedule();
        $targets = $this->getAutomationTarget($checkSchedule);
        $checkCondition = $this->checkAutomationCondition($targets);
    }

    private function setData($data)
    {
        $formatData = [];
        $formatData['budget'] = $formatData['impressions'] = $formatData['click'] = $formatData['spend'] = $formatData['sales'] = $formatData['unique_total'] = $formatData['margin'] = $formatData['cpc'] = $formatData['ctr'] = $formatData['cpa'] = $formatData['cvr']= $formatData['margin_ratio'] = 0;
        foreach ($data as $d) {
            if(isset($d['status']) && in_array($d['status'], ['ON', '1', 'ACTIVE', 'ENABLED'])){
                $formatData['status'] = 'ON';
            }else{
                $formatData['status'] = 'OFF';
            }
            
            $formatData['budget'] += $d['budget'];
            $formatData['impressions'] += $d['impressions'];
            $formatData['click'] += $d['click'];
            $formatData['spend'] += $d['spend'];
            $formatData['sales'] += $d['sales'];
            $formatData['unique_total'] += $d['unique_total'];
            $formatData['margin'] += $d['margin'];

            //CPC(Cost Per Click: 클릭당단가 (1회 클릭당 비용)) = 지출액/링크클릭
            if($formatData['click'] > 0){
                $formatData['cpc'] = $formatData['spend'] / $formatData['click'];
            }

            //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
            
            if($formatData['impressions'] > 0){
                $formatData['ctr'] = ($formatData['click'] / $formatData['impressions']) * 100;
            }

            //CPA(Cost Per Action: 현재 DB단가(전환당 비용)) = 지출액/유효db
            if($formatData['unique_total'] > 0){
                $formatData['cpa'] = $formatData['spend'] / $formatData['unique_total'];
            }

            //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
            if ($formatData['click'] > 0) {
                $formatData['cvr'] = ($formatData['unique_total'] / $formatData['click']) * 100;
            }

            //수익률 = (수익/매출액)*100
            if ($formatData['sales'] > 0) {
                $formatData['margin_ratio'] = ($formatData['margin'] / $formatData['sales']) * 100;
            } else {
                $formatData['margin_ratio'] = 0;
            } 
        }

        return $formatData;
    }
}
