<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
include __DIR__ . "/include/kmapi.php";

$hour = date('H');

$chainsaw = new ChainsawKM();
if ($hour >= 0 && $hour <= 3) exit;

$is_reset = (date('H') == "23" && date('i') == "55") ? true : false;
if ($is_reset) {
	$chainsaw->autoCreativeOnOff('off');
	echo date('[H:i:s]') . '소재 자동 ON 완료' . PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
} else {
	$chainsaw->autoCreativeOnOff('on');
	ob_flush();
	flush();
	usleep(1);
}
