<?php
namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\ThirdParty\googleads_api\ZenithGG;
use CodeIgniter\CLI\CLI;
use Config\Paths;
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
        $result = $this->chainsaw->getAll();
        CLI::write("계정/계정예산/에셋/캠페인/그룹/소재/보고서 업데이트 완료", "yellow");
        // $paths = new Paths();
        // $log_file = fopen($paths->writableDirectory . '/logs/GoogleAdsGetAll.txt', 'a');
        // fwrite($log_file, $result . "\r\n\r\n");
        // fclose($log_file);
    }
    public function getAdSchedules() {
        $result = $this->chainsaw->getAdSchedules();
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
        $campaigns = $this->chainsaw->getCampaigns('7792262348', '1705230747', '20870581914');
        dd($campaigns);
    }

    public function getCriterion() {
        // $campaigns = $this->chainsaw->getCriterions('7792262348', '1705230747', '20870581914');
        // $campaigns = $this->chainsaw->getCriterions('7792262348', '8023466215', '20999659346');
        $campaigns = $this->chainsaw->getCriterions('7792262348', '9049844350', '20979863645');
        dd($campaigns);
    }

    public function getAdGroups() {
        $adgroups = $this->chainsaw->getAdGroups('5980790227', '3931611101', '20581900068');
        dd($adgroups);
    }

    public function getAds() {
        $ads = $this->chainsaw->getAds('5980790227', '3288458378', '130392657290', '2023-12-22');
        dd($ads);
    }

    public function getData() {
        $getAll = $this->chainsaw->getAll(null, [['manageCustomer'=>7177486093, 'customerId'=>9604111811]]);
        dd($getAll);
    }
}
