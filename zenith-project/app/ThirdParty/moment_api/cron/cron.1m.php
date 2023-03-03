<?php
require __DIR__ . '/common.php';
include __DIR__ . "/include/kmapi.php";

$hour = date('H');

$chainsaw = new ChainsawKM();
$db = new KMDB();

if ($hour >= 0 && $hour <= 3) exit;

$chainsaw->getCreativesUseLanding();
// echo date('[H:i:s]').'유효DB 및 매출 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);