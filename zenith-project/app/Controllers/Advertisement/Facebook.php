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

    public function getInsight($all=null, $date=null, $edate=null) {
        CLI::clearScreen();
        if($all == null)
            $all = CLI::prompt("보고서를 수신 할 때 캠페인,광고그룹,광고 데이터를 함께 수신하시겠습니까?\n(시간이 오래 걸릴 수 있습니다.)", ["true","false"]);
        if($date == null)
            $date = CLI::prompt("보고서수신 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($edate == null)
            $edate = CLI::prompt("보고서수신 할 종료날짜를 입력해주세요.", $date);
        $run = CLI::prompt("광고데이터를 ".($all=="true"?"포함":"미포함")."하여 {$date}~{$edate} 기간의 인사이트를 수신합니다.",["y","n"]);
        if($run != 'y') return false;
        CLI::write("보고서 수신을 시작합니다.", "light_red");
        $this->chainsaw->getAsyncInsights($all, $date, $edate);
    }

    public function getAdLead($from = null, $to = null) {
        CLI::clearScreen();
        if($from == null)
            $from = CLI::prompt("잠재고객 업데이트 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($to == null)
            $to = CLI::prompt("잠재고객 업데이트 할 종료날짜를 입력해주세요.", date('Y-m-d'));
        $run = CLI::prompt("{$from}~{$to}일자의 잠재고객을 업데이트 합니다.",["y","n"]);
        if($run != 'y') return false;
        CLI::write("잠재고객을 업데이트합니다.", "light_red");
        $this->chainsaw->getAdLead($from, $to);
    }

    // 잠재 고객 작성 정보 불러오기
    public function getLeadgens($from = null, $to = null) {
        CLI::clearScreen();
        if($from == null)
            $from = CLI::prompt("잠재고객 작성정보를 업데이트 할 시작날짜를 입력해주세요.", date('Y-m-d'));
        if($to == null)
            $to = CLI::prompt("잠재고객 작성정보를 업데이트 할 종료날짜를 입력해주세요.", date('Y-m-d'));
        $run = CLI::prompt("잠재고객 작성정보를 업데이트 합니다.",["y","n"]);
        if($run != 'y') return false;
        CLI::write("잠재고객 작성정보를 업데이트 합니다.", "light_red");

        $this->chainsaw->getLeadgens($from, $to);
    }
    
    public function updateCampaignsAdsetsAds() {
        CLI::clearScreen();
        $run = CLI::prompt("캠페인을 업데이트 합니다.\n(시간이 오래 걸릴 수 있습니다.)",["y","n"]);
        if($run != 'y') return false;
        CLI::write("캠페인을 업데이트 합니다.", "light_red");

        $getAds = $this->chainsaw->getAds();
        $updateAdsets = $this->chainsaw->updateAdsets($getAds);
        $updateCampaigns = $this->chainsaw->updateCampaigns($updateAdsets);
    }

    public function updateAds() {
        CLI::clearScreen();
        $run = CLI::prompt("광고를 업데이트 합니다.\n(시간이 오래 걸릴 수 있습니다.)",["y","n"]);
        if($run != 'y') return false;
        CLI::write("광고를 업데이트 합니다.", "light_red");

        $this->chainsaw->updateAds();
    }
}