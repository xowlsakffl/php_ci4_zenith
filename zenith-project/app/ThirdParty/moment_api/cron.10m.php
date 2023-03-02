<?php
require __DIR__ . '/common.php';

include __DIR__ . "/include/kmapi.php";

$hour = date('H');

$chainsaw = new ChainsawKM();
$db = new KMDB();

if ($hour >= 0 && $hour <= 3) exit;

$chainsaw->getCreativesUseLanding();
echo date('[H:i:s]') . '유효DB 및 매출 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->updateAdAccounts();
echo date('[H:i:s]') . '계정 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);

$chainsaw->updateCampaigns();
echo date('[H:i:s]') . '캠페인 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->updateAdGroups();
echo date('[H:i:s]') . '광고그룹 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->updateCreatives();
echo date('[H:i:s]') . '소재 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->updateBizform();
echo date('[H:i:s]') . '비즈폼 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->moveToAppsubscribe();
echo date('[H:i:s]') . 'app_subscribe 데이터 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
$chainsaw->getCreativesUseLanding();
echo date('[H:i:s]') . '유효DB 및 매출 업데이트 완료' . PHP_EOL;
ob_flush();
flush();
usleep(1);
