<?php
session_start();
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);

$hour = date('H');
if($hour >= 0 && $hour <= 9) exit;

$db = mysqli_connect('db.chainsaw.co.kr', 'moment', 'qkdlqmdkfTl#mm', 'moment');
$db->query("set session character_set_client=utf8mb;");
$db->query("set session character_set_connection=utf8mb;");

include __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."icode.component.php";

$chk_tbl = array();
$chk_tb = array( 'mm_ad_account' 			=> array('name' => '광고 계정', 'fld' => 'update_time')
				,'mm_campaign' 				=> array('name' => '캠페인', 'fld' => 'update_time')
				,'mm_adgroup'				=> array('name' => '광고그룹', 'fld' => 'update_time')
				,'mm_creative' 				=> array('name' => '소재', 'fld' => 'update_time')
				,'mm_creative_report_basic'	=> array('name' => '리포트 기록', 'fld' => 'update_time')
				,'mm_db_count'			 	=> array('name' => 'DB카운트', 'fld' => 'update_time')
			);
foreach($chk_tb as $tbl => $data) {
	$sql = "SELECT {$data['fld']} AS last_update, FALSE AS send_time FROM {$tbl} ORDER BY {$data['fld']} DESC LIMIT 1";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	$chk_tbl[$tbl] = $row;
}

$SMS = new SMS;                                                 /* SMS 모듈 클래스 생성 */
$strTelList     = "01040010000;01079994865;01031065711;";//01079994865;01075739139;01031065711;";       /* 수신번호 : 01000000001;0100000002; */
$strCallBack    = "01079994865";                                /* 발신번호 */
$strDest        = explode(";", $strTelList);
$nCount         = count($strDest)-1;                            // 문자 수신번호 갯수
$chkSendFlag    = 0;    /* 예약 구분자 : 0 즉시전송, 1 예약발송 */
$R_YEAR         = "";   /* 예약 : 년(4자리) 2016 */
$R_MONTH        = "";   /* 예약 : 월(2자리) 01 */
$R_DAY          = "";   /* 예약 : 일(2자리) 31 */
$R_HOUR         = "";   /* 예약 : 시(2자리) 02 */
$R_MIN          = "";   /* 예약 : 분(2자리) 59 */
// 예약설정을 합니다.
if ($chkSendFlag) {
	$strDate =  $R_YEAR.$R_MONTH.$R_DAY.$R_HOUR.$R_MIN;
} else {
	$strDate = "";
}
$strData = '== MomentAPI 누락 알림 =='.PHP_EOL;
$smsCnt = 0;
$chk_time = strtotime('-1 hour');
$cur_time = time();
$hour = date('H');

$filename = __DIR__.DIRECTORY_SEPARATOR."chk_tbl.json";
$db_file = fopen($filename, "r") or die("Unable to open file!");
$content = fread($db_file, filesize($filename)); // 읽기
$data = json_decode($content, true);
fclose($db_file);

foreach ($chk_tbl as $tbl => $row) {
	$last_update = strtotime($row['last_update']);
	$chk_tbl[$tbl]['send_time'] = $data[$tbl]['send_time'];
	 if($last_update < $chk_time) {
		 if($data[$tbl]['send_time'] == 0 || strtotime($data[$tbl]['send_time']. ' +6 hour') <= $cur_time) {
			 $strData .= '['.$chk_tb[$tbl]['name'].'('.$tbl.')] '.date('m/d H:i', strtotime($row['last_update'])).PHP_EOL;
			 $chk_tbl[$tbl]['send_time'] = date('Y-m-d H:i:s', $cur_time);
			 $smsCnt++;
		 }
	 }
}

if ($smsCnt) {
	// 문자 발송에 필요한 항목을 배열에 추가
	$result = $SMS->Add($strDest, $strCallBack, '', '', '', $strData, $strDate, $nCount);
	// 패킷이 정상적이라면 발송에 시도합니다.
	if(!$response = $SMS->Send()) {
		echo '문자전송 실패'.PHP_EOL;
	} else {
		$db_file = fopen($filename, "w") or die("Unable to open file!");
		$db = json_encode($chk_tbl);
		fwrite($db_file, $db) or die("Can not Write file!");
		fclose($db_file);
	}
	
	echo $smsCnt .'개의 테이블 업데이트 문제 발생'.PHP_EOL;
}
echo date('[H:i:s]').' 모먼트API 테이블 체크 완료'.PHP_EOL;
?>
