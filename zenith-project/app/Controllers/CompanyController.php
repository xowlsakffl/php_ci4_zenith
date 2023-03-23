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
    protected $db;

    public function __construct(){
        $this->companyModel = model(CompanyModel::class);
        $this->db = \Config\Database::connect();
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

            $builder = $this->db->table('companies_idx');
            $count = $builder->where('cdx', $data['cdx'])->countAllResults();
            
            if($count > 0){
                $builder->where('cdx', $data['cdx']);
                $builder->set('parent_cdx', $data['parent_cdx']);
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
