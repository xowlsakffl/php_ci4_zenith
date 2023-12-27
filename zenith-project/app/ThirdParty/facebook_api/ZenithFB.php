<?php
namespace App\ThirdParty\facebook_api;
///////////////////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//ini_set('max_execution_time', 1800);
set_time_limit(0);
ini_set('memory_limit', '-1');

require_once __DIR__ . '/vendor/autoload.php';

use App\ThirdParty\facebook_api\FBDB;
use CodeIgniter\CLI\CLI;

use JanuSoftware\Facebook\Facebook;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FacebookAds\Object\Values\AdsInsightsLevelValues;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;
use FacebookAds\Object\Values\AdsInsightsBreakdownsValues;
use FacebookAds\Object\Fields\LeadFields;
use DateTime;
use Exception;

class ZenithFB
{
    private $app_id; //(주)케어랩스 //'318448081868728'; // 열혈패밀리_ver3
    private $app_secret; //'881a2a6c6edcc9a5291e829278cb91e2';
    private $access_token;
    private $longLivedAccessToken;
    private $db;
    private $fb;
    private $business_id_list;
    private $business_id;
    private $account_id;
    private $campaign_id;
    private $adset_id;
    private $ad_id;
    private $result_data = [];
    private $account, $campaign, $adset, $ad;

