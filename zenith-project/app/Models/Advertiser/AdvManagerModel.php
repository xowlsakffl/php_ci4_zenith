<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvManagerModel extends Model
{
    protected $zenith, $admanager;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
		$this->admanager = \Config\Database::connect('admanager');
    }

    public function getAccounts($data)
    {
        $builder = $this->zenith->table('companies c');
        $builder->select('c.id, c.name');
        $builder->join('company_adaccounts ca', 'c.id = ca.company_id');
        $builder->join('z_admanager.ad_account aac', 'ca.ad_account_id = aac.ad_account_id AND ca.media = aac.media');
        $builder->join('z_admanager.ad ad', 'aac.ad_account_id = ad.account_id AND aac.media = ad.media');
        $builder->join('z_admanager.ad_report ar', 'ad.id = ar.ad_id AND ad.media = ar.media');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(ar.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(ar.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['company_id'])){
            $builder->whereIn('c.id', explode("|",$data['company_id']));
        }
        
        $builder->groupBy('c.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getCampaigns($data)
    {
        $srch = $data['searchData'];
        $builder = $this->admanager->table('ad_report ar');
        $builder->select('
        c.*, 
        SUM(ar.impressions) AS impressions, 
        SUM(ar.click) AS click, 
        SUM(ar.spend) AS spend, 
        SUM(ar.unique_total) as unique_total, 
        SUM(ar.sales) as sales, 
        SUM(ar.margin) as margin
        ');
        $builder->join('ad ad', 'ar.ad_id = ad.id', 'left');
        $builder->join('campaign c', 'ad.campaign_id = c.id', 'left');
        $builder->join('zenith.company_adaccounts ca', 'c.account_id = ca.ad_account_id', 'left');

        if(!empty($srch['dates']['sdate']) && !empty($srch['dates']['edate'])){
            $builder->where('DATE(ar.date) >=', $srch['dates']['sdate']);
            $builder->where('DATE(ar.date) <=', $srch['dates']['edate']);
        }
        
        if(!empty($srch['companies'])){
			$builder->whereIn('ca.company_id', explode("|",$srch['companies']));
        }

        $builder->groupBy('c.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getAdSets($data)
    {
        $srch = $data['searchData'];
        $builder = $this->admanager->table('adgroup ag');
        $builder->select('
        ag.*,
        SUM(ar.impressions) AS impressions, 
        SUM(ar.click) AS click, 
        SUM(ar.spend) AS spend, 
        SUM(ar.unique_total) as unique_total, 
        SUM(ar.sales) as sales, 
        SUM(ar.margin) as margin
        ');
        $builder->join('ad ad', 'ag.id = ad.adgroup_id', 'left');
        $builder->join('ad_report ar', 'ad.id = ar.ad_id', 'left');

        if(!empty($srch['dates']['sdate']) && !empty($srch['dates']['edate'])){
            $builder->where('DATE(ar.date) >=', $srch['dates']['sdate']);
            $builder->where('DATE(ar.date) <=', $srch['dates']['edate']);
        }
        
        $builder->groupBy('ag.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getAds($data)
    {
        $srch = $data['searchData'];
        $builder = $this->admanager->table('ad ad');
        $builder->select('
        ad.*,
        SUM(ar.impressions) AS impressions, 
        SUM(ar.click) AS click, 
        SUM(ar.spend) AS spend, 
        SUM(ar.unique_total) as unique_total, 
        SUM(ar.sales) as sales, 
        SUM(ar.margin) as margin,
        0 as budget
        ');
        $builder->join('ad_report ar', 'ad.id = ar.ad_id', 'left');

        if(!empty($srch['dates']['sdate']) && !empty($srch['dates']['edate'])){
            $builder->where('DATE(ar.date) >=', $srch['dates']['sdate']);
            $builder->where('DATE(ar.date) <=', $srch['dates']['edate']);
        }
        
        $builder->groupBy('ad.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
