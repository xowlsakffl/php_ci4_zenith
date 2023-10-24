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
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->automation->getAutomation($arg);
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
                case 'adset':
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
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            //$arg = $this->request->getPost();
            $data = $this->request->getGet();
            $validationResult = $this->validationData($data);
            if($validationResult['result'] != true){
                return $this->failValidationErrors($validationResult);
            }
            $result = $this->automation->createAutomation($data);
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

        if($data['detail']['targetConditionDisabled'] != "1"){
            $validationRules['target.id'] = 'required';
            $validationMessages['target.id'] = ['required' => '대상항목을 추가해주세요.'];
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

        if($data['detail']['targetConditionDisabled'] != "1"){
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
        }

        $validation->reset();

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
}
