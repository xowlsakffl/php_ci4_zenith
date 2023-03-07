<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//ini_set('max_execution_time', 1800);
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

require_once __DIR__ . '/adsdb.php';
require_once __DIR__ . '/google-ads-php/vendor/autoload.php';

use CodeIgniter\CLI\CLI;
use GetOpt\GetOpt;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Examples\Utils\Helper;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V12\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V12\Resources\CustomerClient;
use Google\Ads\GoogleAds\V12\Resources\BiddingStrategy;
use Google\Ads\GoogleAds\V12\Resources\ChangeEvent;
use Google\Ads\GoogleAds\V12\Services\CampaignService;
use Google\Ads\GoogleAds\V12\Services\AdGroupAdService;
use Google\Ads\GoogleAds\V12\Services\CustomerServiceClient;
use Google\Ads\GoogleAds\V12\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V12\Services\FrequencyCap;
use Google\ApiCore\ApiException;
use Google\Ads\GoogleAds\V12\Common\AdTextAsset;
use Google\Ads\GoogleAds\V12\Common\AdImageAsset;
use Google\Ads\GoogleAds\V12\Common\AdVideoAsset;
use Google\Ads\GoogleAds\V12\Enums\AccountBudgetStatusEnum\AccountBudgetStatus;
use Google\Ads\GoogleAds\V12\Enums\SpendingLimitTypeEnum\SpendingLimitType;
use Google\Ads\GoogleAds\V12\Enums\TimeTypeEnum\TimeType;
use Google\Ads\GoogleAds\V12\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V12\Enums\CampaignServingStatusEnum\CampaignServingStatus;
use Google\Ads\GoogleAds\V12\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V12\Enums\AdGroupTypeEnum\AdGroupType;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelSubTypeEnum\AdvertisingChannelSubType;
use Google\Ads\GoogleAds\V12\Enums\AdServingOptimizationStatusEnum\AdServingOptimizationStatus;
use Google\Ads\GoogleAds\V12\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V12\Enums\PolicyReviewStatusEnum\PolicyReviewStatus;
use Google\Ads\GoogleAds\V12\Enums\PolicyApprovalStatusEnum\PolicyApprovalStatus;
use Google\Ads\GoogleAds\V12\Enums\ServedAssetFieldTypeEnum\ServedAssetFieldType;
use Google\Ads\GoogleAds\V12\Enums\AdTypeEnum\AdType;
use Google\Ads\GoogleAds\V12\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V12\Enums\BudgetStatusEnum\BudgetStatus;
use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V12\Enums\CustomerStatusEnum\CustomerStatus;
use Google\Ads\GoogleAds\V12\Enums\MimeTypeEnum\MimeType;
use Google\Ads\GoogleAds\V12\Enums\ChangeEventResourceTypeEnum\ChangeEventResourceType;
use Google\Ads\GoogleAds\V12\Enums\ChangeClientTypeEnum\ChangeClientType;
use Google\Ads\GoogleAds\V12\Enums\AssetTypeEnum\AssetType;
use Google\Ads\GoogleAds\V12\Enums\ResourceChangeOperationEnum\ResourceChangeOperation;
use Google\Ads\GoogleAds\V12\Resources\Campaign;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;

class GoogleAds
{
    private $session;
    private $clientCustomerId;
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

