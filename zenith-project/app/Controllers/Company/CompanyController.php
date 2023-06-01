<?php

namespace App\Controllers\Company;

use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class CompanyController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    protected $data, $zenith, $company, $validation;

    public function __construct()
    {
        /* if(!auth()->user()->ingroup('superadmin', 'admin', 'developer')){
            return $this->failUnauthorized("권한이 없습니다.");
        } */
        $this->zenith = \Config\Database::connect();
        $this->company = model(CompanyModel::class);
    }

    public function index()
    {
        return view('companies/company');
    }

    public function getCompanies()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->company->getCompanies($param);
            $result = [
                'data' => $result['data'],
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($param['draw']),
            ];

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getCompany()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->company->getCompany($param['id']);
            $result['username'] = explode(',', $result['username']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createCompany()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $agency = $this->company->getAgencyByName($param['p_name']); 
                $validation = \Config\Services::validation();
                if(empty($agency)) {
                    return $this->failValidationErrors(["p_name" => "존재하지 않는 대행사입니다."]);
                }
                $validation->setRules($this->company->validationRules, $this->company->validationMessages);
                if (!$validation->run($param)) {
                    $errors = $validation->getErrors();
                    return $this->failValidationErrors($errors);
                }

                $result = $this->company->createCompany($param, $agency);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setCompany()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $agency = $this->company->getAgencyByName($param['p_name']); 
                $validation = \Config\Services::validation();
                if(empty($agency)) {
                    return $this->failValidationErrors(["p_name" => "존재하지 않는 대행사입니다."]);
                }
                $validation->setRules($this->company->validationRules, $this->company->validationMessages);
                if (!$validation->run($param)) {
                    $errors = $validation->getErrors();
                    return $this->failValidationErrors($errors);
                }

                $result = $this->company->setCompany($param, $agency);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
    
    public function getSearchAgencies()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->company->getSearchAgencies($param['stx']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function deleteCompany()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete') {
            $param = $this->request->getRawInput();
            $result = $this->company->deleteCompany($param);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getSearchAdAccounts()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->company->getSearchAdAccounts($param['stx']);
            foreach($result as &$row){
                if($row['status'] == 'ENABLED' || $row['status'] == 1 || $row['status'] == 'ON'){
                    $row['status'] = '활성';
                }else{
                    $row['status'] = '비활성';
                }
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getCompanyAdAccounts()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->company->getCompanyAdAccounts($param);
            foreach($result as &$row){
                if($row['status'] == 'ENABLED' || $row['status'] == 1 || $row['status'] == 'ON'){
                    $row['status'] = '활성';
                }else{
                    $row['status'] = '비활성';
                }
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setCompanyAdAccount()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['ad_account_id']);
            $param['ad_account_id'] = $sliceId[1];
            $param['media'] = $sliceId[0];
            $adAccount = $this->company->getCompanyAdAccount($param);
            if(!empty($adAccount)){
                return $this->failValidationErrors(["username" => "이미 소속되어 있습니다."]);
            }
            $result = $this->company->setCompanyAdAccount($param);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function exceptCompanyAdAccount()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete') {
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['ad_account_id']);
            $param['ad_account_id'] = $sliceId[1];
            $param['media'] = $sliceId[0];
            if (!empty($param)) {
                $result = $this->company->exceptCompanyAdAccount($param);
            }else{
                return $this->fail("잘못된 요청");
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
