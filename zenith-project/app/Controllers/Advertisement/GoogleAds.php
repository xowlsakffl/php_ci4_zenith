<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use CodeIgniter\CLI\CLI;

class GoogleAds extends BaseController
{
    private $chainsaw;

    public function __construct(...$param)
    {
        include APPPATH."/ThirdParty/googleads_api/adsapi.php";
        $this->chainsaw = new \GoogleAds();       
    }

    public function getAdsUseLanding()
    {
        CLI::clearScreen();
        CLI::write("유효DB 개수 업데이트를 진행합니다.", "light_red");
        $date = CLI::prompt("유효DB 개수를 수신할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->getAdsUseLanding($date);
        CLI::write("유효DB 개수 업데이트 완료", "yellow");
    }
}
