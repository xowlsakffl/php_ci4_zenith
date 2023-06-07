<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvFacebookManagerModel;
use App\Models\Advertiser\AdvGoogleManagerModel;
use App\Models\Advertiser\AdvKakaoManagerModel;
use App\Models\Advertiser\AdvNaverManagerModel;
use CodeIgniter\API\ResponseTrait;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\moment_api\ZenithKM;

class AdvAllManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $facebook, $kakao, $google, $naver;
    private $campaign;
    public function __construct() 
    {
        $this->facebook = model(AdvFacebookManagerModel::class);
        $this->kakao = model(AdvKakaoManagerModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
        $this->naver = model(AdvNaverManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/manage');
    }

    public function getReport()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $arg['dates'] = [
                'sdate' => $arg['sdate'],
                'edate' => $arg['edate'],
            ];

            if(empty($arg['media'])){
                $result = [];
                return $this->respond($result);
            }

            $result = [];
            foreach ($arg['media'] as $media) {
                switch ($media) {
                    case 'facebook':
                        $res = $this->facebook->getReport($arg);
                        $result = array_merge($result, $res); 
                        break;
                    case 'kakao':
                        $res = $this->kakao->getReport($arg);
                        $result = array_merge($result, $res); 
                        break;
                    case 'google':
                        $res = $this->google->getReport($arg);
                        $result = array_merge($result, $res); 
                        break;
                    case 'naver':
                        $res = $this->naver->getReport($arg);
                        $result = array_merge($result, $res); 
                        break;
                    default:
                        return $this->fail("지원하지 않는 매체입니다.");
                }
            }

            $columnIndex = 0;
            $data = [];
            foreach($result as $row) {
                $data[] = $row;
                foreach ($row as $col => $val) {
                    if ($val == NULL) $val = "0";
                    $total[$col][$columnIndex] = $val;
                }
                $columnIndex++;
            }

            $report['impressions_sum'] = $report['clicks_sum'] = $report['click_ratio_sum'] = $report['spend_sum'] = $report['unique_total_sum'] = $report['unique_one_price_sum'] = $report['conversion_ratio_sum'] = $report['profit_sum'] = $report['per_sum'] = $report['price_sum'] = $report['spend_ratio_sum'] = 0;
    
            if(!empty($result)){
                $report['impressions_sum'] = array_sum($total['impressions']); //총 노출수
                $report['clicks_sum'] = array_sum($total['click']); //총 클릭수
                if ($report['clicks_sum'] != 0 && $report['impressions_sum'] != 0) {
                    $report['click_ratio_sum'] = round(($report['clicks_sum'] / $report['impressions_sum']) * 100, 2); //총 클릭률    
                }
                $report['spend_sum'] = array_sum($total['spend']); //총 지출액
                $report['spend_ratio_sum'] = floor(array_sum($total['spend']) * 0.85); //총 매체비
                if ($report['clicks_sum'] != 0) {
                    $report['cpc'] = round($report['spend_sum'] / $report['clicks_sum'], 2);
                } else {
                    $report['cpc'] = 0;
                }
                $report['unique_total_sum'] = array_sum($total['unique_total']); //총 유효db수
                if ($report['spend_sum'] != 0 && $report['unique_total_sum'] != 0) {
                    $report['unique_one_price_sum'] = round($report['spend_sum'] / $report['unique_total_sum'], 0); //총 db당 단가
                }
                if ($report['unique_total_sum'] != 0 && $report['clicks_sum'] != 0) {
                    $report['conversion_ratio_sum'] = round(($report['unique_total_sum'] / $report['clicks_sum']) * 100, 2); //총 전환율
                }
                $report['price_sum'] = array_sum($total['price']); //총 매출액
                $report['profit_sum'] = array_sum($total['profit']); //총 수익
                if ($report['profit_sum'] != 0 && $report['price_sum'] != 0) {
                    $report['per_sum'] = round(($report['profit_sum'] / $report['price_sum']) * 100, 2); //총 수익률
                }

                $report['impressions_sum'] = number_format($report['impressions_sum']);
                $report['clicks_sum'] = number_format($report['clicks_sum']);
                $report['spend_sum'] = number_format($report['spend_sum']);
                $report['unique_one_price_sum'] = number_format($report['unique_one_price_sum']);
                $report['spend_ratio_sum'] = number_format($report['spend_ratio_sum']);
                $report['price_sum'] = number_format($report['price_sum']);
            }
            return $this->respond($report);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $arg['searchData']['dates'] = [
                'sdate' => $arg['searchData']['sdate'],
                'edate' => $arg['searchData']['edate'],
            ];

            if(empty($arg['searchData']['media'])){
                $result = [];
                return $this->respond($result);
            }
            
            switch ($arg['searchData']['type']) {
                case 'ads':
                    $result = $this->getAds($arg);
                    break;
                case 'adsets':
                    $result = $this->getAdSets($arg);
                    break;
                case 'campaigns':
                    $result = $this->getCampaigns($arg);
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }
            
            foreach ($result['data'] as &$value) {
                $value['budget'] = number_format($value['budget']);
                $value['impressions'] = number_format($value['impressions']);
                $value['click'] = number_format($value['click']);
                $value['spend'] = number_format($value['spend']);
                $value['sales'] = number_format($value['sales']);
                $value['unique_total'] = number_format($value['unique_total']);
                $value['margin'] = number_format($value['margin']);
                $value['cpa'] = number_format($value['cpa']);
                $value['cpc'] = number_format($value['cpc']);
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getCampaigns($arg)
    {
        $result  = [];    
        foreach ($arg['searchData']['media'] as $media) {
            switch ($media) {
                case 'facebook':
                    $campaigns = $this->facebook->getCampaigns($arg);
                    $campaigns = $this->facebook->getStatuses("campaigns", $campaigns, $arg['searchData']['dates']);
                    $result = array_merge($result, $campaigns); 
                    break;
                case 'kakao':
                    $campaigns = $this->kakao->getCampaigns($arg);
                    $campaigns = $this->kakao->getStatuses("campaigns", $campaigns, $arg['searchData']['dates']);
                    $result = array_merge($result, $campaigns); 
                    break;
                case 'google':
                    $campaigns = $this->google->getCampaigns($arg);
                    $campaigns = $this->google->getStatuses("campaigns", $campaigns, $arg['searchData']['dates']);
                    foreach ($campaigns as &$campaign) {
                        $campaign['adType'] = '';
    
                        switch($campaign['advertisingChannelType']) {
                            case 'UNSPECIFIED' : $adType = ''; break;
                            case 'UNKNOWN' : $adType = ''; break;
                            case 'SEARCH' : $adType = '검색'; break;
                            case 'DISPLAY' : $adType = '디스플레이'; break;
                            case 'SHOPPING' : $adType = '쇼핑'; break;
                            case 'HOTEL' : $adType = '호텔'; break;
                            case 'VIDEO' : $adType = '비디오'; break;
                            case 'MULTI_CHANNEL' : $adType = '멀티채널'; break;
                            case 'LOCAL' : $adType = '지역'; break;
                            case 'SMART' : $adType = '스마트'; break;
                            case 'PERFORMANCE_MAX' : $adType = '성능최대'; break;
                            case 'LOCAL_SERVICES' : $adType = '지역서비스'; break;
                        }
                        switch($campaign['advertisingChannelSubType']) {
                            case 'UNSPECIFIED' : $adType .= ''; break;
                            case 'UNKNOWN' : $adType .= ''; break;
                            case 'SEARCH_MOBILE_APP' : $adType .= ' - 모바일 앱'; break;
                            case 'DISPLAY_MOBILE_APP' : $adType .= ' - 모바일 앱'; break;
                            case 'SEARCH_EXPRESS' : $adType .= ' - 익스프레스'; break;
                            case 'DISPLAY_EXPRESS' : $adType .= ' - 익스프레스'; break;
                            case 'SHOPPING_SMART_ADS' : $adType .= ' - 스마트 쇼핑'; break;
                            case 'DISPLAY_GMAIL_AD' : $adType .= ' - Gmail 광고'; break;
                            case 'DISPLAY_SMART_CAMPAIGN' : $adType .= ' - 스마트 디스플레이'; break;
                            case 'VIDEO_OUTSTREAM' : $adType .= ' - 아웃스트림'; break;
                            case 'VIDEO_ACTION' : $adType .= ' - 액션 뷰'; break;
                            case 'VIDEO_NON_SKIPPABLE' : $adType .= ' - 건너뛸 수 없는 동영상'; break;
                            case 'APP_CAMPAIGN' : $adType .= ' - 앱'; break;
                            case 'APP_CAMPAIGN_FOR_ENGAGEMENT' : $adType .= ' - 앱 참여유도'; break;
                            case 'LOCAL_CAMPAIGN' : $adType .= ' - 지역 광고'; break;
                            case 'SHOPPING_COMPARISON_LISTING_ADS' : $adType .= ' - 쇼핑 비교'; break;
                            case 'SMART_CAMPAIGN' : $adType .= ' - 표준 스마트'; break;
                            case 'VIDEO_SEQUENCE' : $adType .= ' - 시퀀스 비디오'; break;
                            case 'APP_CAMPAIGN_FOR_PRE_REGISTRATION' : $adType .= ' - 앱 사전 등록 광고'; break;
                        }
    
                        $campaign['adType'] = $adType;
                    }
                    $result = array_merge($result, $campaigns); 
                    break;
                case 'naver':
                    $campaigns = $this->naver->getCampaigns($arg);
                    $campaigns = $this->naver->getStatuses("campaigns", $campaigns, $arg['searchData']['dates']);
                    $result = array_merge($result, $campaigns); 
                    break;
                default:
                    return $this->fail("지원하지 않는 매체입니다.");
            }
        }

        $total = $this->getTotal($result);
        
        $result = [
            'total' => $total,
            'data' => $result,
        ];

        return $result;
    }

    private function getAdSets($arg)
    {
        $result  = [];    
        foreach ($arg['searchData']['media'] as $media) {
            switch ($media) {
                case 'facebook':
                    $adsets = $this->facebook->getAdsets($arg);
                    $adsets = $this->facebook->getStatuses("adsets", $adsets, $arg['searchData']['dates']);
                    $result = array_merge($result, $adsets); 
                    break;
                case 'kakao':
                    $adsets = $this->kakao->getAdsets($arg);
                    $adsets = $this->kakao->getStatuses("adsets", $adsets, $arg['searchData']['dates']);
                    $result = array_merge($result, $adsets); 
                    break;
                case 'google':
                    $adsets = $this->google->getAdsets($arg);
                    $adsets = $this->google->getStatuses("adsets", $adsets, $arg['searchData']['dates']);              
                    foreach ($adsets as &$adset) {
                        $adset['bidAmount'] = max([$adset['cpcBidAmount'],$adset['cpmBidAmount']]);
                        if($adset['biddingStrategyType'] == '타겟 CPA')
                            $adset['bidAmount'] = $adset['cpaBidAmount'];
                    }
                    $result = array_merge($result, $adsets); 
                    break;
                case 'naver':
                    $adsets = $this->naver->getAdsets($arg);
                    $adsets = $this->naver->getStatuses("adsets", $adsets, $arg['searchData']['dates']);
                    $result = array_merge($result, $adsets); 
                    break;
                default:
                    return $this->fail("지원하지 않는 매체입니다.");
            }
        }
        $total = $this->getTotal($result);

        $result = [
            'total' => $total,
            'data' => $result
        ];

        return $result;
    }

    private function getAds($arg)
    {
        $result  = [];    
        foreach ($arg['searchData']['media'] as $media) {
            switch ($media) {
                case 'facebook':
                    $ads = $this->facebook->getAds($arg);
                    $ads = $this->facebook->getStatuses("ads", $ads, $arg['searchData']['dates']);
                    $result = array_merge($result, $ads); 
                    break;
                case 'kakao':
                    $ads = $this->kakao->getAds($arg);
                    $ads = $this->kakao->getStatuses("ads", $ads, $arg['searchData']['dates']);
                    $result = array_merge($result, $ads); 
                    break;
                case 'google':
                    $ads = $this->google->getAds($arg);
                    $ads = $this->google->getStatuses("ads", $ads, $arg['searchData']['dates']);
                    $result = array_merge($result, $ads); 
                    break;
                case 'naver':
                    $ads = $this->naver->getAds($arg);
                    $ads = $this->naver->getStatuses("ads", $ads, $arg['searchData']['dates']);
                    $result = array_merge($result, $ads); 
                    break;
                default:
                    return $this->fail("지원하지 않는 매체입니다.");
            }
        }
        $total = $this->getTotal($result);

        $result = [
            'total' => $total,
            'data' => $result
        ];

        return $result;
    }

    private function getTotal($datas)
    {
        $total = [];
        $total['impressions'] = 0;
        $total['click'] = 0;
        $total['spend'] = 0;
        $total['margin'] = 0;
        $total['unique_total'] = 0;
        $total['sales'] = 0;
        $total['budget'] = 0;
        $total['cpc'] = 0;
        $total['ctr'] = 0;
        $total['cpa'] = 0;
        $total['cvr'] = 0;
        $total['margin_ratio'] = 0;
        $total['expect_db'] = 0;
        $total['avg_cpc'] = 0;
        $total['avg_cpa'] = 0;
        $total['avg_cvr'] = 0;
        foreach($datas as $data){
            $total['impressions'] +=$data['impressions'];
            $total['click'] +=$data['click'];
            $total['spend'] +=$data['spend'];
            $total['margin'] +=$data['margin'];
            $total['unique_total'] +=$data['unique_total'];
            $total['sales'] +=$data['sales'];
            $total['budget'] +=$data['budget'];
            $total['cpc'] +=$data['cpc'];
            $total['ctr'] +=$data['ctr'];
            $total['cpa'] +=$data['cpa'];
            $total['cvr'] +=$data['cvr'];
            $total['margin_ratio'] +=$data['margin_ratio'];

            //CPC(Cost Per Click: 클릭당단가 (1회 클릭당 비용)) = 지출액/링크클릭
            if($total['click'] > 0){
                $total['avg_cpc'] = $total['spend'] / $total['click'];
            }

            //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
            $total['avg_ctr'] = ($total['click'] / $total['impressions']) * 100;

            //CPA(Cost Per Action: 현재 DB단가(전환당 비용)) = 지출액/유효db
            if($total['unique_total'] > 0){
                $total['avg_cpa'] = $total['spend'] / $total['unique_total'];
            }

            //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
            if ($total['click'] > 0) {
                $total['avg_cvr'] = ($total['unique_total'] / $total['click']) * 100;
            }

            //수익률 = (수익/매출액)*100
            if ($total['sales'] > 0) {
                $total['avg_margin_ratio'] = ($total['margin'] / $total['sales']) * 100;
            } else {
                $total['avg_margin_ratio'] = 0;
            } 

            if ($data['status'] == 'ACTIVE' && $data['unique_total']){
                $total['expect_db'] += round($data['budget'] / $data['cpa']);
            }
        }

        $total['impressions'] = number_format($total['impressions']);
        $total['budget'] = number_format($total['budget']);
        $total['click'] = number_format($total['click']);
        $total['spend'] = number_format($total['spend']);
        $total['avg_cpa'] = number_format($total['avg_cpa']);
        $total['margin'] = number_format($total['margin']);
        $total['sales'] = number_format($total['sales']);
        $total['avg_cpc'] = number_format($total['avg_cpc']);

        return $total;
    }

    public function getAccounts()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $arg['dates'] = [
                'sdate' => $arg['sdate'],
                'edate' => $arg['edate'],
            ];

            if(empty($arg['media'])){
                $result = [];
                return $this->respond($result);
            }
            
            $result  = [];    
            foreach ($arg['media'] as $media) {
                switch ($media) {
                    case 'facebook':
                        $accounts = $this->facebook->getAccounts($arg);
                        $accounts = $this->updateAccountsForFacebook($accounts);
                        $result = array_merge($result, $accounts); 
                        break;
                    case 'kakao':
                        $accounts = $this->kakao->getAccounts($arg);
                        $accounts = $this->updateAccountsForKakao($accounts);
                        $result = array_merge($result, $accounts); 
                        break;
                    case 'google':
                        $accounts = $this->google->getManageAccounts($arg);
                        $accounts = $this->google->getAccounts($arg);
                        $accounts = $this->updateAccountsForGoogle($accounts);
                        $result = array_merge($result, $accounts); 
                        break;
                    case 'naver':
                        $accounts = $this->naver->getAccounts($arg);
                        $result = array_merge($result, $accounts); 
                        break;
                    default:
                        return $this->fail("지원하지 않는 매체입니다.");
                }
            }
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function updateAccountsForFacebook($accounts)
    {
        $getDisapprovalByAccount = $this->getDisapprovalByAccount('ad_account_id');
        foreach ($accounts as &$account) {
            $account['class'] = [];
            $account['db_ratio'] = 0;

            if ($account['status'] != 1) 
                array_push($account['class'], 'tag-inactive');
            if (in_array($account['id'], $getDisapprovalByAccount)) 
                array_push($account['class'], 'disapproval');

            $account['db_count'] = $account['db_count'] * $account['date_count'];

            if($account['db_sum'] && $account['db_count']) 
                $account['db_ratio'] = round($account['db_sum'] / $account['db_count'] * 100,1);

            if($account['db_ratio'] >= 100) { 
                $account['db_ratio'] = 100; 
                array_push($account['class'], 'over');
            }

            if(!$account['db_sum']) $account['db_sum'] = 0;    
        }

        return $accounts;
    }

    private function updateAccountsForKakao($accounts)
    {
        $getDisapprovalByAccount = $this->getDisapprovalByAccount('account_id');
        foreach ($accounts as &$account) {
            $account['class'] = [];
            if($account['config'] == 'OFF' || $account['isAdminStop'] == 1)
                array_push($account['class'], 'tag-inactive');
            if(is_array($getDisapprovalByAccount) && in_array($account['id'], $getDisapprovalByAccount))
                array_push($account['class'], 'disapproval');  
        }

        return $accounts;
    }

    private function updateAccountsForGoogle($accounts)
    {
        $getDisapprovalByAccount = $this->getDisapprovalByAccount('customerId');
        foreach ($accounts as &$account) {
        
            $account['class'] = [];
            $account['db_ratio'] = '';

            if($account['is_exposed'] === 0)
                array_push($account['class'], 'tag-inactive');
            if(in_array($account['id'], $getDisapprovalByAccount))
                array_push($account['class'], 'disapproval'); 
            
            $account['db_count'] = $account['db_count'] * $account['date_count'];

            if($account['db_sum'] && $account['db_count']) 
                $account['db_ratio'] = round($account['db_sum'] / $account['db_count'] * 100,1);
            
            if($account['db_ratio'] >= 100) { 
                $account['db_ratio'] = 100; 
                array_push($account['class'], 'over');
            }

            if(!$account['db_sum']) $account['db_sum'] = 0;
        }

        return $accounts;
    }

    private function getDisapprovalByAccount($id)
    {
        switch ($id) {
            case 'customerId':
                $disapprovals = $this->google->getDisapproval();
                break;
            case 'account_id':
                $disapprovals = $this->kakao->getDisapproval();
                break;
            case 'ad_account_id':
                $disapprovals = $this->facebook->getDisapproval();
                break;
            default:
                return $this->fail("잘못된 요청.");
        }

        $data = [];
        foreach ($disapprovals as $row) {
            $data[] = $row[$id];
        }
        $data = array_unique($data);

        return $data;
    }

    public function updateStatus()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'tab' => $param['tab'],
                'status' => $param['status'],
                'customerId' => $param['customerId'],
            ];

            switch ($data['tab']) {
                case 'ads':
                    switch ($data['media']) {
                        case 'facebook':
                            if($data['status'] === "ON"){
                                $status = 'ACTIVE';
                            }else{
                                $status = 'PAUSED';
                            }
                            $zenith = new ZenithFB();
                            $result = $zenith->setAdStatus($data['id'], $status);
                            break;
                        case 'kakao':
                            if($data['status'] === "ON"){
                                $status = 'ON';
                            }else{
                                $status = 'OFF';
                            }
                            $zenith = new ZenithKM();
                            $result = $zenith->setCreativeOnOff($data['id'], $data['status']);
                            break;
                        case 'google':
                            if($data['status'] === "ON"){
                                $param = ['status' => 'ENABLED'];
                            }else{
                                $param = ['status' => 'PAUSED'];
                            }
                            $zenith = new ZenithGG();
                            $result = $zenith->updateAdGroupAd($data['customerId'], null, $data['id'], $param);
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                case 'adsets':
                    switch ($data['media']) {
                        case 'facebook':
                            if($data['status'] === "ON"){
                                $status = 'ACTIVE';
                            }else{
                                $status = 'PAUSED';
                            }
                            $zenith = new ZenithFB();
                            $result = $zenith->setAdsetStatus($data['id'], $status);
                            break;
                        case 'kakao':
                            $zenith = new ZenithKM();
                            $result = $zenith->setAdGroupOnOff($data['id'], $data['status']);
                            break;
                        case 'google':
                            if($data['status'] === "ON"){
                                $param = ['status' => 'ENABLED'];
                            }else{
                                $param = ['status' => 'PAUSED'];
                            }
                            $zenith = new ZenithGG();
                            $result = $zenith->updateAdGroup($data['customerId'], $data['id'], $param);
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                case 'campaigns':
                    switch ($data['media']) {
                        case 'facebook':
                            if($data['status'] === "ON"){
                                $status = 'ACTIVE';
                            }else{
                                $status = 'PAUSED';
                            }
                            $zenith = new ZenithFB();
                            $result = $zenith->setCampaignStatus($data['id'], $status);
                            break;
                        case 'kakao':
                            $zenith = new ZenithKM();
                            $result = $zenith->setCampaignOnOff($data['id'], $data['status']);
                            break;
                        case 'google':
                            if($data['status'] === "ON"){
                                $param = ['status' => 'ENABLED'];
                            }else{
                                $param = ['status' => 'PAUSED'];
                            }
                            $zenith = new ZenithGG();
                            $result = $zenith->updateCampaign($data['customerId'], $data['id'], $param);
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            if(!empty($result)){
                $result['response'] = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateName()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'tab' => $param['tab'],
                'name' => $param['name'],
                'customerId' => $param['customerId'],
            ];

            $param = [
                'id' => $data['id'],
                'name' => $data['name'],
            ];
            
            switch ($data['tab']) {
                case 'ads':
                    switch ($data['media']) {
                        case 'facebook':
                            $param['type'] = $data['tab'];
                            $zenith = new ZenithFB();
                            $result = $zenith->updateName($param);
                            break;
                        case 'kakao':
                            $zenith = new ZenithKM();
                            $result = $zenith->setCreative($param, $data['customerId']);
                            break;
                        case 'google':
                            return $this->fail("지원하지 않는 매체입니다.");
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                case 'adsets':
                    switch ($data['media']) {
                        case 'facebook':
                            $param['type'] = $data['tab'];
                            $zenith = new ZenithFB();
                            $result = $zenith->updateName($param);
                            break;
                        case 'kakao':
                            $zenith = new ZenithKM();
                            $result = $zenith->setAdGroup($param, $data['customerId']);
                            break;
                        case 'google':
                            $zenith = new ZenithGG();
                            $result = $zenith->updateAdGroup($data['customerId'], $data['id'], $param);
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                case 'campaigns':
                    switch ($data['media']) {
                        case 'facebook':
                            $param['type'] = $data['tab'];
                            $zenith = new ZenithFB();
                            $result = $zenith->updateName($param);
                            break;
                        case 'kakao':
                            $zenith = new ZenithKM();
                            $result = $zenith->setCampaign($param, $data['customerId']);
                            break;
                        case 'google':
                            $zenith = new ZenithGG();
                            $result = $zenith->updateCampaign($data['customerId'], $data['id'], $param);
                            break;
                        default:
                            return $this->fail("지원하지 않는 매체입니다.");
                    }
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            if(!empty($result)){
                $result['response'] = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
 