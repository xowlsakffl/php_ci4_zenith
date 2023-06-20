<?php
namespace App\Controllers\User;

use App\Models\Api\CompanyModel;
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
        if (/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getUsers($param);
            foreach ($result['data'] as &$row) {
                $groups = explode(",", $row['groups']);
                foreach ($groups as &$group) {
                    switch ($group) {
                        case 'superadmin':
                            $group = '최고관리자';
                            break;
                        case 'admin':
                            $group = '관리자';
                            break;
                        case 'developer':
                            $group = '개발자';
                            break;
                        case 'user':
                            $group = '사용자';
                            break;
                        case 'agency':
                            $group = '광고대행사';
                            break;
                        case 'advertiser':
                            $group = '광고주';
                            break;
                        case 'guest':
                            $group = '게스트';
                            break;
                        default:
                            $group = '';
                            break;
                    }
                }
                $row['groups'] = implode(',', $groups);
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
                $company = $this->company->getCompanyByName($param['company_name']);
                if(empty($company)) {
                    return $this->failValidationErrors(["company" => "존재하지 않는 광고주/광고대행사입니다."]);
                }

                $param['company_id'] = $company['id'];
                
                switch ($param['status']) {
                    case 1:
                        $param['status_message'] = '활성';
                        break;
                    case 2:
                        $param['status_message'] = '비활성';
                        break;
                    default:
                        break;
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