<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
ini_set('mysql.connect_timeout', '86400');
ini_set('default_socket_timeout', '86400');

include __DIR__."/include/adsapi.php";
echo 'Start!'.PHP_EOL;
$hour = date('H');

$chainsaw = new GoogleAds();

$sdate = date('Y-m-d', strtotime("-15 day"));
$edate = date('Y-m-d');
$gap = (strtotime($edate) - strtotime($sdate)) / 60 / 60 / 24;
for($day=0; $day<$gap; $day++) {
	$date = date('Y-m-d', strtotime("$sdate $day day"));
	$chainsaw->getAll($date);
	echo date('[H:i:s] ').$date.' 광고 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
	$chainsaw->getAdsUseLanding($date);
	echo date('[H:i:s] ').$date.' 유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
	ob_flush();
	flush();
	usleep(1);
}