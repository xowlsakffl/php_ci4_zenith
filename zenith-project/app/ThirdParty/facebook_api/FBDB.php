<?php
namespace App\ThirdParty\facebook_api;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Config;
use CodeIgniter\Database\RawSql;
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

    public function getCampaign($campaign_id)
    {
        if (empty($campaign_id)) {
            return null;
        }

        $sql = "SELECT * FROM fb_campaign WHERE campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);
        $row = $result->getRowArray();

        return $row;
    }

    public function getAdset($adset_id)
    {
        if (empty($adset_id)) {
            return null;
        }

        $sql = "SELECT * FROM fb_adset WHERE adset_id = {$adset_id}";
        $result = $this->db_query($sql);
        $row = $result->getRowArray();

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
      
    public function getAdSetsWithAccount($selector = ['adset_id', 'campaign_id'], $query = '')
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
      
    public function getLeadgenAds() {
        $sql = "SELECT * FROM fb_ad WHERE (leadgen_id IS NOT NULL AND leadgen_id <> '')";
        $result = $this->db_query($sql);
        return $result;
    }

      
    public function updateAdAccounts($list)
    {
        if (empty($list)) {
            return null;
        }

        if(!empty($list[0][0])){
            $this->db->query("UPDATE fb_ad_account SET status = 0, disable_reason = 0, perm = 0 WHERE business_id = '{$list[0][0]}'");
        }
        
        foreach ($list as $key => $row) {
            if (empty($row)) {
                continue;
            }

            $sql = "INSERT INTO fb_ad_account (business_id, ad_account_id, name, funding_source, status, disable_reason, pixel_id, perm, update_time)
					VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', {$row[4]}, {$row[5]}, {$row[6]}, 1, NOW())
					ON DUPLICATE KEY
					UPDATE name = '{$row[2]}', funding_source = '{$row[3]}', status = {$row[4]}, disable_reason = {$row[5]}, pixel_id = {$row[6]}, perm = 1, update_time = NOW();";
            $result = $this->db_query($sql);
        }
    }

      
    public function insertAsyncInsights($lists)
    {
        foreach ($lists as $key => $report) {
            if ($report['date_start'] != $report['date_stop']) continue;
            $hour = ($report['date_start'] == date('Y-m-d')) ? date('H') : 23;
            if(isset($report['hourly_stats_aggregated_by_audience_time_zone']))
                $hour = preg_replace('/^([0-9]{2}).+$/', '$1', $report['hourly_stats_aggregated_by_audience_time_zone']);
            $data = [
                'ad_id' => $report['ad_id'],
                'date' => $report['date_start'],
                'hour' => $hour,
                'impressions' => $report['impressions']??0,
                'clicks' => $report['clicks']??0,
                'inline_link_clicks' => $report['inline_link_clicks']??0,
                'spend' => $report['spend']??0,
            ];
            $builder = $this->db->table('fb_ad_insight_history');
            $builder->setData($data);
            $updateTime = ['update_date' => new RawSql('NOW()')];
            $builder->updateFields($updateTime, true);
            $result = $builder->upsert();

            // 캠페인 저장
            $data = [
                'campaign_id' => $report['campaign_id'],
                'campaign_name' => $report['campaign_name'],
                'account_id' => $report['account_id']
            ];
            $builder = $this->db->table('fb_campaign');
            $builder->setData($data);
            $updateTime = ['update_date' => new RawSql('NOW()')];
            $builder->updateFields($updateTime, true);
            $result = $builder->upsert();
            // 광고세트 저장
            $data = [
                'adset_id' => $report['adset_id'],
                'adset_name' => $report['adset_name'],
                'campaign_id' => $report['campaign_id']
            ];
            $builder = $this->db->table('fb_adset');
            $builder->setData($data);
            $updateTime = ['update_date' => new RawSql('NOW()')];
            $builder->updateFields($updateTime, true);
            $result = $builder->upsert();
            // 광고 저장
            $use_landing = 0;
            if (preg_match('/\#[0-9\_]+.+\*[0-9]+.+\&[a-z]+.*/i', $report['ad_name'])) {
                $use_landing = 1;
            }
            $data = [
                'ad_id' => $report['ad_id'],
                'ad_name' => $report['ad_name'],
                'adset_id' => $report['adset_id'],
                'use_landing' => $use_landing
            ];
            $builder = $this->db->table('fb_ad');
            $builder->setData($data);
            $updateTime = ['update_date' => new RawSql('NOW()')];
            $builder->updateFields($updateTime, true);
            $result = $builder->upsert();
        }
    }
      
    public function updateAdCreatives($lists)
    {
        if (!is_array($lists) && !count($lists)) return;
        foreach ($lists as $row) {
            if (empty($row)) continue;
            $data = [
                'adcreative_id' => $row['adcreative_id'],
                'ad_id' => $row['ad_id'],
                'object_type' => $row['object_type']??null,
                'thumbnail' => $row['thumbnail_url']??null,
                'link' => $row['link']??null
            ];
            $builder = $this->db->table('fb_adcreative');
            $builder->setData($data);
            $updateTime = ['update_date' => new RawSql('NOW()')];
            $builder->updateFields($updateTime, true);
            $result = $builder->upsert();
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
            $result = $this->db_query($sql);
            if(!$result){return false;};
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
        return true;
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

            $lst_sig_edit_ts = '0000-00-00 00:00:00';
            if(!is_null($report['learning_stage_info'])){
                if(!is_null($report['learning_stage_info']['last_sig_edit_ts'])){
                    $lst_sig_edit_ts = date('Y-m-d H:i:s', $report['learning_stage_info']['last_sig_edit_ts']);
                }
            }
            
            $lsiConversions = $report['learning_stage_info']['conversions'] ?? '';
            $lsiStatus = $report['learning_stage_info']['status'] ?? '';

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
                        '{$lsiConversions}',
                        '{$lsiStatus}',
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
						lsi_conversions = '{$lsiConversions}',
                        lsi_status = '{$lsiStatus}',
                        lsi_last_sig_edit_ts = '{$lst_sig_edit_ts}',
                        created_time = '{$created_time}',
						updated_time = '{$updated_time}',
						update_date = NOW()";
            $result = $this->db_query($sql);
            if(!$result){return false;};
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
        return true;
    }
      
    public function updateCampaigns($data)
    {
        foreach ($data as $key => $report) {
            if (empty($report['budget_remaining'])) $report['budget_remaining'] = 'NULL';
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
                $budget_type = "";
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
            $result = $this->db_query($sql, true);
            if(!$result){return false;};
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
        return true;
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
                            $val = "";
                            if(isset($field['values']) && isset($field['values'][0])) $val = trim(addslashes($field['values'][0]));
                            // @$this->db->query("ALTER TABLE `fb_ad_lead` ADD `{$field['name']}` VARCHAR(50) NULL DEFAULT NULL AFTER `gender`;");
                            // @$this->db->query("ALTER TABLE `fb_ad_lead_delete` ADD `{$field['name']}` VARCHAR(50) NULL DEFAULT NULL AFTER `gender`;");
                            if (trim($val)) {
                                $values .= ", {$field['name']} = '{$val}'";
                            }
                        } else {
                            array_push($custom_fields, $field);
                        }
                    }

                    $field_data = addslashes(var_export($custom_fields, true));     // 배열 그대로 저장하는데 조금 이상하다...이상해씨
                    $values .= ", field_data = '{$field_data}'";
                    $sql = $insert . $values . ", created_time = DATE_ADD('{$created_time}', INTERVAL 9 HOUR) ON DUPLICATE KEY UPDATE {$values}, update_date = NOW();";
                    $result = $this->db_query($sql, true);
                }
            }
            $this->db_query('COMMIT');
        }
    }

    // 광고 활성/종료 업데이트하는
    public function setCampaignStatus($campaign_id, $status)
    {
        if (empty($campaign_id) || empty($status)) {
            return null;
        }
        $sql = "UPDATE fb_campaign SET status = '$status' WHERE campaign_id = '$campaign_id'";
        $result = $this->db_query($sql);
    }

    public function setAdsetStatus($adset_id, $status)
    {
        if (empty($adset_id) || empty($status)) {
            return null;
        }
        $sql = "UPDATE fb_adset SET status = '$status' WHERE adset_id = '$adset_id'";
        $this->db_query($sql);
    }

    public function setAdStatus($ad_id, $status)
    {
        if (empty($ad_id) || empty($status)) {
            return null;
        }
        $sql = "UPDATE fb_ad SET status = '$status' WHERE ad_id = '$ad_id'";
        $this->db_query($sql);
    }
    
    public function updateCampaignName($data)
    {
        if (empty($data)) {
            return null;
        }

        $sql = "UPDATE fb_campaign SET campaign_name = '{$data['name']}' WHERE campaign_id = {$data['id']}";
        $result = $this->db_query($sql);
    }

    public function updateAdsetName($data)
    {
        if (empty($data)) {
            return null;
        }

        $sql = "UPDATE fb_adset SET adset_name = '{$data['name']}' WHERE adset_id = {$data['id']}";
        $result = $this->db_query($sql);
    }

    public function updateAdName($data)
    {
        if (empty($data)) {
            return null;
        }
        
        $sql = "UPDATE fb_ad SET ad_name = '{$data['name']}' WHERE ad_id = {$data['id']}";
        $result = $this->db_query($sql);
    }
    
    public function updateInsight($data)
    {
        $row = $data;
        if(empty($row['data'])) return;
        foreach($row['data'] as $v) {
            if ($row['ad_id']) {
                $data = [
                    'ad_id' => $row['ad_id'],
                    'date' => $row['date'],
                    'hour' => $v['hour'],
                    'media' => $row['media'],
                    'period' => $row['period_ad'],
                    'event_seq' => $row['event_seq'],
                    'site' => $row['site'],
                    'db_price' => $row['db_price'],
                    'db_count' => $v['count'],
                    'margin' => $v['margin'],
                    'sales' => $v['sales'],
                ];
                $builder = $this->db->table('fb_ad_insight_history');
                $builder->setData($data);
                $updateTime = ['update_date' => new RawSql('NOW()')];
                $builder->updateFields($updateTime, true);
                // d($builder->getCompiledUpsert());
                $builder->upsert();
                /*
                $sql = "UPDATE `z_facebook`.`fb_ad_insight_history` 
                SET `media` = '{$row['media']}', `period` = '{$row['period_ad']}', `event_seq` = '{$row['event_seq']}', `site` = '{$row['site']}', `db_price` = '{$row['db_price']}', `db_count` = '{$v['count']}', `margin` = '{$v['margin']}', `sales` = '{$v['sales']}', `update_date` = NOW()
                WHERE `ad_id` = '{$row['ad_id']}' AND `date` = '{$row['date']}' AND `hour` = '{$v['hour']}'";
                $this->db_query($sql, true);
                */
            }
        }
    }

    public function getAdLeads($date)
    {
        $sql = "SELECT his.ad_id, CONCAT('{',GROUP_CONCAT('\"',his.`hour`,'\":',his.spend),'}') AS spend_data, ad.code, ad.ad_name, adset.adset_name, campaign.campaign_name
                FROM `z_facebook`.`fb_ad_insight_history` AS his
                    LEFT JOIN `z_facebook`.fb_ad AS ad
                        ON his.ad_id = ad.ad_id
                    LEFT JOIN `z_facebook`.fb_adset AS adset
                        ON adset.adset_id = ad.adset_id
                    LEFT JOIN `z_facebook`.fb_campaign AS campaign
                        ON adset.campaign_id = campaign.campaign_id
                    LEFT JOIN `z_facebook`.fb_ad_account AS account
                        ON campaign.account_id = account.ad_account_id
                WHERE his.date = '{$date}' AND account.perm = 1 GROUP BY his.ad_id;";
        $result = $this->db_query($sql);

        return $result;
    }

    public function getDbPrice($data)
    {
        if (empty($data['ad_id']) || empty($data['date'])) return NULL;
        $sql = "SELECT ad_id, date, db_price FROM `z_facebook`.`fb_ad_insight_history` WHERE `ad_id` = '{$data['ad_id']}' AND `date` = '{$data['date']}' GROUP BY date ORDER BY hour DESC LIMIT 1;";
        $result = $this->db_query($sql);
        if (empty($result)) return null;
        return $result->getResultArray();
    }

    public function updateCampaignBudget($campaign_id, $budget)
    {
        $sql = "UPDATE fb_campaign set budget = {$budget} where campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);
        $sql = "UPDATE fb_adset set budget_type = '', budget = NULL, budget_remaining = NULL where campaign_id = {$campaign_id}";
        $result = $this->db_query($sql);

        if (empty($result)) return null;
        return true;
    }

    public function updateAdSetBudget($adset_id, $budget)
    {
        $sql = "UPDATE fb_adset set budget = {$budget} where adset_id = {$adset_id}";
        $result = $this->db_query($sql);
        if (empty($result)) return null;
        return true;
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

    public function db_query($sql, $error = false)
    {
        if (empty($sql)) return false;
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
