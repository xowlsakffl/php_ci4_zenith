<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    public function getIssueComplete()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $param = $this->request->getPost();

            log_message('debug', json_encode($param));
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