    public function getCampaigns($loginCustomerId = null, $customerId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        // Creates a query that retrieves all campaigns.
        $query = 'SELECT customer.id, campaign.id, campaign.name, campaign.status, campaign.serving_status, campaign.start_date, campaign.end_date, campaign.advertising_channel_type, campaign.advertising_channel_sub_type, campaign.ad_serving_optimization_status, campaign.base_campaign, campaign_budget.id, campaign_budget.name, campaign_budget.reference_count, campaign_budget.status, campaign_budget.amount_micros, campaign_budget.delivery_method, campaign.target_cpa.target_cpa_micros, campaign.frequency_caps FROM campaign WHERE campaign.status IN ("ENABLED","PAUSED","REMOVED") ORDER BY campaign.start_date DESC ';
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        $result = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $c = $googleAdsRow->getCampaign();
            $budget = $googleAdsRow->getCampaignBudget();
            $advertisingChannelType = ($c->getAdvertisingChannelType() <= 11) ? AdvertisingChannelType::name($c->getAdvertisingChannelType()) : $c->getAdvertisingChannelType();
            $data = [
                'customerId' => $googleAdsRow->getCustomer()->getId(), 'id' => $c->getId(), 'name' => $c->getName(), 'status' => CampaignStatus::name($c->getStatus()), 'servingStatus' => CampaignServingStatus::name($c->getServingStatus()), 'startDate' => $c->getStartDate(), 'endDate' => $c->getEndDate(), 'advertisingChannelType' => $advertisingChannelType, 'advertisingChannelSubType' => AdvertisingChannelSubType::name($c->getAdvertisingChannelSubType()), 'adServingOptimizationStatus' => AdServingOptimizationStatus::name($c->getAdServingOptimizationStatus()), 'baseCampaign' => $c->getBaseCampaign()
                //,'frequencyCaps' => $c->getFrequencyCaps()
                , 'budgetId' => $budget->getId(), 'budgetName' => $budget->getName(), 'budgetReferenceCount' => $budget->getReferenceCount(), 'budgetStatus' => BudgetStatus::name($budget->getStatus()), 'budgetAmount' => ($budget->getAmountMicros() / 1000000), 'budgetDeliveryMethod' => BudgetDeliveryMethod::name($budget->getDeliveryMethod())
                //,'targetCpa' => $c->getTargetCpa()
            ];
            //echo '<pre>'.print_r($data,1).'</pre>';
            if ($this->db->updateCampaign($data))
                $result[] = $data;
        }
        return $result;
    }

    public function updateCampaign($customerId = null, $campaignId = null, $data = [])
    {
        self::setCustomerId($customerId);
        $campaignServiceClient = $this->googleAdsClient->getCampaignServiceClient();

        $campaign = new Campaign([
            'resource_name' => ResourceNames::forCampaign($customerId, $campaignId),
            $data
        ]);

        $campaignOperation = new CampaignOperation();
        $campaignOperation->setUpdate($campaign);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

        // Issues a mutate request to update the campaign.
        $response = $campaignServiceClient->mutateCampaigns(
            $customerId,
            [$campaignOperation]
        );

        // Prints the resource name of the updated campaign.
        /** @var Campaign $updatedCampaign */
        $updatedCampaign = $response->getResults()[0];
        printf(
            "Updated campaign with resource name: '%s'%s",
            $updatedCampaign->getResourceName(),
            PHP_EOL
        );
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
            //$bid = BiddingStrategy $googleAdsRow->getBiddingStrategy();
            $data = [
                'campaignId' => $c->getId(), 'id' => $g->getId(), 'name' => $g->getName(), 'status' => AdGroupStatus::name($g->getStatus()), 'adGroupType' => AdGroupType::name($g->getType())
                //,'biddingStrategyType' => BiddingStrategyType::name($c->getBiddingStrategy())
                , 'biddingStrategyType' => $c->getCampaignBiddingStrategy(), 'cpcBidAmount' => $g->getCpcBidMicros()
                //,'cpcBidSource' => $bid->getEffectiveCpcBidSource()
                , 'cpmBidAmount' => $g->getCpmBidMicros()
                //,'cpmBidSource' => $bid->getEffectiveCpmBidSource()
                // ,'cpaBidAmount' => $g->getCpaBidAmount()
                // ,'cpaBidSource' => $g->getCpaBidSource()
            ];
            if ($this->db->updateAdGroup($data))
                $result[] = $data;
        }
        //echo '<pre>'.print_r($data,1).'</pre>';
        return $result;
    }

    public function getAds($loginCustomerId = null, $customerId = null, $adGroupId = null, $date = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        if ($date == null)
            $date = date('Y-m-d');
        $query = 'SELECT ad_group.id, ad_group_ad.ad.id, ad_group_ad.ad.name, ad_group_ad.status, ad_group_ad.policy_summary.review_status, ad_group_ad.policy_summary.approval_status, ad_group_ad.ad.type, ad_group_ad.ad.image_ad.image_url, ad_group_ad.ad.final_urls, ad_group_ad.ad.url_collections, ad_group_ad.ad.video_responsive_ad.call_to_actions, ad_group_ad.ad.image_ad.mime_type, ad_group_ad.ad.responsive_display_ad.marketing_images, ad_group_ad.ad.video_responsive_ad.videos, metrics.clicks, metrics.impressions, metrics.cost_micros FROM ad_group_ad WHERE ad_group_ad.status IN ("ENABLED","PAUSED","REMOVED") AND segments.date = "' . $date . '" ';
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
            //var_dump(PolicyReviewStatus::name($d->getPolicySummary()->getReviewStatus())); exit;
            $data = [
                'adgroupId' => $g->getId(), 'id' => $d->getAd()->getId(), 'name' => $d->getAd()->getName(), 'status' => AdGroupAdStatus::name($d->getStatus()), 'reviewStatus' => PolicyReviewStatus::name($d->getPolicySummary()->getReviewStatus()), 'approvalStatus' => PolicyApprovalStatus::name($d->getPolicySummary()->getApprovalStatus()), 'code' => "", 'adType' => $adType, 'mediaType' => $imgType, 'imageUrl' => $imgUrl, 'assets' => $assets, 'finalUrl' => $finalUrl, 'date' => $date, 'clicks' => $metric->getClicks(), 'impressions' => $metric->getImpressions(), 'cost' => round($metric->getCostMicros() / 1000000)
            ];
            //echo '<pre>'.print_r($data,1).'</pre>';
            if ($this->db->updateAd($data))
                $result[] = $data;
        }
        return $result;
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
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            $asset = $googleAdsRow->getAsset();
            $type = AssetType::name($asset->getType());
            $data = [
                'id' => $asset->getId(), 'name' => $asset->getName(), 'type' => $type
            ];
            $tData = [];
            if ($type == 'IMAGE') {
                $tData = [
                    'url' => $asset->getImageAsset()->getFullSize()->getUrl()
                ];
            } else if ($type == 'YOUTUBE_VIDEO') {
                if (method_exists($asset->getYoutubeVideoAsset(), 'getYoutubeVideoId')) {
                    $tData = [
                        'video_id' => $asset->getYoutubeVideoAsset()->getYoutubeVideoId(), 'name' => $asset->getYoutubeVideoAsset()->getYoutubeVideoTitle(), 'url' => 'http://i4.ytimg.com/vi/' . $asset->getYoutubeVideoAsset()->getYoutubeVideoId() . '/mqdefault.jpg'
                    ];
                }
            } else if ($type == 'TEXT') {
                $tData = [
                    'name' => $asset->getTextAsset()->getText()
                ];
            }
            $data = array_merge($data, $tData);
            if ($this->db->updateAsset($data))
                $result[] = $data;
        }
        return $result;
    }

    public function getAll($date = null)
    {
        $this->getAccounts();
        echo date('[H:i:s]') . " - 계정 업데이트 완료" . PHP_EOL;
        ob_flush();
        flush();
        usleep(1);
        $accounts = $this->db->getAccounts(0, "AND status = 'ENABLED'");
        $step = 1;
        $total = $accounts->getNumRows();
        CLI::write("[".date("Y-m-d H:i:s")."]"."전체 광고계정 수신을 시작합니다.", "light_red");
        foreach ($accounts->getResultArray() as $account) {
            CLI::showProgress($step++, $total);
            echo date('[H:i:s]') . "{$account['customerId']}({$account['name']}) - ";
            $this->getAccountBudgets($account['manageCustomer'], $account['customerId']);
            if ($account['status'] != 'ENABLED') {
                echo $account['status'] . ' 업데이트 미진행' . PHP_EOL;
                continue;
            }
            $assets = $this->getAsset($account['manageCustomer'], $account['customerId']);
            $campaigns = $this->getCampaigns($account['manageCustomer'], $account['customerId']);
            if (count($campaigns)) {
                $adGroups = $this->getAdGroups($account['manageCustomer'], $account['customerId']);
                $ads = $this->getAds($account['manageCustomer'], $account['customerId'], null, $date);
            }
            //echo '<pre>'.print_r($campaigns,1).'</pre>';
            //echo '<pre>'.str_repeat('-', 2).print_r($adGroups,1).'</pre>';
            //echo '<pre>'.str_repeat('-', 4).print_r($ads,1).'</pre>';
            /*
			foreach($campaigns as $campaign) {
				
				echo '<pre>'.str_repeat('-', 2).print_r($adGroups,1).'</pre>';
				foreach($adGroups as $adGroup) {
					
					echo '<pre>'.str_repeat('-', 4).print_r($ads,1).'</pre>';
				}
			}
			*/
            echo ' 업데이트 완료';
            echo PHP_EOL;
            ob_flush();
            flush();
            usleep(1);
        }
    }

    public function getChangeEvent($loginCustomerId = null, $customerId = null)
    {
        self::setCustomerId($loginCustomerId);
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = 'SELECT change_event.resource_name, '
            . 'change_event.change_date_time, '
            . 'change_event.change_resource_name, '
            . 'change_event.user_email, '
            . 'change_event.client_type, '
            . 'change_event.change_resource_type, '
            . 'change_event.old_resource, '
            . 'change_event.new_resource, '
            . 'change_event.resource_change_operation, '
            . 'change_event.changed_fields '
            . 'FROM change_event '
            . sprintf(
                'WHERE change_event.change_date_time <= %s ',
                date_format(new DateTime('+1 day'), 'Ymd')
            ) . sprintf(
                'AND change_event.change_date_time >= %s ',
                date_format(new DateTime('-14 days'), 'Ymd')
            ) . 'ORDER BY change_event.change_date_time DESC '
            . 'LIMIT 5';
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);
        $result = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $changeEvent = $googleAdsRow->getChangeEvent();
            $oldResource = $changeEvent->getOldResource();
            $newResource = $changeEvent->getNewResource();

            $isResourceTypeKnown = true;
            $oldResourceEntity = null;
            $newResourceEntity = null;
            switch ($changeEvent->getChangeResourceType()) {
                case ChangeEventResourceType::AD:
                    $oldResourceEntity = $oldResource->getAd();
                    $newResourceEntity = $newResource->getAd();
                    break;
                case ChangeEventResourceType::AD_GROUP:
                    $oldResourceEntity = $oldResource->getAdGroup();
                    $newResourceEntity = $newResource->getAdGroup();
                    break;
                case ChangeEventResourceType::AD_GROUP_AD:
                    $oldResourceEntity = $oldResource->getAdGroupAd();
                    $newResourceEntity = $newResource->getAdGroupAd();
                    break;
                case ChangeEventResourceType::AD_GROUP_ASSET:
                    $oldResourceEntity = $oldResource->getAdGroupAsset();
                    $newResourceEntity = $newResource->getAdGroupAsset();
                    break;
                case ChangeEventResourceType::AD_GROUP_CRITERION:
                    $oldResourceEntity = $oldResource->getAdGroupCriterion();
                    $newResourceEntity = $newResource->getAdGroupCriterion();
                    break;
                case ChangeEventResourceType::AD_GROUP_BID_MODIFIER:
                    $oldResourceEntity = $oldResource->getAdGroupBidModifier();
                    $newResourceEntity = $newResource->getAdGroupBidModifier();
                    break;
                case ChangeEventResourceType::ASSET:
                    $oldResourceEntity = $oldResource->getAsset();
                    $newResourceEntity = $newResource->getAsset();
                    break;
                case ChangeEventResourceType::CAMPAIGN:
                    $oldResourceEntity = $oldResource->getCampaign();
                    $newResourceEntity = $newResource->getCampaign();
                    break;
                case ChangeEventResourceType::CAMPAIGN_ASSET:
                    $oldResourceEntity = $oldResource->getCampaignAsset();
                    $newResourceEntity = $newResource->getCampaignAsset();
                    break;
                case ChangeEventResourceType::CAMPAIGN_BUDGET:
                    $oldResourceEntity = $oldResource->getCampaignBudget();
                    $newResourceEntity = $newResource->getCampaignBudget();
                    break;
                case ChangeEventResourceType::CAMPAIGN_CRITERION:
                    $oldResourceEntity = $oldResource->getCampaignCriterion();
                    $newResourceEntity = $newResource->getCampaignCriterion();
                    break;
                case ChangeEventResourceType::AD_GROUP_FEED:
                    $oldResourceEntity = $oldResource->getAdGroupFeed();
                    $newResourceEntity = $newResource->getAdGroupFeed();
                    break;
                case ChangeEventResourceType::CAMPAIGN_FEED:
                    $oldResourceEntity = $oldResource->getCampaignFeed();
                    $newResourceEntity = $newResource->getCampaignFeed();
                    break;
                case ChangeEventResourceType::CUSTOMER_ASSET:
                    $oldResourceEntity = $oldResource->getCustomerAsset();
                    $newResourceEntity = $newResource->getCustomerAsset();
                    break;
                case ChangeEventResourceType::FEED:
                    $oldResourceEntity = $oldResource->getFeed();
                    $newResourceEntity = $newResource->getFeed();
                    break;
                case ChangeEventResourceType::FEED_ITEM:
                    $oldResourceEntity = $oldResource->getFeedItem();
                    $newResourceEntity = $newResource->getFeedItem();
                    break;
                default:
                    $isResourceTypeKnown = false;
                    break;
            }
            if (!$isResourceTypeKnown) {
                printf(
                    "Unknown change_resource_type %s.%s",
                    ChangeEventResourceType::name($changeEvent->getChangeResourceType()),
                    PHP_EOL
                );
            }
            $resourceChangeOperation = $changeEvent->getResourceChangeOperation();
            printf(
                "On %s, user '%s' used interface '%s' to perform a(n) '%s' operation on a '%s' "
                    . "with resource name '%s'.%s",
                $changeEvent->getChangeDateTime(),
                $changeEvent->getUserEmail(),
                ChangeClientType::name($changeEvent->getClientType()),
                ResourceChangeOperation::name($resourceChangeOperation),
                ChangeEventResourceType::name($changeEvent->getChangeResourceType()),
                $changeEvent->getChangeResourceName(),
                '<br>'
            );

            if (
                $resourceChangeOperation !== ResourceChangeOperation::CREATE
                && $resourceChangeOperation !== ResourceChangeOperation::UPDATE
            ) {
                continue;
            }
            foreach ($changeEvent->getChangedFields()->getPaths() as $path) {
                $newValueStr = self::convertToString(
                    FieldMasks::getFieldValue($path, $newResourceEntity, true)
                );
                if ($resourceChangeOperation === ResourceChangeOperation::CREATE) {
                    printf("'$path' set to '%s'.%s", $newValueStr, '<br>');
                } elseif ($resourceChangeOperation === ResourceChangeOperation::UPDATE) {
                    echo FieldMasks::getFieldValue($path, $oldResourceEntity, true);
                    printf(
                        "'$path' changed from '%s' to '%s'.%s",
                        self::convertToString(
                            FieldMasks::getFieldValue($path, $oldResourceEntity, true)
                        ),
                        $newValueStr,
                        '<br>'
                    );
                }
            }
        }
        //echo '<pre>'.print_r($data,1).'</pre>';
        return $result;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function landingGroup($title)
    {
        if (!$title) {
            return null;
        }
        preg_match_all('/^.*?\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        if (!$matches[9][0]) {    // site underscore exception
            preg_match_all('/\#([0-9]+)?(\_([0-9]+))?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches_re);
            $matches[9][0] = $matches_re[11][0];
            $matches[3][0] = $matches[3][0] . $matches_re[4][0];
            $matches[6][0] = $matches_re[8][0];
            // $matches[12][0] = $matches_re[14][0];
        }
        if (!$matches[1][0]) { // Event SEQ를 추출할 수 없다면, $title 변수에 캠페인명이 넘어왔다고 보고 다른 로직으로 $matches 대입
            preg_match_all('/^([^>]+)?>([^|]+)(>[^|]+)||((http|https):\/\/[^\"\'\s()]+)$/', $title, $mc);
            $code = explode('>', $mc[2][0]);
            $matches[1][0] = trim($code[0]);
            $matches[6][0] = trim($code[1]);
            $matches[9][0] = trim($code[2]);
            $matches[12][0] = trim(str_replace('^', '', $code[3]));
            $url = $mc[4][4];
            $qs = parse_url($url, PHP_URL_QUERY);
            parse_str($qs, $params);
            $matches[3][0] = trim($params['site']);
        }
        // echo $title;
        // echo '<pre>' . print_r($matches, 1) . '</pre>';
        $db_prefix = '';
        switch ($matches[9][0]) {
            case 'ghr':
                $media = '핫이벤트 룰렛';
                if ($matches[1][0]) $db_prefix = 'app_';
                break;
            case 'ghrcpm':
                $media = '핫이벤트 룰렛_cpm';
                if ($matches[1][0]) $db_prefix = 'app_';
                break;
            case 'ger':
                $media = '이벤트 랜딩';
                if ($matches[1][0]) $db_prefix = 'evt_';
                break;
            case 'gercpm':
                $media = '이벤트 랜딩_cpm';
                if ($matches[1][0]) $db_prefix = 'evt_';
                break;
            case 'ghsp':
                $media = '핫이벤트 스핀';
                if ($matches[1][0]) $db_prefix = 'event_';
                break;
            case 'ghspcpm':
                $media = '핫이벤트 스핀_cpm';
                if ($matches[1][0]) $db_prefix = 'event_';
                break;
            case 'wghr':
                $media = '오토랜딩';
                if ($matches[1][0]) $db_prefix = 'wr_';
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
        $result = array('name' => '', 'media' => '', 'db_prefix' => '', 'event_id' => '', 'app_id' => '', 'site' => '', 'db_price' => 0, 'period_ad' => '');
        if ($media) {
            $result['name']         = $title;
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
    { //유효DB 개수 업데이트
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $step = 1;
        $ads = $this->db->getAdLeads($date);
        $total = $ads->getNumRows();
        if (!$total) {
            return null;
        }
        $i = 0;
        CLI::write("[".date("Y-m-d H:i:s")."]"."전체 광고계정 수신을 시작합니다.", "light_red");
        foreach ($ads->getResultArray() as $row) {
            CLI::showProgress($step, $total);
            $title = trim($row['code']) ? $row['code'] : $row['campaign_name'] . '||' . $row['finalUrl'];
            // echo date('[H:i:s]') . "광고({$row['ad_id']}) 유효DB개수 업데이트 - {$title}";
            $landing = $this->landingGroup($title);
            // echo '<pre>' . print_r($landing, 1) . '</pre>';
            if ($landing['media']) {
                $result[$i]['name'] = $landing['name'];
                $result[$i]['ad_id'] = $row['ad_id'];
                $result[$i]['cost'] = $row['cost'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['app_id'] = $landing['app_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['db_price'] = $landing['db_price'];
                $result[$i]['period_ad'] = $landing['period_ad'];
                $result[$i]['media'] = $landing['media'];
                $result[$i]['db_prefix'] = $landing['db_prefix'];
                if (!preg_match('/cpm/', $landing['media'])) {
                    if (!$landing['app_id']) $error[] = $landing['name'] . '(' . $row['ad_id'] . '): APP_ID 미입력' . PHP_EOL;
                    if (!$landing['db_price']) $error[] = $landing['name'] . '(' . $row['ad_id'] . '): DB단가 미입력' . PHP_EOL;
                }
                $i++;
            } else {
                if (preg_match('/&[a-z]+/', $row['code'])) $error[] = $landing['name'] . '(' . $row['ad_id'] . '): 인식 오류' . PHP_EOL;
            }
            //echo PHP_EOL;
        }
        if (is_array($error) && count($error) > 0)
            $this->exception_handler($error);
        if (is_array($result)) {
            foreach ($result as $i => $data) {
                $result[$i]['count'] = 0;
                $result[$i]['sales'] = 0;
                $result[$i]['margin'] = 0;
                $sales = 0;
                $echo = '[' . $date . '] 광고(' . $data['ad_id'] . ') - 제목: ' . $data['name'] . '/랜딩번호:' . $data['app_id'] . '/사이트값:' . $data['site'];
                if ($data['app_id']) {
                    $dbcount = $this->db->getDbCount($data['ad_id'], $date);
                    $rows = $this->db->getAppSubscribeCount($data, $date);
                    $result[$i]['count'] = $rows;
                    $db_price = $data['db_price'];
                    if ($dbcount['db_price'] && $date != date('Y-m-d'))
                        $db_price = $result[$i]['db_price'] = $dbcount['db_price'];
                    $echo .= '/DB수:' . $rows;
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
                    $echo .= '/DB단가:' . $db_price;
                    if ($db_price) {
                        if (!$initZero)
                            $sales = $db_price * $rows;
                        $insight_data = new stdClass();
                        $insight_data->ad_id = $data['ad_id'];
                        $insight_data->date = $date;
                        $insight_data->data['sales'] = $sales;
                        $result[$i]['sales'] = $sales;
                        $echo .= '/매출:' . $sales;
                        $this->db->updateReport($insight_data);
                    }
                    if (!$initZero)
                        $result[$i]['margin'] = $sales - $data['cost'];
                }
                if ($data['period_ad']) {
                    $result[$i]['margin'] = $data['cost'] * ('0.' . $data['period_ad']);
                }
                $echo .= '/수익:' . $result[$i]['margin'];
                $echo .= (isset($_SERVER['SHELL'])) ? PHP_EOL : "<br />";
                // echo $echo;
            }
            // echo '<pre>' . print_r($result, 1) . '</pre>';
            $this->db->insertDbCount($result, $date);
            if (!$rows) {
                unset($result[$i]);
            }
        }
        return $result;
    }

    private function getExpandedTextAds($googleAdsServiceClient, $customerId = null, $adGroupId = null)
    {

        // Creates a query that retrieves expanded text ads.
        $query =
            'SELECT ad_group.id, '
            . 'ad_group_ad.ad.id, '
            . 'ad_group_ad.ad.expanded_text_ad.headline_part1, '
            . 'ad_group_ad.ad.expanded_text_ad.headline_part2, '
            . 'ad_group_ad.status '
            . 'FROM ad_group_ad '
            . 'WHERE ad_group_ad.ad.type = EXPANDED_TEXT_AD';
        if ($adGroupId !== null) {
            $query .= " AND ad_group.id = $adGroupId";
        }

        // Issues a search request by specifying page size.
        $response =
            $googleAdsServiceClient->search($customerId, $query, ['pageSize' => 1000]);

        // Iterates over all rows in all pages and prints the requested field values for
        // the expanded text ad in each row.
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
            $ad = $googleAdsRow->getAdGroupAd()->getAd();
            printf(
                "Expanded text ad with ID %d, status '%s', and headline '%s - %s' was found in ad "
                    . "group with ID %d.%s",
                $ad->getId(),
                AdGroupAdStatus::name($googleAdsRow->getAdGroupAd()->getStatus()),
                $ad->getExpandedTextAd()->getHeadlinePart1(),
                $ad->getExpandedTextAd()->getHeadlinePart2(),
                $googleAdsRow->getAdGroup()->getId(),
                PHP_EOL
            );
        }
    }

    private function getResponsiveSearchAds($googleAdsServiceClient, $customerId = null, $adGroupId = null)
    {
        // Creates a query that retrieves responsive search ads.
        $query =
            'SELECT ad_group.id, '
            . 'ad_group_ad.ad.id, '
            . 'ad_group_ad.ad.type, '
            . 'ad_group_ad.ad.responsive_search_ad.headlines, '
            . 'ad_group_ad.ad.responsive_search_ad.descriptions, '
            . 'ad_group_ad.ad.responsive_display_ad.headlines, '
            . 'ad_group_ad.ad.expanded_text_ad.headline_part1, '
            . 'ad_group_ad.ad.expanded_text_ad.headline_part2, '
            . 'ad_group_ad.status, '
            . 'metrics.clicks, metrics.impressions, metrics.cost_micros '
            . 'FROM ad_group_ad '
            . 'WHERE '
            //. 'WHERE ad_group_ad.ad.type = RESPONSIVE_SEARCH_AD '
            . ' segments.date = "2020-06-05" '
            . ' AND ad_group_ad.status != "REMOVED"';
        if (!is_null($adGroupId)) {
            $query .= " AND ad_group.id = $adGroupId";
        }

        // Issues a search request by specifying page size.
        $response =
            $googleAdsServiceClient->search($customerId, $query, ['pageSize' => 1000]);

        // Iterates over all rows in all pages and prints the requested field values for
        // the responsive search ad in each row.
        $isEmptyResult = true;
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
            $isEmptyResult = false;
            $ad = $googleAdsRow->getAdGroupAd()->getAd();
            $metric = $googleAdsRow->getMetrics();
            $responsiveSearchAdInfo = $ad->getResponsiveSearchAd();
            echo
            $ad->getResourceName()
                . '/' . AdGroupAdStatus::name($googleAdsRow->getAdGroupAd()->getStatus())
                . '/' . AdType::name($ad->getType())
                . '/' . $metric->getClicks()
                . '/' . $metric->getImpressions()
                . '/' . round($metric->getCostMicros() / 1000000)
                . '<br/>';
            /*
            $responsiveSearchAdInfo = $ad->getResponsiveSearchAd();
            printf(
                'Headlines:%1$s%2$sDescriptions:%1$s%3$s%1$s',
                PHP_EOL,
                self::convertAdTextAssetsToString($responsiveSearchAdInfo->getHeadlines()),
                self::convertAdTextAssetsToString($responsiveSearchAdInfo->getDescriptions())
            );
			*/
        }
        if ($isEmptyResult) {
            print 'No responsive search ads were found.' . PHP_EOL;
        }
    }

    /**
     * Converts the list of AdTextAsset objects into a string representation.
     *
     * @param RepeatedField $assets the list of AdTextAsset objects
     * @return string the string representation of the provided list of AdTextAsset objects
     */
    private static function convertAdTextAssetsToString(RepeatedField $assets): string
    {
        $result = '';
        foreach ($assets as $asset) {
            /** @var AdTextAsset $asset */
            $result .= sprintf(
                "\t%s pinned to %s.%s",
                $asset->getText(),
                ServedAssetFieldType::name($asset->getPinnedField()),
                PHP_EOL
            );
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
}
