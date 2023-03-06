<?php
class AWDB
{
    //private $host = "viberc-1.cluster-cxrnaofyo5ly.ap-northeast-2.rds.amazonaws.com";
    private $host = "db.chainsaw.co.kr";
    private $host2 = "db2.chainsaw.co.kr";
    private $user = "adwords";
    private $password = "qkdlqmdkfTl#aw";
    private $dbname = "adwords";
    private $db;
    private $g5db;
    private $sltDB;

    public function __construct()
    {
        $this->db = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
        $this->db2 = mysqli_connect($this->host2, $this->user, $this->password, $this->dbname);
        $this->db->query("set session character_set_client=utf8mb;");
        $this->db->query("set session character_set_connection=utf8mb;");
        $this->g5db = mysqli_connect($this->host, 'chainsaw_old', 'cpdls#db', 'chainsaw_old');
//      $this->db_query("SET FOREIGN_KEY_CHECKS = 0;");
    }

    public function getCampaignsWithCustomer($query = "") {
        $sql = "SELECT A.*, B.customerId, B.is_update FROM aw_campaign AS A, aw_ad_account AS B
                    WHERE A.customerId = B.customerId AND B.is_hidden = 0 AND B.is_update = 1";
        if($query) 
            $sql .= " ".$query;
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdGroupsWithCustomer() {
        $sql = "SELECT A.*, C.customerId, C.is_update FROM aw_adgroup AS A, aw_campaign AS B, aw_ad_account AS C
                    WHERE A.campaignId = B.id AND B.customerId = C.customerId AND C.is_hidden = 0 AND C.is_update = 1";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdsWithCustomer() {
        $sql = "SELECT A.*, D.customerId, D.is_update FROM aw_ad AS A, aw_adgroup AS B, aw_campaign AS C, aw_ad_account AS D
                    WHERE A.adgroupId = B.id AND B.campaignId = C.id AND C.customerId = D.customerId AND D.is_hidden = 0 AND D.is_update = 1 ORDER BY A.update_time ASC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getCustomerIdByCampaignId($campaignId) {
        if(!$campaignId) return false;
        $result = false;
        $sql = "SELECT customerId FROM aw_campaign WHERE id = {$campaignId}";
        if($result = $this->db_query($sql)) {
            $row = $result->fetch_assoc();
            $result = $row['customerId'];
        }
        return $result;
    }

    public function getCustomerIdByAdGroupId($adgroupId) {
        if(!$adgroupId) return false;
        $result = false;
        $sql = "SELECT customer_id FROM aw_ad_list WHERE adgroup_id = {$adgroupId}";
        if($result = $this->db_query($sql)) {
            $row = $result->fetch_assoc();
            $result = $row['customer_id'];
        }
        return $result;
    }

    public function getCustomerIdByAdId($adId) {
        if(!$adId) return false;
        $result = false;
        $sql = "SELECT customer_id FROM aw_ad_list WHERE ad_id = {$adId}";
        if($result = $this->db_query($sql)) {
            $row = $result->fetch_assoc();
            $result = $row['customer_id'];
        }
        return $result;
    }

    public function getAdGroupIdByAdId($adId) {
        if(!$adId) return false;
        $result = false;
        $sql = "SELECT adgroup_id FROM aw_ad_list WHERE ad_id = {$adId}";
        if($result = $this->db_query($sql)) {
            $row = $result->fetch_assoc();
            $result = $row['adgroup_id'];
        }
        return $result;
    }

    public function getAdAccounts($is_hidden=0, $order=false) {
        $sql = "SELECT * FROM aw_ad_account WHERE 1";

        if(!is_null($is_hidden))
            $sql .= " AND is_hidden = {$is_hidden}";
        if($order) $sql .= " ".$order;
        $result = $this->db_query($sql);

        return $result;
    }

    public function insertAdAccounts($accountList, $is_hidden=0) {
        $result = $this->db_query($sql);
        foreach($accountList['data'] as $data) {
            $this->insertAccount($data, $is_hidden);
        }
    }

    public function insertAccount($data, $is_hidden=0) { //1 숨김, 0 활성화
		$is_update = 0;
        if($is_hidden === 'true') {
			$is_hidden = 1;
		}
		if($is_hidden == 0) {
			$is_update = 1; //1 업데이트, 0 제외
		}
        $data['Name'] = $this->db->real_escape_string($data['Name']);
        $sql = "INSERT INTO aw_ad_account (customerId, name, canManageClients, currencyCode, dateTimeZone, testAccount, is_hidden, create_time)
                VALUES ({$data['CustomerId']}, '{$data['Name']}', {$data['CanManageClients']}, '{$data['CurrencyCode']}', '{$data['DateTimeZone']}', {$data['TestAccount']}, {$is_hidden}, NOW()) ON DUPLICATE KEY
                UPDATE customerId = {$data['CustomerId']}, is_update = {$is_update}, name = '{$data['Name']}', canManageClients = {$data['CanManageClients']}, currencyCode = '{$data['CurrencyCode']}', dateTimeZone = '{$data['DateTimeZone']}', testAccount = {$data['TestAccount']}, is_hidden = {$is_hidden}, update_time=NOW();";
        $result = $this->db_query($sql, true);
        return $result;
    }

    public function hiddenAccount() {
        $sql = "UPDATE aw_ad_account SET is_update = 0";
        $result = $this->db_query($sql, true);
    }

    public function insertCampaigns($campaignList) {
        foreach($campaignList as $data) {
            if(is_array($data) && count($data) > 0) $this->insertCampaign($data);
        }
    }

    public function getCampaigns() {
        $sql = "SELECT * FROM aw_campaign WHERE 1";
        $result = $this->db_query($sql);

        return $result;
    }

    public function insertCampaign($data) {
        if($data['Id']) {
            if(!$data['Name']) $data['Name'] = '제목없음';
            $data['Name'] = $this->db->real_escape_string($data['Name']);
            $sql = "INSERT INTO aw_campaign(customerId, id, name, status, servingStatus, startDate, endDate, budgetId, budgetName, budgetReferenceCount, budgetStatus, amount, deliveryMethod, advertisingChannelType, AdServingOptimizationStatus, campaignTrialType, baseCampaignId, fc_impressions, fc_timeUnit, fc_level, create_time)
                    VALUES('{$data['CustomerId']}', '{$data['Id']}', '{$data['Name']}', '{$data['Status']}', '{$data['ServingStatus']}', '{$data['StartDate']}', '{$data['EndDate']}', '{$data['BudgetId']}', '{$data['BudgetName']}', '{$data['BudgetReferenceCount']}', '{$data['BudgetStatus']}', '{$data['Amount']}', '{$data['DeliveryMethod']}', '{$data['AdvertisingChannelType']}', '{$data['AdServingOptimizationStatus']}', '{$data['CampaignTrialType']}', '{$data['BaseCampaignId']}', '{$data['FrequencyCap']['impressions']}', '{$data['FrequencyCap']['timeUnit']}', '{$data['FrequencyCap']['level']}', NOW())
                    ON DUPLICATE KEY UPDATE
                        name = '{$data['Name']}',
                        status = '{$data['Status']}',
                        servingStatus = '{$data['ServingStatus']}',
                        startDate = '{$data['StartDate']}',
                        endDate = '{$data['EndDate']}',
                        budgetId = '{$data['BudgetId']}',
                        budgetName = '{$data['BudgetName']}',
                        budgetReferenceCount = '{$data['BudgetReferenceCount']}',
                        budgetStatus = '{$data['BudgetStatus']}',
                        amount = '{$data['Amount']}',
                        deliveryMethod = '{$data['DeliveryMethod']}',
                        advertisingChannelType = '{$data['AdvertisingChannelType']}',
                        AdServingOptimizationStatus = '{$data['AdServingOptimizationStatus']}',
                        campaignTrialType = '{$data['CampaignTrialType']}',
                        baseCampaignId = '{$data['BaseCampaignId']}',
                        fc_impressions = '{$data['FrequencyCap']['impressions']}',
                        fc_timeUnit = '{$data['FrequencyCap']['timeUnit']}',
                        fc_level = '{$data['FrequencyCap']['level']}',
                        is_updating = 0,
                        update_time = NOW()";
                        // echo $sql .'<br>'; exit;
            $result = $this->db_query($sql, true);
        }
        return $result;
    }

    public function canNotUpdateCampaign($campaign_id) {
        $result = null;
        if($campaign_id) {
            $sql = "UPDATE aw_campaign SET status = 'NODATA' WHERE id = {$campaign_id}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function updateCampaign($data) {
        $result = false;
        if(!$data['id'])
            return $result;
        if($data['name']) {
            $data['name'] = $this->db->real_escape_string($data['name']);
            $query[] = "name = '{$data['name']}'";
        }
        if($data['status']) $query[] = "status = '{$data['status']}'";
        if($data['budget']) {
            $data['budget'] = $data['budget'] / 1000000;
            $query[] = "amount = '{$data['budget']}'";
        }
        if($data['cpaBidAmount']) $query[] = "cpaBidAmount = '{$data['cpaBidAmount']}'";
        if(is_array($query)) {
            $sql = "UPDATE aw_campaign SET ".implode(',', $query)." WHERE id = {$data['id']}";
            $result = $this->db_query($sql, true);
        }
        return $result;
    }

    public function insertAdGroups($adgroupList) {
        foreach($adgroupList as $data) {
            if(is_array($data) && count($data) > 0) $this->insertAdGroup($data);
        }
    }

    public function getAdGroups() {
        $sql = "SELECT * FROM aw_adgroup WHERE 1";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getCampaignBudget($campaign_id) {
        $result = false;
        if($campaign_id) {
            $sql = "SELECT amount FROM aw_campaign WHERE id = {$campaign_id} ";
            $result = $this->db_query($sql);
            $row = $result->fetch_assoc();
            $result = $row['amount'];
        }
        return $result;
    }

    public function insertAdGroup($data) {
        if($data['Id']) {
            if(!$data['Name']) $data['Name'] = '제목없음';
            $data['Name'] = $this->db->real_escape_string($data['Name']);
            $sql = "INSERT INTO aw_adgroup(campaignId, id, name, status, adGroupType, biddingStrategyType, cpcBidAmount, cpcBidSource, cpmBidAmount, cpmBidSource, cpaBidAmount, cpaBidSource, create_time)
                    VALUES('{$data['CampaignId']}', '{$data['Id']}', '{$data['Name']}', '{$data['Status']}', '{$data['AdGroupType']}', '{$data['BiddingStrategyConfiguration']['BiddingStrategyType']}', '{$data['BiddingStrategyConfiguration']['CpcBidAmount']}', '{$data['BiddingStrategyConfiguration']['CpcBidSource']}', '{$data['BiddingStrategyConfiguration']['CpmBidAmount']}', '{$data['BiddingStrategyConfiguration']['CpmBidSource']}', '{$data['BiddingStrategyConfiguration']['CpaBidAmount']}', '{$data['BiddingStrategyConfiguration']['CpaBidSource']}', NOW())
                    ON DUPLICATE KEY UPDATE
                        name = '{$data['Name']}',
                        status = '{$data['Status']}',
                        adGroupType = '{$data['AdGroupType']}',
                        biddingStrategyType = '{$data['BiddingStrategyConfiguration']['BiddingStrategyType']}',
                        cpcBidAmount = '{$data['BiddingStrategyConfiguration']['CpcBidAmount']}',
                        cpcBidSource = '{$data['BiddingStrategyConfiguration']['CpcBidSource']}',
                        cpmBidAmount = '{$data['BiddingStrategyConfiguration']['CpmBidAmount']}',
                        cpmBidSource = '{$data['BiddingStrategyConfiguration']['CpmBidSource']}',
                        cpaBidAmount = '{$data['BiddingStrategyConfiguration']['CpaBidAmount']}',
                        cpaBidSource = '{$data['BiddingStrategyConfiguration']['CpaBidSource']}',
                        update_time = NOW()";
            $result = $this->db_query($sql, true);
        }
        return $result;
    }

    public function canNotUpdateAdGroup($adgroup_id) {
        $result = null;
        if($adgroup_id) {
            $sql = "UPDATE aw_adgroup SET status = 'NODATA' WHERE id = {$adgroup_id}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function updateAdGroup($data) {
        $result = false;
        if(!$data['id'])
            return $result;
        if($data['name']) {
            $data['name'] = $this->db->real_escape_string($data['name']);
            $query[] = "name = '{$data['name']}'";
        }
        if($data['status']) $query[] = "status = '{$data['status']}'";
        if($data['cpcBidAmount']) $query[] = "cpcBidAmount = '{$data['cpcBidAmount']}'";
        if($data['cpmBidAmount']) $query[] = "cpmBidAmount = '{$data['cpmBidAmount']}'";
        if($data['cpaBidAmount']) $query[] = "cpaBidAmount = '{$data['cpaBidAmount']}'";
        if(is_array($query)) {
            $sql = "UPDATE aw_adgroup SET ".implode(',', $query)." WHERE id = {$data['id']}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function getAdsWithCampaign($campaign_ids) {
        $result = NULL;
        if(is_array($campaign_ids)) {
            $sql = "SELECT * FROM aw_ad_list WHERE campaign_id IN (".implode(',', $campaign_ids).")";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function insertAds($adList) {
        foreach($adList as $data) {
            if(is_array($data) && count($data) > 0) $this->insertAd($data);
        }
    }

    public function insertAd($data) {
        if($data['Id']) {
            if(!$data['Name']) $data['Name'] = '제목없음';
            $data['Name'] = $this->db->real_escape_string($data['Name']);
            $sql = "INSERT INTO aw_ad(adgroupId, id, name, status, adType, mediaType, imageUrl, finalUrl, create_time)
                        VALUES('{$data['AdGroupId']}', '{$data['Id']}', '{$data['Name']}', '{$data['Status']}', '{$data['AdType']}', '{$data['MediaType']}', '{$data['Image']['Urls']}', '{$data['finalUrl']}', NOW())
                        ON DUPLICATE KEY UPDATE
                            adgroupId = '{$data['AdGroupId']}',
                            name = '{$data['Name']}',
                            status = '{$data['Status']}',
                            adType = '{$data['AdType']}',
                            mediaType = '{$data['MediaType']}',
                            imageUrl = '{$data['Image']['Urls']}',
                            finalUrl = '{$data['finalUrl']}',
                            update_time = NOW();";
                            // echo $sql.'<br>';
            $result = $this->db_query($sql, true);
        }
        return $result;
    }

    public function canNotUpdateAd($ad_id) {
        $result = null;
        if($ad_id) {
            $sql = "UPDATE aw_ad SET status = 'NODATA' WHERE id = {$ad_id}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function updateAd($data) {
        $result = false;
        if(!$data['id'])
            return $result;
        if($data['name']) {
            $data['name'] = $this->db->real_escape_string($data['name']);
            $query[] = "name = '{$data['name']}'";
        }
        if($data['status']) $query[] = "status = '{$data['status']}'";
        if($data['code']) $query[] = "code = '{$data['code']}'";
        if(is_array($query)) {
            $sql = "UPDATE aw_ad SET ".implode(',', $query)." WHERE id = {$data['id']}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function setAdCode($data) {
        $result = false;
        if($data['ad_id']) {
            $sql = "UPDATE aw_ad SET code = '{$data['code']}' WHERE id = {$data['ad_id']}";
            $result = $this->db_query($sql);
        }
        return $result;
    }

    public function insertAdReports($reports) {
        foreach($reports as $data) {
            if(count($data) > 0) $this->insertAdReport($data);
        }
    }

    public function insertAdReport($data) {
        $data['cost'] = $data['cost'] / 1000000;
        $sql = "INSERT INTO aw_ad_report(ad_id, impressions, clicks, cost, create_time)
                    VALUES('{$data['ad_id']}', '{$data['impressions']}', '{$data['clicks']}', '{$data['cost']}', NOW())
                    ON DUPLICATE KEY UPDATE
                        impressions = '{$data['impressions']}',
                        clicks = '{$data['clicks']}',
                        cost = '{$data['cost']}',
                        update_time = NOW()";
        $result = $this->db_query($sql, true);

        $sql = "INSERT INTO aw_ad_report_history(ad_id, date, impressions, clicks, cost, create_time)
                    VALUES('{$data['ad_id']}', '{$data['date']}', '{$data['impressions']}', '{$data['clicks']}', '{$data['cost']}', NOW())
                    ON DUPLICATE KEY UPDATE
                        impressions = '{$data['impressions']}',
                        clicks = '{$data['clicks']}',
                        cost = '{$data['cost']}',
                        update_time = NOW()";
        $result = $this->db_query($sql, true);
    }

    public function getAdLeads($date)
    {
        $sql = "SELECT his.ad_id, his.cost, ad.code
            FROM `aw_ad_report_history` AS his
            LEFT JOIN aw_ad AS ad
                ON his.ad_id = ad.id
            WHERE his.date = '{$date}' AND ad.code <> '' GROUP BY his.ad_id";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAppSubscribe($data, $date)
    {
        if (!$data['db_prefix']) {
            return 0;
        }
        $sql = "SELECT * FROM app_subscribe WHERE group_id = '{$data['app_id']}' AND status = 1 AND site = '{$data['site']}' AND DATE_FORMAT(reg_date, '%Y-%m-%d') = '{$date}' AND deleted = 0";
        $res = $this->g5db->query($sql);
        $num_rows = $res->num_rows;
        return $num_rows;
    }

    public function updateReport($data)
    {
        if ($data->ad_id && $data->date) {
            
            foreach ($data->data as $field => $value) {
                $query[] = "{$field} = '{$value}'";
            }
            $sql = "UPDATE aw_ad_report_history SET ".implode(',', $query)." WHERE ad_id = {$data->ad_id} AND date = '{$data->date}'";
            $this->db_query($sql);
            
        }
        return false;
    }

    public function insertAdLeads($data) {
        if (count($data) > 0) {
            if(!$data['lead_id']) return false;
            $insert = "INSERT IGNORE INTO `aw_ad_lead` SET ";
            $this->db_query('BEGIN');
            $custom_fields = array();
            $values = "id = '{$data['lead_id']}', form_id = '{$data['form_id']}', creative_id = '{$data['creative_id']}', created_time = NOW()";
            if (count($data['user_column_data']) > 0) {
                foreach ($data['user_column_data'] as $key => $field) {
                    if (preg_match("/[a-z\_]+/i", $field['column_id']) && !preg_match("/[가-힣]+/i", $field['column_id'])) {
                        $field['column_id'] = strtolower($field['column_id']);
                        $field['string_value'] = trim(addslashes($field['string_value']));
                        @$this->db->query("ALTER TABLE `aw_ad_lead` ADD `{$field['column_id']}` VARCHAR(100) NULL DEFAULT NULL AFTER `phone_number`;");
                        if (trim($field['string_value'])) {
                            $values .= ", {$field['column_id']} = '{$field['string_value']}'";
                        }
                    } else {
                        array_push($custom_fields, $field);
                    }
                }

                $field_data = addslashes(var_export($custom_fields, true));
                $values .= ", field_data = '{$field_data}'";
                $sql = $insert . $values;
                $result = $this->db_query($sql);
            }
            $this->db_query('COMMIT');
            return "sql:".$sql;
        }
    }

    public function insertDbCount($data, $date)
    {
        foreach ($data as $key => $row) {
            if ($row['ad_id']) {
                $sql = "INSERT INTO aw_db_count (ad_id, event_id, site, media, db_price, db_count, margin, date, create_time)
                        VALUES ('{$row['ad_id']}', '{$row['app_id']}', '{$row['site']}', '{$row['media']}', '{$row['db_price']}', '{$row['count']}', '{$row['margin']}', '{$date}', NOW())
                        ON DUPLICATE KEY
                        UPDATE ad_id = '{$row['ad_id']}', event_id = '{$row['app_id']}', site = '{$row['site']}', media = '{$row['media']}', db_price = '{$row['db_price']}', db_count = '{$row['count']}', margin = '{$row['margin']}', date = '{$date}', update_time = NOW();";
                $result = $this->db_query($sql, true);
            }
        }
    }

    public function getEventInfoByCampaignId($campaign_id, $date=null){
        $date = date('Y-m-d');
        $sql = "SELECT D.* FROM aw_campaign A
                    LEFT JOIN aw_adgroup AS B ON A.id = B.campaignId
                    LEFT JOIN aw_ad AS C ON B.id = C.adgroupId
                    LEFT JOIN aw_db_count AS D ON C.id = D.ad_id
                    WHERE D.date = '{$date}' AND A.id = {$campaign_id} ";
        $result = $this->db_query($sql, true);
        return $result;
    }

    public function getImpressionsByEvent($evt, $site="", $date="", $code="") {
        $sql = "SELECT seq, site, date, SUM(impressions) AS impressions FROM event_impressions_history WHERE seq = {$evt} ";
        if($code) $sql .= " AND site = '{$code}'";
        if($site) $sql .= " AND site = '{$site}'";
        if($date) $sql .= " AND date = '{$date}'";
        $sql .= " GROUP BY seq";
        $result = $this->g5db->query($sql);
        return $result;
    }

    //정파고(광고)
    public function getOptimization_campaignByReport($date, $type)
    {
        $sql = "SELECT A.id AS id, A.name AS name, C.adgroupId, A.status, A.amount AS budget, D.date, ROUND(SUM( D.cost )/ SUM( D.clicks )) AS cpc, GROUP_CONCAT(DISTINCT TRIM(REPLACE(SUBSTRING_INDEX(C.CODE,'*', 1 ),'#',''))) AS evt, E.budget_00, SUM(DISTINCT D.clicks) AS clicks
                FROM aw_campaign A, aw_adgroup B, aw_ad C, aw_ad_report_history D, aw_optimization_campaign E
                WHERE A.id = B.campaignId AND B.id = C.adgroupId AND A.id = E.campaign_id AND C.id = D.ad_id AND A.status != 'NODATA'AND TRIM( C.CODE ) <> ''AND SUBSTRING( TRIM( C.CODE ), 2, 4 ) <> '9999' AND D.date = '{$date}' AND E.type = '{$type}'
                GROUP BY A.id, D.date ";
        $result = $this->db_query($sql, true);

        return $result;
    }

    public function get_leveled_Optimization_campaignByReport($date, $pre_type)
    {
        $sql = "SELECT A.id AS id, A.name AS name, B.id AS adgroup_id, C.id AS ad_id, A.status, A.amount AS budget, ROUND(SUM( H.cost )/ SUM( H.clicks )) AS cpc, SUM(DISTINCT H.clicks ) AS clicks, E.type, D.db_price, SUM(D.db_count) AS db_count FROM aw_campaign A
            LEFT JOIN aw_adgroup AS B ON A.id = B.campaignId
            LEFT JOIN aw_ad AS C ON B.id = C.adgroupId
            LEFT JOIN aw_db_count AS D ON C.id = D.ad_id
            LEFT JOIN aw_optimization_campaign AS E ON A.id = E.campaign_id
            LEFT JOIN aw_ad_report_history AS H ON C.id = H.ad_id
        WHERE H.date = '{$date}' AND D.date = '{$date}' AND E.type LIKE '{$pre_type}%'
        GROUP BY A.id";
        $result = $this->db_query($sql, true);

        return $result;
    }

    public function getOptimization_campaignById($campaign_id)
    {
        $sql = "SELECT campaign_id FROM aw_optimization_campaign WHERE campaign_id = {$campaign_id}";
        $result = $this->db_query($sql, true);
        $data = $result->fetch_assoc();

        return $data['campaign_id'];
    }

    public function getOptimization_campaign($type)
    {
        $sql = "SELECT A.id, A.name, A.status FROM aw_campaign A, aw_optimization_campaign E WHERE A.id = E.campaign_id AND A.STATUS != 'NODATA' AND type = '{$type}' GROUP BY A.id";
        $result = $this->db_query($sql, true);

        return $result;
    }

    public function getOptimization_leveled_campaign($pre_fix)
    {
        $sql = "SELECT A.id, A.name, A.status, E.type FROM aw_campaign A, aw_optimization_campaign E WHERE A.id = E.campaign_id AND A.STATUS != 'NODATA' AND type LIKE '{$pre_fix}%' GROUP BY A.id";
        $result = $this->db_query($sql, true);

        return $result;
    }

    //캠페인단 정파고 on/off
    public function insertOptimization_campaign($campaign_id, $mb_id, $op_type)
    {
        $sql = "SELECT seq, campaign_id FROM aw_optimization_campaign WHERE campaign_id='{$campaign_id}' AND type = '{$op_type}'";

        $res = $this->db_query($sql);
        $row = $res->fetch_assoc();

        if ($row['campaign_id']) {
            $sql = "DELETE FROM aw_optimization_campaign WHERE campaign_id = '{$campaign_id}' AND seq = {$row['seq']}";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            if($this->getOptimization_campaignById($campaign_id)) return "MA";      // AI 중복 가동 체크
            $sql = "INSERT INTO aw_optimization_campaign (campaign_id, type, create_time)
                SELECT '{$campaign_id}', '{$op_type}' , NOW()
                FROM DUAL
                WHERE not exists(SELECT campaign_id,create_time FROM aw_optimization_campaign where campaign_id='{$campaign_id}' AND type = '{$op_type}')";
                $this->db_query($sql);
            $re_type = "ON";
        }


        $sql = "INSERT INTO aw_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
                VALUES ('{$campaign_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
                $this->db_query($sql);
        return $re_type;
    }

    public function insertOptimization_campaign_withBudget($campaign_id, $mb_id, $op_type, $budget)
    {
        $sql = "SELECT seq, campaign_id FROM aw_optimization_campaign WHERE campaign_id='{$campaign_id}' AND type = '{$op_type}'";
        $res = $this->db_query($sql);
        $row = $res->fetch_assoc();

        if ($row['campaign_id']) {
            if($budget>0){
                $sql = "UPDATE aw_optimization_campaign SET budget_00 = '{$budget}', update_time = NOW() WHERE campaign_id='{$campaign_id}' AND seq = {$row['seq']}";
                $this->db_query($sql);
                $re_type = "ON";
            }else{
                $sql = "DELETE FROM aw_optimization_campaign WHERE campaign_id = '{$campaign_id}' AND seq = {$row['seq']}";
                $this->db_query($sql);
                $re_type = "OFF";
            }
        } else {
            if($this->getOptimization_campaignById($campaign_id)) return "MA";      // AI 중복 가동 체크
            $sql = "INSERT INTO aw_optimization_campaign (campaign_id, type, budget_00, create_time)
                SELECT '{$campaign_id}', '{$op_type}', '{$budget}' , NOW()
                FROM DUAL
                WHERE not exists(SELECT campaign_id,create_time FROM aw_optimization_campaign where campaign_id='{$campaign_id}' AND type = '{$op_type}')";
                $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO aw_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
                VALUES ('{$campaign_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
                $this->db_query($sql);
        return $re_type;
    }

    //캠페인단 SONG AI on/off
    public function insert_Leveled_Optimization_campaign($campaign_id, $mb_id, $pre_fix, $level, $max_level=3)
    {
        $sql = "SELECT seq, campaign_id, type, budget_00 FROM aw_optimization_campaign WHERE campaign_id='{$campaign_id}' AND type LIKE '{$pre_fix}%'";
        $res = $this->db_query($sql);
        $row = $res->fetch_assoc();

        $budget_00 = $row['budget_00'];
        if ($row['campaign_id'] && $level=='0') {
            $re_type = "OFF";
            $sql = "DELETE FROM aw_optimization_campaign WHERE campaign_id = '{$campaign_id}' AND seq = {$row['seq']}";
            $this->db_query($sql);
        }else if($row['campaign_id'] && $level!='0'){
            $re_type = $pre_fix.$level;
            $sql = "UPDATE aw_optimization_campaign SET type = '{$re_type}', update_time = NOW() WHERE campaign_id='{$campaign_id}' AND seq = {$row['seq']}";
            $this->db_query($sql);
        }else{
            if($this->getOptimization_campaignById($campaign_id)) return "MA";      // AI 중복 가동 체크
            $re_type = $pre_fix.$level;
            $budget_00 = $this->getCampaignBudget($campaign_id);
            $sql = "INSERT INTO aw_optimization_campaign (campaign_id, type, budget_00, create_time)
            SELECT '{$campaign_id}', '{$re_type}', '{$budget_00}' , NOW()
            FROM DUAL
            WHERE not exists(SELECT campaign_id,create_time FROM aw_optimization_campaign where campaign_id='{$campaign_id}' AND type LIKE '{$pre_fix}%' )";
            $this->db_query($sql);
        }

        $sql = "INSERT INTO aw_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
                VALUES ('{$campaign_id}', '{$mb_id}', '{$row['type']}', '{$re_type}', NOW())";
                $this->db_query($sql);      // OnOff history
        return $re_type;
    }

    //정파고(캠페인) 히스토리
    public function insertOptimizationHistory_campaign($campaign_id, $budget, $type)
    {
        $sql = "INSERT INTO aw_optimization_history_campaign SET campaign_id='{$campaign_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //캠페인 예산 히스토리
    public function insertOptimizationBudgetHistory_campaign($campaign_id, $sort=NULL, $budget_old, $budget_new, $type)
    {
        $sql = "INSERT INTO aw_optimization_budget_history_campaign SET campaign_id='{$campaign_id}', sort='{$sort}', budget_old='{$budget_old}', budget_new='{$budget_new}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    public function insertOnoffHistoryCampaign($campaign_id, $mb_id, $type, $switch){
        $sql = "INSERT INTO aw_onoff_history_campaign SET campaign_id='{$campaign_id}', mb_id='{$mb_id}', type='{$type}', switch='{$switch}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //정파고(광고) ON/OFF 업데이트
    public function updateOptimization_campaign()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT  A.id AS id, A.name AS name, A.status AS status, A.is_updating, SUM(D.cost) AS cost, A.amount
            FROM aw_campaign A, aw_adgroup B, aw_ad C, aw_ad_report_history D, aw_optimization_campaign E
            WHERE A.id = E.campaign_id AND A.id = B.campaignId AND B.id = C.adgroupId AND C.id = D.ad_id AND A.status != 'NODATA' And D.date = '{$today}' GROUP BY id
        ";

//      echo $sql."<br/>";

        $res = $this->db_query($sql);

        while ($row = $res->fetch_assoc()) {
            //지출액이 예산보다 많을 때
            if ($row['cost'] >= $row['amount']) {
                $budget= "PAUSED"; //off
            }
            else{
                $budget= "ACTIVE"; //on
            }

//          echo $row['ad_id']."^".$row['db_count']."^".$row['margin']."^".$row['sales']."^".$margin_ratio."<br/>";

            //==========================================================================================

            $sql = "UPDATE aw_optimization_campaign set budget_{$hour} = '{$budget}', update_time = NOW() where campaign_id='{$row['id']}' AND TYPE = 901";
            // echo $sql; exit;
            $this->db->query($sql) or die($this->db->error);

            $sql = "UPDATE aw_optimization_campaign set budget_00 = 'ACTIVE', update_time = NOW()  where campaign_id='{$row['id']}' AND TYPE = 901";
            $this->db->query($sql) or die($this->db->error);
        }
        //====================================================================
        //미진행중인 광고중 정파고(광고)켜있는거 모두 삭제
        if($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
            $sql = "SELECT * FROM aw_optimization_campaign WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR) AND type = 901 ";
            $res = $this->db->query($sql);
            while ($row = $res->fetch_assoc()) {
                $sql = "DELETE FROM aw_optimization_campaign WHERE campaign_id = '{$row['campaign_id']}' AND type = 901 ";
                $this->db->query($sql) or die($this->db->error);

                //정파고(광고) off 히스토리 남기기
                $sql = "INSERT INTO aw_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
                        VALUES ('{$row['campaign_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
                $this->db->query($sql) or die($this->db->error);
            }
        }
        //====================================================================
    }

    public function insertReport($data) {
        /********************************
        # # 영상 광고 엑셀 업데이트 함수
        #
        # 1. 광고 테이블 인서트/업데이트   (aw_campaign, aw_adgroup, aw_ad, aw_ad_report, aw_db_count)
        #       - 영상 광고는 API를 통해 객체를 불러올 수 없음.
        # 2. 엑셀 저장
        #       - 경로 : home/chainsaw/www/plugin/adwords_api/excel
        #
        *********************************/
        if(count($data) > 0) {
            // 동영상 광고는 evt 랜딩에서만 사용 중이라고 가정함. 이외의 코드는 추가가 필요합니다.
            $landingGroup = array(
                "ghr"=>array(
                    "media"=>"핫이벤트 룰렛"
                    ,"db_prefix"=>"app_"
                )
                ,"ghrcpm"=>array(
                    "media"=>"핫이벤트 룰렛_cpm"
                    ,"db_prefix"=>"app_"
                )
                ,"ger"=>array(
                    "media"=>"이벤트 랜딩"
                    ,"db_prefix"=>"evt_"
                )
                ,"cpm"=>array(
                    "media"=>"cpm"
                    ,"db_prefix"=>""
                )
            );
            $biddingStrategyType_list = array(
                "타겟 CPA"=>"TARGET_CPA"
                ,"타겟 광고 투자수익(ROAS)"=>"TARGET_ROAS"
                ,"클릭수 최대화"=>"TARGET_SPEND"
                ,"전환수 최대화"=>"MAXIMIZE_CONVERSIONS"
                ,"향상된 CPC 입찰기능"=>"PAGE_ONE_PROMOTED"
                ,"수동 입찰 전략"=>"MANUAL_CPM"
                ,"수동 CPC"=>"MANUAL_CPC"
                ,"알수없음"=>"UNKNOWN"
            );
            $succeess = 0;
            $fail = 0;
            $failed_rows = [];
            for($r=1;$r<=sizeof($data);$r++){
                if(!$data[$r]['customerId'] || !$data[$r]['campaignId'] || !$data[$r]['adgroupId'] || !$data[$r]['ad_id']) continue;

                $ad_url_param = [];
                foreach(explode('&',array_pop(explode('?',$data[$r]['finalUrl']))) as $k => $v){
                    $param = explode('=', $v);
                    $ad_url_param[$param[0]] = $param[1];
                }
                $campaign_code = explode('>', $data[$r]['campaign_name']);
                $ad_code = "#".$campaign_code[1]."_".$ad_url_param['site']." *".$campaign_code[2]." &".$campaign_code[3];       // #랜딩번호_사이트값 *DB단가 &ger
                $customerId = str_replace('-','',$data[$r]['customerId']);
                $date = $data[$r]['date']==date('Y-m-d')?"NOW()":"'".$data[$r]['date']."'";
                $campaign_status = strpos($data[$r]['campaign_status'], "운영 가능")!==false?"ENABLED":"PAUSED";
                $Adgroupstate = $data[$r]['Adgroupstate']=="사용중"?"ENABLED":"PAUSED";
                $Adstate = $data[$r]['Adstate']=="사용중"?"ENABLED":"PAUSED";
                $biddingStrategyType = $biddingStrategyType_list[$data[$r]['biddingStrategyType']]?$biddingStrategyType_list[$data[$r]['biddingStrategyType']]:"UNKNOWN";

                $aw_campaign = "INSERT INTO aw_campaign ( customerId, id, name, status, amount, advertisingChannelType, create_time ) 
                                VALUES ( {$customerId}, {$data[$r]['campaignId']}, '{$data[$r]['campaign_name']}', '{$campaign_status}', {$data[$r]['amount']}, 'VIDEO', {$date} ) 
                                ON DUPLICATE KEY 
                                UPDATE customerId  = {$customerId}, id = {$data[$r]['campaignId']}, name = '{$data[$r]['campaign_name']}', status = '{$campaign_status}', amount = '{$data[$r]['amount']}', advertisingChannelType = 'VIDEO', update_time = NOW(); ";
                $aw_adgroup = "INSERT INTO aw_adgroup ( campaignId, id, name, status, adGroupType, biddingStrategyType, create_time ) 
                                VALUES ( {$data[$r]['campaignId']}, {$data[$r]['adgroupId']}, '{$data[$r]['adgroup']}', '{$Adgroupstate}', 'VIDEO_STANDARD', '{$biddingStrategyType}', {$date} ) 
                                ON DUPLICATE KEY 
                                UPDATE campaignId  = {$data[$r]['campaignId']}, id = {$data[$r]['adgroupId']}, name = '{$data[$r]['adgroup']}', status = '{$Adgroupstate}', adGroupType = 'VIDEO_STANDARD', biddingStrategyType = '{$biddingStrategyType}', update_time = NOW(); ";
                $aw_ad = "INSERT INTO aw_ad ( adgroupId, id, name, code, status, adType, finalUrl, create_time ) 
                        VALUES ( {$data[$r]['adgroupId']}, {$data[$r]['ad_id']}, '{$data[$r]['ad_name']}', '{$ad_code}', '{$Adstate}', 'VIDEO_AD', '{$data[$r]['finalUrl']}', {$date} ) 
                        ON DUPLICATE KEY 
                        UPDATE adgroupId  = {$data[$r]['adgroupId']}, id = {$data[$r]['ad_id']}, name = '{$data[$r]['ad_name']}', code = '{$ad_code}', status = '{$Adstate}', adType = 'VIDEO_AD', finalUrl = '{$data[$r]['finalUrl']}', update_time = NOW(); ";

                $data[$r]['cost_ori'] = $data[$r]['cost'];
                $data[$r]['impressions'] = str_replace(',','',$data[$r]['impressions']);
                $data[$r]['clicks'] = str_replace(',','',$data[$r]['clicks']);
                $data[$r]['cost'] = str_replace(',','',$data[$r]['cost']) * 1000000;
                $this->insertAdReport($data[$r]);       // adReport 업데이트

                // echo $aw_campaign."<br>";
                // echo $aw_adgroup."<br>";
                // echo $aw_ad."<br>";

                $this->db_query($aw_campaign);
                $this->db_query($aw_adgroup);
                $this->db_query($aw_ad);

                if($data[$r]['date']!=date('Y-m-d')){
                    $past_date = $data[$r]['date'];

                    $getAppSubscribe['app_id'] = $landingGroup[$campaign_code[3]]['db_prefix'].$campaign_code[1];
                    $getAppSubscribe['site'] = $ad_url_param['site'];
                    $getAppSubscribe['db_prefix'] = 1;
                    $rows = $this->getAppSubscribe($getAppSubscribe, $past_date);
                    $margin = $campaign_code[2] * $rows - $data[$r]['cost_ori'];

                    $sql = "INSERT INTO aw_db_count (ad_id, event_id, site, media, db_price, db_count, margin, date, create_time)
                        VALUES ('{$data[$r]['ad_id']}', 'evt_{$campaign_code[1]}', '{$ad_url_param['site']}', '이벤트 랜딩', '{$campaign_code[2]}', '{$rows}', '{$margin}', '{$past_date}', '{$past_date}')
                        ON DUPLICATE KEY
                        UPDATE ad_id = '{$data[$r]['ad_id']}', event_id = 'evt_{$campaign_code[1]}', site = '{$ad_url_param['site']}', media = '이벤트 랜딩', db_price = '{$campaign_code[2]}', db_count = '{$rows}', margin = '{$margin}', date = '{$past_date}', update_time = NOW();";
                    // echo $sql."<br>";
                    $this->db_query($sql);
                }
                $succeess++;
            }
            
            $msg = $succeess."건 업로드 완료 \n";
            if($fail>0) $msg.=$fail."건 실패 / 실패한 행[".implode($failed_rows)."]";
            return $msg;
        }
    }

    public function insertVideoReport($data) {
        /********************************
        # # 영상 광고 엑셀 업데이트 함수 # #
        # # 광고 테이블 인서트/업데이트   (aw_campaign, aw_adgroup, aw_ad, aw_db_count)
        #       - 영상 광고는 API를 통해 객체를 불러올 수 없음.
        *********************************/
        if(count($data) <= 0) return null;

        for($r=1;$r<=sizeof($data);$r++){
            if(!$data[$r]['CustomerID'] || !$data[$r]['CampaignID'] || !$data[$r]['AdgroupID'] || !$data[$r]['AdID']) continue;

            $Campaignstate = strtoupper($data[$r]['Campaignstate']);
            $Adgroupstate = strtoupper($data[$r]['Adgroupstate']);
            $Adstate = strtoupper($data[$r]['Adstate']);

            $CampaignName = str_replace("'","",$data[$r]['Campaign']);
            $adName = $this->getAdName($data[$r]['AdID']);
            $adName = $adName?$adName:$data[$r]['Adgroup'];

            # Campaign
            $aw_campaign = "INSERT INTO aw_campaign ( customerId, id, name, status, advertisingChannelType, create_time ) 
            VALUES ( {$data[$r]['CustomerID']}, {$data[$r]['CampaignID']}, '{$CampaignName}', '{$Campaignstate}', 'VIDEO', NOW() ) 
            ON DUPLICATE KEY 
            UPDATE customerId  = {$data[$r]['CustomerID']}, id = {$data[$r]['CampaignID']}, name = '$CampaignName', status = '{$Campaignstate}', advertisingChannelType = 'VIDEO', update_time = NOW(); ";

            # Adgroup
            $aw_adgroup = "INSERT INTO aw_adgroup ( campaignId, id, name, status, adGroupType, create_time ) 
            VALUES ( {$data[$r]['CampaignID']}, {$data[$r]['AdgroupID']}, '{$data[$r]['Adgroup']}', '{$Adgroupstate}', 'VIDEO_STANDARD', NOW() ) 
            ON DUPLICATE KEY 
            UPDATE campaignId  = {$data[$r]['CampaignID']}, id = {$data[$r]['AdgroupID']}, name = '{$data[$r]['Adgroup']}', status = '{$Adgroupstate}', adGroupType = 'VIDEO_STANDARD', update_time = NOW(); ";

            # Ad
            $aw_ad = "INSERT INTO aw_ad ( adgroupId, id, name, status, adType, create_time ) 
            VALUES ( {$data[$r]['AdgroupID']}, {$data[$r]['AdID']}, '{$adName}', '{$Adstate}', 'VIDEO_AD', NOW() ) 
            ON DUPLICATE KEY 
            UPDATE adgroupId  = {$data[$r]['AdgroupID']}, id = {$data[$r]['AdID']}, name = '{$adName}', status = '{$Adstate}', adType = 'VIDEO_AD', update_time = NOW(); ";

            // echo $aw_campaign."<br>";
            // echo $aw_adgroup."<br>";
            // echo $aw_ad."<br>";

            $this->db_query($aw_campaign);
            $this->db_query($aw_adgroup);
            $this->db_query($aw_ad);
        }
    }

    public function getAdName($ad_id){
        $sql = "SELECT name FROM aw_ad WHERE id = '{$ad_id}'";
        $result = $this->db_query($sql);
        $row = $result->fetch_assoc();

        return $row['name'];
    }


    public function db_query($sql, $error=false) {
        if(!$sql) return false;
        $result = null;
        if(preg_match('#^select.*#i', trim($sql)))
            $this->sltDB = $this->db2;
        else
            $this->sltDB = $this->db;
        $this->sltDB->query("BEGIN");
        if($error) 
            $result = $this->sltDB->query($sql) or die("ERROR :".$this->sltDB->error.' :'.$sql);
        else
            $result = $this->sltDB->query($sql);
        if($result) {
            //$this->tracking($sql);
        }
        $this->sltDB->query("COMMIT");
        return $result;
    }

    public function tracking($sql) {
        global $member;
        $action = strtoupper(substr($sql, 0, 6));
        $allow = ['INSERT', 'UPDATE'];
        $sql = preg_replace("/\r\n|\t+|\s+/", " ", $sql);
        $table = preg_replace("/{$action}.+(aw_[_a-z]+)\s.+/i", "$1", $sql);
        $result = ['action' => $action, 'table' => $table , 'query' => $this->db->real_escape_string($sql)];
        if(in_array($table, ['fb_optimization', 'fb_optimization_history', 'fb_optimization_onoff_history'])) return;
        // echo '<pre>'.print_r($result,1).'</pre>';
        if(in_array($action, $allow) && $member['mb_id']) {
            switch($action) {
                case "INSERT" :
                    preg_match_all("/^.+\(([a-z,\s\_]*[^\)]+)\).+\(([a-z,\s\_]*[^\)]+)\).+/", $sql, $matches);
                    if(@count($matches[1])>0) {
                        $m['fields'] = array_map('trim', explode(",", $matches[1][0]));
                        $m['values'] = array_map('trim', explode(",", $matches[2][0]));
                        $combine = array_combine($m['fields'], $m['values']);
                        if (($key = array_search("NOW(", $combine)) !== false) unset($combine[$key]);
                        $result['data'] = array_map(function($v){return trim($v, "'");}, $combine);
                    }
                    break;
                case "UPDATE" :
                    $m['where'] = preg_replace("/.+WHERE (.+[^;]+);?$/i", "$1", $sql);
                    $set = preg_replace("/.+SET\s(.+)WHERE.+/i", "$1", $sql);
                    $set = array_map('trim', explode(",", $set));
                    foreach($set as $k => $v) {
                        if(!preg_match('/\=/', $v)) {
                            $set[$k-1] .= ','.$v;
                            unset($set[$k]);
                        }
                    }
                    foreach($set as $row) {
                        list($m['fields'][], $m['values'][]) = array_map('trim', explode("=", $row));
                    }
                    list($m['fields'][], $m['values'][]) = array_map('trim', explode("=", $m['where']));
                    $combine = array_combine($m['fields'], $m['values']);
                    if (($key = array_search("NOW()", $combine)) !== false) unset($combine[$key]);
                    $result['data'] = array_map(function($v){return trim($v, "'");}, $combine);
                    break;
                case "DELETE" :
                case "SELECT" :
                default :
                    break;
            }
            $sql = "INSERT INTO tracking_logs SET ";
            $sql .= "action = '{$result['action']}', table_name = '{$result['table']}', query = '{$result['query']}', mb_id = '{$member['mb_id']}', reg_time = NOW()";
            foreach($result['data'] as $field => $v) {
                @$this->db->query("ALTER TABLE `tracking_logs` ADD `{$field}` VARCHAR(255) NULL DEFAULT NULL AFTER `query`;");
                $sql .= ", {$field} = '{$v}'";
            }
            $this->db->query($sql) or die($this->db->error);
        }
        //echo '<br>'.PHP_EOL;
    }

    public function getMemo($p) {
        $sql = "SELECT * FROM aw_memo WHERE id = '{$p['id']}' AND type = '{$p['type']}' ORDER BY datetime DESC";
        $result = $this->db_query($sql);
        if($result->num_rows) {
            while($row = $result->fetch_assoc()) {
                $sql = "SELECT mb_name FROM g5_member WHERE mb_id = '{$row['mb_id']}'";
                $mb = $this->g5db->query($sql)->fetch_assoc();
                $row['mb_name'] = $mb['mb_name'];
                $memo[] = $row;
            }
        }
        return $memo;
    }

    public function addMemo($data) {
        $data['memo'] = $this->db->real_escape_string($data['memo']);
        $sql = "INSERT INTO aw_memo (`id`, `type`, `memo`, `mb_id`, `datetime`) VALUES({$data['id']}, '{$data['type']}', '{$data['memo']}', '{$data['mb_id']}', NOW())";
        if($this->db_query($sql))
            return $data['id'];
    }
}