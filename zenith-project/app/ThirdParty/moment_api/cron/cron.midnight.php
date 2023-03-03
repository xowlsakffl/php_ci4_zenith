<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);
include __DIR__."/include/kmapi.php";

$chainsaw = new ChainsawKM();
$db = new KMDB();

$chainsaw->setAdGroupsAiRun();
echo date('[H:i:s]').'광고그룹 AI 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);