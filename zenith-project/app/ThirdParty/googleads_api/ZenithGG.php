<?php
namespace App\ThirdParty\googleads_api;

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//ini_set('max_execution_time', 1800);
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

require_once __DIR__ . '/vendor/autoload.php';

use App\ThirdParty\googleads_api\GADB;
use CodeIgniter\CLI\CLI;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use App\ThirdParty\googleads_api\lib\Utils\Helper;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V13\Resources\CustomerClient;
use Google\Ads\GoogleAds\V13\Services\CustomerServiceClient;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V13\Enums\SpendingLimitTypeEnum\SpendingLimitType;
use Google\Ads\GoogleAds\V13\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V13\Enums\CampaignServingStatusEnum\CampaignServingStatus;
use Google\Ads\GoogleAds\V13\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V13\Enums\AdGroupTypeEnum\AdGroupType;
use Google\Ads\GoogleAds\V13\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V13\Enums\AdvertisingChannelSubTypeEnum\AdvertisingChannelSubType;
use Google\Ads\GoogleAds\V13\Enums\AdServingOptimizationStatusEnum\AdServingOptimizationStatus;
use Google\Ads\GoogleAds\V13\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V13\Enums\PolicyReviewStatusEnum\PolicyReviewStatus;
use Google\Ads\GoogleAds\V13\Enums\PolicyApprovalStatusEnum\PolicyApprovalStatus;
use Google\Ads\GoogleAds\V13\Enums\AdTypeEnum\AdType;
use Google\Ads\GoogleAds\V13\Enums\BudgetStatusEnum\BudgetStatus;
use Google\Ads\GoogleAds\V13\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V13\Enums\CustomerStatusEnum\CustomerStatus;
use Google\Ads\GoogleAds\V13\Enums\MimeTypeEnum\MimeType;
use Google\Ads\GoogleAds\V13\Enums\AssetTypeEnum\AssetType;
use Google\Ads\GoogleAds\V13\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V13\Enums\ResponseContentTypeEnum\ResponseContentType;
use Google\Ads\GoogleAds\V13\Resources\Ad;
use Google\Ads\GoogleAds\V13\Resources\AdGroup;
use Google\Ads\GoogleAds\V13\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V13\Resources\Campaign;
use Google\Ads\GoogleAds\V13\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V13\Services\AdGroupOperation;
use Google\Ads\GoogleAds\V13\Services\AdOperation;
use Google\Ads\GoogleAds\V13\Services\CampaignOperation;
use Google\Ads\GoogleAds\V13\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V13\Services\CampaignBudgetOperation;

class ZenithGG
{
    private $manageCustomerId = "4013365335"; //4013365335
    private $db;
    private static $rootCustomerClients = ['5980790227', '4324269025', '2409346509', '4946840644', '4943963823', '7933651274', '5045171745', '4486211678', '8135785284', '2667057443', '4560872762'];
    private static $oAuth2Credential, $googleAdsClient;

    public function __construct($clientCustomerId = "")
    {
        $this->db = new GADB();
        $this->oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(__DIR__ . "/google_ads_php.ini")->build();
    }
      
    private function setCustomerId($customerId = null)
    {
        $this->googleAdsClient = (new GoogleAdsClientBuilder())
            ->fromFile(__DIR__ . "/google_ads_php.ini")
            ->withOAuth2Credential($this->oAuth2Credential)
            ->withLoginCustomerId($customerId ?? $this->manageCustomerId)
            ->build();
    }
      
    public function getAccounts($loginCustomerId = null)
    {
        self::setCustomerId();
        $rootCustomerIds = [];
        $rootCustomerIds = self::getAccessibleCustomers($this->googleAdsClient);
        $allHierarchies = [];
        $accountsWithNoInfo = [];
        $step = 1;
        $total = count($rootCustomerIds);
        CLI::write("[".date("Y-m-d H:i:s")."]"."광고계정 수신을 시작합니다.", "light_red");
        foreach ($rootCustomerIds as $rootCustomerId) {
            CLI::showProgress($step++, $total);
            $customerClientToHierarchy = self::createCustomerClientToHierarchy($loginCustomerId, $rootCustomerId);
            if (is_null($customerClientToHierarchy)) {
                $accountsWithNoInfo[] = $rootCustomerId;
            } else {
                $allHierarchies += $customerClientToHierarchy;
            }
        }

        foreach ($allHierarchies as $rootCustomerId => $customerIdsToChildAccounts) {
            $data = self::printAccountHierarchy(
                self::$rootCustomerClients[$rootCustomerId],
                $customerIdsToChildAccounts,
                0
            );
            $data = [
                'customerId' => $data['customerId'],
                'manageCustomer' => $data['manageCustomer'],
                'name' => $data['name'],
                'status' => $data['status'],
                'canManageClients' => $data['canManageClients'],
                'currencyCode' => $data['currencyCode'],
                'dateTimeZone' => $data['dateTimeZone'],
                'testAccount' => $data['testAccount'],
                'is_hidden' => $data['is_hidden'],
            ];
            $this->db->updateAccount($data);
        }
    }
      
