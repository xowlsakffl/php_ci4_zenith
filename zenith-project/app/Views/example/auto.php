<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore/js/dataTables.stateRestore.min.js"></script>
<script src="/static/js/jszip.min.js"></script>
<script src="/static/js/pdfmake/pdfmake.min.js"></script>
<script src="/static/js/pdfmake/vfs_fonts.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">자동화 목록</h2>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
                <button class="btn-special" type="button" data-bs-toggle="modal" data-bs-target="#regiModal">작성하기</button>
            </div>
        </form>
    </div>

    <div class="section">
        <div class="row">
            <p class="num-line">페이지당 줄수 <span>25개</span></p>

            <table class="table table-striped tbl-dark">
                <colgroup>
                    <col style="width:20%;">
                    <col style="width:20%;">
                    <col style="width:20%;">
                    <col style="width:20%">
                    <col>
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">이름</th>
                        <th scope="col">작성자</th>
                        <th scope="col">업데이트</th>
                        <th scope="col">마지막 실행</th>
                        <th scope="col">사용</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">카카오 DB초과 OFF</td>
                        <td class="text-center">배익준</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">
                            <div class="td-inner">
                                <div class="ui-toggle">
                                    <input type="checkbox" name="use01" id="use01">
                                    <label for="use01">사용</label>
                                </div>
                                <div class="more-action">
                                    <button type="button" class="btn-more"><span>더보기</span></button>
                                    <ul class="action-list">
                                        <li><a href="#">복제하기</a></li>
                                        <li><a href="#">제거하기</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">카카오 DB초과 OFF</td>
                        <td class="text-center">배익준</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">
                            <div class="td-inner">
                                <div class="ui-toggle">
                                    <input type="checkbox" name="use01" id="use01">
                                    <label for="use01">사용</label>
                                </div>
                                <div class="more-action">
                                    <button type="button" class="btn-more"><span>더보기</span></button>
                                    <ul class="action-list">
                                        <li><a href="#">복제하기</a></li>
                                        <li><a href="#">제거하기</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">카카오 DB초과 OFF</td>
                        <td class="text-center">배익준</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">
                            <div class="td-inner">
                                <div class="ui-toggle">
                                    <input type="checkbox" name="use01" id="use01">
                                    <label for="use01">사용</label>
                                </div>
                                <div class="more-action">
                                    <button type="button" class="btn-more"><span>더보기</span></button>
                                    <ul class="action-list">
                                        <li><a href="#">복제하기</a></li>
                                        <li><a href="#">제거하기</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">카카오 DB초과 OFF</td>
                        <td class="text-center">배익준</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">
                            <div class="td-inner">
                                <div class="ui-toggle">
                                    <input type="checkbox" name="use01" id="use01">
                                    <label for="use01">사용</label>
                                </div>
                                <div class="more-action">
                                    <button type="button" class="btn-more"><span>더보기</span></button>
                                    <ul class="action-list">
                                        <li><a href="#">복제하기</a></li>
                                        <li><a href="#">제거하기</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">카카오 DB초과 OFF</td>
                        <td class="text-center">배익준</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">2023-09-27 12:52</td>
                        <td class="text-center">
                            <div class="td-inner">
                                <div class="ui-toggle">
                                    <input type="checkbox" name="use01" id="use01">
                                    <label for="use01">사용</label>
                                </div>
                                <div class="more-action">
                                    <button type="button" class="btn-more"><span>더보기</span></button>
                                    <ul class="action-list">
                                        <li><a href="#">복제하기</a></li>
                                        <li><a href="#">제거하기</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="paging">
                <a href="#" class="btn-prev">이전</a>
                <a href="#" class="current">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <span>...</span>
                <a href="#">291</a>
                <a href="#" class="btn-next">다음</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="regiModal" tabindex="-1" aria-labelledby="memoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="regi-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="step">
                        <ol class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="tab-link" role="presentation" type="button" id="home-tab"  data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="false">
                                <strong>일정</strong>
                                
                                <p>매 2시간 마다<br>
                                매일 00시 30분[7일 마다 7시 정각에]<br>
                                매주 일요일 03시 정각에<br>
                                매월 [첫번째 날, 마지막 날] 09시 30분에
                                ...</p>
                            </li>
                            <li class="tab-link" role="presentation" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                <strong>대상</strong>
                             
                                <p>광고주<br>
                                페이스북<br>
                                [전국]상상의원_광고주랜딩*
                                ...</p>
                            </li>
                            <li class="tab-link" role="presentation" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                <strong>조건</strong>
                                <p>지출액 - 100,000원 초과<br>
                                AND<br>
                                유효DB - 100건 이하</p>
                            </li>
                            <li class="tab-link active" role="presentation" id="preactice-tab" data-bs-toggle="tab" data-bs-target="#preactice" type="button" role="tab" aria-controls="messages" aria-selected="true">
                                <strong>실행</strong>
                                <p>* 캠페인 - 페이스북<br>
                                노안라식_180509<br>
                                상태 OFF<br>
                                * 캠페인 - 구글<br>
                                밝은성모안과(지원자, 응답) -<br>
                                전국 #2000_001 *40000 &fhr<br>
                                예산 50,000원</p>
                            </li>
                            <li class="tab-link" role="presentation" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                <strong>상세정보</strong>
                            </li>
                        </ol>
                    </div>
                    <div class="detail-wrap">
                        <div class="detail" id="home" role="tabpanel" aria-labelledby="home-tab" tabindex="0"> 
                            <table class="table tbl-side">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">다음 시간마다 규칙적으로 실행</th>
                                    <td>
                                        <input type="text" class="form-control short">
                                        <select name="" class="form-select short">
                                            <option value="">분</option>
                                            <option value="">시간</option>
                                            <option value="">일</option>
                                            <option value="">주</option>
                                            <option value="">월</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">요일</th>
                                    <td>
                                        <div class="week-radio">
                                            <div class="day">
                                                <input type="radio" name="day" id="day01">
                                                <label for="day01">월</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day02">
                                                <label for="day02">화</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day03">
                                                <label for="day03">수</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day04">
                                                <label for="day04">목</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day05">
                                                <label for="day05">금</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day06">
                                                <label for="day06">토</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="day" id="day07">
                                                <label for="day07">일</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">다음 날짜에</th>
                                    <td>
                                        <select name="" class="form-select">
                                            <option value="">매달 첫번째 날</option>
                                            <option value="">매달 마지막 날</option>
                                            <option value="">처음</option>
                                            <option value="">마지막</option>
                                            <option value="">날짜</option>
                                        </select>
                                        <select name="" class="form-select">
                                            <option value="">1일</option>
                                            <option value="">2일</option>
                                            <option value="">3일</option>
                                            <option value="">4일</option>
                                            <option value="">5일</option>
                                        </select>
                                        <select name="" class="form-select">
                                            <option value="">월</option>
                                            <option value="">화</option>
                                            <option value="">수</option>
                                            <option value="">목</option>
                                            <option value="">금</option>
                                            <option value="">토</option>
                                            <option value="">일</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">시간</th>
                                    <td>
                                        <select name="" class="form-select middle">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">제외 시간</th>
                                    <td>
                                        <div class="form-flex">
                                            <select name="" class="form-select middle">
                                                <option value="">00:00</option>
                                                <option value="">00:30</option>
                                                <option value="">01:00</option>
                                                <option value="">01:30</option>
                                                <option value="">02:00</option>
                                            </select>
                                            <span>~</span>
                                            <select name="" class="form-select middle">
                                                <option value="">00:00</option>
                                                <option value="">00:30</option>
                                                <option value="">01:00</option>
                                                <option value="">01:30</option>
                                                <option value="">02:00</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="detail" id="profile" role="tabpanel"  aria-labelledby="profile-tab" tabindex="1">
                            <ul class="tab">
                                <li class="active"><a href="#">광고주</a></li>
                                <li><a href="#">캠페인</a></li>
                                <li><a href="#">광고그룹</a></li>
                                <li><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <input type="text" placeholder="검색어를 입력하세요">
                            </div>
                            <table class="table tbl-header">
                                <colgroup>
                                    <col style="width:24%">
                                    <col style="width:28%">
                                    <col>
                                    <col style="width:15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">매체</th>
                                        <th scope="col">광고주 ID</th>
                                        <th scope="col">광고주명</th>
                                        <th scope="col">상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td><b class="em">활성화</b></td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td>중지</td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td><b class="em">활성화</b></td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td>중지</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="2">
                            <table class="table tbl-header">
                                <colgroup>
                                    <col>
                                    <col style="width:45%">
                                    <col style="width:5%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                        <th scope="col">구분</th>
                                        <th scope="col"><button type="button" class="btn-add">추가</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <select name="" class="form-select">
                                                    <option value="">조건 항목</option>
                                                    <option value="">상태</option>
                                                    <option value="">예산</option>
                                                    <option value="">DB단가</option>
                                                    <option value="">유효DB</option>
                                                    <option value="">지출액</option>
                                                    <option value="">수익</option>
                                                    <option value="">수익률</option>
                                                    <option value="">매출액</option>
                                                    <option value="">노출수</option>
                                                    <option value="">링크클릭</option>
                                                    <option value="">CPC</option>
                                                    <option value="">CTR</option>
                                                    <option value="">DB전환률</option>
                                                </select>
                                                <select name="" class="form-select">
                                                    <option value="">상태</option>
                                                    <option value="">ON</option>
                                                    <option value="">OFF</option>
                                                </select>
                                                <input type="text" class="form-control">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-flex">
                                            <select name="" class="form-select">
                                                <option value="">일치여부</option>
                                                <option value="">초과</option>
                                                <option value="">보다 크거나 같음</option>
                                                <option value="">미만</option>
                                                <option value="">보다 작거나 같음</option>
                                                <option value="">같음</option>
                                                <option value="">같지않음</option>
                                            </select>
                                            <select name="" class="form-select no-flex">
                                                <option value="">AND / OR</option>
                                                <option value="">AND</option>
                                                <option value="">OR</option>
                                            </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <select name="" class="form-select">
                                                    <option value="">조건 항목</option>
                                                    <option value="">상태</option>
                                                    <option value="">예산</option>
                                                    <option value="">DB단가</option>
                                                    <option value="">유효DB</option>
                                                    <option value="">지출액</option>
                                                    <option value="">수익</option>
                                                    <option value="">수익률</option>
                                                    <option value="">매출액</option>
                                                    <option value="">노출수</option>
                                                    <option value="">링크클릭</option>
                                                    <option value="">CPC</option>
                                                    <option value="">CTR</option>
                                                    <option value="">DB전환률</option>
                                                </select>
                                                <select name="" class="form-select">
                                                    <option value="">상태</option>
                                                    <option value="">ON</option>
                                                    <option value="">OFF</option>
                                                </select>
                                                <input type="text" class="form-control">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-flex">
                                            <select name="" class="form-select">
                                                <option value="">일치여부</option>
                                                <option value="">초과</option>
                                                <option value="">보다 크거나 같음</option>
                                                <option value="">미만</option>
                                                <option value="">보다 작거나 같음</option>
                                                <option value="">같음</option>
                                                <option value="">같지않음</option>
                                            </select>
                                            <select name="" class="form-select no-flex">
                                                <option value="">AND / OR</option>
                                                <option value="">AND</option>
                                                <option value="">OR</option>
                                            </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="preactice" role="tabpanel" aria-labelledby="preactice-tab" tabindex="3">
                            <ul class="tab">
                                <li class="active"><a href="#">캠페인</a></li>
                                <li><a href="#">광고그룹</a></li>
                                <li><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <input type="text" placeholder="검색어를 입력하세요">
                            </div>
                            <table class="table tbl-header">
                                <colgroup>
                                    <col style="width:24%">
                                    <col style="width:28%">
                                    <col>
                                    <col style="width:15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">매체</th>
                                        <th scope="col">광고주 ID</th>
                                        <th scope="col">광고주명</th>
                                        <th scope="col">상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td><b class="em">활성화</b></td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td>중지</td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td><b class="em">활성화</b></td>
                                    </tr>
                                    <tr>
                                        <td>페이스북</td>
                                        <td>5897268653698510</td>
                                        <td>[전국]상상의원_광고주랜딩*</td>
                                        <td>중지</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table tbl-header">
                                <colgroup>
                                    <col>
                                    <col style="width:5%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                        <th scope="col"><button type="button" class="btn-add">추가</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div class="form-flex">
                                                <select name="" class="form-select">
                                                    <option value="">실행항목</option>
                                                    <option value="">상태</option>
                                                    <option value="">예산</option>
                                                </select>
                                                <select name="" class="form-select">
                                                    <option value="">상태</option>
                                                    <option value="">ON</option>
                                                    <option value="">OFF</option>
                                                </select>
                                                <input type="text" class="form-control">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="detail" role="tabpanel" aria-labelledby="detail-tab" tabindex="4">
                            <table class="table tbl-side">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">이름*</th>
                                    <td>
                                        <input type="text" class="form-control bg">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">설명</th>
                                    <td>
                                        <textarea class="form-control"></textarea>
                                    </td>
                                </tr>
                            </table>
                            <duv class="btn-area">
                                <button type="button" class="btn-special">저장</button>
                            </duv>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="memoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="memoModalLabel"><i class="ico-log"></i> 감사 로그</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table tbl-dark">
                    <colgroup>
                        <col>
                        <col style="width:22%;">
                        <col style="width:18%;">
                        <col style="width:22%;">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">이름</th>
                            <th scope="col">작성자</th>
                            <th scope="col">업데이트</th>
                            <th scope="col">사용</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b class="em">성공</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                        <tr>
                            <td class="text-center">2023-09-27 12:52 <span class="num">(20763687216)</span></td>
                            <td class="text-center">커밋 후 작업</td>
                            <td class="text-center"><b>실패</b></td>
                            <td class="text-center">더보기</td>
                        </tr>
                    </tbody>
                </table>

                <div class="paging">
                    <a href="#" class="btn-prev">이전</a>
                    <a href="#" class="current">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <a href="#">5</a>
                    <span>...</span>
                    <a href="#">291</a>
                    <a href="#" class="btn-next">다음</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('body').on('click', '.tab-link', function() {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
    });

   if($('#preactice-tab').attr('aria-selected')){
        $('#preactice').addClass('active');
    }
</script>
<?=$this->endSection();?>


<?=$this->section('footer');?>
<?=$this->endSection();?>