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
            <div class="type">
                <div class="summary">
                    <strong><i class="facebook"></i>페이스북</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd>46.51</dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd>953,458</dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd>34,456</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd>809</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd>1.09</dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd>1,234,123</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd>61</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd>45,234</dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd>7.34</dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd>23,456,900</dd>
                    </dl>
                </div>
            </div>
            <a href="#" class="btn-more btn-primary"><span>더보기</span></a>
        </div>
        <div class="row">
            <div class="type">
                <div class="summary">
                    <strong><i class="kakao"></i>카카오 모먼트</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd>46.51</dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd>953,458</dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd>34,456</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd>809</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd>1.09</dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd>1,234,123</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd>61</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd>45,234</dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd>7.34</dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd>23,456,900</dd>
                    </dl>
                </div>
            </div>
            <a href="#" class="btn-more"><span>더보기</span></a>
        </div>
        <div class="row">
            <div class="type">
                <div class="summary">
                    <strong><i class="google"></i>구글 애드워즈</strong>
                    <dl class="percentage">
                        <dt>수익률</dt>
                        <dd>46.51</dd>
                    </dl>
                    <dl>
                        <dt>수익</dt>
                        <dd>953,458</dd>
                    </dl>
                </div>
                <div class="ad-detail-info row">
                    <dl class="col">
                        <dt>노출수</dt>
                        <dd>34,456</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭수</dt>
                        <dd>809</dd>
                    </dl>
                    <dl class="col">
                        <dt>클릭율</dt>
                        <dd>1.09</dd>
                    </dl>
                    <dl class="col">
                        <dt>지출액</dt>
                        <dd>1,234,123</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB수</dt>
                        <dd>61</dd>
                    </dl>
                    <dl class="col">
                        <dt>DB당 단가</dt>
                        <dd>45,234</dd>
                    </dl>
                    <dl class="col">
                        <dt>전환율</dt>
                        <dd>7.34</dd>
                    </dl>
                    <dl class="col">
                        <dt>매출</dt>
                        <dd>23,456,900</dd>
                    </dl>
                </div>
            </div>
            <a href="#" class="btn-more btn-primary"><span>더보기</span></a>
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

function getReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#click_ratio_sum').text(data.click_ratio_sum);
            $('#spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#unique_total_sum').text(data.unique_total_sum);
            $('#unique_one_price_sum').text(data.unique_one_price_sum.toLocaleString('ko-KR'));
            $('#conversion_ratio_sum').text(data.conversion_ratio_sum);
            $('#profit_sum').text(data.profit_sum.toLocaleString('ko-KR'));
            $('#per_sum').text(data.per_sum);
            $('#price_01_sum').text(data.price_sum.toLocaleString('ko-KR'));
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