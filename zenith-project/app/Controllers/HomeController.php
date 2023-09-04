<?php

namespace App\Controllers;

use App\Controllers\AdvertisementManager\AdvFacebookManagerController;

class HomeController extends BaseController
{
    public function index()
    {
        $page = new PageController();
        return $page->view();
    }
}
