<?php
namespace App\Controllers\Advertisement;
use App\Controllers\BaseController;

class ApiController extends BaseController
{
    protected $chainsaw;
/*
    public function __construct(...$params) {
        print_r($params); exit;
        include APPPATH."/ThirdParty/facebook_api/facebook-api.php";
        $this->chainsaw = new \ChainsawFB();
    }
    */
    public function _remap($method, ...$params) {
        if (method_exists($this, $method)) {
            return $this->{$method}(...$params);
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    protected function facebook(...$params) {
        include APPPATH."/ThirdParty/facebook_api/facebook-api.php";
        $this->chainsaw = new \ChainsawFB();
        $this->fb_func(...$params);
    }

    protected function fb_func(...$params) {
        if (method_exists($this->chainsaw, $params[0])) {
            $result = $this->chainsaw->{$params[0]}();
            if(in_array('grid', $params)) $this->chainsaw->grid($result);
            
            return $result;
        }
    }

    protected function kakaoMoment(...$params) {
        include APPPATH."/ThirdParty/moment_api/kmapi.php";
        $this->chainsaw = new \ChainsawKM();
        $this->km_func(...$params);
    }

    protected function km_func(...$params) {
        dd($this->chainsaw);
        if (method_exists($this->chainsaw, $params[0])) {
            $result = $this->chainsaw->{$params[0]}();
            if(in_array('grid', $params)) $this->chainsaw->grid($result);
            
            return $result;
        }
    }
}