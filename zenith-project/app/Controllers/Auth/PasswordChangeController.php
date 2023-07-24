<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class PasswordChangeController extends BaseController
{
    public function changePasswordView()
    {
        $data = $this->request->getGet();
        dd($data);
    }
}
