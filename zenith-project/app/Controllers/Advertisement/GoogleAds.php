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

    public function getAccounts()
    {
        CLI::clearScreen();
        CLI::write("광고계정 수신을 진행합니다.", "light_red");
        $this->chainsaw->getAccounts();
        CLI::write("광고계정 업데이트 완료", "yellow");
    }

    public function getAll()
    {
        CLI::clearScreen();
        CLI::write("계정 데이터 업데이트를 진행합니다.", "light_red");
        $date = CLI::prompt("광고 데이터를 수신할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->getAll($date);
        CLI::write("계정 데이터 업데이트 완료", "yellow");
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
