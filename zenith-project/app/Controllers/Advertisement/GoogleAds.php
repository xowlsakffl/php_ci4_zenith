<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;

class GoogleAds extends BaseController
{
    private $chainsaw;

    public function __construct(...$param)
    {
        include APPPATH."/ThirdParty/googleAds_api/adsapi.php";
        $this->chainsaw = new \GoogleAds();       
    }

    public function index()
    {
        //
    }
}
