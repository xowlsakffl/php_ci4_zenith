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

use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\Authentication\AccessToken;

use FacebookAds\Api;
use FacebookAds\Object\User;
use FacebookAds\Object\Fields\UserFields;
use FacebookAds\Object\AdReportRun;
use FacebookAds\Object\Fields\AdReportRunFields;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\CampaignDatePresetValues;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetDatePresetValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Object\Values\AdObjectives;
use FacebookAds\Object\Values\BillingEvents;
use FacebookAds\Object\Values\OptimizationGoals;
use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\Fields\TargetingFields;

use FacebookAds\Object\AdCreative;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\AdCreativeLinkDataChildAttachment;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;
use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\AdCreativeObjectStorySpec;
use FacebookAds\Object\Fields\ObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdCreativeLinkDataFields;
use FacebookAds\Object\Fields\AdCreativeLinkDataChildAttachmentFields;
//use FacebookAds\Object\Values\CallToActionTypes;

use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Values\AdDatePresetValues;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FacebookAds\Object\Values\AdsInsightsLevelValues;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;
use FacebookAds\Object\Values\AdsInsightsBreakdownsValues;

use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;

use FacebookAds\Object\AdsPixel;
use FacebookAds\Object\Fields\AdsPixelsFields;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceSubtypes;

use FacebookAds\Object\LeadgenForm;
use FacebookAds\Object\Fields\LeadFields;
use FacebookAds\Object\Page;

use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Search\TargetingSearchTypes;

use Google\Cloud\Translate\TranslateClient;

use Curl\Curl;
use FacebookAds\Logger\CurlLogger;

class ZenithFB
{
    private $app_id = '718087176708750'; //(주)케어랩스 //'318448081868728'; // 열혈패밀리_ver3
    private $app_secret = '81b9a694a853428e88f7c6f144efc080'; //'881a2a6c6edcc9a5291e829278cb91e2';
    private $access_token = 'EAAKNGLMV4o4BABCrZBY6AsWfpadUc2fumCOKxStBHzdFsJnPy8kOgxfdPTUMOmnNwZA8vRzmEMKDmncluzmNro1ZB0YHZCea3zuz8c6RErqSNDnMzH7SnuQFTyQbMqCPE4AKvyMoh1GQU3chYpjVfYM7skvy8Ltu7yqpZAgAj8mZBVw6hySyJ61kolHQEnMHiA4urTrDkbksONZC2xaGsbN';
    private $longLivedAccessToken = 'EAAKNGLMV4o4BAGHXK97JQ9yz8CLZAfF4WlUcg8yrSZBV6j8w4FWMvQyuZCAxdrmBiP6K2kcrR2esqvYOsZAGxiIo10taHkSv9cvrx3IZAXlIGtIrNV1U9Td91ZAithdZBQNhGFnrbXlVRkUvJP9ZA58NBtF0oea17LkMaxPCZAZA7V6qglHTMdX0Q6NovfdIbhB41p7hLKZAv55sZAaUBWQlUpBA';
    private $db;
    private $fb, $fb_app;
    private $business_id_list = [
        '213123902836946' //케어랩스7
        ,'2859468974281473' //케어랩스5
        ,'316991668497111' //열혈패밀리
    ];
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
            $this->db = new FBDB();
            // $account = $this->db->getAccessToken();

            // $this->account_id = "act_" . $account['ad_account_id'];
            // $this->access_token = $account['access_token'];
            if($this->longLivedAccessToken)
                $this->access_token = $this->longLivedAccessToken;
            Api::init($this->app_id, $this->app_secret, $this->access_token, false);

            // $this->account = new AdAccount($this->account_id);

            $this->fb = new Facebook([
                'app_id' => $this->app_id,
                'app_secret' => $this->app_secret,
                'default_access_token' => $this->access_token,
                'default_graph_version' => 'v16.0'
            ]);
            $this->fb_app = new FacebookApp($this->app_id, $this->app_secret);
            if ($bs_id) $this->business_id = $bs_id;
            else $this->business_id = $this->business_id_list[0];
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
      
