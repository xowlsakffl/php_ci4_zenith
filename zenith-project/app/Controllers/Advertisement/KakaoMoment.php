<?php

namespace App\Controllers\Advertisement;

use CodeIgniter\CLI\CLI;
use App\Controllers\BaseController;

class KakaoMoment extends BaseController
{
    private $chainsaw;

    public function __construct(...$param)
    {
        include APPPATH."/ThirdParty/moment_api/kmapi.php";
        $this->chainsaw = new \ChainsawKM();       
    }
}
