<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class TestController extends BaseController
{
    use ResponseTrait;

    public function getInterlockData()
    {
        $data = $this->request->getVar();
        log_message('info', 'interlock : '.print_r($data));
        return $this->respond(['result'=> 200, 'msg' => 'success']);
    } 
}
