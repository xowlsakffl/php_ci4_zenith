<?php

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//ini_set('max_execution_time', 1800);
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);

require_once __DIR__ . '/adwordsdb.php';
require_once __DIR__ . '/vendor/autoload.php';

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\Campaign;
use Google\AdsApi\AdWords\v201809\cm\CampaignOperation;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\AdsApi\AdWords\v201809\cm\CampaignStatus;
use Google\AdsApi\AdWords\v201809\cm\ServingStatus;
use Google\AdsApi\AdWords\v201809\cm\BudgetService;
use Google\AdsApi\AdWords\v201809\cm\BudgetOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroup;
use Google\AdsApi\AdWords\v201809\cm\AdGroupService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupStatus;
use Google\AdsApi\AdWords\v201809\cm\AdGroupOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAd;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdStatus;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdOperation;
use Google\AdsApi\AdWords\v201809\cm\BiddingStrategyConfiguration;
use Google\AdsApi\AdWords\v201809\cm\Ad;
use Google\AdsApi\AdWords\v201809\cm\AdService;
use Google\AdsApi\AdWords\v201809\cm\AdStatus;
use Google\AdsApi\AdWords\v201809\cm\AdOperation;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\DateRange;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;
use Google\AdsApi\AdWords\v201809\cm\FrequencyCap;
use Google\AdsApi\AdWords\v201809\cm\Budget;
use Google\AdsApi\AdWords\v201809\cm\BudgetBudgetStatus;
use Google\AdsApi\AdWords\v201809\cm\Money;
use Google\AdsApi\AdWords\v201809\cm\CpcBid;
use Google\AdsApi\AdWords\v201809\cm\CpaBid;
use Google\AdsApi\AdWords\v201809\cm\TargetCpaBiddingScheme;
//use Google\AdsApi\AdWords\v201809\cm\BudgetBudgetDeliveryMethod;
use Google\AdsApi\AdWords\v201809\cm\Level;
use Google\AdsApi\AdWords\v201809\cm\TimeUnit;
use Google\AdsApi\AdWords\v201809\cm\ReportDefinitionReportType;
use Google\AdsApi\AdWords\v201809\cm\ReportDefinitionService;
use Google\AdsApi\AdWords\Query\v201809\ReportQueryBuilder;
use Google\AdsApi\AdWords\Reporting\v201809\DownloadFormat;
use Google\AdsApi\AdWords\Reporting\v201809\ReportDefinition;
use Google\AdsApi\AdWords\Reporting\v201809\ReportDefinitionDateRangeType;
use Google\AdsApi\AdWords\Reporting\v201809\ReportDownloader;
use Google\AdsApi\AdWords\ReportSettingsBuilder;

//use Google\AdsApi\AdWords\v201809\mcm\ManagedCustomer;
use Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerService;
use Google\AdsApi\Common\OAuth2TokenBuilder;


class AdWords {
    const PAGE_LIMIT = 500;
    
    private $adWordsServices;
    private $session;
    private $oAuth2Credential;
    private $clientCustomerIdLists = ['5980790227','4324269025','2409346509','4946840644','4943963823','7933651274','5045171745','4486211678','8135785284', '2667057443'];//'4013365335';
    private $clientCustomerId;

    public function __construct($clientCustomerId="") {
        $this->db = new AWDB();
        $this->oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(__DIR__."/adsapi_php.ini")->build();   
        if(!$clientCustomerId)
            $this->clientCustomerId = $this->clientCustomerIdLists[0];
        $this->setClientCustomerId($this->clientCustomerId); // RootAccount
    }
    
    public function setClientCustomerId($clientCustomerId) {
        $this->clientCustomerId = $clientCustomerId;
        $this->adWordsServices = new AdWordsServices();
        $this->session = (new AdWordsSessionBuilder())
                ->fromFile(__DIR__."/adsapi_php.ini")
                ->withOAuth2Credential($this->oAuth2Credential)->withClientCustomerId($this->clientCustomerId)
                ->build();
    }
    
