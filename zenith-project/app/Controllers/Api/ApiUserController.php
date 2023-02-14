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
                $data['result'] = $this->userModel->find($id);
                $data['group'] = $this->userModel->find($id)->getGroups();
            } else {
                $data['result'] = $this->userModel->getUsersGroups()->paginate(15);
                $data['pager'] = $this->userModel->pager;
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
                    'password' => 'required',
                    'password_confirm' => 'required|matches[password]',
                ],
                [   // Errors
                    'username' => [
                        'required' => '이름은 필수 입력사항입니다.',
                    ],
                    'password' => [
                        'required' => '비밀번호는 필수 입력사항입니다.',
                    ],
                    'password_confirm' => [
                        'required' => '비밀번호는 필수 입력사항입니다.',
                        'matches' => '비밀번호가 일치하지 않습니다.',
                    ],
                ]);
                if($this->validation->run($this->data)){  
                    $user = $this->userModel->findById($id);      
                    $user->fill([
                        'username' => $this->data['username'],
                        'password' => $this->data['password'],
                    ]);     
                    $this->userModel->save($user);
                    $ret = true;
                }else{
                    if($this->validation->hasError('username')){
                        $error = $this->validation->getError('username');
                    }else if($this->validation->hasError('password')){
                        $error = $this->validation->getError('password');
                    }else if($this->validation->hasError('password_confirm')){
                        $error = $this->validation->getError('password_confirm');
                    }
    
                    return $this->failValidationErrors($error);
                }
            }else{
                return $this->fail("잘못된 요청");
            }
        }
        return $this->respond($ret);
    }

    protected function post() {
        $ret = false;
        if (!empty($this->data)) {
            $this->validation = \Config\Services::validation();
            $this->validation->setRules([
                'email' => 'required',
                'username' => 'required',
                'password' => 'required',
                'password_confirm' => 'required|matches[password]',
            ],
            [   // Errors
                'email' => [
                    'required' => '이메일은 필수 입력사항입니다.',
                ],
                'username' => [
                    'required' => '이름은 필수 입력사항입니다.',
                ],
                'password' => [
                    'required' => '비밀번호는 필수 입력사항입니다.',
                ],
                'password_confirm' => [
                    'required' => '비밀번호는 필수 입력사항입니다.',
                    'matches' => '비밀번호가 일치하지 않습니다.',
                ],
            ]);

            if($this->validation->run($this->data)){
                $user = new User([
                    'email'    => $this->data['email'],
                    'username' => $this->data['username'],
                    'password' => $this->data['password'],
                ]);
                
                $this->userModel->save($user);
                $user = $this->userModel->findById($this->userModel->getInsertID());
                $this->userModel->addToDefaultGroup($user);
                $ret = true;
            }else{
                if($this->validation->hasError('email')){
                    $error = $this->validation->getError('email');
                }else if($this->validation->hasError('username')){
                    $error = $this->validation->getError('username');
                }else if($this->validation->hasError('password')){
                    $error = $this->validation->getError('password');
                }else if($this->validation->hasError('password_confirm')){
                    $error = $this->validation->getError('password_confirm');
                }

                return $this->failValidationErrors($error);
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