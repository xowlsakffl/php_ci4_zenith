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

    //유효DB 개수 업데이트
    public function getCreativesUseLanding()
    { 
        CLI::clearScreen();
        CLI::write("유효DB 개수 업데이트를 진행합니다.", "light_red");
        $date = CLI::prompt("유효DB 개수 수신할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->getCreativesUseLanding($date);
        CLI::write("유효DB 개수 업데이트 완료", "yellow");
    }

    //리포트데이터를 업데이트
    public function updateReportByDate($sdate=null, $edate=null)
    { 
        CLI::clearScreen();
        CLI::write("리포트데이터를 업데이트를 진행합니다.", "light_red");
        if($sdate == null)
            $sdate = CLI::prompt("리포트데이터를 수신할 기간 중 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("리포트데이터를 수신할 기간 중 종료날짜를 입력해주세요.", $sdate);
        $this->chainsaw->updateReportByDate($sdate, $edate);
        CLI::write("리포트데이터 업데이트 완료", "yellow");
    }

    /* 
    $step = 1;
    CLI::write("[".date("Y-m-d H:i:s")."]"."전체 소재 보고서 BASIC  수신을 시작합니다.", "light_red");
    CLI::showProgress($step++, $total); 
    */
}
