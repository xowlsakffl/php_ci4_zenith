<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvFacebookManagerModel extends Model
{
	public function getAccounts($data)
	{
		$builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name
		');
		$builder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $builder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

        if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }
		
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }
		
		if(!empty($data['facebookCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.campaign_id', $data['facebookCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.adset_id', $data['facebookCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.ad_id', $data['facebookCheck']);
					break;
				default:
					break;
			}
        }

        return $builder;
	}

	public function getMediaAccounts($data)
	{
		$builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
			"facebook" AS media,
			E.name AS media_account_name,
			E.ad_account_id AS media_account_id,
			E.status AS status,
			"" AS isAdminStop,
			"" AS is_exposed,
			E.db_count AS db_count,
			SUM(A.db_count) AS db_sum,
			COUNT(DISTINCT A.date) AS date_count
		');
		$builder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $builder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
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
		
		if(!empty($data['facebookCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.campaign_id', $data['facebookCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.adset_id', $data['facebookCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.ad_id', $data['facebookCheck']);
					break;
				default:
					break;
			}
        }
		$builder->groupBy('E.ad_account_id');
		
        return $builder;
	}

	public function getOnlyAdAccount($data)
	{
		$builder = $this->db->table('z_facebook.fb_ad_account');
        $builder->select('
			"facebook" AS media,
			name AS media_account_name,
			ad_account_id AS media_account_id,
			"" AS is_exposed,
			"" AS canManageClients,
			db_count AS db_count
		');
		$builder->where('status', 1);
		$builder->where('perm', 1);

		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('name', $data['stx']);
            $builder->groupEnd();
        }
		
        return $builder;
	}

    public function getCampaigns($data)
    {
		$subQuery = $this->db->table('z_facebook.fb_ad_insight_history A');
		$subQuery->select('
		D.account_id as customerId, 
		D.campaign_id AS id, 
		D.campaign_name AS name, 
		D.status AS status, 
		D.budget AS budget,
		SUM(A.impressions) AS impressions, 
		SUM(A.inline_link_clicks) AS click, 
		SUM(A.spend) AS spend, 
		SUM(A.sales) as sales, 
		SUM(A.db_count) as unique_total, 
		SUM(A.margin) as margin');
		$subQuery->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
		$subQuery->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
		$subQuery->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('DATE(A.date) >=', $data['sdate']);
			$subQuery->where('DATE(A.date) <=', $data['edate']);
		}
		$subQuery->groupBy('D.campaign_id');

		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
		$builder->select('
		G.id AS company_id, 
		G.name AS company_name,
		E.ad_account_id as customerId,
		"facebook" AS media, 
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
		$builder->join("z_facebook.fb_ad_account AS E", 'sub.customerId = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

		if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.ad_account_id', explode("|",$data['account']));
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
		$subQuery = $this->db->table('z_facebook.fb_ad_insight_history A');
		$subQuery->select('
		C.campaign_id as campaign_id, 
		C.adset_id AS id, 
		C.adset_name AS name, 
		C.status AS status, 
		C.budget AS budget, 
		SUM(A.impressions) AS impressions, 
		SUM(A.inline_link_clicks) AS click, 
		SUM(A.spend) AS spend, 
		SUM(A.sales) as sales, 
		SUM(A.db_count) as unique_total, 
		SUM(A.margin) as margin');
		$subQuery->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
		$subQuery->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('DATE(A.date) >=', $data['sdate']);
			$subQuery->where('DATE(A.date) <=', $data['edate']);
		}
		$subQuery->groupBy('C.adset_id');

		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name,
			E.ad_account_id as customerId,
			D.campaign_id AS campaign_id,
			"facebook" AS media, 
			sub.id AS id, 
			sub.name AS name, 
			sub.status AS status, 
			sub.budget AS budget, 
			"" AS biddingStrategyType,
			0 AS bidamount, 
			0 AS campaign_bidamount,
			"" AS bidamount_type,
			sub.impressions, 
			sub.click, 
			sub.spend, 
			sub.sales, 
			sub.unique_total, 
			sub.margin
		');
        $builder->join('z_facebook.fb_campaign D', 'sub.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.ad_account_id', explode("|",$data['account']));
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
		$subQuery = $this->db->table('z_facebook.fb_ad_insight_history A');
		$subQuery->select('
		B.adset_id as adset_id, 
		B.ad_id AS id, 
		B.ad_name AS name, 
		B.code AS code,
		B.status AS status, 
		C.thumbnail AS thumbnail,
		C.link AS landingUrl,
		SUM(A.impressions) AS impressions, 
		SUM(A.inline_link_clicks) AS click, 
		SUM(A.spend) AS spend, 
		SUM(A.sales) as sales, 
		SUM(A.db_count) as unique_total, 
		SUM(A.margin) as margin');
		$subQuery->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
		$subQuery->join('z_facebook.fb_adcreative C', 'A.ad_id = C.ad_id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('DATE(A.date) >=', $data['sdate']);
			$subQuery->where('DATE(A.date) <=', $data['edate']);
		}
		$subQuery->groupBy('B.ad_id');

		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name,
			E.ad_account_id as customerId,
			D.campaign_id AS campaign_id,
			C.adset_id AS adset_id,
			"facebook" AS media, 
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
        $builder->join('z_facebook.fb_adset C', 'sub.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.ad_account_id', explode("|",$data['account']));
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
		$builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
		A.date, 
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
		(SUM(A.db_price * A.db_count) - SUM(A.spend)) / SUM(A.db_price * A.db_count) * 100 AS per
		');
		$builder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $builder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
		$builder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');

        if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('DATE(A.date) >=', $data['sdate']);
            $builder->where('DATE(A.date) <=', $data['edate']);
        }

		if(!empty($data['facebookCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.campaign_id', $data['facebookCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.adset_id', $data['facebookCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.ad_id', $data['facebookCheck']);
					break;
				default:
					break;
			}
        }

        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.ad_account_id', explode("|",$data['account']));
        }
		
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }
		
        $builder->groupBy('A.date');
        return $builder;
	}

    public function getDisapproval()
	{
        $builder = $this->db->table('z_facebook.fb_ad_account acc');
		$builder->select('acc.ad_account_id');
		$builder->join('z_facebook.fb_campaign ac', 'acc.ad_account_id = ac.account_id', 'left');
		$builder->join('z_facebook.fb_adset ag', 'ac.campaign_id = ag.campaign_id', 'left');
		$builder->join('z_facebook.fb_ad ad', 'ag.adset_id = ag.adset_id', 'left');
		$builder->join('z_facebook.fb_adcreative as', 'ad.ad_id = as.ad_id', 'left');
		$builder->where('ad.effective_status', 'DISAPPROVED');
		$builder->where('acc.status', 1);
		$builder->where('ac.status', 'ACTIVE');
		$builder->where('ag.status', 'ACTIVE');
		$builder->where('ad.status', 'ACTIVE');
		// $builder->orderBy('ad.created_time', 'DESC');
		$builder->groupBy('acc.ad_account_id');
		$result = $builder->get()->getResultArray();

        return $result;
	}

	public function getCampaignById($ids)
    {
		$builder = $this->db->table('z_facebook.fb_campaign');
		$builder->select('"페이스북" as media, "캠페인" as type, campaign_id as id, campaign_name as name, status as status');
		$builder->whereIn('campaign_id', $ids);
		
        return $builder;
    }

	public function getAdgroupById($ids)
    {
		$builder = $this->db->table('z_facebook.fb_adset');
		$builder->select('"페이스북" as media, "광고그룹" as type, adset_id as id, adset_name as name, status as status');
		$builder->whereIn('adset_id', $ids);
		
        return $builder;
    }

	public function getAdById($ids)
    {
		$builder = $this->db->table('z_facebook.fb_ad');
		$builder->select('"페이스북" as media, "광고" as type, ad_id as id, ad_name as name, status as status');
		$builder->whereIn('ad_id', $ids);
		
        return $builder;
    }

	public function updateCode($data) {
		$result = [];
		$this->db->transStart();
        $builder = $this->db->table('z_facebook.fb_ad');  
		$builder->set('code', $data['code']);
		$builder->where('ad_id', $data['id']);
		$builder->update();
		$queryResult = $this->db->transComplete();
		if($queryResult){
			$result['code'] = $data['code'] ?? '';
		}

		return $result;
	}

	public function updateDbCount($data) {
		$this->db->transStart();
        $builder = $this->db->table('z_facebook.fb_ad_account');  
		$builder->set('db_count', (integer)$data['db_count']);
		$builder->where('ad_account_id', $data['id']);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}

	public function setUpdatingByAds($campaignIds){
		$this->db->transStart();
		$builder = $this->db->table('z_facebook.fb_campaign');
		$builder->whereIn('campaign_id', $campaignIds);
		$builder->set('is_updating', 1);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}
}
