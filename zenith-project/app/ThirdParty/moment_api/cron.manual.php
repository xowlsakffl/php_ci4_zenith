<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);

if(date('Y-m-d') !== '2023-01-11') exit;

include __DIR__."/include/kmapi.php";

$chainsaw = new ChainsawKM();
$db = new KMDB();

$sdate = '2023-01-06';
$edate = '2023-01-09';

$chainsaw->updateReportByDate($sdate, $edate);
echo date('[H:i:s]')." {$sdate}~{$edate} 보고서 BASIC 데이터 업데이트 완료".PHP_EOL; ob_flush(); flush(); usleep(1);
$gap = (strtotime($edate) - strtotime($sdate)) / 60 / 60 / 24;
for($day=0; $day<=$gap; $day++) {
	$date = date('Y-m-d', strtotime("$sdate $day day"));
	$chainsaw->getCreativesUseLanding($date);
	echo date('[H:i:s]')." {$date} 유효DB수 데이터 업데이트 완료".PHP_EOL; ob_flush(); flush(); usleep(1);
	ob_flush();
	flush();
	usleep(1);
}
