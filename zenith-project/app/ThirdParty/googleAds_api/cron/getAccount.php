<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
ini_set('mysql.connect_timeout', '86400');
ini_set('default_socket_timeout', '86400');

include __DIR__."/include/adsapi.php";
echo 'Start!'.PHP_EOL;

$date = date('Y-m-d');
$chainsaw = new GoogleAds();

//API 업데이트가 잘 되고 있는지 체크
//$chainsaw->checkDoingWell();
$chainsaw->getAccounts();
echo date('[H:i:s]').'광고계정 수신'.PHP_EOL; ob_flush(); flush(); usleep(1);