    // https://developers.facebook.com/tools/debug/accesstoken
    public function __construct($bs_id = '')
    {
        @set_exception_handler(array($this, 'exception_handler'));

        try {
            include __DIR__."/config.php";
            $this->app_id = $config['app_id'];
            $this->app_secret = $config['app_secret'];
            $this->access_token = $config['access_token'];
            $this->longLivedAccessToken = $config['longLivedAccessToken'];
            $this->business_id_list = $config['business_id_list'];

            $this->db = new FBDB();
            $this->access_token = $this->longLivedAccessToken == "" ? $this->access_token : $this->longLivedAccessToken;
            Api::init($this->app_id, $this->app_secret, $this->access_token, false);
            $this->fb = new Facebook([
                'app_id' => $this->app_id,
                'app_secret' => $this->app_secret,
                'default_access_token' => $this->access_token,
                'default_graph_version' => 'v17.0'
            ]);
            $this->business_id = $bs_id ? $bs_id : $this->business_id_list[0];
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
      
    // 일반 엑세스 토큰을 연장시킴
    public function getLongLivedAccessToken()
    {
        try { //EAAEhoHjMl7gBAJVuAZCygZCHp11NFWNmf6Hng4KSCDBZCEakZC7yEkZAnAkqvXw9wSAqWX3Qg20r0rzoQORglAp1RMNdqHEeQ4Gy1GZCBlVaDIwvI4BiQzBNavFDRWk49adliwGauowZCc6j3DoMKyDuenoSa0iBVHh9t2hMJ35Pfd8Y1XcHRBy
            $oAuth2Client = $this->fb->getOAuth2Client();
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($this->access_token);
            $this->access_token = $longLivedAccessToken;
            CLI::write("longLivedAccessToken at ". CLI::color($longLivedAccessToken, "white"), "yellow");
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    // 페이스북 광고 계정 목록
    /*  1 = ACTIVE
        2 = DISABLED
        101 = CLOSED */
      
    public function getFBAccounts()
    {
        $response = $this->fb->get(
            '/' . $this->business_id . '/owned_ad_accounts?fields=account_id,name,account_status,disable_reason,funding_source_details,adspixels{id,name}&limit=20',
            $this->access_token
        );
        $edges = $response->getGraphEdge();
        $results = [];

        do {
            foreach ($edges as $account) {
                $account = $account->asArray();
                $pixel_id = 'NULL';
                $funding_source = 'NULL';
                if (isset($account['adspixels'])) {
                    $pixel_id = $account['adspixels'][0]['id'];
                }
                if (isset($account['funding_source_details']) && isset($account['funding_source_details']['display_string'])) {
                    $funding_source = $account['funding_source_details']['display_string'];
                }
                array_push($results, [$this->business_id, $account['account_id'], $account['name'], $funding_source, $account['account_status'], $account['disable_reason'], $pixel_id]);
            }
        } while ($edges = $this->fb->next($edges));

        return $results;
    }

    // 비지니스 설정   
    public function setBusinessId($bs_id = null) {
        if(!is_null($bs_id)) $this->business_id = $bs_id;
    }

    // 광고 계정 설정   
    function setAdAccount($account_id)
    {
        $this->account_id = "act_" . $account_id;
        $this->account = new AdAccount($this->account_id);
    }

    // 캠페인 ID 설정   
    function setCampaignId($campaign_id)
    {
        $this->campaign_id = $campaign_id;
        $this->campaign = new Campaign($this->campaign_id);
    }

    // 광고세트 ID 설정   
    function setAdsetId($adset_id)
    {
        $this->adset_id = $adset_id;
        $this->adset = new AdSet($this->adset_id);
    }

    // 광고 ID 설정   
    function setAdId($adset_id)
    {
        $this->ad_id = $adset_id;
        $this->ad = new Ad($this->ad_id);
    }

    // 인사이트 비동기 호출
    public function getAsyncInsights($all = "false", $date = null, $edate = null, $account_id = null)
    {
        $params = [
            'date_preset' => AdsInsightsDatePresetValues::TODAY,
            'level' => AdsInsightsLevelValues::AD,
            'breakdowns' => AdsInsightsBreakdownsValues::HOURLY_STATS_AGGREGATED_BY_AUDIENCE_TIME_ZONE,
            'filtering' => [
                // [
                //     'field'     => 'ad.impressions',
                //     'operator'  => 'GREATER_THAN',
                //     'value'     => 0
                // ],
                // [
                //     'field'     => 'ad.spend',
                //     'operator'  => 'GREATER_THAN',
                //     'value'     => 0
                // ],
                // [
                //     'field'     => 'ad.effective_status',
                //     'operator'  => 'IN',
                //     'value'     => ['ACTIVE', 'ADSET_PAUSED', 'ARCHIVED', 'CAMPAIGN_PAUSED', 'DELETED', 'DISAPPROVED', 'IN_PROCESS', 'PAUSED', 'PENDING_BILLING_INFO', 'PENDING_REVIEW', 'PREAPPROVED', 'WITH_ISSUES']
                // ]
            ],
            'fields' => [
                AdsInsightsFields::ACCOUNT_ID,
                AdsInsightsFields::CAMPAIGN_ID,
                AdsInsightsFields::CAMPAIGN_NAME,
                AdsInsightsFields::ADSET_ID,
                AdsInsightsFields::ADSET_NAME,
                AdsInsightsFields::AD_ID,
                AdsInsightsFields::AD_NAME,
                AdsInsightsFields::DATE_START,
                AdsInsightsFields::DATE_STOP,
                AdsInsightsFields::IMPRESSIONS,
                AdsInsightsFields::CLICKS,
                AdsInsightsFields::INLINE_LINK_CLICKS,
                AdsInsightsFields::SPEND
            ]
        ];
        if (!is_null($date)) {
            if (is_null($edate)) {
                $edate = $date;
            }
            $params['time_range'] = ['since' => $date, 'until' => $edate];
            unset($params['date_preset']);
        }
        if(!is_null($account_id)) {
            $accounts[]['ad_account_id'] = $account_id;
            $total = 1;
        } else {
            $account_id = $this->db->getAdAccounts(true);
            $accounts = $account_id->getResultArray();
            $total = $account_id->getNumRows();
        }
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 계정에 대한 광고인사이트 수신을 시작합니다.", "light_red");
        $return = [];
        foreach ($accounts as $row) {
            $result = [];
            $this->setAdAccount($row['ad_account_id']);
            $async_job = $this->account->getInsightsAsync([], $params);
            $getSelf = $async_job->getSelf();
            $count = 0;
            $continue = false;
            while (!$getSelf->isComplete() && !$continue) {
                $getSelf = $async_job->getSelf();
                if ($count > 100 && !$getSelf->isComplete()) {
                    ob_flush(); flush(); sleep(1);
                    $continue = true;
                }
                $count++;
            }
            CLI::showProgress($step++, $total);
            // if ($continue) continue;
            $insights = $getSelf->getInsights();
            $getResponse = $insights->getResponse();
            $response = $getResponse->getContent();
            $result = array_merge($result, $response['data']);
            if (isset($response['paging'])) {
                $url = isset($response['paging']['next']) ? $response['paging']['next'] : false;
                while ($url) {
                    $data = $this->getFBRequest_CURL($url);
                    if (!is_null($data) && isset($data['data'])) $result = array_merge($result, $data['data']);
                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : false;
                }
            }

            $this->db->insertAsyncInsights($result);
            $return = array_merge($return, $result);
        }
        if ($all == "true") {
            $this->updateAds($return);
            $this->updateAdCreatives($return);
            $this->updateAdsets($return);
            $this->updateCampaigns($return);
        }
        
        return $return;
    }
      
    public function updateAllByAccounts() {
        foreach($this->business_id_list as $bs_id) {
            self::setBusinessId($bs_id);
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$bs_id}비지니스 업데이트 시작", "white", "magenta");
            self::updateAllByAccount();
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$bs_id}비지니스 업데이트 종료", "white", "magenta");
        }
    }
      
    public function updateAllByAccount($bs_id=null) {
        if(is_null($bs_id)) $bs_id = $this->business_id;
        $account_id = $this->db->getAdAccounts(true, " AND business_id = '{$bs_id}'");
        $accounts = $account_id->getResultArray();
        $total = $account_id->getNumRows();
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 계정에 대한 광고데이터 수신을 시작합니다.", "light_red");
        $result = [];
        $campaigns_fields = implode(',', [ CampaignFields::ID, CampaignFields::NAME, CampaignFields::ACCOUNT_ID, CampaignFields::DAILY_BUDGET, CampaignFields::BUDGET_REMAINING, CampaignFields::BUDGET_REBALANCE_FLAG, CampaignFields::CAN_USE_SPEND_CAP, CampaignFields::SPEND_CAP, CampaignFields::OBJECTIVE, CampaignFields::EFFECTIVE_STATUS, CampaignFields::STATUS, CampaignFields::START_TIME, CampaignFields::CREATED_TIME, CampaignFields::UPDATED_TIME ]);
        $adsets_fields = implode(',', [ AdSetFields::ID, AdSetFields::NAME, AdSetFields::CAMPAIGN_ID, AdSetFields::EFFECTIVE_STATUS, AdSetFields::STATUS, AdSetFields::LEARNING_STAGE_INFO, AdSetFields::START_TIME, AdSetFields::UPDATED_TIME, AdSetFields::CREATED_TIME, AdSetFields::DAILY_BUDGET, AdSetFields::LIFETIME_BUDGET, AdSetFields::BUDGET_REMAINING ]);
        $ads_fields = implode(',', [ AdFields::ID, AdFields::NAME, AdFields::ADSET_ID, AdFields::EFFECTIVE_STATUS, AdFields::STATUS, AdFields::UPDATED_TIME, AdFields::CREATED_TIME, AdFields::TRACKING_SPECS, AdFields::CONVERSION_SPECS ]);
        $adcreatives_fields = implode(',', [ AdCreativeFields::ID, AdCreativeFields::BODY, AdCreativeFields::OBJECT_TYPE, AdCreativeFields::OBJECT_URL, AdCreativeFields::THUMBNAIL_URL, AdCreativeFields::IMAGE_FILE, AdCreativeFields::IMAGE_URL, AdCreativeFields::CALL_TO_ACTION_TYPE, AdCreativeFields::OBJECT_STORY_SPEC ]);
        foreach($accounts as $account) {
            CLI::showProgress($step++, $total);
            $response = $this->fb->get(
                "/act_{$account['ad_account_id']}?fields=id,name,campaigns{{$campaigns_fields},adsets{{$adsets_fields},ads{{$ads_fields},adcreatives{{$adcreatives_fields}}}}}",
                $this->access_token
            );
            $data = $response->getDecodedBody();
            $results = [];
            $campaigns = $data['campaigns']['data'] ?? [];
            foreach ($campaigns as $campaign) {
                $adsets = $campaign['adsets']['data'] ?? [];
                foreach ($adsets as $adset) {
                    $ads = $adset['ads']['data'] ?? [];
                    foreach ($ads as $ad) {
                        $adcreatives = $ad['adcreatives']['data'] ?? [];
                        foreach ($adcreatives as &$adcreative) {
                            $adcreative['ad_id'] = $ad['id'];
                        }
                        unset($adcreative);
                        $this->updateAdcreatives(null, $adcreatives);
                    }
                    $this->db->updateAds($ads);
                }
                $this->db->updateAdsets($adsets);
            }
            $this->db->updateCampaigns($campaigns);
        }
    }
      
    public function updateAdCreatives($data = null, $adcreatives = null)
    {
        if(is_null($adcreatives)) $adcreatives = $this->getAdCreatives($data);

        $result = [];
        foreach ($adcreatives as $data) {
            $updatedAdCreative = [
                'adcreative_id' => $data['id'],
                'ad_id' => $data['ad_id'],
                'thumbnail_url' => $data['thumbnail_url'] ?? '',
                'object_type' => $data[AdCreativeFields::OBJECT_TYPE],
            ];
            if (isset($data[AdCreativeFields::CALL_TO_ACTION_TYPE]) && in_array($data[AdCreativeFields::CALL_TO_ACTION_TYPE], ["LEARN_MORE", "APPLY_NOW"])) {
                $object_story_spec = $data[AdCreativeFields::OBJECT_STORY_SPEC] ?? [];
                $video_data = $object_story_spec[AdCreativeObjectStorySpecFields::VIDEO_DATA] ?? [];
                $link_data = $object_story_spec[AdCreativeObjectStorySpecFields::LINK_DATA] ?? [];
                if(isset($updatedAdCreative['object_type'])){
                    switch ($updatedAdCreative['object_type']) {
                        case 'SHARE':
                        case 'STATUS':
                            if (is_array($link_data) && isset($link_data['link'])) { //슬라이드형
                                $updatedAdCreative['link'] = $link_data['link'];
                            }
                            break;
                        case 'VIDEO':
                            if (is_array($video_data) && isset($video_data['call_to_action']) && isset($video_data['call_to_action']['value'])) { //비디오
                                $updatedAdCreative['link'] = $video_data['call_to_action']['value']['link'];
                            }
                            break;
                        default:
                            break;
                    }
                }
                if (isset($updatedAdCreative['link']) && ($updatedAdCreative['link'] == 'https://fb.me/' || $updatedAdCreative['link'] == 'http://fb.me/'))
                    $updatedAdCreative['link'] = '';
            }
            $result[] = $updatedAdCreative;
        }
        $this->db->updateAdCreatives($result);
        return $result;
    }
      
    public function getAdCreatives($data = null)
    {
        $result = [];
        $params = [
            'thumbnail_width' => 250,
            'thumbnail_height' => 250,
        ];
        $fields = [
            AdCreativeFields::ID,
            AdCreativeFields::BODY,
            AdCreativeFields::OBJECT_TYPE,
            AdCreativeFields::OBJECT_URL,
            AdCreativeFields::THUMBNAIL_URL,
            AdCreativeFields::IMAGE_FILE,
            AdCreativeFields::IMAGE_URL,
            AdCreativeFields::CALL_TO_ACTION_TYPE,
            AdCreativeFields::OBJECT_STORY_SPEC
        ];
        if ($data == null) {
            $ad_ids = $this->db->getAdsWithAccount();
            foreach ($ad_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        foreach ($data as $row) {
            if (isset($row['id']))
                $row['ad_id'] = $row['id'];
            $this->setAdId($row['ad_id']);
            $adcrearives = $this->ad->getAdCreatives($fields, $params);
            $response = $adcrearives->getResponse()->getContent();

            if (!empty($response['data'])) {
                $response['data'][0]['ad_id'] = $row['ad_id'];
                $result = array_merge($result, $response['data']);
            }
        }
        return $result;
    }

    // 개별 광고 조회 업데이트
      
    public function updateAds($data = null)
    {
        $params = [
            'fields' => [
                AdFields::ID,
                AdFields::NAME,
                AdFields::ADSET_ID,
                AdFields::EFFECTIVE_STATUS,
                AdFields::STATUS,
                //AdFields::RECOMMENDATIONS,
                AdFields::UPDATED_TIME,
                AdFields::CREATED_TIME,
                AdFields::TRACKING_SPECS,
                AdFields::CONVERSION_SPECS
            ]
        ];
        if ($data == null) {
            $ad_ids = $this->db->getAds();
            $data = $ad_ids->getResultArray();
        }
        $result = [];
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['ad_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            if(is_cli()){
                CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 광고 데이터 수신을 시작합니다.", "light_red");
            }
            foreach ($ids as $ad_id) {
                $this->setAdId($ad_id);
                $ads = $this->ad->getSelf([], $params);
                $response = $ads->getData();
                if(is_cli()){
                    CLI::showProgress($step++, $total);
                }
                $result[] = [
                    'tracking_specs' => $response['tracking_specs'] ?? [],
                    'conversion_specs' => $response['conversion_specs'] ?? [],
                    'created_time' => $response['created_time'] ?? '',
                    'updated_time' => $response['updated_time'] ?? '',
                    'name' => $response['name'] ?? '',
                    'id' => $response['id'] ?? '',
                    'adset_id' => $response['adset_id'] ?? '',
                    'campaign_id' => $response['campaign_id'] ?? '',
                    'effective_status' => $response['effective_status'] ?? '',
                    'status' => $response['status'] ?? '',
                    'fb_pixel' => $response['fb_pixel'] ?? '',
                ];
            }
            $this->db->updateAds($result);
        }

        return $result;
    }

    // 개별 광고세트 조회 업데이트   
    public function updateAdsets($data = null)
    {
        $params = [
            'fields' => [
                AdSetFields::ID,
                AdSetFields::NAME,
                AdSetFields::CAMPAIGN_ID,
                AdSetFields::EFFECTIVE_STATUS,
                AdSetFields::STATUS,
                AdSetFields::LEARNING_STAGE_INFO,
                //AdSetFields::RECOMMENDATIONS,
                AdSetFields::START_TIME,
                AdSetFields::UPDATED_TIME,
                AdSetFields::CREATED_TIME,
                AdSetFields::DAILY_BUDGET,
                AdSetFields::LIFETIME_BUDGET,
                AdSetFields::BUDGET_REMAINING
            ]
        ];
        if ($data == null) {
            $adset_ids = $this->db->getAdSetsWithAccount();
            foreach ($adset_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        $result = [];
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['adset_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            if(is_cli()){
                CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 광고그룹 데이터 수신을 시작합니다.", "light_red");
            }
            foreach ($ids as $adset_id) {
                $this->setAdsetId($adset_id);
                $adset = $this->adset->getSelf([], $params);
                $response = $adset->getData();
                // echo '<pre>'.print_r($response,1).'</pre>'; exit;
                if(is_cli()){
                    CLI::showProgress($step++, $total);
                }
                $result[] = [
                    'budget_remaining' => $response['budget_remaining'] ?? '',
                    'start_time' => $response['start_time'] ?? '',
                    'created_time' => $response['created_time'] ?? '',
                    'updated_time' => $response['updated_time'] ?? '',
                    'name' => $response['name'] ?? '',
                    'lifetime_budget' => $response['lifetime_budget'] ?? '',
                    'daily_budget' => $response['daily_budget'] ?? '',
                    'learning_stage_info' => $response['learning_stage_info'] ?? [],
                    'id' => $response['id'] ?? '',
                    'campaign_id' => $response['campaign_id'] ?? '',
                    'effective_status' => $response['effective_status'] ?? '',
                    'status' => $response['status'] ?? '',
                ];
            }
            $this->db->updateAdsets($result);
        }
        return $result;
    }

    // 개별 캠페인 조회 업데이트
    public function updateCampaigns($data = null)
    {
        $params = [
            'fields' => [
                CampaignFields::ID,
                CampaignFields::NAME,
                CampaignFields::ACCOUNT_ID,
                CampaignFields::DAILY_BUDGET,
                CampaignFields::BUDGET_REMAINING,
                CampaignFields::BUDGET_REBALANCE_FLAG,
                CampaignFields::CAN_USE_SPEND_CAP,
                CampaignFields::SPEND_CAP,
                CampaignFields::OBJECTIVE,
                CampaignFields::EFFECTIVE_STATUS,
                //CampaignFields::RECOMMENDATIONS,
                CampaignFields::STATUS,
                CampaignFields::START_TIME,
                CampaignFields::CREATED_TIME,
                CampaignFields::UPDATED_TIME
            ]
        ];
        if ($data == null) {
            $campaign_ids = $this->db->getCampaignsWithAccount();
            foreach ($campaign_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        $result = [];
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['campaign_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            if(is_cli()){
                CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 캠페인 데이터 수신을 시작합니다.", "light_red");
            }
            foreach ($ids as $campaign_id) {
                $this->setCampaignId($campaign_id);
                $campaign = $this->campaign->getSelf([], $params);
                $response = $campaign->getData();
                if(is_cli()){
                    CLI::showProgress($step++, $total);
                }
                $result[] = [
                    'budget_remaining' => $response['budget_remaining'] ?? '',
                    'start_time' => $response['start_time'] ?? '',
                    'created_time' => $response['created_time'] ?? '',
                    'updated_time' => $response['updated_time'] ?? '',
                    'name' => $response['name'] ?? '',
                    'can_use_spend_cap' => $response['can_use_spend_cap'] ?? '',
                    'budget_rebalance_flag' => $response['budget_rebalance_flag'] ?? '',
                    'spend_cap' => $response['spend_cap'] ?? '',
                    'lifetime_budget' => $response['lifetime_budget'] ?? '',
                    'daily_budget' => $response['daily_budget'] ?? '',
                    'id' => $response['id'] ?? '',
                    'account_id' => $response['account_id'] ?? '',
                    'effective_status' => $response['effective_status'] ?? '',
                    'status' => $response['status'] ?? '',
                    'objective' => $response['objective'] ?? '',
                ];
            }

            $this->db->updateCampaigns($result);
        }

        return $result;
    }

    // 광고 조회   
    public function getAds()
    {
        $params = [
            // 'time_range' => ['since'=>date('Y-m-d', strtotime('-2 year')), 'until'=>date('Y-m-d')],
            // 'date_preset' => 'today',
            AdFields::EFFECTIVE_STATUS => [
                'ACTIVE',
                // 'ADSET_PAUSED',
                // 'ARCHIVED',
                // 'CAMPAIGN_PAUSED',
                // 'DELETED',
                'DISAPPROVED',
                // 'IN_PROCESS',
                'PAUSED',
                'PENDING_BILLING_INFO',
                'PENDING_REVIEW',
                'PREAPPROVED',
                'WITH_ISSUES'
            ],
            /*
            AdFields::STATUS => array(
                'ACTIVE',
                'PAUSED'
            ),*/
            'fields' => [
                AdFields::ID,
                AdFields::NAME,
                AdFields::ADSET_ID,
                AdFields::EFFECTIVE_STATUS,
                AdFields::STATUS,
                AdFields::RECOMMENDATIONS,
                AdFields::UPDATED_TIME,
                AdFields::CREATED_TIME,
                AdFields::TRACKING_SPECS,
                AdFields::CONVERSION_SPECS
            ]
        ];

        $ad_accounts = $this->db->getAdAccounts();          // 각 광고 계정별
        $total = $ad_accounts->getNumRows();
        $step = 1;
        $result = [];
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 계정에 대한 광고 데이터 수신을 시작합니다.", "light_red");
        foreach ($ad_accounts->getResultArray() as $row) {
            // $row['ad_account_id'] = 796319794698742;
            $this->setAdAccount($row['ad_account_id']);
            $ads = $this->account->getAds([], $params);
            $response = $ads->getResponse()->getContent();
            CLI::showProgress($step++, $total);
            $result = array_merge($result, $response['data']);
            if (isset($response['paging'])) {
                $url = isset($response['paging']['next']) ? $response['paging']['next'] : false;
                while ($url) {
                    $data = $this->getFBRequest_CURL($url);

                    if (isset($data['data'])) {
                        $result = array_merge($result, $data['data']);
                    }

                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : false;
                }
            }
            // echo '<pre>'.print_r($result,1).'</pre>'; exit;
        }
        $this->db->updateAds($result);
        return $result;
    }

    // 페이징 전용 함수   
    private function getFBRequest_CURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        return $data;
    }

    public function adLeadByAd($ad_id, $from = "-1 day", $to = null) {
        $result = [];
        $params = [];
        // 시작 날짜
        $from_dt = new DateTime($from);
        $from_dt->format('Y-m-d 00:00:00');
        $from_date = $from_dt->getTimestamp();

        $params['filtering'] = [
            [
                'field'     => 'time_created',
                'operator'  => 'GREATER_THAN',
                'value'     => $from_date
            ],
        ];

        // 끝 날짜
        if ($from !== "-1 day" && $to !== null) {
            $to_dt = new DateTime($to." 23:59:59");
            $to_dt->format('Y-m-d H:i:s');
            $to_date = $to_dt->getTimestamp();

            $params['filtering'][1] = [
                'field'     => 'time_created',
                'operator'  => 'LESS_THAN',
                'value'     => $to_date
            ];
        }

        $fields = [
            LeadFields::ID,
            LeadFields::FORM_ID,
            LeadFields::AD_ID,
            LeadFields::AD_NAME,
            LeadFields::ADSET_ID,
            LeadFields::ADSET_NAME,
            LeadFields::CAMPAIGN_ID,
            LeadFields::CAMPAIGN_NAME,
            LeadFields::IS_ORGANIC,
            LeadFields::FIELD_DATA,
            LeadFields::CREATED_TIME,
            LeadFields::CUSTOM_DISCLAIMER_RESPONSES
        ];
        $this->setAdId($ad_id);
        $leads = $this->ad->getLeads($fields, $params);
        $response = $leads->getResponse()->getContent();
        $lead_data = [];
        if (!empty($response['data'])) {
            $lead_data = array_merge($lead_data, $response['data']);
            $result = array_merge($result, $response['data']);
        }
        if (isset($response['paging'])) {
            $url = isset($response['paging']['next']) ? $response['paging']['next'] : false;
            while ($url) {
                $data = $this->getFBRequest_CURL($url);
                if (isset($data['data'])) {
                    $lead_data = array_merge($lead_data, $response['data']);
                    $result = array_merge($result, $data['data']);
                }
                $url = isset($data['paging']['next']) ? $data['paging']['next'] : false;
            }
        }
        return $result;
    }

    // 각 AdID별 폼 목록
    public function getAdLead($from = "-1 day", $to = null)
    {
        $result = [];
        $params = [];

        // 시작 날짜
        $from_dt = new DateTime($from);
        $from_dt->format('Y-m-d 00:00:00');
        $from_date = $from_dt->getTimestamp();

        $params['filtering'] = [
            [
                'field'     => 'time_created',
                'operator'  => 'GREATER_THAN',
                'value'     => $from_date
            ],
        ];

        // 끝 날짜
        if ($from !== "-1 day" && $to !== null) {
            $to_dt = new DateTime($to." 23:59:59");
            $to_dt->format('Y-m-d H:i:s');
            $to_date = $to_dt->getTimestamp();

            $params['filtering'][1] = [
                'field'     => 'time_created',
                'operator'  => 'LESS_THAN',
                'value'     => $to_date
            ];
        }

        $fields = [
            LeadFields::ID,
            LeadFields::FORM_ID,
            LeadFields::AD_ID,
            LeadFields::AD_NAME,
            LeadFields::ADSET_ID,
            LeadFields::ADSET_NAME,
            LeadFields::CAMPAIGN_ID,
            LeadFields::CAMPAIGN_NAME,
            LeadFields::IS_ORGANIC,
            LeadFields::FIELD_DATA,
            LeadFields::CREATED_TIME,
            LeadFields::CUSTOM_DISCLAIMER_RESPONSES
        ];
        
        $ad_ids = $this->db->getLeadgenAds(); //from DB
        $total = $ad_ids->getNumRows();
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 광고에서 {$from} ~ {$to} 기간의 잠재고객 데이터를 수신합니다.", "light_red");
        foreach ($ad_ids->getResultArray() as $row) { //while $row['page_id']
            CLI::showProgress($step++, $total);
            // $this->grid($row); continue;
            if ($row['leadgen_id'] == null || $row['leadgen_id'] == '' || strtotime($row['update_date']) <= strtotime('-1 month')) { //소재가 1개월 이상 업데이트 되지 않았으면 잠재고객을 받지 않음
                continue;
            }
            // echo '<pre>'.print_r($row,1).'</pre>';
            $this->setAdId($row['ad_id']);
            $leads = $this->ad->getLeads($fields, $params);
            $response = $leads->getResponse()->getContent();
            $lead_data = [];
            if (!empty($response['data'])) {
                $lead_data = array_merge($lead_data, $response['data']);
                $result = array_merge($result, $response['data']);
            }
            if (isset($response['paging'])) {
                $url = isset($response['paging']['next']) ? $response['paging']['next'] : false;
                while ($url) {
                    $data = $this->getFBRequest_CURL($url);
                    if (isset($data['data'])) {
                        $lead_data = array_merge($lead_data, $response['data']);
                        $result = array_merge($result, $data['data']);
                    }
                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : false;
                }
            }
            if(count($lead_data))
                $this->db->insertAdLeads($lead_data);
        }

        return $result;
    }

      
    public function updateAdAccounts()
    {
        $result = [];
        foreach($this->business_id_list as $bs_id) {
            self::setBusinessId($bs_id);
            $accounts = self::getFBAccounts();
            $this->db->updateAdAccounts($accounts);
            $result = array_merge($result, $accounts);
        }
        return $result;
    }

    public function getCampaignStatusBudget($campaign_id)
    {
        $params = [
            'fields' => [
                CampaignFields::ID,
                CampaignFields::DAILY_BUDGET,
                CampaignFields::STATUS,
            ]
        ];

        $this->setCampaignId($campaign_id);
        $campaign = $this->campaign->getSelf([], $params);
        $response = $campaign->getData();

        $result = [
            'id' => $response['id'],
            'budget' => $response['daily_budget'],
            'status' => $response['status']
        ];
        return $result;
    }

    //광고 상태 업데이트
    public function setCampaignStatus($id, $status)
    {
        $result = [];
        $statusValue = $status == 'ACTIVE' ? Campaign::STATUS_ACTIVE : Campaign::STATUS_PAUSED;

        $this->setCampaignId($id);
        $campaign = $this->campaign->updateSelf([], [
            Campaign::STATUS_PARAM_NAME => $statusValue,
        ]);
        
        $campaign = $campaign->getData();
        if ($campaign['success'] == 1) {
            $this->db->setCampaignStatus($id, $status);
            $result = ['response' => true];
            return $result;
        }

        return null;
    }
    
    public function getAdsetStatusBudget($adset_id)
    {
        $params = [
            'fields' => [
                AdSetFields::ID,
                AdSetFields::STATUS,
                AdSetFields::DAILY_BUDGET,
            ]
        ];

        $this->setAdsetId($adset_id);
        $adset = $this->adset->getSelf([], $params);
        $response = $adset->getData();

        $result = [
            'id' => $response['id'],
            'budget' => $response['daily_budget'],
            'status' => $response['status']
        ];
        return $result;
    }

    public function setAdsetStatus($id, $status)
    {
        $result = [];
        $statusValue = $status == 'ACTIVE' ? AdSet::STATUS_ACTIVE : AdSet::STATUS_PAUSED;

        $this->setAdsetId($id);
        $adset = $this->adset->updateSelf([], [
            AdSet::STATUS_PARAM_NAME => $statusValue,
        ]);
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            $this->db->setAdsetStatus($id, $status);
            $result = ['response' => true];
            return $result;
        }

        return null;
    }

    public function getAdStatusBudget($ad_id)
    {
        $params = [
            'fields' => [
                AdFields::ID,
                AdFields::STATUS,
            ]
        ];

        $this->setAdId($ad_id);
        $ads = $this->ad->getSelf([], $params);
        $response = $ads->getData();

        $result = [
            'id' => $response['id'],
            'status' => $response['status']
        ];
        return $result;
    }

    public function setAdStatus($id, $status)
    {
        $result = [];
        $statusValue = $status == 'ACTIVE' ? Ad::STATUS_ACTIVE : Ad::STATUS_PAUSED;

        $this->setAdId($id);
        $ad = $this->ad->updateSelf([], [
            Ad::STATUS_PARAM_NAME => $statusValue,
        ]);
        $ad = $ad->getData();
        if ($ad['success'] == 1) {
            $this->db->setAdStatus($id, $status);
            $result = ['response' => true];
            return $result;
        }

        return null;
    }

    public function updateCampaignBudget($data)
    {
        //한개씩 캠페인 일일예산 수정
        $campaign_id = $data['id'] ?? null;
        $budget = $data['budget'] ?? null;

        if (!$campaign_id || $budget === null) {
            return null;
        }

        $row = $this->db->getCampaign($campaign_id);
        // 기본 정보 설정
        $campaignfields = [CampaignFields::DAILY_BUDGET => intval($budget)];
        
        $campaign = new Campaign($campaign_id);

        // $campaign->setData($campaignfields);
        $campaign = $campaign->updateSelf([], $campaignfields);
        $campaign = $campaign->getData();
        
        if ($campaign['success'] == 1) {
            $this->db->updateCampaignBudget($campaign_id, $budget);
            return $campaign_id;
        }
        return null;
    }

    public function updateAdSetBudget($data)
    {
        //한개씩 일일예산 수정
        $adset_id = $data['id'] ?? null;
        $budget = $data['budget'] ?? null;
        if (!$adset_id || !$budget || $budget < 1000) {
            return null;
        }
        $row = $this->db->getAdSet($adset_id);
        $field = '';
        switch ($row['budget_type']) {
            case 'lifetime':
                $field = AdSetFields::LIFETIME_BUDGET;
                break;
            case 'daily':
                $field = AdSetFields::DAILY_BUDGET;
                break;
        }
        // 기본 정보 설정
        $adsetfields = [$field => $budget];

        $adset = new AdSet($adset_id);
        // $adset->setData($adsetfields);
        $adset = $adset->updateSelf([], $adsetfields);
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            $this->db->updateAdSetBudget($adset_id, $budget);
            return $adset_id;
        }

        return null;
    }
    
    public function updateName($data)
    {
        $id = trim($data['id'] ?? '');
        $type = trim($data['type'] ?? '');

        if (!$id || !$type) {
            return null;
        }
        $result = '';
        switch ($type) {
            case 'campaigns':
                $result = $this->updateCampaignName($data);
                break;
            case 'adsets':
                $result = $this->updateAdsetName($data);
                break;
            case 'ads':
                $result = $this->updateAdName($data);
                break;
            default:
                return null;
        }
        return $result;
    }

    private function updateCampaign(array $fields, array $data)
    {
        if (empty($fields) || empty($data)) {
            return null;
        }
    
        if (empty($data['id'])) {
            return null;
        }

        $campaign = new Campaign($data['id']);
        // $campaign->setData($fields);
        $campaign = $campaign->updateSelf([], $data);
        $campaign = $campaign->getData();
        if ($campaign['success'] == 1) {
            return $data;
        }

        return null;
    }

    private function updateAdset(array $fields, array $data)
    {
        if (empty($fields) || empty($data)) {
            return null;
        }
    
        if (empty($data['id'])) {
            return null;
        }

        $adset = new AdSet($data['id']);
        // $adset->setData($fields);
        $adset = $adset->updateSelf([], $data);
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            return $data;
        }

        return null;
    }

    private function updateAd(array $fields, array $data)
    {
        if (empty($fields) || empty($data)) {
            return null;
        }
    
        if (empty($data['id'])) {
            return null;
        }

        $ad = new Ad($data['id']);
        // $ad->setData($fields);
        $ad = $ad->updateSelf([], $data);
        $ad = $ad->getData();
        if ($ad['success'] == 1) {
            return $data;
        }

        return null;
    }
    
    public function updateCampaignName(array $data)
    {
        if(empty($data['name'])){
            return null;
        }

        $fields = [CampaignFields::NAME => $data['name']];
        $apiResult = $this->updateCampaign($fields, $data);
        if ($apiResult) {
            $this->db->updateCampaignName($data);
            return $apiResult;
        }
        
        return null;
    }

    public function updateAdsetName(array $data)
    {
        if(empty($data['name'])){
            return null;
        }

        $fields = [AdSetFields::NAME => $data['name']];
        $apiResult = $this->updateAdset($fields, $data);
        if ($apiResult) {
            $this->db->updateAdsetName($data);
            return $apiResult;
        }
        return null;
    }

    public function updateAdName(array $data)
    {
        if(empty($data['name'])){
            return null;
        }

        $fields = [AdFields::NAME => $data['name']];
        $apiResult = $this->updateAd($fields, $data);
        if ($apiResult) {
            $this->db->updateAdName($data);
            return $apiResult;
        }
        return null;
    }

    public function setManualUpdate($campaignIds)
    {
        if(!$campaignIds){return false;}

        $campaignParam = [
            CampaignFields::ID,
            CampaignFields::NAME,
            CampaignFields::ACCOUNT_ID,
            CampaignFields::DAILY_BUDGET,
            CampaignFields::BUDGET_REMAINING,
            CampaignFields::BUDGET_REBALANCE_FLAG,
            CampaignFields::CAN_USE_SPEND_CAP,
            CampaignFields::SPEND_CAP,
            CampaignFields::OBJECTIVE,
            CampaignFields::EFFECTIVE_STATUS,
            //CampaignFields::RECOMMENDATIONS,
            CampaignFields::STATUS,
            CampaignFields::START_TIME,
            CampaignFields::CREATED_TIME,
            CampaignFields::UPDATED_TIME
        ];

        $adsetParam = [
            AdSetFields::ID,
            AdSetFields::NAME,
            AdSetFields::CAMPAIGN_ID,
            AdSetFields::EFFECTIVE_STATUS,
            AdSetFields::STATUS,
            AdSetFields::LEARNING_STAGE_INFO,
            //AdSetFields::RECOMMENDATIONS,
            AdSetFields::START_TIME,
            AdSetFields::UPDATED_TIME,
            AdSetFields::CREATED_TIME,
            AdSetFields::DAILY_BUDGET,
            AdSetFields::LIFETIME_BUDGET,
            AdSetFields::BUDGET_REMAINING
        ];

        $adParam = [
            AdFields::ID,
            AdFields::NAME,
            AdFields::ADSET_ID,
            AdFields::EFFECTIVE_STATUS,
            AdFields::STATUS,
            //AdFields::RECOMMENDATIONS,
            AdFields::UPDATED_TIME,
            AdFields::CREATED_TIME,
            AdFields::TRACKING_SPECS,
            AdFields::CONVERSION_SPECS
        ];

        $adCreativeParams = [
            'thumbnail_width' => 250,
            'thumbnail_height' => 250,
        ];

        $adCreativeFields = [
            AdCreativeFields::ID,
            AdCreativeFields::BODY,
            AdCreativeFields::OBJECT_TYPE,
            AdCreativeFields::OBJECT_URL,
            AdCreativeFields::THUMBNAIL_URL,
            AdCreativeFields::IMAGE_FILE,
            AdCreativeFields::IMAGE_URL,
            AdCreativeFields::CALL_TO_ACTION_TYPE,
            AdCreativeFields::OBJECT_STORY_SPEC
        ];

        $campaignResult = [];
        foreach ($campaignIds as $campaignId) {
            $this->setCampaignId($campaignId);
            $campaign = $this->campaign->read($campaignParam);
            $campaignResponse = $campaign->getData();
            $campaignResult[] = [
                'budget_remaining' => $campaignResponse['budget_remaining'] ?? '',
                'start_time' => $campaignResponse['start_time'] ?? '',
                'created_time' => $campaignResponse['created_time'] ?? '',
                'updated_time' => $campaignResponse['updated_time'] ?? '',
                'name' => $campaignResponse['name'] ?? '',
                'can_use_spend_cap' => $campaignResponse['can_use_spend_cap'] ?? '',
                'budget_rebalance_flag' => $campaignResponse['budget_rebalance_flag'] ?? '',
                'spend_cap' => $campaignResponse['spend_cap'] ?? '',
                'lifetime_budget' => $campaignResponse['lifetime_budget'] ?? '',
                'daily_budget' => $campaignResponse['daily_budget'] ?? '',
                'id' => $campaignResponse['id'] ?? '',
                'account_id' => $campaignResponse['account_id'] ?? '',
                'effective_status' => $campaignResponse['effective_status'] ?? '',
                'status' => $campaignResponse['status'] ?? '',
                'objective' => $campaignResponse['objective'] ?? '',
            ];

            $adsetResult = [];
            $adsets = $this->campaign->getAdSets($adsetParam);
            foreach ($adsets as $adset) {
                $adsetResponse = $adset->getData();
                $adsetResult[] = [
                    'budget_remaining' => $adsetResponse['budget_remaining'] ?? '',
                    'start_time' => $adsetResponse['start_time'] ?? '',
                    'created_time' => $adsetResponse['created_time'] ?? '',
                    'updated_time' => $adsetResponse['updated_time'] ?? '',
                    'name' => $adsetResponse['name'] ?? '',
                    'lifetime_budget' => $adsetResponse['lifetime_budget'] ?? '',
                    'daily_budget' => $adsetResponse['daily_budget'] ?? '',
                    'learning_stage_info' => $adsetResponse['learning_stage_info'] ?? [],
                    'id' => $adsetResponse['id'] ?? '',
                    'campaign_id' => $adsetResponse['campaign_id'] ?? '',
                    'effective_status' => $adsetResponse['effective_status'] ?? '',
                    'status' => $adsetResponse['status'] ?? '',
                ];

                $adResult = [];
                $ads = $adset->getAds($adParam);
                foreach ($ads as $ad) {
                    $adResponse = $ad->getData();
                    $adResult[] = [
                        'tracking_specs' => $adResponse['tracking_specs'] ?? [],
                        'conversion_specs' => $adResponse['conversion_specs'] ?? [],
                        'created_time' => $adResponse['created_time'] ?? '',
                        'updated_time' => $adResponse['updated_time'] ?? '',
                        'name' => $adResponse['name'] ?? '',
                        'id' => $adResponse['id'] ?? '',
                        'adset_id' => $adResponse['adset_id'] ?? '',
                        'campaign_id' => $adResponse['campaign_id'] ?? '',
                        'effective_status' => $adResponse['effective_status'] ?? '',
                        'status' => $adResponse['status'] ?? '',
                        'fb_pixel' => $adResponse['fb_pixel'] ?? '',
                    ];

                    $adCreatives = $ad->getAdCreatives($adCreativeFields, $adCreativeParams);
                    foreach ($adCreatives as $adCreative) {
                        $acResponse = $adCreative->getData();
                        $acResponse['ad_id'] = $adResponse['id'];
                        $acResult[] = $acResponse;
                    }
                    $result = $this->updateAdcreatives(null, $acResult);
                    if(!$result){
                        return false;
                    }
                }
                $result = $this->db->updateAds($adResult);
                if(!$result){
                    return false;
                }
            }
            $result = $this->db->updateAdsets($adsetResult);
            if(!$result){
                return false;
            }
        }
        $result = $this->db->updateCampaigns($campaignResult);
        if(!$result){
            return false;
        }
        return true;
    }

    public function landingGroup($title)
    {
        if (empty($title)) return null;
        preg_match_all('/.+\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        if(!isset($matches[9][0])) $matches[9][0] = "";
        switch ($matches[9][0]) {
            case 'fer': $media = '이벤트 랜딩'; break;
            case 'fercpm': $media = '이벤트 랜딩_cpm'; break;
            case 'ler': $media = '이벤트 잠재고객'; break;
            case 'lercpm': $media = '이벤트 잠재고객_cpm'; break;
            case 'cpm': $media = 'cpm'; break;
            default: $media = '';break;
        }
        if ($media) {
            $period_ad = isset($matches[12][0]) && $matches[12][0] ? $matches[12][0] : 0;
            $result = [
                'name' => $matches[0][0] ?? ''
                ,'media' => $media
                ,'media_code' => $matches[9][0]??''
                ,'event_seq' => $matches[1][0] ?? ''
                ,'site' => $matches[3][0] ?? ''
                ,'db_price' => $matches[6][0] ?? 0
                ,'period_ad' => $period_ad
            ];
            return $result;
        }
        return null;
    }

    public function getAdsUseLanding($date = null)
    {
        //유효DB 개수 업데이트
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $ads = $this->db->getAdLeads($date);
        $step = 1;
        $total = $ads->getNumRows();
        if(!$total) return null;
        $result = [];
        foreach($ads->getResultArray() as $row) {
            $error = [];
            CLI::showProgress($step++, $total);
            if (!empty($row['code'])) {
                $title = trim($row['code']);
            }else{
                $title = $row['ad_name'];
            }
            $landing = $this->landingGroup($title); //소재와 이벤트 매칭
            $data = [];
            $data = [
                 'date' => $date
                ,'ad_id' => $row['ad_id']
            ];
            if(!is_null($landing)){
                $data = array_merge($data, $landing);
                if (!preg_match('/cpm/', $landing['media'])) {
                    if (!$landing['event_seq']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 이벤트번호 미입력' . PHP_EOL;
                    if (!$landing['db_price']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): DB단가 미입력' . PHP_EOL;
                }
            }else if(preg_match('/&[a-z]+/', $row['ad_name'])){
                $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 인식 오류' . PHP_EOL;
            }

            if(!empty($error)){
                foreach($error as $err) CLI::write("{$err}", "light_purple");
            }

            if(empty($landing)) continue;

            $dp = $this->db->getDbPrice($data);
            $leads = $this->db->getLeads($data);
            $cpm = false;
            if(is_null($leads) && $data['media'] === 'cpm') $cpm = true;
            $db_price = $data['db_price'];
            if(isset($dp['db_price']) && $data['date'] != date('Y-m-d'))
                $db_price = $data['db_price'] = $dp['db_price'];
            /* 
            *수익, 매출액 계산
            !xxxcpm - 유효db n / 수익,매출0
            !cpm - 유효db 0 / 수익,매출0
            !period - ^25 = *0.25
            */
            $sp_data = json_decode($row['spend_data'],1);
            if(!$data['event_seq'] && $data['media']) {
                foreach($sp_data as $hour => $spend) {
                    $margin = 0;
                    if($data['period_ad']) $margin = $spend * ('0.' . $data['period_ad']);
                    $data['data'][] = ['hour' => $hour,'spend' => $spend,'count' => "",'sales' => "",'margin' => $margin];
                }
            }
            $initZero = false;
            if(preg_match('/cpm/i', $data['media'])) //cpm (fhrm, fhspcpm, jhrcpm) 계산을 무효화
                $initZero = true;
            $lead = [];
            if(!is_null($leads)) {
                foreach($leads->getResultArray() as $row) {
                    // if($data['ad_id'] == 23853888597370162) dd($row);
                    $sales = 0;
                    $db_count = $row['db_count'] ?? 0;
                    if($db_price) $sales = $db_price * $db_count;
                    if($initZero) $sales = 0;
                    if(preg_match('/cpm/i', $data['media'])) $db_count = 0;
                    $lead[$row['hour']] = [
                        'sales' => $sales
                        ,'db_count' => $db_count
                    ];
                }
            }
            for($i=0; $i<=23; $i++) { //DB수량이 없어도 지출금액이 갱신되어야하기 때문에 0~23시까지 모두 저장
                // if($data['ad_id'] == 23853888597370162) dd($lead);
                $hour = $i;
                $spend = $sp_data[$i]??0;
                $count = $lead[$i]['db_count']??0;
                $sales = $lead[$i]['sales']??0;
                $margin = $sales - $spend;
                if($initZero) $margin = $sales = 0;
                if(preg_match('/cpm/i', $data['media'])) $db_count = 0;
                if($data['period_ad']) $margin = $spend * ('0.' . $data['period_ad']);
                $data['data'][] = [
                    'hour' => $hour
                    ,'spend' => $spend
                    ,'count' => $count
                    ,'sales' => $sales
                    ,'margin' => $margin
                ];
                $result = array_merge($result, $data);
            }
            // if($data['ad_id'] == 23852707712760746) dd($data);
            if(isset($data['ad_id'])) $this->db->updateInsight($data);
        }
        return $result;
    }

    public static function exception_handler($e)
    {
        //echo nl2br(print_r($e,1));
        echo ('<xmp style="color:#fff;background-color:#000;">');
        print_r($e);
        echo ('</xmp>');
        return true;
    }

    ////////////////////////////////////////////////////
    public function getMemo($data)
    {
        if(!empty($data)){
            $response = $this->db->getMemo($data);
            return $response;
        }
    }

    public function addMemo($data)
    {
        if(!empty($data)){
            return $this->db->addMemo($data);
        }
    }

    public function updateMemo($data)
    {
        if(!empty($data)){
            return $this->db->updateMemo($data);
        }
    }
}
