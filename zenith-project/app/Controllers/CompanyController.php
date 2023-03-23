<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class CompanyController extends BaseController
{
    use ResponseTrait;
    
    protected $companyModel;
    protected $data;

    public function __construct(){
        $this->companyModel = model(CompanyModel::class);
    }

    public function index()
    {
        return view('companies/company');
    }

    public function belong($id){
        if(!auth()->user()->ingroup('superadmin', 'admin', 'developer')){
            return redirect()->back()->with('message', '권한이 없습니다.');
        }

        $data = [
            'company' => $this->companyModel->getCompanies($id),
            'agencies' => $this->companyModel->getAgency(),
        ];

        return view('companies/belong', $data);
    }

    public function updateCompanies(){
        $ret = false;
        if(!auth()->user()->ingroup('superadmin', 'admin', 'developer')){
            return $this->failUnauthorized("권한이 없습니다.");
        }
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $data = $this->request->getRawInput();

            $builder = $this->companyUserModel;
            $count = $builder->where('user_id', $data['user_id'])->countAllResults();
            
            if($count > 0){
                $builder->where('user_id', $data['user_id']);
                $builder->set('company_id', $data['company_id']);
                $builder->update();
            }else{
                $builder->insert($data);
            }

            $ret = true;
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($ret);
    }
}
