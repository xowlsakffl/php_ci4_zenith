<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvManagerModel extends Model
{
    protected $zenith;
    public function __construct()
    {
		$this->zenith = \Config\Database::connect();
    }

    public function getAccounts($data)
	{
        $builder = $this->zenith->table('companies c');
		$builder->select('ca.ad_account_id AS id, c.name, ca.media AS media, faa.business_id, faa.status, faa.db_count, SUM(fai.db_count) AS db_sum, COUNT(DISTINCT fai.date) AS date_count');
		$builder->join('company_adaccounts ca', 'c.id = ca.company_id', "left");
		$builder->join('z_facebook.fb_ad_account faa', 'ca.ad_account_id = faa.ad_account_id AND ca.media = "페이스북"', "left");
        $builder->join('z_facebook.fb_campaign fc', 'faa.ad_account_id = fc.account_id', 'left');
        $builder->join('z_facebook.fb_adset fas', 'fc.campaign_id = fas.campaign_id', 'left');
        $builder->join('z_facebook.fb_ad fad', 'fas.adset_id = fad.adset_id', 'left');
		$builder->join('z_facebook.fb_ad_insight_history fai', 'fad.ad_id = fai.ad_id', 'left');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(fai.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(fai.date) <=', $data['dates']['edate']);
        }

		$builder->where('faa.is_admin', 0);
        $builder->where('faa.name !=', '');

        if(!empty($data['business'])){
			$builder->whereIn('faa.business_id', explode("|",$data['business']));
        }

        $builder->groupBy('c.id');
        $builder->orderBy('c.name', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
	}


    public function test(){
        //campaign
        $builder = $this->facebook->table('fb_campaign fc');
        $builder->select('fc.campaign_id AS id, fc.campaign_name AS name, fc.status AS status, fc.budget AS budget, SUM(fai.impressions) AS impressions, SUM(fai.inline_link_clicks) AS click, SUM(fai.spend) AS spend, SUM(fai.db_count) as unique_total, SUM(fai.sales) as sales, SUM(fai.margin) as margin');
        $builder->join('fb_adset fas', 'fc.campaign_id = fas.campaign_id');
        $builder->join('fb_ad fad', 'fas.adset_id = fad.adset_id');
        $builder->join('fb_ad_insight_history fai', 'fad.ad_id = fai.ad_id');

        $builder = $this->facebook->table('aw_campaign ac');
        $builder->select('ac.id AS id, ac.name AS name, ac.status AS status, ac.amount AS budget, SUM(aar.impressions) AS impressions, SUM(aar.clicks) AS click, SUM(aar.cost) AS spend, SUM(aar.db_count) AS unique_total, sum(aar.sales) AS sales, SUM(aar.margin) AS margin');
        $builder->join('aw_adgroup aas', 'ac.id = aas.campaignId');
        $builder->join('aw_ad aad', 'aas.id = aad.adgroupId');
        $builder->join('aw_ad_report_history aar', 'aad.id = aar.ad_id');

        $builder = $this->kakao->table('mm_campaign mc');
        $builder->select('mc.id AS id, mc.name AS name, mc.config AS status, mc.dailyBudgetAmount AS budget, SUM(mcrb.imp) AS impressions, SUM(mcrb.click) AS click, SUM(mcrb.cost) AS spend, SUM(mcrb.db_count) as unique_total, SUM(mcrb.sales) AS sales, SUM(mcrb.margin) as margin');
        $builder->join('mm_adgroup mag', 'mc.id = mag.campaign_id');
        $builder->join('mm_creative mct', 'mag.id = mct.adgroup_id');
        $builder->join('mm_creative_report_basic mcrb', 'mct.id = mcrb.id');



        //adsets
        $builder = $this->facebook->table('fb_adset fas');
        $builder->select('
        fas.adset_id AS id, 
        fas.adset_name AS name, 
        fas.status AS status, 
        SUM(D.impressions) impressions, 
        SUM(D.inline_link_clicks) click, 
        SUM(D.spend) spend, 
        SUM(D.db_count) as unique_total, 
        SUM(D.margin) as margin,
        SUM(D.sales) as sales');
        $builder->join('fb_campaign fc', 'fc.campaign_id = fas.campaign_id');
        $builder->join('fb_ad fad', 'fas.adset_id = fad.adset_id');
        $builder->join('fb_ad_insight_history fai', 'fad.ad_id = fai.ad_id');
        $builder->groupBy('fas.adset_id');

        $builder = $this->google->table('aw_adgroup aas');
        $builder->select('
        aas.id AS id, 
        aas.name AS name, 
        aas.status AS status, 
        SUM(aar.impressions) impressions,
        SUM(aar.clicks) click, 
        SUM(aar.cost) spend, 
        SUM(aar.db_count) as unique_total, 
        SUM(aar.margin) as margin, 
        sum(aar.sales) as sales');
        $builder->join('aw_campaign ac', 'aas.campaignId = ac.id');
        $builder->join('aw_ad aad', 'aas.id = aad.adgroupId');
        $builder->join('aw_ad_report_history aar', 'aad.id = aar.ad_id');
        $builder->groupBy('aas.campaignId');

        $builder = $this->kakao->table('mm_adgroup mag');
        $builder->select('
        mag.id AS id, 
        mag.name AS name, 
        mag.config AS status, 
        SUM(mcrb.imp) impressions, 
        SUM(mcrb.click) click, 
        SUM(mcrb.cost) spend, 
        SUM(mcrb.db_count) as unique_total, 
        SUM(mcrb.margin) as margin,
        SUM(mcrb.sales) as sales
        ');
        $builder->join('mm_campaign mc', 'mag.campaign_id = mc.id');
        $builder->join('mm_creative mct', 'mag.id = mct.adgroup_id');
        $builder->join('mm_creative_report_basic mcrb', 'mct.id = mcrb.id');
        $builder->groupBy('aas.campaignId');
    }
}
