<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\UserModel;
use App\Models\Api\CompanyModel;
use App\Models\Api\CompanyUserModel;
use CodeIgniter\Shield\Entities\User;

class UserController extends ResourceController 
{
    use ResponseTrait;

    protected $userModel;
    protected $data;

    public function __construct(){
        $this->userModel = model(UserModel::class);
        $this->companyModel = model(CompanyModel::class);
        $this->companyUserModel = model(CompanyUserModel::class);
    }

    public function index(){
        return view('users/user');
    }

    public function belong($id){
        $data = [
            'user' => $this->userModel->getUser($id),
        ];
        return view('users/belong', $data);
    }
    
    public function getCompanies(){
        if($this->request->isAJAX()){
            $result = $this->companyModel->getCompanies();
            $data = [
                'companies' => $result,
            ];
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
    }

    public function updateCompanies(){
        $ret = false;
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $data = $this->request->getRawInput();

            $builder = $this->companyUserModel;

            dd($companyUser);

            $ret = true;
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($ret);
    }

    public function post() {
        $ret = false;
        if(!empty($this->data)) {
            $ret = true;
            $this->userModel->insert($this->data);
        }

        return $this->respond($ret);
    }

    public function show($id = false) {
        if($id) {
            $data = $this->userModel->find($id);
        } else {
            $data = $this->userModel->findAll();
        }

        return $this->respond($data);
    }

    public function put($id = false) {
        $ret = false;
        if($id && !empty($this->data)) {
            $ret = true;
            $this->userModel->update($id, $this->data);
        }

        return $this->respond($ret);
    }

    public function delete($id = false) {
        $ret = false;
        if($id) {
            $ret = true;
            $this->userModel->delete($id);
        }

        return $this->respond($ret);
    }
}