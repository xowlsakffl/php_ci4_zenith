<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvFacebookManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvFacebookManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $facebook;
    public function __construct() 
    {
        $this->facebook = model(AdvFacebookManagerModel::class);
    }

    public function index()
    {
        return view('advertisements/facebook/facebook');
    }

    public function getAdAccount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $result = $this->facebook->getAdAccounts();

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getAccounts()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'dates' => [
                    'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'),
                ],
            ];

            $accounts = $this->facebook->getAccounts($arg);
            $getDisapprovalByAccount = $this->getDisapprovalByAccount();
            foreach ($accounts as &$account) {
                
                $account['class'] = [];
                $account['db_ratio'] = '';

                if ($account['status'] != 1) 
                    array_push($account['class'], 'tag-inactive');
                /* if (in_array($account['ad_account_id'], $arg['accounts'])) 
                    array_push($account['class'], 'active'); */
                if (in_array($account['ad_account_id'], $getDisapprovalByAccount)) 
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
            ];
            
            if($arg['type'] == 'campaigns'){
                $result = $this->getCampaigns($arg);
            }else if($arg['type'] == 'adsets'){
                $result = $this->getAdSets($arg);
            }else{
                
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getCampaigns($arg)
    {
            $campaigns = $this->facebook->getCampaigns($arg);
            $campaigns = $this->facebook->getStatuses("campaigns", $campaigns, $arg['dates']);
            
            $total = [];
            $total['impressions'] = 0;
            $total['inline_link_clicks'] = 0;
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
            foreach($campaigns as $campaign){
                $total['impressions'] +=$campaign['impressions'];
                $total['inline_link_clicks'] +=$campaign['inline_link_clicks'];
                $total['spend'] +=$campaign['spend'];
                $total['margin'] +=$campaign['margin'];
                $total['unique_total'] +=$campaign['unique_total'];
                $total['sales'] +=$campaign['sales'];
                $total['budget'] +=$campaign['budget'];
                $total['cpc'] +=$campaign['cpc'];
                $total['ctr'] +=$campaign['ctr'];
                $total['cpa'] +=$campaign['cpa'];
                $total['cvr'] +=$campaign['cvr'];
                $total['margin_ratio'] +=$campaign['margin_ratio'];

                //CPC(Cost Per Click: 클릭당단가 (1회 클릭당 비용)) = 지출액/링크클릭
                if($total['inline_link_clicks'] > 0){
                    $total['avg_cpc'] = $total['spend'] / $total['inline_link_clicks'];
                }else{
                    $total['avg_cpc'] = 0;
                }

                //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
                $total['avg_ctr'] = ($total['inline_link_clicks'] / $total['impressions']) * 100;

                //CPA(Cost Per Action: 현재 DB단가(전환당 비용)) = 지출액/유효db
                if($total['unique_total'] > 0){
                    $total['avg_cpa'] = $total['spend'] / $total['unique_total'];
                }else{
                    $total['avg_cpa'] = 0;
                }

                //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
                if ($total['inline_link_clicks'] > 0) {
                    $total['avg_cvr'] = ($total['unique_total'] / $total['inline_link_clicks']) * 100;
                } else {
                    $total['avg_cvr'] = 0;
                } 	

                //수익률 = (수익/매출액)*100
                if ($total['sales'] > 0) {
                    $total['avg_margin_ratio'] = ($total['margin'] / $total['sales']) * 100;
                } else {
                    $total['avg_margin_ratio'] = 0;
                } 

                if ($campaign['status'] == 'ACTIVE' && $campaign['unique_total']){
                    $total['expect_db'] += round($campaign['budget'] / $campaign['cpa']);
                }
                        
                // 수익이 마이너스면 빨간색으로 표시
                /* if ($total['margins'] <= 0) {
                    $margin_minus = "margin_minus";
                } else {
                    $margin_minus = "";
                } */

                // 수익률이 20%이하면  빨간색으로 표시
                /* if ($avg_margin_ratio < 20 && $avg_margin_ratio <> 0) {
                    $margin_ratio_minus = "margin_ratio_minus";
                } else {
                    $margin_ratio_minus = "";
                } */
            }
            /* if (isset($args['is_multisort']) && $args['is_multisort'] === true)
                $campaigns = $this->sort($campaigns, $args['sort']); */

            $result = [
                'total' => $total,
                'campaigns' => $campaigns,
            ];

            return $result;
    }

    public function getAdSets($arg)
    {
        $adsets = $this->facebook->getAdSets($arg);
        $adsets = $this->facebook->getStatuses("adsets", $adsets, $arg['dates']);

        $result = [
            'adsets' => $adsets
        ];

        return $result;
    }

    private function getDisapprovalByAccount()
    {
        $disapprovals = $this->facebook->getDisapproval();
        $data = [];
        foreach ($disapprovals as $row) {
            $data[] = $row['ad_account_id'];
        }
        $data = array_unique($data);

        return $data;
    }
}
