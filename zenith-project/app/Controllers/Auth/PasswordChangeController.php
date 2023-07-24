<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Response;

class PasswordChangeController extends BaseController
{
    private $loginModel;

    public function __construct()
    {
        $this->loginModel = model(LoginModel::class);
    }

    public function changePasswordView($user, $identifier)
    {
        $this->response->setStatusCode(404, 'Nope. Not here.');
    }
}
