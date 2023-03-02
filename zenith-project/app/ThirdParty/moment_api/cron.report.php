<?php
require __DIR__ . '/common.php';
include __DIR__ . "/include/kmapi.php";

$procs = preg_grep('/report|1d/', $proc);
if (count($procs) > 1) {
    exit;
}

$hour = date('H');

$chainsaw = new ChainsawKM();
$db = new KMDB();

if ($hour >= 0 && $hour <= 3) exit;
$chainsaw->updateCreativesReportBasic();
echo date('[H:i:s]') . '보고서 BASIC 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->getCreativesUseLanding();
echo date('[H:i:s]') . '유효DB 및 매출 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->autoAiOn(); //Ai 자동켜기