    // 일반 엑세스 토큰을 연장시킴
    public function getLongLivedAccessToken()
    {
        try { //EAAEhoHjMl7gBAJVuAZCygZCHp11NFWNmf6Hng4KSCDBZCEakZC7yEkZAnAkqvXw9wSAqWX3Qg20r0rzoQORglAp1RMNdqHEeQ4Gy1GZCBlVaDIwvI4BiQzBNavFDRWk49adliwGauowZCc6j3DoMKyDuenoSa0iBVHh9t2hMJ35Pfd8Y1XcHRBy
            // 주석된 부분이 토큰을 연장하는 부분
            $oAuth2Client = $this->fb->getOAuth2Client();
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($this->access_token);
            $accesstoken = new AccessToken($longLivedAccessToken);
            $this->access_token = $longLivedAccessToken;
            CLI::write("longLivedAccessToken at ". CLI::color($longLivedAccessToken, "white"), "yellow");

            // 현재 토큰 정보 조회
            $accesstoken = new AccessToken($this->access_token);
            /*
            echo '<pre>'. print_r($accesstoken,1) .'</pre>';
            echo '<p>Issued at ' . $accesstoken->isLongLived() .'</p>';
            echo '<p>Expirest at ' . $accesstoken->getValue()->getExpiresAt()->format('Y-m-d\TH:i:s') .'</p>';
            */
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // 페이스북 광고 계정 목록
    /*  1 = ACTIVE
        2 = DISABLED
        101 = CLOSED */
      
    public function getFBAccounts()
    {
        $response = $this->fb->get(
            '/' . $this->business_id . '/owned_ad_accounts?fields=account_id,name,account_status,funding_source_details,adspixels{id,name}&limit=20',
            $this->access_token
        );
        $edges = $response->getGraphEdge();
        $results = array();
        // echo '<pre>'.print_r($edges,1).'</pre>'; exit;

        do {
            foreach ($edges as $account) {
                $pixel_id = 'NULL';
                $funding_source = 'NULL';
                if ($account['adspixels']) {
                    $pixel_id = $account['adspixels'][0]['id'];
                }
                if ($account['funding_source_details']) {
                    $funding_source = $account['funding_source_details']['display_string'];
                }
                array_push($results, array($this->business_id, $account['account_id'], $account['name'], $funding_source, $account['account_status'], $pixel_id));
            }
        } while ($edges = $this->fb->next($edges));

        return $results;
    }

    // 비지니스 설정   
    public function setBusinessId($bs_id=null) {
        if(is_null($bs_id)) return;
        $this->business_id = $bs_id;
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

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // 인사이트 비동기 호출
      
    public function getAsyncInsights($all = "false", $date = null, $edate = null)
    {
        $params = array(
            'date_preset' => AdsInsightsDatePresetValues::TODAY,
            'level' => AdsInsightsLevelValues::AD,
            'breakdowns' => AdsInsightsBreakdownsValues::HOURLY_STATS_AGGREGATED_BY_AUDIENCE_TIME_ZONE,
            'filtering' => array(
                array(
                    'field'     => 'ad.impressions',
                    'operator'  => 'GREATER_THAN',
                    'value'     => 0
                ),
                array(
                    'field'     => 'ad.spend',
                    'operator'  => 'GREATER_THAN',
                    'value'     => 0
                ),
                array(
                    'field'     => 'ad.effective_status',
                    'operator'  => 'IN',
                    'value'     => ['ACTIVE', 'ADSET_PAUSED', 'ARCHIVED', 'CAMPAIGN_PAUSED', 'DELETED', 'DISAPPROVED', 'IN_PROCESS', 'PAUSED', 'PENDING_BILLING_INFO', 'PENDING_REVIEW', 'PREAPPROVED', 'WITH_ISSUES']
                )
            ),
            'fields' => array(
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
            )
        );
        if ($date != null) {
            if ($edate == null) {
                $edate = $date;
            }
            $params['time_range'] = array('since' => $date, 'until' => $edate);
            unset($params['date_preset']);
        }
        $account_id = $this->db->getAdAccounts(true);
        $accounts = $account_id->getResultArray();
        $total = $account_id->getNumRows();
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 계정에 대한 광고인사이트 수신을 시작합니다.", "light_red");
        $return = array();
        foreach ($accounts as $row) {
            $result = [];
            $this->setAdAccount($row['ad_account_id']);
            $async_job = $this->account->getInsightsAsync(array(), $params);
            $getSelf = $async_job->getSelf();
            $count = 0;
            $continue = false;
            while (!$getSelf->isComplete() && $continue == false) {
                usleep(1);
                $getSelf = $async_job->getSelf();
                if ($count > 100 && !$getSelf->isComplete()) {
                    // echo $row['name'] . '(' . $getSelf->{AdReportRunFields::ACCOUNT_ID} . '):';
                    // echo $getSelf->{AdReportRunFields::ID} . '/';
                    // echo 'Continue' . PHP_EOL;
                    ob_flush(); flush(); sleep(1);
                    $continue = true;
                }
                $count++;
            }
            CLI::showProgress($step++, $total);
            if ($continue) continue;
            ob_flush(); flush(); usleep(1);
            $insights = $getSelf->getInsights();
            $getResponse = $insights->getResponse();
            $response = $getResponse->getContent();
            // if(count($response['data'])) { echo '<pre>'.print_r($response,1).'</pre>'; exit; }
            $result = array_merge($result, $response['data']);
            if (isset($response['paging'])) {
                $url = @$response['paging']['next'];
                while ($url) {
                    $data = $this->getFBRequest_CURL($url);
                    if (isset($data['data'])) $result = array_merge($result, $data['data']);
                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;
                }
            }
            $this->db->insertAsyncInsights($result);
            if ($all == "true") {
                $this->updateAds($result);
                $this->updateAdCreatives($result);
                $this->updateAdsets($result);
                $this->updateCampaigns($result);
            }
            $return = array_merge($return, $result);
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
            $results = array();
            // print_r($this->fb->next($data)); exit;
            // echo '<pre>'.print_r($data,1).'</pre>'; exit;
            // do {
                $campaigns = $data['campaigns']['data'];
                if(!@count($campaigns)) continue;
                foreach($campaigns as $campaign) {
                    $adsets = $campaign['adsets']['data'];
                    if(!@count($adsets)) continue;
                    foreach($adsets as $adset) {
                        $ads = $adset['ads']['data'];
                        if(!@count($ads)) continue;
                        foreach($ads as $ad) {
                            $adcreatives = $ad['adcreatives']['data'];
                            if(!@count($adcreatives)) continue;
                            for($i=0; $i<count($adcreatives); $i++) $adcreatives[$i]['ad_id'] = $ad['id'];
                            $this->updateAdcreatives(null, $adcreatives);
                        }
                        $this->db->updateAds($ads);
                    }
                    $this->db->updateAdsets($adsets);
                }
                $this->db->updateCampaigns($campaigns);
            // } while ($data = $this->fb->next($data));
           
            // print_r($result);
        }
    }
      
    public function updateAdCreatives($data = null, $adcreatives = null)
    {
        if(is_null($adcreatives)) $adcreatives = $this->getAdCreatives($data);
        $i = 0;
        foreach ($adcreatives as $data) {
            $result[$i]['adcreative_id'] = $data['id'];
            $result[$i]['ad_id'] = $data['ad_id'];
            // $data['thumbnail_url'] = str_replace('w=64&h=64', 'w=550&h=550', $data['thumbnail_url']); //썸네일 크기
            $result[$i]['thumbnail_url'] = $data['thumbnail_url'];
            $result[$i]['object_type'] = $data[AdCreativeFields::OBJECT_TYPE];
            // echo '<pre>'.print_r($data,1).'</pre>'; ob_flush(); flush(); usleep(1);
            if (in_array($data[AdCreativeFields::CALL_TO_ACTION_TYPE], ["LEARN_MORE", "APPLY_NOW"])) {
                $object_story_spec = $data[AdCreativeFields::OBJECT_STORY_SPEC];
                $video_data = $object_story_spec[AdCreativeObjectStorySpecFields::VIDEO_DATA];
                $link_data = $object_story_spec[AdCreativeObjectStorySpecFields::LINK_DATA];
                switch ($result[$i]['object_type']) {
                    case 'SHARE':
                    case 'STATUS':
                        if (is_array($link_data)) { //슬라이드형
                            $result[$i]['link'] = $link_data['link'];
                        }
                        break;
                    case 'VIDEO':
                        if (is_array($video_data)) { //비디오
                            $result[$i]['link'] = $video_data['call_to_action']['value']['link'];
                        }
                        break;
                    default:

                        break;
                }
                if ($result[$i]['link'] == 'https://fb.me/' || $result[$i]['link'] == 'http://fb.me/')
                    $result[$i]['link'] = '';
            }
            $i++;
        }
        $this->db->updateAdCreatives($result);
        return $result;
    }
      
    public function getAdCreatives($data = null)
    {
        $result = array();
        $params = array(
            'thumbnail_width' => 250,
            'thumbnail_height' => 250,
        );
        $fields = array(
            AdCreativeFields::ID,
            AdCreativeFields::BODY,
            AdCreativeFields::OBJECT_TYPE,
            AdCreativeFields::OBJECT_URL,
            AdCreativeFields::THUMBNAIL_URL,
            AdCreativeFields::IMAGE_FILE,
            AdCreativeFields::IMAGE_URL,
            AdCreativeFields::CALL_TO_ACTION_TYPE,
            AdCreativeFields::OBJECT_STORY_SPEC
        );
        if ($data == null) {
            $ad_ids = $this->db->getAdsWithAccount();
            foreach ($ad_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        foreach ($data as $row) {
            if ($row['id'])
                $row['ad_id'] = $row['id'];
            $this->setAdId($row['ad_id']);
            $adcrearives = $this->ad->getAdCreatives($fields, $params);
            $response = $adcrearives->getResponse()->getContent();
            if (!count($response['data'])) {
                continue;
            }
            $response['data'][0]['ad_id'] = $row['ad_id'];
            $result = array_merge($result, $response['data']);
            // echo '<pre>'.nl2br(print_r($response['data'],1)).'</pre>';
        }
        return $result;
    }

    // 개별 광고 조회 업데이트
      
    public function updateAds($data = null)
    {
        $params = array(
            'fields' => array(
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
            )
        );
        if ($data == null) {
            $ad_ids = $this->db->getAds();
            $data = $ad_ids->getResultArray();
        }
        $result = array();
        $cnt = 0;
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['ad_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 광고 데이터 수신을 시작합니다.", "light_red");
            foreach ($ids as $ad_id) {
                $this->setAdId($ad_id);
                $ads = $this->ad->getSelf(array(), $params);
                $response = $ads->getData();
                CLI::showProgress($step++, $total);
                $result[] = $response;
            }
            $this->db->updateAds($result);
        }
        return $result;
    }

    // 개별 광고세트 조회 업데이트   
    public function updateAdsets($data = null)
    {
        $params = array(
            'fields' => array(
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
            )
        );
        if ($data == null) {
            $adset_ids = $this->db->getAdSetsWithAccount();
            foreach ($adset_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        $result = array();
        $cnt = 0;
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['adset_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 광고그룹 데이터 수신을 시작합니다.", "light_red");
            foreach ($ids as $adset_id) {
                $this->setAdsetId($adset_id);
                $adset = $this->adset->getSelf(array(), $params);
                $response = $adset->getData();
                // echo '<pre>'.print_r($response,1).'</pre>'; exit;
                CLI::showProgress($step++, $total);
                $result[] = $response;
            }
            $this->db->updateAdsets($result);
        }
        return $result;
    }

    // 개별 캠페인 조회 업데이트
    public function updateCampaigns($data = null)
    {
        $params = array(
            'fields' => array(
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
            )
        );
        if ($data == null) {
            $campaign_ids = $this->db->getCampaignsWithAccount();
            foreach ($campaign_ids->getResultArray() as $row) {
                $data[] = $row;
            }
        }
        $result = array();
        $cnt = 0;
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['campaign_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            $total = count($ids);
            $step = 1;
            CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 캠페인 데이터 수신을 시작합니다.", "light_red");
            foreach ($ids as $campaign_id) {
                $this->setCampaignId($campaign_id);
                $campaign = $this->campaign->getSelf(array(), $params);
                $response = $campaign->getData();
                CLI::showProgress($step++, $total);
                $result[] = $response;
            }
            $this->db->updateCampaigns($result);
        }
        return $result;
    }

    // 광고 조회   
    public function getAds()
    {
        $params = array(
            // 'time_range' => ['since'=>date('Y-m-d', strtotime('-2 year')), 'until'=>date('Y-m-d')],
            // 'date_preset' => 'today',
            AdFields::EFFECTIVE_STATUS => array(
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
            ),
            /*
            AdFields::STATUS => array(
                'ACTIVE',
                'PAUSED'
            ),*/
            'fields' => array(
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
            )
        );

        $ad_accounts = $this->db->getAdAccounts();          // 각 광고 계정별
        $total = $ad_accounts->getNumRows();
        $step = 1;
        $result = array();
        $cnt = 0;
        CLI::write("[".date("Y-m-d H:i:s")."]"."{$total}개의 계정에 대한 광고 데이터 수신을 시작합니다.", "light_red");
        foreach ($ad_accounts->getResultArray() as $row) {
            // $row['ad_account_id'] = 796319794698742;
            $this->setAdAccount($row['ad_account_id']);
            $ads = $this->account->getAds(array(), $params);
            $response = $ads->getResponse()->getContent();
            CLI::showProgress($step++, $total);
            $result = array_merge($result, $response['data']);
            if (isset($response['paging'])) {
                $url = @$response['paging']['next'];
                while ($url) {
                    $data = $this->getFBRequest_CURL($url);

                    if (isset($data['data'])) {
                        $result = array_merge($result, $data['data']);
                    }

                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;
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

    // 각 AdID별 폼 목록
      
    public function getAdLead($from = "-1 day", $to = null)
    {
        $result = array();
        $params = array();

        // 시작 날짜
        $from_dt = new DateTime($from);
        $from_dt->format('Y-m-d 00:00:00');
        $from_date = $from_dt->getTimestamp();

        $params['filtering'] = array(
            array(
                'field'     => 'time_created',
                'operator'  => 'GREATER_THAN',
                'value'     => $from_date
            ),
        );

        // 끝 날짜
        if ($from != "-1 day" && $to != null) {
            $to_dt = new DateTime($to." 23:59:59");
            $to_dt->format('Y-m-d H:i:s');
            $to_date = $to_dt->getTimestamp();

            $params['filtering'][1] = array(
                'field'     => 'time_created',
                'operator'  => 'LESS_THAN',
                'value'     => $to_date
            );
        }

        $fields = array(
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
        );
        
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
            if (!count($response['data'])) {
                continue;
            }
            $result = array_merge($result, $response['data']);
            if (isset($response['paging'])) {
                $url = @$response['paging']['next'];

                while ($url) {
                    $data = $this->getFBRequest_CURL($url);

                    if (isset($data['data'])) {
                        $result = array_merge($result, $data['data']);
                    }

                    $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;
                }
            }
        }
        $this->db->insertAdLeads($result);
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
        // echo '<pre>'.print_r($accounts,1).'</pre>';
        // $accounts = $this->getFBInstagramAccounts();
        // $this->db->updateInstagramAccounts($accounts);

        // // $accounts = $this->getFBPages();
        // $this->db->updatePages($accounts);
        return $result;
    }

    //광고 상태 업데이트
    function setCampaignStatus($id, $status)
    {
        if ($status == 'ACTIVE') {
            $status = Campaign::STATUS_ACTIVE;
        } else {
            $status = Campaign::STATUS_PAUSED;
        }

        $this->setCampaignId($id);
        $campaign = $this->campaign->updateSelf(array(), array(
            Campaign::STATUS_PARAM_NAME => $status,
        ));
        $campaign = $campaign->getData();
        if ($campaign['success'] == 1) {
            $this->db->setCampaignStatus($id, $status);
            return true;
        }

        return null;
    }
    
    public function landingGroup($title)
    {
        if (!$title) return null;
        preg_match_all('/.+\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        switch ($matches[9][0]) {
            case 'fer': $media = '이벤트 랜딩'; break;
            case 'fercpm': $media = '이벤트 랜딩_cpm'; break;
            case 'ler': $media = '이벤트 잠재고객'; break;
            case 'lercpm': $media = '이벤트 잠재고객_cpm'; break;
            case 'cpm': $media = 'cpm'; break;
            default: $media = '';break;
        }
        if ($media) {
            $result = [
                'name' => $matches[0][0]
                ,'media' => $media
                ,'event_seq' => $matches[1][0]
                ,'site' => $matches[3][0]
                ,'db_price' => $matches[6][0]
                ,'period_ad' => $matches[12][0]
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
            $landing = $this->landingGroup($row['ad_name']); //소재와 이벤트 매칭
            $data = [];
            $data = [
                 'date' => $date
                ,'ad_id' => $row['ad_id']
            ];
            $data = @array_merge($data, $landing);
            if (!is_null($landing) && !preg_match('/cpm/', $landing['media'])) {
                if (!$landing['event_seq']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 이벤트번호 미입력' . PHP_EOL;
                if (!$landing['db_price']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): DB단가 미입력' . PHP_EOL;
            }
            if(is_null($landing) && preg_match('/&[a-z]+/', $row['ad_name'])) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 인식 오류' . PHP_EOL;
            if(count($error)) foreach($error as $err) CLI::write("{$err}", "light_purple");
            if(is_null($landing)) continue;
            $dp = $this->db->getDbPrice($data);
            $leads = $this->db->getAppSubscribe($data);
            $cpm = false;
            if(is_null($leads) && $data['media'] === 'cpm') $cpm = true;
            if(!is_null($leads)) {
                if(!$leads->getNumRows() && !$cpm) continue;
            }
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
            $period_margin = [];
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
            if(!is_null($leads)) {
                foreach($leads->getResultArray() as $row) {
                    $sales = $margin = 0;
                    $spend = $sp_data[$row['hour']];
                    $db_count = $row['db_count'];
                    if($db_price) $sales = $db_price * $db_count;
                    $margin = $sales - $spend;
                    if($initZero) $margin = $sales = 0;
                    if($data['media'] === 'cpm') $db_count = 0;
                    if($data['period_ad']) $margin = $spend * ('0.' . $data['period_ad']);
                    $data['data'][] = [
                        'hour' => $row['hour']
                        ,'spend' => $spend
                        ,'count' => $db_count
                        ,'sales' => $sales
                        ,'margin' => $margin
                    ];
                    $result = array_merge($result, $data);
                }
            }
            if(isset($data['ad_id']))
                $this->db->updateInsight($data);
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
        $response = $this->db->getMemo($data);
        return $response;
    }

    public function addMemo($data)
    {
        return $this->db->addMemo($data);
    }

    public function updateMemo($data)
    {
        return $this->db->updateMemo($data);
    }
}