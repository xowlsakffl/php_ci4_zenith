<?php

use CodeIgniter\CLI\CLI;

class GADB
{
	private $db, $db2, $zenith;
	private $sltDB;
	
    public function __construct()
    {
        $this->db = \Config\Database::connect('google');
        $this->db2 = \Config\Database::connect('google2');
        //$this->zenith = \Config\Database::connect();
        //      $this->db_query("SET FOREIGN_KEY_CHECKS = 0;");
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

	private function updateAccount($data, $is_hidden = 0)
	{ //1 숨김, 0 활성화
		$is_update = 0;
		if ($is_hidden == '0') {
			$is_update = 1; //1 업데이트, 0 제외
		}
		$data['Name'] = $this->db->escape($data['Name']);
		$sql = "INSERT INTO aw_ad_account (customerId, manageCustomer, name, status, canManageClients, currencyCode, dateTimeZone, testAccount, is_hidden, create_time)
                VALUES ({$data['customerId']}, {$data['manageCustomer']}, {$data['name']}, {$data['status']}, {$data['canManageClients']}, {$data['currencyCode']}, {$data['dateTimeZone']}, {$data['testAccount']}, {$data['is_hidden']}, NOW()) ON DUPLICATE KEY
                UPDATE customerId = {$data['customerId']}, manageCustomer = {$data['manageCustomer']}, is_update = {$is_update}, name = {$data['name']}, status = {$data['status']},  canManageClients = {$data['canManageClients']}, currencyCode = {$data['currencyCode']}, dateTimeZone = {$data['dateTimeZone']}, testAccount = {$data['testAccount']}, is_hidden = {$data['is_hidden']}, update_time=NOW();";
		//echo $sql .'<br>';
		$result = $this->db_query($sql, true);
		return $result;
	}

	private function modifyAccountBudget($data)
	{
		$sql = "UPDATE aw_ad_account SET amountSpendingLimit = {$data['amountSpendingLimit']}, amountServed = {$data['amountServed']}  WHERE customerId = {$data['customerId']}";
		$result = $this->db_query($sql, true);
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

	private function updateCampaign($data = null)
	{
		if (is_null($data)) return false;
		//print_r($data);exit;
		$sql = "INSERT INTO aw_campaign(customerId, id, name, status, servingStatus, startDate, endDate, budgetId, budgetName, budgetReferenceCount, budgetStatus, amount, deliveryMethod, advertisingChannelType, advertisingChannelSubType, AdServingOptimizationStatus, create_time)
				VALUES(
					{$data['customerId']},
					{$data['id']},
					{$data['name']}, 
					{$data['status']},
					{$data['servingStatus']},
					{$data['startDate']},
					{$data['endDate']},
					{$data['budgetId']},
					{$data['budgetName']},
					{$data['budgetReferenceCount']},
					{$data['budgetStatus']},
					{$data['budgetAmount']},
					{$data['budgetDeliveryMethod']},
					{$data['advertisingChannelType']},
					{$data['advertisingChannelSubType']},
					{$data['adServingOptimizationStatus']}, NOW())
				ON DUPLICATE KEY UPDATE
					name = {$data['name']},
					status = {$data['status']},
					servingStatus = {$data['servingStatus']},
					startDate = {$data['startDate']},
					endDate = {$data['endDate']},
					budgetId = {$data['budgetId']},
					budgetName = {$data['budgetName']},
					budgetReferenceCount = {$data['budgetReferenceCount']},
					budgetStatus = {$data['budgetStatus']},
					amount = {$data['budgetAmount']},
					deliveryMethod = {$data['budgetDeliveryMethod']},
					advertisingChannelType = {$data['advertisingChannelType']},
					advertisingChannelSubType = {$data['advertisingChannelSubType']},
					AdServingOptimizationStatus = {$data['adServingOptimizationStatus']},
					is_updating = 0,
					update_time = NOW()";
		//echo $sql .'<br>'; exit;
		$result = $this->db_query($sql, true);

		return $result;
	}

	private function updateAdGroup($data = null)
	{
		if (is_null($data)) return false;

		$sql = "INSERT INTO aw_adgroup(campaignId, id, name, status, adGroupType, biddingStrategyType, cpcBidAmount, cpcBidSource, cpmBidAmount, cpmBidSource, cpaBidAmount, cpaBidSource, create_time)
				VALUES(
					{$data['campaignId']}, 
					{$data['id']}, 
					{$data['name']}, 
					{$data['status']}, 
					{$data['adGroupType']}, 
					{$data['biddingStrategyType']}, 
					{$data['cpcBidAmount']}, 
					{$data['cpcBidSource']}, 
					{$data['cpmBidAmount']}, 
					{$data['cpmBidSource']}, 
					{$data['cpaBidAmount']}, 
					{$data['cpaBidSource']}, NOW())
				ON DUPLICATE KEY UPDATE
					name = {$data['name']},
					status = {$data['status']},
					adGroupType = {$data['adGroupType']},
					biddingStrategyType = {$data['biddingStrategyType']},
					cpcBidAmount = {$data['cpcBidAmount']},
					cpcBidSource = {$data['cpcBidSource']},
					cpmBidAmount = {$data['cpmBidAmount']},
					cpmBidSource = {$data['cpmBidSource']},
					cpaBidAmount = {$data['cpaBidAmount']},
					cpaBidSource = {$data['cpaBidSource']},
					update_time = NOW()";
		//echo $sql .'<br>'; exit;
		$result = $this->db_query($sql, true);

		return $result;
	}

	private function updateAd($data = null)
	{
		if (is_null($data)) return false;

		$sql = "INSERT INTO aw_ad(adgroupId, id, name, status, reviewStatus, approvalStatus, code, adType, mediaType, assets, imageUrl, finalUrl, create_time)
				VALUES(
					{$data['adgroupId']}, 
					{$data['id']}, 
					{$data['name']}, 
					{$data['status']}, 
					{$data['reviewStatus']}, 
					{$data['approvalStatus']}, 
					{$data['code']}, 
					{$data['adType']}, 
					{$data['mediaType']}, 
					{$data['assets']}, 
					{$data['imageUrl']}, 
					{$data['finalUrl']}, 
					NOW()
				)
				ON DUPLICATE KEY UPDATE
					name = {$data['name']},
					status = {$data['status']},
					reviewStatus = {$data['reviewStatus']},
					approvalStatus = {$data['approvalStatus']},
					adType = {$data['adType']},
					mediaType = {$data['mediaType']},
					assets = {$data['assets']},
					imageUrl = {$data['imageUrl']},
					finalUrl = {$data['finalUrl']},
					update_time = NOW()";
		//echo $sql .'<br>'; exit;
		if ($result = $this->db_query($sql, true)) {
			if ($data['impressions']) {
				$sql = "INSERT INTO aw_ad_report(ad_id, impressions, clicks, cost, create_time)
							VALUES(
								{$data['id']}, 
								{$data['impressions']}, 
								{$data['clicks']}, 
								{$data['cost']}, 
								NOW()
							)
							ON DUPLICATE KEY UPDATE
								impressions = {$data['impressions']},
								clicks = {$data['clicks']},
								cost = {$data['cost']},
								update_time = NOW()";
				$this->db_query($sql, true);
				$sql = "INSERT INTO aw_ad_report_history(ad_id, date, impressions, clicks, cost, create_time)
							VALUES(
								{$data['id']}, 
								{$data['date']}, 
								{$data['impressions']}, 
								{$data['clicks']}, 
								{$data['cost']},
								 NOW()
							)
							ON DUPLICATE KEY UPDATE
								impressions = {$data['impressions']},
								clicks = {$data['clicks']},
								cost = {$data['cost']},
								update_time = NOW()";
				$this->db_query($sql, true);
			}
		}

		return $result;
	}

	private function updateAsset($data)
	{
		if (is_null($data)) return false;

		$sql = "INSERT INTO aw_asset(
					id, name, type, video_id, url, create_time
				)
				VALUES(
					{$data['id']}, 
					{$data['name']}, 
					{$data['type']}, 
					{$data['video_id']}, 
					{$data['url']}, 
					NOW()
				)
				ON DUPLICATE KEY UPDATE 
					name = {$data['name']},
					type = {$data['type']},
					video_id = {$data['video_id']},
					url = {$data['url']},
					update_time = NOW()";
		//echo $sql .'<br>'; exit;
		$result = $this->db_query($sql, true);

		return $result;
	}

	public function getDbCount($ad_id, $date)
	{
		if (!$ad_id || !$date) return NULL;
		$sql = "SELECT * FROM aw_db_count WHERE ad_id = '{$ad_id}' AND date = '{$date}'";
		$result = $this->db_query($sql);
		if (!$result) return null;
		return $result->getResultArray();
	}

	public function getAppSubscribeCount($data, $date)
	{
		if (!$data['db_prefix']) return 0;
		$sql = "SELECT * FROM app_subscribe WHERE group_id = '{$data['app_id']}' AND status = 1 AND site = '{$data['site']}' AND DATE_FORMAT(reg_date, '%Y-%m-%d') = '{$date}' AND deleted = 0";
		$res = $this->zenith->query($sql);
		$num_rows = $res->num_rows;
		return $num_rows;
	}

	public function getAdLeads($date)
	{
		$sql = "SELECT his.ad_id, his.cost, ad.code, ad.finalUrl, ac.name AS campaign_name
				FROM
					`aw_ad_report_history` AS his
					LEFT JOIN aw_ad AS ad ON his.ad_id = ad.id
					LEFT JOIN aw_adgroup AS ag ON ad.adgroupId = ag.id
					LEFT JOIN aw_campaign AS ac ON ag.campaignId = ac.id
            	WHERE his.date = '{$date}' GROUP BY his.ad_id";
		$result = $this->db_query($sql);

		return $result;
	}

	public function insertDbCount($data, $date)
	{
		foreach ($data as $key => $row) {
			if ($row['ad_id']) {
				$sql = "INSERT INTO aw_db_count (ad_id, event_id, site, media, db_price, db_count, margin, date, create_time)
                        VALUES ('{$row['ad_id']}', '{$row['app_id']}', '{$row['site']}', '{$row['media']}', '{$row['db_price']}', '{$row['count']}', '{$row['margin']}', '{$date}', NOW())
                        ON DUPLICATE KEY
                        UPDATE ad_id = '{$row['ad_id']}', event_id = '{$row['app_id']}', site = '{$row['site']}', media = '{$row['media']}', db_price = '{$row['db_price']}', db_count = '{$row['count']}', margin = '{$row['margin']}', date = '{$date}', update_time = NOW();";
				//echo $sql.'<br>';
				$result = $this->db_query($sql, true);
			}
		}
	}

	public function updateReport($data)
	{
		if ($data->ad_id && $data->date) {
			foreach ($data->data as $field => $value) $query[] = "{$field} = '{$value}'";
			$sql = "UPDATE aw_ad_report_history SET " . implode(',', $query) . " WHERE ad_id = {$data->ad_id} AND date = '{$data->date}'";
			$this->db_query($sql);
		}
		return false;
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
