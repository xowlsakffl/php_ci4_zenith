<?php
$uri = current_url(true)->getPath();
?>
<div class="left-side">
    <button type="button" class="btn-menu">메뉴보기</button>
    <h1 class="logo"><a href="/"><img src="/img/logo.png" alt="CHAIN 열혈광고"></a></h1>
    <div class="nav-wrap">
        <ul class="nav flex-column">
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#advertisements" aria-expanded="false"><i class="bi bi-graph-up-arrow"></i>통합 광고관리</button>
                <div class="collapse" id="advertisements">
                    <ul class="btn-toggle-nav">
                        <li><a href="/advertisements" class="<?php if($uri === '/advertisements'){ echo "active";}?>">통합 광고관리</a></li>
                        <li><a href="/automation" class="<?php if($uri === '/automation'){ echo "active";}?>">자동화 관리</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="/integrate" class="<?php if($uri === '/integrate'){ echo "active";}?>"><button><i class="bi bi-pie-chart-fill"></i>통합 DB관리</button></a>
            </li>
            <li>
                <a href="/integrate-user" class="<?php if($uri === '/integrate-user'){ echo "active";}?>"><button><i class="bi bi-pie-chart-fill"></i>사용자 DB관리</button></a>
            </li>
            <?php if (auth()->user()->inGroup('superadmin', 'admin')) {?>
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
            <?php }?>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#event" aria-expanded="false"><i class="bi bi-calendar-week"></i>이벤트</button>
                <div class="collapse" id="event">
                    <ul class="btn-toggle-nav">
                        <li><a href="/eventmanage/event" class="<?php if($uri === '/eventmanage/event'){ echo "active";}?>">이벤트 관리</a></li>
                        <li><a href="/eventmanage/advertiser" class="<?php if($uri === '/eventmanage/advertiser'){ echo "active";}?>">광고주 관리</a></li>
                        <li><a href="/eventmanage/media" class="<?php if($uri === '/eventmanage/media'){ echo "active";}?>">매체 관리</a></li>
                        <li><a href="/eventmanage/change" class="<?php if($uri === '/eventmanage/change'){ echo "active";}?>">전환 관리</a></li>
                        <li><a href="/eventmanage/blacklist" class="<?php if($uri === '/eventmanage/blacklist'){ echo "active";}?>">블랙리스트 관리</a></li>
                        <li><a href="/eventmanage/excel" class="<?php if($uri === '/eventmanage/excel'){ echo "active";}?>">엑셀 업로드</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#user" aria-expanded="false"><i class="bi bi-people-fill"></i>회원 관리</button>
                <div class="collapse" id="user">
                    <ul class="btn-toggle-nav">
                        <li><a href="/company" class="<?php if($uri === '/company'){ echo "active";}?>">광고주/광고대행사 관리</a></li>
                        <?php if (auth()->user()->inGroup('superadmin', 'admin')) {?>
                        <li><a href="/user" class="<?php if($uri === '/user'){ echo "active";}?>">회원 관리</a></li>
                        <?php }?>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <div class="user-nav">
        <p><?php echo auth()->user()->username;?> 로그인 중</p>
        <div class="util-nav">
            <a href="/mypage">마이페이지</a>
            <a href="/logout">로그아웃</a>
        </div>
    </div>
    <div class="btn-top">
        <a href="#"><i class="bi bi-arrow-up-square-fill"></i><span>TOP</span></a>
    </div>
</div>
