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

    public function getCampaigns($args)
    {
        $param = [
            'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
            'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'),
        ];

        $campaigns = $this->db->getCampaigns($args);
        $campaigns = $this->db->getStats("campaigns", $campaigns, $args['dates']);
        $accounts = $this->getAccounts($args);
        $getDisapprovalByAccount = [];
        $getDisapprovalByAccount = $this->getDisapprovalByAccount();
        $accounts_list = '';
        while ($account = $accounts->fetch_assoc()) {
            $class = [];
            if ($account['status'] != 1) $class[] = 'tag-inactive';
            if (in_array($account['ad_account_id'], $args['accounts'])) $class[] = 'active';
            if (in_array($account['ad_account_id'], $getDisapprovalByAccount)) $class[] = 'disapproval';
            $db_ratio = 0;
            $set_ratio = '';
            $class_over = "";
            $db_count = $account['db_count'] * $account['date_count'];
            if($account['db_sum'] && $account['db_count']) $db_ratio = round($account['db_sum'] / $db_count * 100,1);
            if($db_ratio >= 100) { $db_ratio = 100; $class_over = ' class="over"';}
            if(!$account['db_sum']) $account['db_sum'] = 0;
            if($account['db_count']) $set_ratio = '<div class="db_ratio"><div class="bar" style="width:'.$db_ratio.'%"></div><u'.$class_over.'>'.$account['db_sum'].'/'.$db_count.'</u></div>';
            $accounts_list .= '<a href="#" class="act-tab act-' . $account['ad_account_id'] . ' ' . implode(' ', $class) . '" data="' . $account['ad_account_id'] . '">' . $account['name'].$set_ratio . '</a>';
        }
        if (isset($args['is_multisort']) && $args['is_multisort'] === true)
            $campaigns = $this->sort($campaigns, $args['sort']);

        include('../views/list_campaign.php');
    }
}
