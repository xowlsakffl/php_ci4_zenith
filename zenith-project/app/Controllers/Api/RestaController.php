<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\Calc;
use App\Models\Advertiser\AdvFacebookManagerModel;
use App\Models\Advertiser\AdvGoogleManagerModel;
use App\Models\Advertiser\AdvKakaoManagerModel;
use App\Models\Advertiser\AdvManagerModel;
use App\Models\Api\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class RestaController extends BaseController
{
    use ResponseTrait;
    
    protected $admanager, $facebook, $google, $kakao;
    private $advertiser = [
        '더스퀘어치과의원', 
        '메이드영성형외과의원',
        '모아만의원',
        '모우림의원',
        '열정치과의원',
        '우리들40플란트치과병원',
        '하루플란트치과의원광고주',
        '한나이브성형외과의원',
        '강남애프터치과의원',
        '참좋은치과의원',
        '보가치과의원'
    ];
    public function __construct() 
    {
        $this->admanager = model(AdvManagerModel::class);
        $this->facebook = model(AdvFacebookManagerModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
        $this->kakao = model(AdvKakaoManagerModel::class);
    }

    public function getList()
    {
        if(strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(empty($arg['external']) || $arg['external'] != 'resta'){
                return $this->fail("잘못된 요청");
            }

            $company = model(CompanyModel::class);
            $companySeq = $company->getCompaniesByName($this->advertiser);
            $ids = array_column($companySeq, 'id');
            $ids = implode("|", $ids);
            $arg['searchData']['resta'] = $ids;
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
            $result['accounts'] = $this->getAccounts($arg);
            $result['media_accounts'] = $this->getMediaAccounts($arg);
            $result['report'] = $this->getReport($arg);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getAccounts($arg)
    {
        $result  = [];    
        $accounts = $this->admanager->getAccounts($arg);
        //$accounts = $this->setAccountData($accounts);
        $result = array_merge($result, $accounts); 
        return $result;
    }

    private function getMediaAccounts($arg)
    { 
        $result = $this->admanager->getMediaAccounts($arg);
        return $result;
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

    private function getCampaigns($arg)
    {
        $campaigns = $this->admanager->getCampaigns($arg);
        $campaigns = $this->setData($campaigns);

        $total = $this->getTotal($campaigns);
        
        $result = [
            'total' => $total,
            'data' => $campaigns,
        ];

        return $result;
    }

    private function getAdSets($arg)
    {
        $result  = [];    
        $campaigns = $this->admanager->getAdSets($arg);
        $campaigns = $this->setData($campaigns);
        $result = array_merge($result, $campaigns); 

        $total = $this->getTotal($result);
        
        $result = [
            'total' => $total,
            'data' => $result,
        ];

        return $result;
    }

    private function getAds($arg)
    {
        $result  = [];    
        $campaigns = $this->admanager->getAds($arg);
        $campaigns = $this->setData($campaigns);
        $result = array_merge($result, $campaigns); 

        $total = $this->getTotal($result);
        
        $result = [
            'total' => $total,
            'data' => $result,
        ];

        return $result;
    }

    private function setData($result)
    {
        foreach ($result as &$row) {
			$row['status'] = $this->setStatus($row['status']);
			
            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률

			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율
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
        $total['budget'] = number_format($total['budget']);
        $total['click'] = number_format($total['click']);
        $total['spend'] = number_format($total['spend']);
        $total['avg_cpa'] = number_format($total['avg_cpa']);
        $total['margin'] = number_format($total['margin']);
        $total['sales'] = number_format($total['sales']);
        $total['avg_cpc'] = number_format($total['avg_cpc']);

        return $total;
    }
}
