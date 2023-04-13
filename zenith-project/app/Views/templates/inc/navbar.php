<?php
$uri = current_url(true)->getPath();
?>
<div class="left-side">
    <button type="button" class="btn-menu">메뉴보기</button>
    <h1 class="logo"><a href="/"><img src="/img/logo.png" alt="CHAIN 열혈광고"></a></h1>
    <div class="nav-wrap">
        <ul class="nav flex-column">
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#ad" aria-expanded="false"><i class="bi bi-graph-up-arrow"></i>광고 관리</button>
                <div class="collapse" id="ad">
                    <ul class="btn-toggle-nav">                     
                        <li><a href="/advertisements/facebook" class="<?php if($uri === '/advertisements/facebook'){ echo "active";}?>">페이스북</a></li>                      
                        <li><a href="/advertisements/kakao" class="<?php if($uri === '/advertisements/kakao'){ echo "active";}?>">카카오 모먼트</a></li>
                        <li><a href="/advertisements/google" class="<?php if($uri === '/advertisements/google'){ echo "active";}?>">구글 애드워즈</a></li>
                        <li><a href="/advertisements/etc" class="<?php if($uri === '/advertisements/etc'){ echo "active";}?>">기타</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="/integrate/management" class="<?php if($uri === '/integrate/management'){ echo "active";}?>"><button><i class="bi bi-people-fill"></i>통합 DB 관리</button></a>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#accounting" aria-expanded="false"><i class="bi bi-cash-coin"></i>회계 관리</button>
                <div class="collapse" id="accounting">
                    <ul class="btn-toggle-nav">
                        <li><a href="/accounting/withdraw" class="<?php if($uri === '/accounting/withdraw' || $uri === '/accounting/withdrawList'){ echo "active";}?>">출금요청</a></li>                      
                        <li><a href="/accounting/tax" class="<?php if($uri === '/accounting/tax' || $uri === '/accounting/taxList'){ echo "active";}?>">세금계산서 요청</a></li>
                        <li><a href="/accounting/unpaid" class="<?php if($uri === '/accounting/unpaid'){ echo "active";}?>">미수금 관리</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="/humanresource/management" class="<?php if($uri === '/humanresource/management'){ echo "active";}?>"><button><i class="bi bi-person-vcard-fill"></i>인사 관리</button></a>                
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#event" aria-expanded="false"><i class="bi bi-calendar-week"></i>이벤트</button>
                <div class="collapse" id="event">
                    <ul class="btn-toggle-nav">
                        <li><a href="/eventManage/event">이벤트 관리</a></li>
                        <li><a href="/eventManage/advertiser" class="">광고주 관리</a></li>
                        <li><a href="/eventManage/media">매체 관리</a></li>
                        <li><a href="/eventManage/change">전환 관리</a></li>
                        <li><a href="/eventManage/exel">엑셀 업로드</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <div class="util-nav">
        <a href="#">마이페이지</a>
        <a href="/logout">로그아웃</a>
    </div>
</div>