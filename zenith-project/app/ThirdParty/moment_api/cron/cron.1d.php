<?php
require __DIR__.'/common.php';
include __DIR__."/include/kmapi.php";

$chainsaw = new ChainsawKM();
$db = new KMDB();
echo 'Start'.PHP_EOL; ob_flush(); flush(); usleep(1);
$ago = date('Y-m-d', strtotime('-3 day'));
$today = date('Y-m-d', strtotime('-1 day'));
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for($i=0; $i<=$gap; $i++) {
	$date = date('Y-m-d', strtotime("$ago $i day"));
	$day = ($i==$gap)?'YESTERDAY':$date;
	$chainsaw->updateCreativesReportBasic($day);
	echo date('[H:i:s]').$day .' 보고서 BASIC 데이터 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
	$chainsaw->getCreativesUseLanding($date);
	echo date('[H:i:s]').$date .' 유효DB수 데이터 업데이트 완료'.PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
