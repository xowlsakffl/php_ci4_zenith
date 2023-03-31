<?php

namespace App\Controllers\Advertisement;

use CodeIgniter\CLI\CLI;
use App\Controllers\BaseController;

class KakaoMoment extends BaseController
{
    private $chainsaw;

    public function __construct(...$param)
    {
        include APPPATH."/ThirdParty/moment_api/kakao-api.php";
        $this->chainsaw = new \ChainsawKM();       
    }

    //토큰 업데이트
    public function refresh_token()
    { 
        CLI::clearScreen();
        CLI::write("토큰 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->refresh_token();
        CLI::write("토큰 업데이트 완료", "yellow");
    }

    //전체 광고계정 업데이트
    public function updateAdAccounts()
    {
        CLI::clearScreen();
        CLI::write("전체 광고계정 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateAdAccounts();
        CLI::write("전체 광고계정 업데이트 완료", "yellow");
    }

    //캠페인 데이터 업데이트
    public function updateCampaigns()
    { 
        CLI::clearScreen();
        CLI::write("캠페인 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateCampaigns();
        CLI::write("캠페인 업데이트 완료", "yellow");
    }

    //광고그룹 데이터 업데이트
    public function updateAdGroups()
    { 
        CLI::clearScreen();
        CLI::write("광고그룹 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateAdGroups();
        CLI::write("광고그룹 업데이트 완료", "yellow");
    }

    //소재 데이터 업데이트
    public function updateCreatives()
    { 
        CLI::clearScreen();
        CLI::write("소재 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateCreatives();
        CLI::write("소재 데이터 업데이트 완료", "yellow");
    }

    //전체 소재 보고서 BASIC 업데이트
    public function updateReportByAdgroup($date = null)
    { 
        CLI::clearScreen();
        CLI::write("소재 보고서 업데이트를 진행합니다.", "light_red");
        if(is_null($date))
            $date = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.(ex. ".date('Y-m-d', strtotime('-1 day')).")", 'TODAY');
        $this->chainsaw->updateCreativesReportBasic($date);
        CLI::write("소재 보고서 업데이트 완료", "yellow");
    }

    public function updateReportByHour($date = null)
    { 
        CLI::clearScreen();
        CLI::write("소재 보고서 업데이트를 진행합니다.", "light_red");
        if(is_null($date))
            $date = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.(ex. ".date('Y-m-d', strtotime('-1 day')).")", 'TODAY');
        $this->chainsaw->updateHourReportBasic($date);
        CLI::write("소재 보고서 업데이트 완료", "yellow");
    }

    public function getAll() {
        $this->chainsaw->updateAdAccounts();
        $this->chainsaw->updateCampaigns();
        $this->chainsaw->updateAdGroups();
        $this->chainsaw->updateCreatives();
        $this->chainsaw->updateBizform();
    }
    

    //비즈폼 데이터 업데이트
    public function updateBizform()
    { 
        CLI::clearScreen();
        CLI::write("비즈폼 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->updateBizform();
        CLI::write("비즈폼 데이터 업데이트 완료", "yellow");
    }

    //app_subscribe 데이터 업데이트
    public function moveToAppsubscribe()
    { 
        CLI::clearScreen();
        CLI::write("app_subscribe 데이터 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->moveToAppsubscribe();
        CLI::write("app_subscribe 데이터 업데이트 완료", "yellow");
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

    //소재 자동 변경
    public function autoCreativeOnOff()
    { 
        CLI::clearScreen();
        CLI::write("소재 자동 변경을 진행합니다.", "light_red");
        $check = CLI::prompt("전체 소재 보고서 BASIC 수신할 날짜를 입력해주세요.", ['on', 'off']);
        $this->chainsaw->autoCreativeOnOff();
        CLI::write("소재 자동 변경 완료", "yellow");
    }

    //자동 입찰가한도 리셋
    public function autoLimitBidAmountReset()
    { 
        CLI::clearScreen();
        CLI::write("자동 입찰가한도 리셋을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 입찰가한도 리셋할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBidAmountReset($date);
        CLI::write("자동 입찰가한도 리셋 완료", "yellow");
    }

    //자동 입찰가 설정
    public function autoLimitBidAmount()
    { 
        CLI::clearScreen();
        CLI::write("자동 입찰가한도 설정을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 입찰가한도 설정할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBidAmount($date);
        CLI::write("자동 입찰가한도 설정 완료", "yellow");
    }

    //자동 예산한도 리셋
    public function autoLimitBudgetReset()
    { 
        CLI::clearScreen();
        CLI::write("자동 예산한도 리셋을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 예산한도 리셋할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBudgetReset($date);
        CLI::write("자동 예산한도 리셋 완료", "yellow");
    }

    //자동 예산한도 설정
    public function autoLimitBudget()
    { 
        CLI::clearScreen();
        CLI::write("자동 예산한도 설정을 진행합니다.", "light_red");
        $date = CLI::prompt("자동 예산한도 설정할 날짜를 입력해주세요.", date('Y-m-d'));
        $this->chainsaw->autoLimitBudget();
        CLI::write("자동 예산한도 설정 완료", "yellow");
    }

    //광고그룹 AI 업데이트
    public function setAdGroupsAiRun()
    { 
        CLI::clearScreen();
        CLI::write("광고그룹 AI 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->setAdGroupsAiRun();
        CLI::write("광고그룹 AI 업데이트 완료", "yellow");
    }

    //AI 자동켜기
    public function autoAiOn()
    { 
        CLI::clearScreen();
        CLI::write("AI를 시작합니다.", "light_red");
        $this->chainsaw->autoAiOn();
        CLI::write("AI 시작", "yellow");
    }
}
