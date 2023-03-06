<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1');
ini_set('mysql.connect_timeout', '86400');
ini_set('default_socket_timeout', '86400');

include __DIR__."/include/adwords.php";
echo 'Start!'.PHP_EOL;
$hour = date('H');

if($hour >= 0 && $hour <= 1) exit;
$date = date('Y-m-d');
$adwords = new AdWords();

//API 업데이트가 잘 되고 있는지 체크
//$chainsaw->checkDoingWell();
$adwords->getAdsUseLanding();
echo date('[H:i:s]').'유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//$adwords->getAdAccountList();
//echo date('[H:i:s]').'계정정보 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//$adwords->getPausedCampaigns();
//echo date('[H:i:s]').'중지된 캠페인 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//$adwords->getAll();
//echo date('[H:i:s]').'현재 운영중인 광고 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
$adwords->getOptimization_campaign($date); // 정파고 캠페인 ON/OFF
echo date('[H:i:s]').'정파고(캠페인) ON/OFF 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);

//송ai
$adwords->eval_songOptimization_campaign();
echo date('[H:i:s]').'송ai-평가 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//최ai
$adwords->eval_choiOptimization_campaign();
echo date('[H:i:s]').'최ai-평가 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
