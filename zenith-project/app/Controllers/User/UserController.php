<?php
namespace App\Controllers\User;

use CodeIgniter\API\ResponseTrait;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Entities\User;

class UserController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    protected $user;
    protected $company;
    protected $data;
    protected $logginedUser;
    protected $validation;

    public function __construct() {
        $this->user = model(UserModel::class);
        $this->company = model(CompanyModel::class);
        $this->logginedUser = auth()->user();
    }
    
    public function index()
    {
        return view('users/user');
    }

    public function getUsers(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getUsers($param);
            foreach ($result['data'] as &$row) {
                switch ($row['groups']) {
                    case 'superadmin':
                        $row['groups'] = '최고관리자';
                        break;
                    case 'admin':
                        $row['groups'] = '관리자';
                        break;
                    case 'developer':
                        $row['groups'] = '개발자';
                        break;
                    case 'user':
                        $row['groups'] = '사용자';
                        break;
                    case 'agency':
                        $row['groups'] = '광고대행사';
                        break;
                    case 'advertiser':
                        $row['groups'] = '광고주';
                        break;
                    case 'guest':
                        $row['groups'] = '게스트';
                        break;
                    default:
                        $row['groups'] = '';
                        break;
                }
            };
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

    public function getUser(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getUser($param);
            $result['groups'] = explode(",", $result['groups']);
            $result['permissions'] = explode(",", $result['permissions']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getSearchUsers(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getSearchUsers($param);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getBelongUsers(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getBelongUsers($param['company_id']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
    
    public function setUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                if(empty($param['company_id'])){
                    $company = $this->company->getCompanyByName($param);
                    $param['company_id'] = $company['id'];
                }

                $result = $this->user->setUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setBelongUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $belongUser = $this->user->getBelongUser($param);
                if(!empty($belongUser)){
                    return $this->failValidationErrors(["username" => "이미 소속되어 있습니다."]);
                }
                $result = $this->user->setBelongUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function exceptBelongUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $result = $this->user->exceptBelongUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function put($id) {
        $ret = false;
        $checkPermission = $this->checkPermission($this->logginedUser, $id, $this->data['groups']);
        if($checkPermission == false){
            return $this->failUnauthorized("권한이 없습니다.");
        }
        if (strtolower($this->request->getMethod()) === 'put') {
            if ($id && !empty($this->data)) {         
                $this->validation = \Config\Services::validation();
                $this->validation->setRules($this->user->validationRules, $this->user->validationMessages);
                if (!$this->validation->run($this->data)) {
                    $errors = $this->validation->getErrors();
                    return $this->failValidationErrors($errors);
                }

                $user = $this->user->findById($id);  
                    
                $user->fill([
                    'username' => $this->data['username'],
                ]);     

                $this->user->save($user);
                $groups = $this->data['groups'];
                $user->syncGroups(...$this->data['groups']);
                $user->syncPermissions(...$this->data['permission']);
                $ret = true;
            }else{
                return $this->fail("잘못된 요청");
            }
        }
        return $this->respond($ret);
    }

    protected function delete($id) {
        $ret = false;
        if (strtolower($this->request->getMethod()) === 'delete') {
            if ($id) {
                $ret = true;
                $this->user->delete($id);
            }
        }else{
            return $this->fail("잘못된 요청");
        }
        
        return $this->respond($ret);
    }

    protected function checkPermission($logginedUser, $updateId, $postGroups)
    {
        $updateUser = $this->user->find($updateId);
        $updateUserGroups = $updateUser->getGroups();
        //user - 본인만 수정 권한 있음
        if($logginedUser->inGroup('user') && !$logginedUser->inGroup('superadmin', 'admin', 'developer') && !$logginedUser->hasPermission('user.edit')){
            if($logginedUser->id != $updateUser->id){
                return false;
            }
            if(in_array('superadmin', $postGroups) || in_array('admin', $postGroups) || in_array('developer', $postGroups) || in_array('agency', $postGroups) || in_array('advertiser', $postGroups)){
                return false;
            }
        }
        //developer - 본인, user 수정 권한 있음
        if($logginedUser->inGroup('developer') && !$logginedUser->inGroup('superadmin', 'admin')){
            if($updateUser->inGroup('superadmin', 'admin')){
                return false;
            }

            if($logginedUser->ingroup('developer') && $updateUser->inGroup('developer')){
                if($logginedUser->id != $updateUser->id){
                    return false;
                }
            }

            if(in_array('superadmin', $postGroups) || in_array('admin', $postGroups) || in_array('developer', $postGroups)){
                return false;
            }
        }
        //admin - 본인, user 수정 권한 있음
        if($logginedUser->inGroup('admin') && !$logginedUser->inGroup('superadmin')){
            if($updateUser->inGroup('superadmin')){
                return false;
            }

            if($logginedUser->ingroup('admin') && $updateUser->inGroup('admin')){
                if($logginedUser->id != $updateUser->id){
                    return false;
                }
            }

            if(in_array('superadmin', $postGroups)){
                return false;
            }
        }

        return true;
    }
}