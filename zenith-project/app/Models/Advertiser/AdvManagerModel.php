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

    public function getCampaigns($data)
    {
        $srch = $data['searchData'];
        $builder = $this->admanager->table('campaign c');
        $builder->select('
        c.*, 
        ar.ad_id,
        SUM(ar.impressions) AS impressions, 
        SUM(ar.click) AS click, 
        SUM(ar.spend) AS spend, 
        SUM(ar.unique_total) as unique_total, 
        SUM(ar.sales) as sales, 
        SUM(ar.margin) as margin
        ');
        $builder->join('ad ad', 'c.id = ad.campaign_id', 'left');
        $builder->join('ad_report ar', 'ad.id = ar.ad_id', 'left');

        if(!empty($srch['dates']['sdate']) && !empty($srch['dates']['edate'])){
            $builder->where('DATE(ar.date) >=', $srch['dates']['sdate']);
            $builder->where('DATE(ar.date) <=', $srch['dates']['edate']);
        }
        
        $builder->groupBy('c.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
