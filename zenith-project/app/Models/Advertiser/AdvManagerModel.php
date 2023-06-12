<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvManagerModel extends Model
{
    protected $admanager;
    public function __construct()
    {
		$this->admanager = \Config\Database::connect('admanager');
    }

    public function getCampaigns()
    {
        $builder = $this->admanager->table('campaign');
        $builder->select('*');
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
        SUM(fai.impressions) impressions, 
        SUM(fai.inline_link_clicks) click, 
        SUM(fai.spend) spend, 
        SUM(fai.db_count) as unique_total, 
        SUM(fai.margin) as margin,
        SUM(fai.sales) as sales');
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
        $builder->groupBy('aas.id');

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
        $builder->groupBy('mag.id');

        //ads
        $builder = $this->facebook->table('fb_ad fad');
		$builder->select('
        "facebook" AS media, 
        fad.ad_id AS id, 
        fad.ad_name AS name, 
        fad.status AS status, 
        SUM(fai.impressions) AS impressions, 
        SUM(fai.inline_link_clicks) AS click, 
        SUM(fai.spend) AS spend, 
        SUM(fai.sales) AS sales, 
        SUM(fai.db_count) AS unique_total, 
        SUM(fai.margin) AS margin, 
        ');
		$builder->join('fb_adset fas', 'fad.adset_id = fas.adset_id');
		$builder->join('fb_campaign fc', 'fas.campaign_id = fc.campaign_id');
		$builder->join('fb_ad_insight_history fai', 'fad.ad_id = fai.ad_id', 'left');
		$builder->join('fb_adcreative fat', 'fai.ad_id = fat.ad_id', 'left');
        $builder->groupBy('fad.ad_id');

        $builder = $this->google->table('aw_ad aad');
		$builder->select('
        "google" AS media, 
        aad.id AS id, 
        aad.name AS name,
        aad.status AS status, 
        SUM(aar.impressions) AS impressions, 
        SUM(aar.clicks) AS click, 
        SUM(aar.cost) AS spend, 
        sum(aar.sales) AS sales, 
        SUM(aar.db_count) AS unique_total, 
        SUM(aar.margin) AS margin');
		$builder->join('aw_adgroup aas', 'aad.adgroupId = aas.id');
		$builder->join('aw_campaign ac', 'aas.campaignId = ac.id');
		$builder->join('aw_ad_report_history aar', 'aad.id = aar.ad_id');
        $builder->groupBy('aad.id');

        $builder = $this->kakao->table('mm_creative mct');
		$builder->select('
        "kakao" AS media, 
        mct.id AS id, 
        mct.name AS name, 
        mct.config AS status, 
        SUM(mcrb.imp) AS impressions, 
        SUM(mcrb.click) AS click, 
        SUM(mcrb.cost) AS spend, 
        SUM(mcrb.sales) AS sales, 
        SUM(mcrb.db_count) AS unique_total, 
        SUM(mcrb.margin) AS margin, 
        ');
        $builder->join('mm_adgroup mag', 'mct.adgroup_id = mag.id');
        $builder->join('mm_campaign mc', 'mag.campaign_id = mc.id');
		$builder->join('mm_creative_report_basic mcrb', 'mct.id = mcrb.id');
        $builder->groupBy('mct.id');



        /*report*/
        $builder = $this->facebook->table('fb_ad_insight_history fai');
        $builder->select('
        "facebook" AS media, 
        faa.name,
        fai.date, 
        SUM(fai.impressions) AS impressions,
        SUM(fai.inline_link_clicks) AS click,
        (SUM(fai.inline_link_clicks) / SUM(fai.impressions)) * 100 AS click_ratio,
        (SUM(fai.db_count) / SUM(fai.inline_link_clicks)) * 100 AS conversion_ratio,
        SUM(fai.spend) AS spend,
        SUM(fai.db_count) AS unique_total,
        IFNULL(SUM(fai.spend) / SUM(fai.db_count), 0) AS unique_one_price,
        SUM(fai.db_price) AS unit_price,
        SUM(fai.sales) AS price,
        SUM(fai.margin) AS profit,
        (SUM(fai.db_price * fai.db_count) - SUM(fai.spend)) / SUM(fai.db_price * fai.db_count) * 100 AS per
        ');
        $builder->join('fb_ad fad', 'fai.ad_id = fad.ad_id', 'left');
        $builder->join('fb_adset fas', 'fad.adset_id = fas.adset_id', 'left');
        $builder->join('fb_campaign fc', 'fas.campaign_id = fc.campaign_id', 'left');
        $builder->join('fb_ad_account faa', 'fc.account_id = faa.ad_account_id', 'left');
        $builder->groupBy('fai.date');

        $builder = $this->google->table('aw_ad_report_history aar');
        $builder->select('
        "google" AS media, 
        aaa.name,
        aar.date, 
        SUM(aar.impressions) AS impressions,
        SUM(aar.clicks) AS click,
        (SUM(aar.clicks) / SUM(aar.impressions)) * 100 AS click_ratio,
        (SUM(aar.db_count) / SUM(A.clicks)) * 100 AS conversion_ratio,
        SUM(aar.cost) AS spend,
        SUM(aar.db_count) AS unique_total,
        IFNULL(SUM(aar.cost) / SUM(aar.db_count), 0) AS unique_one_price,
        SUM(aar.db_price) AS unit_price,
        SUM(aar.sales) AS price,
        SUM(aar.margin) AS profit,
        (SUM(aar.db_price * aar.db_count) - SUM(aar.cost)) / SUM(aar.db_price * aar.db_count) * 100 AS per 
        ');
        $builder->join('aw_ad aad', 'aar.ad_id = aad.id', 'left');
        $builder->join('aw_adgroup aas', 'aad.adgroupId = aas.id', 'left');
        $builder->join('aw_campaign ac', 'aas.campaignId = ac.id', 'left');
        $builder->join('aw_ad_account aaa', 'ac.customerId = aaa.customerId', 'left');
        $builder->groupBy('aar.date');

        $builder = $this->kakao->table('mm_creative_report_basic mcrb');
        $builder->select('
        "kakao" AS media, 
        maa.name,
        mcrb.date, 
        SUM(mcrb.imp) AS impressions,
        SUM(mcrb.click) AS click,   
        (SUM(mcrb.click) / SUM(mcrb.imp)) * 100 AS click_ratio, 
        (SUM(mcrb.db_count) / SUM(mcrb.click)) * 100 AS conversion_ratio,
        SUM(mcrb.cost) AS spend,
        SUM(mcrb.db_count) AS unique_total, 
        IFNULL(SUM(mcrb.cost) / SUM(mcrb.db_count),0) AS unique_one_price,
        SUM(mcrb.db_price) AS unit_price, 
        SUM(mcrb.sales) AS price,  
        SUM(mcrb.margin) AS profit,
        (SUM(mcrb.db_price * mcrb.db_count) - SUM(mcrb.cost)) / SUM(mcrb.db_price * mcrb.db_count) * 100 AS per
        ');
        $builder->join('mm_creative mct', 'mcrb.id = mct.id', 'left');
        $builder->join('mm_adgroup mag', 'mct.adgroup_id = mag.id', 'left');
        $builder->join('mm_campaign mc', 'mag.campaign_id = mc.id', 'left');
        $builder->join('mm_ad_account maa', 'mc.ad_account_id = maa.id', 'left');
        $builder->groupBy('mcrb.date');

    }
}
