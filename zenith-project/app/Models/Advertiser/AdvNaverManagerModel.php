<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvNaverManagerModel extends Model
{
    protected $naver, $ro_naver;
    public function __construct()
    {
        $this->naver = \Config\Database::connect('naver');
        $this->ro_naver = \Config\Database::connect('ro_naver');
    }

    public function getCampaigns($data)
	{
		$builder = $this->naver->table('gfa_ad_report_history');
        $builder->select('campaign_id AS id, campaign_name AS name, 
        COUNT(adset_id) AS adgroups, COUNT(ad_id) AS ads, SUM(impression) AS impressions, SUM(click) AS click, SUM(sales) AS spend, SUM(db_sales) AS sales, SUM(db_count) as unique_total, 0 AS budget, 0 AS status');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(date) >=', $data['dates']['sdate']);
            $builder->where('DATE(date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('campaign_id');
		$builder->orderBy('create_time', 'desc');
        $builder->orderBy('name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getAdsets($data)
	{
		$builder = $this->naver->table('gfa_ad_report_history');
        $builder->select('adset_id AS id, adset_name AS name,
        COUNT(ad_id) AS ads, SUM(impression) AS impressions,
        SUM(click) AS click, SUM(db_count) as unique_total, SUM(sales) AS spend, SUM(db_sales) AS sales, 0 AS budget, 0 AS status');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(date) >=', $data['dates']['sdate']);
            $builder->where('DATE(date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('adset_id');
		$builder->orderBy('create_time', 'desc');
        $builder->orderBy('adset_name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getAds($data)
	{
		$builder = $this->naver->table('gfa_ad_report_history');
		$builder->select('ad_id AS id, ad_name AS name, SUM(impression) AS impressions, SUM(click) AS click, SUM(db_count) as unique_total, SUM(sales) AS spend, SUM(db_sales) AS sales, 0 AS budget, 0 AS status');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(date) >=', $data['dates']['sdate']);
            $builder->where('DATE(date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('ad_id');
		$builder->orderBy('create_time', 'desc');
        $builder->orderBy('ad_name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as &$row) {
            $row['cost_ori'] = $row['spend'];
			$row['spend'] = $row['spend']/1.1;
            $row['margin'] = $row['sales'] - $row['spend'];

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률

			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
		 	$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
		 	$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율
        }
        return $result;
    }

    public function getAccounts($data)
	{
        $builder = $this->naver->table('gfa_ad_account A');
		$builder->select('A.account_id AS id, A.name');
        $builder->join('gfa_ad_report_history B', 'A.account_id = B.account_id', 'left');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(B.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(B.date) <=', $data['dates']['edate']);
        } 

        $builder->where('A.name !=', '');
        $builder->groupBy('A.account_id');
        $builder->orderBy('A.name', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
	}

    public function getReport($data)
	{
		$builder = $this->naver->table('gfa_ad_report_history A');
        $builder->select('A.date, SUM(A.impression) AS impressions, SUM(A.click) AS click, (SUM(A.click) / SUM(A.impression)) * 100 AS click_ratio, (SUM(A.db_count) / SUM(A.click)) * 100 AS conversion_ratio, SUM(A.sales)/1.1 AS spend, FLOOR(SUM(A.sales)/1.1 * 0.85) AS spend_ratio, SUM(A.db_count) AS unique_total, IFNULL(SUM(A.sales)/1.1 / SUM(A.db_count),0) AS unique_one_price, SUM(A.db_price) AS unit_price, SUM(A.db_sales) AS price, SUM(A.margin) AS profit,  
        (SUM(A.db_price * A.db_count) - SUM(A.sales)/1.1) / SUM(A.db_price * A.db_count) * 100 AS per');
        $builder->join('gfa_ad_account B', 'A.account_id = B.account_id', 'left');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(A.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(A.date) <=', $data['dates']['edate']);
        } 

		if(!empty($data['accounts'])){
			$builder->whereIn('B.account_id', $data['accounts']);
        }

		$builder->groupBy('A.date');
		$builder->orderBy('A.date', 'ASC');
		$result = $builder->get()->getResultArray();

        return $result;
	}
}
