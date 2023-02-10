<?php
namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Shield\Models\UserModel;


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
                $data = $this->userModel->find($id);
            } else {
                $data = $this->userModel->findAll();
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
                    'username' => 'required|is_unique[users.username]',
                ]);
                if($this->validation->run($this->data)){
                    $ret = true;
                    $this->userModel->update($id, $this->data);
                }else{
                    return $this->failValidationErrors("유효성 검사 에러");
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
            $ret = true;
            $this->userModel->insert($this->data);
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