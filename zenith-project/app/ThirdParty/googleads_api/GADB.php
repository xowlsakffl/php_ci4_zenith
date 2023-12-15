<?php
namespace App\ThirdParty\googleads_api;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\RawSql;

class GADB
{
	private $db, $db2, $zenith;
	private $sltDB;
	
    public function __construct()
    {
        $this->db = \Config\Database::connect('google');
        $this->db2 = \Config\Database::connect('ro_google');
        $this->zenith = \Config\Database::connect();
    }

	public function __call(string $method, array $data)
	{
		if (is_array($data)) {
			array_walk_recursive($data, function (&$v) {
				if (is_string($v))
					$v = $this->db->escape($v);
			});
		}
		if (!method_exists($this, $method))
			trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);

		return call_user_func_array([$this, $method], $data);
	}

	public function updateAccount($data, $is_hidden = 0)
	{ //1 숨김, 0 활성화
		$is_update = 0;
		if ($is_hidden == '0') {
			$is_update = 1; //1 업데이트, 0 제외
		}

		if(!isset($data['customerId'])) return;
		$sql = "INSERT INTO aw_ad_account (
			customerId, 
			manageCustomer, 
			name, 
			status, 
			canManageClients, 
			currencyCode, 
			dateTimeZone, 
			testAccount, 
			is_hidden, 
			create_time)
		VALUES (
			:customerId:, 
			:manageCustomer:, 
			:name:, 
			:status:, 
			:canManageClients:, 
			:currencyCode:, 
			:dateTimeZone:, 
			:testAccount:, 
			:is_hidden:, 
			NOW()
		) ON DUPLICATE KEY
			UPDATE customerId = :customerId:,
			manageCustomer = :manageCustomer:, 
			is_update = :is_update:,
			name = :name:,
			status = :status:, 
			canManageClients = :canManageClients:, 
			currencyCode = :currencyCode:, 
			dateTimeZone = :dateTimeZone:, 
			testAccount = :testAccount:, 
			is_hidden = :is_hidden:, 
			update_time = NOW();";

		$params = [
			'customerId' => $data['customerId'],
			'manageCustomer' => $data['manageCustomer'],
			'name' => $data['name'],
			'status' => $data['status'],
			'canManageClients' => (integer)$data['canManageClients'],
			'currencyCode' => $data['currencyCode'],
			'dateTimeZone' => $data['dateTimeZone'],
			'testAccount' => (integer)$data['testAccount'],
			'is_hidden' => (integer)$data['is_hidden'],
			'is_update' => (integer)$is_update
		];
		$result = $this->db->query($sql, $params);
		return $result;
	}

	public function modifyAccountBudget($data)
	{
		$sql = "UPDATE aw_ad_account SET amountSpendingLimit = :amountSpendingLimit:, amountServed = :amountServed:  WHERE customerId = :customerId:";

		$params = [
			'amountSpendingLimit' => $data['amountSpendingLimit'],
			'amountServed' => $data['amountServed'],
			'customerId' => $data['customerId']
		];

		$result = $this->db->query($sql, $params);
		return $result;
	}

	public function getAccounts($is_hidden = 0, $order = false)
	{
		$sql = "SELECT * FROM aw_ad_account WHERE 1";

		if (!is_null($is_hidden))
			$sql .= " AND is_hidden = {$is_hidden}";
		if ($order) $sql .= " " . $order;
		$result = $this->db_query($sql);

		return $result;
	}

	public function getCampaign($id)
	{
		if (is_null($id)) return false;

		$sql = "SELECT * FROM aw_campaign WHERE id = {$id}";
		$result = $this->db_query($sql, true);

		return $result;
	}

	public function updateCampaign($data = null)
	{
		if (is_null($data)) return false;
		//print_r($data);exit;
		$sql = "INSERT INTO aw_campaign(customerId, id, name, status, servingStatus, startDate, endDate, budgetId, budgetName, budgetReferenceCount, budgetStatus, amount, deliveryMethod, advertisingChannelType, advertisingChannelSubType, AdServingOptimizationStatus, create_time)
				VALUES(
					:customerId:,
					:id:,
					:name:, 
					:status:,
					:servingStatus:,
					:startDate:,
					:endDate:,
					:budgetId:,
					:budgetName:,
					:budgetReferenceCount:,
					:budgetStatus:,
					:budgetAmount:,
					:budgetDeliveryMethod:,
					:advertisingChannelType:,
					:advertisingChannelSubType:,
					:adServingOptimizationStatus:, NOW())
				ON DUPLICATE KEY UPDATE
					name = :name:,
					status = :status:,
					servingStatus = :servingStatus:,
					startDate = :startDate:,
					endDate = :endDate:,
					budgetId = :budgetId:,
					budgetName = :budgetName:,
					budgetReferenceCount = :budgetReferenceCount:,
					budgetStatus = :budgetStatus:,
					amount = :budgetAmount:,
					deliveryMethod = :budgetDeliveryMethod:,
					advertisingChannelType = :advertisingChannelType:,
					advertisingChannelSubType = :advertisingChannelSubType:,
					AdServingOptimizationStatus = :adServingOptimizationStatus:,
					is_updating = 0,
					update_time = NOW()";

		$params = [
			'customerId' => $data['customerId'],
			'id' => $data['id'],
			'name' => $data['name'],
			'status' => $data['status'],
			'servingStatus' => $data['servingStatus'],
			'startDate' => $data['startDate'],
			'endDate' => $data['endDate'],
			'budgetId' => (integer)$data['budgetId'],
			'budgetName' => $data['budgetName'],
			'budgetReferenceCount' => (integer)$data['budgetReferenceCount'],
			'budgetStatus' => $data['budgetStatus'],
			'budgetAmount' => (integer)$data['budgetAmount'],
			'budgetDeliveryMethod' => $data['budgetDeliveryMethod'],
			'advertisingChannelType' => $data['advertisingChannelType'],
			'advertisingChannelSubType' => $data['advertisingChannelSubType'],
			'adServingOptimizationStatus' => $data['adServingOptimizationStatus']
		];

		$result = $this->db->query($sql, $params);

		return $result;
	}

	public function updateCampaignField($data = null)
	{
		if (is_null($data)) return false;

		$builder = $this->db->table('aw_campaign');

		$updateData = [];

		if (isset($data['name'])) {
			$updateData['name'] = $data['name'];
		}
		if (isset($data['status'])) {
			$updateData['status'] = $data['status'];
		}
		if (isset($data['amount'])) {
			$updateData['amount'] = $data['amount'];
		}

		$builder->set($updateData);
		$builder->where('id', $data['id']);
		$result = $builder->update();

		return $result;
	}

	public function getAdGroupIdByAd($id)
	{
		if (is_null($id)) return false;

		$sql = "SELECT adgroupId FROM aw_ad WHERE id = {$id}";
		$result = $this->db_query($sql, true);

		return $result;
	}
	public function updateAdGroup($data = null)
	{
		if (is_null($data)) return false;

		$sql = "INSERT INTO aw_adgroup(campaignId, id, name, status, adGroupType, biddingStrategyType, cpcBidAmount, cpcBidSource, cpmBidAmount, cpmBidSource, cpaBidAmount, cpaBidSource, create_time)
				VALUES(
					:campaignId:, 
					:id:, 
					:name:, 
					:status:, 
					:adGroupType:, 
					:biddingStrategyType:, 
					:cpcBidAmount:, 
					:cpcBidSource:, 
					:cpmBidAmount:, 
					:cpmBidSource:, 
					0, 
					'', NOW())
				ON DUPLICATE KEY UPDATE
					name = :name:,
					status = :status:,
					adGroupType = :adGroupType:,
					biddingStrategyType = :biddingStrategyType:,
					cpcBidAmount = :cpcBidAmount:,
					cpcBidSource = :cpcBidSource:,
					cpmBidAmount = :cpmBidAmount:,
					cpmBidSource = :cpmBidSource:,
					cpaBidAmount = 0,
					cpaBidSource = '',
					update_time = NOW()";
		$params = [
			'campaignId' => $data['campaignId'],
			'id' => $data['id'],
			'name' => $data['name'],
			'status' => $data['status'],
			'adGroupType' => $data['adGroupType'],
			'biddingStrategyType' => $data['biddingStrategyType'],
			'cpcBidAmount' => (integer)$data['cpcBidAmount'] ?? 0,
			'cpcBidSource' => $data['cpcBidSource'],
			'cpmBidAmount' => (integer)$data['cpmBidAmount'] ?? 0,
			'cpmBidSource' => $data['cpmBidSource']
		];
		
		$result = $this->db->query($sql, $params);

		return $result;
	}

	public function updateAdgroupField($data = null)
	{
		if (is_null($data)) return false;

		$builder = $this->db->table('aw_adgroup');

		$updateData = [];

		if (isset($data['name'])) {
			$updateData['name'] = $data['name'];
		}
		if (isset($data['status'])) {
			$updateData['status'] = $data['status'];
		}

		$builder->set($updateData);
		$builder->where('id', $data['id']);
		$result = $builder->update();

		return $result;
	}

	public function updateAd($data = null)
	{
		if (is_null($data)) return false;
		$this->db->transStart();
		$sql = "INSERT INTO aw_ad(adgroupId, id, name, status, reviewStatus, approvalStatus, policyTopic, code, adType, mediaType, assets, imageUrl, finalUrl, create_time)
				VALUES(
					:adgroupId:, 
					:id:, 
					:name:, 
					:status:, 
					:reviewStatus:, 
					:approvalStatus:, 
					:policyTopic:, 
					:code:, 
					:adType:, 
					:mediaType:, 
					:assets:, 
					:imageUrl:, 
					:finalUrl:, 
					NOW()
				)
				ON DUPLICATE KEY UPDATE
					name = :name:,
					status = :status:,
					reviewStatus = :reviewStatus:,
					approvalStatus = :approvalStatus:,
					policyTopic = :policyTopic:,
					adType = :adType:,
					mediaType = :mediaType:,
					assets = :assets:,
					imageUrl = :imageUrl:,
					finalUrl = :finalUrl:,
					update_time = NOW()";
		$params = [
			'adgroupId' => $data['adgroupId'],
			'id' => $data['id'],
			'name' => $data['name'],
			'status' => $data['status'],
			'reviewStatus' => $data['reviewStatus'],
			'approvalStatus' => $data['approvalStatus'],
			'policyTopic' => $data['policyTopic'],
			'code' => $data['code'],
			'adType' => $data['adType'],
			'mediaType' => $data['mediaType'],
			'assets' => $data['assets'],
			'imageUrl' => $data['imageUrl'],
			'finalUrl' => $data['finalUrl']
		];
		if ($result = $this->db->query($sql, $params)) {
			if ($data['impressions']) {
				$sql = "INSERT INTO aw_ad_report_history(ad_id, date, hour, impressions, clicks, cost, create_time)
                VALUES(
                    :id:, 
                    :date:, 
                    0,
                    :impressions:, 
                    :clicks:, 
                    :cost:,
                     NOW()
                )
                ON DUPLICATE KEY UPDATE
                    impressions = :impressions:,
                    clicks = :clicks:,
                    cost = :cost:,
                    update_time = NOW()";
				$params = [
					'id' => $data['id'],
					'date' => $data['date'],
					'impressions' => (integer)$data['impressions'],
					'clicks' => (integer)$data['clicks'],
					'cost' => (integer)$data['cost']
				];
				$this->db->query($sql, $params);
			}
		}
		$this->db->transComplete();
		return $result;
	}

	public function updateAdField($data = null)
	{
		if (is_null($data)) return false;

		$builder = $this->db->table('aw_ad');

		$updateData = [];

		if (isset($data['name'])) {
			$updateData['name'] = $data['name'];
		}
		if (isset($data['status'])) {
			$updateData['status'] = $data['status'];
		}

		$builder->set($updateData);
		$builder->where('id', $data['id']);
		$result = $builder->update();

		return $result;
	}

	public function updateAsset($data)
	{
		if (is_null($data)) return false;

		if(empty($data['video_id'])){
			$data['video_id'] = '';
		}

		if(empty($data['url'])){
			$data['url'] = '';
		}

		$data = [
			'id' => $this->db->escape($data['id']),
			'name' => $this->db->escape($data['name'] ?? ''),
			'type' => $this->db->escape($data['type'] ?? ''),
			'video_id' => $this->db->escape($data['video_id'] ?? ''),
			'url' => $this->db->escape($data['url'] ?? ''),
		];	
		
		$builder = $this->db->table('aw_asset');
        $builder->setData($data, false);
		$updateTime = ['update_time' => new RawSql('NOW()')];
        $builder->updateFields($updateTime, true);
		$result = $builder->upsert();
		return $result;
	}

	public function getDbCount($ad_id, $date)
	{
		if (empty($ad_id) || empty($date)) return NULL;
		$sql = "SELECT * FROM aw_db_count WHERE ad_id = '{$ad_id}' AND date = '{$date}'";
		$result = $this->db_query($sql);
		if (!$result) return null;
		return $result->getResultArray();
	}

	public function getAppSubscribeCount($data, $date)
	{
		if (empty($data['db_prefix'])) return 0;
		$sql = "SELECT * FROM app_subscribe WHERE group_id = '{$data['app_id']}' AND status = 1 AND site = '{$data['site']}' AND DATE_FORMAT(reg_date, '%Y-%m-%d') = '{$date}' AND deleted = 0";
		$res = $this->zenith->query($sql);
		$num_rows = $res->getNumRows();
		return $num_rows;
	}

	public function getAdLeads($date)
	{
		$sql = "SELECT his.ad_id, CONCAT('{',GROUP_CONCAT('\"',his.`hour`,'\":',his.cost),'}') AS spend_data, ad.code, ad.finalUrl, ad.name AS ad_name, ac.name AS campaign_name
				FROM
					`aw_ad_report_history` AS his
					LEFT JOIN aw_ad AS ad ON his.ad_id = ad.id
					LEFT JOIN aw_adgroup AS ag ON ad.adgroupId = ag.id
					LEFT JOIN aw_campaign AS ac ON ag.campaignId = ac.id
            	WHERE his.date = '{$date}' GROUP BY his.ad_id";
		$result = $this->db_query($sql);

		return $result;
	}

	public function getDbPrice($data)
    {
        if (empty($data['ad_id']) || empty($data['date'])){
			return NULL;
		}

        $sql = "SELECT ad_id, date, db_price FROM `z_adwords`.`aw_ad_report_history` WHERE `ad_id` = '{$data['ad_id']}' AND `date` = '{$data['date']}' GROUP BY date ORDER BY hour DESC LIMIT 1;";
        $result = $this->db_query($sql);
		$result = $result->getResultArray();

        if (empty($result)){return null;}
        return $result;
    }

    public function getLeads($data)
    {
        if (empty($data['event_seq'])) return null;
        $sql = "SELECT event_seq, site, date(from_unixtime(reg_timestamp)) AS date, HOUR(from_unixtime(reg_timestamp)) AS hour, count(event_seq) AS db_count
                FROM `zenith`.`event_leads`
                WHERE `reg_timestamp` >= unix_timestamp('{$data['date']}')
                AND `status` = 1 AND `is_deleted` = 0
                AND `event_seq` = {$data['event_seq']} AND `site` = '{$data['site']}' AND DATE_FORMAT(`reg_date`, '%Y-%m-%d') = '{$data['date']}'
                GROUP BY `event_seq`, `site`, HOUR(from_unixtime(reg_timestamp))";
        $result = $this->zenith->query($sql);
        return $result;
    }

	public function updateReport($data)
    {
		if(!empty($data)){
			$row = $data;
		}else{
			return false;
		}
        
		if(isset($row['data'])){
			foreach($row['data'] as $v) {
				if ($row['ad_id']) {
					$data = [
						'ad_id' => $row['ad_id'],
						'date' => $row['date'],
						'hour' => $v['hour'],
						'media' => $row['media'],
						'period' => is_numeric($row['period_ad']) ? $row['period_ad'] : 0,
						'event_seq' => $row['event_seq'],
						'site' => $row['site'],
						'db_price' => $row['db_price'],
						'db_count' => $v['count'],
						'margin' => $v['margin'],
						'sales' => $v['sales'],
					];
					$builder = $this->db->table('aw_ad_report_history');
					$builder->setData($data);
					$updateTime = ['update_time' => new RawSql('NOW()')];
					$builder->updateFields($updateTime, true);
					// d($builder->getCompiledUpsert());
					$builder->upsert();
					/*
					$sql = "UPDATE `z_adwords`.`aw_ad_report_history` 
					SET `media` = '{$row['media']}', `period` = '{$row['period_ad']}', `event_seq` = '{$row['event_seq']}', `site` = '{$row['site']}', `db_price` = '{$row['db_price']}', `db_count` = '{$v['count']}', `margin` = '{$v['margin']}', `sales` = '{$v['sales']}', `update_time` = NOW()
					WHERE `ad_id` = '{$row['ad_id']}' AND `date` = '{$row['date']}' AND `hour` = '{$v['hour']}'";
					$this->db_query($sql, true);
					*/
				}
			}
		}
    }

	public function db_query($sql, $error = false)
	{
		if (!$sql) return false;
		$result = null;
		if (preg_match('#^select.*#i', trim($sql)))
			$this->sltDB = $this->db2;
		else
			$this->sltDB = $this->db;
		$this->sltDB->query("BEGIN");
		if ($error)
			$result = $this->sltDB->query($sql) or die("ERROR :" . $this->sltDB->error . ' :' . $sql);
		else
			$result = $this->sltDB->query($sql);
		if ($result) {
			//$this->tracking($sql);
		}
		$this->sltDB->query("COMMIT");
		return $result;
	}
}
