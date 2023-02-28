<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Entities\User;

class UserController extends ResourceController 
{
    use ResponseTrait;

    protected $userModel;
    protected $data;

    public function __construct(){
        $this->userModel = model(UserModel::class);
    }

    public function index(){
        return view('users/user');
    }

    public function belong($id){
        $data = [
            'user' => $this->userModel->getUser($id),
        ];
        
        return view('users/belong', $data);
    }

    public function post() {
        $ret = false;
        if(!empty($this->data)) {
            $ret = true;
            $this->userModel->insert($this->data);
        }

        return $this->respond($ret);
    }

    public function show($id = false) {
        if($id) {
            $data = $this->userModel->find($id);
        } else {
            $data = $this->userModel->findAll();
        }

        return $this->respond($data);
    }

    public function put($id = false) {
        $ret = false;
        if($id && !empty($this->data)) {
            $ret = true;
            $this->userModel->update($id, $this->data);
        }

        return $this->respond($ret);
    }

    public function delete($id = false) {
        $ret = false;
        if($id) {
            $ret = true;
            $this->userModel->delete($id);
        }

        return $this->respond($ret);
    }
}