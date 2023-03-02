<?php
require __DIR__.'/common.php';
include __DIR__."/include/kmapi.php";

$chainsaw = new ChainsawKM();
$db = new KMDB();

$d = date('Y-m-01');
$sdate = date('Y-m-01', strtotime("{$d} -1 month"));
$edate = date('Y-m-t', strtotime("{$d} -1 month"));

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
