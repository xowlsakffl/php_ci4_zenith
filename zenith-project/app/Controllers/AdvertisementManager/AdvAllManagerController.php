<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvFacebookManagerModel;
use App\Models\Advertiser\AdvGoogleManagerModel;
use App\Models\Advertiser\AdvKakaoManagerModel;
use App\Models\Advertiser\AdvNaverManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvAllManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $facebook, $kakao, $google, $naver;
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

    public function getData(){
        //if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'media' => $this->request->getGet('media'),
                'dates' => [
                    /* 'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'), */
                    'sdate' => '2023-04-29',
                    'edate' => '2023-04-29',
                ],
                'type' => $this->request->getGet('type'),
                'businesses' => $this->request->getGet('businesses'),
                'accounts' => $this->request->getGet('accounts'),
                'stx' => $this->request->getGet('stx'),
            ];
            
            switch ($arg['type']) {
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
            
            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }

    private function getCampaigns($arg)
    {
        switch ($arg['media']) {
            case 'facebook':
                $campaigns = $this->facebook->getCampaigns($arg);
                $campaigns = $this->facebook->getStatuses("campaigns", $campaigns, $arg['dates']);
                break;
            case 'kakao':
                $campaigns = $this->kakao->getCampaigns($arg);
                $campaigns = $this->kakao->getStatuses("campaigns", $campaigns, $arg['dates']);
                break;
            case 'google':
                $campaigns = $this->google->getCampaigns($arg);
                $campaigns = $this->google->getStatuses("campaigns", $campaigns, $arg['dates']);
                break;
            case 'naver':
                $campaigns = $this->naver->getCampaigns($arg);
                $campaigns = $this->naver->getStatuses("campaigns", $campaigns, $arg['dates']);
                break;
            default:
                return $this->fail("지원하지 않는 매체입니다.");
        }
        
        $total = $this->getTotal($campaigns);
        
        $result = [
            'total' => $total,
            'data' => $campaigns,
        ];

        return $result;
    }

    private function getAdSets($arg)
    {
        $adsets = $this->facebook->getAdSets($arg);
        $adsets = $this->facebook->getStatuses("adsets", $adsets, $arg['dates']);
        $total = $this->getTotal($adsets);

        $result = [
            'total' => $total,
            'data' => $adsets
        ];

        return $result;
    }

    private function getAds($arg)
    {
        $ads = $this->facebook->getAds($arg);
        $ads = $this->facebook->getStatuses("ads", $ads, $arg['dates']);
        $total = $this->getTotal($ads);

        $result = [
            'total' => $total,
            'data' => $ads
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

            if ($data['status'] == 'ACTIVE' && $data['unique_total']){
                $total['expect_db'] += round($data['budget'] / $data['cpa']);
            }
        }

        return $total;
    }

    public function getAccounts()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'media' => $this->request->getGet('media'),
                'dates' => [
                    /* 'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'), */
                    'sdate' => '2023-04-29',
                    'edate' => '2023-04-29',
                ],
                'businesses' => $this->request->getGet('businesses'),
                
            ];

            switch ($arg['media']) {
                case 'facebook':
                    $accounts = $this->facebook->getAccounts($arg);
                    $accounts = $this->updateAccountsForFacebook($accounts);
                    break;
                case 'kakao':
                    $accounts = $this->kakao->getAccounts($arg);
                    $accounts = $this->updateAccountsForKakao($accounts);
                    break;
                case 'google':
                    $accounts = $this->google->getAccounts($arg);
                    $accounts = $this->updateAccountsForGoogle($accounts);
                    break;
                case 'naver':
                    $accounts = $this->naver->getAccounts($arg);
                    break;
                default:
                    return $this->fail("지원하지 않는 매체입니다.");
            }
            
            return $this->respond($accounts);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function updateAccountsForFacebook($accounts)
    {
        $getDisapprovalByAccount = $this->getDisapprovalByAccount('ad_account_id');
        foreach ($accounts as &$account) {
            $account['class'] = [];
            $account['db_ratio'] = '';

            if ($account['status'] != 1) 
                array_push($account['class'], 'tag-inactive');
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
}
 