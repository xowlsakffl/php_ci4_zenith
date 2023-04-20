<?php
namespace App\Libraries;

class Calc {

	public static function cpc($spend, $inline_link_clicks) { //CPC(Cost Per Click: 클릭당단가 (1회 클릭당 비용)) = 지출액/링크클릭
		if($inline_link_clicks > 0) 
			return round($spend / $inline_link_clicks);

		return 0;
	}

	public static function ctr($inline_link_clicks, $impression) { //CTR(Click Through Rate: 클릭율 (노출 대비 클릭한 비율)) = (링크클릭/노출수)*100
		if($impression > 0) 
			return round(($inline_link_clicks / $impression)*100, 2);

		return 0;
	}

//	public static function sales($unique, $db_price) { //매출액 = CPA단가 * 유효 DB
//		if($unique > 0) 
//			return round($unique * $db_price);
//		
//		return 0;
//	}

//	public static function margin($sales, $spend, $shortterm) { //수익 = 매출액(CPA단가*유효 DB) - 지출액
//		$shortterm_val =  "0.".$shortterm;
//
//		if($spend > 0){
//			if($shortterm > 0){
//				$result = $spend * $shortterm_val;//^ 뒤에 값이 있으면 수익 계산 다르게
//			} 
//			else{
//				$result = $sales - $spend;
//			}
//		} 
//		return round($result);
//	}

	public static function margin_ratio($margin, $sales) { //수익률 = (수익/매출액)*100
		if($sales > 0){
			$result =($margin / $sales)*100;
			return round($result,2);
		}
	}

	public static function cpa($unique, $spend) { //CPA(Cost Per Action: DB단가(전환당 비용)) = 지출액/유효db
		if($unique > 0){
			$result = $spend / $unique;
			return round($result);
		}
	}

	public static function cvr($unique, $inline_link_clicks) { //CVR(Conversion Rate:전환율 = (유효db / 링크클릭)*100
		if($inline_link_clicks > 0) 
			return round(($unique / $inline_link_clicks)*100, 2);

		return 0;
	}

//	public static function cvc($spend, $inline_link_clicks) { //Conversion Cost 전환단가 = 지출액/클릭
//		if($inline_link_clicks > 0) 
//			return round($spend / $inline_link_clicks);
//
//		return 0;
//	}

}
?>