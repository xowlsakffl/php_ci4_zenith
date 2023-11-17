<?php

namespace App\Controllers;

use App\Controllers\AdvertisementManager\AdvFacebookManagerController;
use App\Controllers\AdvertisementManager\AdvGoogleManagerController;
use App\Controllers\AdvertisementManager\AdvKakaoManagerController;
use CodeIgniter\API\ResponseTrait;

class HomeController extends BaseController
{
    use ResponseTrait;
    
    public function index()
    {
        $data = [];
        $data['password_check'] = false;
        $password_check = auth()->user()->getEmailIdentity()->password_changed_at;
        if(empty($password_check)){
            $data['password_check'] = true;
        }else{
            if(strtotime($password_check) < strtotime('-90 days')){
                $data['password_check'] = true;
            }
        }

        return view('pages/home', $data);
    }

    public function getReports()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $result = [];
            $facebook = new AdvFacebookManagerController;
            $result['facebookReport'] = $facebook->getReport();

            $google = new AdvGoogleManagerController;
            $result['googleReport'] = $google->getReport();

            $kakao = new AdvKakaoManagerController;
            $result['kakaoReport'] = $kakao->getReport();

            return $this->respond($result);
        }
    }
}
