<?php
class KMDB
{
    private $db, $db2, $zenith;
    private $sltDB;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect('kakao');
        $this->db2 = \Config\Database::connect('ro_kakao');
        $this->zenith = \Config\Database::connect();
        //      $this->db_query("SET FOREIGN_KEY_CHECKS = 0;");
    }

    public function update_token($data)
    {

        $update_sql = '';
        $access_token = '';
        $refresh_token = '';
        if (isset($data['access_token'])) {
            $access_token = $data['access_token'];
            $update_sql .= "access_token = '{$access_token}', ";
        }
        if (isset($data['refresh_token'])) {
            $refresh_token = $data['refresh_token'];
            $update_sql .= "refresh_token = '{$refresh_token}', ";
        }
        $sql = "INSERT INTO api_info (uid, access_token, refresh_token, expires_time, update_time)
                VALUES (0, '{$access_token}', '{$refresh_token}', DATE_ADD(NOW(), INTERVAL {$data['expires_in']} SECOND), NOW())
                ON DUPLICATE KEY
                UPDATE {$update_sql}expires_time = DATE_ADD(NOW(), INTERVAL {$data['expires_in']} SECOND), update_time = NOW();";
        $result = $this->db_query($sql);
    }

    public function get_token()
    {
        $sql = "SELECT access_token, refresh_token, expires_time FROM api_info WHERE uid = 0";
        $result = $this->db_query($sql) or die($this->db->error);
        $row = $result->getRowArray();

        return $row;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
    public function getAdAccounts($config = 'ON', $is_update = 1, $order = false)
    {
        $sql = "SELECT * FROM mm_ad_account WHERE 1";

        if (!is_null($is_update))
            $sql .= " AND is_update = {$is_update}";
        if (!is_null($config))
            $sql .= " AND config = '{$config}'";
        if ($order) $sql .= " " . $order;
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function updateAdAccount($row)
    {
        foreach ($row as $k => $v) $row[$k] = !is_array($v) ? $this->db->escape($v) : $v;
        if (is_array($row['ownerCompany'])) {
            $ownerCompany = $this->db->escape($row['ownerCompany']['name'] . "(" . $row['ownerCompany']['businessRegistrationNumber'] . ")");
        }
        if (is_array($row['advertiser'])) {
            $advertiser = $this->db->escape($row['advertiser']['name'] . "(" . $row['advertiser']['businessRegistrationNumber'] . ")");
        }
        $sql = "INSERT INTO mm_ad_account (id, name, memberType, config, ownerCompany, advertiser, type, isAdminStop, isOutOfBalance, statusDescription, create_time)
                VALUES ({$row['id']}, {$row['name']}, {$row['memberType']}, {$row['config']}, {$ownerCompany}, {$advertiser}, {$row['type']}, {$row['isAdminStop']}, {$row['isOutOfBalance']}, {$row['statusDescription']}, NOW())
                ON DUPLICATE KEY
                UPDATE name={$row['name']}, memberType={$row['memberType']}, config={$row['config']}, ownerCompany={$ownerCompany}, advertiser={$advertiser}, type={$row['type']}, isAdminStop={$row['isAdminStop']}, isOutOfBalance={$row['isOutOfBalance']}, statusDescription={$row['statusDescription']}, update_time=NOW();";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function updateAdAccounts($data)
    { //광고계정 목록 저장

        foreach ($data as $row) {
            if ($row['id']) $this->updateAdAccount($row);
        }
    }
     
    public function getCampaigns($config = ['ON'])
    {
        $sql = "SELECT A.* FROM mm_campaign AS A LEFT JOIN mm_ad_account AS B ON A.ad_account_id = B.id WHERE B.is_update = 1";
        if (!is_null($config))
            $sql .= " AND A.config IN ('" . implode("','", $config) . "')";
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function 
    getAutoLimitBudgetCampaign($data)
    {
        $sql = "SELECT A.id AS id, A.name AS name, A.config AS config, IF(F.campaign_id,true,false) AS already, A.autoBudget AS autoBudget, SUM( D.cost ) AS cost, SUM(E.db_count) AS unique_total, A.dailyBudgetAmount AS dailyBudgetAmount
                FROM mm_campaign A
                    LEFT JOIN mm_adgroup B ON A.id = B.campaign_id
                    LEFT JOIN mm_creative C ON B.id = C.adgroup_id
                    LEFT JOIN mm_creative_report_basic D ON C.id = D.id
                    LEFT JOIN mm_db_count E ON C.id = E.creative_id AND E.date = D.date
                    LEFT JOIN mm_campaign_autobudget F ON A.id = F.campaign_id AND D.date = F.date
                WHERE A.config = 'ON' AND A.autoBudget = 'ON' AND A.name LIKE '%@%' AND D.date = '{$data['date']}'
                GROUP BY A.id";
        $result = $this->db_query($sql);

        return $result;
    }

     
    public function setAutoLimitBudgetCampaign($data)
    {
        $sql = "INSERT INTO mm_campaign_autobudget(campaign_id, date, set_db, valid_db, set_budget, reg_date) VALUES('{$data['id']}', '{$data['date']}', '{$data['set_db']}', '{$data['valid_db']}', '{$data['budget']}', NOW()) ";
        $result = $this->db_query($sql, true);
    }
     
    public function getCampaignById($id)
    {
        if (is_null($id)) return NULL;
        $sql = "SELECT * FROM mm_campaign WHERE id = {$id}";
        $result = $this->db_query($sql);
        $row = $result->getResult();
        return $row;
    }
     
    public function updateCampaign($row)
    {
        foreach ($row as $k => $v) $row[$k] = !is_array($v) ? $this->db->escape($v) : $v;
        $sql = "INSERT INTO mm_campaign (ad_account_id, id, name, type, goal, config, objectiveType, objectiveDetailType, objectiveValue, dailyBudgetAmount, statusDescription, trackId, create_time)
                VALUES ({$row['adAccountId']}, {$row['id']}, {$row['name']}, '{$row['campaignTypeGoal']['campaignType']}', '{$row['campaignTypeGoal']['goal']}', {$row['config']}, '" . @$row['objective']['type'] . "', '" . @$row['objective']['detailType'] . "', '" . @$row['objective']['value'] . "', {$row['dailyBudgetAmount']}, {$row['statusDescription']}, {$row['trackId']}, NOW())
                ON DUPLICATE KEY
                UPDATE name={$row['name']}, type='{$row['campaignTypeGoal']['campaignType']}', goal='{$row['campaignTypeGoal']['goal']}', config={$row['config']}, objectiveType='" . @$row['objective']['type'] . "', objectiveDetailType='" . @$row['objective']['detailType'] . "', objectiveValue='" . @$row['objective']['value'] . "', dailyBudgetAmount={$row['dailyBudgetAmount']}, statusDescription={$row['statusDescription']}, trackId={$row['trackId']}, update_time=NOW();";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }

     
    public function setCampaignDailyBudgetAmount($id, $budget)
    {
        if ($budget == '') $budget = NULL;
        $sql = "UPDATE mm_campaign SET dailyBudgetAmount = '{$budget}' WHERE id = {$id}";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function setCampaign($data)
    {
        foreach ($data as $key => $val) $q[] = "{$key} = '{$val}'";
        $query = implode(",", $q);
        $sql = "UPDATE mm_campaign SET {$query} WHERE id = {$data['id']}";
        $result = $this->db_query($sql);
        return $result;
    }
     
    public function updateCampaigns($data)
    { //캠페인 목록 저장

        foreach ($data as $account_id => $account) {
            foreach ($account as $row) {
                $row['account_id'] = $account_id;
                if ($row['id']) $this->updateCampaign($row);
            }
        }
    }
     
    public function getAdGroups($config = null, $orderby = "")
    {
        $sql = "SELECT A.*, B.dailyBudgetAmount AS campaign_dailyBudgetAmount, B.ad_account_id, C.name AS account_name
                FROM mm_adgroup AS A
                    LEFT JOIN mm_campaign AS B
                        ON A.campaign_id = B.id
                    LEFT JOIN mm_ad_account AS C
                        ON B.ad_account_id = C.id
                WHERE C.is_update = 1";
        if (!is_null($config))
            $sql .= " AND A.config IN ('" . implode("','", $config) . "')";
        if ($orderby) $sql .= " " . $orderby;
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function getAdAccountIdByAdGroupId($id)
    {
        if (is_null($id)) return NULL;
        $sql = "SELECT C.ad_account_id FROM mm_adgroup AS B
                    LEFT JOIN mm_campaign AS C
                        ON B.campaign_id = C.id
                    LEFT JOIN mm_ad_account AS D
                        ON C.ad_account_id = D.id
                WHERE B.id = {$id}";
        $result = $this->db_query($sql);
        $row = $result->getResult();
        $ad_account_id = $row['ad_account_id'];
        return $ad_account_id;
    }
     
    public function updateAdGroup($row)
    {
        foreach ($row as $k => $v) $row[$k] = !is_array($v) ? $this->db->escape($v) : $v;
        $sql = "INSERT INTO mm_adgroup (campaign_id, id, name, type, config, allAvailableDeviceType, allAvailablePlacement, pricingType, pacing, adult, bidStrategy, totalBudget, dailyBudgetAmount, bidAmount, useMaxAutoBidAmount, autoMaxBidAmount, isDailyBudgetAmountOver, creativeOptimization, isValidPeriod, deviceTypes, placements, statusDescription, create_time)
                VALUES(
                {$row['campaign_id']}, {$row['id']}, {$row['name']}, {$row['type']}, {$row['config']}, {$row['allAvailableDeviceType']}, {$row['allAvailablePlacement']}, {$row['pricingType']}, {$row['pacing']}, {$row['adult']}, {$row['bidStrategy']}, {$row['totalBudget']}, {$row['dailyBudgetAmount']}, {$row['bidAmount']}, {$row['useMaxAutoBidAmount']}, {$row['autoMaxBidAmount']}, {$row['isDailyBudgetAmountOver']}, {$row['creativeOptimization']}, {$row['isValidPeriod']}, '" . @implode(',', $row['deviceTypes']) . "', '" . @implode(',', $row['placements']) . "', {$row['statusDescription']}, NOW()) 
                ON DUPLICATE KEY 
                UPDATE campaign_id = {$row['campaign_id']}, name = {$row['name']}, type = {$row['type']}, config = {$row['config']}, allAvailableDeviceType = {$row['allAvailableDeviceType']}, allAvailablePlacement = {$row['allAvailablePlacement']}, pricingType = {$row['pricingType']}, pacing = {$row['pacing']}, adult = {$row['adult']}, bidStrategy = {$row['bidStrategy']}, totalBudget = {$row['totalBudget']}, dailyBudgetAmount = {$row['dailyBudgetAmount']}, bidAmount = {$row['bidAmount']}, useMaxAutoBidAmount = {$row['useMaxAutoBidAmount']}, autoMaxBidAmount = {$row['autoMaxBidAmount']}, isDailyBudgetAmountOver = {$row['isDailyBudgetAmountOver']}, creativeOptimization = {$row['creativeOptimization']}, isValidPeriod = {$row['isValidPeriod']}, deviceTypes = '" . @implode(',', $row['deviceTypes']) . "', placements = '" . @implode(',', $row['placements']) . "', statusDescription = {$row['statusDescription']}, update_time=NOW();";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        // echo "<p>{$sql}</p>";
        return $result;
    }
     
    public function setAdGroupOnOff($adgroupId, $config)
    {
        $sql = "UPDATE mm_adgroup SET config = '{$config}' WHERE id = {$adgroupId}";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }

     
    public function setAdGroupDailyBudgetAmount($id, $budget)
    {
        $sql = "UPDATE mm_adgroup SET dailyBudgetAmount = '{$budget}' WHERE id = {$id}";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function setAdGroupBidAmount($adgroupId, $bidAmount)
    {
        $sql = "UPDATE mm_adgroup SET bidAmount = {$bidAmount} WHERE id = {$adgroupId}";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function setAdGroup($data)
    {
        foreach ($data as $key => $val) $q[] = "{$key} = '{$val}'";
        $query = implode(",", $q);
        $sql = "UPDATE mm_adgroup SET {$query} WHERE id = {$data['id']}";
        $result = $this->db_query($sql);
        return $result;
    }
     
    public function updateAdGroups($data)
    { //광고그룹 목록 저장
        if(isset($data) && count($data)) {
            foreach ($data as $campaign_id => $campaign) {
                foreach ($campaign as $row) {
                    $row['campaign_id'] = $campaign_id;
                    if (isset($row['id'])) $this->updateAdGroup($row);
                    else print_r($row);
                }
            }
        }
    }

     
    public function getAutoLimitBidAmountAdGroup($data)
    {
        $sql = "SELECT A.ad_account_id, 
                    B.id AS id, 
                    B.name AS name, 
                    B.bidStrategy, 
                    C.name AS ad_name, 
                    A.goal, 
                    A.config AS campaign_config, 
                    B.config AS config, 
                    B.aiConfig, 
                    SUM(E.db_count) AS unique_total, 
                    B.bidAmount, 
                    SUM(D.cost) AS cost, 
                    SUM(E.margin) AS margin, 
                    IF(SUM(D.sales)>0, ROUND(SUM(E.margin)/SUM(D.sales)*100,0),0) AS margin_ratio, 
                    IF(F.adgroup_id AND level = 1,true,false) AS already_1, 
                    IF(F.adgroup_id AND level = 2,true,false) AS already_2, 
                    IF(F.adgroup_id AND level = 3,true,false) AS already_3, 
                    IF(F.adgroup_id AND level = 4,true,false) AS already_4
                FROM mm_adgroup B
                LEFT JOIN mm_campaign A ON A.id = B.campaign_id
                LEFT JOIN mm_creative C ON B.id = C.adgroup_id
                LEFT JOIN mm_creative_report_basic D ON C.id = D.id
                LEFT JOIN mm_db_count E ON C.id = E.creative_id AND E.date = D.date
                LEFT JOIN (SELECT H.* 
                            FROM (SELECT adgroup_id, MAX( reg_date ) reg_date FROM mm_adgroup_autobidamount GROUP BY adgroup_id) G
                            JOIN mm_adgroup_autobidamount H ON H.adgroup_id = G.adgroup_id AND H.reg_date = G.reg_date ) F 
                    ON B.id = F.adgroup_id AND E.date = F.date 
                WHERE B.aiConfig = 'ON' AND D.date = '{$data['date']}' AND A.goal IN ('CONVERSION', 'VISITING')
                GROUP BY B.id ";
        $result = $this->db_query($sql, true);
        return $result;
    }
     
    public function setAutoLimitBidAmountAdGroup($data)
    {
        $sql = "INSERT INTO mm_adgroup_autobidamount(adgroup_id, date, level, msg, reg_date) VALUES('{$data['id']}', '{$data['date']}', '{$data['level']}', '{$data['msg']}', NOW()) ";
        echo $sql . PHP_EOL;
        $result = $this->db_query($sql, true);
        return $result;
    }
     
    public function getCreatives($config = ['ON'])
    {
        $sql = "SELECT A.*, C.ad_account_id FROM mm_creative AS A
                    LEFT JOIN mm_adgroup AS B
                        ON A.adgroup_id = B.id
                    LEFT JOIN mm_campaign AS C
                        ON B.campaign_id = C.id
                    LEFT JOIN mm_ad_account AS D
                        ON C.ad_account_id = D.id
                WHERE D.is_update = 1";
        if (!is_null($config))
            $sql .= " AND A.config IN ('" . implode("','", $config) . "')";
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function getAdAccountIdByCreativeId($id)
    {
        if (is_null($id)) return NULL;
        $sql = "SELECT C.ad_account_id FROM mm_creative AS A
                    LEFT JOIN mm_adgroup AS B
                        ON A.adgroup_id = B.id
                    LEFT JOIN mm_campaign AS C
                        ON B.campaign_id = C.id
                    LEFT JOIN mm_ad_account AS D
                        ON C.ad_account_id = D.id
                WHERE A.id = {$id}";
        $result = $this->db_query($sql);
        $row = $result->getRowArray();
        $ad_account_id = $row['ad_account_id'];
        return $ad_account_id;
    }

     
    public function updateCreative($row)
    {
        foreach ($row as $k => $v) $row[$k] = !is_array($v) ? $this->db->escape($v) : $v;
        $bizFormQuery = "";
        if(isset($row['landingInfo']['bizFormId']))
            $bizFormQuery = ", bizFormId = '" . @$row['landingInfo']['bizFormId'] . "'";
        $sql = "INSERT INTO mm_creative (adgroup_id, id, creativeId, name, altText, type, landingType, hasExpandable, bizFormId, format, bidAmount, landingUrl, frequencyCap, frequencyCapType, config, imageUrl, reviewStatus, creativeStatus, statusDescription, create_time)
                VALUES ({$row['adgroup_id']}, {$row['id']}, {$row['creativeId']}, {$row['name']}, {$row['altText']}, {$row['type']}, '" . @$row['landingInfo']['landingType'] . "', {$row['hasExpandable']}, '" . @$row['landingInfo']['bizFormId'] . "', {$row['format']}, {$row['bidAmount']}, {$row['landingUrl']}, {$row['frequencyCap']}, {$row['frequencyCapType']}, {$row['config']}, '" . @$row['image']['url'] . "', {$row['reviewStatus']}, {$row['creativeStatus']}, {$row['statusDescription']}, NOW())
                ON DUPLICATE KEY
                UPDATE adgroup_id = {$row['adgroup_id']}, creativeId = {$row['creativeId']}, name = {$row['name']}, altText = {$row['altText']}, type = {$row['type']}, landingType = '" . @$row['landingInfo']['landingType'] . "', hasExpandable = {$row['hasExpandable']}{$bizFormQuery}, format = {$row['format']}, bidAmount = {$row['bidAmount']}, landingUrl = {$row['landingUrl']}, frequencyCap = {$row['frequencyCap']}, frequencyCapType = {$row['frequencyCapType']}, config = {$row['config']}, imageUrl = '" . @$row['image']['url'] . "', reviewStatus = {$row['reviewStatus']}, creativeStatus = {$row['creativeStatus']}, statusDescription = {$row['statusDescription']}, update_time = NOW();";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        // echo "<p>{$sql}</p>";
        return $result;
    }
     
    public function setCreativeOnOff($creativeId, $config)
    {
        $sql = "UPDATE mm_creative SET config = '{$config}' WHERE id = {$creativeId}";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function insertCreativeAutoOnOff($creative, $set) {
        $sql = "INSERT INTO mm_creative_autoonoff(creative_id, date, type, msg, reg_date) VALUES({$creative['id']}, NOW(), '{$creative['aitype']}', '{$set}', NOW());";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }

     
    public function getAutoCreativeOnOff($type) {
        $sql = "SELECT maa.name AS account_name, maa.config AS account_config, mcm.name AS campaign_name, mcm.config AS campaign_config, ma.config AS adgroup_config, mc.id AS id, mc.name AS creative_name, mc.config AS creative_config, mcrb.date, mdc.db_price, mdc.db_count, mdc.margin, mcrb.cost, mcrb.sales, ROUND(mdc.margin/mcrb.sales*100,2) AS margin_ratio, (CASE WHEN mdc.db_count = 0 AND mdc.db_price <= mcrb.cost THEN '1' WHEN mdc.db_count >= 1 AND mdc.margin/mcrb.sales*100 <= 0 THEN '2' END) AS aitype
                    FROM mm_creative AS mc 
                    LEFT JOIN mm_creative_report_basic AS mcrb ON mc.id = mcrb.id 
                    LEFT JOIN mm_db_count AS mdc ON mc.id = mdc.creative_id
                    LEFT JOIN mm_adgroup AS ma ON ma.id = mc.adgroup_id 
                    LEFT JOIN mm_campaign AS mcm ON mcm.id = ma.campaign_id
                    LEFT JOIN mm_ad_account AS maa ON maa.id = mcm.ad_account_id 
                WHERE mcrb.date = mdc.date AND mdc.date = DATE(NOW()) AND mdc.margin != 0 AND ma.config = 'ON' AND mcm.config = 'ON' AND maa.config = 'ON' AND mc.config = '".strtoupper($type)."' AND mc.aiConfig = 'ON'
                GROUP BY mc.id";
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }

     
    public function setCreative($data)
    {
        foreach ($data as $key => $val) $q[] = "{$key} = '{$val}'";
        $query = implode(",", $q);
        $sql = "UPDATE mm_creative SET {$query} WHERE id = {$data['id']}";
        $result = $this->db_query($sql);
        return $result;
    }
     
    public function updateCreatives($data)
    { //소재 목록 저장
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $adgroup_id => $adgroup) {
                foreach ($adgroup as $row) {
                    $row['adgroup_id'] = $adgroup_id;
                    if ($row['id']) $this->updateCreative($row);
                }
            }
        }
    }
     
    public function getInitCreatives() {
        $sql = "SELECT mcrb.date AS report_date, mcrb.update_time AS report_update_time, maa.name AS account_name, mc2.id AS campaign_id, mc2.name AS campaign_name, mc2.type AS campaign_type, ma.id AS adgroup_id, ma.name AS adgroup_name, mc.id AS creative_id, mc.name AS creative_name, mc.create_time AS creative_create_time
                FROM mm_creative_report_basic AS mcrb
                    LEFT JOIN mm_creative AS mc ON mc.id = mcrb.id
                    LEFT JOIN mm_adgroup AS ma ON ma.id = mc.adgroup_id
                    LEFT JOIN mm_campaign AS mc2 ON mc2.id = ma.campaign_id 
                    LEFT JOIN mm_ad_account AS maa ON maa.id = mc2.ad_account_id 
                WHERE maa.is_update = 1 AND mc2.type IN ('TALK_BIZ_BOARD', 'DISPLAY') AND mc.config = 'ON' AND maa.config = 'ON' AND mc2.config = 'ON' AND DATE(mc.create_time) = DATE(NOW())";
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function getCreativeReportBasic($query = "")
    {
        $sql = "SELECT B.id AS adgroup_id, report.id, report.date, report.imp, report.imp, report.click, report.cost, creative.name, creative.landingUrl
                    FROM mm_creative_report_basic AS report
                        LEFT JOIN mm_creative AS creative
                            ON report.id = creative.id
                        LEFT JOIN mm_adgroup AS B
                            ON creative.adgroup_id = B.id
                        LEFT JOIN mm_campaign AS C
                            ON B.campaign_id = C.id
                        LEFT JOIN mm_ad_account AS D
                            ON C.ad_account_id = D.id
                    WHERE D.is_update = 1 {$query}";
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function updateCreativeReportBasic($row)
    {
        foreach ($row as $k => $v) $row[$k] = !is_array($v) ? $this->db->escape($v) : $v;
        if($row['imp'] == 0 && $row['click'] == 0 && $row['cost'] == 0 && $row['ctr'] == 0) return;
        $sql = "INSERT INTO mm_creative_report_basic (id, date, hour, imp, click, ctr, cost, create_time)
                VALUES ({$row['creative_id']}, {$row['date']}, IF({$row['hour']} <> '', {$row['hour']}, IF(DATE(NOW()) = {$row['date']}, HOUR(NOW()), 23)), {$row['imp']}, {$row['click']}, {$row['ctr']}, {$row['cost']}, NOW())
                ON DUPLICATE KEY
                UPDATE date={$row['date']}, imp={$row['imp']}, click={$row['click']}, ctr={$row['ctr']}, cost={$row['cost']}, update_time=NOW();";
        // echo $sql.'<br>';
        $result = $this->db_query($sql) or die($sql . ' : ' . $this->db->error);
        return $result;
    }
     
    public function updateCreativesReportBasic($data)
    { //소재 목록 저장
        if ($data) {
            foreach ($data as $creative_id => $creative) {
                foreach ($creative as $row) {
                    $row['creative_id'] = $creative_id;
                    if ($row['imp'] >= 0) $this->updateCreativeReportBasic($row);
                }
            }
        }
    }
     
    public function getAppSubscribe($data)
    {
        if (!$data['event_seq']) return null;
        $sql = "SELECT event_seq, site, date(from_unixtime(reg_timestamp)) AS date, HOUR(from_unixtime(reg_timestamp)) AS hour, count(event_seq) AS db_count
                FROM `zenith`.`event_leads`
                WHERE `reg_timestamp` >= unix_timestamp('{$data['date']}')
                AND `status` = 1 AND `is_deleted` = 0
                AND `event_seq` = {$data['event_seq']} AND `site` = '{$data['site']}' AND DATE_FORMAT(`reg_date`, '%Y-%m-%d') = '{$data['date']}'
                GROUP BY `event_seq`, `site`, HOUR(from_unixtime(reg_timestamp))";
                // echo $sql.PHP_EOL;
        $result = $this->zenith->query($sql);
        return $result;
    }
     
    public function allAiOn($data) {
        $sql = "UPDATE mm_campaign SET autoBudget = 'ON' WHERE id = {$data['campaign_id']} AND autoBudget = 'OFF'";
        $result = $this->db_query($sql);
        $sql = "UPDATE mm_adgroup SET aiConfig = 'ON', aiConfig2 = 'ON' WHERE id = {$data['adgroup_id']} AND aiConfig = 'OFF' AND aiConfig2 = 'OFF'";
        $result = $this->db_query($sql);
    }

    public function getAdLeads($date)
    {
        $sql = "SELECT report.id, CONCAT('{',GROUP_CONCAT('\"',report.`hour`,'\":',report.cost),'}') AS cost_data, creative.name, creative.landingUrl
                FROM mm_creative_report_basic AS report
                        LEFT JOIN mm_creative AS creative
                            ON report.id = creative.id
                        LEFT JOIN mm_adgroup AS B
                            ON creative.adgroup_id = B.id
                        LEFT JOIN mm_campaign AS C
                            ON B.campaign_id = C.id
                        LEFT JOIN mm_ad_account AS D
                            ON C.ad_account_id = D.id
                    WHERE D.is_update = 1 AND report.date = '{$date}' GROUP BY report.id";
        $result = $this->db_query($sql);

        return $result;
    }

    public function updateReport($data)
    {
        $row = $data;
        foreach($row['data'] as $v) {
            if ($row['creative_id']) {
                $sql = "UPDATE `z_moment`.`mm_creative_report_basic` 
                SET `media` = '{$row['media']}', `period` = '{$row['period_ad']}', `event_seq` = '{$row['event_seq']}', `site` = '{$row['site']}', `db_price` = '{$row['db_price']}', `db_count` = '{$v['count']}', `margin` = '{$v['margin']}', `sales` = '{$v['sales']}', `update_time` = NOW()
                WHERE `id` = '{$row['creative_id']}' AND `date` = '{$row['date']}' AND `hour` = '{$v['hour']}'";
                $this->db_query($sql, true);
            }
        }
    }
     
    public function getDbPrice($data)
    {
        if (!$data['creative_id'] || !$data['date']) return NULL;
        $sql = "SELECT id, date, db_price FROM `z_moment`.`mm_creative_report_basic` WHERE `id` = '{$data['creative_id']}' AND `date` = '{$data['date']}' GROUP BY date ORDER BY hour DESC LIMIT 1;";
        $result = $this->db_query($sql);
        if (!$result) return null;
        return $result->getResultArray();
    }
     
    public function insertToSubscribe($row)
    {
        $sql = "INSERT INTO app_subscribe(group_id, event_seq, site, name, email, gender, age, phone, add1, add2, add3, add4, add5, add6, addr, reg_date, deleted, fb_ad_lead_id, enc_status)
                VALUES('{$row['group_id']}', '{$row['event_id']}', '{$row['site']}', '{$row['full_name']}', '{$row['email']}', '{$row['gender']}', '{$row['age']}', ENC_DATA('{$row['phone']}'), '{$row['add1']}', '{$row['add2']}', '{$row['add3']}', '{$row['add4']}', '{$row['add5']}', '{$row['add6']}', '{$row['addr']}', '{$row['reg_date']}', 0, '{$row['ad_id']}', 1)
                ON DUPLICATE KEY
                UPDATE group_id='{$row['group_id']}', event_seq='{$row['event_id']}', site='{$row['site']}', name='{$row['full_name']}', email='{$row['email']}', gender='{$row['gender']}', age='{$row['age']}', phone=ENC_DATA('{$row['phone']}'), add1='{$row['add1']}', add2='{$row['add2']}', add3='{$row['add3']}', add4='{$row['add4']}', add5='{$row['add5']}', add6='{$row['add6']}', addr='{$row['addr']}', reg_date='{$row['reg_date']}', fb_ad_lead_id='{$row['ad_id']}'";
        $result = $this->zenith->query($sql, true);
        if ($result) {
            $sql = "update mm_bizform_user_response set send_time=now() where encUserId='{$row['encUserId']}' and bizFormId='{$row['bizFormId']}'";
            $result = $this->db_query($sql);
        } else {
            echo $this->zenith->error;
        }
    }
     
    public function getBizformQuestion($bizformId, $itemId)
    {
        $sql = "SELECT bizform_id, id, title FROM mm_bizform_items WHERE bizform_id = '{$bizformId}' AND id = '{$itemId}'";
        $result = $this->db_query($sql);
        if (!$result->getNumRow()) return null;
        return $result->getResultArray();
    }
     
    public function getBizformUserResponse()
    {
        $sql = "SELECT ur.*, mc.name, mc.id
                FROM mm_creative AS mc
                JOIN mm_bizform_user_response AS ur ON mc.id = ur.creative_id
                WHERE ur.send_time IS NULL
                ORDER BY ur.create_time ASC";
        $result = $this->db_query($sql);

        return $result;
    }
     
    public function getBizformUpdateList()
    {
        $sql = "SELECT ei.creative_id, ei.bizform_apikey, mc.id, mc.bizFormId, mcp.name, ma.name, mc.name
        FROM chainsaw_old.event_information AS ei
            LEFT JOIN `z_moment`.mm_creative AS mc ON ei.creative_id = mc.creativeId
            LEFT JOIN `z_moment`.mm_adgroup AS ma ON ma.id = mc.adgroup_id
            LEFT JOIN `z_moment`.mm_campaign AS mcp ON mcp.id = ma.campaign_id 
            LEFT JOIN `z_moment`.mm_ad_account AS maa ON mcp.ad_account_id = maa.id
        WHERE ei.creative_id <> '' AND ei.bizform_apikey <> '' AND ei.is_stop = 0
        AND mc.config = 'ON' AND maa.config = 'ON' AND maa.is_update = 1";
        $result = $this->db_query($sql);
        if (!$result->getNumRows()) return NULL;
        $data = [];
        foreach ($result->getResultArray() as $row) {
            $data[] = [
                'id' => $row['id'],
                'bizFormId' => $row['bizFormId'],
                'bizFormApiKey' => $row['bizform_apikey']
            ];
        }
        return $data;
    }
     
    public function updateBizform($data)
    {
        $row = $data['data'];
        if(!$row['id']) return;
        $sql = "INSERT INTO mm_bizform(`id`, `title`, `imgUrl`, `startAt`, `startTimeAt`, `endAt`, `endTimeAt`, `applyType`, `privacyScopeUse`, `flowType`, `completeType`, `status`, `runningStatus`, `editingPhase`, `channelUsed`, `channelProfileId`, `prizeAnnouncedDateAt`, `prizeAnnouncedTimeAt`, `partnerCsPhone`, `partnerCsUrl`, `reportEnable`, `createdAt`, `abortedAt`, `finishUv`, `create_time`)
        VALUES('{$row['id']}', '{$row['title']}', '{$row['imgUrl']}', '{$row['startAt']}', '{$row['startTimeAt']}', '{$row['endAt']}', '{$row['endTimeAt']}', '{$row['applyType']}', '{$row['privacyScopeUse']}', '{$row['flowType']}', '{$row['completeType']}', '{$row['status']}', '{$row['runningStatus']}', '{$row['editingPhase']}', '{$row['channelUsed']}', '{$row['channelProfileId']}', '{$row['prizeAnnouncedDateAt']}', '{$row['prizeAnnouncedTimeAt']}', '{$row['partnerCsPhone']}', '{$row['partnerCsUrl']}', '{$row['reportEnable']}', '{$row['createdAt']}', '{$row['abortedAt']}', '{$row['finishUv']}', NOW())
        ON DUPLICATE KEY
        UPDATE `id` = '{$row['id']}', `title` = '{$row['title']}', `imgUrl` = '{$row['imgUrl']}', `startAt` = '{$row['startAt']}', `startTimeAt` = '{$row['startTimeAt']}', `endAt` = '{$row['endAt']}', `endTimeAt` = '{$row['endTimeAt']}', `applyType` = '{$row['applyType']}', `privacyScopeUse` = '{$row['privacyScopeUse']}', `flowType` = '{$row['flowType']}', `completeType` = '{$row['completeType']}', `status` = '{$row['status']}', `runningStatus` = '{$row['runningStatus']}', `editingPhase` = '{$row['editingPhase']}', `channelUsed` = '{$row['channelUsed']}', `channelProfileId` = '{$row['channelProfileId']}', `prizeAnnouncedDateAt` = '{$row['prizeAnnouncedDateAt']}', `prizeAnnouncedTimeAt` = '{$row['prizeAnnouncedTimeAt']}', `partnerCsPhone` = '{$row['partnerCsPhone']}', `partnerCsUrl` = '{$row['partnerCsUrl']}', `reportEnable` = '{$row['reportEnable']}', `createdAt` = '{$row['createdAt']}', `abortedAt` = '{$row['abortedAt']}', `finishUv` = '{$row['finishUv']}', `update_time` = NOW()";
        if ($this->db_query($sql, true)) {
            $this->updateBizFormItems($row['id'], $row['bizformItems']);
        }
    }
     
    public function updateBizFormItems($bizformId, $data)
    {
        if(@count($data) <= 0) return;
        foreach ($data as $row) {
            if(!$row['id']) continue;
            $sql = "INSERT INTO mm_bizform_items(`id`, `bizform_id`, `ordinal`, `title`, `contents`, `required`, `type`, `replyType`, `layoutType`, `multiple`, `multipleLimitMin`, `multipleLimitMax`, `stepGroupId`, `stepOrder`, `step`, `bizformOptions`, `create_time`)
            VALUES('{$row['id']}', '{$bizformId}', '{$row['ordinal']}', '{$row['title']}', '{$row['contents']}', '{$row['required']}', '{$row['type']}', '{$row['replyType']}', '{$row['layoutType']}', '{$row['multiple']}', '{$row['multipleLimitMin']}', '{$row['multipleLimitMax']}', '{$row['stepGroupId']}', '{$row['stepOrder']}', '{$row['step']}', '{$row['bizformOptions']}', NOW())
            ON DUPLICATE KEY
            UPDATE `id` = '{$row['id']}', `bizform_id` = '{$bizformId}', `ordinal` = '{$row['ordinal']}', `title` = '{$row['title']}', `contents` = '{$row['contents']}', `required` = '{$row['required']}', `type` = '{$row['type']}', `replyType` = '{$row['replyType']}', `layoutType` = '{$row['layoutType']}', `multiple` = '{$row['multiple']}', `multipleLimitMin` = '{$row['multipleLimitMin']}', `multipleLimitMax` = '{$row['multipleLimitMax']}', `stepGroupId` = '{$row['stepGroupId']}', `stepOrder` = '{$row['stepOrder']}', `step` = '{$row['step']}', `bizformOptions` = '{$row['bizformOptions']}', `update_time` = NOW()";
            $this->db_query($sql, true);
        }
    }
     
    public function updateBizFormUserResponse($creative_id, $bizformId, $data)
    {
        if ($data) {
            foreach ($data as $row) {
                foreach ($row as $k => $v) $row[$k] = @$this->db->escape($v);
                if(!$row['seq']) continue;
                $sql = "INSERT INTO mm_bizform_user_response(`bizFormId`, `creative_id`, `seq`, `encUserId`, `applyOrUpdate`, `submitAt`, `nickname`, `email`, `phoneNumber`, `responses`, `create_time`)
                    VALUES({$bizformId}, {$creative_id}, {$row['seq']}, {$row['encUserId']}, {$row['applyOrUpdate']}, {$row['submitAt']}, {$row['nickname']}, {$row['email']}, {$row['phoneNumber']}, {$row['response']}, NOW())
                    ON DUPLICATE KEY UPDATE 
                    `update_time`= IF(seq<>VALUES(seq) OR applyOrUpdate<>VALUES(applyOrUpdate) OR submitAt<>VALUES(submitAt) OR nickname<>VALUES(nickname) OR email<>VALUES(email) OR phoneNumber<>VALUES(phoneNumber) OR responses<>VALUES(responses), NOW(), NULL),
                    `seq`={$row['seq']},`applyOrUpdate`={$row['applyOrUpdate']},`submitAt`={$row['submitAt']},`nickname`={$row['nickname']},`email`={$row['email']},`phoneNumber`={$row['phoneNumber']},`responses`={$row['response']}";
                /*
                $sql = "INSERT INTO mm_bizform_user_response(`bizFormId`, `creative_id`, `seq`, `encUserId`, `applyOrUpdate`, `submitAt`, `nickname`, `email`, `phoneNumber`, `responses`, `create_time`)
                    VALUES({$bizformId}, {$creative_id}, {$row['seq']}, {$row['encUserId']}, {$row['applyOrUpdate']}, {$row['submitAt']}, {$row['nickname']}, {$row['email']}, {$row['phoneNumber']}, {$row['response']}, NOW())";
                */
                $this->db_query($sql, true);
            }
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
            $result = $this->sltDB->query($sql) or die($this->sltDB->error);
        else
            $result = $this->sltDB->query($sql);
        if ($result) {
            // $this->tracking($sql);
        }
        $this->sltDB->query("COMMIT");
        return $result;
    }

    public function tracking($sql)
    {
        global $member;
        $action = strtoupper(substr($sql, 0, 6));
        $allow = ['INSERT', 'UPDATE'];
        $sql = preg_replace("/\r\n|\t+|\s+/", " ", $sql);
        $table = preg_replace("/{$action}.+(mm_[_a-z]+)\s.+/i", "$1", $sql);
        $result = ['action' => $action, 'table' => $table, 'query' => $this->db->escape($sql)];
        if (in_array($table, ['fb_optimization', 'fb_optimization_history', 'fb_optimization_onoff_history'])) return;
        // echo '<pre>'.print_r($result,1).'</pre>';
        if (in_array($action, $allow) && $member['mb_id']) {
            switch ($action) {
                case "INSERT":
                    preg_match_all("/^.+\(([a-z,\s\_]*[^\)]+)\).+\(([a-z,\s\_]*[^\)]+)\).+/", $sql, $matches);
                    if (@count($matches[1]) > 0) {
                        $m['fields'] = array_map('trim', explode(",", $matches[1][0]));
                        $m['values'] = array_map('trim', explode(",", $matches[2][0]));
                        $combine = array_combine($m['fields'], $m['values']);
                        if (($key = array_search("NOW(", $combine)) !== false) unset($combine[$key]);
                        $result['data'] = array_map(function ($v) {
                            return trim($v, "'");
                        }, $combine);
                    }
                    break;
                case "UPDATE":
                    $m['where'] = preg_replace("/.+WHERE (.+[^;]+);?$/i", "$1", $sql);
                    $set = preg_replace("/.+SET\s(.+)WHERE.+/i", "$1", $sql);
                    $set = array_map('trim', explode(",", $set));
                    foreach ($set as $k => $v) {
                        if (!preg_match('/\=/', $v)) {
                            $set[$k - 1] .= ',' . $v;
                            unset($set[$k]);
                        }
                    }
                    foreach ($set as $row) {
                        list($m['fields'][], $m['values'][]) = array_map('trim', explode("=", $row));
                    }
                    list($m['fields'][], $m['values'][]) = array_map('trim', explode("=", $m['where']));
                    $combine = array_combine($m['fields'], $m['values']);
                    if (($key = array_search("NOW()", $combine)) !== false) unset($combine[$key]);
                    $result['data'] = array_map(function ($v) {
                        return trim($v, "'");
                    }, $combine);
                    break;
                case "DELETE":
                case "SELECT":
                default:
                    break;
            }
            $sql = "INSERT INTO tracking_logs SET ";
            $sql .= "action = '{$result['action']}', table_name = '{$result['table']}', query = '{$result['query']}', mb_id = '{$member['mb_id']}', reg_time = NOW()";
            foreach ($result['data'] as $field => $v) {
                @$this->db->query("ALTER TABLE `tracking_logs` ADD `{$field}` VARCHAR(255) NULL DEFAULT NULL AFTER `query`;");
                $sql .= ", {$field} = '{$v}'";
            }
            $this->db->query($sql) or die($this->db->error);
        }
        //echo '<br>'.PHP_EOL;
    }
     
    public function getMemo($p)
    {
        $sql = "SELECT * FROM mm_memo WHERE id = '{$p['id']}' AND type = '{$p['type']}' ORDER BY datetime DESC";
        $result = $this->db_query($sql);
        if ($result->getNumRow()) {
            foreach ($result->getResultArray() as $row) {
                $sql = "SELECT mb_name FROM g5_member WHERE mb_id = '{$row['mb_id']}'";
                $mb = $this->zenith->query($sql)->getResult();
                $row['mb_name'] = $mb['mb_name'];
                $memo[] = $row;
            }
        }
        return $memo;
    }
     
    public function addMemo($data)
    {
        $data['memo'] = $this->db->escape($data['memo']);
        $sql = "INSERT INTO mm_memo (`id`, `type`, `memo`, `mb_id`, `datetime`) VALUES({$data['id']}, '{$data['type']}', '{$data['memo']}', '{$data['mb_id']}', NOW())";
        if ($this->db_query($sql))
            return $data['id'];
    }

    public function escape($val)
    {
        return $this->db->escape($val);
    }
}
