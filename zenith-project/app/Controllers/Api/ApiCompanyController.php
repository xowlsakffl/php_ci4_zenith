<?php

namespace App\Controllers\Api;

use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class ApiCompanyController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    public function __construct()
    {
        $this->company = model(CompanyModel::class);
    }

    public function get($id = NULL)
    {
        if (strtolower($this->request->getMethod()) === 'get') {
            if ($id) {
                
            } else {
                
            }
        }else{
            return $this->fail("잘못된 요청");
        }

        return $this->respond($data);
    }

    public function put($id = false)
    {
        $ret = false;
        

        return $this->respond($ret);
    }

    public function post()
    {
        $ret = false;
        

        return $this->respond($ret);
    }

    protected function delete($id = false)
    {
        $ret = false;

        
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
