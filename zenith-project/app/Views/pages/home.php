<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 홈
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script>
</script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<?php if($password_check){?>
<!--비밀번호 변경 팝업-->
<div class="modal fade show" id="passwordChangeModal" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog max-520">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordChangeModalLabel"> 비밀번호 변경 알림</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>회원님의 개인정보를 안전하게 보호하고, 개인정보 도용으로 인한 <br>피해를 예방하기 위해
                <b>90일 이상</b><span> 비밀번호를 변경하지 않은 경우 비밀번호 변경을 권장</span>하고 있습니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="password_extension_btn">2주 후에 알림</button>
                <button type="button" class="btn btn-danger"><a href="/mypage">비밀번호 변경</a></button>
            </div>
        </div>
    </div>
</div>
<?php };?>
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
            <a href="/advertisements" class="btn-more btn-primary"><span>더보기</span></a>
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
            <a href="/advertisements" class="btn-more"><span>더보기</span></a>
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
            <a href="/advertisements" class="btn-more btn-primary"><span>더보기</span></a>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
getReport();

function getReport(){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/home/report",
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $.each(data, function(key, value) {
                console.log(value)
                $('#'+ key + ' #profit_sum').text(value.profit_sum);
                $('#'+ key + ' #impressions_sum').text(value.impressions_sum);
                $('#'+ key + ' #clicks_sum').text(value.clicks_sum);
                $('#'+ key + ' #click_ratio_sum').text(value.click_ratio_sum);
                $('#'+ key + ' #spend_sum').text(value.spend_sum);
                $('#'+ key + ' #unique_total_sum').text(value.unique_total_sum);
                $('#'+ key + ' #unique_one_price_sum').text(value.unique_one_price_sum);
                $('#'+ key + ' #conversion_ratio_sum').text(value.conversion_ratio_sum);
                $('#'+ key + ' #profit_sum').text(value.profit_sum);
                $('#'+ key + ' #per_sum').text(value.per_sum);
                $('#'+ key + ' #price_01_sum').text(value.price_sum);
            });
        }
    });
}

$(document).ready(function () {
    $('#passwordChangeModal').modal('show');

    $('body').on('click', '#password_extension_btn', function() {
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/password-changed-at",
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    $('#passwordChangeModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    });
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>