<?php
require_once __DIR__ . '/kmdb.php';
set_time_limit(0);
ini_set('memory_limit', '-1');

use CodeIgniter\CLI\CLI;

class ChainsawKM
{
    private $app_id = '243424';
    private $app_key = '500a99d34478a54c7c4fbfc04ff90512';
    private $client_secret = 'V1lTM4d4FrLoxVhnISLqGaLdhwsvcGTT';
    private $oauth_url = 'https://kauth.kakao.com/oauth';
    private $code;
    private $host = 'https://apis.moment.kakao.com';
    private $db;
    private $access_token;
    private $refresh_token;
    private $ad_account_id;

    public function __construct()
    {
        // @set_exception_handler(array($this, 'exception_handler'));
        $this->db = new KMDB();
        try {
            $token = $this->db->get_token();
            if ($token['access_token']) {
                $this->access_token = $token['access_token'];
                $this->refresh_token = $token['refresh_token'];
                if (time() >= strtotime($token['expires_time'] . ' -2 hours')) $this->refresh_token();
            } else {
                if (!$_GET['code'])
                    $this->get_code();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
        
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function oauth()
    {
        if ($_GET['code']) {
            $this->koauth($_GET['code']);
        }
    }
    public function get_code()
    {
        $redirect_code_uri = urlencode("http://www.chainsaw.co.kr/kmmanager/oauth");
        $param = "?client_id={$this->app_key}&redirect_uri={$redirect_code_uri}&response_type=code";
        $response = $this->curl($this->oauth_url . '/authorize' . $param, NULL, NULL);
    }

    public function koauth($code = '')
    {
        $data = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->app_key,
            'redirect_uri' => 'http://www.chainsaw.co.kr/kmmanager/oauth',
            'code' => $_GET['code'], //get_code() 에서 받은 code 값
            //'client_secret' => $this->client_secret
        );
        $data = http_build_query($data);
        $response = $this->curl($this->oauth_url . '/token', NULL, $data, 'POST');
        if (isset($response['access_token'])) {
            $this->db->update_token($response);
            echo date('[H:i:s]') . '토큰 생성 완료' . PHP_EOL;
            ob_flush();
            flush();
            usleep(1);
        }
    }

    public function refresh_token()
    {
        $data = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->app_key,
            'refresh_token' => $this->refresh_token
            //'client_secret' => $this->client_secret
        );
        $data = http_build_query($data);
        $response = $this->curl($this->oauth_url . '/token', NULL, $data, 'POST');
        if (isset($response['access_token']) || isset($response['refresh_token'])) {
            $this->db->update_token($response);
            echo date('[H:i:s]') . '토큰 업데이트 완료' . PHP_EOL;
            ob_flush();
            flush();
            usleep(1);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 1. 광고계정 */
    private function getAdAccountList()
    { //1.1 광고계정 리스트 조회
        $request = 'adAccounts';
        $param = array('config' => 'ON,OFF,DEL'); //enum{ON, OFF, DEL}
        $result = $this->getCall($request);
        return $result; //[id] => 41250 [name] => 강남조은눈안과 [memberType] => MASTER [config] => ON
    }

    private function getAdAccount($adAccountId = '')
    { //1.2. 광고계정 조회
        $request = "adAccounts/{$adAccountId}";
        $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request);
        return $result; //Array ( [id] => 41250 [name] => 강남조은눈안과 [ownerCompany] => Array ( [businessRegistrationNumber] => 220-88-36643 [name] => 주식회사 케어랩스 ) [advertiser] => Array ( [businessRegistrationNumber] => 104-90-96978 [name] => 강남조은눈안과 ) [type] => BUSINESS [config] => ON [isAdminStop] => [isOutOfBalance] => [statusDescription] => 운영중 )
    }

    private function getBalance($adAccountId = '')
    { //1.3. 광고계정 실시간 잔액 조회
        $request = 'adAccounts/balance';
        $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param);
        return $result; //Array ( [id] => 41250 [cash] => 4159386 [freeCash] => 0 )
    }

    private function getBizRight($adAccountId = '')
    { //1.4. 광고계정 영업권 조회
        $request = 'adAccounts/bizRight';
        $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param);
        return $result; //Array ( [id] => 0 [bizRightCompanyName] => 주식회사 케어랩스 )
    }

    private function setAdAccountOnOff($adAccountId = '', $config = 'ON')
    { //1.5. 광고계정 ON/OFF 수정
        $request = 'adAccounts/onOff';
        $this->ad_account_id = $adAccountId;
        $data = array('config' => $config);
        $result = $this->getCall($request, $param, $data, 'PUT');
        return $result;
    }

    public function updateAdAccounts()
    { //전체 광고계정 업데이트
        $adAccountList = $this->getAdAccountList();
        $i = 0;
        $total = count($adAccountList['content']);  
        CLI::write("[".date("Y-m-d H:i:s")."]"."전체 광고계정 수신을 시작합니다.", "light_red");
        foreach ($adAccountList['content'] as $row) {
            CLI::showProgress($i, $total);
            $data[$i] = $this->getAdAccount($row['id']);
            $data[$i]['memberType'] = $row['memberType'];
            $i++;
        }
        $this->db->updateAdAccounts($data);
        return $data;
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 2. 캠페인 */
    private function getCampaigns($adAccountId)
    { //2.1. 캠페인 리스트 조회
        $request = 'campaigns';
        $this->ad_account_id = $adAccountId;
        $param = array('config' => 'ON,OFF,DEL');
        $result = $this->getCall($request, $param);
        return $result; //Array ( [id] => 17550 [name] => ★썸네일피드 [type] => DISPLAY [userConfig] => ON )
    }

    private function getCampaign($campaignId, $adAccountId = '')
    { //2.2. 캠페인 조회
        $request = "campaigns/{$campaignId}";
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, '', '', 'GET', true);
        return $result; //Array ( [id] => 17550 [name] => ★썸네일피드 [adPurposeType] => INCREASE_WEB_VISITING [dailyBudgetAmount] => [config] => ON [isDailyBudgetAmountOver] => [statusDescription] => 운영중 )
    }

    public function setCampaignOnOff($campaignId, $config = 'ON', $adAccountId = '')
    { //2.3. 캠페인 ON/OFF 수정
        $request = 'campaigns/onOff';
        $data = array('id' => $campaignId, 'config' => $config);
        $campaign = $this->db->getCampaignById($campaignId);
        if ($campaign['ad_account_id']) $this->ad_account_id = $campaign['ad_account_id'];
        $result = $this->getCall($request, NULL, $data, 'PUT');
        if ($result['http_code'] == 200)
            $this->db->setCampaignOnOff($campaignId, $config);
        return $result;
    }

    private function setCampaignDailyBudgetAmount($campaignId = '', $dailyBudgetAmount = null)
    { //2.4. 캠페인 일예산 수정
        $request = 'campaigns/dailyBudgetAmount';
        $data = array('id' => $campaignId, 'dailyBudgetAmount' => $dailyBudgetAmount);
        $campaign = $this->db->getCampaignById($campaignId);
        if ($campaign['ad_account_id']) $this->ad_account_id = $campaign['ad_account_id'];
        $result = $this->getCall($request, NULL, $data, 'PUT', true);
        return $result;
    }

    public function setCampaign($param = [], $adAccountId = '')
    { //2. 캠페인 수정하기 
        $request = 'campaigns';
        if (!isset($param['id']) || !$param['id']) $this->error('캠페인 아이디를 지정해주십시오.');
        if ($adAccountId)
            $this->ad_account_id = $adAccountId;
        else {
            $campaign = $this->db->getCampaignById($param['id']);
            if ($campaign['ad_account_id']) $this->ad_account_id = $campaign['ad_account_id'];
        }
        $data = $this->getCampaign($param['id'], $this->ad_account_id);
        if (isset($data['trackId']))
            $param['trackId'] = $data['trackId'];
        if (isset($data['dailyBudgetAmount']))
            $param['dailyBudgetAmount'] = $data['dailyBudgetAmount'];
        $result = $this->getCall($request, NULL, $param, 'PUT');
        if ($result['id'] == $param['id'])
            $this->db->setCampaign($param);
        return $result;
    }

    public function setCampaignAutoBudgetOnOff($campaignId = '', $config = 'ON')
    { //Ai On/Off
        $result = $this->db->setCampaignAutoBudgetOnOff($campaignId, $config);
        return $result;
    }

