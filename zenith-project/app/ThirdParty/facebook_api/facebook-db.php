<?php
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Config;
class FBDB extends Config
{
    private $db, $db2, $zenith;
    private $sltDB;

    public function __construct()
    {
        $this->db = \Config\Database::connect('facebook');
        $this->db2 = \Config\Database::connect('ro_facebook');
        $this->zenith = \Config\Database::connect();
        //      $this->db_query("SET FOREIGN_KEY_CHECKS = 0;");
    }

    // 메인 계정에 대한 엑세스 토큰 로드
    public function getAccessToken()
    {
        $sql = "SELECT access_token, ad_account_id FROM fb_ad_account WHERE is_admin = 1";
        $result = $this->db_query($sql);
        $row = $result->getRowArray();

        return $row;
    }

    // 광고 계정 목록
    public function getAdAccounts($perm = true, $query = "")
    {
        $sql = "SELECT * FROM fb_ad_account WHERE 1 AND status = 1";
        if ($perm) {
            $sql .= " AND perm = 1";
        }
        if ($query) {
            $sql .= $query;
        }
        $sql .= " ORDER BY name DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAccountIdsFromCampaign()
    {
        $sql = "SELECT account_id FROM fb_campaign GROUP BY account_id";
        $result = $this->db_query($sql);

        return $result;
    }

    // 캠페인 목록
    public function getCampaigns()
    {
        $sql = "SELECT campaign_id FROM fb_campaign ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getCampaign($campaign_id)
    {
        if (!$campaign_id) {
            return null;
        }

        $sql = "SELECT * FROM fb_campaign WHERE campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);
        $row = $result->getResultArray();

        return $row;
    }

    // 광고세트 목록
    public function getAdSets()
    {
        $sql = "SELECT adset_id, campaign_id FROM fb_adset ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdset($adset_id)
    {
        if (!$adset_id) {
            return null;
        }

        $sql = "SELECT * FROM fb_adset WHERE adset_id = {$adset_id}";
        $result = $this->db_query($sql);
        $row = $result->getResultArray();

        return $row;
    }

    public function getAd($ad_id)
    {
        if (!$ad_id) {
            return null;
        }

        $sql = "SELECT * FROM fb_ad WHERE ad_id = {$ad_id}";
        $result = $this->db_query($sql);
        $row = $result->getResultArray();

        return $row;
    }

    // 광고 목록
    public function getAds($query = '')
    {
        $sql = "SELECT ad_id, adset_id, ad_name, effective_status FROM fb_ad WHERE 1 {$query} ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdsWithAccount($query = '')
    {
        $sql = "SELECT ad_id, adset_id, ad_name, effective_status FROM fb_ad_with_account WHERE 1 {$query} ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdSetsWithAccount($selector = array('adset_id', 'campaign_id'), $query = '')
    {
        $select = implode(',', $selector);
        $sql = "SELECT {$select} FROM fb_adset_with_account WHERE 1 {$query} ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getCampaignsWithAccount($query = '')
    {
        $sql = "SELECT campaign_id, budget FROM fb_campaign_with_account WHERE 1 {$query} ORDER BY update_date DESC";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getAdsByAdAccountId($from = null, $to = null)
    {
        if ($from != null) {
            if ($to == null) {
                $to = time();
            }
            $from = date('Y-m-d', $from);
            $to = date('Y-m-d', $to);
            $between = "AND E.date BETWEEN '{$from}' AND '{$to}'";
        }
        $sql = "SELECT A.ad_id, A.ad_name, A.effective_status, A.leadgen_id, A.created_time, D.name AS account_name FROM fb_ad AS A LEFT JOIN fb_ad_insight_history AS E ON A.ad_id = E.ad_id LEFT JOIN fb_adset AS B ON A.adset_id = B.adset_id LEFT JOIN fb_campaign AS C ON B.campaign_id = C.campaign_id LEFT JOIN fb_ad_account AS D ON C.account_id = D.ad_account_id WHERE 1 {$between} AND D.ad_account_id IS NOT NULL AND D.status = 1 AND D.perm = 1 GROUP BY A.ad_id";
        $result = $this->db_query($sql);

        return $result;
    }

    public function updateAdAccounts($list)
    {
        $this->db->query("UPDATE fb_ad_account SET status = 0, perm = 0 WHERE business_id = '{$list[0][0]}'");
        foreach ($list as $key => $row) {
            $sql = "INSERT INTO fb_ad_account (business_id, ad_account_id, name, funding_source, status, pixel_id, perm, update_time)
					VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', {$row[4]}, {$row[5]}, 1, NOW())
					ON DUPLICATE KEY
					UPDATE name = '{$row[2]}', funding_source = '{$row[3]}', status = {$row[4]}, pixel_id = {$row[5]}, perm = 1, update_time = NOW();";
            $result = $this->db_query($sql);
        }
    }

    public function updateAdAccountPerm($ad_account_id, $perm)
    {
        $sql = "UPDATE fb_ad_account SET perm = {$perm} WHERE ad_account_id = '{$ad_account_id}'";
        $result = $this->db_query($sql);
        return $result;
    }

    public function updateLeadgenCnt()
    {
        $sql = " SELECT count(ad_id) as leadgen_cnt, ad_id, DATE(created_time) AS date, created_time FROM `fb_ad_lead` WHERE 1 GROUP BY DATE(created_time), ad_id ";
        $res = $this->db_query($sql);
        foreach ($res->getResultArray() as $row) {
            $sql = "UPDATE `fb_ad_insight_history` SET leadgen = {$row['leadgen_cnt']} WHERE ad_id = {$row['ad_id']} AND date = '{$row['date']}'";
            $this->db_query($sql);
        }

        $sql = " SELECT * FROM fb_ad WHERE effective_status = 'ACTIVE' AND leadgen_id <> '' ";
        $res = $this->db_query($sql);
        foreach ($res->getResultArray() as $row) {
            $sql = "UPDATE `fb_ad_insight` AS insight SET leadgen = (SELECT SUM(leadgen) FROM fb_ad_insight_history AS history WHERE ad_id = '{$row['ad_id']}') WHERE ad_id = '{$row['ad_id']}'";
            $this->db_query($sql);
        }
    }

    public function moveToLeadgen()
    {
        $sql = " INSERT INTO fb_ad_lead_delete SELECT * FROM fb_ad_lead WHERE created_time <= DATE_SUB(NOW(), INTERVAL 6 MONTH); ";
        $this->db_query($sql);
        $sql = " DELETE FROM fb_ad_lead WHERE created_time <= DATE_SUB(NOW(), INTERVAL 6 MONTH); ";
        $this->db_query($sql);
        $sql = " UPDATE fb_ad_lead_delete SET full_name = NULL, phone_number = NULL, first_name = NULL, last_name = NULL WHERE full_name IS NOT NULL; ";
        $this->db_query($sql);
    }

    public function insertAsyncInsights($data)
    {
        foreach ($data as $key => $report) {
            if (!$report['impressions']) $report['impressions'] = 0;
            if (!$report['clicks']) $report['clicks'] = 0;
            if (!$report['inline_link_clicks']) $report['inline_link_clicks'] = 0;
            if (!$report['spend']) $report['spend'] = 0;
            if ($report['date_start'] == $report['date_stop']) {
                if($report['date_start'] != date('Y-m-d')) {

                }
                $sql = "INSERT INTO fb_ad_insight_history (
							ad_id,
							date,
                            hour,
							impressions,
							clicks,
							inline_link_clicks,
							spend,
							create_date
						)
						VALUES (
							'{$report['ad_id']}',
							'{$report['date_start']}',
                            IF(DATE(NOW()) = '{$report['date_start']}', HOUR(NOW()), 23),
							 {$report['impressions']},
							 {$report['clicks']},
							 {$report['inline_link_clicks']},
							 {$report['spend']},
							 NOW()
						)
						ON DUPLICATE KEY UPDATE
							impressions = {$report['impressions']},
							clicks = {$report['clicks']},
							inline_link_clicks = {$report['inline_link_clicks']},
							spend = {$report['spend']},
							update_date = NOW();";
                // echo $sql.'<br>';
                $this->db_query($sql);
            }
            // 캠페인 저장
            $campaign_name = $this->db->escape($report['campaign_name']);
            // echo $campaign_name; exit;
            $sql = "INSERT INTO fb_campaign (
						campaign_id,
						campaign_name,
						account_id,
						create_date
					) VALUES (
						'{$report['campaign_id']}',
						{$campaign_name},
						'{$report['account_id']}',
						NOW()
					) ON DUPLICATE KEY UPDATE
						campaign_name = {$campaign_name},
						update_date = NOW();";
            $this->db_query($sql);
            // 광고세트 저장
            $adset_name = $this->db->escape($report['adset_name']);
            $sql = "INSERT INTO fb_adset (
						adset_id,
						adset_name,
						campaign_id,
						create_date
					) VALUES (
						'{$report['adset_id']}',
						{$adset_name},
						'{$report['campaign_id']}',
						NOW()
					) ON DUPLICATE KEY UPDATE
						adset_name = {$adset_name},
						update_date = NOW();";
            $this->db_query($sql);
            // 광고 저장
            $ad_name = $this->db->escape($report['ad_name']);
            $use_landing = 0;
            if (preg_match('/\#[0-9\_]+.+\*[0-9]+.+\&[a-z]+.*/i', $ad_name)) {
                $use_landing = 1;
            }
            $sql = "INSERT INTO fb_ad (
						ad_id,
						ad_name,
						adset_id,
						use_landing,
						create_date
					) VALUES (
						'{$report['ad_id']}',
						{$ad_name},
						'{$report['adset_id']}',
						'{$use_landing}',
						NOW()
					) ON DUPLICATE KEY UPDATE
						ad_name = {$ad_name},
						update_date = NOW();";
            $this->db_query($sql);
        }
    }

    public function updateAdCreatives($data)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $row) {
                $sql = "INSERT INTO fb_adcreative(
                            adcreative_id,
                            ad_id,
                            object_type,
                            thumbnail,
                            link,
                            create_date
                        ) VALUES(
                            '{$row['adcreative_id']}',
                            '{$row['ad_id']}',
                            '{$row['object_type']}',
                            '{$row['thumbnail_url']}',
                            '{$row['link']}',
                            NOW()
                        ) ON DUPLICATE KEY UPDATE
                            adcreative_id = '{$row['adcreative_id']}',
                            object_type = '{$row['object_type']}',
                            thumbnail = '{$row['thumbnail_url']}',
                            link = '{$row['link']}',
                            update_date = NOW();";
                $this->db_query($sql);
            }
        }
    }

    public function updateAds($data)
    {
        $cnt = 0;
        foreach ($data as $key => $report) {
            $report['page'] = null;
            $report['fb_pixel'] = null;
            $report['post'] = null;
            $created_time = null;
            $updated_time = null;
            if (!empty($report['tracking_specs'])) {
                foreach ($report['tracking_specs'] as $list) {
                    foreach ($list as $key => $data) {
                        if ($key == 'page') {
                            $report['page'] = $list['page'][0];
                        }
                        if ($key == 'fb_pixel') {
                            $report['fb_pixel'] = $list['fb_pixel'][0];
                        }
                        if ($key == 'post') {
                            $report['post'] = $list['post'][0];
                        }
                    }
                }
            }
            $report['leadgen'] = null;
            if (!empty($report['conversion_specs'])) {
                foreach ($report['conversion_specs'] as $list) {
                    foreach ($list as $key => $data) {
                        if ($key == 'leadgen') {
                            $report['leadgen'] = $list['leadgen'][0];
                        }
                    }
                }
            }

            $created_time = $report['created_time'] && $report['created_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['created_time'])) : '0000-00-00 00:00:00';
            $updated_time = $report['updated_time'] && $report['updated_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['updated_time'])) : '0000-00-00 00:00:00';
            $name = $this->db->escape($report['name']);
            //          $name = addslashes($report['name']);
            $use_landing = 0;
            if (preg_match('/\#[0-9\_]+.+\*[0-9]+.+\&[a-z]+.*/i', $name)) {
                $use_landing = 1;
            }
            $sql = "INSERT INTO fb_ad (
                        ad_id,
                        ad_name,
                        effective_status,
                        status,
                        fb_pixel,
                        page_id,
                        adset_id,
                        use_landing,
                        leadgen_id,
                        created_time,
                        updated_time,
                        create_date
                    ) VALUES (
                        '{$report['id']}',
                        {$name},
                        '{$report['effective_status']}',
                        '{$report['status']}',
                        '{$report['fb_pixel']}',
                        '{$report['page']}',
                        '{$report['adset_id']}',
                        '{$use_landing}',
                        '{$report['leadgen']}',
                        '{$created_time}',
                        '{$updated_time}',
                        NOW()
                    ) ON DUPLICATE KEY UPDATE
						ad_name = {$name},
						effective_status = '{$report['effective_status']}',
						status = '{$report['status']}',
						fb_pixel = '{$report['fb_pixel']}',
						page_id = '{$report['page']}',
						use_landing = {$use_landing},
						leadgen_id = '{$report['leadgen']}',
						created_time = '{$created_time}',
						updated_time = '{$updated_time}',
						update_date = NOW()";
            $this->db_query($sql);
            /*
            $this->db_query("DELETE FROM fb_recommendations WHERE ad_id = '{$report['id']}'");
            if (!empty($report['recommendations'])) { //권고사항 정보 같이 저장
                foreach ($report['recommendations'] as $data) {
                    $sql = "INSERT INTO fb_recommendations (
								ad_id,
								code,
								title,
								message,
								importance,
								confidence,
								blame_field
							) VALUES (
								'{$report['id']}',
								'{$data['code']}',
								'".addslashes($data['title'])."',
								'".addslashes($data['message'])."',
								'{$data['importance']}',
								'{$data['confidence']}',
								'{$data['blame_field']}'
							) ON DUPLICATE KEY UPDATE
								ad_id = '{$report['id']}';";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['title'])."') ";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['message'])."') ";
                    $this->db_query($sql);
                }
            }
            */
        }
    }

    public function updateAdsets($data)
    {
        $cnt = 0;
        foreach ($data as $key => $report) {
            if (!$report['budget_remaining']) $report['budget_remaining'] = 'NULL';
            $start_time = $report['start_time'] && $report['start_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['start_time'])) : '0000-00-00 00:00:00';
            $created_time = $report['created_time'] && $report['created_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['created_time'])) : '0000-00-00 00:00:00';
            $updated_time = $report['updated_time'] && $report['updated_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['updated_time'])) : '0000-00-00 00:00:00';
            $name = $this->db->escape($report['name']);
            if ($report['lifetime_budget']) {
                $budget_type = 'lifetime';
                $budget = $report['lifetime_budget'];
            } elseif ($report['daily_budget']) {
                $budget_type = 'daily';
                $budget = $report['daily_budget'];
            } else {
                $budget_type = NULL;
                $budget = 'NULL';
            }
            $lst_sig_edit_ts = $report['learning_stage_info']['last_sig_edit_ts'] ? date('Y-m-d H:i:s', $report['learning_stage_info']['last_sig_edit_ts']) : '0000-00-00 00:00:00';
            $sql = "INSERT INTO fb_adset (
                        adset_id,
                        adset_name,
                        campaign_id,
                        effective_status,
                        status,
                        start_time,
                        lsi_conversions,
                        lsi_status,
                        lsi_last_sig_edit_ts,
                        created_time,
                        updated_time,
                        create_date
                    ) VALUES (
                        '{$report['id']}',
                        {$name},
                        '{$report['campaign_id']}',
                        '{$report['effective_status']}',
                        '{$report['status']}',
                        '{$start_time}',
                        '{$report['learning_stage_info']['conversions']}',
                        '{$report['learning_stage_info']['status']}',
                        '{$lst_sig_edit_ts}',
                        '{$created_time}',
                        '{$updated_time}',
                        NOW()
                    ) ON DUPLICATE KEY UPDATE
						adset_name = {$name},
						budget_type = '{$budget_type}',
						budget = {$budget},
						budget_remaining = {$report['budget_remaining']},
						effective_status = '{$report['effective_status']}',
						status = '{$report['status']}',
						start_time = '{$start_time}',
						lsi_conversions = '{$report['learning_stage_info']['conversions']}',
                        lsi_status = '{$report['learning_stage_info']['status']}',
                        lsi_last_sig_edit_ts = '{$lst_sig_edit_ts}',
                        created_time = '{$created_time}',
						updated_time = '{$updated_time}',
						update_date = NOW()";
            $this->db_query($sql);
            /*
            $this->db_query("DELETE FROM fb_recommendations WHERE adset_id = '{$report['id']}'");
            if (!empty($report['recommendations'])) { //권고사항 정보 같이 저장
                foreach ($report['recommendations'] as $data) {
                    $sql = "INSERT INTO fb_recommendations (
								adset_id,
								code,
								title,
								message,
								importance,
								confidence,
								blame_field
							) VALUES (
								'{$report['id']}',
								'{$data['code']}',
								'".addslashes($data['title'])."',
								'".addslashes($data['message'])."',
								'{$data['importance']}',
								'{$data['confidence']}',
								'{$data['blame_field']}'
							) ON DUPLICATE KEY UPDATE
								adset_id = '{$report['id']}';";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['title'])."') ";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['message'])."') ";
                    $this->db_query($sql);
                }
            }
            */
        }
    }

    public function updateCampaigns($data)
    {
        foreach ($data as $key => $report) {
            if (!$report['budget_remaining']) $report['budget_remaining'] = 'NULL';
            $start_time = $report['start_time'] && $report['start_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['start_time'])) : '0000-00-00 00:00:00';
            $created_time = $report['created_time'] && $report['created_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['created_time'])) : '0000-00-00 00:00:00';
            $updated_time = $report['updated_time'] && $report['updated_time'] != '1970-01-01T08:59:59+0900' ? date('Y-m-d H:i:s', strtotime($report['updated_time'])) : '0000-00-00 00:00:00';
            $name = $this->db->escape($report['name']);
            $can_use_spend_cap = $report['can_use_spend_cap'] ? $report['can_use_spend_cap'] : '0';
            $budget_rebalance_flag = $report['budget_rebalance_flag'] ? $report['budget_rebalance_flag'] : '0';
            $spend_cap = $report['spend_cap'] ? $report['spend_cap'] : 'NULL';
            if ($report['lifetime_budget']) {
                $budget_type = 'lifetime';
                $budget = $report['lifetime_budget'];
            } elseif ($report['daily_budget']) {
                $budget_type = 'daily';
                $budget = $report['daily_budget'];
            } else {
                $budget_type = NULL;
                $budget = 'NULL';
            }

            $sql = "INSERT INTO fb_campaign (
                        campaign_id,
                        campaign_name,
                        account_id,
                        effective_status,
                        status,
                        budget_type,
                        budget,
                        budget_remaining,
                        budget_rebalance_flag,
                        can_use_spend_cap,
                        spend_cap,
                        objective,
                        start_time,
                        created_time,
                        updated_time,
                        create_date
                    ) VALUES (
                        '{$report['id']}',
                        {$name},
                        '{$report['account_id']}',
                        '{$report['effective_status']}',
                        '{$report['status']}',
                        '{$budget_type}',
                        {$budget},
                        {$report['budget_remaining']},
                        {$budget_rebalance_flag},
                        '{$can_use_spend_cap}',
                        {$spend_cap},
                        '{$report['objective']}',
                        '{$start_time}',
                        '{$created_time}',
                        '{$updated_time}',
                        NOW()
                    ) ON DUPLICATE KEY UPDATE
						campaign_name = {$name},
                        budget_type = '{$budget_type}',
                        budget = {$budget},
                        budget_remaining = {$report['budget_remaining']},
                        budget_rebalance_flag = {$budget_rebalance_flag},
						can_use_spend_cap = '{$can_use_spend_cap}',
						spend_cap = {$spend_cap},
						objective = '{$report['objective']}',
						effective_status = '{$report['effective_status']}',
						status = '{$report['status']}',
						start_time = '{$start_time}',
						created_time = '{$created_time}',
						updated_time = '{$updated_time}',
                        is_updating = 0,
						update_date = NOW()";
            $this->db_query($sql);
            /*
            $this->db_query("DELETE FROM fb_recommendations WHERE campaign_id = '{$report['id']}'");
            if (!empty($report['recommendations'])) { //권고사항 정보 같이 저장
                foreach ($report['recommendations'] as $data) {
                    $sql = "INSERT INTO fb_recommendations (
								campaign_id,
								code,
								title,
								message,
								importance,
								confidence,
								blame_field
							) VALUES (
								'{$report['id']}',
								'{$data['code']}',
								'".addslashes($data['title'])."',
								'".addslashes($data['message'])."',
								'{$data['importance']}',
								'{$data['confidence']}',
								'{$data['blame_field']}'
							) ON DUPLICATE KEY UPDATE
								campaign_id = '{$report['id']}';";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['title'])."') ";
                    $this->db_query($sql);
                    $sql = " INSERT IGNORE INTO fb_translation (seq, original_txt) VALUES ((SELECT MAX(seq)+1 FROM fb_translation a), '".addslashes($data['message'])."') ";
                    $this->db_query($sql);
                }
            }
            */
        }
    }

    public function insertAdLeads($data)
    {
        if (count($data) > 0) {
            $total = count($data);
            $step = 1;
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 잠재고객을 저장합니다.", "light_red");
            $insert = "INSERT INTO `fb_ad_lead` SET ";
            $this->db_query('BEGIN');
            foreach ($data as $key => $lead) {
                CLI::showProgress($step++, $total);
                $created_time = $lead['created_time'];
                $full_name = $phone_number = $date_of_birth = $gender = null;
                $is_organic = (strlen($lead['is_organic']) == 0) ? 0 : 1;
                $custom_fields = array();
                $values = "id = '{$lead['id']}', form_id = '{$lead['form_id']}', ad_id = '{$lead['ad_id']}', is_organic = '{$is_organic}'";
                if (count($lead['field_data']) > 0) {
                    foreach ($lead['field_data'] as $key => $field) {
                        switch ($field['name']) {
                            case '이름':
                                $field['name'] = 'full_name';
                                break;
                            case '전화번호':
                                $field['name'] = 'phone_number';
                                break;
                            case '성별':
                                $field['name'] = 'gender';
                                break;
                            case '생년월일':
                                $field['name'] = 'date_of_birth';
                                break;
                        }
                        if (preg_match("/[a-z\_]+/i", $field['name']) && !preg_match("/[가-힣]+/i", $field['name'])) {
                            $$field['name'] = trim(addslashes($field['values'][0]));
                            // @$this->db->query("ALTER TABLE `fb_ad_lead` ADD `{$field['name']}` VARCHAR(50) NULL DEFAULT NULL AFTER `gender`;");
                            // @$this->db->query("ALTER TABLE `fb_ad_lead_delete` ADD `{$field['name']}` VARCHAR(50) NULL DEFAULT NULL AFTER `gender`;");
                            if (trim($$field['name'])) {
                                $values .= ", {$field['name']} = '{$$field['name']}'";
                            }
                        } else {
                            array_push($custom_fields, $field);
                        }
                    }

                    $field_data = addslashes(var_export($custom_fields, true));     // 배열 그대로 저장하는데 조금 이상하다...이상해씨
                    $values .= ", field_data = '{$field_data}'";
                    $sql = $insert . $values . ", created_time = DATE_ADD('{$created_time}', INTERVAL 9 HOUR) ON DUPLICATE KEY UPDATE {$values}, update_date = NOW();";
                    $result = $this->db_query($sql);
                }
            }
            $this->db_query('COMMIT');
        }
    }

    // 광고 활성/종료 업데이트하는
    public function setCampaignStatus($campaign_id, $status)
    {
        $sql = "UPDATE fb_campaign SET status = '$status' WHERE campaign_id = '$campaign_id'";
        $this->db_query($sql);
    }
    // 캠페인 목표Ai On/Off
    public function setCampaignAi2Status($campaign_id, $status)
    {
        $sql = "UPDATE fb_campaign SET ai2_status = '$status' WHERE campaign_id = '$campaign_id'";
        $result = $this->db_query($sql);
        return $result;
    }
    public function getCampaignAi2List() {
        $sql = "SELECT fc.campaign_id, fc.campaign_name, SUM(flc.db_count) AS db_count
                FROM fb_campaign AS fc  
                LEFT JOIN fb_adset AS fa ON fa.campaign_id = fc.campaign_id 
                LEFT JOIN fb_ad AS fd ON fa.adset_id = fd.adset_id 
                LEFT JOIN fb_lead_count AS flc ON flc.ad_id = fd.ad_id AND DATE(flc.date) = DATE(NOW())
                WHERE fc.ai2_status = 'ON'
                GROUP BY fc.campaign_id;";
        $result = $this->db_query($sql);
        return $result;
    }
    public function setAdsetStatus($adset_id, $status)
    {
        $sql = "UPDATE fb_adset SET status = '$status' WHERE adset_id = '$adset_id'";
        $this->db_query($sql);
    }
    public function setAdStatus($ad_id, $status)
    {
        $sql = "UPDATE fb_ad SET status = '$status' WHERE ad_id = '$ad_id'";
        $this->db_query($sql);
    }

    public function getAdLeads($date)
    {
        $sql = "SELECT his.ad_id, his.spend, ad.ad_name, adset.adset_name, campaign.campaign_name
			FROM `fb_ad_insight_history` AS his
			LEFT JOIN fb_ad AS ad
				ON his.ad_id = ad.ad_id
			LEFT JOIN fb_adset AS adset
				ON adset.adset_id = ad.adset_id
			LEFT JOIN fb_campaign AS campaign
				ON adset.campaign_id = campaign.campaign_id
			LEFT JOIN fb_ad_account AS account
				ON campaign.account_id = account.ad_account_id
			WHERE his.date = '{$date}' AND account.perm = 1 GROUP BY his.ad_id";
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

    public function getSpendByDate($ad_id, $date)
    {
        if ($ad_id && $date) {
            $sql = "SELECT spend FROM fb_ad_insight_history WHERE ad_id ='{$ad_id}' AND date = '{$date}'";
            $result = $this->db_query($sql);
            $row = $result->getResultArray();
            return $row['spend'];
        }
        return false;
    }

    public function updateInsight($data)
    {
        if ($data->ad_id && $data->date) {
            foreach ($data->data as $field => $value) {
                $query[] = "{$field} = '{$value}'";
            }
            $sql = "UPDATE fb_ad_insight_history SET " . implode(',', $query) . " WHERE ad_id = {$data->ad_id} AND date = '{$data->date}'";
            $this->db_query($sql);
        }
        return false;
    }

    public function insertLeadsCount($data, $date)
    {
        foreach ($data as $key => $row) {
            if ($row['ad_id']) {
                $sql = "INSERT INTO fb_lead_count (ad_id, event_id, site, media, db_price, db_count, margin, date, create_time)
						VALUES ('{$row['ad_id']}', '{$row['app_id']}', '{$row['site']}', '{$row['media']}', '{$row['db_price']}', '{$row['count']}', '{$row['margin']}', '{$date}', NOW())
						ON DUPLICATE KEY
						UPDATE ad_id = '{$row['ad_id']}', event_id = '{$row['app_id']}', site = '{$row['site']}', media = '{$row['media']}', db_price = '{$row['db_price']}', db_count = '{$row['count']}', margin = '{$row['margin']}', date = '{$date}', update_time = NOW();";
                $result = $this->db_query($sql);
            }
        }
    }

    public function getDbCount($ad_id, $date)
    {
        if (!$ad_id || !$date) return NULL;
        $sql = "SELECT * FROM fb_lead_count WHERE ad_id = '{$ad_id}' AND date = '{$date}'";
        $result = $this->db_query($sql);
        if (!$result) return null;
        return $result->getResultArray();
    }

    public function updateCampaignBudget($campaign_id, $budget)
    {
        $sql = "UPDATE fb_campaign set budget = {$budget} where campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);
        $sql = "UPDATE fb_adset set budget_type = '', budget = NULL, budget_remaining = NULL where campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);
    }

    public function updateAdSetBudget($adset_id, $budget)
    {
        $sql = "UPDATE fb_adset set budget = {$budget} where adset_id = {$adset_id}";
        $result = $this->db_query($sql);
    }

    public function updateCampaignName($data)
    {
        $sql = "UPDATE fb_campaign SET campaign_name = '{$data['name']}' WHERE campaign_id = {$data['id']}";
        $result = $this->db_query($sql);
    }

    public function updateAdsetName($data)
    {
        $sql = "UPDATE fb_adset SET adset_name = '{$data['name']}' WHERE adset_id = {$data['id']}";
        $result = $this->db_query($sql);
    }

    public function updateAdName($data)
    {
        $sql = "UPDATE fb_ad SET ad_name = '{$data['name']}' WHERE ad_id = {$data['id']}";
        $result = $this->db_query($sql);
    }

    public function getTranslation($all = false)
    {
        if ($all) {
            $query = '';
        } else {
            $query = 'WHERE translation_txt IS NULL';
        }
        $sql = " SELECT * FROM fb_translation {$query} ";
        $result = $this->db_query($sql);

        return $result;
    }

    public function insertTranslate($data)
    {
        foreach ($data as $key => $txt) {
            if ($txt) {
                $sql = " UPDATE fb_translation SET translation_txt = '" . addslashes($txt) . "' WHERE seq = '{$key}' ";
                $result = $this->db_query($sql);
            }
        }
    }
    //정파고 on/off ( op_type > 1:개돌ai, 2:20만ai, 3:3만ai)
    public function insertOptimization($adset_id, $mb_id, $op_type)
    {

        $sql = "SELECT adset_id FROM fb_optimization WHERE adset_id='{$adset_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['adset_id']) {
            $sql = "DELETE FROM fb_optimization WHERE adset_id = '{$adset_id}'";
            $this->db_query($sql);

            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization (adset_id, type, create_time)
				SELECT '{$adset_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT adset_id,create_time FROM fb_optimization  where adset_id='{$adset_id}')";
            $this->db_query($sql);

            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history (adset_id, mb_id, type, switch, update_time)
				VALUES ('{$adset_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    public function autoAiOn($campaign_id, $mb_id, $op_type) {
        $sql = "SELECT campaign_id FROM fb_optimization_campaign WHERE campaign_id='{$campaign_id}'";
        $res = $this->db_query($sql);
        $row = $res->getResultArray();
        if ($row['campaign_id']) return null;

        $sql = "INSERT INTO fb_optimization_campaign (campaign_id, type, create_time)
				SELECT '{$campaign_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT campaign_id,create_time FROM fb_optimization_campaign where campaign_id='{$campaign_id}')";
        $result = $this->db_query($sql);
        if($result) {
            $re_type = "ON";
            $sql = "INSERT INTO fb_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
				VALUES ('{$campaign_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
            $this->db_query($sql);
        }
        return $re_type;
    }

    //캠페인단 정파고 on/off
    public function insertOptimization_campaign($campaign_id, $mb_id, $op_type)
    {
        $sql = "SELECT campaign_id FROM fb_optimization_campaign WHERE campaign_id='{$campaign_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['campaign_id']) {
            $sql = "DELETE FROM fb_optimization_campaign WHERE campaign_id = '{$campaign_id}'";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization_campaign (campaign_id, type, create_time)
				SELECT '{$campaign_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT campaign_id,create_time FROM fb_optimization_campaign where campaign_id='{$campaign_id}')";
            $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
				VALUES ('{$campaign_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    //광고그룹단 ai on/off
    public function insertOptimization_adset($adset_id, $mb_id, $op_type)
    {
        $sql = "SELECT adset_id FROM fb_optimization_adset WHERE adset_id='{$adset_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['adset_id']) {
            $sql = "DELETE FROM fb_optimization_adset WHERE adset_id = '{$adset_id}'";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization_adset (adset_id, type, create_time)
				SELECT '{$adset_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT adset_id,create_time FROM fb_optimization_adset where adset_id='{$adset_id}')";
            $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history_adset (adset_id, mb_id, type, switch, update_time)
				VALUES ('{$adset_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    //목표ai 캠페인 on/off
    public function insertOptimization_goal_campaign($campaign_id, $mb_id, $op_type)
    {
        $sql = "SELECT campaign_id FROM fb_optimization_goal_campaign WHERE campaign_id='{$campaign_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['campaign_id']) {
            $sql = "DELETE FROM fb_optimization_goal_campaign WHERE campaign_id = '{$campaign_id}'";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization_goal_campaign (campaign_id, type, create_time, update_time)
				SELECT '{$campaign_id}', '{$op_type}' , NOW() , NOW()
				FROM DUAL
				WHERE not exists(SELECT campaign_id,create_time FROM fb_optimization_goal_campaign where campaign_id='{$campaign_id}')";
            $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history_goal_campaign (campaign_id, mb_id, type, switch, update_time)
				VALUES ('{$campaign_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    //목표ai 광고세트 on/off
    public function insertOptimization_goal($adset_id, $mb_id, $op_type)
    {
        $sql = "SELECT adset_id FROM fb_optimization_goal WHERE adset_id='{$adset_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['adset_id']) {
            $sql = "DELETE FROM fb_optimization_goal WHERE adset_id = '{$adset_id}'";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization_goal (adset_id, type, create_time)
				SELECT '{$adset_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT adset_id,create_time FROM fb_optimization_goal where adset_id='{$adset_id}')";
            $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history_goal (adset_id, mb_id, type, switch, update_time)
				VALUES ('{$adset_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    //광고 정파고 on/off
    public function insertOptimization_ad($ad_id, $mb_id, $op_type)
    {
        $sql = "SELECT ad_id FROM fb_optimization_ad WHERE ad_id='{$ad_id}'";

        $res = $this->db_query($sql);
        $row = $res->getResultArray();

        if ($row['ad_id']) {
            $sql = "DELETE FROM fb_optimization_ad WHERE ad_id = '{$ad_id}'";
            $this->db_query($sql);
            $re_type = "OFF";
        } else {
            $sql = "INSERT INTO fb_optimization_ad (ad_id, type, create_time)
				SELECT '{$ad_id}', '{$op_type}' , NOW()
				FROM DUAL
				WHERE not exists(SELECT ad_id,create_time FROM fb_optimization_ad where ad_id='{$ad_id}')";
            $this->db_query($sql);
            $re_type = "ON";
        }

        $sql = "INSERT INTO fb_optimization_onoff_history_ad (ad_id, mb_id, type, switch, update_time)
				VALUES ('{$ad_id}', '{$mb_id}', '{$op_type}', '{$re_type}', NOW())";
        $this->db_query($sql);
        return $re_type;
    }

    //잠재고객 데이터 가져오기
    public function getFBAdLead()
    {
        $sql = "SELECT * FROM fb_ad AS ad
		JOIN fb_ad_lead as LE on ad.ad_id = LE.ad_id
		WHERE LE.created_time >= '2018-05-03 10:20:00' and ad.ad_name REGEXP '#[0-9]+' and LE.change_time='0000-00-00 00:00:00'
		ORDER BY LE.created_time ";
        $result = $this->db_query($sql);

        return $result;
    }

    public function insertApp_subscribe($data)
    {
        foreach ($data as $key => $row) {
            $query = "";
            if ($row['group_id']) {
                $is_added = false;
                $sql = "SELECT * FROM app_subscribe WHERE group_id = '{$row['group_id']}' AND site='{$row['site']}' AND name='{$row['full_name']}' AND gender='{$row['gender']}' AND age='{$row['age']}' AND phone=ENC_DATA('{$row['phone']}') AND add1='{$row['add1']}' AND add2='{$row['add2']}' AND add3='{$row['add3']}' AND add4='{$row['add4']}' AND add5='{$row['add5']}' AND add6='{$row['add6']}' AND addr='{$row['addr']}' AND fb_ad_lead_id='{$row['ad_id']}'";
                $result = $this->g5db->query($sql);
                $is_added = $result->num_rows;
                if (!$is_added) {
                    $row['full_name'] = $this->db->escape($row['full_name']);
                    if (preg_match('/^evt_/', $row['group_id'])) $query = ", event_seq='{$row['event_id']}'";
                    $sql = "INSERT INTO app_subscribe SET group_id='{$row['group_id']}'{$query}, site='{$row['site']}', name={$row['full_name']}, gender='{$row['gender']}', age='{$row['age']}', phone=ENC_DATA('{$row['phone']}'), add1='{$row['add1']}', add2='{$row['add2']}', add3='{$row['add3']}', add4='{$row['add4']}', add5='{$row['add5']}', add6='{$row['add6']}', addr='{$row['addr']}', reg_date='{$row['reg_date']}', deleted=0, fb_ad_lead_id='{$row['ad_id']}', enc_status=1";

                    $result = $this->g5db->query($sql);
                    // echo $sql."<br/>";
                    if ($result) {
                        $sql = "update fb_ad_lead set change_time=now() where id='{$row['id']}'";
                        $result = $this->db_query($sql);
                    } else {
                        echo $this->g5db->error;
                    }
                }
            }
        }
    }

    //정파고(광고세트) 히스토리
    public function insertOptimizationHistory($adset_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history SET adset_id='{$adset_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //정파고(캠페인) 히스토리
    public function insertOptimizationHistory_campaign($campaign_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history_campaign SET campaign_id='{$campaign_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //광고관리 히스토리
    public function insertOptimizationHistory_adset($adset_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history_adset SET adset_id='{$adset_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //정파고(광고) 히스토리
    public function insertOptimizationHistory_ad($ad_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history_ad SET ad_id='{$ad_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //목표 히스토리
    public function insertOptimizationHistory_goal_campaign($campaign_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history_goal_campaign SET campaign_id='{$campaign_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }

    //목표 히스토리
    public function getOptimizationHistory_goal_campaign($campaign_id, $type, $date)
    {
        $sql = "SELECT * FROM fb_optimization_history_goal_campaign WHERE campaign_id='{$campaign_id}' AND type='{$type}' AND DATE(update_time) = '{$date}' ORDER BY update_time DESC LIMIT 1 ";
        $result = $this->db_query($sql);
        return $result->getResultArray();
    }

    //목표(광고세트) 히스토리
    public function insertOptimizationHistory_goal($adset_id, $budget, $type)
    {
        $sql = "INSERT INTO fb_optimization_history_goal SET adset_id='{$adset_id}', budget='{$budget}', type='{$type}', update_time = NOW() ";
        $result = $this->db_query($sql);
    }




    //정파고 광고세트단 예산 업데이트
    public function updateOptimization()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT a.adset_id,  ad.ad_id, sum(db_count) as db, a.type, sum(margin) as margin, sum(sales) as sales, sum(spend) as spend, db_price, adset_name, campaign_id
				FROM fb_optimization AS a
				JOIN fb_ad AS ad on a.adset_id = ad.adset_id
				JOIN fb_adset AS ads on ad.adset_id = ads.adset_id
				JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
				JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
				WHERE lc.date ='{$today}' and ads.budget > 0 and ads.budget is not null
				group by a.adset_id
		";
        $res = $this->db_query($sql);

        foreach ($res->getResultArray() as $row) {
            $budget = "";

            if ($row['sales']) {
                $margin_ratio = number_format(($row['margin'] / $row['sales']) * 100, 0); //수익률 = (수익/매출액)*100 / (margin/sales)*100
            } else {
                $margin_ratio = 0;
            }

            //----------------------------  20190702 정파고타임테이블 v8적용
            if ($row['type'] == "1") {
                if ($row['spend'] >= 300000 && $margin_ratio >= 50) { //지출금액 30만원 이상 수익률 50% 이상
                    $budget = "4000000";
                } elseif ($row['spend'] >= 200000 && $margin_ratio >= 50) { //지출금액 20만원 이상 수익률 50% 이상
                    $budget = "3000000";
                } elseif ($row['spend'] >= 100000 && $margin_ratio >= 50) { //지출금액 10만원 이상 수익률 50% 이상
                    $budget = "2000000";
                } elseif ($row['db'] >= 5 && $margin_ratio >= 40) { //유효디비 5개 이상 이고 수익률 40% 이상
                    $budget = "1000000";
                } elseif ($row['db'] >= 5 && $margin_ratio >= 30) { //유효디비 5개 이상 이고 수익률 30% 이상
                    $budget = "300000";
                } elseif ($row['db'] >= 3 && $margin_ratio >= 30) { //유효디비 3개 이상 이고 수익률 30% 이상
                    $budget = "300000";
                } elseif ($row['db'] >= 1 && $margin_ratio >= 30) { //유효디비 1개 이상 이고 수익률 30% 이상
                    $budget = "100000";
                } elseif ($row['db'] >= 1 && $margin_ratio < 30) { //유효디비 1개 이상 이고 수익률 30% 미만
                    $budget = $row['db_price'] * 0.3;
                } elseif ($row['db'] == 0 && $hour >= '6') { //유효디비 0개 이고 오전 6시 이후
                    $budget = "100000";
                } elseif ($row['db'] == 0 && $row['spend'] >= 50000) { //유효디비 0개 이고 지출액 5만원 이상
                    $budget = $row['db_price'] * 0.3;
                } else { //디비단가*0.3  //수익률 0~30 사이인 광고
                    $budget = "30000";
                }
            } else {
                if ($margin_ratio >= 50 && $row['spend'] >= 400000) { //수익률 50% 이상 이고 지출금액 40만원 이상
                    $budget = "5000000";
                } elseif ($margin_ratio >= 50 && $row['spend'] >= 300000) { //수익률 50% 이상 이고 지출금액 30만원 이상
                    $budget = "3000000";
                } elseif ($margin_ratio >= 50 && $row['spend'] >= 200000) { //수익률 50% 이상 이고 지출금액 20만원 이상
                    $budget = "2000000";
                } elseif ($margin_ratio >= 30 && $row['spend'] >= 100000) { //수익률 30% 이상 이고 지출금액 10만원 이상
                    $budget = "1000000";
                } elseif ($margin_ratio >= 30 && $row['db'] >= 5) { //수익률 30% 이상 이고 유효db 5개이상  // 20181130- 20프로 -> 30프로로 변경
                    $budget = "400000";
                } elseif ($margin_ratio >= 30) { //수익률 30% 이상   모든광고
                    $budget = "200000";
                } elseif ($margin_ratio == 0) { //수익률이 0이면
                    if ($row['type'] == "2") {
                        $budget = "200000";
                    } else if ($row['type'] == "3") {
                        $budget = "30000";
                    }
                } else { //디비단가*0.3  //수익률 0~30 사이인 광고
                    $budget = $row['db_price'] * 0.3;
                }
            }

            if ($hour >= '16' && $margin_ratio == 0) { //20190321 오후4시부턴 수익률0이면 / 모든타입에 적용  /디비단가*0.3
                $budget = $row['db_price'] * 0.3;
            }

            if ($row['type'] == "1") {
                $budget_00 = "30000";
            } else if ($row['type'] == "2") {
                $budget_00 = "200000";
            } else if ($row['type'] == "3") {
                $budget_00 = "30000";
            }


            //@ 뒤에있는 db수량만큼만 뽑아야한다(해당캠페인) - 금액 내릴 adset_id찾기
            preg_match_all("/@([0-9]*)/", $row['adset_name'], $matches);
            $check_db_count = $matches[1][0];

            if ($check_db_count) {
                $sql = "SELECT GROUP_CONCAT( ads.adset_id SEPARATOR ',') AS adset_id, sum(db_count) as db, adset_name
					FROM fb_optimization AS a
					JOIN fb_ad AS ad on a.adset_id = ad.adset_id
					JOIN fb_adset AS ads on ad.adset_id = ads.adset_id
					JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
					WHERE lc.date ='{$today}' and campaign_id = '{$row['campaign_id']}'
					group by ads.campaign_id";

                $result = $this->db_query($sql);
                $db_row = $result->getResultArray();
                $db_total  = $db_row['db'];

                if ($check_db_count <= $db_total) {
                    if ($to_adset_id) {
                        $to_adset_id = $to_adset_id . ',' . $db_row['adset_id'];
                    } else {
                        $to_adset_id = $db_row['adset_id'];
                    }
                }
            }

            $sql = "UPDATE fb_optimization set budget_{$hour} = '{$budget}', update_time = NOW() where adset_id='{$row['adset_id']}'";
            $this->db_query($sql);

            $sql = "UPDATE fb_optimization set budget_00 = '{$budget_00}', update_time = NOW()  where adset_id='{$row['adset_id']}'";
            $this->db_query($sql);
        }

        //20190828 미진행중인 정파고(광고세트) 켜있는거 모두 삭제
        $hour = date('H');

        if ($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
            $sql = "SELECT * FROM fb_optimization WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
            $res = $this->db_query($sql);
            foreach ($res->getResultArray() as $row) {
                $sql = "DELETE FROM fb_optimization WHERE adset_id = '{$row['adset_id']}'";
                $this->db_query($sql);

                //20190828 정파고(광고세트) off 히스토리 남기기
                $sql = "INSERT INTO fb_optimization_onoff_history (adset_id, mb_id, type, switch, update_time)
						VALUES ('{$row['adset_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
                $this->db_query($sql);
            }
        }

        //@하루제한 수량 넘었으면 모두 최저금액으로 낮춤// 무조건 2000원
        if ($to_adset_id) {
            $sql = "UPDATE fb_optimization set budget_{$hour} = '2000', update_time = NOW()  where ";

            $adset_id = explode(",", $to_adset_id);

            if (count($adset_id) > 0) {
                $adsets = "'" . implode("','", $adset_id) . "'";
                $sql .= " adset_id IN (" . $adsets . ")";
            }
            $this->db_query($sql);
        }
    }


    //광고세트 목표ai
    public function updateOptimization_goal()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT a.adset_id,  ad.ad_id, sum(db_count) as db, a.type, sum(margin) as margin, sum(sales) as sales, sum(spend) as spend, db_price, adset_name, campaign_id
				FROM fb_optimization_goal AS a
				JOIN fb_ad AS ad on a.adset_id = ad.adset_id
				JOIN fb_adset AS ads on ad.adset_id = ads.adset_id
				JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
				JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
				WHERE lc.date ='{$today}' and ads.budget > 0 and ads.budget is not null
				group by a.adset_id
		";
        $res = $this->db_query($sql);

        foreach ($res->getResultArray() as $row) {
            //@ 뒤에있는 db수량만큼만 뽑아야한다(해당캠페인) - 금액 내릴 adset_id찾기
            preg_match_all("/@([0-9]*)/", $row['adset_name'], $matches);
            $check_db_count = $matches[1][0];


            if ($check_db_count) {
                $sql = "SELECT GROUP_CONCAT( ads.adset_id SEPARATOR ',') AS adset_id, sum(db_count) as db, adset_name
					FROM fb_optimization_goal AS a
					JOIN fb_ad AS ad on a.adset_id = ad.adset_id
					JOIN fb_adset AS ads on ad.adset_id = ads.adset_id
					JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
					WHERE lc.date ='{$today}' and campaign_id = '{$row['campaign_id']}'
					group by ads.campaign_id";

                $result = $this->db_query($sql);
                $db_row = $result->getResultArray();

                if ($check_db_count <= $db_row['db']) {

                    $sum_adset_id = explode(",", $db_row['adset_id']);
                    $count = count($sum_adset_id);

                    for ($i = 0; $i < $count; $i++) {
                        $sql = "SELECT a.adset_id, sum(spend) as spend
								FROM fb_optimization_goal AS a
								JOIN fb_ad AS ad on a.adset_id = ad.adset_id
								JOIN fb_adset AS ads on ad.adset_id = ads.adset_id
								JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
								JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
								WHERE lc.date ='{$today}' and ads.budget > 0 and ads.budget is not null and a.adset_id='{$sum_adset_id[$i]}'
								group by a.adset_id";

                        $result = $this->db_query($sql);
                        $spend_row = $result->getResultArray();

                        $sql = "UPDATE fb_optimization_goal set budget_{$hour} = '{$spend_row['spend']}', update_time = NOW() where adset_id='{$sum_adset_id[$i]}'";
                        $this->db_query($sql);

                        $sql = "UPDATE fb_optimization_goal set budget_00 = '{$spend_row['spend']}', update_time = NOW() where adset_id='{$sum_adset_id[$i]}'";
                        $this->db_query($sql);
                    }
                }
            }
        }

        //20190828 미진행중인 정파고(광고세트) 켜있는거 모두 삭제
        $hour = date('H');

        //		if($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
        //			$sql = "SELECT * FROM fb_optimization_goal WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
        //			$res = $this->db_query($sql);
        //			while ($row = $res->fetch_assoc()) {
        //				$sql = "DELETE FROM fb_optimization_goal WHERE adset_id = '{$row['adset_id']}'";
        //				$this->db_query($sql);
        //
        //				//20190828 정파고(광고세트) off 히스토리 남기기
        //				$sql = "INSERT INTO fb_optimization_onoff_history_goal (adset_id, mb_id, type, switch, update_time)
        //						VALUES ('{$row['adset_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
        //				$this->db_query($sql);
        //			}
        //		}
    }


    //정파고(캠페인) 예산 업데이트
    public function updateOptimization_campaign()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT c.campaign_id, c.campaign_name, sum(db_count) as db, a.type, sum(margin) as margin, sum(sales) as sales, sum(spend) as spend, db_price
			FROM fb_optimization_campaign AS a
			JOIN fb_campaign AS c on a.campaign_id = c.campaign_id
			JOIN fb_adset AS ads on c.campaign_id = ads.campaign_id
			JOIN fb_ad AS ad on ads.adset_id = ad.adset_id
			JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
			JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
			WHERE lc.date ='{$today}' and c.budget > 0 and c.budget is not null and a.type = '901'
			group by a.campaign_id
		";

        $res = $this->db_query($sql);

        foreach ($res->getResultArray() as $row) {
            $budget = "";

            if ($row['sales']) {
                $margin_ratio = number_format(($row['margin'] / $row['sales']) * 100, 0); //수익률 = (수익/매출액)*100 / (margin/sales)*100
            } else {
                $margin_ratio = 0;
            }


            //----------------------------  20190923 정파고_캠페인 타임테이블 v1적용/ 버전1에서 최소금액만 3만원으로 맞춤
            if ($row['spend'] >= 300000 && $margin_ratio >= 50) { //지출금액 30만원 이상 수익률 50% 이상
                $budget = "5000000";
            } elseif ($row['spend'] >= 200000 && $margin_ratio >= 50) { //지출금액 20만원 이상 수익률 50% 이상
                $budget = "3000000";
            } elseif ($row['spend'] >= 100000 && $margin_ratio >= 50) { //지출금액 10만원 이상 수익률 50% 이상
                $budget = "2000000";
            } elseif ($row['db'] >= 5 && $margin_ratio >= 40) { //유효디비 5개 이상 이고 수익률 40% 이상
                $budget = "1000000";
            } elseif ($row['db'] >= 3 && $margin_ratio >= 30) { //유효디비 3개 이상 이고 수익률 30% 이상
                $budget = "500000";
            } elseif ($row['db'] >= 1 && $margin_ratio >= 30) { //유효디비 1개 이상 이고 수익률 30% 이상
                $budget = "200000";
            } elseif ($row['db'] >= 1 && $margin_ratio < 30) { //유효디비 1개 이상 이고 수익률 30% 미만
                $budget = "30000";
            } elseif ($row['db'] == 0 && $row['spend'] >= 50000) { //유효디비 0개 이고 지출금액5만원 이상
                $budget = "30000";
            }

            if ($hour >= '16' && $margin_ratio == 0) { //오후4시부턴 수익률0이면 일일예산 30000
                $budget = "30000";
            }

            $budget_00 = "200000";
            //==========================================================================================
            //@ 뒤에있는 db수량만큼만 뽑아야한다(해당캠페인) - 금액 내릴 campaign_id 찾기
            preg_match_all("/@([0-9]*)/", $row['campaign_name'], $matches);
            $check_db_count = $matches[1][0];

            if ($check_db_count) {
                $sql = "SELECT c.campaign_id, sum(db_count) as db, c.campaign_name
					FROM fb_optimization_campaign AS a
					JOIN fb_campaign AS c on a.campaign_id = c.campaign_id
					JOIN fb_adset AS ads on c.campaign_id = ads.campaign_id
					JOIN fb_ad AS ad on ads.adset_id = ad.adset_id
					JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
					WHERE lc.date ='{$today}' and c.campaign_id = '{$row['campaign_id']}'
					group by a.campaign_id
				";

                $result = $this->db_query($sql);
                $db_row = $result->getResultArray();
                $db_total  = $db_row['db'];

                if ($check_db_count <= $db_total) {
                    if ($to_campaign_id) {
                        $to_campaign_id = $to_campaign_id . ',' . $db_row['campaign_id'];
                    } else {
                        $to_campaign_id = $db_row['campaign_id'];
                    }
                }
            }


            //==========================================================================================

            $sql = "UPDATE fb_optimization_campaign set budget_{$hour} = '{$budget}', update_time = NOW() where campaign_id='{$row['campaign_id']}'";
            $this->db_query($sql);

            $sql = "UPDATE fb_optimization_campaign set budget_00 = '{$budget_00}', update_time = NOW()  where campaign_id='{$row['campaign_id']}'";
            $this->db_query($sql);
        }
        //====================================================================
        //미진행중인 광고중 정파고(캠페인)켜있는거 모두 삭제
        $hour = date('H');
        if ($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
            $sql = "SELECT * FROM fb_optimization_campaign WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR) and type = '901'";
            $res = $this->db_query($sql);
            foreach ($res->getResultArray() as $row) {
                $sql = "DELETE FROM fb_optimization_campaign WHERE campaign_id = '{$row['campaign_id']}'";
                $this->db_query($sql);

                //정파고(캠페인) off 히스토리 남기기
                $sql = "INSERT INTO fb_optimization_onoff_history_campaign (campaign_id, mb_id, type, switch, update_time)
						VALUES ('{$row['campaign_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
                $this->db_query($sql);
            }
        }
        //====================================================================

        //@하루제한 수량 넘었으면 모두 최저금액으로 낮춤// 무조건 30000원
        if ($to_campaign_id) {
            $sql = "UPDATE fb_optimization_campaign set budget_{$hour} = '30000', update_time = NOW()  where ";

            $campaign_id = explode(",", $to_campaign_id);
            if (count($campaign_id) > 0) {
                $adsets = "'" . implode("','", $campaign_id) . "'";
                $sql .= " campaign_id IN (" . $adsets . ")";
            }
            $this->db_query($sql);
        }
    }

    //목표ai ON/OFF 업데이트
    public function updateOptimization_goal_campaign()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT c.campaign_id, c.campaign_name, c.budget, sum(db_count) as db, a.type, a.budget_00 AS is_changed, sum(margin) as margin, sum(sales) as sales, sum(spend) as spend, db_price, c.budget
				FROM fb_optimization_goal_campaign AS a
				JOIN fb_campaign AS c on a.campaign_id = c.campaign_id
				JOIN fb_adset AS ads on c.campaign_id = ads.campaign_id
				JOIN fb_ad AS ad on ads.adset_id = ad.adset_id
				JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
				JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
				WHERE lc.date ='{$today}' and c.budget > 0 and c.budget is not null
				group by a.campaign_id
		";
        $res = $this->db_query($sql);

        foreach ($res->getResultArray() as $row) { //@ 뒤에있는 db수량도달시 캠페인 off
            preg_match_all("/@([0-9]*)/", $row['campaign_name'], $matches);
            $check_db_count = $matches[1][0];

            if ($check_db_count <> "") {
                if ($check_db_count <= $row['db']) {
                    $budget = $row['spend'];
                    $budget_00 = $row['spend'];
                } else {
                    $budget = ""; //on
                    $budget_00 = "";
                }
            }
            echo "{$row['campaign_id']}({$row['campaign_name']}) - DB조건 : {$check_db_count} / DB수 : {$row['db']} / budget_{$hour} : {$budget}" . PHP_EOL;
            //==========================================================================================

            $sql = "UPDATE fb_optimization_goal_campaign set budget_{$hour} = '{$budget}', update_time = NOW() where campaign_id='{$row['campaign_id']}'";
            $this->db_query($sql);

            $sql = "UPDATE fb_optimization_goal_campaign set budget_00 = '{$budget_00}', update_time = NOW()  where campaign_id='{$row['campaign_id']}'";
            $this->db_query($sql);
        }

        //====================================================================
        //미진행중인 광고중 목표 ai켜있는거 모두 삭제
        $hour = date('H');
        if ($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
            $sql = "SELECT * FROM fb_optimization_goal_campaign WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
            $res = $this->db_query($sql);
            foreach ($res->getResultArray() as $row) {
                $sql = "DELETE FROM fb_optimization_goal_campaign WHERE campaign_id = '{$row['campaign_id']}'";
                echo $sql . PHP_EOL;
                $this->db_query($sql);

                //목표 ai off 히스토리 남기기
                $sql = "INSERT INTO fb_optimization_onoff_history_goal_campaign (campaign_id, mb_id, type, switch, update_time)
						VALUES ('{$row['campaign_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
                echo $sql . PHP_EOL;
                $this->db_query($sql);
            }
        }
    }

    //정파고(광고) ON/OFF 업데이트
    public function updateOptimization_ad()
    {
        $today = date('Y-m-d');
        $hour = date('H');

        $sql = "SELECT ad.ad_id, ad.ad_name, db_count, a.type, sum(margin) as margin, sum(sales) as sales, sum(spend) as spend, db_price
			FROM fb_optimization_ad AS a
			JOIN fb_ad AS ad on a.ad_id = ad.ad_id
			JOIN fb_lead_count AS lc on ad.ad_id = lc.ad_id
			JOIN (SELECT * FROM fb_ad_insight_history WHERE date='{$today}')AS ah on lc.ad_id = ah.ad_id
			WHERE lc.date ='{$today}'
			group by ad.ad_id
		";

        //		echo $sql."<br/>";

        $res = $this->db_query($sql);

        foreach ($res->getResultArray() as $row) {
            if ($row['sales']) {
                $margin_ratio = number_format(($row['margin'] / $row['sales']) * 100, 0); //수익률 = (수익/매출액)*100 / (margin/sales)*100
            } else {
                $margin_ratio = 0;
            }

            //디비수 1 이상 수익률 20% 이하
            if ($row['db_count']  >= 1 && $margin_ratio <= 20) {
                $budget = "PAUSED"; //off
            } else {
                $budget = "ACTIVE"; //on
            }

            //			echo $row['ad_id']."^".$row['db_count']."^".$row['margin']."^".$row['sales']."^".$margin_ratio."<br/>";

            //==========================================================================================

            $sql = "UPDATE fb_optimization_ad set budget_{$hour} = '{$budget}', update_time = NOW() where ad_id='{$row['ad_id']}'";
            $this->db_query($sql);

            $sql = "UPDATE fb_optimization_ad set budget_00 = 'ACTIVE', update_time = NOW()  where ad_id='{$row['ad_id']}'";
            $this->db_query($sql);
        }
        //====================================================================
        //미진행중인 광고중 정파고(광고)켜있는거 모두 삭제
        $hour = date('H');
        if ($hour >= 9 && $hour <= 23) { //00시 ~ 09시까지 불필요(1시~4시까지 광고 업데이트 차단중)
            $sql = "SELECT * FROM fb_optimization_ad WHERE update_time <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
            $res = $this->db->query($sql);
            foreach ($res->getResultArray() as $row) {
                $sql = "DELETE FROM fb_optimization_ad WHERE ad_id = '{$row['ad_id']}'";
                $this->db->query($sql) or die($this->db->error);

                //정파고(광고) off 히스토리 남기기
                $sql = "INSERT INTO fb_optimization_onoff_history_ad (ad_id, mb_id, type, switch, update_time)
						VALUES ('{$row['ad_id']}', 'system', '{$row['type']}', 'OFF', NOW())";
                $this->db->query($sql) or die($this->db->error);
            }
        }
        //====================================================================
    }

    //목표ai
    public function getOptimization_goal_campaign($hour)
    {
        if (!$hour) {
            $hour = date('H');
        }

        $sql = "SELECT campaign_id, budget_00 AS is_changed, budget_{$hour} as budget, type FROM fb_optimization_goal_campaign";
        $result = $this->db_query($sql);

        return $result;
    }

    //목표ai(광고세트)
    public function getOptimization_goal($hour)
    {
        if (!$hour) {
            $hour = date('H');
        }

        $sql = "SELECT adset_id, budget_{$hour} as budget, type FROM fb_optimization_goal";
        $result = $this->db_query($sql);

        return $result;
    }


    //정파고(광고세트)
    public function getOptimization($hour)
    {
        if (!$hour) {
            $hour = date('H');
        }

        $sql = "SELECT adset_id, budget_{$hour} as budget, type FROM fb_optimization";
        $result = $this->db_query($sql);

        return $result;
    }




    //정파고(캠페인)
    public function getOptimization_campaign($hour)
    {
        if (!$hour) {
            $hour = date('H');
        }

        $sql = "SELECT campaign_id, budget_{$hour} as budget, type FROM fb_optimization_campaign";
        $result = $this->db_query($sql);

        return $result;
    }

    //정파고(광고)
    public function getOptimization_ad($hour)
    {
        if (!$hour) {
            $hour = date('H');
        }

        $sql = "SELECT ad_id, budget_{$hour} as budget, type FROM fb_optimization_ad";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getInvoices()
    {
        $sql = "SELECT B.name, A.* FROM fb_invoice A 
                    LEFT JOIN fb_ad_account B ON A.ad_account_id = B.ad_account_id
                WHERE 1 ORDER BY A.date DESC";
        $result = $this->db_query($sql);
        return $result;
    }

    public function insertInvoice($data)
    {
        $sql = "INSERT INTO 
                    fb_invoice(ad_account_id, date, tx_id, type, amount, currency, create_date)
                VALUES('{$data['ad_account_id']}', '{$data['date']}', '{$data['tx_id']}', '{$data['type']}', '{$data['amount']}', '{$data['currency']}', NOW())
                ON DUPLICATE KEY UPDATE
                type = '{$data['type']}', amount = '{$data['amount']}', currency = '{$data['currency']}', update_date = NOW()";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getInitCreatives($bs_id) {
        $sql = "SELECT faa.ad_account_id, faih.date AS insight_date, faih.create_date, faih.update_date AS insight_update_date, faa.name AS account_name, faa.status AS account_status, faa.perm, fc.campaign_id, fc.campaign_name, fc.status AS campaign_status, fc.budget, fs.adset_id, fs.adset_name, fs.status AS adset_status, fa.ad_id, fa.ad_name, fa.status AS ad_status, fa.create_date
                FROM fb_ad_insight_history AS faih
                    LEFT JOIN fb_ad AS fa ON fa.ad_id = faih.ad_id 
                    LEFT JOIN fb_adset AS fs ON fs.adset_id = fa.adset_id
                    LEFT JOIN fb_campaign AS fc ON fc.campaign_id = fs.campaign_id 
                    LEFT JOIN fb_ad_account AS faa ON faa.ad_account_id = fc.account_id 
                    LEFT JOIN (SELECT * FROM (SELECT COUNT(*) AS cnt, ad_id, create_date FROM fb_ad_insight_history AS faih WHERE 1 GROUP BY ad_id) AS a WHERE DATE(create_date) = DATE(NOW())) AS ad ON faih.ad_id = ad.ad_id
                WHERE faa.business_id = {$bs_id} AND faa.ad_account_id NOT IN (3793318980893908,4651147134908650,477242001209545) AND faa.status = 1 AND faa.perm = 1 AND fc.budget IS NOT NULL AND fc.status = 'ACTIVE' AND fs.status = 'ACTIVE' AND fa.status = 'ACTIVE' AND ad.cnt = 1";
        $result = $this->db_query($sql);

        return $result;
    }

    public function db_query($sql, $error = false)
    {
        if (!$sql) return false;
        $result = null;
        if (preg_match('#^select.*#i', trim($sql)))
            $this->sltDB = $this->db2;
        else
            $this->sltDB = $this->db;

        $this->sltDB->transStart();
        $result = $this->sltDB->query($sql);
        if (!$result && $error) {
            $err = $this->sltDB->error();
            exit($err['code'] .' : '. $err['message'] .' - '. $sql);
        }
        $this->sltDB->transComplete();
        return $result;
    }

    public function deleteBusiness($bid)
    {
        $sql = "SELECT account.business_id, account.ad_account_id, account.page_id, campaign.campaign_id, adset.adset_id, ad.ad_id
                FROM fb_ad_account AS account
                    LEFT JOIN fb_campaign AS campaign ON account.ad_account_id = campaign.account_id
                    LEFT JOIN fb_adset AS adset ON campaign.campaign_id = adset.campaign_id
                    LEFT JOIN fb_ad AS ad ON adset.adset_id = ad.adset_id
                WHERE account.business_id = '{$bid}'";
        $result = $this->db->query($sql);
        $delete_table_list = [
            'fb_ad' => 'ad_id', 'fb_ad_account' => 'ad_account_id', 'fb_ad_insight' => 'ad_id', 'fb_ad_insight_history' => 'ad_id', 'fb_ad_lead' => 'ad_id', 'fb_adcreative' => 'ad_id', 'fb_adset' => 'adset_id', 'fb_campaign' => 'campaign_id', 'fb_lead_count' => 'ad_id', 'fb_optimization' => 'adset_id', 'fb_optimization_ad' => 'ad_id', 'fb_optimization_campaign' => 'campaign_id', 'fb_optimization_goal' => 'adset_id', 'fb_optimization_goal_campaign' => 'campaign_id'
        ];
        foreach ($result->getResultArray() as $row) {
            $data['ad_account_id'][] = $row['ad_account_id'];
            $data['campaign_id'][] = $row['campaign_id'];
            $data['adset_id'][] = $row['adset_id'];
            $data['ad_id'][] = $row['ad_id'];
        }
        $data['ad_account_id'] = array_unique($data['ad_account_id']);
        $data['campaign_id'] = array_unique($data['campaign_id']);
        $data['adset_id'] = array_unique($data['adset_id']);
        $data['ad_id'] = array_unique($data['ad_id']);

        foreach ($delete_table_list as $table => $field) {
            foreach ($data[$field] as $id) {
                $sql = "DELETE FROM {$table} WHERE {$field} = '{$id}';";
                echo $sql . '<br>';
            }
        }
    }
    ////////////////////////////////////////////////////
    public function getMemo($p)
    {
        $query = "";
        if ($p['id']) $query = " AND fm.id = '{$p['id']}' ";
        $sql = "SELECT fc.campaign_name, fac.name AS account_name, fm.*
                FROM fb_memo AS fm
                LEFT JOIN fb_campaign AS fc ON fm.id = fc.campaign_id
                LEFT JOIN fb_ad_account AS fac ON fc.account_id = fac.ad_account_id
                WHERE 1 AND fm.type = '{$p['type']}'{$query} AND (fm.datetime >= DATE_SUB(NOW(), INTERVAL 3 DAY) OR (fm.datetime <= DATE_SUB(NOW(), INTERVAL 3 DAY) AND fm.is_done = 0)) ORDER BY fm.is_done ASC, fac.name ASC, fm.datetime DESC";
        // echo $sql;
        $result = $this->db_query($sql);
        if ($result->num_rows) {
            foreach ($result->getResultArray() as $row) {
                $memo[] = $row;
            }
        }
        return $memo;
    }

    public function addMemo($data)
    {
        $data['memo'] = $this->db->escape($data['memo']);
        $sql = "INSERT INTO fb_memo (`id`, `type`, `memo`, `mb_name`, `datetime`) VALUES({$data['id']}, '{$data['type']}', {$data['memo']}, '{$data['mb_name']}', NOW())";
        if ($this->db_query($sql))
            return $data['id'];
    }

    public function updateMemo($data)
    {
        $query = "";
        if ($data['is_done']) {
            $query = ", done_mb_name = '{$data['done_mb_name']}', done_datetime = NOW()";
        }
        $sql = "UPDATE fb_memo SET is_done = '{$data['is_done']}'{$query} WHERE seq = {$data['seq']}";
        if ($this->db_query($sql))
            return $data['seq'];
    }
}
