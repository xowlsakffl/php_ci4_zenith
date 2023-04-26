<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvKakaoManagerModel extends Model
{
    protected $kakao, $ro_kakao;
    public function __construct()
    {
        $this->kakao = \Config\Database::connect('kakao');
        $this->ro_kakao = \Config\Database::connect('ro_kakao');
    }

    public function getCampaigns($data)
	{
		$builder = $this->kakao->table('mm_campaign A');
        $builder->select('E.name AS account_name, A.id AS id, A.name AS name, A.goal, A.config AS config, A.autoBudget AS autoBudget, SUM(D.imp) AS impression, SUM(D.click) AS click, SUM(D.cost) AS cost, SUM(D.db_count) as unique_total, A.dailyBudgetAmount AS dailyBudgetAmount, SUM(D.sales) AS sales, SUM(D.margin) as margin');
        $builder->select('(SELECT COUNT(*) AS memos FROM mm_memo E WHERE A.id = E.id AND E.type = \'campaign\' AND DATE(E.datetime) >= DATE(NOW())) AS memos');
		$builder->join('mm_adgroup B', 'A.id = B.campaign_id');
		$builder->join('mm_creative C', 'B.id = C.adgroup_id');
		$builder->join('mm_creative_report_basic D', 'C.id = D.id');
		$builder->join('mm_ad_account E', 'E.id = A.ad_account_id');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.ad_account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('A.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('A.id');
		$builder->orderBy('D.create_time', 'desc');
        $builder->orderBy('A.name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getAdsets($data)
	{
		$builder = $this->kakao->table('mm_adgroup B');
        $builder->select('B.id AS id, B.name AS name, A.goal, A.objectiveDetailType, B.config AS config, B.aiConfig, B.aiConfig2, B.bidAmount,
        COUNT(C.id) creatives, SUM(D.imp) impression,
        SUM(D.click) click, SUM(D.cost) cost, SUM(D.db_count) as unique_total, B.dailyBudgetAmount, sum(D.sales) as sales, SUM(D.margin) as margin');
		$builder->join('mm_campaign A', 'B.campaign_id = A.id');
		$builder->join('mm_creative C', 'B.id = C.adgroup_id');
		$builder->join('mm_creative_report_basic D', 'C.id = D.id');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.ad_account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('B.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('B.id');
		$builder->orderBy('D.create_time', 'desc');
        $builder->orderBy('B.name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getAds($data)
	{
		$builder = $this->kakao->table('mm_creative C');
		$builder->select('C.id AS id, A.name AS campaign_name, A.goal AS campaign_goal, C.name AS name, A.type, C.format, C.config AS config, C.aiConfig, C.landingUrl, C.landingType, C.hasExpandable, C.bizFormId, C.imageUrl, C.frequencyCap,
        SUM(D.imp) impression, SUM(D.click) click, SUM(D.cost) cost, SUM(D.db_count) as unique_total, sum(D.sales) as sales, SUM(D.margin) as margin, 0 AS dailyBudgetAmount');
        $builder->join('mm_adgroup B', 'C.adgroup_id = B.id');
        $builder->join('mm_campaign A', 'B.campaign_id = A.id');
		$builder->join('mm_creative_report_basic D', 'C.id = D.id');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.ad_account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('C.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('C.id');
		$builder->orderBy('D.create_time', 'desc');
        $builder->orderBy('C.name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
	}

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as &$row) {
            /* if ($optimization_stat == "1") {
				$row['optimization'] = "ON";	//어른정파고
				$row['optimization_ch'] = "OFF";	// 어린이정파고
			} else if ($optimization_stat == "2") {
				$row['optimization'] = "OFF";	//어른정파고
				$row['optimization_ch'] = "ON";	// 어린이정파고
			} else {
				$row['optimization'] = "OFF";
				$row['optimization_ch'] = "OFF";
			} */

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률

			$row['cpc'] = Calc::cpc($row['cost'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impression']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['cost']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율
        }
        return $result;
    }
    
    public function getAccounts($data)
	{
        $builder = $this->kakao->table('mm_ad_account F');
		$builder->select('F.id, F.name, F.config');
        $builder->join('mm_campaign A', 'F.id = A.ad_account_id');
        $builder->join('mm_adgroup B', 'A.id = B.campaign_id');
        $builder->join('mm_creative C', 'B.id = C.adgroup_id');
		$builder->join('mm_creative_report_basic D', 'C.id = D.id');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        } 

        $builder->where('F.name !=', '');
        $builder->groupBy('F.id');
        $builder->orderBy('F.name', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
	}

    public function getReport($data)
	{
		$builder = $this->kakao->table('mm_creative_report_basic A');
        $builder->select('A.date, A.update_time,
                SUM(A.imp) AS impressions,
                SUM(A.click) AS clicks,   
                (SUM(A.click) / SUM(A.imp)) * 100 AS click_ratio, 
                (SUM(A.db_count) / SUM(A.click)) * 100 AS conversion_ratio,
                SUM(A.cost) AS spend,
                FLOOR(SUM(A.cost) * 0.85) AS spend_ratio, 
                SUM(A.db_count) AS unique_total, 
                IFNULL(SUM(A.cost) / SUM(A.db_count),0) AS unique_one_price,
                SUM(A.db_price) AS unit_price, 
                SUM(A.sales) AS price,  
                SUM(A.margin) AS profit,
                (SUM(A.db_price * A.db_count) - SUM(A.cost)) / SUM(A.db_price * A.db_count) * 100 AS per');
        $builder->join('mm_creative B', 'A.id = B.id', 'left');
        $builder->join('mm_adgroup C', 'B.adgroup_id = C.id', 'left');
        $builder->join('mm_campaign D', 'C.campaign_id = D.id', 'left');
        $builder->join('mm_ad_account E', 'D.ad_account_id = E.id', 'left');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(A.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(A.date) <=', $data['dates']['edate']);
        } 

		if(!empty($data['accounts'])){
			$builder->whereIn('E.id', $data['accounts']);
        }

		$builder->groupBy('A.date');
		$builder->orderBy('A.date', 'ASC');
		$result = $builder->get()->getResultArray();

        return $result;
	}
}
