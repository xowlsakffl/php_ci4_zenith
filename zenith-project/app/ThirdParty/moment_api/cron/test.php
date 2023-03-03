<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
include __DIR__ . "/include/kmapi.php";
// include __DIR__ . "/include/bizformapi.php";
// include './include/kmdb.php';
$chainsaw = new ChainsawKM();
$updateCreatives = $chainsaw->getBulkCreatives(47147);
$chainsaw->grid($updateCreatives);
// $chainsaw->updateBizform();
echo date('[H:i:s]') . '비즈폼 데이터 업데이트 완료' . PHP_EOL;

// $chainsaw = new ChainsawKM();
// $kmdb = new KMDB();
// $getCreatives = $chainsaw->getCreative(18979622, 270565);
// $chainsaw->updateCreativesReportBasic('YESTERDAY');
// $chainsaw->moveToAppsubscribe();
// $chainsaw->moveToAppsubscribe();
// $getCreatives = $chainsaw->getBizForms(270565);
// echo '<pre>' . print_r($getCreatives, 1) . '</pre>';
// $adgroups = $chainsaw->autoCreativeOnOff('on');
// var_dump($adgroups);
/*$getCreativesUseLanding = $chainsaw->getCreativesUseLanding('2022-11-08');
$chainsaw->grid($getCreativesUseLanding);
*/
/*
for ($i = 0; $i <= 1; $i++) {
	$updateCreatives = $chainsaw->getCreativesUseLanding(date('Y-m-d', strtotime("-{$i} days")));
	$chainsaw->grid($updateCreatives);
}
*/
// $result = $chainsaw->updateCreativesReportBasic('YESTERDAY');
//$updateCreatives = $chainsaw->updateAdGroups();
//$chainsaw->grid($updateCreatives);

// $getCreativesUseLanding = $chainsaw->getBulkCampaigns("171463");
// $chainsaw->grid($getCreativesUseLanding);
// 11,20,21,22,23,28
/*
$ago = date('Y-m-d', strtotime('2023-01-06'));
$today = date('Y-m-d');
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for($day=0; $day<=$gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$chainsaw->updateCreativesReportBasic($date);
	$chainsaw->getCreativesUseLanding($date);
	echo date('[H:i:s]').$date .' 완료'.PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
*/
/*
$ago = date('Y-m-d', strtotime('2022-04-01'));
$today = date('Y-m-d', strtotime('-1 day'));
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for($i=0; $i<=$gap; $i++) {
	$date = date('Y-m-d', strtotime("$ago $i day"));
	$chainsaw->getCreativesUseLanding($date);
	$day = ($i==$gap)?'YESTERDAY':$date;
	//$chainsaw->updateCreativesReportBasic($date);
	echo date('[H:i:s]').$day .' 보고서 BASIC 데이터 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
	echo date('[H:i:s]').$date .' 유효DB수 데이터 업데이트 완료'.PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
*/
// $updateCreatives = $chainsaw->updateCreatives();
// $chainsaw->grid($updateCreatives);
// $updateCreativesReportBasic = $chainsaw->updateCreativesReportBasic();
// // $getCreativesUseLanding = $chainsaw->getCreativesUseLanding("2020-11-10");
// $chainsaw->grid($updateCreativesReportBasic);
/*
$sdate = date('Y-m-d', strtotime("2020-11-19"));
$edate = date('Y-m-d', strtotime("2020-11-20"));

$gap = (strtotime($edate) - strtotime($sdate)) / 60 / 60 / 24;
for($day=1; $day<=$gap; $day++) {
	$date = date('Y-m-d', strtotime("-$day day"));
	$chainsaw->updateCreativesReportBasic($date);
	$chainsaw->getCreativesUseLanding($date);
	echo date('[H:i:s]')." {$date} 유효DB수 데이터 업데이트 완료".PHP_EOL; ob_flush(); flush(); usleep(1);
	ob_flush();
	flush();
	usleep(1);
}
*/
