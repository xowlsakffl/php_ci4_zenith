<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
include __DIR__."/include/kmapi.php";
// include './include/kmdb.php';


$chainsaw = new ChainsawKM();
// $kmdb = new KMDB();
/*
for($i=1; $i<=5; $i++) {
	$updateCreatives = $kmapi->getCreativesUseLanding(date('Y-m-d', strtotime("-{$i} days")));
	$kmapi->grid($updateCreatives);
}
*/
// $chainsaw->updateCreatives();
// $getCreativesUseLanding = $chainsaw->updateReportByDate('2020-10-01', '2020-10-08');
// $chainsaw->grid($getCreativesUseLanding);
/*
$ago = date('Y-m-d', strtotime('2020-10-10'));
$today = date('Y-m-d');
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for($day=0; $day<=$gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$chainsaw->getCreativesUseLanding($date);
	echo date('[H:i:s]').$date .' 완료'.PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
*/
if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
$date = date('Y-m-d', strtotime($_GET['date']));
if(strtotime($date) >= time())
	exit('오늘 이후 날짜는 실행 할 수 없습니다');
echo '<p>카카오 모먼트 광고관리에서 <strong>';
echo date('Y년 m월 d일', strtotime($date));
echo '</strong>자 유효DB를 업데이트 합니다.'; ob_flush(); flush(); usleep(1);
echo '</p>';
// $chainsaw->updateCreativesReportBasic($date);
$getCreativesUseLanding = $chainsaw->getCreativesUseLanding($date);
if($getCreativesUseLanding)
	echo '<p>업데이트 완료</p>'; ob_flush(); flush(); usleep(1);
if($_GET['grid'])
	$chainsaw->grid($getCreativesUseLanding);