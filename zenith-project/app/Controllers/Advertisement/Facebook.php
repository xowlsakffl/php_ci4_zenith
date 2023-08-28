<?php
namespace App\Controllers\Advertisement;

use App\ThirdParty\facebook_api\ZenithFB;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Controller;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use DateInterval;
use DatePeriod;

class Facebook extends Controller
{
    private $zenith;

    public function __construct()
    {
        $this->zenith = new ZenithFB();       
    }

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        $date = "";
        $today = date('Y-m-d');
        $argv = @$request->getServer()['argv'];
        if(!is_null($argv)) {
            $method = $argv[2];
            $params = @array_slice($argv, 3);
            if(!@count($params)) return;
            foreach($params as $v) {
                $value = preg_replace('/[^0-9\-]+/', '', $v);
                if(preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value)) { //request argument에 날짜형식이 있는지 체크
                    $date = $v;
                    break;
                }
            }
            $hour = date("G"); //24-hour format of an hour without leading zeros
            if($date == $today && ($hour >= 0 && $hour <= 7)) {
                CLI::write("당일 0시~8시는 자동업데이트를 사용할 수 없습니다.", "light_purple");
                exit;
            }
        }
    }

    public function getAccounts() {
        $this->zenith->updateAdAccounts();
    }

    public function getLongLivedAccessToken() {
        $this->zenith->getLongLivedAccessToken();
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
        $this->zenith->getAsyncInsights($all, $date, $edate);
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
        $this->zenith->getAdLead($from, $to);
    }

    public function updateAll() {
        CLI::clearScreen();
        // $run = CLI::prompt("캠페인/광고그룹/광고를 업데이트 합니다.",["y","n"]);
        // if($run != 'y') return false;
        CLI::write("캠페인/광고그룹/광고를 업데이트 합니다.", "light_red");
        $this->zenith->updateAllByAccounts();
        /*
        $getAds = $this->zenith->getAds();
        $updateAdsets = $this->zenith->updateAdsets($getAds);
        $updateCampaigns = $this->zenith->updateCampaigns($updateAdsets);
        */
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
            $this->zenith->getAdsUseLanding($date);
        }
    }

    public function updateAds() {
        CLI::clearScreen();
        // $run = CLI::prompt("광고를 업데이트 합니다.",["y","n"]);
        // if($run != 'y') return false;
        CLI::write("광고를 업데이트 합니다.", "light_red");

        $this->zenith->updateAds();
    }
}