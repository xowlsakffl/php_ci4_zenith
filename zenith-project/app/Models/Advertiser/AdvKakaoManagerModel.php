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
        
        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
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
			E.id AS media_account_id,
            E.config AS status,
            E.isAdminStop AS isAdminStop,
            "" AS is_exposed,
			"" AS db_count,
            "" AS db_sum,
			"" AS date_count
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
        
        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
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
        $builder->groupBy('E.id');

        return $builder;
	}

    public function getCampaigns($data)
	{
		$subQuery = $this->db->table('z_moment.mm_creative_report_basic A');
        $subQuery->select('
        D.ad_account_id as customerId,
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
        ');
        $subQuery->join('z_moment.mm_creative B', 'A.id = B.id');
		$subQuery->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		$subQuery->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        if(!empty($data['sdate']) && !empty($data['edate'])){
            $subQuery->where('DATE(A.date) >=', $data['sdate']);
            $subQuery->where('DATE(A.date) <=', $data['edate']);
        }
        $subQuery->groupBy('D.id');

        $builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
		G.id AS company_id, 
		G.name AS company_name, 
        E.id as customerId,
		"kakao" AS media, 
        sub.id AS id, 
		sub.name AS name, 
		sub.status,
		sub.budget,
        0 AS bidamount, 
		"" AS bidamount_type,
        "" AS biddingStrategyType,
		sub.impressions, 
		sub.click, 
		sub.spend, 
		sub.sales, 
		sub.unique_total, 
		sub.margin');
		$builder->join('z_moment.mm_ad_account E', 'sub.customerId = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('sub.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('sub.id');
        $builder->orderBy('sub.name', 'asc');
        return $builder;
	}

    public function getAdsets($data)
	{
        $subQuery = $this->db->table('z_moment.mm_creative_report_basic A');
		$subQuery->select('
		C.campaign_id as campaign_id, 
		C.id AS id, 
        C.name AS name, 
        C.config AS status, 
        C.dailyBudgetAmount AS budget, 
        C.bidAmount AS bidamount,
        SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin');
		$subQuery->join('z_moment.mm_creative B', 'A.id = B.id');
		$subQuery->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('DATE(A.date) >=', $data['sdate']);
			$subQuery->where('DATE(A.date) <=', $data['edate']);
		}
		$subQuery->groupBy('C.id');

        $builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name,
        E.id as customerId,
        D.id AS campaign_id,
        "kakao" AS media, 
        sub.id AS id, 
        sub.name AS name, 
        sub.status AS status, 
        sub.budget AS budget, 
        "" AS biddingStrategyType,
        sub.bidamount AS bidamount, 
        0 AS campaign_bidamount,
		"" AS bidamount_type,
        sub.impressions, 
        sub.click, 
        sub.spend, 
        sub.sales, 
        sub.unique_total, 
        sub.margin
        ');
		$builder->join('z_moment.mm_campaign D', 'sub.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('sub.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('sub.id');
        $builder->orderBy('sub.name', 'asc');
        return $builder;
	}

    public function getAds($data)
	{
        $subQuery = $this->db->table('z_moment.mm_creative_report_basic A');
		$subQuery->select('
		B.adgroup_id as adgroup_id, 
		B.id AS id, 
        B.name AS name, 
        B.code AS code,
        B.config AS status, 
        B.imageUrl AS thumbnail,
        B.landingUrl AS landingUrl,
        SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin');
		$subQuery->join('z_moment.mm_creative B', 'A.id = B.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('DATE(A.date) >=', $data['sdate']);
			$subQuery->where('DATE(A.date) <=', $data['edate']);
		}
		$subQuery->groupBy('B.id');

        $builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
        G.id AS company_id,
		G.name AS company_name,
        E.id as customerId,
        D.id AS campaign_id,
        C.id AS adset_id,
        "kakao" AS media, 
        sub.id AS id, 
        sub.name AS name, 
        sub.code AS code,
        sub.status AS status, 
        sub.thumbnail AS thumbnail,
        "" AS assets,
        sub.landingUrl AS landingUrl,
        0 AS budget, 
        0 AS bidamount, 
		"" AS bidamount_type,
        sub.impressions, 
        sub.click, 
        sub.spend, 
        sub.sales, 
        sub.unique_total, 
        sub.margin 
        ');
		$builder->join('z_moment.mm_adgroup C', 'sub.adgroup_id = C.id');
		$builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
		$builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $builder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['account'])){
			$builder->whereIn('E.id', explode("|",$data['account']));
        }

        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('sub.name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('sub.id');
        $builder->orderBy('sub.name', 'asc');
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

        if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
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

    public function getDisapproval()
	{
        $builder = $this->db->table('z_moment.mm_ad_account acc');
		$builder->select('acc.id AS account_id');
		$builder->join('z_moment.mm_campaign ac', 'ac.ad_account_id = acc.id', 'left');
		$builder->join('z_moment.mm_adgroup ag', 'ag.campaign_id = ac.id', 'left');
		$builder->join('z_moment.mm_creative ad', 'ad.adgroup_id = ag.id', 'left');
        $builder->where("(ad.reviewStatus = 'REJECTED' OR ad.reviewStatus = 'MODIFICATION_REJECTED')");
        $builder->where('ad.creativeStatus is NOT NULL', NULL);
        $builder->where('acc.config', 'ON');
        $builder->where('ac.config', 'ON');
        $builder->where('ag.config', 'ON');
        $builder->where('ad.config', 'ON');
        // $builder->orderBy('ad.create_time', 'DESC');
        $builder->groupBy('acc.id');
		$result = $builder->get()->getResultArray();

        return $result;
	}

    public function getCampaignById($ids)
    {
		$builder = $this->db->table('z_moment.mm_campaign');
		$builder->select('"카카오" as media, "캠페인" as type, id, name, config as status');
		$builder->whereIn('id', $ids);
        
        return $builder;
    }

	public function getAdgroupById($ids)
    {
		$builder = $this->db->table('z_moment.mm_adgroup');
		$builder->select('"카카오" as media, "광고그룹" as type, id, name, config as status');
		$builder->whereIn('id', $ids);
		
        return $builder;
    }

	public function getAdById($ids)
    {
		$builder = $this->db->table('z_moment.aw_ad');
		$builder->select('"카카오" as media, "광고" as type, id, name, config as status');
		$builder->whereIn('id', $ids);
		
        return $builder;
    }

    public function updateCode($data) {
		$result = [];
        $this->db->transStart();
        $builder = $this->db->table('z_moment.mm_creative');  
		$builder->set('code', $data['code']);
		$builder->where('id', $data['id']);
		$builder->update();
        $queryResult = $this->db->transComplete();
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
