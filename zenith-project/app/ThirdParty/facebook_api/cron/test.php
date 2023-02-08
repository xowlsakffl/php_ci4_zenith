<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
//include '../../common.php';
include __DIR__ . "/../facebook.php";
$chainsaw = new ChainsawFB();
//$db = new FBDB();
// $chainsaw->getLongLivedAccessToken();
/*
$ago = date('Y-m-d', strtotime('2022-10-31'));
// $today = date('Y-m-d');
$today = '2022-10-31';
$gap = (strtotime($today) - strtotime($ago)) / 60 / 60 / 24;
for ($day = 0; $day <= $gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$insight = $chainsaw->getAsyncInsights(false, $date);
	$chainsaw->grid($insight);
	$chainsaw->getAdsUseLanding($date);
	echo date('[H:i:s]') . $date . ' 완료' . PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
$chainsaw = new ChainsawFB('2859468974281473'); //케어랩스5
for ($day = 0; $day <= $gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$insight = $chainsaw->getAsyncInsights(false, $date);
	$chainsaw->grid($insight);
	$chainsaw->getAdsUseLanding($date);
	echo date('[H:i:s]') . $date . ' 완료' . PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}
$chainsaw = new ChainsawFB('213123902836946'); //케어랩스7
for ($day = 0; $day <= $gap; $day++) {
	$date = date('Y-m-d', strtotime("$ago $day day"));
	$insight = $chainsaw->getAsyncInsights(false, $date);
	$chainsaw->grid($insight);
	$chainsaw->getAdsUseLanding($date);
	echo date('[H:i:s]') . $date . ' 완료' . PHP_EOL;
	ob_flush();
	flush();
	usleep(1);
}

*/

// $chainsaw->getFBAdOptimization_goal_campaign($hour);
// $chainsaw = new ChainsawFB('213123902836946'); //케어랩스2
// $chainsaw->getFBAdAccountsPerm();
// $getAdsUseLanding = $chainsaw->getLongLivedAccessToken();
// $updateAdsets = $chainsaw->getAdLead();
// $chainsaw->grid($updateAdsets);
// $getAdsUseLanding = $chainsaw->getAsyncInsights(true);
// echo date('[H:i:s]').'유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
// $getAdLead = $chainsaw->getAdLead('-3 day');
// echo date('[H:i:s]').'잠재고객 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
// $chainsaw->grid($getAdLead);
// $updateLeadgenCnt = $db->updateLeadgenCnt();
// echo date('[H:i:s]').'잠재고객 개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
// $chainsaw->getAdsUseLanding();
// echo date('[H:i:s]').'유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//$db->updateOptimization_ad();  //정파고 광고 ON/OFF
//$chainsaw->getFBAdOptimization_ad($hour); // 정파고 광고 ON/OFF

//echo date('[H:i:s]').'정파고(광고) ON/OFF 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//
//$db->updateOptimization_campaign();  //정파고 캠페인단 예산 업데이트
//$chainsaw->getFBAdOptimization_campaign($hour); // 정파고 일일예산 업데이트

//echo date('[H:i:s]').'정파고(캠페인) 일일예산 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//
//$getAdLead = $chainsaw->updateAdAccounts();
//echo date('[H:i:s]').'유효DB개수 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//$chainsaw->grid($getAdLead); exit;

// $chainsaw->updateAdAccounts();
// $accounts = $chainsaw->getFBAccounts();
// $chainsaw->grid($accounts); exit;
// echo date('[H:i:s]').'계정 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);

////$chainsaw->getLeadgens();
//echo date('[H:i:s]').'잠재고객 업데이트 완료'.PHP_EOL; ob_flush(); flush(); usleep(1);
//

/*
$host = "218.38.16.92";
$user = "facebook";
$password = "qkdlqmdkfTl#fb";
$dbname = "facebook";
$db = mysqli_connect($host, $user, $password, $dbname);
$db->query("set session character_set_client=utf8mb;");
$db->query("set session character_set_connection=utf8mb;");

$budget_type = NULL;
$budget = 'NULL';
$budget_remaining = 'NULL';
$sql = "
UPDATE fb_adset SET adset_name = '6월생', budget_type = '$budget_type', budget = $budget, budget_remaining = $budget_remaining, effective_status = 'CAMPAIGN_PAUSED', status = 'ACTIVE', start_time = '2018-08-13 13:24:04', created_time = '2018-08-13 13:24:03', updated_time = '2019-03-19 16:02:35', update_date = NOW() WHERE adset_id = '23842982745750608';
";
$db->query($sql) or die($db->error);
*/
// $business_id = '169265856956659'; //2
// $business_id = '381690169907152'; //3
// $business_id = '676318936342751'; //4
// $chainsaw->deleteBusiness($business_id);