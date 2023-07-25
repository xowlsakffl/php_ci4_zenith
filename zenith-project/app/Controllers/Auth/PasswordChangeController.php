<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Models\LoginModel;

class PasswordChangeController extends BaseController
{
    private $userModel, $loginModel;
    protected $helpers = ['setting'];

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->loginModel = model(LoginModel::class);
        $this->response = service('response');
    }

    public function changePasswordView()
    {
        $data = $this->request->getGet();
        if(empty($data['userId']) || empty($data['token'])){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view(setting('Auth.views')['login']);
    }

    public function changePasswordAction(){
        $data = $this->request->getPost();
        $user = $this->userModel->find($data['userId']);
        $lastLogin = $this->loginModel->lastLogin($user);
        if($data['token'] == $lastLogin->identifier){

        }
    }
}
