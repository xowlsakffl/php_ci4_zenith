<?php

namespace App\Controllers;

use App\Controllers\AdvertisementManager\AdvFacebookManagerController;

class HomeController extends BaseController
{
    public function index()
    {
        /* $userIdentity = auth()->user();
        dd($userIdentity); */
        //if(){}

        $page = new PageController();
        return $page->view();
    }
}
