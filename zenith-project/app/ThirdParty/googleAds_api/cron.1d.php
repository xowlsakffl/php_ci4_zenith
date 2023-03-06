<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
include __DIR__."/include/adwords.php";

$adwords = new AdWords();

//API 업데이트가 잘 되고 있는지 체크
//$chainsaw->checkDoingWell();

$ago = date('Y-m-d', strtotime('-1 month'));
$today = date('Y-m-d');
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for($day=1; $day<$gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$adwords->getAdsUseLanding($date);
	echo date('[H:i:s]').$date .' 완료'.PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}

