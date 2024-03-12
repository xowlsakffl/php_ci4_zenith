<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Libraries\Calc;
use App\Models\Advertiser\AdvFacebookManagerModel;
use App\Models\Advertiser\AdvGoogleManagerModel;
use App\Models\Advertiser\AdvKakaoManagerModel;
use App\Models\Advertiser\AdvManagerModel;
use App\Services\AdvLoggerService;
use CodeIgniter\API\ResponseTrait;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\moment_api\ZenithKM;

class AdvManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $admanager, $facebook, $google, $kakao;
    private $campaign;
    public function __construct() 
    {
        $this->admanager = model(AdvManagerModel::class);
        $this->facebook = model(AdvFacebookManagerModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
        $this->kakao = model(AdvKakaoManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/manage');
    }

    public function getData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
	
			if(getenv('MY_SERVER_NAME') === 'resta' && isset($arg['searchData']['carelabs']) && $arg['searchData']['carelabs'] == 1) {
				$arg['external'] = 'resta';
				return $this->getCareLabsData($arg);
			}
            if(!empty($arg['searchData']['account']) || 
            !empty($arg['searchData']['company']) || 
            !empty($arg['searchData']['media']) ||
            !empty($arg['searchData']['stx'])) {
                // print_r($arg['searchData']);
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
				$orderBy = [];
				if(!empty($arg['order'])) {
					foreach($arg['order'] as $row) {
						if($row['dir'] == 'desc'){
							$sort = SORT_DESC;
						}else{
							$sort = SORT_ASC;
						}
						$col = $arg['columns'][$row['column']]['data'];
						if($col) $orderBy[$col] = $sort;
					}
					array_sort_by_multiple_keys($result['data'], $orderBy);
				}

				foreach ($result['data'] as &$value) {
                    $value['campaign_bidamount'] = number_format($value['campaign_bidamount'] ?? 0);
                    $value['bidamount'] = number_format($value['bidamount']);
					$value['budget'] = number_format($value['budget']);
					$value['impressions'] = number_format($value['impressions']);
					$value['click'] = number_format($value['click']);
					$value['spend'] = number_format($value['spend']);
					$value['sales'] = number_format($value['sales']);
					$value['unique_total'] = number_format($value['unique_total']);
					$value['margin'] = number_format($value['margin']);
					$value['margin_ratio'] = number_format($value['margin_ratio']);
					$value['cpa'] = number_format($value['cpa']);
					$value['cpc'] = number_format($value['cpc']);
				}
				if(isset($arg['noLimit'])) {
					return $this->respond($result['data']);
				}
            }
            $result['report'] = $this->getReport($arg);
            $result['accounts'] = $this->getAccounts($arg);
            $result['media_accounts'] = $this->getMediaAccounts($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getAccounts($arg)
    {  
        $result = $this->admanager->getAccounts($arg);
        return $result;
    }

    private function getMediaAccounts($arg)
    { 
        $result = $this->admanager->getMediaAccounts($arg);
        $accounts = $this->setAccountData($result);
        return $accounts;
    }

    private function setAccountData($accounts)
    {
        $facebookApprovals = [];
        $googleApprovals = [];
        $kakaoApprovals = [];
        if(array_search('facebook', array_column($accounts, 'media')) !== false){
            $facebookApprovals = $this->facebook->getDisapproval();
        }

        if(array_search('google', array_column($accounts, 'media')) !== false){
            $disapprovalGoogle = $this->google->getDisapproval();   
            foreach ($disapprovalGoogle as $row) {
                $policyTopic = [];
                if(!empty($row['policyTopic'])) $policyTopic = explode(',', $row['policyTopic']);
                if($row['approvalStatus'] == 'APPROVED_LIMITED' && in_array('YOUTUBE_AD_REQUIREMENTS_AUTOMATED_CONTENT_POLICY_DECISION', $policyTopic)) {
                    $row['approvalStatus'] = 'DISAPPROVED';
                } else if($row['approvalStatus'] == 'APPROVED' && in_array('HEALTH_IN_PERSONALIZED_ADS', $policyTopic)) {
                    $row['approvalStatus'] = 'APPROVED_LIMITED';
                }
                if(!isset($googleApprovals[$row['approvalStatus']])) $googleApprovals[$row['approvalStatus']] = [];
                if(!in_array($row['customerId'], $googleApprovals[$row['approvalStatus']]))
                    $googleApprovals[$row['approvalStatus']][] = $row['customerId'];
            }
        }

        if(array_search('kakao', array_column($accounts, 'media')) !== false){
            $kakaoApprovals = $this->kakao->getDisapproval();
        }

        foreach ($accounts as &$account) {
            if($account['media'] == 'facebook'){
                $account['db_ratio'] = 0;
                if ($account['status'] != 1){
                    $account['tag_inactive'] = 'tag_inactive';
                }
                if (in_array($account['media_account_id'], $facebookApprovals)){
                    $account['disapproval'] = 'disapproval';
                } 

                $account['db_count'] = $account['db_count'] * $account['date_count'];
    
                if($account['db_sum'] && $account['db_count']){
                    $account['db_ratio'] = round($account['db_sum'] / $account['db_count'] * 100,1);
                }
    
                if($account['db_ratio'] >= 100) { 
                    $account['db_ratio'] = 100; 
                    $account['over'] = 'over';
                }
    
                if(!$account['db_sum']){
                    $account['db_sum'] = 0;  
                }  

                continue;
            }
            
            if($account['media'] == 'google'){
                $account['db_ratio'] = 0;
                if($account['is_exposed'] === 0){
                    $account['tag_inactive'] = 'tag_inactive';
                }
                
                if((isset($googleApprovals['DISAPPROVED']) && in_array($account['media_account_id'], $googleApprovals['DISAPPROVED'])) ||
                (isset($googleApprovals['AREA_OF_INTEREST_ONLY']) && in_array($account['media_account_id'], $googleApprovals['AREA_OF_INTEREST_ONLY']))) 
                {
                    $account['disapproval'] = 'disapproval';
                }

                if(isset($googleApprovals['APPROVED_LIMITED']) && in_array($account['media_account_id'], $googleApprovals['APPROVED_LIMITED']))
                {
                    $account['approved_limited'] = 'approved_limited';
                }
                
                $account['db_count'] = $account['db_count'] * $account['date_count'];

                if($account['db_sum'] && $account['db_count']){
                    $account['db_ratio'] = round($account['db_sum'] / $account['db_count'] * 100,1);
                }

                if($account['db_ratio'] >= 100) { 
                    $account['db_ratio'] = 100; 
                    $account['over'] = 'over';
                }

                if(!$account['db_sum']){
                    $account['db_sum'] = 0;
                }

                continue;
            }

            if($account['media'] == 'kakao'){
                if($account['status'] == 'OFF' || (isset($account['isAdminStop']) && $account['isAdminStop'] == 1)){
                    $account['tag_inactive'] = 'tag_inactive';
                }
                if(is_array($kakaoApprovals) && in_array($account['media_account_id'], $kakaoApprovals)){
                    $account['disapproval'] = 'disapproval';
                }

                continue;
            }
        }

        return $accounts;
    }
    
    public function getCheckData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result['report'] = $this->getReport($arg);
            $result['account'] = $this->getAccounts($arg);
            $result['media_accounts'] = $this->getMediaAccounts($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getDiffReport(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $currentDate = date("Y-m-d");
            $arg['searchData']['sdate'] = $currentDate;
            $arg['searchData']['edate'] = $currentDate;
            $result['today'] = $this->getReport($arg);

            $arg['searchData']['sdate'] = date("Y-m-d", strtotime("-1 days", strtotime($currentDate)));
            $arg['searchData']['edate'] = date("Y-m-d", strtotime("-1 days", strtotime($currentDate)));

            $result['yesterday'] = $this->getReport($arg);
            
            switch ($arg['searchData']['diff']) {
                case '7days':
                    $arg['searchData']['sdate'] = date("Y-m-d", strtotime("-1 week"));
                    $arg['searchData']['edate'] = $currentDate;
                break;
                case '14days':
                    $arg['searchData']['sdate'] = date("Y-m-d", strtotime("-2 week"));
                    $arg['searchData']['edate'] = $currentDate;
                break;
                case '30days':
                    $arg['searchData']['sdate'] = date("Y-m-d", strtotime("-1 month"));
                    $arg['searchData']['edate'] = $currentDate;
                break;
                case 'prevmonth':
                    $arg['searchData']['sdate'] = date("Y-m-01", strtotime("-1 month", strtotime($currentDate)));
                    $arg['searchData']['edate'] = date("Y-m-t", strtotime("-1 month", strtotime($currentDate)));
                break;
                case 'thismonth':
                    $arg['searchData']['sdate'] = date("Y-m-01", strtotime($currentDate));
                    $arg['searchData']['edate'] = $currentDate;
                break;
                default:
                break;
            }
            
            $result['customDate'] = $this->getReport($arg);
            $result['customDate']['date'] = [
                'sdate' => $arg['searchData']['sdate'],
                'edate' => $arg['searchData']['edate'],
            ];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getReport($arg)
    {
        $result = $this->admanager->getReport($arg);
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
        
        $report['impressions_sum'] = $report['clicks_sum'] = $report['click_ratio_sum'] = $report['spend_sum'] = $report['unique_total_sum'] = $report['unique_one_price_sum'] = $report['conversion_ratio_sum'] = $report['profit_sum'] = $report['per_sum'] = $report['price_sum'] = $report['spend_ratio_sum'] =$report['cpc'] = 0;

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
            $report['profit_sum'] = number_format($report['profit_sum']);
        }

        return $report;
    }

    public function getOnlyAdAccount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $param = $this->request->getGet();
            $result = $this->admanager->getOnlyAdAccount($param);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getAdvs()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $param = $this->request->getGet();
            
            $result = $this->admanager->getAdvs($param);
            foreach ($result as &$row) {
                if(isset($row['status']) && $row['status'] == 1 || $row['status'] == 'ON' || $row['status'] == 'ENABLED' || $row['status'] == 'ACTIVE'){
                    $row['status'] = '활성';
                }else{
                    $row['status'] = '비활성';
                }
            }
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getCampaigns($arg)
    {
        $campaigns = $this->admanager->getCampaigns($arg);
		if(!empty($campaigns)){
			$campaigns = $this->setData($campaigns);
            $total = $this->getTotal($campaigns);
        
            $result = [
                'total' => $total,
                'data' => $campaigns,
            ];

            return $result;
		}else{
			return false;
		}
    }

    private function getAdSets($arg)
    {
        $result  = [];    
        $adsets = $this->admanager->getAdSets($arg);
		if(!empty($adsets)){
			$adsets = $this->setData($adsets);
            $result = array_merge($result, $adsets); 

            $total = $this->getTotal($result);
            
            $result = [
                'total' => $total,
                'data' => $result,
            ];

            return $result;
		}else{
			return false;
		}
    }

    private function getAds($arg)
    {
        $result  = [];    
        $ads = $this->admanager->getAds($arg);
		if(!empty($ads)){
			$ads = $this->setData($ads);
            $result = array_merge($result, $ads); 

            foreach ($result as &$row) {
                $row['class'] = [];
                if(!empty($row['status'])){
                    if ($row['status'] != 'ON') {
                        $row['class'][] = 'off';
                    }
                }

                if(!empty($row['approval_status'])){
                    $policyTopic = [];
                    if(!empty($row['policyTopic'])){
                        $policyTopic = explode(',', $row['policyTopic']);
                    }
                    if ($row['approval_status'] == 'DISAPPROVED' || $row['approval_status'] == 'REJECTED' || $row['approval_status'] == 'AREA_OF_INTEREST_ONLY' || ($row['approval_status'] == 'APPROVED_LIMITED' && in_array('YOUTUBE_AD_REQUIREMENTS_AUTOMATED_CONTENT_POLICY_DECISION', $policyTopic))) {
                        $row['class'][] = 'disapproval';
                    }else if($row['approval_status'] == 'APPROVED_LIMITED' || ($row['approval_status'] == 'APPROVED' && in_array('HEALTH_IN_PERSONALIZED_ADS', $policyTopic))){
                        $row['class'][] = 'approved_limited';
                    }
                }

                $row['class'] = implode(" ", $row['class']);
            }
            $total = $this->getTotal($result);
            $result = [
                'total' => $total,
                'data' => $result,
            ];

            return $result;
		}else{
			return false;
		}
    }

    private function setData($result)
    {
        foreach ($result as &$row) {
            if($row['media'] == 'google' && isset($row['thumbnail'])){
                if (!empty($row['assets'])){
                    $assets = $this->google->getAsset($row['assets']);
                    $row['thumbnail'] = $assets['url'];
                }
            }
			$row['status'] = $this->setStatus($row['status']);
			
            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률

			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율

            if($row['bidamount_type'] == 'cpm' && $row['bidamount'] <= 1){
                $row['bidamount_type'] = '';
                $row['bidamount'] = 0;
            }

            if(!empty($row['biddingStrategyType'])){
                switch($row['biddingStrategyType']) {
                    case 'TARGET_CPA' :
                        $row['biddingStrategyType'] = '타겟 CPA';
                        break;
                    case 'TARGET_ROAS' :
                        $row['biddingStrategyType'] = '타겟 광고 투자수익(ROAS)';
                        break;
                    case 'TARGET_SPEND' :
                        $row['biddingStrategyType'] = '클릭수 최대화';
                        break;
                    case 'MAXIMIZE_CONVERSIONS' :
                        $row['biddingStrategyType'] = '전환수 최대화';
                        break;
                    /* //값이 뭔지 모름ㅠㅠ
                    case '' :
                        $row['biddingStrategyType'] = '검색 결과 위치 타겟';
                        break;
                    case '' :
                        $row['biddingStrategyType'] = '경쟁 광고보다 내 광고가 높은 순위에 게재되는 비율 타겟';
                        break;
                    case '' :
                        $row['biddingStrategyType'] = '타겟 노출 점유율';
                        break;
                    */
                    case 'PAGE_ONE_PROMOTED' :
                        $row['biddingStrategyType'] = '향상된 CPC 입찰기능';
                        break;
                    case 'MANUAL_CPM' :
                        $row['biddingStrategyType'] = '수동 입찰 전략';
                        break;
                    case 'MANUAL_CPC' :
                        $row['biddingStrategyType'] = '수동 CPC';
                        break;
                    case 'UNKNOWN' :
                        $row['biddingStrategyType'] = '알수없음';
                        break;
                    default :
                        break;
                }
            }
        }
        return $result;
    }

    private function setStatus($status)
    {
        /*
        ON = 인정,
        READY = 집행 예정,
        FINISHED = 집행 완료,
        STOPPED = 정지,
        ARCHIVED = 보관,
        DELETED = 삭제
        UNKNOWN = 알수 없음
        UNSPECIFIED = 명시되지 않음
        EXCEED_DAILY_BUDGET = 일 예산 초과
        NO_AVAILABLE_CREATIVE = 집행 가능한 소재가 없음
        CANCELED = 계약 해지
        SYSTEM_CONFIG_EXTERNAL_SERVICE_STOP = 연결 서비스 제한으로 운영불가인 상태
        ADACCOUNT_UNAVAILABLE = 광고계정 운영불가
        CAMPAIGN_UNAVAILABLE = 캠페인 운영불가
        ADGROUP_UNAVAILABLE = 광고 상위단위인 광고그룹이 운영불가인 상태
        SYSTEM_CONFIG_ADMIN_STOP = 관리자 정지
        OPERATING = 운영가능 상태
        UNAPPROVED = 심사승인이 아닌 상태(심사중, 심사보류를 모두 포함)
        INVALID_DATE = 집행기간이 도래하지 않았거나 이미 지난 상태
        MONITORING_REJECTED = 관리자정지 상태
        SYSTEM_CONFIG_VOID = 소재 콘텐츠 오류로 소재 운영불가인 상태
        */
        if(!empty($status)){
            if(in_array($status, ['ENABLED', 'ACTIVE', 'LIVE', 'ON'])){$status = 'ON';}
            else if(in_array($status, ['READY'])){$status = 'READY';}
            else if(in_array($status, ['FINISHED'])){$status = 'FINISHED';}
            else if(in_array($status, ['OFF', 'PAUSED'])){$status = 'OFF';}
            else if(in_array($status, ['ARCHIVED'])){$status = 'ARCHIVED';}
            else if(in_array($status, ['DELETED', 'REMOVED'])){$status = 'DELETED';}
            else if(in_array($status, ['UNKNOWN'])){$status = 'UNKNOWN';}
            else if(in_array($status, ['UNSPECIFIED'])){$status = 'UNSPECIFIED';}
            else if(in_array($status, ['EXCEED_DAILY_BUDGET'])){$status = 'EXCEED_DAILY_BUDGET';}
            else if(in_array($status, ['NO_AVAILABLE_CREATIVE'])){$status = 'NO_AVAILABLE_CREATIVE';}
            else if(in_array($status, ['CANCELED'])){$status = 'CANCELED';}
            else if(in_array($status, ['SYSTEM_CONFIG_EXTERNAL_SERVICE_STOP'])){$status = 'SYSTEM_CONFIG_EXTERNAL_SERVICE_STOP';}
            else if(in_array($status, ['ADACCOUNT_UNAVAILABLE'])){$status = 'ADACCOUNT_UNAVAILABLE';}
            else if(in_array($status, ['CAMPAIGN_UNAVAILABLE'])){$status = 'CAMPAIGN_UNAVAILABLE';}
            else if(in_array($status, ['SYSTEM_CONFIG_ADMIN_STOP'])){$status = 'SYSTEM_CONFIG_ADMIN_STOP';}
            else if(in_array($status, ['OPERATING'])){$status = 'OPERATING';}
            else if(in_array($status, ['UNAPPROVED'])){$status = 'UNAPPROVED';}
            else if(in_array($status, ['INVALID_DATE'])){$status = 'INVALID_DATE';}
            else if(in_array($status, ['MONITORING_REJECTED'])){$status = 'MONITORING_REJECTED';}
            else if(in_array($status, ['ADGROUP_UNAVAILABLE'])){$status = 'ADGROUP_UNAVAILABLE';}
            else if(in_array($status, ['SYSTEM_CONFIG_VOID'])){$status = 'SYSTEM_CONFIG_VOID';}
            else{$status = 'ETC';};
        }else{
            $status = NULL;
        };

        return $status;
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
        $total['bidamount'] = 0;
        $total['cpc'] = 0;
        $total['ctr'] = 0;
        $total['cpa'] = 0;
        $total['cvr'] = 0;
        $total['margin_ratio'] = 0;
        $total['expect_db'] = 0;
        $total['avg_cpc'] = 0;
        $total['avg_cpa'] = 0;
        $total['avg_cvr'] = 0;
        $total['avg_ctr'] = 0;
        foreach($datas as $data){
            $total['impressions'] +=$data['impressions'];
            $total['click'] +=$data['click'];
            $total['spend'] +=$data['spend'];
            $total['margin'] +=$data['margin'];
            $total['unique_total'] +=$data['unique_total'];
            $total['sales'] +=$data['sales'];
            $total['bidamount'] +=$data['bidamount'];
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
            if($total['impressions'] > 0){
                $total['avg_ctr'] = ($total['click'] / $total['impressions']) * 100;
            }
            
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

            if ($data['status'] == 'ON' && $data['unique_total'] && $data['cpa'] != 0){
                $total['expect_db'] += round($data['budget'] / $data['cpa']);
            }
        }

        $total['impressions'] = number_format($total['impressions']);
        $total['bidamount'] = number_format($total['bidamount']);
        $total['budget'] = number_format($total['budget']);
        $total['click'] = number_format($total['click']);
        $total['spend'] = number_format($total['spend']);
        $total['avg_cpa'] = number_format($total['avg_cpa']);
        $total['margin'] = number_format($total['margin']);
        $total['sales'] = number_format($total['sales']);
        $total['avg_cpc'] = number_format($total['avg_cpc']);

        return $total;
    }

    public function setDbCount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'db_count' => $param['db_count']
            ];

            switch ($data['media']) {
                case 'facebook':
                    $result = $this->facebook->updateDbCount($data);
                    break;
                case 'google':
                    $result = $this->google->updateDbCount($data);
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setExposed()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput();
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'is_exposed' => $param['is_exposed']
            ];

            switch ($data['media']) {
                case 'google':
                    $result = $this->google->updateExposed($data);
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
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
                'old_status' => $param['old_status'],
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
                $logData = [
                    'media' => $data['media'] ?? '',
                    'id' => $data['id'] ?? '',
                    'change_type' => 'status',
                    'old_value' => $data['old_status'] ?? '',
                    'change_value' => $data['status'] ?? '',
                    'nickname' => auth()->user()->username ?? '',
                ];
        
                $logger = new AdvLoggerService();
                $logger->insertLog($logData);

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
                'old_name' => $param['old_name'],
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
                            return $this->fail("지원하지 않는 매체입니다.");
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
                $logData = [
                    'media' => $data['media'] ?? '',
                    'id' => $data['id'] ?? '',
                    'change_type' => 'name',
                    'old_value' => $data['old_name'] ?? '',
                    'change_value' => $data['name'] ?? '',
                    'nickname' => auth()->user()->username ?? '',
                ];
        
                $logger = new AdvLoggerService();
                $logger->insertLog($logData);

                $result['response'] = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateBudget()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput(); 
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'customer' => $param['customer'],
                'tab' => $param['tab'],
                'budget' => $param['budget'],
                'old_budget' => $param['old_budget']
            ];

            switch ($data['tab']) {
                case 'ads':
                    return $this->fail("지원하지 않습니다.");
                    break;
                case 'adsets':
                    switch ($data['media']) {
                        case 'facebook':
                            $zenith = new ZenithFB();
                            $result = $zenith->updateAdSetBudget($data);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'facebook'
                                ];
                            }
                            break;
                        case 'kakao':
                            $data['type'] = 'adgroup';
                            $zenith = new ZenithKM();
                            $result = $zenith->setDailyBudgetAmount($data);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'kakao'
                                ];
                            }
                            break;
                        case 'google':
                            return $this->fail("지원하지 않습니다.");
                            break;
                        default:
                        return $this->fail("지원하지 않습니다.");
                    }
                    break;
                case 'campaigns':
                    switch ($data['media']) {
                        case 'facebook':
                            $zenith = new ZenithFB();
                            $result = $zenith->updateCampaignBudget($data);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'facebook'
                                ];
                            }
                            break;
                        case 'kakao':
                            $data['type'] = 'campaign';
                            $zenith = new ZenithKM();
                            $result = $zenith->setDailyBudgetAmount($data);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'kakao'
                                ];
                            }
                            break;
                        case 'google':
                            $param = ['budget' => $data['budget']];
                            $zenith = new ZenithGG();
                            $result = $zenith->updateCampaignBudget($data['customer'], $data['id'], $param);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'google'
                                ];
                            }
                            break;
                        default:
                            return $this->fail("지원하지 않습니다.");
                    }
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            if(!empty($result) && $result['id'] == $data['id']){
                $logData = [
                    'media' => $data['media'] ?? '',
                    'id' => $data['id'] ?? '',
                    'change_type' => 'budget',
                    'old_value' => $data['old_budget'] ?? '',
                    'change_value' => $data['budget'] ?? '',
                    'nickname' => auth()->user()->username ?? '',
                ];
        
                $logger = new AdvLoggerService();
                $logger->insertLog($logData);

                $result = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }

        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateBidAmount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $param = $this->request->getRawInput(); 
            $sliceId = explode("_", $param['id']);
            $data = [
                'media' => $sliceId[0],
                'id' => $sliceId[1],
                'customer' => $param['customer'],
                'tab' => $param['tab'],
                'bidamount' => $param['bidamount'],
                'bidamount_type' => $param['bidamount_type'] ?? null,
                'old_bidamount' => $param['old_bidamount']
            ];

            switch ($data['tab']) {
                case 'ads':
                    return $this->fail("지원하지 않습니다.");
                    break;
                case 'adsets':
                    switch ($data['media']) {
                        case 'kakao':
                            $data['type'] = 'adgroup';
                            $zenith = new ZenithKM();
                            $result = $zenith->setAdgroupBidAmount($data['id'], $data['bidamount']);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result,
                                    'media' => 'kakao'
                                ];
                            }
                            break;
                        case 'google':
                            if(!empty($data['bidamount_type'])){
                                $updateArray = [];
                                if($data['bidamount_type'] == 'cpc'){
                                    $updateArray['cpcBidAmount'] = $data['bidamount'];
                                }else if($data['bidamount_type'] == 'cpm'){
                                    $updateArray['cpmBidAmount'] = $data['bidamount'];
                                }else if($data['bidamount_type'] == 'cpa'){
                                    $updateArray['cpaBidAmount'] = $data['bidamount'];
                                }

                                $zenith = new ZenithGG();
                                $result = $zenith->updateAdGroup($data['customer'], $data['id'], $updateArray);
                                
                                if(!empty($result)){
                                    $result = [
                                        'id' => $result['id'],
                                        'media' => 'google'
                                    ];
                                }
                            };

                            break;
                        default:
                        return $this->fail("지원하지 않습니다.");
                    }
                    break;
                case 'campaigns':
                    switch ($data['media']) {
                        case 'google':
                            $updateArray = ['cpaBidAmount' => $data['bidamount']];
                            $zenith = new ZenithGG();
                            $result = $zenith->updateCampaign($data['customer'], $data['id'], $updateArray);
                            if(!empty($result)){
                                $result = [
                                    'id' => $result['id'],
                                    'media' => 'google'
                                ];
                            }
                            break;
                        default:
                            return $this->fail("지원하지 않습니다.");
                    }
                    break;
                default:
                    return $this->fail("잘못된 요청");
            }

            if(!empty($result) && $result['id'] == $data['id']){
                $logData = [
                    'media' => $data['media'] ?? '',
                    'id' => $data['id'] ?? '',
                    'change_type' => 'bigamount',
                    'old_value' => $data['old_bidamount'] ?? '',
                    'change_value' => $data['bidamount'] ?? '',
                    'nickname' => auth()->user()->username ?? '',
                ];
        
                $logger = new AdvLoggerService();
                $logger->insertLog($logData);

                $result = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }

        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateAdv()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $data = $this->request->getRawInput();
            //$data = $this->request->getGet();
            $result = false;
            $facebookArray = [];
            $googleArray = [];
            $kakaoArray = [];
            foreach ($data['check'] as $id) {
                $sliceId = explode("_", $id);
                $media = $sliceId[0];
                $advId = $sliceId[1];
  
                switch ($media) {
                    case 'facebook':
                        $facebookArray[] = $advId;
                        break;
                    case 'kakao':
                        $kakaoArray[] = $advId;
                        break;
                    case 'google':
                        $googleArray[] = $advId;
                        break;
                    default:
                        return $this->fail("지원하지 않는 매체입니다.");
                }
            }

            if (!empty($facebookArray)) {
                $this->facebook->setUpdatingByAds($facebookArray);

                $zenith = new ZenithFB();
                $updated = $zenith->setManualUpdate($facebookArray);

                if(!empty($updated)){
                    $result = true;
                }else{
                    return $this->fail("수동 업데이트 요청에 실패하였습니다.");
                }
            }

            if (!empty($googleArray)) {
                $ids = $this->google->getCustomerByCampaignId($googleArray);
                $this->google->setUpdatingByAds($googleArray);
                $zenith = new ZenithGG();
                $updated = $zenith->setManualUpdate($ids);

                if(!empty($updated)){
                    $result = true;
                }else{
                    return $this->fail("수동 업데이트 요청에 실패하였습니다.");
                }
            }

            if (!empty($kakaoArray)) {
                $ids = $this->kakao->getAccountByCampaignId($kakaoArray);
                $this->kakao->setUpdatingByAds($kakaoArray);
                $zenith = new ZenithKM();
                $updated = $zenith->setManualUpdate($ids);
                
                if(!empty($updated)){
                    $result = true;
                }else{
                    return $this->fail("수동 업데이트 요청에 실패하였습니다.");
                }
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateCode()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $data = $this->request->getRawInput();

            $sliceId = explode("_", $data['id']);
            $media = $sliceId[0];
            $adId = $sliceId[1];

            if($data['tab'] == 'ads'){
                $data = [
                    'code' => $data['code'] ?? '',
                    'id' => $adId
                ];

                switch ($media) {
                    case 'facebook':
                        $result = $this->facebook->updateCode($data);
                        break;
                    case 'kakao':
                        $result = $this->kakao->updateCode($data);
                        break;
                    case 'google':
                        $result = $this->google->updateCode($data);
                        break;
                    default:
                        return $this->fail("지원하지 않는 매체입니다.");
                }
            }

            if(isset($result['code'])){
                $result['response'] = true;
                return $this->respond($result);
            }else{
                return $this->fail("잘못된 요청");
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getMemo()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $result = $this->admanager->getMemo();
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function addMemo()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $param = $this->request->getPost();
            $param['nickname'] = auth()->user()->username;
            $param['is_done'] = 0;
            $result = $this->admanager->addMemo($param);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function checkMemo()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $param = $this->request->getPost();
            $param['done_nickname'] = auth()->user()->username;
            $result = $this->admanager->checkMemo($param);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getChangeLogs()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $id = $this->request->getGet('id');
            if(!empty($id)){
                $sliceId = explode("_", $id);
                $id = $sliceId[1];
                $result = $this->admanager->getChangeLogs($id);
            }else{
                if(auth()->user()->inGroup('developer')){
                    $result = $this->admanager->getChangeLogs();
                }else{
                    $result = false;
                }
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

	private function getCareLabsData($arg)
	{
		$url = "https://carezenith.co.kr/resta/get-adv?".http_build_query($arg);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}
 