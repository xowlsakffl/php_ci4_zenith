<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
include __DIR__."/include/adwords.php";

$adwords = new AdWords();

$adwords->updateCampaignsByDB();
echo date('[H:i:s]').'전체 캠페인 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
$adwords->updateAdGroupsByDB();
echo date('[H:i:s]').'전체 광고그룹 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
$adwords->updateAdsByDB();
echo date('[H:i:s]').'전체 광고 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);