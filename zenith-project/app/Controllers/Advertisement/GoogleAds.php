<?php
namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\ThirdParty\googleads_api\ZenithGG;
use CodeIgniter\CLI\CLI;
use DateInterval;
use DatePeriod;

class GoogleAds extends BaseController
{
    private $chainsaw;

    public function __construct(...$param)
    {
        $this->chainsaw = new ZenithGG();       
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
        CLI::write("계정/계정예산/에셋/캠페인/그룹/소재/보고서 업데이트를 진행합니다.", "light_red");
        $this->chainsaw->getAll();
        CLI::write("계정/계정예산/에셋/캠페인/그룹/소재/보고서 업데이트 완료", "yellow");
    }

    public function updateDB($sdate = null, $edate = null) {
        CLI::clearScreen();
        if($sdate == null)
            $sdate = CLI::prompt("유효DB 업데이트 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("유효DB 업데이트 할 종료날짜를 입력해주세요.", $sdate);
        $sdate = date_create($sdate);
        $edate = date_create(date('Y-m-d', strtotime($edate.'+1 days')));
        $interval = DateInterval::createFromDateString('1 day');
        $date_range = new DatePeriod($sdate, $interval, $edate);
        foreach($date_range as $date) {
            $date = $date->format('Y-m-d');
            CLI::write("{$date} 유효DB를 업데이트 합니다.", "light_red");
            $this->chainsaw->getAdsUseLanding($date);
        }
    }

    public function getCampaign() {
        $campaigns = $this->chainsaw->getCampaigns('7523804427', '4023377096', '20686064765');
        dd($campaigns);
    }

    public function getAds() {
        $ads = $this->chainsaw->getAds('7523804427', '4023377096', '157627335009', '2023-11-20');
        dd($ads);
    }
}
