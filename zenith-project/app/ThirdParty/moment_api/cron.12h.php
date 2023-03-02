<?php
require __DIR__.'/common.php';
include __DIR__."/include/kmapi.php";

$chainsaw = new ChainsawKM();
$db = new KMDB();

$chainsaw->refresh_token();
echo date('[H:i:s]').'토큰 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
