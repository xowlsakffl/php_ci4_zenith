<?php
namespace App\Controllers\Advertisement;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Controller;

class Facebook extends Controller
{
    private $chainsaw;

    public function __construct(...$param)
    {
        include APPPATH."/ThirdParty/facebook_api/facebook-api.php";
        $this->chainsaw = new \ChainsawFB();       
    }

    public function getAccounts() {
        $this->chainsaw->updateAdAccounts();
    }

    public function getLongLivedAccessToken() {
        $this->chainsaw->getLongLivedAccessToken();
    }

    public function getInsight($all=null, $date=null, $edate=null) {
        CLI::clearScreen();
        if($all == null)
            $all = CLI::prompt("인사이트가 수신된 캠페인,광고그룹,광고 데이터를 함께 수신하시겠습니까?\n(시간이 오래 걸릴 수 있습니다.)", ["true","false"]);
        if($date == null)
            $date = CLI::prompt("인사이트를 수신할 기간 중 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("인사이트를 수신할 기간 중 종료날짜를 입력해주세요.", $date);
        // $run = CLI::prompt("광고데이터를 ".($all=="true"?"포함":"미포함")."하여 {$date} ~ {$edate} 기간의 인사이트를 수신합니다.",["y","n"]);
        // if($run != 'y') return false;
        $this->chainsaw->getAsyncInsights($all, $date, $edate);
        CLI::write("데이터 수신이 완료되었습니다.", "yellow");
    }

    public function getAdLead($from = null, $to = null) {
        CLI::clearScreen();
        if($from == null)
            $from = CLI::prompt("잠재고객 업데이트 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($to == null)
            $to = CLI::prompt("잠재고객 업데이트 할 종료날짜를 입력해주세요.", date('Y-m-d'));
        // $run = CLI::prompt("{$from}~{$to}일자의 잠재고객을 업데이트 합니다.",["y","n"]);
        // if($run != 'y') return false;
        $this->chainsaw->getAdLead($from, $to);
    }

    public function updateAll() {
        CLI::clearScreen();
        // $run = CLI::prompt("캠페인/광고그룹/광고를 업데이트 합니다.",["y","n"]);
        // if($run != 'y') return false;
        CLI::write("캠페인/광고그룹/광고를 업데이트 합니다.", "light_red");
        $this->chainsaw->updateAllByAccounts();
        /*
        $getAds = $this->chainsaw->getAds();
        $updateAdsets = $this->chainsaw->updateAdsets($getAds);
        $updateCampaigns = $this->chainsaw->updateCampaigns($updateAdsets);
        */
    }

    public function updateAds() {
        CLI::clearScreen();
        // $run = CLI::prompt("광고를 업데이트 합니다.",["y","n"]);
        // if($run != 'y') return false;
        CLI::write("광고를 업데이트 합니다.", "light_red");

        $this->chainsaw->updateAds();
    }
}