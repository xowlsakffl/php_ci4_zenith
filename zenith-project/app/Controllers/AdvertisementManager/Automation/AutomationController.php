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
                'type_value' => $arg['type_value'] ?? '',
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
                'type_value' => $arg['type_value'],
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
            $lastExecTime = $automation['exec_timestamp'] ?? $automation['aas_reg_datetime'];
            $lastExecTime = Time::parse($lastExecTime);

            $ignoreStartTime = $automation['ignore_start_time'] ?? null;
            $ignoreEndTime = $automation['ignore_end_time'] ?? null;
            

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
                if($automation['exec_type'] === 'hour' || $automation['exec_type'] === 'minute'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    if($automation['exec_type'] === 'hour'){
                        $diffTime = $diffTime->getHours();
                    }else{
                        $diffTime = $diffTime->getMinutes();
                    }
                    if($diffTime >= $automation['type_value']){
                        $execArray[] = $automation;
                        continue;
                    }
                }

                //매n일
                if($automation['exec_type'] === 'day'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getDays();
                    if($diffTime >= $automation['type_value'] && $currentTime === $automation['exec_time']){
                        $execArray[] = $automation;
                        continue;
                    }
                }

                //매n주
                if($automation['exec_type'] === 'week'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getWeeks();
                    $currentDoW = $currentDate->dayOfWeek;
                    if($diffTime >= $automation['type_value'] && $currentDoW === $automation['exec_week'] && $currentTime === $automation['exec_time']){
                        $execArray[] = $automation;
                        continue;
                    }
                }

                //매n월
                if($automation['exec_type'] === 'month'){
                    $diffTime = $lastExecTime->difference($currentDate);
                    $diffTime = $diffTime->getMonths();
                    $currentDoW = $currentDate->dayOfWeek;
                    $currentDay = $currentDate->getDay();
                    if($automation['month_type'] === 'start_day'){
                        if($diffTime >= $automation['type_value'] && $currentDay === '1' && $currentTime === $automation['exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['month_type'] === 'end_day'){
                        $currentMonthLastDay = $currentDate->format('t');      
                        if($diffTime >= $automation['type_value'] && $currentDay === $currentMonthLastDay && $currentTime === $automation['exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['month_type'] === 'first'){
                        $firstDayMonth = $currentDate->setDay(1);
                        while ($firstDayMonth->dayOfWeek != $automation['month_week']) {
                            $firstDayMonth = $firstDayMonth->addDays(1);
                        }
                        if($diffTime >= $automation['type_value'] && $firstDayMonth->equals($currentDate) && $currentTime === $automation['exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['month_type'] === 'last'){
                        $lastDayMonth = $currentDate->setDay($currentDate->format('t'));
                        while ($lastDayMonth->dayOfWeek != $automation['month_week']) {
                            $lastDayMonth = $lastDayMonth->subDays(1);
                        }
                        if($diffTime >= $automation['type_value'] && $lastDayMonth->equals($currentDate) && $currentTime === $automation['exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }else if($automation['month_type'] === 'day'){
                        if($diffTime >= $automation['type_value'] && $currentDay === $automation['month_day'] && $currentTime === $automation['exec_time']){
                            $execArray[] = $automation;
                            continue;
                        }
                    }
                }
            }
        }

        return $execArray;
    }
}
