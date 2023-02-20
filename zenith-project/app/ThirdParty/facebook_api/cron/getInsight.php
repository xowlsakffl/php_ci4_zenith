<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//include '../../common.php';
include __DIR__ . "/../facebook-api.php";
$chainsaw = new ChainsawFB();

$chainsaw->getAsyncInsights();