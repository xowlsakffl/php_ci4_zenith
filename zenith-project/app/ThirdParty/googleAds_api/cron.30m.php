<?php
include __DIR__.'/include/adwords.php';
// /plugin/adwords_api/cron.30m.php

$adwords = new AdWords();
$db = new AWDB();

$date = date('Y-m-d'); // 20181214 시간변수추가 ( 1시간짜리 크론은 빈값으로)

// $db->updateOptimization_campaign();  //정파고 캠페인 ON/OFF
// $adwords->getOptimization_campaign($date); // 정파고 캠페인 ON/OFF
// echo date('[H:i:s]').'정파고(캠페인) ON/OFF 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);

$adwords->getReportWithAwql();
echo date('[H:i:s]').'동영상 광고 리포트 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
