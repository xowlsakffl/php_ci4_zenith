<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);

exec("ps ax | grep -i moment_api | grep -v grep", $exec);
foreach($exec as $v) $proc[] = preg_replace('/^.+php\s(.+)$/', '$1', $v);

include __DIR__."/include/kmapi.php";

$procs = preg_grep('/report/', $proc);
if(count($procs)) {
	echo 'count';
}

$hour = date('H');

$chainsaw = new ChainsawKM();
$db = new KMDB();

if($hour >= 0 && $hour <= 7) exit;
echo 'pass';
exit;
$chainsaw->updateCreativesReportBasic();
echo date('[H:i:s]').'보고서 BASIC 데이터 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
$chainsaw->getCreativesUseLanding();
echo date('[H:i:s]').'유효DB 및 매출 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);