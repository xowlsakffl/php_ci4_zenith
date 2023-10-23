<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvKakaoManagerModel extends Model
{
    public function getAccounts($data)
	{
        $builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }
        
        if(!empty($data['kakaoCheck'])){
			switch ($data['type']) {
				case 'campaigns':
                    $builder->whereIn('D.id', $data['kakaoCheck']);
                    break;
                case 'adsets':
                    $builder->whereIn('C.id', $data['kakaoCheck']);
                    break;
                case 'ads':
                    $builder->whereIn('B.id', $data['kakaoCheck']);
                    break;
				default:
					break;
			}
        }
        
        return $builder;
	}

    public function getMediaAccounts($data)
	{
        $builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
            "kakao" AS media,
			E.name AS media_account_name,
			E.id AS media_account_id
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }
        
        if(!empty($data['kakaoCheck'])){
			switch ($data['type']) {
				case 'campaigns':
                    $builder->whereIn('D.id', $data['kakaoCheck']);
                    break;
                case 'adsets':
                    $builder->whereIn('C.id', $data['kakaoCheck']);
                    break;
                case 'ads':
                    $builder->whereIn('B.id', $data['kakaoCheck']);
                    break;
				default:
					break;
			}
        }

        return $builder;
	}

    public function getCampaigns($data)
	{
		$builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name,
        "kakao" AS media, 
        D.id AS id, 
        D.name AS name, 
        D.config AS status, 
        D.dailyBudgetAmount AS budget, 
        SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin, 
        E.id as customerId
        ');
        //$builder->select('(SELECT COUNT(*) AS memos FROM mm_memo E WHERE A.id = E.id AND E.type = \'campaign\' AND DATE(E.datetime) >= DATE(NOW())) AS memos');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('D.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('D.id');
		$builder->orderBy('A.create_time', 'desc');
        $builder->orderBy('D.name', 'asc');
        return $builder;
	}

    public function getAdsets($data)
	{
        $builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name,
        "kakao" AS media, 
        D.id AS campaign_id,
        C.id AS id, 
        C.name AS name, 
        C.config AS status, 
        C.dailyBudgetAmount AS budget, 
        SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin, 
        E.id as customerId
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('C.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('C.id');
		$builder->orderBy('A.create_time', 'desc');
        $builder->orderBy('C.name', 'asc');
        return $builder;
	}

    public function getAds($data)
	{
        $builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name,
        "kakao" AS media, 
        D.id AS campaign_id,
        C.id AS adset_id,
        B.id AS id, 
        B.name AS name, 
        B.code AS code,
        B.config AS status, 
        0 AS budget, 
        SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin, 
        E.id as customerId
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('B.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('B.id');
		$builder->orderBy('A.create_time', 'desc');
        $builder->orderBy('B.name', 'asc');
        return $builder;
	}

    public function getReport($data)
	{
        $builder = $this->db->table('z_moment.mm_creative_report_basic A');
        $builder->select('
        A.date,
        SUM(A.imp) AS impressions,
        SUM(A.click) AS click,   
        (SUM(A.click) / SUM(A.imp)) * 100 AS click_ratio, 
        (SUM(A.db_count) / SUM(A.click)) * 100 AS conversion_ratio,
        SUM(A.cost) AS spend,
        SUM(A.db_count) AS unique_total, 
        IFNULL(SUM(A.cost) / SUM(A.db_count),0) AS unique_one_price,
        SUM(A.db_price) AS unit_price, 
        SUM(A.sales) AS price,  
        SUM(A.margin) AS profit,
        (SUM(A.db_price * A.db_count) - SUM(A.cost)) / SUM(A.db_price * A.db_count) * 100 AS per
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
		$builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        
		if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

        if(!empty($data['kakaoCheck'])){
			switch ($data['type']) {
				case 'campaigns':
                    $builder->whereIn('D.id', $data['kakaoCheck']);
                    break;
                case 'adsets':
                    $builder->whereIn('C.id', $data['kakaoCheck']);
                    break;
                case 'ads':
                    $builder->whereIn('B.id', $data['kakaoCheck']);
                    break;
				default:
					break;
			}
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        $builder->groupBy('A.date');
        return $builder;
	}

    /* public function getDisapproval()
	{
        $builder = $this->kakao->table('mm_ad_account acc');
		$builder->select('acc.id AS account_id, acc.name AS customer_name,
        ac.id AS campaign_id, ac.name AS campaign_name,
        ag.id AS adgroup_id, ag.name AS adgroup_name,
        ad.id, ad.name, ad.landingUrl, ad.config AS status, ad.reviewStatus, ad.creativeStatus, ad.create_time, ad.update_time');
		$builder->join('mm_campaign ac', 'ac.ad_account_id = acc.id', 'left');
		$builder->join('mm_adgroup ag', 'ag.campaign_id = ac.id', 'left');
		$builder->join('mm_creative ad', 'ad.adgroup_id = ag.id', 'left');
        $builder->where("(ad.reviewStatus = 'REJECTED' OR ad.reviewStatus = 'MODIFICATION_REJECTED')");
        $builder->where('ad.creativeStatus is NOT NULL', NULL);
        $builder->where('acc.config', 'ON');
        $builder->where('ac.config', 'ON');
        $builder->where('ag.config', 'ON');
        $builder->where('ad.config', 'ON');
        $builder->orderBy('ad.create_time', 'DESC');
		$result = $builder->get()->getResultArray();

        return $result;
	} */

    public function updateCode($data) {
		$result = [];
        $builder = $this->db->table('z_moment.mm_creative');  
		$builder->set('code', $data['code']);
		$builder->where('id', $data['id']);
		$queryResult = $builder->update();
        
		if($queryResult){
			$result['code'] = $data['code'] ?? '';
		}

		return $result;
	}

    public function getAccountByCampaignId($campaignIds) {
        $builder = $this->db->table('z_moment.mm_campaign A');  
		$builder->select('id, ad_account_id');
		$builder->whereIn('id', $campaignIds);
		$builder->groupBy('id');
		$result = $builder->get()->getResultArray();

		return $result;
	}

	public function setUpdatingByAds($campaignIds){
		$this->db->transStart();
		$builder = $this->db->table('z_moment.mm_campaign');
		$builder->whereIn('id', $campaignIds);
		$builder->set('is_updating', 1);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}
}
