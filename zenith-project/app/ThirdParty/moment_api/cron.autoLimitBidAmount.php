<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
include __DIR__ . "/include/kmapi.php";

$hour = date('H');

$chainsaw = new ChainsawKM();
if ($hour >= 1 && $hour <= 3) exit;

$is_reset = (date('H') == "23" && date('i') == "55") ? true : false;
if ($is_reset || $_GET['reset']) {
	$chainsaw->autoLimitBidAmountReset();
	echo date('[H:i:s]') . '자동 입찰가한도 리셋 완료' . PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
} else {
	$chainsaw->autoLimitBidAmount();
	ob_flush();
	flush();
	usleep(1);
}
