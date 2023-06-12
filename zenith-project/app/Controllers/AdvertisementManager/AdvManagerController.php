<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Libraries\Calc;
use App\Models\Advertiser\AdvManagerModel;
use CodeIgniter\API\ResponseTrait;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\moment_api\ZenithKM;

class AdvManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $admanager;
    private $campaign;
    public function __construct() 
    {
        $this->admanager = model(AdvManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/manage');
    }

    public function getData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $arg['searchData']['dates'] = [
                'sdate' => $arg['searchData']['sdate'],
                'edate' => $arg['searchData']['edate'],
            ];
            
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
        $campaigns = $this->admanager->getCampaigns($arg);
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
            switch ($row['media']) {
                case 'facebook':
                    $row['media'] = '페이스북';
                    break;
                case 'moment':
                    $row['media'] = '카카오';
                    break;
                case 'google':
                    $row['media'] = '구글';
                    break;
                default:
                    $row['media'] = '기타';
                    break;
            }

			$row['status'] = $this->changeStatus($row['status']);
			
            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률


			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율
        }
        return $result;
    }

    private function changeStatus($status)
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
            else if(in_array($status, ['PAUSED'])){$status = 'STOPPED';}
            else if(in_array($status, ['OFF'])){$status = 'OFF';}
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

            if ($data['status'] == 'ON' && $data['unique_total']){
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
 