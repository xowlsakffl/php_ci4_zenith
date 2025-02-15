<?php
namespace App\Controllers\Advertisement;
use App\Controllers\BaseController;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\moment_api\ZenithKM;
use App\ThirdParty\googleads_api\ZenithGG;

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
        $this->chainsaw = new ZenithFB();
        $this->fb_func(...$params);
    }
    protected function moment(...$params) {
        $this->chainsaw = new ZenithKM();
        $this->fb_func(...$params);
    }
    protected function google(...$params) {
        $this->chainsaw = new ZenithGG();
        $this->fb_func(...$params);
    }

    protected function fb_func(...$params) {
        if (method_exists($this->chainsaw, $params[0])) {
            $result = $this->chainsaw->{$params[0]}();
            if(in_array('grid', $params)) $this->chainsaw->grid($result);
            
            return $result;
        }
    }

    //안쓰는거
    /* 
    protected function kakaoMoment(...$params) {
        include APPPATH."/ThirdParty/moment_api/kmapi.php";
        $this->chainsaw = new \ChainsawKM();
        $this->km_func(...$params);
    }

    protected function km_func(...$params) {
        if (method_exists($this->chainsaw, $params[0])) {
            $result = $this->chainsaw->{$params[0]}();
            if(in_array('grid', $params)) $this->chainsaw->grid($result);
            
            return $result;
        }
    } 
    */
}