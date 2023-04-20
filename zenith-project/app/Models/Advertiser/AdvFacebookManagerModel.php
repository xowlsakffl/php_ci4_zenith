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

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }
        
        if(!empty($data['businesses'])){
            $businesses = "'" . implode("','", $data['businesses']) . "'";
			$builder->whereIn('E.business_id', $businesses);
        }

        if(!empty($data['accounts'])){
            $accounts = "'" . implode("','", $data['accounts']) . "'";
			$builder->whereIn('A.account_id', $accounts);
        }

        if(!empty($data['keyword'])){
            $builder->groupStart();
            $builder->like('A.campaign_name', $data['keyword']);
            $builder->groupEnd();
        }

        $builder->groupBy('A.campaign_id');
		$builder->orderBy('D.create_date', 'desc');
        /* if(!empty($data['sort'])){
            $builder->orderBy('D.create_date', 'desc');
        }else{
            $builder->orderBy('D.create_date', 'desc');
        } */

        $builder->orderBy('A.campaign_name', 'asc');
        $result = $builder->get()->getResultArray();
        return $result;
    }

	public function getAdsets($data)
	{
		$builder = $this->facebook->table('fb_campaign A');
		$builder->select('B.adset_id AS id, B.adset_name AS name, B.status AS status, B.budget_type, A.is_updating AS is_updating, B.lsi_conversions, B.lsi_status, COUNT(C.ad_id) ads, SUM(D.impressions) impressions, SUM(D.inline_link_clicks) inline_link_clicks, SUM(D.spend) spend, 0 AS total, 0 AS unique_total, B.budget, SUM(D.sales) as sales');
		$builder->join('fb_adset B', 'A.campaign_id = B.campaign_id');
		$builder->join('fb_ad C', 'B.adset_id = C.adset_id');
		$builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
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

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as $row) {
            $status = $this->getStat($param, $row['id'], $dates['sdate'], $dates['edate']);
            $optimization_stat = $this->getOptimization($param, $row['id']);
			$optimization_goal = $this->getOptimization_goal($param, $row['id']);
			
			$row['sales'] = $row['sales'];	//매출액
			if(isset($status['unique_total'])){
				$row['unique_total'] = $status['unique_total'];//유효db
			}else{
				$row['unique_total'] = 0;
			}

			if(isset($status['margin'])){
				$row['margin'] = $status['margin'];//수익
			}else{
				$row['margin'] = 0;
			}
			
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
			}

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률


			$row['cpc'] = Calc::cpc($row['spend'], $row['inline_link_clicks']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['inline_link_clicks'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['inline_link_clicks']);	//전환율

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

			$rows[] = $row;
        }
        return $rows;
    }

    private function getStat($param, $id, $sdate, $edate)
	{
        $builder = $this->facebook->table('fb_ad_list');
        $builder->select('GROUP_CONCAT( ad_id SEPARATOR ",") AS ad_id, ad_name');

		if ($param == "ads") {
            $builder->where('ad_id', $id);
            $builder->groupBy('ad_id');
		} else if ($param == "adsets") {
            $builder->where('adset_id', $id);
            $builder->groupBy('adset_id');
		} else {
            $builder->where('campaign_id', $id);
            $builder->groupBy('campaign_id');
		}
		
        $result = $builder->get()->getResultArray();
		foreach($result as $row){
			$stat = $this->getEventStat_hotevent($row['ad_id'], $sdate, $edate);
		}
		return $stat;
	}
    
    private function getEventStat_hotevent($id, $sdate, $edate)
	{
		/*테이블 변경 임시*/
		$ad_id = explode(",", $id);
        $builder = $this->facebook->table('fb_lead_count');
        $builder->select('SUM(db_count) as unique_total, SUM(margin) as margin');
        $builder->where('DATE(date) >=', $sdate.' 00:00:00');
        $builder->where('DATE(date) <=', $edate.' 23:59:59');

        if(!empty($ad_id)){     
			$builder->whereIn('ad_id', $ad_id);
        }

		$result = $builder->get()->getRowArray();
		return $result;
	}
    
    // 정파고 on/off 확인
	private function getOptimization($param, $id)
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

    public function getAccounts($data)
	{
        $builder = $this->facebook->table('fb_ad_account F');
		$builder->select('F.business_id, F.ad_account_id, F.name, F.status, F.db_count, SUM(E.db_count) AS db_sum, COUNT(DISTINCT D.date) AS date_count');
        $builder->join('fb_campaign A', 'F.ad_account_id = A.account_id', 'left');
        $builder->join('fb_adset B', 'A.campaign_id = B.campaign_id', 'left');
        $builder->join('fb_ad C', 'B.adset_id = C.adset_id', 'left');
		$builder->join('fb_ad_insight_history D', 'C.ad_id = D.ad_id', 'left');
		/*테이블변경 임시*/
		$builder->join('fb_lead_count E', 'C.ad_id = E.ad_id AND D.date = E.date', 'left');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        } 

		$builder->where('F.is_admin', 0);
        $builder->where('F.name !=', '');

        if(!empty($data['businesses'])){
            $businesses = "'" . implode("','", $data['businesses']) . "'";
			$builder->whereIn('F.business_id', $businesses);
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
}
