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

    public function __construct() {
        $this->userModel = model(UserModel::class);
    }

    public function get($id = false) {
        if (strtolower($this->request->getMethod()) === 'get') {
            if ($id) {
                $data = $this->userModel->getUserGroups($id);
            } else {
                $data = $this->userModel->getUserGroups();
            }
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
    }

    protected function put($id = false) {
        $ret = false;

        if (strtolower($this->request->getMethod()) === 'put') {
            if ($id && !empty($this->data)) {         
                $this->validation = \Config\Services::validation();
                $this->validation->setRules([
                    'username' => 'required',
                    'groups' => 'required'
                ],
                [   // Errors
                    'username' => [
                        'required' => '이름은 필수 입력사항입니다.',
                    ],
                ]);
                if($this->validation->run($this->data)){  
                    $user = $this->userModel->findById($id);  
                       
                    $user->fill([
                        'username' => $this->data['username'],
                    ]);     
                    $this->userModel->save($user);
                    $groups = $this->data['groups'];
                    $user->syncGroups(...$this->data['groups']);

                    $ret = true;
                }else{
                    if($this->validation->hasError('username')){
                        $error = $this->validation->getError('username');
                    }else if($this->validation->hasError('groups')){
                        $error = $this->validation->getError('groups');
                    }
    
                    return $this->failValidationErrors($error);
                }
            }else{
                return $this->fail("잘못된 요청");
            }
        }
        return $this->respond($ret);
    }

    protected function delete($id = false) {
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