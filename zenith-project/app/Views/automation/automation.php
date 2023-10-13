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
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special" type="button" data-bs-toggle="modal" data-bs-target="#automationModal">작성하기</button>
            </div>
        </form>
    </div>

    <div class="section">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="automation-table">
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="automationModal" tabindex="-1" aria-labelledby="automationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="regi-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="step">
                        <ol id="myTab" role="tablist">
                            <li class="tab-link active" role="presentation" type="button" id="schedule-tab"  data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="true">
                                <strong>일정</strong>
                                
                                <p id="scheduleText"></p>
                            </li>
                            <li class="tab-link" role="presentation" id="target-tab" data-bs-toggle="tab" data-bs-target="#target" type="button" role="tab" aria-controls="target" aria-selected="false">
                                <strong>대상</strong>
                             
                                <p>광고주<br>
                                페이스북<br>
                                [전국]상상의원_광고주랜딩*
                                ...</p>
                            </li>
                            <li class="tab-link" role="presentation" id="condition-tab" data-bs-toggle="tab" data-bs-target="#condition" type="button" role="tab" aria-controls="condition" aria-selected="false">
                                <strong>조건</strong>
                                <p>
                                    지출액 - 100,000원 초과<br>
                                    AND<br>
                                    유효DB - 100건 이하

                                </p>
                            </li>
                            <li class="tab-link" role="presentation" id="preactice-tab" data-bs-toggle="tab" data-bs-target="#preactice" type="button" role="tab" aria-controls="preactice" aria-selected="false">
                                <strong>실행</strong>
                                <p>* 캠페인 - 페이스북<br>
                                노안라식_180509<br>
                                상태 OFF<br>
                                * 캠페인 - 구글<br>
                                밝은성모안과(지원자, 응답) -<br>
                                전국 #2000_001 *40000 &fhr<br>
                                예산 50,000원</p>
                                <p>* 캠페인 - 페이스북<br>
                                노안라식_180509<br>
                                상태 OFF<br>
                                * 캠페인 - 구글<br>
                                밝은성모안과(지원자, 응답) -<br>
                                전국 #2000_001 *40000 &fhr<br>
                                예산 50,000원</p>
                                <p>* 캠페인 - 페이스북<br>
                                노안라식_180509<br>
                                상태 OFF<br>
                                * 캠페인 - 구글<br>
                                밝은성모안과(지원자, 응답) -<br>
                                전국 #2000_001 *40000 &fhr<br>
                                예산 50,000원</p>
                            </li>
                            <li class="tab-link" role="presentation" id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                <strong>상세정보</strong>
                            </li>
                        </ol>
                    </div>
                    <div class="detail-wrap">
                        <div class="detail" id="schedule" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0"> 
                            <table class="table tbl-side" id="scheduleTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">다음 시간마다 규칙적으로 실행</th>
                                    <td>
                                        <input type="text" name="type_value" class="form-control short">
                                        <select name="exec_type" class="form-select short" id="execType">
                                            <option value="minute">분</option>
                                            <option value="hour">시간</option>
                                            <option value="day">일</option>
                                            <option value="week">주</option>
                                            <option value="month">월</option>
                                        </select>
                                        <p></p>
                                    </td>
                                </tr>
                                <tr id="weekdayRow">
                                    <th scope="row">요일</th>
                                    <td>
                                        <div class="week-radio">
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="2" id="day01">
                                                <label for="day01">월</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="3" id="day02">
                                                <label for="day02">화</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="4" id="day03">
                                                <label for="day03">수</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="5" id="day04">
                                                <label for="day04">목</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="6" id="day05">
                                                <label for="day05">금</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="7" id="day06">
                                                <label for="day06">토</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="1" id="day07">
                                                <label for="day07">일</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="nextDateRow">
                                    <th scope="row">다음 날짜에</th>
                                    <td>
                                        <select name="month_type" class="form-select" id="monthType">
                                            <option value="" selected>선택</option>
                                            <option value="start_day">매달 첫번째 날</option>
                                            <option value="end_day">매달 마지막 날</option>
                                            <option value="first">처음</option>
                                            <option value="last">마지막</option>
                                            <option value="day">날짜</option>
                                        </select>
                                        <select name="month_day" class="form-select" id="monthDay">
                                            <option value="" selected>선택</option>
                                            <?php
                                            for ($i = 1; $i <= 31; $i++) {
                                                echo "<option value='$i'>".$i."일</option>";
                                            }
                                            ?>
                                        </select>
                                        <select name="month_week" class="form-select" id="monthWeek">
                                            <option value="" selected>선택</option>
                                            <option value="2">월</option>
                                            <option value="3">화</option>
                                            <option value="4">수</option>
                                            <option value="5">목</option>
                                            <option value="6">금</option>
                                            <option value="7">토</option>
                                            <option value="1">일</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="timeRow">
                                    <th scope="row">시간</th>
                                    <td>
                                        <?php
                                        $interval = new DateInterval('PT30M');
                                        $time = new DateTime('00:00');
                                        $end = new DateTime('24:00');
                                        ?>
                                        <select name="exec_time" id="execTime" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php 
                                            while ($time < $end) {
                                                $value = $time->format('H:i');
                                                echo "<option value='$value'>$value</option>";
                                                $time->add($interval);
                                            }
                                            
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">제외 시간</th>
                                    <td>
                                        <div class="form-flex">
                                        <select name="ignore_start_time" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php
                                            $time = new DateTime('00:00');
                                            while ($time < $end) {
                                                $value = $time->format('H:i');
                                                echo "<option value='$value'>$value</option>";
                                                $time->add($interval);
                                            }
                                            ?>
                                        </select>
                                            <span>~</span>
                                            <select name="ignore_end_time" class="form-select middle">
                                                <option value="">선택</option>
                                                <?php 
                                                $time = new DateTime('00:00');
                                                while ($time < $end) {
                                                    $value = $time->format('H:i');
                                                    echo "<option value='$value'>$value</option>";
                                                    $time->add($interval);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="detail" id="target" role="tabpanel"  aria-labelledby="target-tab" tabindex="1">
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
                        <div class="detail" id="condition" role="tabpanel" aria-labelledby="condition-tab" tabindex="2">
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
                        <div class="detail" id="detailed" role="tabpanel" aria-labelledby="detailed-tab" tabindex="4">
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
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

let data = {};
let dataTable;

setDate();
getList();

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    return data;
}