    public function getAccountBudgets($loginCustomerId = null, $customerId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();

        $query = 'SELECT account_budget.status, '
            . 'account_budget.billing_setup, '
            . 'account_budget.amount_served_micros, '
            . 'account_budget.adjusted_spending_limit_micros, '
            . 'account_budget.adjusted_spending_limit_type '
            . 'FROM account_budget';

        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
            $accountBudget = $googleAdsRow->getAccountBudget();
            $amountServed = Helper::microToBase($accountBudget->getAmountServedMicros());
            $amountSpendingLimit = $accountBudget->getAdjustedSpendingLimitMicros() ? Helper::microToBase($accountBudget->getAdjustedSpendingLimitMicros()) : SpendingLimitType::name($accountBudget->getAdjustedSpendingLimitType());
            $data = [
                'customerId' => $customerId, 'manageCustomer' => $loginCustomerId, 'amountServed' => $amountServed, 'amountSpendingLimit' => $amountSpendingLimit
            ];
            $this->db->modifyAccountBudget($data);
        }
    }

      
    private static function createCustomerClientToHierarchy(
        ?int $loginCustomerId,
        int $rootCustomerId
    ): ?array {
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(__DIR__ . "/google_ads_php.ini")->build();
        $googleAdsClient = (new GoogleAdsClientBuilder())->fromFile(__DIR__ . "/google_ads_php.ini")
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId($loginCustomerId ?? $rootCustomerId)
            ->build();
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
        $query = 'SELECT customer_client.client_customer, customer_client.level,'
            . ' customer_client.manager, customer_client.descriptive_name,'
            . ' customer_client.currency_code, customer_client.time_zone, customer_client.hidden, customer_client.status, customer_client.test_account,'
            . ' customer_client.id FROM customer_client WHERE customer_client.level <= 1';

        $rootCustomerClient = null;
        $managerCustomerIdsToSearch = [$rootCustomerId];

        $customerIdsToChildAccounts = [];

        while (!empty($managerCustomerIdsToSearch)) {
            $customerIdToSearch = array_shift($managerCustomerIdsToSearch);
            $stream = $googleAdsServiceClient->searchStream(
                $customerIdToSearch,
                $query
            );
            foreach ($stream->iterateAllElements() as $googleAdsRow) {
                $customerClient = $googleAdsRow->getCustomerClient();
                if ($customerClient->getId() === $rootCustomerId) {
                    $rootCustomerClient = $customerClient;
                    self::$rootCustomerClients[$rootCustomerId] = $rootCustomerClient;
                }
                if ($customerClient->getId() === $customerIdToSearch) {
                    continue;
                }
                $customerIdsToChildAccounts[$customerIdToSearch][] = $customerClient;
                if ($customerClient->getManager()) {
                    $alreadyVisited = array_key_exists(
                        $customerClient->getId(),
                        $customerIdsToChildAccounts
                    );
                    if (!$alreadyVisited && $customerClient->getLevel() === 1) {
                        array_push($managerCustomerIdsToSearch, $customerClient->getId());
                    }
                }
            }
        }

        return is_null($rootCustomerClient) ? null
            : [$rootCustomerClient->getId() => $customerIdsToChildAccounts];
    }
      
    private static function getAccessibleCustomers(GoogleAdsClient $googleAdsClient): array
    {
        $accessibleCustomerIds = [];
        $customerServiceClient = $googleAdsClient->getCustomerServiceClient();
        $accessibleCustomers = $customerServiceClient->listAccessibleCustomers();

        foreach ($accessibleCustomers->getResourceNames() as $customerResourceName) {
            $customer = CustomerServiceClient::parseName($customerResourceName)['customer_id'];
            $accessibleCustomerIds[] = intval($customer);
        }

        return $accessibleCustomerIds;
    }
      
    private function printAccountHierarchy(
        CustomerClient $customerClient,
        array $customerIdsToChildAccounts,
        int $depth
    ) {
        $customerId = $customerClient->getId();
        if (!array_key_first($customerIdsToChildAccounts)) $rootCustomerId = $customerId;
        else $rootCustomerId = array_key_first($customerIdsToChildAccounts);
        //print str_repeat('-', $depth * 2);
        $data = [
            'customerId' => $customerId,
            'manageCustomer' => $rootCustomerId,
            'name' => $customerClient->getDescriptiveName(),
            'currencyCode' => $customerClient->getCurrencyCode(),
            'dateTimeZone' => $customerClient->getTimeZone(),
            'is_hidden' => $customerClient->getHidden() ? '1' : '0',
            'hidden' => $customerClient->getHidden(),
            'status' => CustomerStatus::name($customerClient->getStatus()),
            'testAccount' => $customerClient->getTestAccount() ? '1' : '0',
            'canManageClients' => $rootCustomerId == $customerId ? '1' : '0',
        ];
        //echo '<pre>'.print_r($data,1).'</pre>';
        if (array_key_exists($customerId, $customerIdsToChildAccounts)) {
            foreach ($customerIdsToChildAccounts[$customerId] as $childAccount) {
                $child = self::printAccountHierarchy($childAccount, $customerIdsToChildAccounts, $depth + 1);
                $this->db->updateAccount($child);
            }
        }
        return $data;
    }
      
    public function getCampaigns($loginCustomerId = null, $customerId = null, $campaignId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        // Creates a query that retrieves all campaigns.
        $query = 'SELECT customer.id, campaign.id, campaign.name, campaign.status, campaign.serving_status, campaign.start_date, campaign.end_date, campaign.advertising_channel_type, campaign.advertising_channel_sub_type, campaign.ad_serving_optimization_status, campaign.base_campaign, campaign_budget.id, campaign_budget.name, campaign_budget.reference_count, campaign_budget.status, campaign_budget.amount_micros, campaign_budget.delivery_method, campaign.target_cpa.target_cpa_micros, campaign.frequency_caps FROM campaign WHERE campaign.status IN ("ENABLED","PAUSED","REMOVED") ';
        if($campaignId !== null){
            $query .= "AND campaign.id = $campaignId";
        }
        $query .= " ORDER BY campaign.start_date DESC";
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        $result = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $c = $googleAdsRow->getCampaign();
            $budget = $googleAdsRow->getCampaignBudget();
            $advertisingChannelType = ($c->getAdvertisingChannelType() <= 11) ? AdvertisingChannelType::name($c->getAdvertisingChannelType()) : $c->getAdvertisingChannelType();
            $data = [
                'customerId' => $googleAdsRow->getCustomer()->getId(), 
                'id' => $c->getId(), 
                'name' => $c->getName(), 
                'status' => CampaignStatus::name($c->getStatus()), 'servingStatus' => CampaignServingStatus::name($c->getServingStatus()), 
                'startDate' => $c->getStartDate(), 
                'endDate' => $c->getEndDate(), 
                'advertisingChannelType' => $advertisingChannelType, 
                'advertisingChannelSubType' => AdvertisingChannelSubType::name($c->getAdvertisingChannelSubType()), 
                'adServingOptimizationStatus' => AdServingOptimizationStatus::name($c->getAdServingOptimizationStatus()), 
                'baseCampaign' => $c->getBaseCampaign(), 
                'budgetId' => $budget->getId(), 
                'budgetName' => $budget->getName(), 
                'budgetReferenceCount' => $budget->getReferenceCount(), 
                'budgetStatus' => BudgetStatus::name($budget->getStatus()), 
                'budgetAmount' => ($budget->getAmountMicros() / 1000000), 
                'budgetDeliveryMethod' => BudgetDeliveryMethod::name($budget->getDeliveryMethod())
                //,'targetCpa' => $c->getTargetCpa()
            ];
            //echo '<pre>'.print_r($data,1).'</pre>';
            if ($this->db->updateCampaign($data))
                $result[] = $data;
        }
        return $result;
    }

    public function updateCampaign($customerId = null, $campaignId = null, $param = null)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);

        $campaignServiceClient = $this->googleAdsClient->getCampaignServiceClient();
        $data = [
            'resource_name' => ResourceNames::forCampaign($customerId, $campaignId),
        ];
        
        if(isset($param['status'])){
            if($param['status'] == 'ENABLED'){
                $data['status'] = CampaignStatus::ENABLED;
            }else{
                $data['status'] = CampaignStatus::PAUSED;
            }
        }
        
        if(isset($param['name'])){
            $data['name'] = $param['name'];
        }

        $campaign = new Campaign($data);
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setUpdate($campaign);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));
        
        $response = $campaignServiceClient->mutateCampaigns(
            $customerId,
            [$campaignOperation],
            ['responseContentType' => ResponseContentType::MUTABLE_RESOURCE]
            //캠페인 결과값도 반환 //상수 RESOURCE_NAME_ONLY - 리소스 네임만
        );
        $updatedCampaign = $response->getResults()[0];
        $campaignInfo = $updatedCampaign->getCampaign();
        if(!empty($campaignInfo)){
            $setData = [
                'id' => $campaignInfo->getId(),
            ];

            if(isset($data['status'])){
                $setData['status'] = $campaignInfo->getStatus() == 2 ? 'ENABLED' : 'PAUSED';
            }

            if(isset($data['name'])){
                $setData['name'] = $campaignInfo->getName();
            }

            $this->db->updateCampaignField($setData);
            return $setData;
        };
    }

    public function getCampaignStatusBudget($customerId, $campaignId)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = "SELECT campaign.id, campaign.status, campaign_budget.amount_micros FROM campaign WHERE campaign.status IN ('ENABLED','PAUSED','REMOVED') AND campaign.id = $campaignId";
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $c = $googleAdsRow->getCampaign();
            $budget = $googleAdsRow->getCampaignBudget();
            $data = [
                'id' => $c->getId(), 
                'status' => CampaignStatus::name($c->getStatus()), 
                'budget' => ($budget->getAmountMicros() / 1000000), 
            ];
        }
        return $data;
    }

    public function updateCampaignBudget($customerId = null, $campaignId = null, $param = null)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);

        $campaign = $this->db->getCampaign($campaignId);
        $campaign = $campaign->getRowArray();
        
        $campaignServiceClient = $this->googleAdsClient->getCampaignBudgetServiceClient();

        $param['budget'] *= 1000000; 
        $data = [
            'resource_name' => ResourceNames::forCampaignBudget($customerId, $campaign['budgetId']),
            'amount_micros' => intval($param['budget']),
        ];

        $campaignBudget = new CampaignBudget($data);
        $campaignOperation = new CampaignBudgetOperation();
        $campaignOperation->setUpdate($campaignBudget);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaignBudget));
        
        $response = $campaignServiceClient->mutateCampaignBudgets(
            $customerId,
            [$campaignOperation],
            ['responseContentType' => ResponseContentType::MUTABLE_RESOURCE]
            //캠페인 결과값도 반환 //상수 RESOURCE_NAME_ONLY - 리소스 네임만
        );
        
        $updatedCampaign = $response->getResults()[0];
        $amount = $updatedCampaign->getCampaignBudget()->getAmountMicros();
        $amount = $amount / 1000000;
        if(!empty($updatedCampaign)){
            $setData = [
                'id' => $campaign['id'],
                'amount' => $amount,
            ];

            $this->db->updateCampaignField($setData);
            return $setData['id'];
        };
    }

    private static function convertToString($value)
    {
        if (is_null($value)) {
            return NULL;
        }
        if (gettype($value) === 'boolean') {
            return $value ? 'true' : 'false';
        } elseif (gettype($value) === 'object' && get_class($value) === RepeatedField::class) {
            return json_encode(iterator_to_array($value->getIterator()));
        } else {
            //return '';
            return strval($value);
        }
    }
      
    public function getAdGroups($loginCustomerId = null, $customerId = null, $campaignId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = 'SELECT campaign.id, ad_group.id, ad_group.name, ad_group.status, ad_group.type, bidding_strategy.id, ad_group.cpc_bid_micros, ad_group.cpm_bid_micros, ad_group.target_cpa_micros FROM ad_group WHERE ad_group.status IN ("ENABLED","PAUSED","REMOVED") ';
        if ($campaignId !== null) {
            $query .= " AND campaign.id = $campaignId";
        }
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        $result = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $g = $googleAdsRow->getAdGroup();
            $c = $googleAdsRow->getCampaign();
            //$bid = $googleAdsRow->getBiddingStrategy();

            $data = [
                'campaignId' => $c->getId(), 
                'id' => $g->getId(), 
                'name' => $g->getName(), 
                'status' => AdGroupStatus::name($g->getStatus()), 
                'adGroupType' => AdGroupType::name($g->getType()),
                'biddingStrategyType' => $c->getCampaignBiddingStrategy() ?? '', 
                'cpcBidAmount' => $g->getCpcBidMicros() ?? 0,
                'cpcBidSource' => '',
                'cpmBidAmount' => $g->getCpmBidMicros() ?? 0,
                'cpmBidSource' => '',
                //'cpaBidAmount' => $g->getEffectiveTargetCpaMicros() ?? 0,
                //'cpaBidSource' => $g->getEffectiveTargetCpaSource() ?? ''
            ];
            
            /* if(!empty($bid)){
                $data['cpcBidSource'] = $bid->getEffectiveCpcBidSource() ?? '';
                $data['cpmBidSource'] = $bid->getEffectiveCpmBidSource() ?? '';
            } */

            if ($this->db->updateAdGroup($data))
                $result[] = $data;
        }
        return $result;
    }

    public function getAdgroupStatus($customerId, $adgroupId)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = "SELECT ad_group.id, ad_group.status FROM ad_group WHERE ad_group.status IN ('ENABLED','PAUSED','REMOVED') AND ad_group.id = $adgroupId";
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $g = $googleAdsRow->getAdGroup();
            $data = [
                'id' => $g->getId(), 
                'status' => AdGroupStatus::name($g->getStatus()), 
            ];
        }
        return $data;
    }

    public function updateAdGroup($customerId = null, $adsetId = null, $param = null)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);

        $adGroupServiceClient  = $this->googleAdsClient->getAdGroupServiceClient();
        $data = [
            'resource_name' => ResourceNames::forAdGroup($customerId, $adsetId),
        ];

        if(isset($param['status'])){
            if($param['status'] == 'ENABLED'){
                $data['status'] = AdGroupStatus::ENABLED;
            }else{
                $data['status'] = AdGroupStatus::PAUSED;
            }
        }

        if(isset($param['name'])){
            $data['name'] = $param['name'];
        }

        $adGroup = new AdGroup($data);
      
        $adGroupOperation = new AdGroupOperation();
        $adGroupOperation->setUpdate($adGroup);
        $adGroupOperation->setUpdateMask(FieldMasks::allSetFieldsOf($adGroup));
        
        $response = $adGroupServiceClient ->mutateAdGroups(
            $customerId,
            [$adGroupOperation],
            ['responseContentType' => ResponseContentType::MUTABLE_RESOURCE]
        );

        $updatedAdGroup = $response->getResults()[0];
        $adGroupInfo = $updatedAdGroup ->getAdGroup();
        if(!empty($adGroupInfo)){
            $setData = [
                'id' => $adGroupInfo->getId(),
            ];

            if(isset($data['status'])){
                $setData['status'] = $adGroupInfo->getStatus() == 2 ? 'ENABLED' : 'PAUSED';
            }

            if(isset($data['name'])){
                $setData['name'] = $adGroupInfo->getName();
            }

            $this->db->updateAdgroupField($setData);
            return $setData;
        };
    }
      
    public function getAds($loginCustomerId = null, $customerId = null, $adGroupId = null, $date = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        if ($date == null)
            $date = date('Y-m-d');

        $query = 'SELECT ad_group.id, ad_group_ad.ad.id, ad_group_ad.ad.name, ad_group_ad.status, ad_group_ad.policy_summary.policy_topic_entries, ad_group_ad.policy_summary.review_status, ad_group_ad.policy_summary.approval_status, ad_group_ad.ad.type, ad_group_ad.ad.image_ad.image_url, ad_group_ad.ad.final_urls, ad_group_ad.ad.url_collections, ad_group_ad.ad.video_responsive_ad.call_to_actions, ad_group_ad.ad.image_ad.mime_type, ad_group_ad.ad.responsive_display_ad.marketing_images, ad_group_ad.ad.video_responsive_ad.videos, metrics.clicks, metrics.impressions, metrics.cost_micros, segments.date FROM ad_group_ad WHERE ad_group_ad.status IN ("ENABLED","PAUSED","REMOVED") AND segments.date = "' . $date . '" ';

        if ($adGroupId !== null) {
            $query .= " AND ad_group.id = $adGroupId";
        }

        //echo $query;

        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        $result = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $g = $googleAdsRow->getAdGroup();
            $d = $googleAdsRow->getAdGroupAd();
            $metric = $googleAdsRow->getMetrics();
            $imgType = "";
            $imgUrl = "";
            $v = [];
            $assets = "";
            if (!is_null($d->getAd()->getImageAd())) {
                $imgType = MimeType::name($d->getAd()->getImageAd()->getMimeType());
                $imgUrl = $d->getAd()->getImageAd()->getImageUrl();
            }
            $finalUrl = $d->getAd()->getFinalUrls()->count() ? $d->getAd()->getFinalUrls()[0] : '';
            $adType = $d->getAd()->getType() < 35 ? AdType::name($d->getAd()->getType()) : $d->getAd()->getType();
            if ($adType == "RESPONSIVE_DISPLAY_AD") {
                foreach ($d->getAd()->getResponsiveDisplayAd()->getMarketingImages() as $row) {
                    $v[] = array_pop(explode('/', $row->getAsset()));
                }
                $assets = implode(',', $v);
            }
            if ($adType == "VIDEO_RESPONSIVE_AD") {
                foreach ($d->getAd()->getVideoResponsiveAd()->getVideos() as $row) {
                    $v[] = array_pop(explode('/', $row->getAsset()));
                }
                $assets = implode(',', $v);
            }
            if ($finalUrl) {
                $url = parse_url($finalUrl);
                $url = array_merge(['evt_no' => @array_pop(explode('/', $url['path'])), 'group' => ''], $url);
                if (preg_match('/event\./', $url['host']) && preg_match('/^[0-9]+$/', $url['evt_no']))
                    $url['group'] = 'ger';
                else if (preg_match('/^app_[0-9]+$/', $url['evt_no']))
                    $url['group'] = 'ghr';
                //print_r($url);
                //echo '<br>';
            }
            
            $status = AdGroupAdStatus::name($d->getStatus());    
            $reviewStatus = PolicyReviewStatus::name($d->getPolicySummary()->getReviewStatus());
            $approvalStatus = PolicyApprovalStatus::name($d->getPolicySummary()->getApprovalStatus());
            $topics = [];
            $topic = '';
            foreach($d->getPolicySummary()->getPolicyTopicEntries() as $entry) {
                $topics[] = $entry->getTopic();
            }
            if(count($topics)) $topic = implode(',', $topics);
            $data = [
                'adgroupId' => $g->getId(), 
                'id' => $d->getAd()->getId() ? $d->getAd()->getId() : "", 
                'name' => $d->getAd()->getName() ? $d->getAd()->getName() : "", 
                'status' => $status ? $status : "", 
                'reviewStatus' => $reviewStatus ? $reviewStatus : "", 
                'approvalStatus' => $approvalStatus ? $approvalStatus : "", 
                'policyTopic' => $topic,
                'code' => "", 
                'adType' => $adType ? $adType : "", 
                'mediaType' => $imgType ? $imgType : "",
                'imageUrl' => $imgUrl ? $imgUrl : "",
                'assets' => $assets ? $assets : "",
                'finalUrl' => $finalUrl ? $finalUrl : "", 
                'date' => $date ? $date : "",
                'clicks' => $metric->getClicks() ? $metric->getClicks() : "", 
                'impressions' => $metric->getImpressions(), 
                'cost' => round($metric->getCostMicros() / 1000000)
            ];

            //echo '<pre>'.print_r($data,1).'</pre>';
            if ($this->db->updateAd($data))
                $result[] = $data;
        }
        
        return $result;
    }
      
    public function getAdStatus($customerId, $adId)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = "SELECT ad_group_ad.ad.id, ad_group_ad.status FROM ad_group_ad WHERE ad_group_ad.status IN ('ENABLED','PAUSED','REMOVED') AND ad_group_ad.ad.id = $adId";
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $d = $googleAdsRow->getAdGroupAd();
            $data = [
                'id' => $d->getAd()->getId(), 
                'status' => AdGroupAdStatus::name($d->getStatus()), 
            ];
        }
        return $data;
    }

    public function updateAdGroupAd($customerId = null, $adGroupId = null, $adId = null, $param = null)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);

        $adGroupId = $this->db->getAdGroupIdByAd($adId);
        $adGroupId = $adGroupId->getRow()->adgroupId;
        $googleAdsClient  = $this->googleAdsClient->getAdGroupAdServiceClient();
        $data = [
            'resource_name' => ResourceNames::forAdGroupAd($customerId,$adGroupId, $adId),
        ];

        if(isset($param['status'])){
            if($param['status'] == 'ENABLED'){
                $data['status'] = AdGroupAdStatus::ENABLED;
            }else{
                $data['status'] = AdGroupAdStatus::PAUSED;
            }
        }

        $adGroupAd = new AdGroupAd($data);

        $adGroupAdOperation = new AdGroupAdOperation();
        $adGroupAdOperation->setUpdate($adGroupAd);
        $adGroupAdOperation->setUpdateMask(FieldMasks::allSetFieldsOf($adGroupAd));

        $response = $googleAdsClient ->mutateAdGroupAds(
            $customerId,
            [$adGroupAdOperation],
            ['responseContentType' => ResponseContentType::MUTABLE_RESOURCE]
        );

        $updatedAdGroupAd = $response->getResults()[0];
        $adGroupAdInfo = $updatedAdGroupAd->getAdGroupAd();
        if(!empty($adGroupAdInfo)){
            $setData = [
                'id' => $adGroupAdInfo->getAd()->getId(),
            ];
            
            if(isset($data['status'])){
                $setData['status'] = $adGroupAdInfo->getStatus() == 2 ? 'ENABLED' : 'PAUSED';
            }

            $this->db->updateAdField($setData);
            return $setData;
        };
    }

    public function updateAd($customerId = null, $adGroupId = null, $adId = null, $param = null)
    {
        $account = $this->db->getAccounts(0, "AND customerId = {$customerId}");
        $account = $account->getRowArray();
        self::setCustomerId($account['manageCustomer']);

        $adGroupId = $this->db->getAdGroupIdByAd($adId);
        $adGroupId = $adGroupId->getRow()->adgroupId;
        $googleAdsClient  = $this->googleAdsClient->getAdServiceClient();
        $data = [
            'resource_name' => ResourceNames::forAd($customerId, $adId),
        ];

        if(isset($param['name'])){
            $data['name'] = $param['name'];
        }
        //dd($googleAdsClient->getAd($data['resource_name']));
        $ad = new Ad($data);

        $adOperation = new AdOperation();
        $adOperation->setUpdate($ad);
        $adOperation->setUpdateMask(FieldMasks::allSetFieldsOf($ad));
        
        $response = $googleAdsClient ->mutateAds(
            $customerId,
            [$adOperation],
            ['responseContentType' => ResponseContentType::MUTABLE_RESOURCE]
        );

        $updatedAd = $response->getResults()[0];
        $adInfo = $updatedAd->getAd();
        if(!empty($adInfo)){
            $setData = [
                'id' => $adInfo->getId(),
            ];
            
            if(isset($data['name'])){
                $setData['name'] = $adInfo->getName();
            }

            $this->db->updateAdField($setData);
            return $setData;
        };
    }

    public function getAsset($loginCustomerId = null, $customerId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        // Creates a query that will retrieve all image assets.
        $query = "SELECT asset.id, asset.name, asset.type, " .
            "asset.youtube_video_asset.youtube_video_id, " .
            "asset.youtube_video_asset.youtube_video_title, " .
            "asset.text_asset.text, " .
            "asset.image_asset.full_size.url " .
            "FROM asset";
        // Issues a search request by specifying page size.
        $response = $googleAdsServiceClient->searchStream($customerId, $query);

        // Iterates over all rows in all pages and prints the requested field values for the image
        // asset in each row.
        $result = [];
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            $asset = $googleAdsRow->getAsset();
            $type = AssetType::name($asset->getType());
            $data = [
                'id' => $asset->getId(), 
                'name' => $asset->getName(), 
                'type' => $type
            ];
            $tData = [];
            if ($type == 'IMAGE') {
                $tData = [
                    'url' => $asset->getImageAsset()->getFullSize()->getUrl() ?? ''
                ];
            } else if ($type == 'YOUTUBE_VIDEO') {
                if(!empty($asset->getYoutubeVideoAsset())){
                    if (method_exists($asset->getYoutubeVideoAsset(), 'getYoutubeVideoId')) {
                        $tData = [
                            'video_id' => $asset->getYoutubeVideoAsset()->getYoutubeVideoId() ?? '', 
                            'name' => $asset->getYoutubeVideoAsset()->getYoutubeVideoTitle() ?? '', 
                            'url' => 'http://i4.ytimg.com/vi/' . $asset->getYoutubeVideoAsset()->getYoutubeVideoId() . '/mqdefault.jpg' ?? ''
                        ];
                    }
                }
            } else if ($type == 'TEXT') {
                $tData = [
                    'name' => $asset->getTextAsset()->getText() ?? ''
                ];
            }
            $data = array_merge($data, $tData);
            $dbResult = $this->db->updateAsset($data);
            if(!empty($dbResult)){
                $result[] = $data;
            }
        }
        return $result;
    }
      
    public function getAll($date = null)
    {
        $this->getAccounts();
        $accounts = $this->db->getAccounts(0, "AND status = 'ENABLED'");
        $step = 1;
        $total = $accounts->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."계정/계정예산/에셋/캠페인/그룹/소재/보고서 업데이트를 시작합니다.", "light_red");
        foreach ($accounts->getResultArray() as $account) {
            CLI::showProgress($step++, $total);
            $this->getAccountBudgets($account['manageCustomer'], $account['customerId']);
            $assets = $this->getAsset($account['manageCustomer'], $account['customerId']);
            
            $campaigns = $this->getCampaigns($account['manageCustomer'], $account['customerId']);
            
            if (count($campaigns)) {
                $adGroups = $this->getAdGroups($account['manageCustomer'], $account['customerId']);
                $ads = $this->getAds($account['manageCustomer'], $account['customerId'], null, $date);
            }
        }
    }
      
    public function setManualUpdate($campaigns)
    {
        if(!$campaigns){return false;}
        foreach ($campaigns as $campaign) {
            $campaignResult = $this->getCampaigns($campaign['manageCustomer'], $campaign['customerId'], $campaign['id']);
            if(!$campaignResult){
                return false;
            }

            $adGroupResult = $this->getAdGroups($campaign['manageCustomer'], $campaign['customerId'], $campaign['id']);
            if(!$adGroupResult){
                return false;
            }
            
            foreach ($adGroupResult as $adGroup) {
                $adResult[] = $this->getAds($campaign['manageCustomer'], $campaign['customerId'], $adGroup['id']);
                if(!$adResult){
                    return false;
                }
            }
        }
        
        return true;
    }

    public function landingGroup($title)
    {
        if (empty($title)) return [];
        preg_match_all('/^.*?\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);   
        if (empty($matches[9][0])) {    // site underscore exception
            preg_match_all('/\#([0-9]+)?(\_([0-9]+))?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches_re);
            $matches[9][0] = $matches_re[11][0] ?? '';
            $matches[3][0] = ($matches[3][0] ?? '') . ($matches_re[4][0] ?? '');
            $matches[6][0] = $matches_re[8][0] ?? '';
            $matches[12][0] = $matches_re[14][0] ?? '';
            // $matches[12][0] = $matches_re[14][0];
        }
        // echo '<pre>' . print_r($matches_re, 1) . '</pre>';
        if (empty($matches[1][0])) { // Event SEQ를 추출할 수 없다면, $title 변수에 캠페인명이 넘어왔다고 보고 다른 로직으로 $matches 대입
            preg_match_all('/^([^>]+)?>([^|]+)(>[^|]+)||((http|https):\/\/[^\"\'\s()]+)$/', $title, $mc);
            $code = explode('>', $mc[2][0] ?? '');
            $matches[1][0] = trim($code[0] ?? '');
            $matches[6][0] = trim($code[1] ?? '');
            $matches[9][0] = trim($code[2] ?? '');
            $matches[12][0] = trim(str_replace('^', '', $code[3] ?? ''));
            $url = $mc[4][4];
            $qs = parse_url($url, PHP_URL_QUERY);
            parse_str($qs, $params);
            $matches[3][0] = trim($params['site'] ?? '');
        }
        switch ($matches[9][0]) {
            case 'ger': $media = '이벤트 랜딩'; break;
            case 'gercpm': $media = '이벤트 랜딩_cpm'; break;
            case 'cpm': $media = 'cpm'; break;
            default: $media = ''; break;
        }
        $result = array('name' => '', 'media' => '', 'event_seq' => '', 'site' => '', 'db_price' => 0, 'period_ad' => '');

        if ($media) {
            $period_ad = isset($matches[12][0]) && $matches[12][0] ? $matches[12][0] : 0;
            $result['name']         = $title;
            $result['media']        = $media;
            $result['event_seq']    = $matches[1][0];
            $result['site']         = $matches[3][0];
            $result['db_price']     = $matches[6][0];
            $result['period_ad']    = $period_ad;
            return $result;
        }
        return [];
    }
      
    public function getAdsUseLanding($date = null)
    { //유효DB 개수 업데이트
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $step = 1;
        $ads = $this->db->getAdLeads($date);
        $total = $ads->getNumRows();
        if (empty($total)) {
            return null;
        }
        $i = 0;
        $result = [];  
        CLI::write("[".date("Y-m-d H:i:s")."]"."유효DB 개수 수신을 시작합니다.", "light_red");
        foreach ($ads->getResultArray() as $row) {
            $error = [];
            CLI::showProgress($step++, $total);

            $title = (trim($row['code']) ? $row['code'] : (strpos($row['ad_name'], '#') !== false ? $row['ad_name'] : $row['campaign_name'] . '||' . $row['finalUrl']));

            // CLI::write("[".date('[H:i:s]') ."] 광고({$row['ad_id']}) 유효DB개수 업데이트 - {$title}");

            $landing = [];
            $landing = $this->landingGroup($title);
            $data = [];
            $data = [
                 'date' => $date,
                 'ad_id' => $row['ad_id']
            ];
            
            $data = array_merge($data, $landing);
 
            if (!empty($landing) && !preg_match('/cpm/', $landing['media'])) {
                if (!$landing['event_seq']){
                    $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 이벤트번호 미입력' . PHP_EOL;
                }
                if (!$landing['db_price']){
                    $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): DB단가 미입력' . PHP_EOL;
                }
            }
            if(empty($landing) && isset($row['ad_name']) && preg_match('/&[a-z]+/', $row['ad_name'])){
                $error[] = $row['ad_name'] . '(' . $row['ad_id'] . '): 인식 오류' . PHP_EOL;
            }
            if(!empty($error)){
                foreach($error as $err){
                    CLI::write("{$err}", "light_purple");
                }
            }
            if(empty($landing)){
                continue;
            }
            $dp = $this->db->getDbPrice($data);
            $leads = $this->db->getLeads($data);
            $cpm = false;
            if(is_null($leads) && isset($data['media']) && preg_match('/cpm/i', $data['media'])){
                $cpm = true;
            }
            $db_price = $data['db_price'] ?? 0;
            if(isset($dp['db_price']) && $data['date'] != date('Y-m-d')){
                $db_price = $data['db_price'] = $dp['db_price'];
            }
            /* 
            *수익, 매출액 계산
            !xxxcpm - 유효db n / 수익,매출0
            !cpm - 유효db 0 / 수익,매출0
            !period - ^25 = *0.25
            */
            $sp_data = json_decode($row['spend_data'],1);
            $period_margin = [];
            if(!isset($data['event_seq']) && isset($data['media'])) {
                foreach($sp_data as $hour => $spend) {
                    $margin = 0;
                    if($data['period_ad']) {
                        $period_ad = is_numeric($data['period_ad']) ? $data['period_ad'] : 0;
                        $margin = $spend * ('0.' . $period_ad);
                    }
                    $data['data'][] = ['hour' => $hour,'spend' => $spend,'count' => "",'sales' => "",'margin' => $margin];
                }
            }
            $initZero = false;
            //cpm (fhrm, fhspcpm, jhrcpm) 계산을 무효화
            if(isset($data['media'])){
                if(preg_match('/cpm/i', $data['media'])){
                    $initZero = true;
                }
            }
            $lead = [];
            if(!is_null($leads)) {
                foreach($leads->getResultArray() as $row) {
                    $sales = 0;
                    $db_count = $row['db_count'];
                    if($db_price) $sales = $db_price * $db_count;
                    if($initZero) $sales = 0;
                    if(preg_match('/cpm/i', $data['media'])) $db_count = 0;
                    $lead[$row['hour']] = [
                        'sales' => $sales
                        ,'db_count' => $db_count
                    ];
                }
            }
            $_spend = $_count = $_sales = $_margin = 0;
            for($i=0; $i<=23; $i++) { //DB수량이 없어도 지출금액이 갱신되어야하기 때문에 0~23시까지 모두 저장
                $hour = $i;
                $spend = $sp_data[$i] ?? 0;
                $count = $lead[$i]['db_count'] ?? 0;
                $sales = $lead[$i]['sales'] ?? 0;
                $margin = $sales - $spend;
                if($initZero) $margin = $sales = 0;
                if(preg_match('/cpm/i', $data['media'])) $db_count = 0;
                if($data['period_ad']) {
                    $period_ad = is_numeric($data['period_ad']) ? $data['period_ad'] : 0;
                    $margin = $spend * ('0.' . $period_ad);
                }
                $_spend += $spend;
                $_count += $count;
                $_sales += $sales;
                $_margin += $margin;
            }
            
            $data['data'][] = [
                'hour' => 0
                ,'spend' => $_spend
                ,'count' => $_count
                ,'sales' => $_sales
                ,'margin' => $_margin
            ];
            $result = array_merge($result, $data);
            if(isset($data['ad_id'])){
                $this->db->updateReport($data);
            }
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
}
