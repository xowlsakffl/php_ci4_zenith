<?php
///////////////////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//ini_set('max_execution_time', 1800);
set_time_limit(0);
ini_set('memory_limit', '-1');

require_once __DIR__ . '/facebook-db.php';
require_once __DIR__ . '/vendor/autoload.php';

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

class ChainsawFB
{
    private $app_id = '718087176708750'; //(주)케어랩스 //'318448081868728'; // 열혈패밀리_ver3
    private $app_secret = '81b9a694a853428e88f7c6f144efc080'; //'881a2a6c6edcc9a5291e829278cb91e2';
    private $access_token = 'EAAKNGLMV4o4BABizxUeNtE0Y5SUl2LBlOUMD5yxyENJCPTVhEMwgV7ZCix73ahxPBQosQtVGwfdVVlrMuZB6I2in7byD5FnUkl2wjUOmgIjJKjx1APYQ8mzyDSLjLL9dB2U4bZCx6bfYaKW6f55FSKCcFI7a1vUFpM8lQZCfzNqRIAt35k0tWq5SfZBuLsD40sVxK6drgDgZDZD';
    private $longLivedAccessToken = 'EAAKNGLMV4o4BAKVyB8cL6B94wkze0SflZCw5cIxyN088JSbkFZAMjZAMXZB6ruIaSKNT1fChukZCmmB4CA8ivBifix228E1cPQty0VBeYIOKocND2tlhHXUjqdOzCl3UYWebKhRjeOb7LDhi64lvfZA6Pqotjn5ahAaqMu4yExhUienOqyiQB0Snao4Txa5axQLEdOmORsAgZDZD';
    private $db;
    private $fb, $fb_app;
    private $business_id_list = ['213123902836946'];
    private $business_id;
    private $account_id;
    private $campaign_id;
    private $adset_id;
    private $ad_id;
    private $result_data = [];

