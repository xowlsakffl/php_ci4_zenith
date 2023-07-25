<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Authentication\Passwords;

class PasswordChangeController extends BaseController
{
    private $user, $userModel;
    protected $helpers = ['setting'];

    public function __construct()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        $this->user = auth()->user();
        $this->userModel = model(UserModel::class);
    }

    public function changePasswordView()
    {
        return view(setting('Auth.views')['set-password']);
    }

    public function changePasswordAction(){
        $data = $this->request->getPost();
        $rules = [
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => 'required|' . Passwords::getMaxLengthRule() . '|strong_password[]',
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes',
                ],
            ],
            'password_confirm' => [
                'label' => 'Auth.passwordConfirm',
                'rules' => 'required|matches[password]',
            ],
        ];

        if (! $this->validateData($data, $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->user->fill($data);
        $result = $this->userModel->save($this->user);
        if($result == true){
            $this->user->undoForcePasswordReset();
        }

        return redirect()->to("/");
    }
}
