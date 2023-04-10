<?=$this->extend('templates/front_example.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 사이트 맵
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap siteMap-container">
    <div class="title-area">
        <h2 class="page-title">사이트 맵</h2>       
    </div>
    
    <div class="sitemap-wrap">
        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 광고관리</h3>
            <div class="row">     
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/facebook">페이스북</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/kakao">카카오 모먼트</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/google">구글 에드워즈</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/etc">기타  광고관리</a></button>                    
                    </div>
                </div>
            </div>
        </div>
  
        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 통합 DB관리</h3>
            <div class="row">     
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/db">통합 DB관리</a></button>                    
                    </div>
                </div>
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 회계관리</h3>
            <div class="row">    
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/unpaid">미수금 관리</a></button>                    
                    </div>
                </div> 
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/withdraw">출금요청</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/withdraw_list">업체목록</a></button>                    
                    </div>
                </div>           
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/tax">세금계산서 요청</a></button>                    
                    </div>
                </div>   
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/tax_list">업체목록<br>(세금 계산서)</a></button>                    
                    </div>
                </div>           
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i>인사관리</h3>
            <div class="row">     
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/manage">시간차 관리</a></button>                    
                    </div>
                </div>
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 이벤트</h3>
            <div class="row">   
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/event_manage">이벤트 관리</a></button>                    
                    </div>
                </div>  
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/client_manage">광고주 관리</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/media_manage">매체 관리</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/change_manage">전환 관리</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/exel">엑셀 업로드</a></button>                    
                    </div>
                </div>
            </div>
        </div>    

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i>백엔드 작업</h3>
            <div class="row">   
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/user/belong/1">사용자 소속 변경</a></button>                    
                    </div>
                </div>    
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/board/list">게시판</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/company/list">광고주,대행사 관리</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/guest">회원가입 승인 대기</a></button>                    
                    </div>
                </div>                     
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i>가이드 페이지</h3>
            <div class="row">     
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/modal">모달 전체</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/index">main 페이지</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/sub">sub 페이지</a></button>                    
                    </div>
                </div>
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i>account</h3>
            <div class="row">   
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/register_agree">이용 약관 동의</a></button>                    
                    </div>
                </div>    
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/login">로그인 A타입</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/login_02">로그인 B타입</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/register">회원가입 A타입</a></button>                    
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/signUp">회원가입 B타입</a></button>                    
                    </div>
                </div>            
            </div>
        </div>

        <div class="section client-list advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i>기타 페이지</h3>
            <div class="row">   
                <div class="col">
                    <div class="inner">
                        <button type="button"><a href="/example/siteMap">사이트 맵</a></button>                    
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>
