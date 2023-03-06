<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>CHAIN 열혈광고</title>
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
                <a href="#" class="btn-more"><span>더보기</span></a>
            </div>
            <div class="row">
                <div class="type">
                    <div class="summary">
                        <strong><i class="google"></i>구글 애드워즈</strong>
                        <dl>
                            <dl class="percentage">
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
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?=$this->endSection();?>
