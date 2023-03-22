<?php 
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
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
                        <li><a href="/advertisements/facebook" class="<?php if($path == '/advertisements/facebook'){ echo "active";} ?>">페이스북</a></li>
                        <li><a href="#" class="<?php if($path == '/advertisements/kakao'){ echo "active";} ?>">카카오 모먼트</a></li>
                        <li><a href="#" class="<?php if($path == '/advertisements/google'){ echo "active";} ?>">구글 애드워즈</a></li>
                        <li><a href="#" class="<?php if($path == '/advertisements/etc'){ echo "active";} ?>">기타</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button><i class="bi bi-people-fill"></i>통합 DB 관리</button>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#accounting" aria-expanded="false"><i class="bi bi-cash-coin"></i>회계 관리</button>
                <div class="collapse" id="accounting">
                    <ul class="btn-toggle-nav">
                        <li><a href="#">출금요청</a></li>
                        <li><a href="#">세금계산서 요청</a></li>
                        <li><a href="#">미수금 관리</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button><i class="bi bi-person-vcard-fill"></i>인사 관리</button>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#event" aria-expanded="false"><i class="bi bi-calendar-week"></i>이벤트</button>
                <div class="collapse" id="event">
                    <ul class="btn-toggle-nav">
                        <li><a href="#">이벤트 관리</a></li>
                        <li><a href="/company/list" class="<?php if($path == '/company/list'){ echo "active";} ?>">광고주 관리</a></li>
                        <li><a href="#">매체 관리</a></li>
                        <li><a href="#">전환 관리</a></li>
                        <li><a href="#">엑셀 업로드</a></li>
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

<script>
    
</script>