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
		
        return $builder;
	}

    public function getCampaigns($data)
    {
        $builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name,
			"facebook" AS media, 
			D.campaign_id AS id, 
			D.campaign_name AS name, 
			D.status AS status, 
			D.budget AS budget, 
			SUM(A.impressions) AS impressions, 
			SUM(A.inline_link_clicks) AS click, 
			SUM(A.spend) AS spend, 
			SUM(A.sales) as sales, 
			SUM(A.db_count) as unique_total, 
			SUM(A.margin) as margin, 
			E.ad_account_id as customerId
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
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('D.campaign_name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('D.campaign_id');
		$builder->orderBy('A.create_date', 'desc');
        $builder->orderBy('D.campaign_name', 'asc');
        return $builder;
    }

	public function getAdsets($data)
	{
		$builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name,
			"facebook" AS media, 
			C.adset_id AS id, 
			C.adset_name AS name, 
			C.status AS status, 
			C.budget AS budget, 
			SUM(A.impressions) AS impressions, 
			SUM(A.inline_link_clicks) AS click, 
			SUM(A.spend) AS spend, 
			SUM(A.sales) as sales, 
			SUM(A.db_count) as unique_total, 
			SUM(A.margin) as margin, 
			E.ad_account_id as customerId
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
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('C.adset_name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('C.adset_id');
		$builder->orderBy('A.create_date', 'desc');
        $builder->orderBy('C.adset_name', 'asc');
        return $builder;
	}

	public function getAds($data)
	{
		$builder = $this->db->table('z_facebook.fb_ad_insight_history A');
        $builder->select('
			G.id AS company_id,
			G.name AS company_name,
			"facebook" AS media, 
			B.ad_id AS id, 
			B.ad_name AS name, 
			B.status AS status, 
			0 AS budget, 
			SUM(A.impressions) AS impressions, 
			SUM(A.inline_link_clicks) AS click, 
			SUM(A.spend) AS spend, 
			SUM(A.sales) as sales, 
			SUM(A.db_count) as unique_total, 
			SUM(A.margin) as margin, 
			E.ad_account_id as customerId
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
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('B.ad_name', $data['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('B.ad_id');
		$builder->orderBy('A.create_date', 'desc');
        $builder->orderBy('B.ad_name', 'asc');
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
        
        if(!empty($data['business'])){
			$builder->whereIn('E.business_id', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

        $builder->groupBy('A.date');
        return $builder;
	}

    /* public function getDisapproval()
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
	} */

	
}
