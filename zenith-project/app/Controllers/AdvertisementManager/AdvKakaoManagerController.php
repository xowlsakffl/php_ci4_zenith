<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvKakaoManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvKakaoManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $kakao;
    public function __construct() 
    {
        $this->kakao = model(AdvKakaoManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/kakao/kakao');
    }

    public function getAccounts()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'dates' => [
                    'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'),
                ],
                'businesses' => $this->request->getGet('businesses'),
            ];

            $accounts = $this->kakao->getAccounts($arg);
            $getDisapprovalByAccount = $this->getDisapprovalByAccount();
            foreach ($accounts as &$account) {
                $account['class'] = [];
                if($account['config'] == 'OFF' || $account['isAdminStop'] == 1)
                    array_push($account['class'], 'tag-inactive');
                if(is_array($getDisapprovalByAccount) && in_array($account['id'], $getDisapprovalByAccount))
                    array_push($account['class'], 'disapproval');  
            }

            return $this->respond($accounts);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'dates' => [
                    'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'),
                ],
                'type' => $this->request->getGet('type'),
                'accounts' => $this->request->getGet('accounts'),
                'stx' => $this->request->getGet('stx'),
            ];
            
            if($arg['type'] == 'ads'){
                $result = $this->getAds($arg);
            }else if($arg['type'] == 'adsets'){
                $result = $this->getAdSets($arg);
            }else{
                $result = $this->getCampaigns($arg);
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getReport()
    {
        if(isset($this->request) && $this->request->isAJAX()){
            $arg = [
                'sdate' => $this->request->getGet('sdate'),
                'edate' => $this->request->getGet('edate'),
            ];
        }else{
            $arg = [
                'sdate' => date("Y-m-d"),
                'edate' => date("Y-m-d"),
            ];
        }

        $res = $this->kakao->getReport($arg)->get()->getResultArray();
        $columnIndex = 0;
        $data = [];
        foreach($res as $row) {
            $data[] = $row;
            foreach ($row as $col => $val) {
                if ($val == NULL) $val = "0";
                $total[$col][$columnIndex] = $val;
            }
            $columnIndex++;
        }

        $report['impressions_sum'] = $report['clicks_sum'] = $report['click_ratio_sum'] = $report['spend_sum'] = $report['unique_total_sum'] = $report['unique_one_price_sum'] = $report['conversion_ratio_sum'] = $report['profit_sum'] = $report['per_sum'] = 0;

        if(!empty($res)){
            $report['impressions_sum'] = array_sum($total['impressions']); //총 노출수
            $report['clicks_sum'] = array_sum($total['click']); //총 클릭수
            if($report['clicks_sum'] != 0 && $report['impressions_sum'] != 0) {
                $report['click_ratio_sum'] = round(($report['clicks_sum'] / $report['impressions_sum']) * 100,2); //총 클릭률    
            }
            $report['spend_sum'] = array_sum($total['spend']); //총 지출액
            $report['unique_total_sum'] = array_sum($total['unique_total']); //총 유효db수
            if($report['spend_sum'] != 0 && $report['unique_total_sum'] != 0) {
                $report['unique_one_price_sum'] = round($report['spend_sum'] / $report['unique_total_sum'],0); //총 db당 단가
            }
            if($report['unique_total_sum'] != 0 && $report['clicks_sum'] != 0) {
                $report['conversion_ratio_sum'] = round(($report['unique_total_sum'] / $report['clicks_sum']) * 100,2); //총 전환율
            }
            $report['price_sum'] = array_sum($total['price']); //총 매출액
            $report['profit_sum'] = array_sum($total['profit']); //총 수익
            if($report['profit_sum'] != 0 && $report['price_sum'] != 0) {
                $report['per_sum'] = round(($report['profit_sum'] / $report['price_sum']) * 100,2); //총 수익률
            }

            $report['impressions_sum'] = number_format($report['impressions_sum']);
            $report['clicks_sum'] = number_format($report['clicks_sum']);
            $report['spend_sum'] = number_format($report['spend_sum']);
            $report['unique_one_price_sum'] = number_format($report['unique_one_price_sum']);
            $report['price_sum'] = number_format($report['price_sum']);
            $report['profit_sum'] = number_format($report['profit_sum']);
        }
        
        if(isset($this->request) && $this->request->isAJAX()){
            return $this->respond($report);
        }else{
            return $report;
        }
    }

    private function getCampaigns($arg)
    {
        $campaigns = $this->kakao->getCampaigns($arg);
        $campaigns = $this->kakao->getStatuses("campaigns", $campaigns, $arg['dates']);
        $total = $this->getTotal($campaigns);
        
        $result = [
            'total' => $total,
            'data' => $campaigns,
        ];

        return $result;
    }

    private function getAdSets($arg)
    {
        $adsets = $this->kakao->getAdSets($arg);
        $adsets = $this->kakao->getStatuses("adsets", $adsets, $arg['dates']);
        $total = $this->getTotal($adsets);

        $result = [
            'total' => $total,
            'data' => $adsets
        ];

        return $result;
    }

    private function getAds($arg)
    {
        $ads = $this->kakao->getAds($arg);
        $ads = $this->kakao->getStatuses("ads", $ads, $arg['dates']);
        $total = $this->getTotal($ads);

        $result = [
            'total' => $total,
            'data' => $ads
        ];

        return $result;
    }

    private function getDisapprovalByAccount()
    {
        $disapprovals = $this->kakao->getDisapproval();
        $data = [];
        foreach ($disapprovals as $row) {
            $data[] = $row['account_id'];
        }
        $data = array_unique($data);

        return $data;
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
            }else{
                $total['avg_cpc'] = 0;
            }

            //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
            $total['avg_ctr'] = ($total['click'] / $total['impressions']) * 100;

            //CPA(Cost Per Action: 현재 DB단가(전환당 비용)) = 지출액/유효db
            if($total['unique_total'] > 0){
                $total['avg_cpa'] = $total['spend'] / $total['unique_total'];
            }else{
                $total['avg_cpa'] = 0;
            }

            //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
            if ($total['click'] > 0) {
                $total['avg_cvr'] = ($total['unique_total'] / $total['click']) * 100;
            } else {
                $total['avg_cvr'] = 0;
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

        return $total;
    }

    private function array_remove_keys($array, $keys)
    {
        $assocKeys = array();
        foreach ($keys as $key) {
            $assocKeys[$key] = true;
        }

        return array_diff_key($array, $assocKeys);
    }
}
