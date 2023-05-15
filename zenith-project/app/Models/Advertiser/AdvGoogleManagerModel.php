<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvGoogleManagerModel extends Model
{
    protected $google, $ro_google;
    public function __construct()
    {
        $this->google = \Config\Database::connect('google');
        $this->ro_google = \Config\Database::connect('ro_google');
    }

    public function getCampaigns($data)
    {
        $builder = $this->google->table('aw_campaign A');
        $builder->select('"구글" AS media, A.id AS id, A.name AS name, A.status AS status, A.is_updating, B.biddingStrategyType AS biddingStrategyType,
        COUNT(B.id) AS adgroups, COUNT(C.id) AS ads, SUM(D.impressions) AS impressions, SUM(D.clicks) AS click, SUM(D.cost) AS spend, D.ad_id, A.amount AS budget, sum(D.sales) AS sales, A.advertisingChannelType AS advertisingChannelType, A.advertisingChannelSubType AS advertisingChannelSubType, SUM(D.db_count) AS unique_total, SUM(D.margin) AS margin');
        /* (SELECT COUNT(*) AS memos FROM aw_memo F WHERE A.id = F.id AND F.type = 'campaign' AND DATE(F.datetime) >= DATE(NOW())) AS memos */
        $builder->join('aw_adgroup B', 'A.id = B.campaignId');
        $builder->join('aw_ad C', 'B.id = C.adgroupId');
        $builder->join('aw_ad_report_history D', 'C.id = D.ad_id');
        $builder->join('aw_ad_account E', 'A.customerId = E.customerId');
        $builder->where('A.status !=', 'NODATA');
        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }
        
        if(!empty($data['businesses'])){
			$builder->whereIn('E.manageCustomer', $data['businesses']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.customerId', $data['accounts']);
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
		$builder = $this->google->table('aw_campaign A');
		$builder->select('"구글" AS media, B.id AS id, B.name AS name, B.status AS status, B.biddingStrategyType AS biddingStrategyType, B.cpcBidAmount AS cpcBidAmount, B.cpmBidAmount AS cpmBidAmount, B.cpaBidAmount AS cpaBidAmount, A.is_updating AS is_updating, B.adGroupType AS adGroupType, COUNT(C.id) creatives, SUM(D.impressions) impressions,
        SUM(D.clicks) click, SUM(D.cost) spend, SUM(D.db_count) as unique_total, SUM(D.margin) as margin, B.cpcBidAmount, sum(D.sales) as sales, 0 as budget');
		$builder->join('aw_adgroup B', 'A.id = B.campaignId');
		$builder->join('aw_ad C', 'B.id = C.adgroupId');
		$builder->join('aw_ad_report_history D', 'C.id = D.ad_id');

		if(!empty($data['businesses'])){
			$builder->join('aw_ad_account E', 'A.customerId = E.customerId');
			$builder->whereIn('E.manageCustomer', $data['businesses']);
        }

		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('B.name', $data['stx']);
            $builder->groupEnd();
        }

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

        if(!empty($data['accounts'])){
			$builder->whereIn('A.customerId', $data['accounts']);
        }

        $builder->where('B.status !=', 'NODATA');

		$builder->groupBy('B.id');
		$builder->orderBy('D.create_time', 'desc');
		$builder->orderBy('B.name', 'asc');
		$result = $builder->get()->getResultArray();

		return $result;

	}

    public function getAds($data)
	{
		$builder = $this->google->table('aw_campaign A');
		$builder->select('"구글" AS media, A.id AS campaignId, C.id AS id, C.name AS name, C.code, C.status AS status, C.imageUrl, C.finalUrl, C.adType, C.mediaType, A.is_updating AS is_updating, C.imageUrl, C.assets,
        SUM(D.impressions) impressions, SUM(D.clicks) click, SUM(D.cost) spend, 0 AS budget, sum(D.sales) as sales, SUM(D.db_count) as unique_total, SUM(D.margin) as margin');
		$builder->join('aw_adgroup B', 'A.id = B.campaignId');
		$builder->join('aw_ad C', 'B.id = C.adgroupId');
		$builder->join('aw_ad_report_history D', 'C.id = D.ad_id');

		if(!empty($data['businesses'])){
			$builder->join('aw_ad_account F', 'A.customerId = E.customerId');
			$builder->whereIn('E.manageCustomer', $data['businesses']);
        }

		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('C.name', $data['stx']);
            $builder->groupEnd();
        }

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        }

		if(!empty($data['accounts'])){
			$builder->whereIn('A.customerId', $data['accounts']);
        }

        $builder->where('C.status !=', 'NODATA');

		$builder->groupBy('C.id');
		$builder->orderBy('D.create_time', 'desc');
		$builder->orderBy('C.name', 'asc');
		$result = $builder->get()->getResultArray();

		return $result;
	}

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as &$row) {
            /* $stat = $this->getStat($params, $row['id'], $dates[0], $dates[1]);
			$optimization_stat = $this->getOptimization($params, $row['id']);
			$optimization_budget = $this->getOptimization_budget($params, $row['id']);

		 	$row['unique_total'] = $stat['unique_total'];	//유효db
			$row['sales'] = $row['sales'];	//매출액

//			echo $stat['shortterm']."!<br/>";

//			$row['margin'] = Calc::margin($row['sales'], $row['cost'], $stat['margin']);	// 수익
			$row['margin'] = $stat['margin'];	// 수익
			
			if($params=="campaigns"){ // 캠페인단 ai
				$optimization_campaign = "OFF";
				$song_optimization_campaign = "OFF";
				$choi_optimization_campaign = "OFF";
				for($i=0;$i<sizeof($optimization_stat);$i++){
					if($optimization_stat[$i]['type'] =="901"){
						$optimization_campaign = "ON";
					}
					if($optimization_stat[$i]['type'] =="801" || $optimization_stat[$i]['type'] =="802" || $optimization_stat[$i]['type'] =="803"){
						$song_optimization_campaign = "Lv".substr($optimization_stat[$i]['type'],-1);
					}
					if($optimization_stat[$i]['type'] =="701"){
						$choi_optimization_campaign = "ON";
					}
				}
				$row['optimization_campaign'] = $optimization_campaign;
				$row['song_optimization_campaign'] = $song_optimization_campaign;
				$row['choi_optimization_campaign'] = $choi_optimization_campaign;
			}
			else if($params=="ads"){ // 광고 ai
				if($optimization_stat =="701"){
					$row['optimization_ad'] = "ON";
				}
				else {
					$row['optimization_ad'] = "OFF";
				}
			}
			else {

				if($optimization_stat =="1"){
					$row['optimization'] = "ON";	//어른정파고
					$row['optimization_ch'] = "OFF";	// 어린이정파고
				}
				else if($optimization_stat =="2"){
					$row['optimization'] = "OFF";	//어른정파고
					$row['optimization_ch'] = "ON";	// 어린이정파고
				}
				else {
					$row['optimization'] = "OFF";
					$row['optimization_ch'] = "OFF";
				}
			}
			if($choi_optimization_campaign=="ON") $row['optimization_campaign_budget'] = $optimization_budget;	// ai 예산 */

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률


			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율

			switch (!empty($row['biddingStrategyType'])) {
				case 'TARGET_CPA' :
					$row['biddingStrategyType'] = '타겟 CPA';
					break;
				case 'TARGET_ROAS' :
					$row['biddingStrategyType'] = '타겟 광고 투자수익(ROAS)';
					break;
				case 'TARGET_SPEND' :
					$row['biddingStrategyType'] = '클릭수 최대화';
					break;
				case 'MAXIMIZE_CONVERSIONS' :
					$row['biddingStrategyType'] = '전환수 최대화';
					break;
                /* //값이 뭔지 모름ㅠㅠ
				case '' :
					$row['biddingStrategyType'] = '검색 결과 위치 타겟';
					break;
				case '' :
					$row['biddingStrategyType'] = '경쟁 광고보다 내 광고가 높은 순위에 게재되는 비율 타겟';
					break;
				case '' :
					$row['biddingStrategyType'] = '타겟 노출 점유율';
					break;
				*/
                case 'PAGE_ONE_PROMOTED' :
                    $row['biddingStrategyType'] = '향상된 CPC 입찰기능';
                    break;
                case 'MANUAL_CPM' :
                    $row['biddingStrategyType'] = '수동 입찰 전략';
                    break;
                case 'MANUAL_CPC' :
                    $row['biddingStrategyType'] = '수동 CPC';
                    break;
                case 'UNKNOWN' :
                    $row['biddingStrategyType'] = '알수없음';
                    break;
                default :
                    break;
			}
        }
        return $result;
    }
    
    public function getManageAccounts() {
        $builder = $this->google->table('aw_ad_account');  
		$builder->select('*');
        $builder->where('canManageClients', 1);
        $builder->where('status', 'ENABLED');
        //$builder->where('is_exposed', 1);
        $builder->orderBy('name', 'asc');
        $builder->orderBy('create_time', 'asc');

        $result = $builder->get()->getResultArray();

		return $result;
	}

    public function getAccounts($data)
	{
        $builder = $this->google->table('aw_ad_account F');
		$builder->select('F.customerId AS id, F.name, F.is_exposed, F.db_count, SUM(F.db_count) AS db_sum, COUNT(DISTINCT D.date) AS date_count');
        $builder->join('aw_campaign A', 'F.customerId = A.customerId', 'left');
        $builder->join('aw_adgroup B', 'A.id = B.campaignId', 'left');
        $builder->join('aw_ad C', 'B.id = C.adgroupId', 'left');
		$builder->join('aw_ad_report_history D', 'C.id = D.ad_id', 'left');

		if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(D.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(D.date) <=', $data['dates']['edate']);
        } 

        $builder->where('F.name !=', '');

        if(!empty($data['businesses'])){
			$builder->whereIn('F.manageCustomer', $data['businesses']);
        }

        $builder->groupBy('F.customerId');
        $builder->orderBy('F.name', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
	}

    public function getDisapproval()
	{
        $builder = $this->google->table('aw_ad_account acc');
		$builder->select('acc.customerId, acc.name AS customer_name,
        ac.id AS campaign_id, ac.name AS campaign_name,
        ag.id AS adgroup_id, ag.name AS adgroup_name,
        ad.id, ad.name, ad.code, ass.url, ad.status, ad.reviewStatus, ad.approvalStatus, ad.adType, ad.finalUrl, ad.create_time, ad.update_time');
		$builder->join('aw_campaign ac', 'ac.customerId = acc.customerId', 'left');
		$builder->join('aw_adgroup ag', 'ag.campaignId = ac.id', 'left');
		$builder->join('aw_ad ad', 'ad.adgroupId = ag.id', 'left');
		$builder->join('aw_asset ass', "SUBSTRING_INDEX(ad.assets, ',', 1) = ass.id", 'left');
        $builder->where("(ad.approvalStatus = 'DISAPPROVED' OR ad.approvalStatus = 'AREA_OF_INTEREST_ONLY')");
		$builder->where('acc.is_exposed', 1);
        $builder->where('acc.status', 'ENABLED');
        $builder->where('ac.status', 'ENABLED');
        $builder->where('ag.status', 'ENABLED');
        $builder->where('ad.status', 'ENABLED');
		$builder->orderBy('ad.create_time', 'DESC');
		$result = $builder->get()->getResultArray();

        return $result;
	}

    public function getReport($data)
	{
		$builder = $this->google->table('aw_ad_report_history A');
        $builder->select('A.date, 
                SUM(A.impressions) AS impressions,
                SUM(A.clicks) AS click,
                (SUM(A.clicks) / SUM(A.impressions)) * 100 AS click_ratio,
                (SUM(A.db_count) / SUM(A.clicks)) * 100 AS conversion_ratio,
                SUM(A.cost) AS spend,
                SUM(A.db_count) AS unique_total,
                IFNULL(SUM(A.cost) / SUM(A.db_count), 0) AS unique_one_price,
                SUM(A.db_price) AS unit_price,
                SUM(A.sales) AS price,
                SUM(A.margin) AS profit,
                (SUM(A.db_price * A.db_count) - SUM(A.cost)) / SUM(A.db_price * A.db_count) * 100 AS per ');
        $builder->join('aw_ad B', 'A.ad_id = B.id', 'left');
        $builder->join('aw_adgroup C', 'B.adgroupId = C.id', 'left');
        $builder->join('aw_campaign D', 'C.campaignId = D.id', 'left');
        $builder->join('aw_ad_account E', 'D.customerId = E.customerId', 'left');

        if(!empty($data['dates']['sdate']) && !empty($data['dates']['edate'])){
            $builder->where('DATE(A.date) >=', $data['dates']['sdate']);
            $builder->where('DATE(A.date) <=', $data['dates']['edate']);
        } 

		if(!empty($data['businesses'])){
			$builder->whereIn('E.manageCustomer', $data['businesses']);
        }

		if(!empty($data['accounts'])){
			$builder->whereIn('D.customerId', $data['accounts']);
        }

		$builder->groupBy('A.date');
		$builder->orderBy('A.date', 'ASC');
		$result = $builder->get()->getResultArray();

        return $result;
	}
}
