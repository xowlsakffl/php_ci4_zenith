<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvFacebookManagerModel extends Model
{
    protected $facebook, $ro_facebook;
    public function __construct()
    {
        $this->facebook = \Config\Database::connect('facebook');
        $this->ro_facebook = \Config\Database::connect('ro_facebook');
    }
    
    public function getCampaigns($data)
    {
        $builder = $this->facebook->table('fb_campaign A');
        $builder->select('A.campaign_id AS id, A.campaign_name AS name, A.status AS status, A.budget AS budget, A.is_updating AS is_updating, A.ai2_status, COUNT(B.adset_id) AS adsets, COUNT(C.ad_id) AS ads, SUM(D.impressions) AS impressions, SUM(D.inline_link_clicks) AS click, SUM(D.spend) AS spend, D.ad_id, SUM(D.sales) as sales, A.account_id, SUM(D.db_count) as unique_total, SUM(D.margin) as margin');
        /* $builder->select('(SELECT COUNT(*) AS memos FROM fb_memo F WHERE A.campaign_id = F.id AND F.type = "campaign" AND DATE(F.datetime) >= DATE(NOW()) AND is_done = 0) AS memos'); */
        $builder->join('fb_adset B', 'A.campaign_id = B.campaign_id');
        $builder->join('fb_ad C', 'B.adset_id = C.adset_id');
        $builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id');
        $builder->join('fb_ad_account E', 'A.account_id = E.ad_account_id');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }
        
        if(!empty($data['businesses'])){
			$builder->whereIn('E.business_id', $data['businesses']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.account_id', $data['accounts']);
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('A.campaign_name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('A.campaign_id');
		$builder->orderBy('D.create_date', 'desc');
        $builder->orderBy('A.campaign_name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
    }

	public function getAdsets($data)
	{
		$builder = $this->facebook->table('fb_campaign A');
		$builder->select('B.adset_id AS id, B.adset_name AS name, B.status AS status, B.budget_type, A.is_updating AS is_updating, B.lsi_conversions, B.lsi_status, COUNT(C.ad_id) ads, SUM(D.impressions) impressions, SUM(D.inline_link_clicks) click, SUM(D.spend) spend, B.budget, SUM(D.sales) as sales, SUM(D.db_count) as unique_total, SUM(D.margin) as margin');
		$builder->join('fb_adset B', 'A.campaign_id = B.campaign_id');
		$builder->join('fb_ad C', 'B.adset_id = C.adset_id');
		$builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id');
		
		if(!empty($data['businesses'])){
			$builder->join('fb_ad_account E', 'A.account_id = E.ad_account_id');
			$builder->whereIn('E.business_id', $data['businesses']);
        }

		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('B.adset_name', $data['stx']);
            $builder->groupEnd();
        }

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.account_id', $data['accounts']);
        }

		$builder->groupBy('B.adset_id');
		$builder->orderBy('D.create_date', 'desc');
		$builder->orderBy('B.adset_name', 'asc');
		$result = $builder->get()->getResultArray();

		return $result;
		/* if (@count($args['ids'][0]) > 0) {
			$campaigns = "'" . implode("','", $args['ids'][0]) . "'";
			$sql .= " AND B.campaign_id IN (" . $campaigns . ")";
		} else if (@count($args['accounts']) > 0 || @count($args['businesses']) > 0) {
			$sql = "SELECT  B.adset_id AS id, B.adset_name AS name, B.status AS status, B.budget_type, A.is_updating AS is_updating, B.lsi_conversions, B.lsi_status,
					COUNT(C.ad_id) ads, SUM(D.impressions) impressions,
					SUM(D.inline_link_clicks) inline_link_clicks, SUM(D.spend) spend, 0 AS total, 0 AS unique_total, B.budget, sum(D.sales) as sales
				FROM fb_campaign A, fb_adset B, fb_ad C, fb_ad_insight_history D, fb_ad_account E
				WHERE A.campaign_id = B.campaign_id AND B.adset_id = C.adset_id AND C.ad_id = D.ad_id AND A.account_id = E.ad_account_id";
			if (count($args['businesses']) > 0) {
				$businesses = "'" . implode("','", $args['businesses']) . "'";
				$sql .= " AND E.business_id IN (" . $businesses . ")";
			}
			if (count($args['accounts']) > 0) {
				$accounts = "'" . implode("','", $args['accounts']) . "'";
				$sql .= " AND A.account_id IN (" . $accounts . ")";
			}
		} */
	}

	public function getAds($data)
	{
		$builder = $this->facebook->table('fb_campaign A');
		$builder->select('C.ad_id AS id, C.ad_name AS name, C.status AS status, E.thumbnail, E.link, A.is_updating AS is_updating, SUM(D.impressions) AS impressions, SUM(D.inline_link_clicks) AS click, SUM(D.spend) AS spend, 0 AS budget, SUM(D.sales) AS sales, SUM(D.db_count) as unique_total, SUM(D.margin) as margin');
		$builder->join('fb_adset B', 'A.campaign_id = B.campaign_id');
		$builder->join('fb_ad C', 'B.adset_id = C.adset_id');
		$builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id', 'left');
		$builder->join('fb_adcreative E', 'D.ad_id = E.ad_id', 'left');

		if(!empty($data['businesses'])){
			$builder->join('fb_ad_account F', 'A.account_id = F.ad_account_id');
			$builder->whereIn('F.business_id', $data['businesses']);
        }

		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('C.ad_name', $data['stx']);
            $builder->groupEnd();
        }

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

		if(!empty($data['accounts'])){
			$builder->whereIn('A.account_id', $data['accounts']);
        }

		$builder->groupBy('C.ad_id');
		$builder->orderBy('D.create_date', 'desc');
		$builder->orderBy('C.ad_name', 'asc');
		$result = $builder->get()->getResultArray();

		return $result;
	}

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as &$row) {
            /* $optimization_stat = $this->getOptimization($param, $row['id']);
			$optimization_goal = $this->getOptimization_goal($param, $row['id']);
			
            if ($param == "campaigns") { // 캠페인단 ai
				if ($optimization_stat == "901" || $optimization_stat == "801") {
					$row['optimization_campaign'] = "ON";
				} else {
					$row['optimization_campaign'] = "OFF";
				}

				if ($optimization_goal == "902") {
					$row['optimization_goal_campaign'] = "ON";
				} else {
					$row['optimization_goal_campaign'] = "OFF";
				}
			} else if ($param == "adsets") { // 광고세트단 ai
				if ($optimization_stat == "801") {
					$row['optimization_adset'] = "ON";
				} else {
					$row['optimization_adset'] = "OFF";
				}
			} else if ($param == "ads") { // 광고 ai
				if ($optimization_stat == "701") {
					$row['optimization_ad'] = "ON";
				} else {
					$row['optimization_ad'] = "OFF";
				}
			} else {	// 광고세트단 ai
				if ($optimization_stat == "1") {
					$row['optimization'] = "ON";	//공격ai
					$row['optimization_ch'] = "OFF";	// 공격ai
					$row['optimization_top'] = "OFF";	// 3만ai
				} else if ($optimization_stat == "2") {
					$row['optimization'] = "OFF";	//공격ai
					$row['optimization_ch'] = "ON";	// 안정ai
					$row['optimization_top'] = "OFF";	// 3만ai
				} else if ($optimization_stat == "3") {
					$row['optimization'] = "OFF";	//공격ai
					$row['optimization_ch'] = "OFF";	// 안정ai
					$row['optimization_top'] = "ON";	// 3만ai
				} else {
					$row['optimization'] = "OFF";	//공격ai
					$row['optimization_ch'] = "OFF";	// 안정ai
					$row['optimization_top'] = "OFF";	// 3만ai
				}

				if ($optimization_goal == "100") {
					$row['optimization_goal'] = "ON";
				} else {
					$row['optimization_goal'] = "OFF";
				}
			} */

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률


			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율

			switch (!empty($row['budget_type'])) {
				case 'daily':
					$row['budget_txt'] = '일일';
					break;
				case 'lifetime':
					$row['budget_txt'] = '소진';
					break;
				default:
					$row['budget_txt'] = '';
					break;
			}
        }
        return $result;
    }
    
    // 정파고 on/off 확인
	/* private function getOptimization($param, $id)
	{
		if ($param == "campaigns") { //캠페인단 ai
            $builder = $this->facebook->table('fb_optimization_campaign');
            $builder->select('campaign_id, type');
            $builder->where('campaign_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['campaign_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		} else if ($param == "ads") { //광고 ai
            $builder = $this->facebook->table('fb_optimization_ad');
            $builder->select('ad_id, type');
            $builder->where('ad_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['ad_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		} else if ($param == "adsets") { //광고세트단 ai
            $builder = $this->facebook->table('fb_optimization_adset');
            $builder->select('adset_id, type');
            $builder->where('adset_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['adset_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		} else { //광고세트단 ai
            $builder = $this->facebook->table('fb_optimization');
            $builder->select('adset_id, type');
            $builder->where('adset_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['adset_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		}
	}

    // 목표ai on/off 확인
	private function getOptimization_goal($param, $id)
	{
		if ($param == "campaigns") { //캠페인단 목표 ai
            $builder = $this->facebook->table('fb_optimization_goal_campaign');
            $builder->select('campaign_id, type');
            $builder->where('campaign_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['campaign_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		} else { //광고세트단 목표 ai
            $builder = $this->facebook->table('fb_optimization_goal');
            $builder->select('adset_id, type');
            $builder->where('adset_id', $id);
            $row = $builder->get()->getRowArray();

			if (!empty($row['adset_id'])) {
				return $row['type'];
			} else {
				return "off";
			}
		}
	}
 */
    public function getAccounts($data)
	{
        $builder = $this->facebook->table('fb_ad_account F');
		$builder->select('F.business_id, F.ad_account_id, F.name, F.status, F.db_count, SUM(D.db_count) AS db_sum, COUNT(DISTINCT D.date) AS date_count');
        $builder->join('fb_campaign A', 'F.ad_account_id = A.account_id', 'left');
        $builder->join('fb_adset B', 'A.campaign_id = B.campaign_id', 'left');
        $builder->join('fb_ad C', 'B.adset_id = C.adset_id', 'left');
		$builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id', 'left');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        } 

		$builder->where('F.is_admin', 0);
        $builder->where('F.name !=', '');

        if(!empty($data['businesses'])){
			$builder->whereIn('F.business_id', $data['businesses']);
        }

        $builder->groupBy('F.ad_account_id');
        $builder->orderBy('F.name', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
	}

    public function getDisapproval()
	{
        $builder = $this->facebook->table('fb_ad_account acc');
		$builder->select('acc.ad_account_id, acc.name AS account_name, ac.campaign_id, ac.campaign_name AS campaign_name, ag.adset_id, ag.adset_name AS adset_name, ad.ad_id, ad.ad_name, as.link, ad.effective_status, ad.status, ad.created_time, ad.updated_time');
		$builder->join('fb_campaign ac', 'ac.account_id = acc.ad_account_id', 'left');
		$builder->join('fb_adset ag', 'ag.campaign_id = ac.campaign_id', 'left');
		$builder->join('fb_ad ad', 'ad.adset_id = ag.adset_id', 'left');
		$builder->join('fb_adcreative as', 'ad.ad_id = as.ad_id', 'left');
		$builder->where('ad.effective_status', 'DISAPPROVED');
		$builder->where('acc.status', 1);
		$builder->where('ac.status', 'ACTIVE');
		$builder->where('ag.status', 'ACTIVE');
		$builder->where('ad.status', 'ACTIVE');
		$builder->where('ad.created_time >=', '2022-01-01 00:00:00');
		$builder->orderBy('ad.created_time', 'DESC');
		$result = $builder->get()->getResultArray();

        return $result;
	}

	public function getReport($data)
	{
		$builder = $this->facebook->table('fb_ad_insight_history A');
        $builder->select('A.date, 
                SUM(A.impressions) AS impressions,
                SUM(A.inline_link_clicks) AS click,
                (SUM(A.inline_link_clicks) / SUM(A.impressions)) * 100 AS click_ratio,
                (SUM(A.db_count) / SUM(A.inline_link_clicks)) * 100 AS conversion_ratio,
                SUM(A.spend) AS spend,
                SUM(A.db_count) AS unique_total,
                IFNULL(SUM(A.spend) / SUM(A.db_count), 0) AS unique_one_price,
                SUM(A.db_price) AS unit_price,
                SUM(A.sales) AS price,
                SUM(A.margin) AS profit,
                (SUM(A.db_price * A.db_count) - SUM(A.spend)) / SUM(A.db_price * A.db_count) * 100 AS per');
        $builder->join('fb_ad B', 'A.ad_id = B.ad_id', 'left');
        $builder->join('fb_adset C', 'B.adset_id = C.adset_id', 'left');
        $builder->join('fb_campaign D', 'C.campaign_id = D.campaign_id', 'left');
        $builder->join('fb_ad_account E', 'D.account_id = E.ad_account_id', 'left');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(A.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(A.date) <=', $data['dates']['edate']);
        } 

		if(!empty($data['businesses'])){
			$builder->whereIn('E.business_id', $data['businesses']);
        }

		if(!empty($data['accounts'])){
			$builder->whereIn('D.account_id', $data['accounts']);
        }

		$builder->groupBy('A.date');
		$builder->orderBy('A.date', 'ASC');
		$result = $builder->get()->getResultArray();

        return $result;
	}
}
