<?php
namespace App\Libraries;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Test extends BaseController 
{
    use ResponseTrait;

    public function getInterlockData()
    {
        $data = $this->request->getVar();
        log_message('info', 'interlock : '.print_r($data));
        return $this->respond(['status'=> 200, 'msg' => 'success']);
    } 
}