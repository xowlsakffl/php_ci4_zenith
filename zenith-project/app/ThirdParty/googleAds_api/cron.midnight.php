<?php
include __DIR__.'/include/adwords.php';

$adwords = new AdWords();
$db = new AWDB();

$adwords->getOptimization_campaign_restart('901'); // 정파고 캠페인 ON/OFF
$adwords->getOptimization_leveled_campaign_restart('80'); // 송ai 캠페인 ON/OFF

// 최ai 타겟 CPA 금액 수정
$adwords->setOptimization_buget('701');     // 최ai 타겟CPA 금액 수정