    public function getBulkCampaigns($adAccountId)
    {
        $request = 'https://apis.moment.kakao.com/openapi/beta/campaigns/search';
        $this->ad_account_id = $adAccountId;
        $page = 0;
        $put_data = [["filterType" => "USER_CONFIG", "values" => ["ON", "OFF"]]];
        while ($page >= 0) {
            // echo '<p>'.$page.'</p>';
            $param = ['page' => $page, 'size' => 1000];
            $response = $this->getCall($request, $param, $put_data, 'PUT');
            // echo '<pre>'.print_r($response,1).'</pre>';
            if (count($response['content']) > 0) {
                foreach ($response['content'] as $row) {
                    // print_r($row);
                    $this->db->updateCampaign($row);
                }
            }
            $page = ($page + 1 < $response['totalPages']) ? $page + 1 : -1;
        }
        return $result;
    }

    public function updateCampaigns()
    { //전체 캠페인 업데이트
        $accounts = $this->db->getAdAccounts();
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."전체 캠페인 수신을 시작합니다.", "light_red");
        foreach ($accounts->getResultArray() as $account) {
            $campaignList = $this->getCampaigns($account['id']);
            if (count($campaignList['content']) > 0) {
                $i = 0;
                $total = count($campaignList['content']);
                foreach ($campaignList['content'] as $row) {
                    CLI::showProgress($step++, $total);
                    if ($row['id'] && $row['config'] != 'DEL') {
                        $campaign = $this->getCampaign($row['id']);
                        $data[$account['id']][$i] = $campaign;
                        if (isset($campaign['extras']) && $campaign['extras']['detailCode'] == '31001') {
                            $delete = ['id' => $row['id'], 'config' => 'DEL'];
                            $this->db->setCampaign($delete);
                            continue;
                            //echo "{$account['id']} - 캠페인({$row['id']}) : 삭제" . PHP_EOL;
                        }
                        $data[$account['id']][$i]['type'] = $row['type'];
                        $i++;
                    } else if ($row['config'] == 'DEL') {
                        $delete = ['id' => $row['id'], 'config' => 'DEL'];
                        $this->db->setCampaign($delete);
                        continue;
                        //echo "{$account['id']} - 캠페인({$row['id']}) : 삭제" . PHP_EOL;
                    }
                }
            }
        }
        // echo '<pre>'.print_r($data,1).'</pre>';
        $this->db->updateCampaigns($data);
        return $data;
    }

    public function updateBulkCampaigns()
    { //전체 캠페인 업데이트
        $accounts = $this->db->getAdAccounts();
        foreach ($accounts->getResultArray() as $account) {
            $this->getBulkCampaigns($account['id']);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 3. 광고그룹 */
    public function getAdGroups($campaignId = '', $adAccountId = '')
    { //3.1. 광고그룹 리스트 조회
        $request = 'adGroups';
        $param = array('campaignId' => $campaignId, 'config' => 'ON,OFF,DEL');
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, '', 'GET', false);
        return $result; //Array ( [id] => 35443 [name] => 3514 [type] => DISPLAY [userConfig] => ON )
    }

    public function getAdGroup($adGroupId, $adAccountId = '')
    { //3.2. 광고그룹 조회
        $request = "adGroups/{$adGroupId}";
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, '', '', 'GET', false);
        // echo '<pre>'.print_r($result,1).'</pre>'; exit;
        return $result; //Array ( [id] => 35443 [name] => 3514 [pricingType] => CPC [pacing] => QUICK [bidStrategy] => MANUAL [dailyBudgetAmount] => 100000 [bidAmount] => 200 [config] => ON [isDailyBudgetAmountOver] => [isValidPeriod] => 1 [statusDescription] => 운영중 )
    }

    public function setAdGroupOnOff($adGroupId = '', $config = 'ON', $adAccountId = '')
    { //3.3. 광고그룹 ON/OFF 수정
        $request = 'adGroups/onOff';
        $data = array('id' => $adGroupId, 'config' => $config);
        $adAccountId = $this->db->getAdAccountIdByAdGroupId($adGroupId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, NULL, $data, 'PUT');
        if ($result['http_code'] == 200)
            $this->db->setAdGroupOnOff($adGroupId, $config);
        sleep(1);
        return $result;
    }

    public function setAdGroupAiOnOff($adGroupId = '', $config = 'ON', $type)
    { //Ai On/Off
        $param = array('adGroupId' => $adGroupId);
        $data = array('config' => $config);
        $result = $this->db->setAdGroupAiOnOff($adGroupId, $config, $type);
        return $result;
    }

    public function setAdGroupsAiRun()
    { //광고그룹 AI 실행 예산수정 aiConfig2 - Ai 2
        $adGroups = $this->db->getAdGroups(['ON'], " AND aiConfig2 = 'ON'");
        $step = 1;
        $total = $adGroups->getNumRow();
        CLI::write("[".date("Y-m-d H:i:s")."]"."광고그룹 AI 업데이트를 시작합니다.", "light_red");
        foreach ($adGroups->getResultArray() as $adGroup) {
            CLI::showProgress($step++, $total); 
            $rs = ['budget'=>'예산변경 대상 아님', 'bid'=>'입찰가변경 대상 아님'];
            echo '['.$adGroup['account_name']. '][' .$adGroup['id'] . ']' . $adGroup['name'] . '/';
            $adAccountId = $adGroup['ad_account_id'];
            if ($adAccountId) $this->ad_account_id = $adAccountId;
            // $result = $adGroup['id'];
            if ($adGroup['dailyBudgetAmount'] >= 200000) { //그룹예산 변경
                $data = ['id' => $adGroup['id'], 'budget' => 200000, 'type' => 'adgroup'];
                $result = $this->setDailyBudgetAmount($data);
                if($result == $adGroup['id']) {
                    $rs['budget'] = '예산변경 성공';
                } else {
                    $rs['budget'] = '예산변경 실패';
                }
                sleep(1);
            }
            $bidAmount = "";
            if($adGroup['bidStrategy'] != 'AUTOBID') { //그룹 입찰가 변경
                $bidAmount = $adGroup['bidAmount'];
                if($adGroup['bidAmount'] >= 750) { //현재 입찰가에서 200원 낮춰서 적용
                    $bidAmount -= 200;
                } else if($adGroup['bidAmount'] >= 650) { //현재 입찰가에서 150원 낮춰서 적용
                    $bidAmount -= 150;
                } else if($adGroup['bidAmount'] >= 500) { //현재 입찰가에서 100원 낮춰서 적용
                    $bidAmount -= 100;
                } else if($adGroup['bidAmount'] >= 180) { //현재 입찰가에서 50원 낮춰서 적용
                    $bidAmount -= 50;
                }
                if($bidAmount >= 130 && $adGroup['bidAmount'] >= 180) { //수정 입찰가가 400원 이상이고, 최초 입찰가가 500원 이상일 때
                    $result = $this->setAdGroupBidAmount($adGroup['id'], $bidAmount, $adAccountId);
                    if($result == $adGroup['id']) { //수정 성공
                        $rs['bid'] = '입찰가변경 성공';
                    } else {
                        $rs['bid'] = '입찰가변경 실패';
                    }
                }
                sleep(1);
            }
            ob_flush();
            flush();
            echo @number_format($adGroup['dailyBudgetAmount']) . ' - ' . $rs['budget'] . '<br>' . PHP_EOL;
            echo @number_format($adGroup['bidAmount']).'>'.@number_format($bidAmount) . ' - ' . $rs['bid'] . '<br><br>' . PHP_EOL;
        }
    }

    private function setAdGroupDailyBudgetAmount($adGroupId = '', $dailyBudgetAmount = '10000')
    { //3.4. 광고그룹 일예산 수정
        $request = 'adGroups/dailyBudgetAmount';
        $data = array('id' => $adGroupId, 'dailyBudgetAmount' => $dailyBudgetAmount);
        $adAccountId = $this->db->getAdAccountIdByAdGroupId($adGroupId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        return $result;
    }

    public function setAdGroupBidAmount($adGroupId = '', $bidAmount = '10000', $adAccountId = '')
    { //3.5. 광고그룹 최대 입찰금액 수정
        $request = 'adGroups/bidAmount';
        $data = array('id' => $adGroupId, 'bidAmount' => $bidAmount);
        $adAccountId = $this->db->getAdAccountIdByAdGroupId($adGroupId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        if ($result['http_code'] == 200) {
            $this->db->setAdGroupBidAmount($adGroupId, $bidAmount);
            $result = $adGroupId;
        }
        return $result;
    }

    private function setAdGroupPacing($adGroupId = '', $pacing = 'NORMAL', $adAccountId = '')
    { //3.6. 광고그룹 게재방식 수정
        $request = 'adGroups/pacing';
        $param = array('adGroupId' => $adGroupId);
        $data = array('pacing' => $pacing);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        return $result;
    }

    public function setAdGroup($param = [], $adAccountId = '')
    { //3. 광고그룹 수정하기
        $request = 'adGroups';
        if (!isset($param['id']) || !$param['id']) $this->error('광고그룹 아이디를 지정해주십시오.');
        if ($adAccountId)
            $this->ad_account_id = $adAccountId;
        else {
            $adAccountId = $this->db->getAdAccountIdByAdGroupId($param['id']);
            if ($adAccountId) $this->ad_account_id = $adAccountId;
        }
        $adGroup = $this->getAdGroup($param['id'], $this->ad_account_id);
        // echo '<pre>'.print_r($adGroup,1).'</pre>';
        if (!$adGroup['deviceTypes'] && $adGroup['allAvailableDeviceType']) $deviceTypes = ['ANDROID', 'IOS', 'PC'];
        else $deviceTypes = $adGroup['deviceTypes'];
        $ag = [
            'campaign' => $adGroup['campaign'], 'placements' => $adGroup['placements'], 'allAvailableDeviceType' => $adGroup['allAvailableDeviceType'], 'allAvailablePlacement' => $adGroup['allAvailablePlacement'], 'deviceTypes' => $deviceTypes, 'targeting' => $adGroup['targeting'], 'adult' => $adGroup['adult'], 'dailyBudgetAmount' => $adGroup['dailyBudgetAmount'], 'bidStrategy' => $adGroup['bidStrategy'], 'pricingType' => $adGroup['pricingType'], 'smartMessage' => $adGroup['smartMessage'], 'bidAmount' => $adGroup['bidAmount'], 'pacing' => ($adGroup['pacing'] ? $adGroup['pacing'] : 'NONE'), 'schedule' => $adGroup['schedule']
        ];
        if ($adGroup['adServingCategories'])
            $ag['adServingCategories'] = $adGroup['adServingCategories'];
        if ($adGroup['messageSendingInfo'])
            $ag['messageSendingInfo'] = $adGroup['messageSendingInfo'];
        $data = array_merge($param, $ag);
        // echo '<pre>'.print_r($param,1).'</pre>';
        $result = $this->getCall($request, NULL, $data, 'PUT');
        // echo '<pre>'.print_r($result,1).'</pre>';
        if ($result['id'] == $param['id'])
            $this->db->setAdGroup($param);
        return $result;
    }

    public function getBulkAdGroups($adAccountId)
    {
        $request = 'https://apis.moment.kakao.com/openapi/beta/adGroups/search';
        $this->ad_account_id = $adAccountId;
        $page = 0;
        $put_data = [["filterType" => "USER_CONFIG", "values" => ["ON", "OFF"]]];
        while ($page >= 0) {
            // echo '<p>'.$page.'</p>';
            $param = ['page' => $page, 'size' => 1000];
            $response = $this->getCall($request, $param, $put_data, 'PUT');
            echo '<pre>' . print_r($response, 1) . '</pre>';
            if (count($response['content']) > 0) {
                foreach ($response['content'] as $row) {
                    // echo '<pre>'.print_r($row,1).'</pre>';
                    $row['campaign_id'] = $row['campaignId'];
                    $this->db->updateAdGroup($row);
                }
            }
            $page = ($page + 1 < $response['totalPages']) ? $page + 1 : -1;
        }
        return $result;
    }

    public function updateAdGroups()
    { //전체 광고그룹 업데이트
        $campaigns = $this->db->getCampaigns(["ON", "OFF"]);
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."광고그룹 수신을 시작합니다.", "light_red");
        foreach ($campaigns->getResultArray() as $campaign) {
            //echo "{$campaign['id']}<br>";
            $adGroupList = $this->getAdGroups($campaign['id'], $campaign['ad_account_id']);
            if (count($adGroupList['content']) > 0) {
                $i = 0;
                $total = count($adGroupList['content']);
                foreach ($adGroupList['content'] as $row) {
                    CLI::showProgress($step++, $total);
                    if ($row['id'] && $row['config'] != 'DEL') { //echo $row['id'].'<br>';
                        $adgroup = $this->getAdGroup($row['id']);
                        $data[$campaign['id']][$i] = $adgroup;
                        if (isset($adgroup['extras']) && $adgroup['extras']['detailCode'] == '32026') {
                            $delete = ['id' => $row['id'], 'config' => 'DEL'];
                            $this->db->setAdgroup($delete);
                            continue;
                            //echo "{$campaign['ad_account_id']} - 광고그룹({$row['id']}) : 삭제" . PHP_EOL;
                        }
                        $data[$campaign['id']][$i]['type'] = $row['type'];
                        $i++;
                    } else if ($row['config'] == 'DEL') {
                        $delete = ['id' => $row['id'], 'config' => 'DEL'];
                        $this->db->setAdgroup($delete);
                        continue;
                        //echo "{$campaign['ad_account_id']} - 광고그룹({$row['id']}) : 삭제" . PHP_EOL;
                    }
                }
            }
        }
        //echo '<pre>'.print_r($data,1).'</pre>';
        $this->db->updateAdGroups($data);
        return $data;
    }

    public function updateBulkAdGroups()
    { //전체 광고그룹 업데이트
        $accounts = $this->db->getAdAccounts();
        foreach ($accounts->getResultArray() as $account) {
            $this->getBulkAdGroups($account['id']);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 4. 소재 */
    public function getCreatives($adGroupId, $adAccountId = '')
    { //4.1. 소재 리스트 조회
        $request = 'creatives';
        $param = array('adGroupId' => $adGroupId, 'config' => 'ON,OFF,DEL');
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param);
        return $result; //Array ( [id] => 716600 [name] => 3514 [type] => DISPLAY [config] => ON )
    }

    public function getCreative($creativeId, $adAccountId = '')
    { //4.2. 소재 조회
        $request = "creatives/{$creativeId}";
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, '', '', 'GET', true);
        print_r($result['landingInfo']);
        return $result; //Array ( [id] => 716600 [creativeId] => 1470241 [name] => 3514 [format] => THUMBNAIL_FEED [bidAmount] => 130 [landingUrl] => http://hotevent.hotblood.co.kr/index.php/app_3514 [frequencyCap] => 2 [config] => ON [reviewStatus] => APPROVED [modifyReviewStatus] => NONE [statusDescription] => 운영중 )
    }

    public function setCreativeOnOff($creativeId, $config = 'ON')
    { //4.3. 소재 ON/OFF
        $request = 'creatives/onOff';
        $data = array('id' => $creativeId, 'config' => $config);
        $adAccountId = $this->db->getAdAccountIdByCreativeId($creativeId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        if ($result['http_code'] == 200)
            $this->db->setCreativeOnOff($creativeId, $config);
        sleep(1);
        return $result;
    }

    public function setCreativeAiOnOff($creativeId = '', $config = 'ON', $type)
    { //Ai On/Off
        $param = array('creativeId' => $creativeId);
        $data = array('config' => $config);
        $result = $this->db->setCreativeAiOnOff($creativeId, $config, $type);
        return $result;
    }

    public function setCreativeBidAmount($creativeId = '', $bidAmount = '0')
    { //4.4. 소재 입찰금액 수정
        $request = 'creatives/bidAmount';
        $data = array('id' => $creativeId, 'bidAmount' => $bidAmount);
        $adAccountId = $this->db->getAdAccountIdByCreativeId($creativeId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        if ($result['http_code'] == 200) {
            $this->db->setCreativeBidAmount($creativeId, $bidAmount);
            $result = $creativeId;
        }
        return $result;
    }

    public function setCreativeFrequencyCap($creativeId = '', $frequencyCap = '5')
    { //4.5. 소재 프리퀀시 캡 수정 //v4에서 사용불가
        $request = 'creatives';
        $creative = $this->db->getCreativeById($creativeId);
        $data = array('id' => $creativeId, 'adGroupId' => $creative['adgroup_id'], 'format' => $creative['format'], 'frequencyCap' => $frequencyCap);
        $adAccountId = $this->db->getAdAccountIdByCreativeId($creativeId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param, $data, 'PUT');
        if ($result['http_code'] == 200) {
            $this->db->setCreativeFrequencyCap($creativeId, $frequencyCap);
            $result = $creativeId;
        }
        return $result;
    }

    public function setCreative($param = [], $adAccountId = '')
    { //3. 소재 수정하기
        $request = 'creatives';
        if (!isset($param['id']) || !$param['id']) $this->error('소재 아이디를 지정해주십시오.');
        if ($adAccountId)
            $this->ad_account_id = $adAccountId;
        else {
            $adAccountId = $this->db->getAdAccountIdByCreativeId($param['id']);
            if ($adAccountId) $this->ad_account_id = $adAccountId;
        }

        $creative = $this->getCreative($param['id'], $this->ad_account_id);

        // echo '<pre>' . print_r($creative, 1) . '</pre>';
        $cv = [
            'adGroupId' => $creative['adGroupId'], 'format' => $creative['format']
        ];

        if($creative['format'] == 'IMAGE_NATIVE'){
            if($creative['title'])
                $cv['title'] = $creative['title']; 

            if($creative['profileName'])
                $cv['profileName'] = $creative['profileName']; 

            if($creative['description'])
                $cv['description'] = $creative['description']; 

            if($creative['actionButton'])
                $cv['actionButton'] = $creative['actionButton']; 
        }
        
        if($creative['altText'])
            $cv['altText'] = $creative['altText'];
        if($creative['landingInfo']['landingType'] == 'BIZ_FORM') {
            $cv['landingInfo']['landingType'] = $creative['landingInfo']['landingType'];
            $cv['landingInfo']['bizFormId'] = $creative['landingInfo']['bizFormId'];
        } else {
            if($creative['pcLandingUrl'])
                $cv['pcLandingUrl'] = $creative['pcLandingUrl'];
            if($creative['mobileLandingUrl'])
                $cv['mobileLandingUrl'] = $creative['mobileLandingUrl'];
            if($creative['rspvLandingUrl'])
                $cv['rspvLandingUrl'] = $creative['rspvLandingUrl'];         
        }
        if ($creative['messageElement'])
            $cv['messageElement'] = $creative['messageElement'];

        $data = array_merge($param, $cv);
        // print_r($data);
        $result = $this->getCall($request, NULL, $data, 'PUT', true);
        // echo '<pre>' . print_r($result, 1) . '</pre>';

        if ($result['id'] == $param['id'])
            $this->db->setCreative($param);
        return $result;
    }

    public function getAdview($adViewId, $adAccountId)
    { //폼 정보 조회
        $request = "https://apis.moment.kakao.com/openapi/v4/adViews/$adViewId";
        $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, null, null, 'GET');
        return $result;
    }

    public function getBizformList($adAccountId)
    { //폼 정보 조회
        $request = "https://apis.moment.kakao.com/openapi/v4/creatives/landing/talkBizForms";
        $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, null, null, 'GET');
        return $result;
    }

    public function getBulkCreatives($adAccountId)
    {
        $request = 'https://apis.moment.kakao.com/openapi/beta/creatives/search';
        $this->ad_account_id = $adAccountId;
        $page = 0;
        $put_data = [["filterType" => "USER_CONFIG", "values" => ["ON", "OFF"]]];
        while ($page >= 0) {
            // echo '<p>'.$page.'</p>';
            $param = ['page' => $page, 'size' => 1000];
            $response = $this->getCall($request, $param, $put_data, 'PUT');
            // echo '<pre>'.print_r($response,1).'</pre>';
            if (count($response['content']) > 0) {
                foreach ($response['content'] as $row) {
                    if (trim($row['pcLandingUrl']))
                        $row['landingUrl'] = $row['pcLandingUrl'];
                    if (trim($row['mobileLandingUrl']))
                        $row['landingUrl'] = $row['mobileLandingUrl'];
                    if (trim($row['rspvLandingUrl']))
                        $row['landingUrl'] = $row['rspvLandingUrl'];
                    $row['adgroup_id'] = $row['adGroupId'];
                    $this->db->updateCreative($row);
                }
            }
            $page = ($page + 1 < $response['totalPages']) ? $page + 1 : -1;
        }
        return $result;
    }

    public function updateCreatives()
    { //전체 소재 업데이트
        $adgroups = $this->db->getAdGroups(["ON", "OFF"]);
        $step = 1;
        CLI::write("[".date("Y-m-d H:i:s")."]"."소재 수신을 시작합니다.", "light_red");
        foreach ($adgroups->getResultArray() as $adgroup) {
            // echo "<p>{$adgroup['id']}, {$adgroup['ad_account_id']}</p>";
            $creativeList = $this->getCreatives($adgroup['id'], $adgroup['ad_account_id']);
            // echo '<pre>'.print_r($creativeList,1).'</pre>';
            if (count($creativeList) > 0) {
                $i = 0;
                $total = count($creativeList);
                foreach ($creativeList as $lists) {
                    foreach ($lists as $row) {
                        CLI::showProgress($step++, $total);
                        if ($row['id'] && $row['config'] != 'DEL') {
                            $creative = $this->getCreative($row['id']);
                            $data[$adgroup['id']][$i] = $creative;
                            if (isset($creative['extras']) && $creative['extras']['detailCode'] == '33003') {
                                $delete = ['id' => $row['id'], 'config' => 'DEL'];
                                $this->db->setCreative($delete);
                                continue;
                                //echo "{$adgroup['ad_account_id']} - 소재({$row['id']}) : 삭제" . PHP_EOL;
                            }
                            $data[$adgroup['id']][$i]['type'] = $row['type'];
                            //landingUrl 필드 삭제 변경으로 인한 패치
                            if (trim($data[$adgroup['id']][$i]['pcLandingUrl']))
                                $data[$adgroup['id']][$i]['landingUrl'] = $data[$adgroup['id']][$i]['pcLandingUrl'];
                            if (trim($data[$adgroup['id']][$i]['mobileLandingUrl']))
                                $data[$adgroup['id']][$i]['landingUrl'] = $data[$adgroup['id']][$i]['mobileLandingUrl'];
                            if (trim($data[$adgroup['id']][$i]['rspvLandingUrl']))
                                $data[$adgroup['id']][$i]['landingUrl'] = $data[$adgroup['id']][$i]['rspvLandingUrl'];
                            $i++;
                        } else if ($row['config'] == 'DEL') {
                            $delete = ['id' => $row['id'], 'config' => 'DEL'];
                            $this->db->setCreative($delete);
                            continue;
                            //echo "{$adgroup['ad_account_id']} - 소재({$row['id']}) : 삭제" . PHP_EOL;
                        }
                    }
                }
            }
        }
        $this->db->updateCreatives($data);
        return $data;
    }

    public function updateBulkCreatives()
    { //전체 소재 업데이트
        $accounts = $this->db->getAdAccounts();
        foreach ($accounts->getResultArray() as $account) {
            $this->getBulkCreatives($account['id']);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 5. 보고서 */
    private function getAdAccountReport($adAccountId)
    { //5.1. 광고계정 보고서 조회
        $request = 'adAccount/report';
        $param = array('adAccountId' => $adAccountId, 'level' => 'AD_ACCOUNT', 'metricsGroup' => 'BASIC');
        $result = $this->getCall($request, $param);
        return $result; // Array ( [start] => 2018-11-09 [end] => 2018-11-09 [dimensions] => Array ( [ad_account_id] => 41250 ) [metrics] => Array ( [imp] => 77431 [click] => 516 [ctr] => 0.66639976236908 [cost] => 104071 )
    }

    private function getCampaignReport($campaignId)
    { //5.2. 캠페인 보고서 조회
        $request = 'campaigns/report';
        $param = array('campaignId' => $campaignId, 'level' => 'CAMPAIGN', 'dimension' => 'HOUR', 'metricsGroup' => 'BASIC');
        $result = $this->getCall($request, $param);
        echo nl2br(print_r($result, 1));
    }

    public function getAdGroupReport($adGroupId, $level = 'AD_GROUP', $datePreset = 'TODAY', $dimension = 'CREATIVE_FORMAT', $metrics = 'BASIC')
    { //5.3. 광고그룹 보고서 조회
        $request = 'adGroups/report';
        $param = array('adGroupId' => $adGroupId, 'level' => $level);
        if (preg_match('/^[A-Z]+/', $datePreset))
            $param['datePreset'] = $datePreset;
        else {
            $date = preg_replace('/[^0-9]+/', '', $datePreset);
            $param['start'] = $date;
            $param['end'] = $date;
        }
        if ($dimension)
            $param['dimension'] = $dimension;
        if ($metrics)
            $param['metricsGroup'] = $metrics;
        $result = $this->getCall($request, $param);
        return $result;
    }

    private function getCreativeReport($creativeId, $datePreset = 'TODAY', $dimension = 'CREATIVE_FORMAT', $metrics = 'BASIC')
    { //5.4. 소재 보고서 조회 //3034514
        $request = 'creatives/report';
        $param = array('creativeId' => $creativeId);
        if (preg_match('/^[A-Z]+/', $datePreset))
            $param['datePreset'] = $datePreset;
        else {
            $date = preg_replace('/[^0-9]+/', '', $datePreset);
            $param['start'] = $date;
            $param['end'] = $date;
        }
        if ($dimension)
            $param['dimension'] = $dimension;
        if ($metrics)
            $param['metricsGroup'] = $metrics;
        $adAccountId = $this->db->getAdAccountIdByCreativeId($creativeId);
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request, $param);
        return $result;
    }

    public function getBizForms($adAccountId)
    {
        $request = 'creatives/landing/talkBizForms';
        if ($adAccountId) $this->ad_account_id = $adAccountId;
        $result = $this->getCall($request);
        return $result;
    }

    public function updateBizform()
    {
        require __DIR__ . '/bizformapi.php';
        new ChainsawKMBF();
    }

    public function moveToAppsubscribe()
    { //잠재고객 > app_subscribe 테이블로 이동
        $ads = $this->db->getBizFormUserResponse();
        $total = $ads->getNumRows();
        $step = 1;
        if (!$total) {
            return null;
        }
        CLI::write("[".date("Y-m-d H:i:s")."]"."app_subscribe 데이터 업데이트를 시작합니다.", "light_red");
        foreach ($ads->getResultArray() as $row) {
            CLI::showProgress($step++, $total); 
            $landing = $this->landingGroup($row);
            if(is_null($landing)) {
                echo '비즈폼 매칭 오류 발생 : <pre>' . print_r($row, 1) . '</pre>';
                continue;
            }
            //전화번호
            $phone = str_replace("+82010", "010", $row['phoneNumber']);
            $phone = str_replace("+8210", "010", $phone);
            $phone = preg_replace("/^8210(.+)$/", "010$1", $phone);
            $phone = str_replace("+82 10", "010", $phone);
            $phone = str_replace("-", "", $phone);
            if ($row['email'] == '없음') $row['email'] = '';

            //추가질문
            $questions = [];
            $add = [];
            $responses = json_decode($row['responses'], 1);
            $acnt = 1;
            foreach ($responses as $response) {
                $qs = $this->db->getBizformQuestion($row['bizFormId'], $response['bizformItemId']);
                if (!key_exists($qs['id'], $questions))
                    $questions[$qs['id']] = $qs['title'];
                $add[] = ${'add' . $acnt} = $questions[$response['bizformItemId']] . '::' . $response['response'];
                $acnt++;
            }
            $result = [];
            if ($landing['media']) {
                $result['group_id'] = $landing['app_id'];
                $result['event_id'] = $landing['event_id'];
                $result['site'] = $landing['site'];
                $result['full_name'] = $this->db->real_escape_string($row['nickname']);
                $result['email'] = $this->db->real_escape_string($row['email']);
                $result['gender'] = $this->db->real_escape_string($row['gender']);
                $result['age'] = $this->db->real_escape_string($row['age']);
                $result['phone'] = $this->db->real_escape_string($phone);
                $result['add1'] = $this->db->real_escape_string($add1);
                $result['add2'] = $this->db->real_escape_string($add2);
                $result['add3'] = $this->db->real_escape_string($add3);
                $result['add4'] = $this->db->real_escape_string($add4);
                $result['add5'] = $this->db->real_escape_string($add5);
                $result['add6'] = $this->db->real_escape_string($add6);
                $result['addr'] = $this->db->real_escape_string($addr);
                $result['reg_date'] = $row['submitAt'];
                $result['ad_id'] = $row['id'];
                $result['encUserId'] = $row['encUserId'];
                $result['bizFormId'] = $row['bizFormId'];
            }
            // echo '<pre>' . print_r($row, 1) . '</pre>';

            if (is_array($result) && count($result)) {
                $this->db->insertToSubscribe($result);
            }

            // return $result;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function updateCreativesReportBasic($datePreset = 'TODAY', $dimension = 'CREATIVE_FORMAT', $metrics = 'BASIC')
    { //전체 소재 보고서 BASIC 업데이트
        $adgroups = $this->db->getAdGroups(['ON', 'OFF'], "ORDER BY B.ad_account_id DESC");
        $cnt = 1;
        $step = 1;
        $ids = [];
        $this->ad_account_id = '';
        $total = $adgroups->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."전체 소재 보고서 BASIC  수신을 시작합니다.", "light_red");
        foreach ($adgroups->getResultArray() as $adgroup) {
            // if(!in_array($adgroup['id'], ['1365907','1365923'])) {$cnt++; continue;}
            if (!$this->ad_account_id)
                $this->ad_account_id = $adgroup['ad_account_id'];
            if ($adgroup['id']) {
                if ($this->ad_account_id == $adgroup['ad_account_id'])
                    $ids[] = $adgroup['id'];
                else {
                    $new_ids = [];
                    $new_ids[] = $adgroup['id'];
                }
            }
            CLI::showProgress($step++, $total);
            // echo '<h1>'.date('[H:i:s] ').$this->ad_account_id.','.$adgroup['ad_account_id'].'</h1>';
            // echo '<h2>'.$cnt.'</h2>';
            if (count($ids) == 20 || $this->ad_account_id != $adgroup['ad_account_id'] || $adgroups->getNumRows() == $cnt) {
                $adgroup_ids = implode(",", $ids);
                // echo '<h3>'.$adgroup_ids.'</h3>';
                $report = $this->getAdGroupReport($adgroup_ids, 'CREATIVE', $datePreset, $dimension, $metrics);
                // echo '<pre>'.print_r($report,1).'</pre>';
                if ($report['message'] == 'Success' && count($report['data']) > 0) {
                    $i = 0;
                    foreach ($report['data'] as $row) {
                        if (count($row['metrics'])) {
                            $data[$row['dimensions']['creative_id']][$i] = $row['metrics'];
                            $data[$row['dimensions']['creative_id']][$i]['cost'] = $row['metrics']['cost']; //부가세 제거
                            $data[$row['dimensions']['creative_id']][$i]['date'] = $row['start'];
                            $i++;
                        }
                    }
                }
                $ids = $new_ids;
                $this->ad_account_id = $adgroup['ad_account_id'];
                ob_flush();
                flush();
                sleep(5);
            }
            $cnt++;
        }
        $this->db->updateCreativesReportBasic($data);
        return $data;
    }

    public function autoAiOn() {
        $adgroups = $this->db->getInitCreatives();
        $step = 1;
        $total = $adgroups->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."AI를 시작합니다.", "light_red");
        if(!$total) return;
        foreach ($adgroups->getResultArray() as $adgroup) {
            CLI::showProgress($step++, $total); 
            if($adgroup['report_date'] == date('Y-m-d') && $adgroup['report_update_time'] == '0000-00-00 00:00:00') {
                $aiData = [
                    'campaign_id' => $adgroup['campaign_id'],
                    'adgroup_id' => $adgroup['adgroup_id']
                ];
                $this->db->allAiOn($aiData);
                echo "<br>[[[[[[[[[[Ai ON]]]]]]]]]]".PHP_EOL;
            }
            echo "[{$adgroup['account_name']}] 캠페인:{$adgroup['campaign_name']}({$adgroup['campaign_id']})/광고그룹:{$adgroup['adgroup_name']}({$adgroup['adgroup_id']})/report-{$adgroup['report_update_time']}[{$adgroup['report_date']}]/type-{$adgroup['campaign_type']}".PHP_EOL;
        }
    }

    public function autoCreativeOnOff($onoff='on') {
    /*
    1. 디비 0개 소재명의 디비단가 소진 > 해당 소재 off 익일 00시 on
    2. 디비 1개 이상  0% 이하 > 해당 소재 off 익일 00시 on
    */
        $creatives = $this->db->getAutoCreativeOnOff($onoff);
        $step = 1;
        $total = $creatives->getNumRows();
        if(!$total) return;
        if($onoff=='on') $set = 'off';
        else $set = 'on';
        CLI::write("[".date("Y-m-d H:i:s")."]"."소재 자동 변경을 시작합니다.", "light_red");
        foreach ($creatives->getResultArray() as $creative) {
            CLI::showProgress($step++, $total); 
            if(!is_null($creative['aitype']) && strtolower($creative['creative_config']) == strtolower($onoff)) {
                // echo '<pre>'.print_r($creative,1).'</pre>';
                // $result['http_code'] = 200;
                $result = $this->setCreativeOnOff($creative['id'], strtoupper($set));
                if($result['http_code'] == 200) {
                    $this->db->insertCreativeAutoOnOff($creative, $set);
                    echo "[{$creative['id']}]{$creative['creative_name']}-CASE:{$creative['aitype']}-상태 {$set} 성공".PHP_EOL;
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function updateName($data)
    {
        if (!trim($data['id']) || !trim($data['type'])) {
            return false;
        }
        switch ($data['type']) {
            case 'campaign':
                $result = $this->setCampaign($data);
                break;
            case 'adset':
                $result = $this->setAdGroup($data);
                break;
            case 'ad':
                $result = $this->setCreative($data);
                break;
        }
        return $result;
    }

    public function landingGroup($data)
    {
        if (!$data['name']) {
            return null;
        }

        preg_match_all('/(.+)?\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $data['name'], $matches);

        // $data['landingUrl'] = 'http://hotevent.hotblood.co.kr/event_spin/event_62.php';
        if (preg_match("/hotblood\.co\.kr/i", $data['landingUrl'])) {
            // echo $data['landingUrl'].'<br>';
            $urls = parse_url($data['landingUrl']);
            parse_str($urls['query'], $urls['qs']);
            $event_id = @array_pop(explode('/', $urls['path']));
            $site = @$urls['qs']['site'];
        } else {
            $event_id = @$matches[2][0];
            $site = @$matches[4][0];
        }
        if(@$urls['qs']['site'] != @$matches[4][0]) //제목 site값 우선
            $site = @$matches[4][0];
        // echo '<pre>' . $data['name'] . ':' . print_r($urls, 1) . '</pre>';
        $db_prefix = '';
        if (isset($matches[10][0]) && $matches[10][0]) {
            switch ($matches[10][0]) {
                case 'khr':
                    $media = '핫이벤트 룰렛';
                    $db_prefix = 'app_';
                    break;
                case 'khrcpm':
                    $media = '핫이벤트 룰렛_cpm';
                    $db_prefix = 'app_';
                    break;
                case 'khsp':
                    $media = '핫이벤트 스핀';
                    $db_prefix = 'event_';
                    break;
                case 'khspcpm':
                    $media = '핫이벤트 스핀_cpm';
                    $db_prefix = 'event_';
                    break;
                case 'ker':
                    $media = '이벤트';
                   $db_prefix = 'evt_';
                    break;
                case 'kercpm':
                    $media = '이벤트_cpm';
                    $db_prefix = 'evt_';
                    break;
                case 'wkhr':
                    $media = '오토랜딩';
                    if ($matches[1][0]) {
                        $db_prefix = 'wr_';
                    }
                    break;
                case 'ber':
                    $media = '이벤트 비즈폼';
                    if ($matches[1][0]) {
                        $db_prefix = 'evt_';
                    }
                    break;
                case 'bercpm':
                    $media = '이벤트 비즈폼_cpm';
                    $db_prefix = 'evt_';
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
        }
        //print_r($matches); echo '<br>';
        $result = array(
            'name' => '', 'media' => '', 'db_prefix' => '', 'event_id' => '', 'app_id' => '', 'site' => '', 'db_price' => 0, 'period_ad' => ''
        );
        if (isset($media) && $media) {
            $result['name']         = $matches[0][0];
            $result['media']        = $media;
            $result['db_prefix']    = $db_prefix;
            $result['event_id']     = $event_id;
            $result['app_id']       = $db_prefix . $event_id;
            $result['site']         = $site;
            $result['db_price']     = $matches[7][0];
            $result['period_ad']    = $matches[13][0];
            $result['url']          = $data['landingUrl'];
            return $result;
        }
        return null;
    }

    public function updateReportByDate($sdate = null, $edate = null)
    { //이미 입력된 리포트데이터를 다시 업데이트 함
        if (is_null($sdate) || is_null($edate))
            return false;
        $creatives = $this->db->getCreativeReportBasic("AND report.date BETWEEN '{$sdate}' AND '{$edate}' ORDER BY report.date ASC");
        if (!$creatives->getNumRows()) {
            return null;
        }
        $data = [];
        $i = 0;
        $cnt = 1;
        $total = $creatives->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."리포트데이터 수신을 시작합니다.", "light_red");
        foreach ($creatives->getResultArray() as $row) {
            CLI::showProgress($i, $total); 
            $report = $this->getCreativeReport($row['id'], $row['date']);
            if ($report['message'] == 'Success' && count($report['data']) > 0) {
                echo date('[H:i:s]') . " {$row['id']}/{$row['date']} 수신" . PHP_EOL;
                foreach ($report['data'] as $v) {
                    if (count($v['metrics'])) {
                        $data[$v['dimensions']['creative_id']][$i] = $v['metrics'];
                        $data[$v['dimensions']['creative_id']][$i]['cost'] = $v['metrics']['cost'] / 1.1; //부가세 제거
                        $data[$v['dimensions']['creative_id']][$i]['date'] = $v['start'];
                        $i++;
                    }
                }
            }
            ob_flush();
            flush();
            sleep(5);
        }
        $this->db->updateCreativesReportBasic($data);
        return $data;
    }

    public function getCreativesUseLanding($date = null)
    { //유효DB 개수 업데이트
        if ($date == null) {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($date));
        }
        $creatives = $this->db->getCreativeReportBasic("AND report.date='{$date}'");
        if (!$creatives->getNumRows()) {
            return null;
        } else {
            $result = array();
        }
        $i = 0;
        $step = 1;
        $total = $creatives->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."유효DB 개수 수신을 시작합니다.", "light_red");
        foreach ($creatives->getResultArray() as $row) {
            CLI::showProgress($step++, $total);
            $landing = $this->landingGroup($row);
            if (isset($landing['media'])) {
                $result[$i]['date'] = $date;
                $result[$i]['creative_id'] = $row['id'];
                $result[$i]['cost'] = $row['cost'];
                $result[$i]['name'] = $landing['name'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['app_id'] = $landing['app_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['db_price'] = $landing['db_price'];
                $result[$i]['period_ad'] = $landing['period_ad'];
                $result[$i]['media'] = $landing['media'];
                $result[$i]['db_prefix'] = $landing['db_prefix'];
                $result[$i]['url'] = $landing['url'];
                $i++;
            }
        }
        if (is_array($result)) {
            $rows = 0;
            foreach ($result as $i => $data) {
                $result[$i]['count'] = 0;
                $result[$i]['sales'] = 0;
                $result[$i]['margin'] = 0;
                $sales = 0;
                if ($data['app_id']) {
                    $dbcount = $this->db->getDbCount($data['creative_id'], $date);
                    $rows = $this->db->getAppSubscribe($data, $date);
                    $result[$i]['count'] = $rows;
                    $result[$i]['db_price2'] = $dbcount['db_price'];
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
                    /* 수익, 매출액 계산 */
                    if ($db_price) {
                        if (!$initZero)
                            $sales = $db_price * $rows;
                        $report_data = new stdClass();
                        $report_data->creative_id = $data['creative_id'];
                        $report_data->date = $date;
                        $report_data->data['sales'] = $sales;
                        $this->db->updateReport($report_data);
                        $result[$i]['sales'] = $sales;
                    }
                    /*
                    카카오 계산법 수정

                    지출액 = 지출액/1.1
                    수익은 = 수익+지출액(위에 계산 후에 금액) *0.15
                    ^ 수식 들어간 기존대로...
                    */
                    if (!$initZero)
                        $result[$i]['margin'] = $sales - $data['cost'];
                    // $result[$i]['margin'] = ($sales - $data['cost']) + ($data['cost'] * 0.15); //2021-11-10 수수료 제외 By 허민
                }
                if ($data['period_ad']) { // ^ 수식
                    $result[$i]['margin'] = $data['cost'] * ('0.' . $data['period_ad']);
                }
            }
            // echo '<pre>'.print_r($result,1).'</pre>';
            $this->db->insertDbCount($result, $date);
            // usort($result, function($a, $b) {
            //  return $b['event_id'] <=> $a['event_id'];
            // });
            if (!$rows) {
                unset($result[$i]);
            }
        }
        return $result;
    }

    public function setDailyBudgetAmount($data)
    {
        switch ($data['type']) {
            case 'campaign':
                if ($data['budget'] && ($data['budget'] < 50000 || $data['budget'] > 1000000000 || $data['budget'] % 10 != 0)) {
                    $return['code'] = false;
                    $return['msg'] = '캠페인 일예산 설정값은 최소 50,000원에서 최대 1,000,000,000원까지 설정 가능하며 10원 단위로 가능합니다';
                    return $return;
                }
                if ($data['budget'] == 0) $data['budget'] = '';
                $result = $this->setCampaignDailyBudgetAmount($data['id'], $data['budget']);
                if ($result['http_code'] == 200) {
                    $this->db->setCampaignDailyBudgetAmount($data['id'], $data['budget']);
                    $return = $data['id'];
                } else if (isset($result['extras'])) {
                    $return['code'] = $result['extras']['detailCode'];
                    $return['msg'] = $result['extras']['detailMsg'];
                }
                return $return;
                break;
            case 'adgroup':
                if ($data['budget'] < 10000 || $data['budget'] > 1000000000 || $data['budget'] % 10 != 0) {
                    $return['code'] = false;
                    $return['msg'] = '광고그룹 일예산 설정값은 최소 10,000원에서 최대 1,000,000,000원까지 설정 가능하며 10원 단위로 가능합니다';
                    return $return;
                }
                $result = $this->setAdGroupDailyBudgetAmount($data['id'], $data['budget']);
                if ($result['http_code'] == 200) {
                    $this->db->setAdGroupDailyBudgetAmount($data['id'], $data['budget']);
                    $return = $data['id'];
                } else if (isset($result['extras'])) {
                    $return['code'] = $result['extras']['detailCode'];
                    $return['msg'] = $result['extras']['detailMsg'];
                }
                return $return;
                break;
        }
    }

    public function autoLimitBudget($data = [])
    {
        if (!isset($data['date'])) $data['date'] = date('Y-m-d');
        $campaigns = $this->db->getAutoLimitBudgetCampaign($data);
        $step = 1;
        $total = $campaigns->getNumRows();
        if ($total) {
            CLI::write("[".date("Y-m-d H:i:s")."]"."자동 예산한도 설정을 시작합니다.", "light_red");
            foreach ($campaigns->getResultArray() as $row) {
                CLI::showProgress($step++, $total); 
                preg_match_all('/@([0-9]+)/', $row['name'], $matches); //제목에서 @숫자 추출
                $row['limit_db'] = $matches[1][0]; //설정DB 개수
                if ($row['limit_db'] <= $row['unique_total'] && $row['cost'] && $row['already'] == 0) {
                    // echo '<pre>'.print_r($row,true).'</pre>';
                    $data = ['id' => $row['id'], 'date' => $data['date'], 'set_db' => $row['limit_db'], 'valid_db' => $row['unique_total'], 'budget' => $row['cost'], 'type' => 'campaign'];
                    $result = $this->setDailyBudgetAmount($data);
                    sleep(5);
                    if ($result == $row['id']) {
                        $this->db->setAutoLimitBudgetCampaign($data);
                        echo '<strong>변경완료</strong> '.json_encode($row,JSON_UNESCAPED_UNICODE).''.PHP_EOL;
                    } else {
                        echo '변경실패 ' . print_r($result) . '' . PHP_EOL;
                    }
                } else {
                    echo '대상 아님 '.json_encode($row,JSON_UNESCAPED_UNICODE).''.PHP_EOL;
                }
            }
        }
    }

    public function autoLimitBudgetReset($data = [])
    { //23시 55분에 예산 리셋
        if (!isset($data['date'])) $data['date'] = date('Y-m-d');
        $campaigns = $this->db->getAutoLimitBudgetCampaign($data);
        $step = 1;
        $total = $campaigns->getNumRows();
        if ($total) {
            CLI::write("[".date("Y-m-d H:i:s")."]"."자동 예산한도 리셋을 시작합니다.", "light_red");
            foreach ($campaigns->getResultArray() as $row) {
                CLI::showProgress($step++, $total); 
                preg_match_all('/@([0-9]+)/', $row['name'], $matches); //제목에서 @숫자 추출
                $row['limit_db'] = $matches[1][0]; //설정DB 개수
                if ($row['already'] == 1) {
                    $data = ['id' => $row['id'], 'date' => $data['date'], 'set_db' => $row['limit_db'], 'valid_db' => $row['unique_total'], 'budget' => "0", 'type' => 'campaign'];
                    $result = $this->setDailyBudgetAmount($data);
                    sleep(5);
                    if ($result == $row['id']) {
                        $this->db->setAutoLimitBudgetCampaign($data);
                        echo '<strong>변경완료</strong> '.json_encode($row,JSON_UNESCAPED_UNICODE).''.PHP_EOL;
                    } else {
                        echo '변경실패 ' . print_r($result) . '' . PHP_EOL;
                    }
                }
            }
        }
    }

    public function autoLimitBidAmount($data = [])
    {
        /*
        Ai 1
        Level 1. 유효DB 1개이상, 수익률 20% 이하 > 입찰가 -30원
        Level 2. 유효DB 1개이상, 수익률 -100% 이하 > 광고그룹 OFF
        Level 3. 유효DB 1개이상, 수익 -10만원 이하 > 광고그룹 OFF
        Level 4. 유효DB 0개, 설정한 DB단가보다 소진금액이 높을 때 > 입찰가 -30원 + 광고그룹 OFF
        */
        if (!isset($data['date'])) {
            $data['date'] = $date = date('Y-m-d');
        }
        $adgroups = $this->db->getAutoLimitBidAmountAdGroup($data);
        $step = 1;
        $total = $adgroups->getNumRows();
        if ($total) {
            CLI::write("[".date("Y-m-d H:i:s")."]"."자동 입찰가 설정을 시작합니다.", "light_red");
            foreach ($adgroups->getResultArray() as $row) {
                CLI::showProgress($step++, $total); 
                $data = [];
                unset($result);
                // echo '내역<pre>'.print_r($row,true).'</pre>';
                if ($row['id'] && $row['campaign_config'] == 'ON' && $row['config'] == 'ON') {
                    if (!is_null($row['unique_total']) && $row['unique_total'] > 0) { //유효DB가 1개 이상일 때
                        if ($row['margin_ratio'] <= 20 && $row['goal'] != 'CONVERSION' && $row['already_1'] == 0 && $row['already_2'] == 0 && $row['already_3'] == 0) { //Level 1-수익률이 20% 이하일 때 입찰가에 -30원 조정(전환캠페인은 제외)
                            echo "Level 1:{$row['id']}" . PHP_EOL;
                            $setBidAmount = ($row['bidAmount'] < 100) ? $row['bidAmount'] - 10 : $row['bidAmount'] - 30;
                            $data = ['level' => '1', 'date' => $date, 'id' => $row['id'], 'bidAmount' => $setBidAmount, 'adAccountId' => $row['ad_account_id'], 'msg' => "Level 1({$row['bidAmount']}=>{$setBidAmount})"];
                            if($row['bidStrategy'] == 'AUTOBID') continue; //자동입찰일 경우 건너띄기
                            $result = $this->setAdGroupBidAmount($data['id'], $data['bidAmount'], $data['adAccountId']); //현재입찰가에 -30원 조정
                        } else if ($row['margin_ratio'] <= -100 && $row['already_2'] == 0) { //Level 2-수익률이 -100%가 넘으면 상태 OFF
                            echo "Level 2:{$row['id']}" . PHP_EOL;
                            $data = ['level' => '2', 'date' => $date, 'id' => $row['id'], 'config' => 'OFF', 'adAccountId' => $row['ad_account_id'], 'msg' => "Level 2({$row['config']}=>OFF)"];
                            $result = $this->setAdGroupOnOff($data['id'], $data['config'], $data['adAccountId']);
                        } else if ($row['margin'] <= -100000 && $row['already_3'] == 0) { //Level 3-수익이 -10만원이 넘으면 상태 OFF
                            echo "Level 3:{$row['id']}" . PHP_EOL;
                            $data = ['level' => '3', 'date' => $date, 'id' => $row['id'], 'config' => 'OFF', 'adAccountId' => $row['ad_account_id'], 'msg' => "Level 3({$row['config']}=>OFF)"];
                            $result = $this->setAdGroupOnOff($data['id'], $data['config'], $data['adAccountId']);
                        } else {
                            echo 'LEVEL1,2,3 대상 아님'.json_encode($row,JSON_UNESCAPED_UNICODE).''.PHP_EOL;
                        }
                    } else { //유효DB가 없을 때
                        preg_match_all('/\*([0-9]+)/', $row['ad_name'], $matches); //제목에서 *숫자 추출
                        $set_cpa = $matches[1][0]; //DB단가
                        if ($set_cpa && $row['cost'] >= $set_cpa && $row['already_4'] == 0) { //제목에 설정한 DB단가보다 소진금액이 높을 때
                            echo "Level 4:{$row['id']}" . PHP_EOL;
                            $setBidAmount = ($row['bidAmount'] < 100) ? $row['bidAmount'] - 10 : $row['bidAmount'] - 30;
                            $data = ['level' => '4', 'date' => $date, 'id' => $row['id'], 'cost' => $row['cost'], 'set_cpa' => $set_cpa, 'config' => 'OFF', 'bidAmount' => $setBidAmount, 'adAccountId' => $row['ad_account_id'], 'msg' => "Level 3({$row['bidAmount']}=>{$setBidAmount})"];
                            $result = $this->setAdGroupOnOff($data['id'], $data['config'], $data['adAccountId']);
                            sleep(5);
                            if ($row['goal'] != 'CONVERSION' && $row['bidStrategy'] != 'AUTOBID') {
                                $data['msg'] = "Level 4({$row['bidAmount']}=>{$setBidAmount}, {$row['config']}=>OFF)";
                                $result = $this->setAdGroupBidAmount($data['id'], $data['bidAmount'], $data['adAccountId']); //현재입찰가 -30원으로 조정
                            }
                        } else {
                            echo 'LEVEL4 대상 아님'.json_encode($row,JSON_UNESCAPED_UNICODE).''.PHP_EOL;
                        }
                    }
                    if ($result == $row['id'] || @$result['result'] == true || $result['http_code'] == 200) {
                        $this->db->setAutoLimitBidAmountAdGroup($data);
                        sleep(5);
                        echo '<strong>변경완료</strong> <pre>' . json_encode($data, JSON_UNESCAPED_UNICODE) . json_encode($row, JSON_UNESCAPED_UNICODE) . '</pre>';
                    } else if (isset($result)) {
                        echo '변경실패 <pre>' . json_encode($result, JSON_UNESCAPED_UNICODE) . json_encode($row, JSON_UNESCAPED_UNICODE) . '</pre>' . PHP_EOL;
                    }
                }
            }
        }
    }

    public function autoLimitBidAmountReset($data = [])
    { //23시 55분에 ON
        if (!isset($data['date'])) {
            $data['date'] = $date = date('Y-m-d');
        }
        $adgroups = $this->db->getAutoLimitBidAmountAdGroup($data);
        $step = 1;
        $total = $adgroups->getNumRows();
        if ($total) {
            CLI::write("[".date("Y-m-d H:i:s")."]"."자동 입찰가한도 리셋을 시작합니다.", "light_red");
            foreach ($adgroups->getResultArray() as $row) {
                CLI::showProgress($step++, $total);          
                if ($row['campaign_config'] == 'ON' && $row['config'] == 'OFF') {
                    if ($row['already_2'] == 1 || $row['already_3'] == 1 || $row['already_4'] == 1) {
                        if ($row['already_2']) $level = "2";
                        else if ($row['already_3']) $level = "3";
                        else if ($row['already_4']) $level = "4";
                        echo "Level {$level}" . PHP_EOL;
                        $data = ['level' => $level, 'date' => $date, 'id' => $row['id'], 'config' => 'ON', 'adAccountId' => $row['ad_account_id'], 'msg' => "Level {$level}({$row['config']}=>ON)"];
                        $result = $this->setAdGroupOnOff($data['id'], $data['config'], $data['adAccountId']);
                        sleep(5);
                        if ($result['http_code'] == 200) {
                            $this->db->setAutoLimitBidAmountAdGroup($data);
                            echo '<strong>변경완료</strong> ' . print_r($data) . '';
                        } else if (isset($result)) {
                            echo '변경실패 ' . print_r($result) . print_r($data) . '' . PHP_EOL;
                        }
                    }
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function getCall($request, $param = '', $data = '', $type = 'GET', $getError = false)
    {
        if (preg_match('/^http/', $request)) {
            $url = $request;
        } else {
            $url = $this->host . '/openapi/v4/' . $request;
            if (preg_match('/^\//', $request))
                $url = $this->host . '' . $request;
        }
        if ($param) {
            $url .= '?' . http_build_query($param);
        }
        $response = $this->curl($url, $this->access_token, $data, $type, false, $getError);
        return $response;
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    protected function curl($url, $api_key, $data, $type = "GET", $multipart = false, $getError = false)
    {
        if ($api_key != NULL)
            $headers = array("Authorization: Bearer {$api_key}");
        else
            $headers = array();

        if ($this->ad_account_id) {
            $headers[] = "adAccountId: {$this->ad_account_id}";
        }

        if ($multipart == true && is_array($data)) {
            $headers[] = 'Content-type: multipart/form-data';
            $data['image'] = new CURLFile($data['image']['tmp_name'], $data['image']['type'], $data['image']['name']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                break;
            case 'PUT':
                $headers[] = 'Content-type: application/json';
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = json_decode($result, true);
        // echo json_encode($data);
        // echo '<pre>headers:'.print_r($headers,1).'</pre>';
        // echo '<pre>data:'.print_r($data,1).'</pre>';
        // echo '<pre>info:'.print_r($info,1).'</pre>';
        // echo '<pre>result:'.print_r($result,1).'</pre>';
        if (isset($result['error'])) {
            $this->error($result['error'], $result['error_description']);
        }
        switch ($info['http_code']) {
            case 200:
                if ($info['download_content_length'] == 0) {
                    $result = array();
                    $result['http_code'] = 200;
                }
                break;
            case 302:
                header('Location: ' . $info['redirect_url']);
                break;
            case 400:
            case 403:
            case 405:
            case 429:
            case 500:
                if (!$getError) {
                    echo '<pre>headers:' . print_r($headers, 1) . '</pre>';
                    echo '<pre>data:' . print_r($data, 1) . '</pre>';
                    echo '<pre>info:' . print_r($info, 1) . '</pre>';
                    echo '<pre>result:' . print_r($result, 1) . '</pre>';
                    if (isset($result['extras']))
                        $this->apiError($result);
                    else
                        $this->error($info['http_code'], $result['msg']);
                }
                return $result;
                break;
            default:
                $this->error($info['http_code']);
                break;
        }
        curl_close($ch);

        return $result;
    }

    protected function error($title, $desc = '')
    {
        echo '<h1>' . $title . '</h1>';
        if ($desc)
            echo '<p>' . $desc . '</p>';
        // exit;
    }

    protected function apiError($data)
    {
        echo "<h1>{$data['msg']} (code: {$data['code']})</h1>";
        if (isset($data['extras']['detailMsg']))
            echo "<p>{$data['extras']['detailMsg']} - {$data['extras']['detailCode']}</p>";
        if (isset($data['extras']['message']))
            echo "<p>{$data['extras']['message']} - {$data['extras']['status']}</p>";
        // exit;
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
                            if ($k == $key) $var = str_replace('{' . $k . '}', $var, $v);
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

    public function getMemo($data)
    {
        $response = $this->db->getMemo($data);
        return $response;
    }

    public function addMemo($data)
    {
        return $this->db->addMemo($data);
    }
}
