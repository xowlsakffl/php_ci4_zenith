<?php

namespace App\Controllers\AdvertisementManager\Automation;

use App\Controllers\BaseController;
use App\Models\Advertiser\AutomationModel;
use App\ThirdParty\facebook_api\ZenithFB;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use DateTime;
use Exception;

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
        $matchedArray = [];
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
                        $matchedArray[] = $automation['aa_seq'];
                        continue;
                    }
                }

                //매n일
                if($automation['aas_exec_type'] === 'day'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getDays();
                    if($diffTime >= $automation['aas_type_value'] && $currentTime === $automation['aas_exec_time']){
                        $matchedArray[] = $automation['aa_seq'];
                        continue;
                    }
                }

                //매n주
                if($automation['aas_exec_type'] === 'week'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getWeeks();
                    $currentDoW = $currentDate->dayOfWeek;
                    if($diffTime >= $automation['aas_type_value'] && $currentDoW === $automation['aas_exec_week'] && $currentTime === $automation['aas_exec_time']){
                        $matchedArray[] = $automation['aa_seq'];
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
                            $matchedArray[] = $automation['aa_seq'];
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'end_day'){
                        $currentMonthLastDay = $currentDate->format('t');      
                        if($diffTime >= $automation['aas_type_value'] && $currentDay === $currentMonthLastDay && $currentTime === $automation['aas_exec_time']){
                            $matchedArray[] = $automation['aa_seq'];
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'first'){
                        $firstDayMonth = $currentDate->setDay(1);
                        while ($firstDayMonth->dayOfWeek != $automation['aas_month_week']) {
                            $firstDayMonth = $firstDayMonth->addDays(1);
                        }
                        if($diffTime >= $automation['aas_type_value'] && $firstDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                            $matchedArray[] = $automation['aa_seq'];
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'last'){
                        $lastDayMonth = $currentDate->setDay($currentDate->format('t'));
                        while ($lastDayMonth->dayOfWeek != $automation['aas_month_week']) {
                            $lastDayMonth = $lastDayMonth->subDays(1);
                        }
                        if($diffTime >= $automation['aas_type_value'] && $lastDayMonth->equals($currentDate) && $currentTime === $automation['aas_exec_time']){
                            $matchedArray[] = $automation['aa_seq'];
                            continue;
                        }
                    }else if($automation['aas_month_type'] === 'day'){
                        if($diffTime >= $automation['aas_type_value'] && $currentDay === $automation['aas_month_day'] && $currentTime === $automation['aas_exec_time']){
                            $matchedArray[] = $automation['aa_seq'];
                            continue;
                        }
                    }
                }
            }
        }

        return $matchedArray;
    }

    public function getAutomationTarget($aaSeqs)
    {
        if(empty($aaSeqs)){return false;}
        //aa_seq만 가져오기
        $targets = $this->automation->getTargets($aaSeqs);
        $matchedArray = [];
        $types = ['advertiser', 'account', 'campaign', 'adgroup', 'ad'];
        $mediaTypes = ['company', 'facebook', 'google', 'kakao'];
        foreach ($targets as $automation) {
            //값 별로 메소드 매칭
            if (in_array($automation['aat_type'], $types) && in_array($automation['aat_media'], $mediaTypes)) {
                $methodName = "getTarget" . ucfirst($automation['aat_media']);
                if (method_exists($this->automation, $methodName)) {
                    $data = $this->automation->$methodName($automation);
                    $data = $this->setData($data);
                    $data['aa_seq'] = $automation['aa_seq'];
                    $matchedArray[] = $data;
                }
            }
        }
        return $matchedArray;
    }

    public function checkAutomationCondition($targetDatas)
    {
        if(empty($targetDatas)){return false;}
        $matchedArray = [];
        $types = ['budget', 'dbcost', 'dbcount', 'cost', 'margin', 'margin_rate', 'sale', 'impression', 'click', 'cpc', 'ctr', 'conversion'];
        d($targetDatas);
        foreach ($targetDatas as $target) {
            $conditions = $this->automation->getAutomationConditionBySeq($target['aa_seq']);
            if(!$conditions){continue;}

            //순서 재정렬
            usort($conditions, function($a, $b) {
                return $a['order'] - $b['order'];
            });

            $isTargetMatched = false;
            $allConditionsMatched = true;
            foreach ($conditions as $condition) {
                $conditionMatched = false;       
                if ($condition['type'] === 'status') {//status 비교
                    if ($target['status'] == $condition['type_value']) {
                        $conditionMatched = true;
                    }
                }else{//그 외 필드 비교
                    foreach ($types as $type) {
                        if ($condition['type'] === $type) {
                            $conditionMatched = $this->compareType($target[$type], $condition);
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
                    }
                }

                if($condition['operation'] == 'and'){
                    if(!$conditionMatched){
                        $allConditionsMatched = false; 
                    }
                }
            }
            
            if($isTargetMatched || $allConditionsMatched){
                $matchedArray['match'][] = $target['aa_seq'];
            }else{
                $matchedArray['notMatch'][] = $target['aa_seq'];
            }
        }
        dd($matchedArray);
        return $matchedArray;
    }

    public function execAutomation()
    {
        $checkScheduleSeq = $this->checkAutomationSchedule();
        $targetDatas = $this->getAutomationTarget($checkScheduleSeq);
        $checkConditionSeq = $this->checkAutomationCondition($targetDatas);
        $executions = $this->automation->getExecutions($checkConditionSeq);
        
        //순서 재정렬
        usort($executions, function($a, $b) {
            return $a['aae_order'] - $b['aae_order'];
        });

        foreach ($executions as $execution) {
            switch ($execution['aae_media']) {
                case 'facebook':
                    switch ($execution['aae_type']) {
                        case 'campaign':
                            switch ($execution['aae_exec_type']) {
                                case 'status':                              
                                    if($execution['aae_exec_value'] === "ON"){
                                        $status = 'ACTIVE';
                                    }else{
                                        $status = 'PAUSED';
                                    }
                                    $zenith = new ZenithFB();
                                    $result = $zenith->setCampaignStatus($execution['aae_id'], $status);
                                    break;
                                case 'budget':
                                    # code...
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            break;
                        case 'adgroup':
                            switch ($execution['aae_exec_type']) {
                                case 'status':
                                    # code...
                                    break;
                                case 'budget':
                                    # code...
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            break;
                        case 'ad':
                            switch ($execution['aae_exec_type']) {
                                case 'status':
                                    # code...
                                    break;
                                case 'budget':
                                    # code...
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                case 'google':
                
                    break;
                case 'kakao':
            
                    break;
                default:
                    break;
            }
        }
    }

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

    private function compareType($type, $condition)
    {
        switch ($condition['compare']) {
            case 'less':
                return $type < $condition['type_value'];
            case 'greater':
                return $type > $condition['type_value'];
            case 'less_equal':
                return $type <= $condition['type_value'];
            case 'greater_equal':
                return $type >= $condition['type_value'];
            case 'equal':
                return $type == $condition['type_value'];
            case 'not_equal':
                return $type != $condition['type_value'];
            default:
                return false;
        }
    }
}
