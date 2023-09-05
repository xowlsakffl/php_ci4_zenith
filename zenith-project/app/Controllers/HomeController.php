<?php

namespace App\Controllers;

use App\Controllers\AdvertisementManager\AdvFacebookManagerController;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [];
        $data['password_check'] = false;
        $password_check = auth()->user()->getEmailIdentity()->password_changed_at;
        if(empty($password_check)){
            $data['password_check'] = true;
        }else{
            if(strtotime($password_check) < strtotime('-2 weeks')){
                $data['password_check'] = true;
            }
        }

        return view('pages/home', $data);
    }
}
