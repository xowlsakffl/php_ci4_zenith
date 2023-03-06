<?php

// https://www.chainsaw.co.kr/plugin/adwords_api/test.php
include __DIR__.'/include/adwords.php';

$adwords = new AdWords();
$db = new AWDB();

$hour = ""; // 20181214 시간변수추가 ( 1시간짜리 크론은 빈값으로)
// $adwords->getReport('2020-08-24');
// $db->updateOptimization_campaign();  //정파고 광고 ON/OFF
// $adwords->getOptimization_campaign($hour); // 정파고 광고 ON/OFF
// echo date('[H:i:s]').'정파고(캠페인) ON/OFF 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//if($reports) $db->insertAdReports($reports);
$adwords->updateCampaignsByDB();
// echo '<pre>'.print_r($result,1).'</pre>';
/*
echo '----------------------------------------------------------------------------------';
$getAdGroups = $adwords->getAdGroups(6918566735, 1625039424);
echo '<pre>'.print_r($getAdGroups['data'],1).'</pre>';
*/
/*
$adAccounts = $adwords->getAdAccounts('true');
foreach($adAccounts['data'] as $customerId => $account) {
	$campaigns[] = $adwords->getCampaigns($account['CustomerId'], null, null);
}
*/
/*
$data = [
	'campaignId' => '1810851796',
	'name' => '',
	'status' => '',
];
$updateCampaign = $adwords->updateCampaign($data);
echo '<pre>'.print_r($updateCampaign,1).'</pre>';
$adwords->grid($updateCampaign);
*/