    private static function getData($data, $fields) {
        // echo '<pre>'.print_r($data,1).'</pre>';
        foreach($fields as $field) {
            $result = NULL;
            switch($field) {
                case 'BudgetId' :
                    $result = $data->getBudget()->getBudgetId();
                    break;
                case 'BudgetName' :
                    $result = $data->getBudget()->getName();
                    break;
                case 'DeliveryMethod' :
                    $result = $data->getBudget()->getDeliveryMethod();
                    break;
                case 'BudgetReferenceCount' :
                    $result = $data->getBudget()->getReferenceCount();
                    break;
                case 'BudgetStatus' :
                    $result = $data->getBudget()->getStatus();
                    break;
                case 'Amount' :
                    $result = $data->getBudget()->getAmount();
                    if ($result)
                        $result = ($result->getMicroAmount()) / 1000000;
                    else
                        $result = 0;
                    break;
                case 'FrequencyCap' :
                    $frequencyCap = $data->getFrequencyCap();
                    if ($frequencyCap) {
                        $result['impressions'] = $frequencyCap->getImpressions();
                        $result['timeUnit'] = $frequencyCap->getTimeUnit();
                        $result['level'] = $frequencyCap->getLevel();
                    }
                    break;
                case 'BiddingStrategyConfiguration' :
                    $BiddingStrategyConfiguration = $data->getBiddingStrategyConfiguration();
                    if($BiddingStrategyConfiguration) {
                        $result['BiddingStrategyId'] = $BiddingStrategyConfiguration->getBiddingStrategyId();
                        $result['BiddingStrategyName'] = $BiddingStrategyConfiguration->getBiddingStrategyName();
                        $result['BiddingStrategyType'] = $BiddingStrategyConfiguration->getBiddingStrategyType();
                        $result['BiddingStrategySource'] = $BiddingStrategyConfiguration->getBiddingStrategySource();
                        $result['BiddingScheme'] = $BiddingStrategyConfiguration->getBiddingScheme();
                        if(!is_null($BiddingStrategyConfiguration->getBiddingScheme()) && $BiddingStrategyConfiguration->getBiddingStrategyType() == 'TARGET_CPA') {
                            $result['cpaBidAmount'] = $BiddingStrategyConfiguration->getBiddingScheme()->getTargetCpa()->getMicroAmount();
                        }
                        $bids = $BiddingStrategyConfiguration->getBids();
                        // $result['bids'] = $bids;
                        // echo '<pre>'.print_r($BiddingStrategyConfiguration,1).'</pre>';
                        if($bids){
                            foreach($bids as $bid) {
                                $BidsType = $bid->getBidsType();
                                $result[$BidsType.'Amount'] = $bid->getBid()->getMicroAmount() / 1000000;
                                if($BidsType == 'CpcBid') {
                                    $result['CpcBidSource'] = $bid->getCpcBidSource();    
                                } elseif($BidsType == 'CpmBid') {
                                    $result['CpmBidSource'] = $bid->getCpmBidSource();
                                } elseif($BidsType == 'CpaBid') {
                                    $result['CpaBidSource'] = $bid->getBidSource();
                                }
                            }
                        }
                    }
                    break;
                case 'Ad' :
                    $result['AdGroupId'] = $data->getAdGroupId();
                    $result['Status'] = $data->getStatus();

                    $data = $data->getAd();
                    $result['Id'] = $data->getId();
                    $result['AdType'] = $data->getType();
                    if(method_exists($data,"getDisplayUrl"))
                        $result['displayUrl'] = $data->getDisplayUrl();
                    if(method_exists($data,'getName'))
                        $result['Name'] = $data->getName();
                    if(method_exists($data,"getFinalUrls"))
                        $result['finalUrl'] = $data->getFinalUrls()[0];
                    switch ($result['AdType']) {
                        case 'DEPRECATED_AD' :
                            break;
                        case 'IMAGE_AD' :
                            $imageAd = $data->getImage();
                            $result['Name'] = $data->getName();
                            $result['MediaType'] = $imageAd->getMediaType();
                            $urls = $imageAd->getUrls();
                            foreach($urls as $row) {
                                if($row->getKey() == 'FULL') {
                                    $result['Image']['Urls'] = $row->getValue();
                                }
                            }
                            break;
                        case 'PRODUCT_AD' :
                            break;
                        case 'TEMPLATE_AD' :
                            break;
                        case 'TEXT_AD' :
                            $result['Name'] = $data->getHeadline();
                            break;
                        case 'THIRD_PARTY_REDIRECT_AD' :
                            break;
                        case 'DYNAMIC_SEARCH_AD' :
                            break;
                        case 'CALL_ONLY_AD' : 
                            $result['Name'] = $data->getCallOnlyAdDescription1();
                            break;
                        case 'EXPANDED_TEXT_AD' :
                            $result['Name'] = $data->getHeadlinePart1();
                            break;
                        case 'RESPONSIVE_DISPLAY_AD' :
                            // echo '<pre>'.print_r($data,1).'</pre>';
                            $result['Name'] = $data->getShortHeadline();
                            $marketingImage = $data->getMarketingImage();
                            $urls = $marketingImage->getUrls();
                            foreach($urls as $row) {
                                if($row->getKey() == 'FULL') {
                                    $result['Image']['Urls'] = $row->getValue();
                                }
                            }
                            break;
                        case 'SHOWCASE_AD' :
                            break;
                        case 'GOAL_OPTIMIZED_SHOPPING_AD' :
                            break;
                        case 'EXPANDED_DYNAMIC_SEARCH_AD' :
                            break;
                        case 'GMAIL_AD' :
                            break;
                        case 'RESPONSIVE_SEARCH_AD' :
                            break;
                        case 'MULTI_ASSET_RESPONSIVE_DISPLAY_AD' :
                            $result['Name'] = $data->getHeadlines()[0]->getAsset()->getAssetText();
                            $marketingImage = $data->getMarketingImages();
                            $result['Image']['Urls'] = $marketingImage[0]->getAsset()->getfullSizeInfo()->getImageUrl();
                            break;
                        case 'UNIVERSAL_APP_AD' :
                            break;
                        case 'UNKNOWN' :
                            break;     
                        default :
                            $result['Name'] = $data->getName();
                            break;                   
                    }
                    break;
                default :
                    if(method_exists($data,"get".$field))
                        $result = $data->{"get".$field}();
                    break;
            }

            switch(gettype($result)) {
                case 'integer' :
                case 'boolean' :
                    $result = sprintf('%d', $result);
                    break;
                /*
                case 'array' :
                    $result = json_encode($result);
                    break;
                */
                case 'NULL' :
                    $result = NULL;
                    break;
                default :
                    $result = $result;
                    break;
            }
            
            $return[$field] = $result;
        }
        return $return;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function getAdAccounts($ExcludeHiddenAccounts='false') { //false 전체계정, true 활성화계정
        $managedCustomerService = $this->adWordsServices->get($this->session, ManagedCustomerService::class);
        
        $selector = new Selector();
        $fields = ['CustomerId', 'Name', 'CanManageClients', 'CurrencyCode', 'DateTimeZone', 'TestAccount'];
        $selector->setFields($fields);
        $selector->setPredicates(array(
           'field' => 'ExcludeHiddenAccounts',
           'operator' => 'EQUALS' ,
           'values' => $ExcludeHiddenAccounts
        ));
        $selector->setOrdering([new OrderBy('CustomerId', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        
        $customerIdsToAccounts = [];
        $totalNumEntries = 0;
        do {
            $page = $managedCustomerService->get($selector);

            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                
                foreach ($page->getEntries() as $account) {
                    $account->setExcludeHiddenAccounts($ExcludeHiddenAccounts);
                    // $customerIdsToAccounts[strval($account->getCustomerId())] = $account;
                    $result['data'][] = $this->getData($account, ['CustomerId', 'Name', 'CanManageClients', 'CurrencyCode', 'DateTimeZone', 'TestAccount', 'ExcludeHiddenAccounts']);
                }
            }

            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + self::PAGE_LIMIT);
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
        return $result;
    }

    // 캠페인 목록
    public function getCampaigns($customerId=null, $campaignId=null, $status=[CampaignStatus::ENABLED]) {

        if ($customerId != null)
            $this->setClientCustomerId($customerId);
            //$this->setClientCustomerId($account->getCustomerId());

        $campaignService = $this->adWordsServices->get($this->session, CampaignService::class);

        // CampaignService 클래스와 Campaign 클래스의 필드가 다르므로 구분 필요
        // https://developers.google.com/adwords/api/docs/appendix/selectorfields#v201809-CampaignService
        $selector = new Selector();
        $CSFields = array('Id', 'Name', 'Status',  'ServingStatus', 'StartDate', 'EndDate',
                          'FrequencyCapMaxImpressions', 'Level', 'TimeUnit', 
                          'BudgetId', 'BudgetName', 'BudgetReferenceCount', 'BudgetStatus',
                          'Amount', 'DeliveryMethod', 'AdServingOptimizationStatus', 'AdvertisingChannelType', 'AdvertisingChannelSubType', 
                          'CampaignTrialType', 'BaseCampaignId', 'TrackingUrlTemplate', 'FinalUrlSuffix', 'TargetCpa');
        $selector->setFields($CSFields);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        
        if($campaignId != null) {
            $setPredicates[] = new Predicate('Id', PredicateOperator::IN, [$campaignId]);
        }
        if($status != null) {
            $setPredicates[] = new Predicate('Status', PredicateOperator::EQUALS, $status);
        }
        if($setPredicates) $selector->setPredicates($setPredicates);
        // $dateRange = new DateRange();
        // $dateRange->min = date('Ymd', time());
        // $dateRange->max = date('Ymd', time());
        //$selector->setDateRange(new DateRange(date('Ymd', strtotime(time(), '-1 year')),date('Ymd', time())));

        $Cfields = array('Id', 'CampaignGroupId', 'Name', 'Status', 'ServingStatus', 'StartDate', 'EndDate', 
                        'BudgetId', 'BudgetName', 'BudgetReferenceCount', 'BudgetStatus', 
                        'Amount', 'DeliveryMethod', 'AdServingOptimizationStatus', 'AdvertisingChannelType', 'AdvertisingChannelSubType', 
                        'CampaignTrialType', 'BaseCampaignId', 'TrackingUrlTemplate', 'FinalUrlSuffix', 
                        'UrlCustomParameters', 'SelectiveOptimization', 'FrequencyCap', 'BiddingStrategyConfiguration');
        
        $result = [];
        // $result['CustomerId'] = $this->clientCustomerId;
        // $result['CustomerName'] = $account->getName();
        $result['data'] = [];
        do {
            $page = $campaignService->get($selector);
            
            if ($page->getEntries() !== null) {
                $result['total'] = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $campaign) {
                    $data = $this->getData($campaign, $Cfields);
                    $data['CustomerId'] = $this->clientCustomerId;
                    $result['data'][] = $data;
                }
            }

            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + self::PAGE_LIMIT);
        } while ($selector->getPaging()->getStartIndex() < $result['total']);
        // print_r($result); exit;
        return $result;
    }

    public function getAdGroups($customerId=null, $campaignId=null, $adGroupId=null) {
        if ($customerId != null)
            $this->setClientCustomerId($customerId);

        $adGroupService = $this->adWordsServices->get($this->session, AdGroupService::class);

        // https://developers.google.com/adwords/api/docs/appendix/selectorfields#v201809-AdGroupServiceÍ
        $selector = new Selector();
        $selector->setFields(['CampaignId', 'Id', 'Name', 'Status', "AdGroupType", 'CpcBid', 'CpmBid', 'TargetCpaBid', 'TargetCpaBidSource', 'TargetCpa', 'TargetRoasOverride', 'BiddingStrategySource', ]);
        $selector->setOrdering([new OrderBy('Id', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        if ($adGroupId != null) {
            $setPredicates[] = new Predicate('Id', PredicateOperator::IN, [$adGroupId]);
        }
        if ($campaignId != null) {
            $setPredicates[] = new Predicate('CampaignId', PredicateOperator::IN, [$campaignId]);
        }

        if($setPredicates) $selector->setPredicates($setPredicates);


        // $result['campaignId'] = $campaignId;
        $result['data'] = [];
        do {
            $page = $adGroupService->get($selector);

            if ($page->getEntries() !== null) {
                $result['total'] = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $adGroup) {
                    $data = $this->getData($adGroup, array("CampaignId", "Id", "Name", "Status", "AdGroupType", "BiddingStrategyConfiguration"));
                    $data['CustomerId'] = $this->clientCustomerId;
                    $result['data'][] = $data;
                }
            }
            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + self::PAGE_LIMIT);
        } while ($selector->getPaging()->getStartIndex() < $result['total']);

        return $result;
    }

    public function getAdGroupAds($customerId=null, $adGroupId=null, $adId=null) {
        if ($customerId != null)
            $this->setClientCustomerId($customerId);
        
        $adgroupadService = $this->adWordsServices->get($this->session, AdGroupAdService::class);

        // AdGroupService selector fields 참조
        // https://developers.google.com/adwords/api/docs/appendix/selectorfields#v201809-AdGroupAdService
        $selector = new Selector();
        $selector->setFields(['Id', 'Name', 'Status', 'AdStrengthInfo', 'AdType', 'DisplayUrl', 'Url', 'UrlData', 'ImageCreativeName', 'CreativeFinalUrls', 'MultiAssetResponsiveDisplayAdMarketingImages', 'MultiAssetResponsiveDisplayAdHeadlines', 'Headline', 'HeadlinePart1', 'ShortHeadline', 'CallOnlyAdDescription1']);
        $selector->setOrdering([new OrderBy('Id', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        if ($adId != null) {
            $setPredicates[] = new Predicate('Id', PredicateOperator::IN, [$adId]);
        }
        if ($adGroupId != null) {
            $setPredicates[] = new Predicate('AdGroupId', PredicateOperator::IN, [$adGroupId]);
        }
        if($setPredicates) $selector->setPredicates($setPredicates);

        $result['data'] = [];
        do {
            $page = $adgroupadService->get($selector);

            if ($page->getEntries() !== null) {
                $result['total'] = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $ad) {
                    $data = $this->getData($ad, array("Ad"))['Ad'];
                    $data['CustomerId'] = $this->clientCustomerId;
                    $result['data'][] = $data;
                }
            }

            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + self::PAGE_LIMIT);
        } while ($selector->getPaging()->getStartIndex() < $result['total']);

        return $result;
    }

    public function translate_adType($type) {
        switch($type) {
            case 'DEPRECATED_AD' :
                $result = '삭제된 광고'; break;
            case 'IMAGE_AD' :
                $result = '이미지 광고'; break;
            case 'PRODUCT_AD' :
                $result = '제품 광고'; break;
            case 'TEMPLATE_AD' :
                $result = '템플릿 광고'; break;
            case 'TEXT_AD' :
                $result = '텍스트 광고'; break;
            case 'THIRD_PARTY_REDIRECT_AD' :
                $result = $type; break;
            case 'DYNAMIC_SEARCH_AD' :
                $result = '동적 검색 광고'; break;
            case 'CALL_ONLY_AD' :
                $result = '통화 전용 광고'; break;
            case 'EXPANDED_TEXT_AD' :
                $result = '확장 텍스트 광고'; break;
            case 'RESPONSIVE_DISPLAY_AD' :
                $result = '반응형 디스플레이 광고'; break;
            case 'SHOWCASE_AD' :
                $result = '쇼핑 광고'; break;
            case 'GOAL_OPTIMIZED_SHOPPING_AD' :
                $result = '스마트 쇼핑 광고'; break;
            case 'EXPANDED_DYNAMIC_SEARCH_AD' :
                $result = '확장 동적 검색 광고'; break;
            case 'GMAIL_AD' :
                $result = 'Gmail 광고'; break;
            case 'RESPONSIVE_SEARCH_AD' :
                $result = '반응형 검색 광고'; break;
            case 'MULTI_ASSET_RESPONSIVE_DISPLAY_AD' :
                $result = '다중 자산 반응형 디스플레이 광고'; break;
            case 'UNIVERSAL_APP_AD' :
                $result = '범용 앱 광고'; break;
            default :
                $result = $type; break;
        }
        return $result;
    }

    public function getAdsWithCampaign($args) {
        $campaign_ids = $args['ids'][0];
        $ads['result'] = false;
        if(is_array($campaign_ids) && count($campaign_ids) > 0) {
            if($result = $this->db->getAdsWithCampaign($campaign_ids)) {
                $ads['result'] = true;
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    if($c_id != $row['campaign_id']) $data = array();
                    //if($row['ad_status'] != 'ENABLED') continue;
                    $data[] = [
                        'id' => $row['ad_id'],
                        'name' => $row['ad_name'],
                        'adgroup_name' => $row['adgroup_name'],
                        'status' => $row['ad_status'],
                        'type' => $this->translate_adType($row['ad_type']),
                        'code' => $row['ad_code'],
                        'image_url' => $row['ad_image_url'],
                        'final_url' => $row['ad_final_url']
                    ];
                    $ads['data'][$row['campaign_id']]['campaign_id'] = $row['campaign_id'];
                    $ads['data'][$row['campaign_id']]['campaign_name'] = $row['campaign_name'];
                    $ads['data'][$row['campaign_id']]['ads'] = $data;
                    sort($ads['data'][$row['campaign_id']]['ads']);
                    $c_id = $row['campaign_id'];
                    $i++;
                }
                if(is_array($ads['data']))
                    sort($ads['data']);
            } else {
                $ads['msg'] = '데이터가 없습니다.';
            }
        } else {
            $ads['msg'] = '선택된 캠페인이 없습니다.';
        }
        echo json_encode($ads);
        exit;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function updateCampaign($data)
    {
        if(!$data['id']) return false;
        $customerId = $this->db->getCustomerIdByCampaignId($data['id']);
        $curCampaign = $this->getCampaigns($customerId, $data['id'])['data'][0];
        ////////////////////////////////////////////////////////////////////////
        // 1. Campagin
        /*
        $campaignName = "임플란트_180524(신규배너)";
        $campaignStatus = CampaignStatus::PAUSED; // ENABLED, PAUSED, REMOVED
        $campaignStartDate = date('Ymd', strtotime('+5 day'));
        $campaignEndDate = date('Ymd', strtotime('+1 year'));
        */
        $campaign = new Campaign();
        $campaign->setId($data['id']);
        if(trim($data['name']))
            $campaign->setName($data['name']);
        if(in_array($data['status'], [CampaignStatus::PAUSED, CampaignStatus::ENABLED]))
            $campaign->setStatus($data['status']); 
        // $campaign->setStartDate($campaignStartDate);
        // $campaign->setEndDate($campaignEndDate);
        if($data['status'] && $data['mb_id'])$this->db->insertOnoffHistoryCampaign($data['id'], $data['mb_id'], 'MANUAL', $data['status']);
      
        ////////////////////////////////////////////////////////////////////////
        // 2. Budget
        if($data['budget']) {
            $data['budget'] *= 1000000; 

            $budget = new Budget();
            $budget->setBudgetId($curCampaign['BudgetId']);
            $money = new Money();
            $money->setMicroAmount($data['budget']);
            $budget->setAmount($money);
            $budget->setIsExplicitlyShared(false);

            $operations = [];
            $operation = new BudgetOperation();
            $operation->setOperand($budget);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $budgetService = $this->adWordsServices->get($this->session, BudgetService::class);
            $result = $budgetService->mutate($operations);
        }

        ////////////////////////////////////////////////////////////////////////
        // 3. Frequency cap
        /*
        $frequencyCapImpressisons = 15;
        $frequencyCapTimeUnit = TimeUnit::DAY;
        $frequencyCapLevel = Level::ADGROUP;

        $frequencyCap = new FrequencyCap();
        $frequencyCap->setImpressions($frequencyCapImpressisons);
        $frequencyCap->setTimeUnit($frequencyCapTimeUnit);
        $frequencyCap->setLevel($frequencyCapLevel);
        $campaign->setFrequencyCap($frequencyCap);
        */
        ////////////////////////////////////////////////////////////////////////
        // 4. Bidding
        if (!is_null($data['cpaBidAmount'])) {
            $cpaBidMicroAmount = $data['cpaBidAmount'] * 1000000;
            $TargetCpaBiddingScheme = new TargetCpaBiddingScheme();
            $money = new Money();
            $money->setMicroAmount($cpaBidMicroAmount);
            $target_cpa = $TargetCpaBiddingScheme->setTargetCpa($money);
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->setBiddingScheme($target_cpa);
            
            $campaign->setBiddingStrategyConfiguration($biddingStrategyConfiguration);
        }
        ////////////////////////////////////////////////////////////////////////
        $operations = [];
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;
        
        $campaignService = $this->adWordsServices->get($this->session, CampaignService::class);
        $result = $campaignService->mutate($operations);
        $result = $result->getValue()[0];
        // echo '<pre>'.print_r($result,1).'</pre>';
        if($data['id'] == $result->getId()) {
            if(!$this->db->updateCampaign($data)) {
                echo json_encode(['result'=>false,'message'=>'DB저장 실패']);
            }
        }
        return $result;
    }

    public function updateAdGroup($data)
    {
        if(!$data['id']) return false;
        $customerId = $this->db->getCustomerIdByAdGroupId($data['id']);
        $this->setClientCustomerId($customerId);

        $adGroup = new AdGroup();
        $data['id'] = (float) $data['id'];
        $adGroup->setId($data['id']);

        if(trim($data['name']))
            $adGroup->setName($data['name']);
        if(in_array($data['status'], [AdGroupStatus::PAUSED, AdGroupStatus::ENABLED]))
            $adGroup->setStatus($data['status']); 

        if (!is_null($data['cpcBidAmount'])) {
            $cpcBidMicroAmount = $data['cpcBidAmount'] * 1000000;
            $bid = new CpcBid();
            $money = new Money();
            $money->setMicroAmount($cpcBidMicroAmount);
            $bid->setBid($money);
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->setBids([$bid]);
            $adGroup->setBiddingStrategyConfiguration($biddingStrategyConfiguration);
        }

        if (!is_null($data['cpaBidAmount'])) {
            $cpaBidMicroAmount = $data['cpaBidAmount'] * 1000000;
            $TargetCpaBiddingScheme = new TargetCpaBiddingScheme();
            $bid = new CpaBid();
            $money = new Money();
            $money->setMicroAmount($cpaBidMicroAmount);
            $bid->setBid($money);
            $target_cpa = $TargetCpaBiddingScheme->getTargetCpa();
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->setBiddingScheme($target_cpa);
            // $biddingStrategyConfiguration->setBiddingStrategySource('ADGROUP');
            $biddingStrategyConfiguration->setBids([$bid]);
            
            $adGroup->setBiddingStrategyConfiguration($biddingStrategyConfiguration);
        }

        $operations = [];
        $operation = new AdGroupOperation();
        $operation->setOperand($adGroup);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        $adGroupService = $this->adWordsServices->get($this->session, AdGroupService::class);
        $result = $adGroupService->mutate($operations);
        
        
        $result = $result->getValue()[0];
        // echo '<pre>'.print_r($result,1).'</pre>';
        if($data['id'] == $result->getId()) {
            if(!$this->db->updateAdGroup($data)) {
                echo json_encode(['result'=>false,'message'=>'DB저장 실패']);
            }
        }
        return $result;
    }

    public function updateAd($data)
    {
        if(!$data['id']) return false;
        $customerId = $this->db->getCustomerIdByAdId($data['id']);
        $this->setClientCustomerId($customerId);
        //https://developers.google.com/adwords/api/docs/guides/ad-features
        $ad = new Ad();
        $ad->setId($data['id']);

        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($this->db->getAdGroupIdByAdId($data['id']));
        $adGroupAd->setAd($ad);
        if(in_array($data['status'], [AdGroupAdStatus::PAUSED, AdGroupAdStatus::ENABLED]))
            $adGroupAd->setStatus($data['status']);
        $operations = [];
        $operation = new AdGroupAdOperation();
        $operation->setOperand($adGroupAd);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        $adGroupAdService = $this->adWordsServices->get($this->session, AdGroupAdService::class);
        $result = $adGroupAdService->mutate($operations)->getValue()[0];

        $result = $result->getAd();
        if($data['id'] == $result->getId()) {
            if(!$this->db->updateAd($data)) {
                echo json_encode(['result'=>false,'message'=>'DB저장 실패']);
            }
        }
        return $result;
    }

    public function setAdCode($data) {
        if($this->db->setAdCode($data)) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
        }
        echo json_encode($result);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getReport($date=null)
    {
        // Create selector.
        $selector = new Selector();
        $selector->setFields(
            [
                // 'CampaignId',
                // 'AdGroupId',
                'Id',
                'Impressions',
                'Clicks',
                'Cost'
            ]
        );

        // Use a predicate to filter out paused criteria (this is optional).
        $selector->setPredicates([
                // new Predicate('Status', PredicateOperator::NOT_IN, ['PAUSED']),
                new Predicate('Impressions', PredicateOperator::GREATER_THAN, [0])
        ]);
        if($date == null) $date = date('Y-m-d');
        $selector->setDateRange(new DateRange(date('Ymd', strtotime($date)),date('Ymd', strtotime($date))));

        // Create report definition.
        $reportDefinition = new ReportDefinition();
        $reportDefinition->setSelector($selector);
        $reportDefinition->setReportName(uniqid());
        $reportDefinition->setDateRangeType(
            ReportDefinitionDateRangeType::CUSTOM_DATE
        );
        $reportDefinition->setReportType(
            ReportDefinitionReportType::AD_PERFORMANCE_REPORT
        );
        $reportDefinition->setDownloadFormat(DownloadFormat::CSV);

        // Download report.
        $reportDownloader = new ReportDownloader($this->session);
        // Optional: If you need to adjust report settings just for this one
        // request, you can create and supply the settings override here. Otherwise,
        // default values from the configuration file (adsapi_php.ini) are used.
        $reportSettingsOverride = (new ReportSettingsBuilder())->includeZeroImpressions(false)->build();
        $reportDownloadResult = $reportDownloader->downloadReport(
            $reportDefinition,
            $reportSettingsOverride
        );
        $data = $reportDownloadResult->getAsString();
        if(preg_match('/Total.+0,0,0/', $data)) return;
        $csvData = preg_split('/\r\n|\r|\n/', $data);
        $dataLength = count($csvData);
        $i = 0;
        $result = array();
        $rData = array();
        foreach($csvData as $row) {
            if(!preg_match('/^[0-9]+/', $row) || $row == '') continue;
            $rData[$i]['date'] = $date;
            list($rData[$i]['ad_id'], $rData[$i]['impressions'], $rData[$i]['clicks'], $rData[$i]['cost']) = explode(',', $row);
            $i++;
        }
        $result = $rData;
        
        /*
        if(file_exists($filePath)) {
            if($handle = fopen($filePath, 'r')) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    echo "<p> $num fields in line $row: <br /></p>\n";
                    $row++;
                    for ($c=0; $c < $num; $c++) {
                        echo $data[$c] . "<br />\n";
                    }
                }
                fclose($handle);
            }
        }
        */
        return $result;
    }

    public function getReportWithAwql($date=null)
    {
        $CUSTOMER_SERVING_TYPE_REPORT_MISMATCH = [2409346509, 2667057443, 4324269025, 4486211678, 4943963823, 4946840644, 5045171745, 5980790227, 7933651274, 8135785284];
        $adAccounts = $this->db->getAdAccounts(0);
        $data = array();
        while($row=$adAccounts->fetch_assoc()) {
            if(in_array($row['customerId'], $CUSTOMER_SERVING_TYPE_REPORT_MISMATCH)) continue;
            $this->setClientCustomerId($row['customerId']);
            // Create report query to get the data for last 7 days.
            if($date == null) $date = date('Y-m-d');
            $query = (new ReportQueryBuilder())
                ->select([
                    'AccountCurrencyCode'
                    ,'AccountDescriptiveName'
                    ,'AccountTimeZone'
                    ,'AdGroupId'
                    ,'AdGroupName'
                    ,'AdGroupStatus'
                    ,'AdNetworkType1'
                    ,'AdNetworkType2'
                    // ,'AllConversionRate'
                    ,'AllConversionValue'
                    ,'AllConversions'
                    ,'AverageCpm'
                    // ,'AverageCpv'
                    ,'CampaignId'
                    ,'CampaignName'
                    ,'CampaignStatus'
                    // ,'ClickType'
                    ,'Clicks'
                    // ,'ConversionCategoryName'
                    ,'ConversionRate'
                    // ,'ConversionTrackerId'
                    // ,'ConversionTypeName'
                    ,'ConversionValue'
                    ,'Conversions'
                    ,'Cost'
                    ,'CostPerAllConversion'
                    ,'CostPerConversion'
                    ,'CreativeId'
                    ,'CreativeStatus'
                    ,'CrossDeviceConversions'
                    ,'Ctr'
                    ,'CustomerDescriptiveName'
                    ,'Date'
                    ,'DayOfWeek'
                    ,'Device'
                    ,'EngagementRate'
                    ,'Engagements'
                    // ,'ExternalConversionSource'
                    ,'ExternalCustomerId'
                    ,'Impressions'
                    ,'Month'
                    ,'MonthOfYear'
                    ,'Quarter'
                    ,'ValuePerAllConversion'
                    ,'VideoChannelId'
                    ,'VideoDuration'
                    ,'VideoId'
                    ,'VideoQuartile100Rate'
                    ,'VideoQuartile25Rate'
                    ,'VideoQuartile50Rate'
                    ,'VideoQuartile75Rate'
                    ,'VideoTitle'
                    ,'VideoViewRate'
                    ,'VideoViews'
                    ,'ViewThroughConversions'
                    ,'Week'
                    ,'Year'
                ])
                ->from(ReportDefinitionReportType::VIDEO_PERFORMANCE_REPORT)
                // ->where('Status')->in(['ENABLED', 'PAUSED'])
                // ->duringDateRange(ReportDefinitionDateRangeType::CUSTOM_DATE)
                ->during(date('Ymd', strtotime($date)),date('Ymd', strtotime($date)))
                ->build();
            // Download report as a string.
            $reportDownloader = new ReportDownloader($this->session);
            // Optional: If you need to adjust report settings just for this one
            // request, you can create and supply the settings override here.
            // Otherwise, default values from the configuration
            // file (adsapi_php.ini) are used.
            $reportSettingsOverride = (new ReportSettingsBuilder())
                // ->includeZeroImpressions(false)
                ->build();
            $reportDownloadResult = $reportDownloader->downloadReportWithAwql(
                sprintf('%s', $query),
                DownloadFormat::CSV,
                $reportSettingsOverride
            );
            $data = $reportDownloadResult->getAsString();
            if(preg_match('/Total.+0,0,0/', $data)) continue;       // 데이터가 없는 행은 무시하고 진행
            $csvData = preg_split('/\r\n|\r|\n/', $data);
            
            // DATA formatting
            $videoData = array();
            $keys = array();
            for($f=1;$f<sizeof($csvData);$f++){
                $tmpvd = explode(',',$csvData[$f]);
                if($f==1){
                    $keys = str_replace(' ','',$tmpvd);
                    continue;
                } 
                $idx = 0;
                $vd = array();
                if($tmpvd[0]=="Total") break;
                while($idx<sizeof($tmpvd)){
                    $vd[$keys[$idx]] = $tmpvd[$idx];
                    $idx++;
                }
                array_push($videoData, $vd);
            }

            $this->db->insertVideoReport($videoData);
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getAdAccountList() {
		$lists = ['all'=>[],'active'=>[]];
        $this->db->hiddenAccount();
        foreach($this->clientCustomerIdLists as $clientCustomerId) {
            $this->setClientCustomerId($clientCustomerId);
            $lists['all'] = array_merge($lists['all'], $this->getAdAccounts());
            $this->db->insertAdAccounts($lists['all'], 1); //전체 리스트를 모두 숨김으로 insert
            $lists['active'] = array_merge($lists['active'], $this->getAdAccounts('true')); //활성화 리스트 호출
            $this->db->insertAdAccounts($lists['active'], 0); //활성화 리스트만 0으로 update
        }
        // echo '<pre>'.print_r($lists,1).'</pre>';
        return $lists;
    }


    public function getCampaignList() {
        $adAccounts = $this->db->getAdAccounts();
        $data = array();
        while($row=$adAccounts->fetch_assoc()) {
            $lists = $this->getCampaigns($row['customerId']);
            $data = array_merge($data, $lists['data']);
        }
        $this->db->insertCampaigns($data);
        return $data;
    }

    public function getAdGroupList() {
        $campaigns = $this->db->getCampaigns();
        $data = array();
        $i=0;
        while($row=$campaigns->fetch_assoc()) {
            $lists = $this->getAdGroups($row['id']);
            $data = array_merge($data, $lists['data']);
        }
        $this->db->insertAdGroups($data);
        return $lists;
    }

    public function updateAds($data) {
        foreach($data['ad_id'] as $ad_id) {
            $customer_id = $this->db->getCustomerIdByAdId($ad_id);
            $ad = $this->getAdGroupAds($customer_id, null, $ad_id)['data'][0];
            $ads[] = $ad;
            $result['adgroup_id'][] = $ad['AdGroupId'];
        }
        $result['adgroup_id'] = array_unique($result['adgroup_id']);
        sort($result['adgroup_id']);
        $this->db->insertAds($ads);
        return $result;
    }

    public function updateAdGroups($data) {
        foreach($data['adgroup_id'] as $adgroup_id) {
            $customer_id = $this->db->getCustomerIdByAdGroupId($adgroup_id);
            $adGroup = $this->getAdGroups($customer_id, null, $adgroup_id)['data'][0];
            $adGroups[] = $adGroup;
            $result['campaign_id'][] = $adGroup['CampaignId'];
        }
        $result['campaign_id'] = array_unique($result['campaign_id']);
        sort($result['campaign_id']);
        //echo '<pre>'.print_r($adGroups).'</pre>';
        $this->db->insertAdGroups($adGroups);
        return $result;
    }

    public function updateCampaigns($data, $report_term=0) {
        foreach($data['campaign_id'] as $campaign_id) {
            $customer_id = $this->db->getCustomerIdByCampaignId($campaign_id);
            $campaign = $this->getCampaigns($customer_id, $campaign_id)['data'][0];
            $campaigns[] = $campaign;
            $result['campaign_id'][] = $campaign['Id'];
            for($i=$report_term; $i>=0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} day"));
                $reports = $this->getReport($date);
                if($reports) $this->db->insertAdReports($reports);
                $this->getAdsUseLanding($date);
                ob_flush(); flush(); usleep(1);
            }
        }
        $result['campaign_id'] = array_unique($result['campaign_id']);
        sort($result['campaign_id']);
        $this->db->insertCampaigns($campaigns);
        return $result;
    }

    public function updateCampaignsByDB() {
        $db_result = $this->db->getCampaignsWithCustomer();
        while($row=$db_result->fetch_assoc()) {
            if($row['advertisingChannelType']=='VIDEO') continue;       // 영상형 광고 스킵
            if(@!in_array($row['id'], $data[$row['customerId']]['campaign_id']) && strtotime($row['update_time']) <= strtotime('-24 hour', time()) && $row['status'] != 'NODATA' && $row['is_update'] == 1) {
				echo date('[H:i:s]')."{$row['customerId']}-캠페인({$row['id']}) 업데이트 ";
                $data[$row['customerId']]['campaign_id'][] = $row['id'];
                $campaign = $this->getCampaigns($row['customerId'], $row['id'], null)['data'][0];
                for($i=7; $i>=0; $i--) {
                    $date = date('Y-m-d', strtotime("-{$i} day"));
                    $reports = $this->getReport($date);
                    if($reports) $this->db->insertAdReports($reports);
                }
                if($this->db->insertCampaign($campaign)) {
                    $result['campaign']['success'][] = $row['id'];
                    echo "성공".PHP_EOL;
                } else {
                    $this->db->canNotUpdateCampaign($row['id']);
                    $result['campaign']['fail'][] = $row['id'];
                    echo "실패".PHP_EOL;
                }
                ob_flush(); flush(); usleep(1);
            }
        }
    }
    public function updateAdGroupsByDB() {
        $db_result = $this->db->getAdGroupsWithCustomer();
        while($row=$db_result->fetch_assoc()) {
            if($row['adGroupType']=='VIDEO_STANDARD') continue;       // 영상형 광고 스킵
            if(@!in_array($row['id'], $data[$row['customerId']]['adgroup_id']) && strtotime($row['update_time']) <= strtotime('-24 hour', time()) && $row['status'] != 'NODATA' && $row['is_update'] == 1) {
				echo date('[H:i:s]')."{$row['customerId']}-광고그룹({$row['id']}) 업데이트 ";
                $data[$row['customerId']]['adgroup_id'][] = $row['id'];
                $adGroup = $this->getAdGroups($row['customerId'], null, $row['id'])['data'][0];
                if($this->db->insertAdGroup($adGroup)) {
                    $result['adgroup']['success'][] = $row['id'];
                    echo "성공".PHP_EOL;
                } else {
                    $this->db->canNotUpdateAdGroup($row['id']);
                    $result['adgroup']['fail'][] = $row['id'];
                    echo "실패".PHP_EOL;
                }
                ob_flush(); flush(); usleep(1);
            }
        }
    }
    public function updateAdsByDB() {
        $db_result = $this->db->getAdsWithCustomer();
        while($row=$db_result->fetch_assoc()) {
            if($row['adType']=='VIDEO_AD') continue;       // 영상형 광고 스킵
            if(@!in_array($row['id'], $data[$row['customerId']]['ad_id']) && strtotime($row['update_time']) <= strtotime('-24 hour', time()) && $row['status'] != 'NODATA' && $row['is_update'] == 1) {
				echo date('[H:i:s]')."{$row['customerId']}-광고({$row['id']}) 업데이트 ";
                $data[$row['customerId']]['ad_id'][] = $row['id'];
                $ad = $this->getAdGroupAds($row['customerId'], null, $row['id'])['data'][0];
                if($this->db->insertAd($ad)) {
                    $result['ad']['success'][] = $row['id'];
                    echo "성공".PHP_EOL;
                } else {
                    $this->db->canNotUpdateAd($row['id']);
                    $result['ad']['fail'][] = $row['id'];
                    echo "실패".PHP_EOL;
                }
                ob_flush(); flush(); usleep(1);
            }
        }
    }

    public function getPausedCampaigns($ExcludeHiddenAccounts='true') {
        foreach($this->clientCustomerIdLists as $clientCustomerId) {
            $this->setClientCustomerId($clientCustomerId);
            $adAccounts = $this->getAdAccounts($ExcludeHiddenAccounts); //true 활성화계정
            $status = [CampaignStatus::PAUSED];
            if($all) $status = null;
            foreach($adAccounts['data'] as $customerId => $account) {//2735729633
                echo date('[H:i:s]')."{$account['CustomerId']} 계정 중지된 캠페인 업데이트 -";
                if($account['ExcludeHiddenAccounts'] == 'true') {
                    echo " 공개";
                    $campaigns = $this->getCampaigns($account['CustomerId'], null, $status);
                    if($account['CanManageClients'] == false) {
                        $reports = $this->getReport();
                        if($reports) $this->db->insertAdReports($reports);
                    }
                    $this->db->insertCampaigns($campaigns['data']);
                } else {
                    echo " 숨김";
                }
                echo PHP_EOL;
                ob_flush(); flush(); usleep(1);
            }
        }
    }
    
    public function getAll($ExcludeHiddenAccounts='true', $all=false) {
        foreach($this->clientCustomerIdLists as $clientCustomerId) {
            $this->setClientCustomerId($clientCustomerId);
            $adAccounts = $this->getAdAccounts($ExcludeHiddenAccounts); //true 활성화계정
            $status = [CampaignStatus::ENABLED];
            if($all) $status = null;
            foreach($adAccounts['data'] as $customerId => $account) {//2735729633
//                if($account['CustomerId'] != "9402079227") continue;
    			echo date('[H:i:s]')."{$account['CustomerId']} 계정 광고 업데이트 -";
                if($account['ExcludeHiddenAccounts'] == 'true') {
    				echo " 공개";
                    $campaigns = $this->getCampaigns($account['CustomerId'], null, $status);
                    if($account['CanManageClients'] == false) {
                        $reports = $this->getReport();
//                        print_r($reports);
                        if($reports) $this->db->insertAdReports($reports);
                    }
                    $this->db->insertCampaigns($campaigns['data']);
                    foreach($campaigns['data'] as $campaign) {
                        $adGroups = $this->getAdGroups(null, $campaign['Id']);
                        $this->db->insertAdGroups($adGroups['data']);
                        $i = 0;
                        foreach($adGroups['data'] as $adGroup) {
                            $ads = $this->getAdGroupAds(null, $adGroup['Id']);
                            $this->db->insertAds($ads['data']);
                            // echo nl2br(print_r($ad,1));
                            /*
                            foreach($ads['data'] as $ad) {
                                echo date('[H:i:s]').' ';
                                echo $account['Name'].'/'.$campaign['Id'].':'.$campaign['Status'].':'.$campaign['Name'].'/'.$adGroup['Id'].':'.$adGroup['Status'].':'.$adGroup['Name'].'/'.$ad['Id'].':'.$ad['Status'].':'.$ad['AdType'].':'.$ad['Name'].'<br>'.PHP_EOL;
                            }
                            */
                            
                            // return; // For testing
                        }
                    }
                } else {
    				echo " 숨김";
    			}
    			echo PHP_EOL;
                ob_flush(); flush(); usleep(1);
            }
        }
    }

    public function getCpaBidAmount($data){
        if(!$data['id']) return false;
        $customerId = $this->db->getCustomerIdByCampaignId($data['id']);
        $curCampaign = $this->getCampaigns($customerId, $data['id'])['data'][0];

        return $curCampaign['BiddingStrategyConfiguration']['cpaBidAmount']/1000000;
    }

    // 정파고(캠페인) ON/OFF 업데이트
    public function getOptimization_campaign($date="")
    {
        if(!$date) $date = date('Y-m-d');
        $result = $this->db->getOptimization_campaignByReport($date, '901'); //정파고(캠페인)  가져오기
        while ($data = $result->fetch_assoc()) {
            $campaign_id = $data['id'];
            if (!$campaign_id) return false;
            $events = explode(',', $data['evt']);
            $data['impressions'] = 0;
            foreach($events as $event) {
                list($evt_no, $site_no) = explode('_', $event);
                $imp_result = $this->db->getImpressionsByEvent($evt_no, $site_no, $date, '3EFFACECD4D3A3015616264B0620BE7B');
                while($row = $imp_result->fetch_assoc()) {
                    $data['impressions'] += $row['impressions'];
                }
            }
            $data['spend'] = 0;
            if(!is_null($data['cpc']))
                $data['spend'] = $data['cpc'] * $data['impressions'];
            // echo '<pre>'.print_r($data,1).'</pre>'; //continue;
            $status = CampaignStatus::ENABLED;
            if($data['budget'] <= $data['spend']){
                $status = CampaignStatus::PAUSED;//CampaignStatus::ENABLED;
            }
            echo "{$data['id']}({$data['name']})/budget:{$data['budget']}/CPC:{$data['cpc']}/impressions:{$data['impressions']}/spend:{$data['spend']}/Events:{$data['evt']}/status:{$data['status']}=>{$status}".PHP_EOL;
            ob_flush(); flush(); usleep(1);
            if($campaign_id && $data['status'] != $status) {
                $data = ['id' => $campaign_id, 'status' => $status];
                $updateCampaign = $this->updateCampaign($data);
                $getId = $updateCampaign->getId();
                if ($getId == $campaign_id) {
                    $this->db->insertOptimizationHistory_campaign($campaign_id, $status, 0);
                    $this->db->insertOnoffHistoryCampaign($campaign_id, "SYSTEM", '901', $status);
                }
            }
        }
    }

    public function getOptimization_campaign_restart($type) { //12시에 다시 켬
        $result = $this->db->getOptimization_campaign($type); //ai(캠페인)  가져오기
        while ($row = $result->fetch_assoc()) {
            if($row['id'] && $row['status'] == CampaignStatus::PAUSED) {
                $status = CampaignStatus::ENABLED;
                echo "{$row['id']}({$row['name']})/status:{$row['status']}".PHP_EOL;
                ob_flush(); flush(); usleep(1);
                $data = ['id' => $row['id'], 'status' => $status];
                $updateCampaign = $this->updateCampaign($data);
                $getId = $updateCampaign->getId();
                if ($getId == $data['id']) {
                    $this->db->insertOptimizationHistory_campaign($data['id'], $status, 0);
                    $this->db->insertOnoffHistoryCampaign($campaign_id, "SYSTEM", $type, $status);
                }
            }
        }
    }

    public function getOptimization_leveled_campaign_restart($pre_fix) { //12시에 다시 켬
        $result = $this->db->getOptimization_leveled_campaign($pre_fix); //ai(캠페인)  가져오기
        while ($row = $result->fetch_assoc()) {
            if($row['id'] && $row['status'] == CampaignStatus::PAUSED) {
                $status = CampaignStatus::ENABLED;
                echo "{$row['id']}({$row['name']})/status:{$row['status']}".PHP_EOL;
                ob_flush(); flush(); usleep(1);
                $data = ['id' => $row['id'], 'status' => $status];
                $updateCampaign = $this->updateCampaign($data);
                $getId = $updateCampaign->getId();
                if ($getId == $data['id']) {
                    $this->db->insertOptimizationHistory_campaign($data['id'], $status, $row['type']);
                    $this->db->insertOnoffHistoryCampaign($data['id'], "SYSTEM", $row['type'], $status);
                }
            }
        }
    }

    public function song_Optimization($campaign_id, $level){
        $data = [];
        $data['id'] = $campaign_id;
        if($level=='1'){
            $data['budget'] = 300000;
        }else if($level=='2'){
            $data['budget'] = 1000000;
        }else if($level=='3'){
            $data['budget'] = 3000000;
        }
        $budget_old = $this->db->getCampaignBudget($campaign_id);
        
        $updateCampaign = $this->updateCampaign($data);
        $getId = $updateCampaign->getId();
        if ($getId == $campaign_id) {
            $this->db->insertOptimizationBudgetHistory_campaign($campaign_id, 'BUDGET', $budget_old, $data['budget'], '80'.$level);
        }
        return $data['budget'];
    }

    public function eval_songOptimization_campaign() { //송ai
        if(!$date) $date = date('Y-m-d');
        $result = $this->db->get_leveled_Optimization_campaignByReport($date, '80'); //송(캠페인)  가져오기
        while ($data = $result->fetch_assoc()) {
            $campaign_id = $data['id'];
            $type = $data['type'];
            $level = substr($data['type'],-1);
            if (!$campaign_id || !$level) return false;

            $impressions = 0;
            $evts = $this->db->getEventInfoByCampaignId($campaign_id);
            while ($evt = $evts->fetch_assoc()) {
                $impss = $this->db->getImpressionsByEvent(str_replace('evt_','',$evt['event_id']), $evt['site'], $date);
                if($impss){
                    $imps = $impss->fetch_assoc();
                    $impressions += $imps['impressions'];
                }
            }

            $cpc = $data['cpc']==0||!$data['cpc']?400:$data['cpc'];
            // evaluation -> 랜딩 유입수 * CPC <> DB단가 * DB수
            $eval_click = $data['db_price'] * $data['db_count'] - $data['clicks'] * $cpc;
            $eval = $data['db_price'] * $data['db_count'] - $impressions * $cpc;
            if(($level=='1' && $eval<-50000) || ($level=='2' && $eval<-100000) || ($level=='3' && $eval<-200000)) $status = CampaignStatus::PAUSED;//CampaignStatus::ENABLED;
            if($campaign_id && $status && $data['status'] != "PAUSED") {
                $campaign_data = ['id' => $campaign_id, 'status' => $status];
                $updateCampaign = $this->updateCampaign($campaign_data);
                $getId = $updateCampaign->getId();
                if ($getId == $campaign_id) {
                    $this->db->insertOptimizationHistory_campaign($campaign_id, $status, $type);
                    $this->db->insertOnoffHistoryCampaign($campaign_id, "SYSTEM", $type, $status);
                }
            }
            // 캠페인 아이디, 캠페인명, 랜딩 유입수, 클릭수, DB단가, 유효DB수, CPC, 평가(click), 평가(랜딩유입수)
            $str = $campaign_id."\t".str_replace(' ','',$data['name'])."\t".$impressions."\t".$data['clicks']."\t".$data['db_price']."\t".$data['db_count']."\t".$cpc."\t".$eval_click."\t".$eval;
            $this->log_write('/home/chainsaw/www/plugin/adwords_api/log/songAi_'.date('Ymd').'.log', $str);
        }
    }

    public function eval_choiOptimization_campaign() { //최ai
        if(!$date) $date = date('Y-m-d');
        $result = $this->db->getOptimization_campaignByReport($date, '701'); //최ai(캠페인)  가져오기
        while ($data = $result->fetch_assoc()) {
            $campaign_id = $data['id'];
            $cpaBidAmount_old = $this->getCpaBidAmount($data);

            if($data['clicks']<500){        // 클릭수 체크
                $cpaBidAmount = $data['budget_00'];
            }else{
                // 타겟 CPA 금액 변경 = 목표 DB 단가 * 80%or120%
                $eval = $data['db_price'] / $data['budget_00'];
                if($eval<=20){
                    $cpaBidAmount = $data['budget_00'] * 0.8;
                }else if($eval>=80){
                    $cpaBidAmount = $data['budget_00'] * 1.2;
                }
            }
            $campaign = ['id'=>$campaign_id, 'cpaBidAmount'=>$cpaBidAmount];

            if (!is_null($cpaBidAmount) && $cpaBidAmount_old!=$cpaBidAmount) {
                $updateCampaign = $this->updateCampaign($campaign);
                $getId = $updateCampaign->getId();
                if ($getId == $campaign_id) {
                    $this->db->insertOptimizationBudgetHistory_campaign($campaign_id, 'TARGETCPA', $cpaBidAmount_old, $cpaBidAmount, '701');
                }
            }
        }
    }

    public function setOptimization_buget($type){
        if(!$date) $date = date('Y-m-d');
        $result = $this->db->getOptimization_campaignByReport($date, $type); //최ai(캠페인)  가져오기
        while ($data = $result->fetch_assoc()) {
            $campaign_id = $data['id'];
            $cpaBidAmount = $data['budget_00'];
            $cpaBidAmount_old = $this->getCpaBidAmount($data);
            $campaign = ['id'=>$campaign_id, 'cpaBidAmount'=>$cpaBidAmount];

            if (!is_null($cpaBidAmount)) {
                $updateCampaign = $this->updateCampaign($campaign);
                $getId = $updateCampaign->getId();
                if ($getId == $campaign_id) {
                    $this->db->insertOptimizationBudgetHistory_campaign($campaign_id, 'TARGETCPA', $cpaBidAmount_old, $cpaBidAmount, '701');
                }
            }
        }

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function landingGroup($title)
    {
        if (!$title) {
            return null;
        }
        preg_match_all('/\#([0-9]+)?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches);
        if(!$matches[9][0]){    // site underscore exception
            preg_match_all('/\#([0-9]+)?(\_([0-9]+))?(\_([0-9]+))?([\s]+)?(\*([0-9]+)?)?([\s]+)?(\&([a-z]+))?([\s]+)?(\^([0-9]+))?/i', $title, $matches_re);
            $matches[9][0] = $matches_re[11][0];
            $matches[3][0] = $matches[3][0].$matches_re[4][0];
            $matches[6][0] = $matches_re[8][0];
            // $matches[12][0] = $matches_re[14][0];
        } 
        $db_prefix = '';
        switch ($matches[9][0]) {
            case 'ghr':
                $media = '핫이벤트 룰렛';
                if ($matches[1][0]) {
                    $db_prefix = 'app_';
                }
                break;
            case 'ghrcpm':
                $media = '핫이벤트 룰렛_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'app_';
                }
                break;
            case 'ger':
                $media = '이벤트 랜딩';
                if ($matches[1][0]) {
                    $db_prefix = 'evt_';
                }
                break;
            case 'ghsp':
                $media = '핫이벤트 스핀';
                if ($matches[1][0]) {
                    $db_prefix = 'event_';
                }
                break;
            case 'ghspcpm':
                $media = '핫이벤트 스핀_cpm';
                if ($matches[1][0]) {
                    $db_prefix = 'event_';
                }
                break;
            case 'wghr':
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
            'name' => ''
            ,'media' => ''
            ,'db_prefix' => ''
            ,'event_id' => ''
            ,'app_id' => ''
            ,'site' => ''
            ,'db_price' => 0
            ,'period_ad' => ''
        );
        if ($media) {
            $result['media']        = $media;
            $result['db_prefix']    = $db_prefix;
            $result['event_id']     = $matches[1][0];
            $result['app_id']       = $db_prefix.$matches[1][0];
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
        while ($row = $ads->fetch_assoc()) {
			//echo date('[H:i:s]')."광고({$row['ad_id']}) 유효DB개수 업데이트 - ";
            $landing = $this->landingGroup($row['code']);
            if ($landing['media']) {
                $result[$i]['ad_id'] = $row['ad_id'];
                $result[$i]['cost'] = $row['cost'];
                $result[$i]['event_id'] = $landing['event_id'];
                $result[$i]['app_id'] = $landing['app_id'];
                $result[$i]['site'] = $landing['site'];
                $result[$i]['db_price'] = $landing['db_price'];
                $result[$i]['period_ad'] = $landing['period_ad'];
                $result[$i]['media'] = $landing['media'];
                $result[$i]['db_prefix'] = $landing['db_prefix'];
                if(!preg_match('/cpm/', $landing['media'])) {
                    if(!$landing['app_id']) $error[] = '('.$row['ad_id'].'): APP_ID 미입력'.PHP_EOL;
                    if(!$landing['db_price']) $error[] = '('.$row['ad_id'].'): DB단가 미입력'.PHP_EOL;
                }
                $i++;
            } else {
                if(preg_match('/&[a-z]+/', $row['code'])) $error[] = '('.$row['ad_id'].'): 인식 오류'.PHP_EOL;
            }
			//echo PHP_EOL;
        }
        if(is_array($error) && count($error) > 0)
            $this->exception_handler($error);
        if (is_array($result)) {
            foreach ($result as $i => $data) {
                $result[$i]['count'] = 0;
                $result[$i]['sales'] = 0;
                $result[$i]['margin'] = 0;
                $sales = 0;
				$echo = '광고('.$data['ad_id'].') - 랜딩번호:'.$data['app_id'].'/사이트값:'.$data['site'];
                if ($data['app_id']) {
                    $rows = $this->db->getAppSubscribe($data, $date);
                    $result[$i]['count'] = $rows;
					$echo .= '/DB수:'.$rows;
                    /* 수익, 매출액 계산 */
                    /*=============================== 2018-11-19
                    fhrcpm /fhspcpm/ jhrcpm
                    유효db수는 불러오지만 수익,매출0

                    cpm
                    우효db 0 / 수익/ 매출0

                    ^25 = *0.25
                    */
                    $initZero = false;
                    if(preg_match('/cpm/i', $data['media'])) { //app_id 가 있는 cpm (fhrm, fhspcpm, jhrcpm)의 계산을 무효화
                        $initZero = true;
                    }
					$echo .= '/DB단가:'.$data['db_price'];
                    if ($data['db_price']) {
                        if(!$initZero)
                            $sales = $data['db_price'] * $rows;
                        $insight_data = new stdClass();
                        $insight_data->ad_id = $data['ad_id'];
                        $insight_data->date = $date;
                        $insight_data->data['sales'] = $sales;
                        $result[$i]['sales'] = $sales;
						$echo .= '/매출:'.$sales;
                        $this->db->updateReport($insight_data);
                    }
                    if(!$initZero)
                        $result[$i]['margin'] = $sales - $data['cost'];
                }
                if ($data['period_ad']) {
                    $result[$i]['margin'] = $data['spend'] * ('0.'.$data['period_ad']);
                }
				$echo .= '/수익:'.$result[$i]['margin'];
				$echo .= (isset($_SERVER['SHELL'])) ? PHP_EOL : "<br />";
                // echo $echo;
            }
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

    public function getReportByCsv($file) {
        $row = 1;       // or 3
        $result = [];
        
        $enc_array = array("EUC-KR","UTF-8");
        if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $data[$c] = $this->str_encode($data[$c]);
                    if($row == 1) {
                        $param = $this->getCsvTitle($data[$c]);
                        $titles[] = $param;
                        continue;
                    }
                    if($titles[$c]) $result[$i][$titles[$c]] = $data[$c];
                }
                $row++;
                $i++;
            }
            fclose($handle);
        }
        $msg = $this->db->insertReport($result);
        
        return $msg;
    }

    public function str_encode($str){
        $enc_array = array("EUC-KR","UTF-8");
        $enc = mb_detect_encoding($str, $enc_array);
        if($enc!="UTF-8") $str = iconv("EUC-KR", "UTF-8", $str);

        return $str;
    }

    private function csvTrim($txt) { //CSV공백제거
        $txt = preg_replace("/\xEF\xBB\xBF/", "", trim($txt));
        return $txt;
    }

    private function getCsvTitle($txt) {
        $titles = [
            "customer_name" => "계정명"
            ,"customerId" => "고객ID"
            ,"finalUrl" => "최종URL"
            ,"ad_title" => "광고제목"
            ,"ad_title1" => "광고제목1"
            ,"ad_title2" => "광고제목2"
            ,"ad_title3" => "광고제목3"
            ,"ad_title4" => "광고제목4"
            ,"ad_title5" => "광고제목5"
            ,"ad_description1" => "설명1"
            ,"ad_description2" => "설명2"
            ,"ad_description3" => "설명3"
            ,"ad_description4" => "설명4"
            ,"ad_click_draw_text" => "클릭유도문안텍스트"
            ,"ad_click_draw_title" => "클릭유도문안광고제목"
            ,"ad_name" => "광고이름"
            ,"campaign_name" => "캠페인"
            ,"adgroup" => "광고그룹"
            ,"amount" => "예산"
            ,"campaign_type" => "캠페인유형"
            ,"campaignId" => "캠페인ID"
            ,"adgroupId" => "광고그룹ID"
            ,"ad_id" => "광고ID"
            ,"campaign_status" => "캠페인상태"
            ,"date" => "일"
            ,"Adgroupstate" => "광고그룹상태"
            ,"Adstate" => "광고상태"
            ,"biddingStrategyType" => "캠페인입찰전략유형"
            ,"currency" => "통화코드"
            ,"impressions" => "노출수"
            ,"clicks" => "클릭수"
            ,"ctr" => "클릭률(CTR)"
            ,"cpc" => "평균CPC"
            ,"cost" => "비용"
            ,"conversion" => "전환"
            ,"conversion_price" => "전환당비용"
            ,"conversion_rate" => "전환율"
        ];
        
        $txt = $this->csvTrim($txt);
        $txt = preg_replace('/\s+/', '', trim($txt));
        
        if($result = array_search($txt, $titles))
            return $result;
        else 
            return NULL;
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
                                $var = str_replace('{'.$k.'}', $var, $v);
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

    public static function exception_handler($e)
    {
        //echo nl2br(print_r($e,1));
        echo('<xmp style="color:#fff;background-color:#000;">');
        print_r($e);
        echo('</xmp>');
        return true;
    }

    public function getMemo($data) {
        $response = $this->db->getMemo($data);
        return $response;
    }

    public function addMemo($data) {
        return $this->db->addMemo($data);
    }

    public function log_write($log_dir, $str){
        $fp = fopen($log_dir,'a');
        if($log_dir=='' || $log_dir==null) throw new exception("Log_dir error");    // 1. log_dir check
        if($str=='' || $str==null) throw new exception("Log_data non-exist");       // 2. log_data check
        try{
            fwrite($fp, date('H:i:s')."\t".$str."\n");       // 3. write log
        }
        catch(Exception $e){
            $e = $e->getMessage().'(오류코드:'.$e->getCode().')';
        }
        
        fclose($fp);        // 4. close
    }
}