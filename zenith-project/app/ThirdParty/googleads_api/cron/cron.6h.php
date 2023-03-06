<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
ini_set('mysql.connect_timeout', '86400');
ini_set('default_socket_timeout', '86400');

include __DIR__."/include/adsapi.php";
echo 'Start!'.PHP_EOL;
$hour = date('H');

if($hour >= 0 && $hour <= 1) exit;
$chainsaw = new GoogleAds();

$yesterday = date('Y-m-d', strtotime('-1 day'));

$chainsaw->getAll($yesterday);
echo date('[H:i:s]').'전날 광고 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
$chainsaw->getAdsUseLanding($yesterday);
echo date('[H:i:s]').'전날 유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);