function getList(){
    dataTable = $('#automation-table').DataTable({
        "autoWidth": false,
        "order": [[2,'desc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "scrollX": true,
        "scrollY": 500,
        "scrollCollapse": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "ajax": {
            "url": "<?=base_url()?>/automation/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { 
                "data": "aa_subject",
                "width": "20%",
            },
            { 
                "data": "aa_description", 
                "width": "20%",
            },
            { 
                "data": "aa_mod_datetime", 
                "width": "20%",
                "render": function(data){
                    if(data != null){
                        data = data.substr(0, 16);
                    }else{
                        data = null;
                    }

                    return data;
                }
            },
            { 
                "data": "aar_exec_timestamp", 
                "width": "20%",
                "render": function(data){
                    if(data != null){
                        data = data.substr(0, 16);
                    }else{
                        data = null;
                    }

                    return data;
                }
            },
            { 
                "data": "aa_status", 
                "width": "20%",
                "render": function(data, type, row){
                    console.log();
                    checked = data == 1 ? 'checked' : '';
                    var status = '<div class="td-inner"><div class="ui-toggle"><input type="checkbox" name="status" id="status_' + row.aa_seq + '" ' + checked + ' value="'+row.aa_seq+'"><label for="status_' + row.aa_seq + '">사용</label></div><div class="more-action"><button type="button" class="btn-more" data-seq="' + row.aa_seq + '"><span>더보기</span></button><ul class="action-list z-1"><li><a href="#">복제하기</a></li><li><a href="#">제거하기</a></li></ul></div></div>';

                    return status;
                }
            },
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
    });
}

function setDate(){
    $('#sdate, #edate').daterangepicker({
        locale: {
                "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                "applyLabel": "확인",                    // 확인 버튼 텍스트
                "cancelLabel": "취소",                   // 취소 버튼 텍스트
                "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
        },
        alwaysShowCalendars: true,                        // 시간 노출 여부
        showDropdowns: true,                     // 년월 수동 설정 여부
        autoApply: true,                         // 확인/취소 버튼 사용여부
        maxDate: new Date(),
        autoUpdateInput: false,
        ranges: {
            '오늘': [moment(), moment()],
            '어제': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '지난 일주일': [moment().subtract(6, 'days'), moment()],
            '지난 한달': [moment().subtract(29, 'days'), moment()],
            '이번달': [moment().startOf('month'), moment().endOf('month')],
        }
    }, function(start, end, label) {
        // Lets update the fields manually this event fires on selection of range
        startDate = start.format('YYYY-MM-DD'); // selected start
        endDate = end.format('YYYY-MM-DD'); // selected end

        $checkinInput = $('#sdate');
        $checkoutInput = $('#edate');

        // Updating Fields with selected dates
        $checkinInput.val(startDate);
        $checkoutInput.val(endDate);

        // Setting the Selection of dates on calender on CHECKOUT FIELD (To get this it must be binded by Ids not Calss)
        var checkOutPicker = $checkoutInput.data('daterangepicker');
        checkOutPicker.setStartDate(startDate);
        checkOutPicker.setEndDate(endDate);

        // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
        var checkInPicker = $checkinInput.data('daterangepicker');
        checkInPicker.setStartDate($checkinInput.val(startDate));
        checkInPicker.setEndDate(endDate);
    
    });
}

function setAutomationStatus(data)
{
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/automation/set-status",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            if (data != true) {
                alert('오류가 발생하였습니다.');
            } 
        },
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

function chkSchedule()
{
    var selectedValue = $('#execType').val();
    $('#weekdayRow, #nextDateRow, #timeRow').show();

    if (selectedValue === "minute" || selectedValue === "hour") {
        $('#weekdayRow, #nextDateRow, #timeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
        $('#timeRow select').val('');
    } else if (selectedValue === "day") {
        $('#weekdayRow, #nextDateRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
    } else if (selectedValue === "week") {
        $('#nextDateRow').hide();
        $('#nextDateRow select').val('');
    } else if(selectedValue === "month"){

    }
}

function scheduleText()
{
    var type_value = $('input[name="type_value"]').val();
    var exec_type = $('#execType').val();
    var exec_week = $('input[name="exec_week"]:checked').siblings('label').text();
    var month_type = $('#monthType').val();
    var month_day = $('#monthDay').val();
    var month_week = $('#monthWeek').val();
    var exec_time = $('#execTime').val();
    var ignore_start_time = $('select[name="ignore_start_time"] option:selected').text();
    var ignore_end_time = $('select[name="ignore_end_time"] option:selected').text();

    let scheduleTextParts= [];
    if(type_value) {
        switch(exec_type){
            case "minute":
                scheduleTextParts.push("매 "+type_value+"분 마다");
                break;
            case "hour":
                scheduleTextParts.push("매 "+type_value+"시간 마다");
                break;
            case "day":
                dayTextPart_1 = type_value == 1 ? '매일' : '매 '+type_value+'일 마다 ';
                dayTextPart_2 = exec_time ? exec_time+'에' : '';
                scheduleTextParts.push(dayTextPart_1+dayTextPart_2);
                break;
            case "week":
                if(exec_week){
                    weekTextPart_1 = type_value == 1 ? '매주 ' : '매 '+type_value+'주 ';
                    weekTextPart_2 = exec_time ? exec_time+'에' : '';
                    scheduleTextParts.push(weekTextPart_1+exec_week+"요일 마다 "+weekTextPart_2);
                }
                break;
            case "month":
                if(month_type){
                    switch(month_type){
                        case "":
                            // 첫번째 날, 마지막 날 
                            scheduleTextParts.push("매월 첫번째 날, 마지막 날"+ exec_time +"에");
                            break;
                        default:
                            // 처음, 마지막 
                            let weekDayIndex= parseInt(month_day.replace('day',''))-1;   
                            scheduleTextParts.push("매월 처음, 마지막"+ dayText[weekDayIndex] +"요일 "+ exec_time +"에");  
                            break;
                    }
                }else{
                    // 2달 마다 3일째 23시 30분에
                    if(month_day && exec_time)
                        scheduleTextParts.push(type_value+"달 마다" + month_day+ " 일째"+ exec_time+ "에");  
                }
                break;
            default:
                break;
        }
    }
    $("#scheduleText").html(scheduleTextParts.join(", "));
}
$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('#automation-table').on('click', '.btn-more', function () {
    var seq = $(this).data('seq');
    var currentActionList = $(this).closest('.more-action').find('.action-list');
    $('.action-list').not(currentActionList).fadeOut(0);
    currentActionList.fadeToggle();
});

$('body').on('click', '.tab-link', function() {
    $('.tab-link').removeClass('active');
    $(this).addClass('active');
});

$('body').on('change', '.ui-toggle input[name=status]', function() {
    var isChecked = $(this).is(':checked');
    var seq = $(this).val();
    var status = isChecked ? 1 : 0;
    
    data = {
        'seq' : seq,
        'status' : status
    };
    setAutomationStatus(data);
});

if($('#home-tab').attr('aria-selected')){
    $('#home').addClass('active');
}

$('#automationModal').on('show.bs.modal', function(e) {
    chkSchedule();
})
.on('hidden.bs.modal', function(e) { 

});

//등록
$('body').on('change', '#execType', function() {
    chkSchedule();
});

$('body').on('change', '#scheduleTable input, #scheduleTable select', function() {
    scheduleText();
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>