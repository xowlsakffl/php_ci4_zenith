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
        $builder->select('E.name AS account_name, A.id AS id, A.name AS name, A.goal, A.config AS config, A.autoBudget AS autoBudget, SUM(D.imp) AS impression, SUM(D.click) AS click, SUM(D.cost) AS cost, 0 AS total, 0 AS unique_total, A.dailyBudgetAmount AS dailyBudgetAmount, SUM(D.sales) AS sales');
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
        $builder->join('mm_campaign A', 'F.id = A.ad_account_id', 'left');
        $builder->join('mm_adgroup B', 'A.id = B.campaign_id', 'left');
        $builder->join('mm_creative C', 'B.id = C.adgroup_id', 'left');
		$builder->join('mm_creative_report_basic D', 'C.id = D.id', 'left');

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
}
