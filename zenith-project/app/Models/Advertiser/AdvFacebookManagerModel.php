<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvFacebookManagerModel extends Model
{
    protected $facebook, $ro_facebook;
    public function __construct()
    {
        $this->facebook = \Config\Database::connect('facebook');
        $this->ro_facebook = \Config\Database::connect('ro_facebook');
    }

    public function getAdAccounts()
    {
        $builder = $this->facebook->table('fb_ad_account');
        $builder->select("*");
        $builder->where('status', 1);
        $builder->where('perm', 1);
        $builder->where('pixel_id IS NOT NULL');
        $result = $builder->get()->getResultArray();
        
        return $result;
    }
    
    public function getCampaigns($data)
    {
        $builder = $this->facebook->table('fb_campaign A');
        $builder->select('A.campaign_id AS id, A.campaign_name AS name, A.status AS status, A.budget AS budget, A.is_updating AS is_updating, A.ai2_status, COUNT(B.adset_id) AS adsets, COUNT(C.ad_id) AS ads, SUM(D.impressions) AS impressions, SUM(D.inline_link_clicks) AS inline_link_clicks, SUM(D.spend) AS spend, 0 AS total, 0 AS unique_total, D.ad_id, SUM(D.sales) as sales, A.account_id');
        $builder->select('(SELECT COUNT(*) AS memos FROM fb_memo F WHERE A.campaign_id = F.id AND F.type = "campaign" AND DATE(F.datetime) >= DATE(NOW()) AND is_done = 0) AS memos');
        $builder->join('fb_adset B', 'A.campaign_id = B.campaign_id');
        $builder->join('fb_ad C', 'B.adset_id = C.adset_id');
        $builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id');
        $builder->join('fb_ad_account E', 'A.account_id = E.ad_account_id');
        $builder->where('DATE(el.reg_date) >=', $data['sdate']);
        $builder->where('DATE(el.reg_date) <=', $data['edate']);

        if(!empty($data['businesses'])){
            $businesses = "'" . implode("','", $data['businesses']) . "'";
			$builder->whereIn('E.business_id', $businesses);
        }

        if(!empty($data['accounts'])){
            $accounts = "'" . implode("','", $data['accounts']) . "'";
			$builder->whereIn('A.account_id', $accounts);
        }

        $builder->groupBy('A.campaign_id');
    }
}
