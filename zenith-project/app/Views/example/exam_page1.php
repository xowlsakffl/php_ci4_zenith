<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>페이스북 광고관리_연습</title>
</head>
<body>
<div class="wrap d-flex">
    <div class="left-side">
        <h1 class="logo"><a href="/"><img src="/img/logo.png" alt="CHAIN 열혈광고"></a></h1>
        <ul class="nav flex-column">
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#ad" aria-expanded="true">광고 관리</button>
                <div class="collapse show" id="ad">
                    <ul class="btn-toggle-nav">
                        <li><a href="#" class="active">페이스북</a></li>
                        <li><a href="#">카카오 모먼트</a></li>
                        <li><a href="#">구글 애드워즈</a></li>
                        <li><a href="#">기타</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button>통합 DB 관리</button>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#accounting" aria-expanded="false">회계 관리</button>
                <div class="collapse" id="accounting">
                    <ul class="btn-toggle-nav">
                        <li><a href="#">출금요청</a></li>
                        <li><a href="#">세금계산서 요청</a></li>
                        <li><a href="#">미수금 관리</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button>인사 관리</button>
            </li>
            <li>
                <button data-bs-toggle="collapse" data-bs-target="#event" aria-expanded="false">이벤트</button>
                <div class="collapse" id="event">
                    <ul class="btn-toggle-nav">
                        <li><a href="#">이벤트 관리</a></li>
                        <li><a href="#">광고주 관리</a></li>
                        <li><a href="#">매체 관리</a></li>
                        <li><a href="#">전환 관리</a></li>
                        <li><a href="#">엑셀 업로드</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        <div class="util-nav">
            <a href="#">마이페이지</a>
            <a href="#">로그아웃</a>
        </div>
    </div>
    <main class="contents-wrap">
        <form class="ad-management text-center">
            <div class="row">
                <div class="title">
                    <h1>페이스북 광고관리</h1>
                    <p>숫자 중심의 퍼포먼스 마케팅에 집중! 사람들이 어떤 콘텐츠에 반응하는가? 비용 대비 얼만큼의 성과가 있는가?</p>
                </div>              
                <div class="type">
                    <div class="summary">
                    <div class="search d-flex"> 
                        <input class="form-control me-2" type="search" placeholder="검색어를 입력하세요" aria-label="Search">
                        <button class="btn btn-primary" type="submit">조회</button>
                    </div>
                    </div>
                    <div class="detail">
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
            </div>
            <article class="row menu account">
                <div class="title">
                    <i class="check-circle"></i><strong>비지니스 계정</strong>
                    <span class="line"></span>
                </div>
                <div class="btn-tab d-flex">
                    <a href="#" class="btn-management active btn-primary"><span>열혈 패밀리</span></a>
                    <a href="#" class="btn-management btn-primary"><span>케어랩스5</span></a>
                    <a href="#" class="btn-management btn-primary"><span>케어랩스7케어랩스7케어랩스</span></a>
                </div>
            </article>

            <article class="row menu advertiser">
                <div class="title">
                    <i class="check-circle"></i><strong>광고주</strong>
                    <span class="line"></span>
                </div>
                <div class="btn-tab d-flex">
                    <a href="#" class="btn-management active btn-primary"><span>[대전]상상의원_가나다라마바</span></a>
                    <a href="#" class="btn-management btn-primary"><span>플란치과_임플</span></a>
                    <a href="#" class="btn-management btn-primary"><span>비결뷰티센터</span></a>
                </div>
            </article>
            
            <section class="row">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-selected="true">캠페인</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-selected="false">광고세트</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-selected="false">광고</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="btn-tab d-flex">
                        <a href="#" class="btn btn-outline-primary"><span>수동 업데이트</span></a>
                        <a href="#" class="btn btn-outline-primary"><span>데이터 비교</span></a>
                        <a href="#" class="btn btn-outline-primary"><span>메모확인</span></a>
                    </div>
                    <div class="tab-pane active" id="home"  aria-labelledby="home-tab">캠페인</div>
                    <div class="tab-pane" id="profile"  aria-labelledby="profile-tab">광고세트</div>
                    <div class="tab-pane" id="messages"  aria-labelledby="messages-tab">광고</div>
                </div>
            </section>
            <!-- <div class="row">
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
                    <div class="detail row">
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
            </div> -->
        </form>
    </main>
</div>
<script>
     var firstTabEl = document.querySelector('#myTab li:last-child button')
  var firstTab = new bootstrap.Tab(firstTabEl)

  firstTab.show()
</script>
</body>
</html>
<?=$this->endSection();?>
