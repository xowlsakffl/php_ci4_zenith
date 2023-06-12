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
            
            /* foreach ($result['data'] as &$value) {
                $value['budget'] = number_format($value['budget']);
                $value['impressions'] = number_format($value['impressions']);
                $value['click'] = number_format($value['click']);
                $value['spend'] = number_format($value['spend']);
                $value['sales'] = number_format($value['sales']);
                $value['unique_total'] = number_format($value['unique_total']);
                $value['margin'] = number_format($value['margin']);
                $value['cpa'] = number_format($value['cpa']);
                $value['cpc'] = number_format($value['cpc']);
            } */

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function getCampaigns($arg)
    {
        $result  = [];    
        $campaigns = $this->admanager->getCampaigns($arg);
        $campaigns = $this->getStatuses($campaigns);
        $result = array_merge($result, $campaigns); 

        //$total = $this->getTotal($result);
        
        /* $result = [
            'total' => $total,
            'data' => $result,
        ]; */

        return $result;
    }

    private function getStatuses($result)
    {
        foreach ($result as &$row) {
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
        1 = 인정,
        2 = 정지,
        3 = 보관,
        4 = 삭제
        */
        if(!empty($status)){
            if(in_array($status, ['ENABLED', 'ACTIVE'])){
                $status = 1;
            }else if(in_array($status, ['PAUSED'])){
                $status = 2;
            }else if(in_array($status, ['ARCHIVED'])){
                $status = 3;
            }else if(in_array($status, ['DELETED'])){
                $status = 4;
            };
        }else{
            $status = NULL;
        };

        return $status;
    }
}
 