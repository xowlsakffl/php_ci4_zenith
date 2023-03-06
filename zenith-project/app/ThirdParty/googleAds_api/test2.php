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
$date = "2022-01-11";
$adwords = new AdWords();

//API 업데이트가 잘 되고 있는지 체크
//$chainsaw->checkDoingWell();
// $campaign['campaign_id'] = 14791609838;
// $adwords->updateCampaigns($campaign, 1);
// echo date('[H:i:s]').'유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);

$reports = $adwords->getReport($date);
if($reports) $adwords->db->insertAdReports($reports);
echo date('[H:i:s]').'리포트 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
// $campaign = $adwords->getCampaigns('5461581597', '15494258298', null)['data'][0];
// echo "<pre>".print_r($campaign)."</pre><br>";