    // https://developers.facebook.com/tools/debug/accesstoken
    public function __construct($bs_id = '')
    {
        @set_exception_handler(array($this, 'exception_handler'));

        try {
            $this->db = new \FBDB();
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
                // 'default_graph_version' => 'v16.0'
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
            echo '<p>longLivedAccessToken at ' . $longLivedAccessToken . '</p>';

            // 현재 토큰 정보 조회
            $accesstoken = new AccessToken($this->access_token);
            echo '<pre>'. print_r($accesstoken,1) .'</pre>';
            echo '<p>Issued at ' . $accesstoken->isLongLived() .'</p>';
            echo '<p>Expirest at ' . $accesstoken->getValue()->getExpiresAt()->format('Y-m-d\TH:i:s') .'</p>';
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
        /*
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->fb->get(
                '/'.$this->business_id.'/owned_ad_accounts?fields=account_id,name,account_status,funding_source_details,adspixels{id,name}&limit=500',
                $this->access_token
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        echo '<pre>'.print_r($response,1).'</pre>'; exit;
        $graphNode = $response->getGraphNode();
        */
        // $request = new FacebookRequest(
        //     $this->fb_app,
        //     $this->access_token,
        //     'GET',
        //     '/'.$this->business_id.'/owned_ad_accounts?fields=account_id,name,account_status,funding_source_details,adspixels{id,name}&limit=500' //3.0 에 role 사라짐
        // );
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

    public function getFBAdAccountsPerm()
    {
        $ad_accounts = $this->db->getAdAccounts(false);          // 각 광고 계정별
        $accounts = $ad_accounts->getResultArray();
        $result = array();
        $cnt = 0;
        foreach ($accounts as $row) {
            if ($row['business_id'] != $this->business_id) continue;
            $response = $this->fb->get(
                "/act_{$row['ad_account_id']}/assigned_users?business={$this->business_id}",
                $this->access_token
            );
            $edges = $response->getGraphEdge();
            do {
                $permission = 0;
                echo $this->business_id . ':' . $row['ad_account_id'] . "({$row['name']})";
                foreach ($edges as $account) {
                    $account = $account->asArray();
                    // echo "<pre>".print_r($account,1)."</pre>"; exit;
                    if (in_array($account['id'], ['482525632506639', '109156827315469', '112622820616127', '110599870819040', '100151575213916', '336797230922166'])) { //482525632506639 / 배익준, 432677113839199 / 박용태
                        if (in_array("ANALYZE", $account['tasks'])) { //Token 주인 ID
                            $permission = 1;
                            echo ' :Permission OK.';
                            $this->db->updateAdAccountPerm($row['ad_account_id'], $permission);
                            continue;
                        } else {
                            echo " :No Have Permission.";
                        }
                    }
                }
                if (!$permission) {
                    echo " :No Added User.";
                }
                echo '<br>';
            } while ($edges = $this->fb->next($edges));
        }
    }

    public function getFBAdAccounts()
    {
        $params = array(
            'fields' => array(
                UserFields::ID,
                UserFields::NAME
            )
        );
        $this->user = new User($this->business_id);

        $adaccounts = $this->user->getAdAccounts(array(), $params);
        $response = $adaccounts->getResponse()->getContent();
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

        return $result;
    }

    public function getFBAdAccountList()
    {
        $account_ids = $this->db->getAccountIdsFromCampaign();
        $data = $account_ids->getResultArray();
        foreach ($data as $row) {
            echo $row['account_id'] . '<br>';
        }
    }

    // 인스타그램 계정 목록
    function getFBInstagramAccounts()
    {
        $request = new FacebookRequest(
            $this->fb_app,
            $this->access_token,
            'GET',
            '/316991668497111/instagram_accounts?fields=id,username'
        );

        $response = $this->fb->getClient()->sendRequest($request);
        $edges = $response->getGraphEdge();
        $results = array();

        do {
            foreach ($edges as $account) {
                array_push($results, array($account['id'], $account['username']));
            }
        } while ($edges = $this->fb->next($edges));

        return $results;
    }

    // 광고 페이지 목록
    function getFBPages()
    {
        $request = new FacebookRequest(
            $this->fb_app,
            $this->access_token,
            'GET',
            '/me/accounts?fields=id,name,access_token,tasks'
        );

        $response = $this->fb->getClient()->sendRequest($request);
        $edges = $response->getGraphEdge();
        $results = array();

        do {
            foreach ($edges as $account) {
                $perm = 0;
                if (is_object($account['tasks'])) {
                    if (in_array("MANAGE", $account['tasks']->asArray()) === true || in_array("ADVERTISE", $account['tasks']->asArray()) === true) {
                        $perm = 1;
                    }
                }

                array_push($results, array($account['id'], $account['name'], $account['access_token'], $perm));
            }
        } while ($edges = $this->fb->next($edges));

        return $results;
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
    function multipleRequests($type, $errRe = false)
    {
        // exit; //TEMP
        $lists = [];
        $total_rows = count($this->result_data[$type]);
        $cnt = 1;
        foreach ($this->result_data[$type] as $name => $row) {
            if (($errRe == true && $row['StatusCode'] && $row['StatusCode'] == 500) || !$errRe) {
                $lists[$type . '_' . $name] = $this->fb->request('POST', "/{$this->account_id}/{$type}", $row);
            }
            if (($cnt % 50 === 0 || $total_rows == $cnt) && count($lists) > 0) {
                $this->sendBatchRequests($lists);
                $lists = [];
                usleep(1);
            }
            $cnt++;
        }
    }
    private function chkBatch($responses)
    {
        $errCnt = 0;
        foreach ($responses as $key => $response) {
            list($type, $name) = explode('_', $key);
            $body = json_decode($response->getBody());
            switch ($type) {
                case 'adsets':
                    $set_name = 'adset_id';
                    break;
                case 'ads':
                    $set_name = 'ad_id';
                    break;
            }
            if ($response->getHttpStatusCode() != 200) {
                if ($response->isError()) {
                    $e = $response->getThrownException();
                    // echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
                    // echo '<p>Graph Said: ' . "\n\n";
                    // var_dump($e->getResponse());
                    // exit;
                } else {
                    // echo "<p>(" . $type .':'. $name . ") HTTP status code: " . (($response->getHttpStatusCode() != 200) ? '<u style="color:red;font-weight:bold;">'.$response->getHttpStatusCode().'</u>' : $response->getHttpStatusCode()) . "<br />\n";
                    // echo "Response: " . $response->getBody() . "</p>\n\n";
                    // echo "<hr />\n\n";
                }
                $this->result_data[$type][$name]['StatusResponse'] = $response->getBody();
                $errCnt++;
            } else {
                $this->result_data['ads'][$name][$set_name] = $body->id;
            }
            $this->result_data[$type][$name]['StatusCode'] = $response->getHttpStatusCode();
        }
        if ($errCnt > 0) {
            $this->multipleRequests($type, true);
        }
        return;
    }
    private function sendBatchRequests($batch)
    {
        try {
            $responses = $this->fb->sendBatchRequest($batch);
            $this->chkBatch($responses);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }
    private function sendBatchRequest($batch)
    {
        try {
            $responses = $this->fb->sendBatchRequest($batch);
            return $responses;
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }
    public function addBatch($data)
    {
        // $this->setAdAccount('537465169783092');
        foreach ($data as $name => $row) {
            $lists[$name] = $this->fb->request('POST', "/{$this->account_id}/ads", $row);
        }
        $result = $this->sendBatchRequest($lists);
        return $result;
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // 인사이트 비동기 호출
    public function getAsyncInsights($all = false, $date = null, $edate = null)
    {
        $params = array(
            'date_preset' => AdsInsightsDatePresetValues::TODAY,
            'level' => AdsInsightsLevelValues::AD,
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
        $account_id = $this->db->getAdAccounts(true, " AND business_id = '{$this->business_id}'");
        $accounts = $account_id->getResultArray();
        $result = array();
        $cnt = 0;
        foreach ($accounts as $row) {
            $this->setAdAccount($row['ad_account_id']);
            // echo $row['name'].'('.$getSelf->{AdReportRunFields::ACCOUNT_ID} .'):';
            /** @var AdReportRun $async_job */
            $async_job = $this->account->getInsightsAsync(array(), $params);
            $getSelf = $async_job->getSelf();
            $count = 0;
            $continue = false;
            while (!$getSelf->isComplete() && $continue == false) {
                usleep(1);
                $getSelf = $async_job->getSelf();
                if ($count > 100 && !$getSelf->isComplete()) {
                    echo $row['name'] . '(' . $getSelf->{AdReportRunFields::ACCOUNT_ID} . '):';
                    echo $getSelf->{AdReportRunFields::ID} . '/';
                    echo 'Continue' . PHP_EOL;
                    ob_flush();
                    flush();
                    sleep(1);
                    $continue = true;
                }
                $count++;
            }
            if ($continue) continue;
            ob_flush();
            flush();
            usleep(1);
            $insights = $getSelf->getInsights();
            $getResponse = $insights->getResponse();
            $response = $getResponse->getContent();
            echo '<pre>'.print_r($response,1).'</pre>'; exit;
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
            $cnt++;
            // echo '<pre>'.print_r($result,1).'</pre>'; exit;
        }
        $this->db->insertAsyncInsights($result);
        if ($all) {
            $this->updateAds($result);
            $this->updateAdCreatives($result);
            $this->updateAdsets($result);
            $this->updateCampaigns($result);
        }
        return $result;
    }

    public function updateAdCreatives($data = null)
    {
        $adcreatives = $this->getAdCreatives($data);
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
            $ad_ids = $this->db->getAdsWithAccount();
            $data = $ad_ids->getResultArray();
        }
        $result = array();
        $cnt = 0;
        $_ids = [];
        $ids = [];
        foreach ($data as $row) $_ids[] = $row['ad_id'];
        if (count($_ids)) {
            $ids = array_unique($_ids);
            foreach ($ids as $ad_id) {
                $this->setAdId($ad_id);
                $ads = $this->ad->getSelf(array(), $params);
                $response = $ads->getData();
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
            foreach ($ids as $adset_id) {
                $this->setAdsetId($adset_id);
                $adset = $this->adset->getSelf(array(), $params);
                $response = $adset->getData();
                // echo '<pre>'.print_r($response,1).'</pre>'; exit;
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
            foreach ($ids as $campaign_id) {
                $this->setCampaignId($campaign_id);
                $campaign = $this->campaign->getSelf(array(), $params);
                $response = $campaign->getData();
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
        $result = array();
        $cnt = 0;
        foreach ($ad_accounts->getResultArray() as $row) {
            // $row['ad_account_id'] = 796319794698742;
            $this->setAdAccount($row['ad_account_id']);
            $ads = $this->account->getAds(array(), $params);
            $response = $ads->getResponse()->getContent();
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

    public function landingGroup($title)
    {
        if (!$title) {
            return null;
        }
        preg_match_all('/.+\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        $db_prefix = '';
        switch ($matches[9][0]) {
            case 'fhr':
                $media = '핫이벤트 룰렛';
                if ($matches[1][0]) {
                    $db_prefix = 'app_';
                }
                break;
            case 'fhrcpm':
                $media = '핫이벤트 룰렛_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'app_';
                }
                break;
            case 'fer':
                $media = '이벤트 랜딩';
                if ($matches[1][0]) {
                    $db_prefix = 'evt_';
                }
                break;
            case 'fercpm':
                $media = '이벤트 랜딩_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'evt_';
                }
                break;
            case 'fhsp':
                $media = '핫이벤트 스핀';
                if ($matches[1][0]) {
                    $db_prefix = 'event_';
                }
                break;
            case 'fhspcpm':
                $media = '핫이벤트 스핀_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'event_';
                }
                break;
            case 'ler':
                $media = '이벤트 잠재고객';
                if ($matches[1][0]) {
                    $db_prefix = 'evt_';
                }
                break;
            case 'lercpm':
                $media = '이벤트 잠재고객_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'evt_';
                }
                break;
            case 'jhr':
                $media = '핫이벤트 잠재고객';
                if ($matches[1][0]) {
                    $db_prefix = 'APP_';
                }
                break;
            case 'jhrcpm':
                $media = '핫이벤트 잠재고객_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'APP_';
                }
                break;
            case 'wfhr':
                $media = '오토랜딩';
                if ($matches[1][0]) {
                    $db_prefix = 'wr_';
                }
                break;
            case 'cpm':
                $media = 'cpm';
                $db_prefix = '';
                break;
            default:
                $media = '';
                $db_prefix = '';
                break;
        }
        $result = array(
            'name' => '', 'media' => '', 'db_prefix' => '', 'event_id' => '', 'app_id' => '', 'site' => '', 'db_price' => 0, 'period_ad' => ''
        );
        if ($media) {
            $result['name']         = $matches[0][0];
            $result['media']        = $media;
            $result['db_prefix']    = $db_prefix;
            $result['event_id']     = $matches[1][0];
            $result['app_id']       = $db_prefix . $matches[1][0];
            $result['site']         = $matches[3][0];
            $result['db_price']     = $matches[6][0];
            $result['period_ad']    = $matches[12][0];
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
        if (!$ads->num_rows) {
            return null;
        }
        $i = 0;
        foreach ($ads->getResultArray() as $row) {
            $landing = $this->landingGroup($row['ad_name']);
            if ($landing['media']) {
                $result[$i]['date'] = $date;
                $result[$i]['ad_id'] = $row['ad_id'];
                $result[$i]['spend'] = $row['spend'];
                $result[$i]['name'] = $landing['name'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['app_id'] = $landing['app_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['db_price'] = $landing['db_price'];
                $result[$i]['media_code'] = $landing['media_code'];
                $result[$i]['period_ad'] = $landing['period_ad'];
                $result[$i]['media'] = $landing['media'];
                $result[$i]['db_prefix'] = $landing['db_prefix'];
                if (!preg_match('/cpm/', $landing['media'])) {
                    if (!$landing['app_id']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): APP_ID 미입력' . PHP_EOL;
                    if (!$landing['db_price']) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): DB단가 미입력' . PHP_EOL;
                }
                $i++;
            } else {
                if (preg_match('/&[a-z]+/', $row['ad_name'])) $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 인식 오류' . PHP_EOL;
            }
        }
        $this->exception_handler($error);
        if (is_array($result)) {
            foreach ($result as $i => $data) {
                $result[$i]['count'] = 0;
                $result[$i]['sales'] = 0;
                $result[$i]['margin'] = 0;
                $sales = 0;
                if ($data['app_id']) {
                    $dbcount = $this->db->getDbCount($data['ad_id'], $date);
                    $rows = $this->db->getAppSubscribe($data, $date);
                    $result[$i]['count'] = $rows;
                    $db_price = $data['db_price'];
                    if ($dbcount['db_price'] && $date != date('Y-m-d'))
                        $db_price = $result[$i]['db_price'] = $dbcount['db_price'];
                    /* 수익, 매출액 계산 */
                    /*=============================== 2018-11-19
                    fhrcpm /fhspcpm/ jhrcpm
                    유효db수는 불러오지만 수익,매출0

                    cpm
                    우효db 0 / 수익/ 매출0

                    ^25 = *0.25
                    */
                    $initZero = false;
                    if (preg_match('/cpm/i', $data['media'])) { //app_id 가 있는 cpm (fhrm, fhspcpm, jhrcpm)의 계산을 무효화
                        $initZero = true;
                    }
                    if ($db_price) {
                        if (!$initZero)
                            $sales = $db_price * $rows;
                        $insight_data = new stdClass();
                        $insight_data->ad_id = $data['ad_id'];
                        $insight_data->date = $date;
                        $insight_data->data['sales'] = $sales;
                        $result[$i]['sales'] = $sales;
                        $this->db->updateInsight($insight_data);
                    }
                    if (!$initZero)
                        $result[$i]['margin'] = $sales - $data['spend'];
                }
                if ($data['period_ad']) {
                    $result[$i]['margin'] = $data['spend'] * ('0.' . $data['period_ad']);
                }
            }
            $this->db->insertLeadsCount($result, $date);
            // usort($result, function($a, $b) {
            //  return $b['event_id'] <=> $a['event_id'];
            // });
            if (!$rows) {
                unset($result[$i]);
            }
        }
        return $result;
    }

    // 광고세트 조회(Ads에서 조회)
    public function getAdsetsFromAds($datepreset = '')
    {
        $query = $this->updateQuery($datepreset);
        $adset_ids = $this->db->getAds($query);
        foreach ($adset_ids->getResultArray() as $row) {
            $result[] = $row;
        }
        $this->updateAdsets($result);
        return $result;
    }

    // 캠페인 조회(AdSets에서 조회)
    public function getCampaignsFromAdSets($datepreset = '')
    {
        $query = $this->updateQuery($datepreset);
        $campaign_ids = $this->db->getAdSets($query);
        foreach ($campaign_ids->getResultArray() as $row) {
            $result[] = $row;
        }
        $this->updateCampaigns($result);
        return $result;
    }
    private function updateQuery($datepreset)
    {
        switch (strtolower($datepreset)) {
            case 'today':
                $query = "AND DATE(update_date) = CURDATE()";
                break;
            case 'yesterday':
                $query = "AND DATE(update_date) = CURDATE() - INTERVAL 1 DAY";
                break;
            case 'fromyesterday':
                $query = "AND DATE(update_date) >= CURDATE() - INTERVAL 1 DAY";
                break;
            case 'fromweek':
                $query = "AND DATE(update_date) >= CURDATE() - INTERVAL 1 WEEK";
                break;
            default:
                $query = '';
                break;
        }
        return $query;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    // 페이징 전용 함수
    private function getFBRequest_CURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        return $data;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getAdAccounts()
    {
        return $this->db->getAdAccounts();
    }

    public function getInstagramAccounts()
    {
        return $this->db->getInstagramAccounts();
    }

    public function getAdPages()
    {
        return $this->db->getAdPages();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    // 각 페이지별 폼 목록
    public function getLeadgenForms()
    {
        $pages = $this->db->getAdPages(); //from DB
        $result = array();

        foreach ($pages->getResultArray() as $row) { //while $row['page_id']
            if ($row['is_admin'] != 1) {
                continue;
            }

            $page_access_token = $row['access_token'];
            Api::init($this->app_id, $this->app_secret, $page_access_token, false); //v2.11 /page/leadgen_forms 접근 시 page_access_token 사용 하여야 함..
            $page = new Page($row['page_id']);
            $forms = $page->getLeadgenForms();
            $response = $forms->getResponse()->getContent();
            $result = array_merge($result, $response['data']);
        }

        return $result;
    }

    // 잠재 고객 작성 정보 불러오기
    public function getLeadgens($from = "-1 day", $to = null)
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
            $to_dt = new DateTime($to);
            $to_dt->format('Y-m-d 00:00:00');
            $to_date = $to_dt->getTimestamp();

            $params['filtering'][1] = array(
                'field'     => 'time_created',
                'operator'  => 'LESS_THAN',
                'value'     => $to_date
            );
        }

        $fields = array(
            LeadFields::AD_ID,
            LeadFields::AD_NAME,
            LeadFields::ADSET_ID,
            LeadFields::ADSET_NAME,
            LeadFields::CAMPAIGN_ID,
            LeadFields::CAMPAIGN_NAME,
            LeadFields::IS_ORGANIC,
            LeadFields::FIELD_DATA,
            LeadFields::CREATED_TIME,
        );

        $forms = $this->getLeadgenForms();   // 모든 폼 별
        Api::init($this->app_id, $this->app_secret, $this->access_token, false); //v2.11 /page/leadgen_forms 접근 시 page_access_token 사용 하여야 한다는데 왜 하위 edge는 다시 user_access_token 인가..?
        foreach ($forms as $key => $row) {
            if ($row['status'] != 'ACTIVE') {
                continue;
            }

            $form = new LeadgenForm($row['id']);
            $leads = $form->getLeads($fields, $params);

            $response = $leads->getResponse()->getContent();
            if (!count($response['data'])) {
                continue;
            }

            $data['id'] = $row['id'];
            $data['data'] = $response['data'];

            if (isset($response['paging'])) {
                $url = @$response['paging']['next'];

                while ($url) {
                    $curl = $this->getFBRequest_CURL($url);

                    if (isset($curl['data'])) {
                        $data['data'] = array_merge($data['data'], $curl['data']);
                    }

                    $url = isset($curl['paging']['next']) ? $curl['paging']['next'] : null;
                }
            }

            array_push($result, $data);
        }
        //      print_r($result);
        $this->db->insertLeads($result);
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
            $to_dt = new DateTime($to);
            $to_dt->format('Y-m-d 00:00:00');
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
        $ad_ids = $this->db->getAdsByAdAccountId($from_date, $to_date); //from DB
        foreach ($ad_ids->getResultArray() as $row) { //while $row['page_id']
            // $this->grid($row); continue;
            if ($row['leadgen_id'] == null || $row['leadgen_id'] == '' || $row['effective_status'] != 'ACTIVE'/* || strtotime($row['created_time']) <= strtotime('-26 month')*/) {
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

    public function getTitleChar()
    {
        $ads = $this->db->getAds();
        foreach ($ads as $data) {
            if (preg_match('/\#[0-9\_]+.+\*[0-9]+.+\&[a-z]+.*/i', $data['ad_name'])) {
                $this->db->updateUseLanding($data['ad_id']);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function updateAdAccounts()
    {
        $accounts = $this->getFBAccounts();

        $this->db->updateAdAccounts($accounts);
        // echo '<pre>'.print_r($accounts,1).'</pre>';
        // $accounts = $this->getFBInstagramAccounts();
        // $this->db->updateInstagramAccounts($accounts);

        // // $accounts = $this->getFBPages();
        // $this->db->updatePages($accounts);
        return $accounts;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

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

    function setAdsetStatus($id, $status)
    {
        if ($status == 'ACTIVE') {
            $status = AdSet::STATUS_ACTIVE;
        } else {
            $status = AdSet::STATUS_PAUSED;
        }

        $this->setAdsetId($id);
        $adset = $this->adset->updateSelf(array(), array(
            AdSet::STATUS_PARAM_NAME => $status,
        ));
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            $this->db->setAdsetStatus($id, $status);
            return true;
        }

        return null;
    }

    function setAdStatus($id, $status)
    {
        if ($status == 'ACTIVE') {
            $status = Ad::STATUS_ACTIVE;
        } else {
            $status = Ad::STATUS_PAUSED;
        }

        $this->setAdId($id);
        $ad = $this->ad->updateSelf(array(), array(
            Ad::STATUS_PARAM_NAME => $status,
        ));
        $ad = $ad->getData();
        if ($ad['success'] == 1) {
            $this->db->setAdStatus($id, $status);
            return true;
        }

        return null;
    }

    public function updateCampaignBudget($data)
    {
        //한개씩 캠페인 일일예산 수정
        $campaign_id = $data['id'];
        $budget = $data['budget'];
        if (!$campaign_id) {
            return false;
        }
        $row = $this->db->getCampaign($campaign_id);

        // 기본 정보 설정
        $campaignfields = array(CampaignFields::DAILY_BUDGET => $budget);

        $campaign = new Campaign($campaign_id);
        // $campaign->setData($campaignfields);
        $campaign = $campaign->updateSelf(array(), $campaignfields);
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
        $adset_id = $data['id'];
        $budget = $data['budget'];
        if (!$adset_id || $budget < 1000) {
            return false;
        }
        $row = $this->db->getAdSet($adset_id);

        switch ($row['budget_type']) {
            case 'lifetime':
                $field = AdSetFields::LIFETIME_BUDGET;
                break;
            case 'daily':
                $field = AdSetFields::DAILY_BUDGET;
                break;
        }
        // 기본 정보 설정
        $adsetfields = array($field => $budget);

        $adset = new AdSet($adset_id);
        // $adset->setData($adsetfields);
        $adset = $adset->updateSelf(array(), $adsetfields);
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            $this->db->updateAdSetBudget($adset_id, $budget);
            return $adset_id;
        }
        return null;
    }

    public function updateName($data)
    {
        if (!trim($data['id']) || !trim($data['type'])) {
            return false;
        }
        switch ($data['type']) {
            case 'campaign':
                $result = $this->updateCampaignName($data);
                break;
            case 'adset':
                $result = $this->updateAdsetName($data);
                break;
            case 'ad':
                $result = $this->updateAdName($data);
                break;
        }
        return $result;
    }

    private function updateCampaign($fields, $data)
    {
        if (!is_array($fields) || !is_array($data)) {
            return null;
        }
        if (!$data['id']) {
            return null;
        }
        $campaign = new Campaign($data['id']);
        // $campaign->setData($fields);
        $campaign = $campaign->updateSelf(array(), $data);
        $campaign = $campaign->getData();
        if ($campaign['success'] == 1) {
            return $data['id'];
        }
    }

    private function updateAdset($fields, $data)
    {
        if (!is_array($fields) || !is_array($data)) {
            return null;
        }
        if (!$data['id']) {
            return null;
        }
        $adset = new AdSet($data['id']);
        // $adset->setData($fields);
        $adset = $adset->updateSelf(array(), $data);
        $adset = $adset->getData();
        if ($adset['success'] == 1) {
            return $data['id'];
        }
    }

    private function updateAd($fields, $data)
    {
        if (!is_array($fields) || !is_array($data)) {
            return null;
        }
        if (!$data['id']) {
            return null;
        }
        $ad = new Ad($data['id']);
        // $ad->setData($fields);
        $ad = $ad->updateSelf(array(), $data);
        $ad = $ad->getData();
        if ($ad['success'] == 1) {
            return $data['id'];
        }
    }

    private function updateCampaignName($data)
    {
        $fields = array(CampaignFields::NAME => $data['name']);
        $id = $this->updateCampaign($fields, $data);
        if ($id) {
            $this->db->updateCampaignName($data);
            return $id;
        }
        return null;
    }

    private function updateAdsetName($data)
    {
        $fields = array(AdSetFields::NAME => $data['name']);
        $id = $this->updateAdset($fields, $data);
        if ($id) {
            $this->db->updateAdsetName($data);
            return $id;
        }
        return null;
    }

    private function updateAdName($data)
    {
        $fields = array(AdFields::NAME => $data['name']);
        $id = $this->updateAd($fields, $data);
        if ($id) {
            $this->db->updateAdName($data);
            return $id;
        }
        return null;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function exception_handler($e)
    {
        //echo nl2br(print_r($e,1));
        echo ('<xmp style="color:#fff;background-color:#000;">');
        print_r($e);
        echo ('</xmp>');
        return true;
    }


    public function grid($data, $link = null)
    {
        if (empty($data)) {
            echo '<p>null data</p>';
            return;
        }
        $table = '';
        foreach ($data as $row) {
            if (is_array($row)) {
                $table .= '<tr>';
                foreach ($row as $key => $var) {
                    if ($link) {
                        foreach ($link as $k => $v) {
                            if ($k == $key) {
                                $var = str_replace('{' . $k . '}', $var, $v);
                            }
                        }
                    }
                    $table .= '<td>' . (is_object($var) ? $var->load() : $var) . '</td>';
                }
                $table .= '</tr>';
            }
        }
        if (isset($row) && is_array($row)) {
            $thead = '<thead><tr>';
            foreach ($row as $key => $tmp) {
                $thead .= '<th>' . $key . '</th>';
            }
            $thead .= '</tr></thead>';
        } else {
            $thead = '<thead><tr>';
            $table = '<tr>';
            foreach ($data as $k => $v) {
                $thead .= '<th>' . $k . '</th>';
                $table .= '<td>' . $v . '</td>';
            }
            $thead .= '</tr></thead>';
            $table .= '</tr>';
        }
        echo '<table class="_dev_util_grid" border="1">' . $thead . $table . '</table>';
    }

    public function getFBAdLeadLanding()
    {
        //잠재고객 > app_subscribe 테이블로 이동
        $ads = $this->db->getFBAdLead();
        if (!$ads->num_rows) {
            return null;
        }
        $i = 0;
        foreach ($ads->getResultArray() as $row) {
            $landing = $this->landingGroup($row['ad_name']);
            $i = 0;

            //이름
            $full_name = $row['full_name'];
            if (!$full_name || $full_name == null) {
                $full_name = trim($row['first_name'] . ' ' . $row['last_name']);
            }

            //성별
            if ($row['gender'] == "female") {
                $gender = "여자";
            } elseif ($row['gender'] == "male") {
                $gender = "남자";
            } else {
                $gender = $row['gender'];
            }

            //나이
            if ($row['date_of_birth']) {
                $birthyear = date("Y", strtotime($row['date_of_birth']));
                $nowyear = date("Y");
                $age = $nowyear - $birthyear + 1;
            } else {
                $age = "";
            }

            //전화번호
            $phone = str_replace("+82010", "010", $row['phone_number']);
            $phone = str_replace("+8210", "010", $phone);
            $phone = str_replace("-", "", $phone);


            //주소
            if ($row['street_address']) {
                $addr = $row['street_address'];
            } else {
                $addr = "";
            }



            //추가질문
            preg_match_all("/0 => \'(.*)\',/iU", $row['field_data'], $match);
            if ($match[1][0]) {
                $add1 = $match[1][0];
            } else {
                $add1 = "";
            }
            if ($match[1][1]) {
                $add2 = $match[1][1];
            } else {
                $add2 = "";
            }
            if ($match[1][2]) {
                $add3 = $match[1][2];
            } else {
                $add3 = "";
            }
            if ($match[1][3]) {
                $add4 = $match[1][3];
            } else {
                $add4 = "";
            }
            if ($match[1][4]) {
                $add5 = $match[1][4];
            } else {
                $add5 = "";
            }
            if ($match[1][5]) {
                $add6 = $match[1][5];
            } else {
                $add6 = "";
            }
            if ($match[1][6]) {
                $add6 .=  "/" . $match[1][6];
            } else {
                $add6 = $add6;
            }
            if ($match[1][7]) {
                $add6 .=  "/" . $match[1][7];
            } else {
                $add6 = $add6;
            }
            if ($match[1][8]) {
                $add6 .=  "/" . $match[1][8];
            } else {
                $add6 = $add6;
            }
            if ($match[1][9]) {
                $add6 .=  "/" . $match[1][9];
            } else {
                $add6 = $add6;
            }
            if ($match[1][10]) {
                $add6 .=  "/" . $match[1][10];
            } else {
                $add6 = $add6;
            }


            if ($landing['media']) {
                $result[$i]['group_id'] = $landing['app_id'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['full_name'] = $this->db->real_escape_string($full_name);
                $result[$i]['gender'] = $this->db->real_escape_string($gender);
                $result[$i]['age'] = $this->db->real_escape_string($age);
                $result[$i]['phone'] = $this->db->real_escape_string($phone);
                $result[$i]['add1'] = $this->db->real_escape_string($add1);
                $result[$i]['add2'] = $this->db->real_escape_string($add2);
                $result[$i]['add3'] = $this->db->real_escape_string($add3);
                $result[$i]['add4'] = $this->db->real_escape_string($add4);
                $result[$i]['add5'] = $this->db->real_escape_string($add5);
                $result[$i]['add6'] = $this->db->real_escape_string($add6);
                $result[$i]['addr'] = $this->db->real_escape_string($addr);
                $result[$i]['reg_date'] = $row['created_time'];
                $result[$i]['ad_id'] = $row['ad_id'];
                $result[$i]['id'] = $row['id'];

                //              if($result[$i]['add2']){
                //                  echo "group_id:".$result[$i]['group_id'] ."/"."site:".$landing['site']."/"."first_name:".$result[$i]['full_name']."/"."gender:".$result[$i]['gender']."/"."age:".$result[$i]['age']."/"."phone:".$result[$i]['phone']."/".$result[$i]['add1']."/".$result[$i]['add2']."/".$result[$i]['add3']."/".$result[$i]['add4']."/".$result[$i]['add5']."/".$result[$i]['add6']."<br/>";
                //              }
                $i++;
            }
            // echo '<pre>'.print_r($result,1).'</pre>';

            if (is_array($result)) {
                $this->db->insertApp_subscribe($result);
            }

            // return $result;
        }
    }


    private function setCurl($url, $forms = [])
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_HEADER, 0);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $curl->setOpt(CURLOPT_COOKIESESSION, TRUE);
        $curl->setCookieJar(__DIR__ . '/cookie.txt');
        $curl->setCookieFile(__DIR__ . '/cookie.txt');
        $curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36');
        $curl->setReferrer('https://www.facebook.com/');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        if (count($forms) > 1)
            $curl->post($url, $forms);
        else
            $curl->get($url);
        // if(preg_match('/302/', $curl->responseHeaders['status-line'])) {
        //     return $this->setCurl($curl->responseHeaders['location']);
        // }
        $result['headers'] = $curl->responseHeaders;
        $result['response'] = $curl->response;

        return $result;
    }

    public function getInvoices()
    {
        $invoices = $this->db->getInvoices();
        foreach ($invoices->getResultArray() as $row) {
            $result[] = $row;
        }
        $this->grid($result);
    }

    public function downloadInvoices($sdate, $edate)
    {
        $url = "https://www.facebook.com/login.php";
        $login_url = "https://www.facebook.com/login/device-based/regular/login/?login_attempt=1&lwv=100";
        //https://www.facebook.com/login/device-based/regular/login/?login_attempt=1&lwv=100
        $curl = $this->setCurl($url);

        if (!preg_match('@<form .*?</form>@si', $curl['response'], $matches)) {
            exit;
        }
        $form = $matches[0];
        $cnt = preg_match_all('@(<input (.*?)/?>|<textarea .*?name\s*=\s*(([\'"])(.*?)\4|([^\s>]+)).*?>(.*?)</textarea>)@si', $form, $matches);
        for ($i = 0; $i < $cnt; $i++) {
            if ($matches[2][$i] != '') {
                $cnt2 = preg_match_all('@([a-z]+)\s*=\s*(([\'"])(.*?)\3|([^\s]+))@i', $matches[2][$i], $values);
                for ($j = 0; $j < $cnt2; $j++) {
                    @$input->{$values[1][$j]} = ($values[4][$j] != '') ? $values[4][$j] : $values[5][$j];
                }
            } else {
                $input->name = ($matches[5][$i] != '') ? $matches[5][$i] : $matches[6][$i];
                $input->value = $matches[7][$i];
            }
            $inputs[] = $input;
            unset($input);
        }
        $forms = [];
        foreach ($inputs as $row) {
            if ($row->name) {
                $value = $row->value;
                $forms = array_merge($forms, [$row->name => $value]);
            }
        }
        $forms = array_merge($forms, ['email' => 'june@june44.com', 'pass' => 'Juny1716#f']);

        $curl = $this->setCurl($login_url, $forms);
        $date['start'] = strtotime($sdate . ' 00:00:00');
        $date['end'] = strtotime($edate . ' 23:59:59');
        $accounts = $this->getAdAccounts();
        foreach ($accounts->getResultArray() as $row) {
            $invoice_url = "https://business.facebook.com/ads/manage/invoices_generator/?ts={$date['start']}&time_end={$date['end']}&act={$row['ad_account_id']}&report=true&format=csv";
            $curl = $this->setCurl($invoice_url);
            echo $row['name'] . ':' . $invoice_url . ' - <br>' . PHP_EOL;
            if (!preg_match('/Error/s', $curl['response'])) {
                echo '<pre>' . $curl['response'] . '</pre>' . PHP_EOL;
                $this->insertInvoices($row['ad_account_id'], $curl['response']);
            }
            ob_flush();
            flush();
            usleep(1000);
        }
    }

    private function insertInvoices($ad_account_id, $data)
    {
        if ($data) {
            $data = explode("\n", $data);
            $result['ad_account_id'] = $ad_account_id;
            foreach ($data as $row) {
                if (preg_match('/^20[0-9]{2}\-[0-9]{2}-[0-9]{2}/', $row)) {
                    $row = preg_replace_callback('/\"(.+)\"/', function ($matches) {
                        return preg_replace('/\"|,/', '', $matches[0]);
                    }, $row);
                    $val = explode(',', $row);
                    if (count($val) == 5) {
                        list($result['date'], $result['tx_id'], $result['type'], $result['amount'], $result['currency']) = explode(',', $row);
                    } else {
                        list($result['date'], $result['tx_id'], $result['amount'], $result['currency']) = explode(',', $row);
                    }
                    $this->db->insertInvoice($result);
                }
            }
        }
    }

    public function deleteBusiness($bid)
    {
        $this->db->deleteBusiness($bid);
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
