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

    //전체 광고계정 업데이트
    public function updateAdAccounts()
    { 
        CLI::clearScreen();
        CLI::write("전체 광고계정 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateAdAccounts();
        CLI::write("전체 광고계정 업데이트 완료", "yellow");
    }

    //전체 소재 보고서 BASIC 업데이트
    public function updateCreativesReportBasic()
    { 
        CLI::clearScreen();
        CLI::write("전체 소재 보고서 BASIC 업데이트를 진행합니다.", "light_red");
        $date = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->updateCreativesReportBasic($date);
        CLI::write("전체 소재 보고서 BASIC 업데이트 완료", "yellow");
    }
}
