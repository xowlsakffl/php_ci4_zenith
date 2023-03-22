<?php
namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Entities\User;

class ApiUserController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    protected $userModel;
    protected $data;
    protected $logginedUser;
    protected $validation;

    public function __construct() {
        $this->userModel = model(UserModel::class);
        $this->logginedUser = auth()->user();
    }

    public function get($id = NULL) {
        if (strtolower($this->request->getMethod()) === 'get') {
            if ($id) {
                $data['result'] = $this->userModel->getUser($id);       
                $data['result']->permission = explode(',', $data['result']->permission);
                $data['result']->groups = explode(',', $data['result']->groups);
            } else {
                $param = $this->request->getGet();

                $builder = $this->userModel->select('u.*, GROUP_CONCAT(DISTINCT agu.group) as groups');

                if(!empty($param['limit'])){
                    $limit = $param['limit'];
                }else{
                    $limit = 10;
                }            
                $builder->from('users as u');
                $builder->join('auth_groups_users as agu', 'u.id = agu.user_id');

                if(!empty($param['startDate']) && !empty($param['endDate'])){
                    $builder->where('u.created_at >=', $param['startDate'].' 00:00:00');
                    $builder->where('u.created_at <=', $param['endDate'].' 23:59:59');
                    
                    $data['pager']['startDate'] = $param['startDate'];
                    $data['pager']['endDate'] = $param['endDate'];
                }

                $builder->where('u.deleted_at', NULL);

                if(!empty($param['search'])){
                    $searchText = $param['search'];
                    $builder->like('u.username', $searchText);
                    $data['pager']['search'] = $param['search'];
                }
     
                $builder->groupBy('u.id');

                if(!empty($param['sort'])){
                    if($param['sort'] == 'old'){
                        $builder->orderBy('u.created_at', 'asc');
                    }else{
                        $builder->orderBy('u.created_at', 'desc');
                    }
                    
                    $data['pager']['sort'] = $param['sort'];
                }

                $data['result'] = $builder->paginate($limit);

                $data['pager']['limit'] = intval($limit);
                $data['pager']['total'] = $builder->pager->getTotal();
                $data['pager']['pageCount'] = $builder->pager->getPageCount();
                $data['pager']['currentPage'] = $builder->pager->getCurrentPage();
                $data['pager']['firstPage'] = $builder->pager->getFirstPage();
                $data['pager']['lastPage'] = $builder->pager->getLastPage();
            }
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
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
                $this->validation->setRules($this->userModel->validationRules, $this->userModel->validationMessages);
                if (!$this->validation->run($this->data)) {
                    $errors = $this->validation->getErrors();
                    return $this->failValidationErrors($errors);
                }

                $user = $this->userModel->findById($id);  
                    
                $user->fill([
                    'username' => $this->data['username'],
                ]);     

                $this->userModel->save($user);
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
                $this->userModel->delete($id);
            }
        }else{
            return $this->fail("잘못된 요청");
        }
        
        return $this->respond($ret);
    }

    protected function checkPermission($logginedUser, $updateId, $postGroups)
    {
        $updateUser = $this->userModel->find($updateId);
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

    public function _remap(...$params) {
        $method = $this->request->getMethod();
        $params = [($params[0] !== 'get' ? $params[0] : false)];
        $this->data = $this->request->getRawInput();

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}