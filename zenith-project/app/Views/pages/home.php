<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 홈
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script>
    console.log('header')
</script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="main-contents-wrap">
    <div class="ad-list text-center">
        <div class="row">
            <div class="type" id="facebookReport">
                <div class="summary">
                    <strong><i class="facebook"></i>페이스북</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd id="per_sum"></dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd id="profit_sum"></dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd id="impressions_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd id="clicks_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd id="click_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd id="spend_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd id="unique_total_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd id="unique_one_price_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd id="conversion_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd id="price_01_sum"></dd>
                    </dl>
                </div>
            </div>
            <a href="/advertisements/facebook" class="btn-more btn-primary"><span>더보기</span></a>
        </div>
        <div class="row">
            <div class="type" id="kakaoReport">
                <div class="summary">
                    <strong><i class="kakao"></i>카카오 모먼트</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd id="per_sum"></dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd id="profit_sum"></dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd id="impressions_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd id="clicks_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd id="click_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd id="spend_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd id="unique_total_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd id="unique_one_price_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd id="conversion_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd id="price_01_sum"></dd>
                    </dl>
                </div>
            </div>
            <a href="/advertisements/kakao" class="btn-more"><span>더보기</span></a>
        </div>
        <div class="row">
            <div class="type" id="googleReport">
                <div class="summary">
                    <strong><i class="google"></i>구글 애드워즈</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd id="per_sum"></dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd id="profit_sum"></dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd id="impressions_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd id="clicks_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd id="click_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd id="spend_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd id="unique_total_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd id="unique_one_price_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd id="conversion_ratio_sum"></dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd id="price_01_sum"></dd>
                    </dl>
                </div>
            </div>
            <a href="/advertisements/google" class="btn-more btn-primary"><span>더보기</span></a>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var args = {
'sdate': moment().format('YYYY-MM-DD'),
'edate': moment().format('YYYY-MM-DD'),
};

getReport(args);

function getReport(){
    getFacebookReport(args);
    getKakaoReport(args);
    getGoogleReport(args);
}
function getFacebookReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/facebook/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#facebookReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#facebookReport #impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#facebookReport #clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#facebookReport #click_ratio_sum').text(data.click_ratio_sum);
            $('#facebookReport #spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#facebookReport #unique_total_sum').text(data.unique_total_sum);
            $('#facebookReport #unique_one_price_sum').text(data.unique_one_price_sum.toLocaleString('ko-KR'));
            $('#facebookReport #conversion_ratio_sum').text(data.conversion_ratio_sum);
            $('#facebookReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#facebookReport #per_sum').text(data.per_sum);
            $('#facebookReport #price_01_sum').text(data.price_sum.toLocaleString('ko-KR'));
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function getKakaoReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/kakao/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#kakaoReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #click_ratio_sum').text(data.click_ratio_sum);
            $('#kakaoReport #spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #unique_total_sum').text(data.unique_total_sum);
            $('#kakaoReport #unique_one_price_sum').text(data.unique_one_price_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #conversion_ratio_sum').text(data.conversion_ratio_sum);
            $('#kakaoReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#kakaoReport #per_sum').text(data.per_sum);
            $('#kakaoReport #price_01_sum').text(data.price_sum.toLocaleString('ko-KR'));
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function getGoogleReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/google/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#googleReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#googleReport #impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#googleReport #clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#googleReport #click_ratio_sum').text(data.click_ratio_sum);
            $('#googleReport #spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#googleReport #unique_total_sum').text(data.unique_total_sum);
            $('#googleReport #unique_one_price_sum').text(data.unique_one_price_sum.toLocaleString('ko-KR'));
            $('#googleReport #conversion_ratio_sum').text(data.conversion_ratio_sum);
            $('#googleReport #profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#googleReport #per_sum').text(data.per_sum);
            $('#googleReport #price_01_sum').text(data.price_sum.toLocaleString('ko-KR'));
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>