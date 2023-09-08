<?php

namespace App\Controllers\AdvertisementManager\Automation;

use App\Controllers\BaseController;
use App\Models\Advertiser\AutomationModel;
use CodeIgniter\API\ResponseTrait;

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
                'exec_type' => $arg['exec_type'],
                'type_value' => $arg['type_value'],
                'exec_time' => $arg['exec_time'],
                'ignore_time' => $arg['ignore_time'],
                'exec_week' => $arg['exec_week'],
                'month_type' => $arg['month_type'],
                'month_day' => $arg['month_day'],
                'month_week' => $arg['month_week'],
            ];
            
            $this->automation->createAutomationSchedule($data);
            
            return $this->respond(true);
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
}
