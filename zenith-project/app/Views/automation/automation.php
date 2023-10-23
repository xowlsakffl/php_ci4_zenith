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
<style>
    .ui-autocomplete{
        z-index: 10000000;
        max-height: 300px;
        overflow-y: auto; /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
</style>
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
                <button class="btn-special createBtn" type="button" data-bs-toggle="modal" data-bs-target="#automationModal">작성하기</button>
            </div>
        </form>
    </div>

    <div class="section">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="automation-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">제목</th>
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
                                <p id="targetText"></p>
                            </li>
                            <li class="tab-link" role="presentation" id="condition-tab" data-bs-toggle="tab" data-bs-target="#condition" type="button" role="tab" aria-controls="condition" aria-selected="false">
                                <strong>조건</strong>
                                <p id="text-condition-1">
                                    <span class="typeText"></span>
                                    <span class="typeValueText"></span>
                                    <span class="compareText"></span>
                                    <span class="operationText"></span>
                                </p>
                            </li>
                            <li class="tab-link" role="presentation" id="preactice-tab" data-bs-toggle="tab" data-bs-target="#preactice" type="button" role="tab" aria-controls="preactice" aria-selected="false">
                                <strong>실행</strong>
                            </li>
                            <li class="tab-link" role="presentation" id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                <strong>상세정보</strong>
                                <p id="detailText">
                                    <span id="subjectText"></span><br>
                                    <span id="descriptionText"></span>
                                </p>
                            </li>
                        </ol>
                    </div>
                    <div class="detail-wrap">
                        <div class="detail show active" id="schedule" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0"> 
                            <table class="table tbl-side" id="scheduleTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">다음 시간마다 규칙적으로 실행</th>
                                    <td>
                                        <input type="text" name="type_value" class="form-control short" oninput="onlyNumber(this);" maxlength="3" />
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

                                        ob_start();

                                        while ($time < $end) {
                                            $value = $time->format('H:i');
                                            echo "<option value='$value'>$value</option>";
                                            $time->add($interval);
                                        }

                                        $timeOptions = ob_get_clean();
                                        ?>
                                        <select name="exec_time" id="execTime" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php echo $timeOptions; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">제외 시간</th>
                                    <td>
                                        <div class="form-flex">
                                        <select name="ignore_start_time" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php echo $timeOptions; ?>
                                        </select>
                                            <span>~</span>
                                            <select name="ignore_end_time" class="form-select middle">
                                                <option value="">선택</option>
                                                <?php echo $timeOptions; ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="detail" id="target" role="tabpanel"  aria-labelledby="target-tab" tabindex="1">
                            <ul class="tab" id="targetTab">
                                <li class="active" data-tab="advertiser"><a href="#">광고주</a></li>
                                <li data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adset"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search w-100">
                                <form name="search-target-form" class="search d-flex justify-content-center w-100">
                                    <input type="text" placeholder="검색어를 입력하세요" id="showTargetAdv" class="w-100">
                                    <button class="btn-primary" id="search_target_btn" type="submit">조회</button>
                                </form>
                            </div>
                            <table class="table tbl-header w-100" id="targetTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:40%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col"  class="text-center">매체</th>
                                        <th scope="col"  class="text-center">분류</th>
                                        <th scope="col"  class="text-center">ID</th>
                                        <th scope="col"  class="text-center">제목</th>
                                        <th scope="col"  class="text-center">상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="targetSelectTable">
                                <input type="hidden" name="adv_info" id="advInfo">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:40%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">선택 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="condition" role="tabpanel" aria-labelledby="condition-tab" tabindex="2">
                            <table class="table tbl-header" id="conditionTable">
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
                                    <tr id="condition-1">
                                        <td>
                                            <div class="form-flex">
                                                <input type="text" name="order" placeholder="순서(1~)" class="form-control conditionOrder" oninput="onlyNumber(this);" maxlength="3">
                                                <select name="type" class="form-select conditionType">
                                                    <option value="">조건 항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                    <option value="dbcost">DB단가</option>
                                                    <option value="dbcount">유효DB</option>
                                                    <option value="cost">지출액</option>
                                                    <option value="margin">수익</option>
                                                    <option value="margin_rate">수익률</option>
                                                    <option value="sale">매출액</option>
                                                    <option value="impression">노출수</option>
                                                    <option value="click">링크클릭</option>
                                                    <option value="cpc">CPC</option>
                                                    <option value="ctr">CTR</option>
                                                    <option value="conversion">DB전환률</option>
                                                </select>
                                           
                                                <select name="type_value_status" class="form-select conditionTypeValueStatus" style="display: none;">
                                                    <option value="">상태값 선택</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-flex">
                                            <select name="compare" class="form-select conditionCompare">
                                                <option value="">일치여부</option>
                                                <option value="greater">초과</option>
                                                <option value="greater_equal">보다 크거나 같음</option>
                                                <option value="less">미만</option>
                                                <option value="less_equal">보다 작거나 같음</option>
                                                <option value="equal">같음</option>
                                                <option value="not_equal">같지않음</option>
                                            </select>
                                            <select name="operation" class="form-select no-flex conditionOperation">
                                                <option value="">AND / OR</option>
                                                <option value="and">AND</option>
                                                <option value="or">OR</option>
                                            </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="preactice" role="tabpanel" aria-labelledby="preactice-tab" tabindex="3">
                            <ul class="tab" id="execTab">
                                <li class="active" data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adset"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" value="1" id="targetConditionDisabled">
                                    <label class="form-check-label" for="targetConditionDisabled">
                                        대상, 조건 미적용
                                    </label>
                                </div>
                                <form name="search-exec-form" class="search d-flex justify-content-center w-100">
                                    <input type="text" placeholder="검색어를 입력하세요" id="showExecAdv" class="w-100">
                                    <button class="btn-primary" id="search_exec_btn" type="submit">조회</button>
                                </form>
                            </div>
                            <table class="table tbl-header w-100 mt-4" id="execTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:40%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">매체</th>
                                        <th scope="col">분류</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">제목</th>
                                        <th scope="col">상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="execConditionTable">
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <select name="exec_condition_type" class="form-select" id="execConditionType">
                                                    <option value="">실행항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                </select>
                                                <select name="exec_condition_value_status" class="form-select" style="display: none;" id="execConditionValueStatus">
                                                    <option value="">상태</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="exec_condition_value" class="form-control" id="execConditionValue">
                                                <button class="btn-primnary" id="execConditionBtn">적용</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="execSelectTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:25%">
                                    <col style="width:25%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="7"  class="text-center">선택 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="detailed" role="tabpanel" aria-labelledby="detailed-tab" tabindex="4">
                            <table class="table tbl-side" id="detailTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">제목*</th>
                                    <td>
                                        <input type="text" name="subject" class="form-control bg">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">설명</th>
                                    <td>
                                        <textarea name="description" class="form-control"></textarea>
                                    </td>
                                </tr>
                            </table>
                            <duv class="btn-area">
                                <button type="button" id="createAutomationBtn" class="btn-special">저장</button>
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
                            <th scope="col">제목</th>
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
                "data": "aa_nickname", 
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
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.aa_seq);
            $(row).addClass("updateBtn");
            $(row).attr("data-bs-toggle", "modal");
            $(row).attr("data-bs-target", "#automationModal");
        },
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
    } else if (selectedValue === "month"){
        $('#weekdayRow').hide();
        $('input[name=exec_week]').prop('checked', false);
    }
}

function chkScheduleMonthType()
{
    month_type = $('#monthType').val();
    $('#nextDateRow select').show();
    if(month_type == 'start_day' || month_type == 'end_day' ){
        $('#nextDateRow select[name=month_day], #nextDateRow select[name=month_week]').hide();
        $('#monthDay, #monthWeek').val('');
    }else if(month_type == 'first' || month_type == 'last'){
        $('#nextDateRow select[name=month_day]').hide();
        $('#monthDay').val('');
    }else if (month_type == 'day'){
        $('#nextDateRow select[name=month_week]').hide();
        $('#monthWeek').val('');
    }
}

//좌측 탭 일정 텍스트
function scheduleText()
{
    var type_value = $('input[name="type_value"]').val();
    var exec_type = $('#execType').val();
    var exec_week = $('input[name="exec_week"]:checked').siblings('label').text();
    var month_type = $('#monthType').val();
    var month_day = $('#monthDay').val();
    var month_week = $('#monthWeek option:selected').text();
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
                dayTextPart_1 = type_value == 1 ? '매일 ' : '매 '+type_value+'일 마다 ';
                dayTextPart_2 = exec_time ? exec_time+'에' : '';
                scheduleTextParts.push(dayTextPart_1+dayTextPart_2);
                break;
            case "week":
                weekTextPart_1 = type_value == 1 ? '매주 ' : '매 '+type_value+'주 ';
                weekTextPart_2 = exec_week ? exec_week+"요일 마다 " : '';
                weekTextPart_3 = exec_time ? exec_time+'에' : '';
                if(exec_week){
                    scheduleTextParts.push(weekTextPart_1+weekTextPart_2+weekTextPart_3);
                }
                break;
            case "month":
                monthTextPart_1 = type_value == 1 ? '매월 ' : type_value+'달 마다 ';
                monthTextPart_3 = '';
                switch (month_type) {
                    case 'start_day':
                        monthTextPart_2 = '첫번째 날 ';
                        break;
                    case 'end_day':
                        monthTextPart_2 = '마지막 날 ';
                        break;
                    case 'first':
                        monthTextPart_2 = '처음 ';                  
                        if(month_week && month_week != '선택'){                          
                            monthTextPart_3 = month_week ? month_week+"요일 마다 " : '';
                        }
                        break;
                    case 'last':
                        monthTextPart_2 = '마지막 ';
                        if(month_week && month_week != '선택'){                          
                            monthTextPart_3 = month_week ? month_week+"요일 마다 " : '';
                        }
                        break;
                    case 'day':
                        monthTextPart_2 = month_day ? month_day+'일째 ' : '';
                        break;
                    default:
                        monthTextPart_2 = '';
                        break;
                }
                monthTextPart_4 = exec_time ? exec_time+'에' : '';

                scheduleTextParts.push(monthTextPart_1+monthTextPart_2+monthTextPart_3+monthTextPart_4);
                break;
            default:
                break;
        }
    }
    $("#scheduleText").html(scheduleTextParts.join(", "));
}
//좌측 탭 대상 텍스트
function targetText(){
    var rowMedia = $('#targetSelectTable tbody tr').children('td').eq(0).text();
    var rowType = $('#targetSelectTable tbody tr').children('td').eq(1).text();
    var rowName = $('#targetSelectTable tbody tr').children('td').eq(3).text();
    $('#targetText').html(rowMedia+"<br>"+rowType+"<br>"+rowName);
}
//좌측 탭 조건 텍스트
function conditionText($this)
{
    var name = $this.attr('name');
    var trId = $this.closest('tr').attr('id');

    if(name == 'type'){
        value = $this.find('option:selected').text()+" - ";
        $("#text-"+trId+" .typeText").html(value);
    }
    
    if(name == 'type_value_status'){
        $("#text-"+trId+" .typeValueText").text('');
        value = $this.find('option:selected').text();
        $("#text-"+trId+" .typeValueText").html(value);
    }

    if(name == 'type_value'){
        $("#text-"+trId+" .typeValueText").text('');
        value = $this.val();
        $("#text-"+trId+" .typeValueText").html(value);
    }

    if(name == 'compare'){
        $("#text-"+trId+" .compareText").text('');
        value = $this.find('option:selected').text();
        $("#text-"+trId+" .compareText").html(value);
    }

    if(name == 'operation'){
        $("#text-"+trId+" .operationText").text('');
        value = $this.find('option:selected').text();
        $("#text-"+trId+" .operationText").html("<br>"+value);
    }
}

function addConditionRow(uniqueId){
    var row = `
        <tr id="${uniqueId}">
        <td><div class="form-flex"><input type="text" name="order" placeholder="순서(1~)"class="form-control conditionOrder" oninput="onlyNumber(this);" maxlength="3"><select name="type" class="form-select conditionType"><option value="">조건 항목</option><option value="status">상태</option><option value="budget">예산</option><option value="dbcost">DB단가</option><option value="dbcount">유효DB</option><option value="cost">지출액</option><option value="margin">수익</option><option value="margin_rate">수익률</option><option value="sale">매출액</option><option value="impression">노출수</option><option value="click">링크클릭</option><option value="cpc">CPC</option><option value="ctr">CTR</option><option value="conversion">DB전환률</option></select><select name="type_value_status" class="form-select conditionTypeValueStatus" ><option value="">상태값 선택</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="type_value" class="form-control"placeholder="조건값"></div></td><td colspan="2"><div class="form-flex"><select name="compare" class="form-select conditionCompare"><option value="">일치여부</option><option value="greater">초과</option><option value="greater_equal">보다 크거나 같음</option><option value="less">미만</option><option value="less_equal">보다 작거나 같음</option><option value="equal">같음</option><option value="not_equal">같지않음</option></select><select name="operation" class="form-select no-flex conditionOperation"><option value="">AND / OR</option><option value="and">AND</option><option value="or">OR</option></select><button class="deleteBtn" style="width:20px;flex:0"><i class="fa fa-times"></i></button></div></td>
        </tr>`;
    var rowText = `<p id="text-${uniqueId}"><span class="typeText"></span><span class="typeValueText"></span><span class="compareText"></span><span class="operationText"></span></p>`;
    $('#conditionTable tbody').append(row);
    $('#condition-tab').append(rowText);
}

function getTargetAdvs(data){
    targetTable = $('#targetTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/adv",
            "data": data,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columns": [
            { "data": "media", "width": "10%"},
            { "data": "type", "width": "10%"},
            { "data": "id", "width": "30%"},
            { "data": "name", "width": "40%"},
            { "data": "status", "width": "10%"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.type+"_"+data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    });
}

function getExecAdvs(data){
    execTable = $('#execTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/adv",
            "data": data,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columns": [
            { "data": "media", "width": "10%"},
            { "data": "type", "width": "10%"},
            { "data": "id", "width": "30%"},
            { "data": "name", "width": "40%"},
            { "data": "status", "width": "10%"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.type+"_"+data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    });
}

function validationData(){
    $type_value = $('#scheduleTable input[name=type_value]').val();
    $exec_type = $('#scheduleTable select[name=exec_type]').val();
    $exec_time = $('#scheduleTable select[name=exec_time]').val();
    $exec_week = $('#scheduleTable input[name=exec_week]:checked').length;
    $month_type = $('#scheduleTable select[name=month_type]').val();
    $month_week = $('#scheduleTable select[name=month_week]').val();
    $month_day = $('#scheduleTable select[name=month_day]').val();

    $targetConditionDisabled = $('#targetConditionDisabled').is(':checked');
    $selectTarget = $('#targetSelectTable tbody tr').length;
    $selectExec = $('#execSelectTable tbody tr').length;

    $subject = $('#detailTable input[name=subject]').val();

    if (!$type_value) {
        alert('시간값을 입력해주세요');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable input[name=type_value]').focus();
        return false;
    }

    if ($exec_type === 'day' && !$exec_time) {
        alert('시간을 선택해주세요.');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable select[name=exec_time]').focus();
        return false;
    }

    if ($exec_type === 'week') {
        if(!$exec_week > 0){
            alert('요일을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable input[name=exec_week]').focus();
            return false;
        }

        if(!$exec_time){
            alert('시간을 입력해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=exec_time]').focus();
            return false;
        }
    }

    if ($exec_type === 'month') {
        if(!$month_type){
            alert('월 조건값을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_type]').focus();
            return false;
        }

        if(($month_type === 'first' || $month_type === 'last') && !$month_week){
            alert('요일을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_week]').focus();
            return false;
        }

        if($month_type === 'day' && !$month_day){
            alert('일자를 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_day]').focus();
            return false;
        }

        if(!$exec_time){
            alert('시간을 입력해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=exec_time]').focus();
            return false;
        }
    }

    if(!$targetConditionDisabled){
        if(!$selectTarget > 0){
            alert('대상을 추가해주세욧.');
            $('#target-tab').trigger('click');
            $('#showTargetAdv').focus();
            return false;
        }

        var eachValid = true;
        $('tr[id^="condition-"]').each(function() {
            var $row = $(this);
            var $conditionOrder = $row.find('input[name=order]').val();
            var $conditionType = $row.find('select[name=type]').val();
            var $conditionTypeValueStatus = $row.find('select[name=type_value_status]').val();
            var $conditionTypeValue = $row.find('input[name=type_value]').val();
            var $conditionCompare = $row.find('select[name=compare]').val();
            var $conditionOperation = $row.find('select[name=operation]').val();

            if(!$conditionOrder){
                alert('순서를 입력해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('input[name=order]').focus();
                eachValid = false;
                return false;
            }

            if(!$conditionType){
                alert('조건항목을 선택해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('select[name=type]').focus();
                eachValid = false;
                return false;
            }

            if($conditionType == 'status'){
                if(!$conditionTypeValueStatus){
                    alert('상태값을 선택해주세요.');
                    $('#condition-tab').trigger('click');
                    $row.find('select[name=type_value_status]').focus();
                    eachValid = false;
                    return false;
                }
            }else{
                if(!$conditionTypeValue){
                    alert('조건값을 입력해주세요.');
                    $('#condition-tab').trigger('click');
                    $row.find('input[name=type_value]').focus();
                    eachValid = false;
                    return false;
                }
            }

            if(!$conditionCompare){
                alert('일치여부를 선택해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('select[name=compare]').focus();
                eachValid = false;
                return false;
            }

            if(!$conditionOperation){
                alert('연산조건을 선택해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('select[name=operation]').focus();
                eachValid = false;
                return false;
            }
        });

        if(!eachValid){
            return false;
        }
    }

    if(!$selectExec > 0){
        alert('실행항목을 추가해주세요.');
        $('#preactice-tab').trigger('click');
        $('#showExecAdv').focus();
        return false;
    }

    if(!$subject){
        alert('제목을 추가해주세요.');
        $('#detailTable input[name=subject]').focus();
        return false;
    }

    return true;
}

function onlyNumber(inputElement) {
    inputElement.value = inputElement.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
}

function setModalData(data){
    $('#scheduleTable select[name=exec_type]').val(data.aas_exec_type);
    $('#scheduleTable input[name=type_value]').val(data.aas_type_value);
    if(data.aas_exec_week){
        $('#scheduleTable input[name=exec_week][value="' + data.aas_exec_week + '"]').prop('checked', true);
    }
    if(data.aas_month_type){
        $('#scheduleTable select[name=month_type]').val(data.aas_month_type);
    }
    if(data.aas_month_day){
        $('#scheduleTable select[name=month_day]').val(data.aas_month_day);
    }
    if(data.aas_month_week){
        $('#scheduleTable select[name=month_week]').val(data.aas_month_week);
    }
    if(data.aas_exec_time){
        $('#scheduleTable select[name=exec_time]').val(data.aas_exec_time);
    }
    if(data.aas_ignore_start_time){
        $('#scheduleTable select[name=ignore_start_time]').val(data.aas_ignore_start_time);
    }
    if(data.aas_ignore_end_time){
        $('#scheduleTable select[name=ignore_end_time]').val(data.aas_ignore_end_time);
    }

    var selectedData = '<tr><td>' + data.aat_media + '</td><td>' + data.aat_type + '</td><td>' 
    + data.aat_id + '</td><td>' + data.aat_name + '</td><td>'
    + data.aat_status  +'</td></tr>';
    $('#targetSelectTable tbody').append(selectedData);
    $('#targetSelectTable input[name=adv_info]').val(data.aat_media+"_"+data.aat_type+"_"+data.aat_id);
    targetText();

    //조건
    if (data.conditions) {
        data.conditions.forEach(function(condition, index) {
            if(index === 0) {
                $('#condition-1 .conditionOrder').val(condition.order);
                $('#condition-1 .conditionType').val(condition.type);
                if(condition.type == 'status'){
                    $('#condition-1 .conditionTypeValue').hide();
                    $('#condition-1 .conditionTypeValueStatus').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValueStatus option:selected').text();
                }else{
                    $('#condition-1 .conditionTypeValueStatus').hide();
                    $('#condition-1 .conditionTypeValue').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValueStatus').val();
                }
                
                $('#condition-1 .conditionCompare').val(condition.compare);
                $('#condition-1 .conditionOperation').val(condition.operation);
                $("#text-condition-1 .typeText").html($('#condition-1 .conditionType option:selected').text());
                $("#text-condition-1 .typeValueText").html(conditionTypeValueText);
                $("#text-condition-1 .compareText").html($('#condition-1 .conditionCompare option:selected').text());
                $("#text-condition-1 .operationText").html("<br>"+$('#condition-1 .conditionOperation option:selected').text());
            } else { // 그 외의 항목일 경우
                var uniqueId = 'condition-' + (index + 1);
                addConditionRow(uniqueId);
                $(`#${uniqueId} .conditionOrder`).val(condition.order);
                $(`#${uniqueId} .conditionType`).val(condition.type);
                if(condition.type == 'status'){
                    $(`#${uniqueId} .conditionTypeValue`).hide();
                    $(`#${uniqueId} .conditionTypeValueStatus`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValueStatus option:selected").text();
                }else{
                    $(`#${uniqueId} .conditionTypeValueStatus`).hide();
                    $(`#${uniqueId} .conditionTypeValue`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValueStatus").val();
                }
                $(`#${uniqueId} .conditionCompare`).val(condition.compare);
                $(`#${uniqueId} .conditionOperation`).val(condition.operation);

                console.log($("#condition-"+uniqueId+" .conditionType option:selected"));
                $("#text-"+uniqueId+" .typeText").html($("#"+uniqueId+" .conditionType option:selected").text());
                $("#text-"+uniqueId+" .typeValueText").html(conditionTypeValueText);
                $("#text-"+uniqueId+" .compareText").html($("#"+uniqueId+" .conditionCompare option:selected").text());
                $("#text-"+uniqueId+" .operationText").html("<br>"+$("#"+uniqueId+" .conditionOperation option:selected").text());
            }
        });
    }
    //실행
    if (data.executions && Array.isArray(data.executions)) {
        data.executions.forEach(function(execution, index) {
            var execIndex = index+1;
            var executionData = '<tr data-id="'+execution.media+"_"+execution.type+"_"+execution.id+'" id="exec-'+execIndex+'"><td>' + execution.media + '</td><td>'
                + execution.type  +'</td><td>'
                + execution.id  +'</td><td>'
                + execution.name  +'</td><td>'
                + execution.status  +'</td><td>'
                + execution.exec_type  +'</td><td><span>'+execution.exec_value+'</span><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td></tr>';
            var newExecText = '<p id="text-exec-'+execIndex+'">* '+execution.type+' - '+execution.media+'<br>'+execution.name+'<br>'+execution.exec_type+' '+ execution.exec_value+'</p>';
            $('#execSelectTable tbody').append(executionData);
            $('#preactice-tab').append(newExecText);
        });
    }

    if(data.aa_target_condition_disabled == 1){
        $('#targetConditionDisabled').prop('checked', true);
    }else{
        $('#targetConditionDisabled').prop('checked', false);
    }

    $('#detailTable input[name=subject]').val(data.aa_subject);
    if(data.aa_description){
        $('#detailTable textarea[name=description]').val(data.aa_description); 
    }

    $('#detailText #subjectText').text(data.aa_subject);
    $('#detailText #descriptionText').text(data.aa_description);
}

function reset(){
    $('#conditionTable tbody tr:not(#condition-1)').remove()
    $('#target-tab #disabledText, #condition-tab #disabledText').remove();
    $('#targetSelectTable tbody tr, #execSelectTable tbody tr').empty();

    $('#myTab li').each(function(index){
        let $pTags = $(this).find('p');
        if (index === 2 || index === 4) {
            $pTags.first().find('span').text('');
            
        } else {
            $pTags.first().text('');
        }
        $pTags.not(':first').remove();
    })

    $('#automationModal').find('select').each(function() {
        $(this).prop('selectedIndex', 0);
    });
    $('#automationModal').find('input[type=text], input[type=hidden], textarea').each(function() {
        $(this).val('');
    }); 
    
    $('#targetConditionDisabled').prop('checked', false);
    $('#showTargetAdv').prop('disabled', false);
    
    $('#targetText').show();
    $('#condition-tab p').show();
    
    if ($.fn.DataTable.isDataTable('#targetTable')) {
        targetTable = $('#targetTable').DataTable();
        targetTable.destroy();
    }
    if ($.fn.DataTable.isDataTable('#execTable')) {
        execTabl = $('#execTable').DataTable();
        execTabl.destroy();
    }

    $('#targetTable tbody tr, #execTable tbody tr').remove();
    $('#schedule-tab').trigger('click');
}
//검색
$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

//리스트 더보기 버튼
$('#automation-table').on('click', '.btn-more', function () {
    var seq = $(this).data('seq');
    var currentActionList = $(this).closest('.more-action').find('.action-list');
    $('.action-list').not(currentActionList).fadeOut(0);
    currentActionList.fadeToggle();
});

//status 변경
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

//모달 보기
$('#automationModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.hasClass('updateBtn')) {
        var id = $btn.data('id');
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/automation/get-automation",
            data: {'id':id},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setModalData(data);
                chkSchedule();
                if(data.aas_month_type){
                    chkScheduleMonthType()
                }
                scheduleText();
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }else{
        chkSchedule();
    }

    
})//모달 닫기
.on('hidden.bs.modal', function(e) { 
    reset();
});

//등록 부분 시작
$('body').on('change', '#execType', function() {
    chkSchedule();
});

$('body').on('change', '#monthType', function() {
    chkScheduleMonthType();
});

$('body').on('change', '#scheduleTable input, #scheduleTable select', function() {
    scheduleText();
});

$('form[name="search-target-form"]').bind('submit', function() {
    var data = {
        'tab': $('#targetTab li.active').data('tab'),
        'stx': $('#showTargetAdv').val(),
    }
    getTargetAdvs(data);
    return false;
});

$('form[name="search-exec-form"]').bind('submit', function() {
    var disableChecked = $('#targetConditionDisabled').is(':checked');
    var data = {
        'tab': $('#execTab li.active').data('tab'),
        'stx': $('#showExecAdv').val(),
        'adv': disableChecked ? null : $('input[name=adv_info]').val(),
    }
    console.log(data);
    getExecAdvs(data);
    return false;
});

$('body').on('click', '#targetTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
         $(this).removeClass('selected');
         $('#targetSelectTable input[name=adv_info]').val('');
         $('#targetSelectTable tbody').empty();
         $('#targetText').html('');
    }
    else {
        $('#targetTable tr.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#targetSelectTable tbody').empty();

        var targetId = $(this).data('id');
        $('#targetSelectTable input[name=adv_info]').val(targetId);
        targetText();
        $(this).closest('tr').clone().appendTo('#targetSelectTable');
    }
});

$('body').on('click', '#execTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }
    else {
        $('#execTable tr.selected').removeClass('selected');
        $(this).addClass('selected');
    }
});

$('body').on('click', '#targetTab li', function(){
    $('#targetTab li').removeClass('active');
    $(this).addClass('active');
})

$('body').on('click', '#execTab li', function(){
    $('#execTab li').removeClass('active');
    $(this).addClass('active');
})

//미적용 체크
$('body').on('change', '#targetConditionDisabled', function(){
    if($(this).is(':checked')) {  
        var disabledText = '<p id="disabledText">미적용</p>';
        $('#showTargetAdv').prop('disabled', true);
        $('#targetText').hide();

        $('#condition-tab p').hide();
        $('#condition-tab, #target-tab').append(disabledText);
    }else{
        $('#showTargetAdv').prop('disabled', false);

        $('#target-tab #disabledText, #condition-tab #disabledText').remove();
        $('#targetText').show();
        $('#condition-tab p').show();
    }
})

$('body').on('click', '#conditionTable .btn-add', function(){
    var currentRowCount = $('#conditionTable tbody tr').length;
    var uniqueId = 'condition-' + (currentRowCount + 1);
    addConditionRow(uniqueId);
})

$('body').on('change', '#conditionTable select[name=type]', function() {
    //상태 선택
    var type = $(this).val();
    var rowId = $(this).closest('tr').attr('id');
    if(type == 'status'){
        $('#text-'+rowId+" .typeValueText").text('');
        $(this).siblings('input[name=type_value]').val('').hide();
        $(this).closest('tr').find('select[name=compare]').children('option:not([value="equal"], [value="not_equal"])').hide();
        $(this).siblings('select[name=type_value_status]').show();
    }else{
        if($('#text-'+rowId+" .typeValueText").text() == 'ON' || $('#text-'+rowId+" .typeValueText").text() == 'OFF'){
            $('#text-'+rowId+" .typeValueText").text('');
        }
        $(this).siblings('select[name=type_value_status]').val('').hide();
        $(this).closest('tr').find('select[name=compare]').children('option').show();   
        $(this).siblings('input[name=type_value]').show();   
    }
});

$('body').on('change', '#conditionTable input, #conditionTable select', function() {
    var $this = $(this);
    conditionText($this);
});

$('body').on('change', '#execConditionTable select[name=exec_condition_type]', function() {
    //상태 선택
    var type = $(this).val();
    if(type == 'status'){
        $(this).siblings('input[name=exec_condition_value]').val('').hide();
        $(this).siblings('select[name=exec_condition_value_status]').show();
    }else{
        $(this).siblings('select[name=exec_condition_value_status]').val('').hide();
        $(this).siblings('input[name=exec_condition_value]').show();   
    }
});

$('body').on('click', '#execConditionBtn', function() {
    var tr = $('#execTable tbody tr.selected');
    var trId = tr.data('id');

    if(tr.length == 0){
        alert("항목을 선택해주세요.");
    }else{
        if ($('#execSelectTable tbody tr[data-id="' + trId + '"]').length == 0) {
            var cloneRow = $('#execTable tbody tr.selected').clone();
            var execConditionType = $('#execConditionTable select[name=exec_condition_type]').val();
            var execConditionTypeText = $('#execConditionTable select[name=exec_condition_type] option:selected').text();
            var execConditionValue = '';
            if(execConditionType != ''){
                if(execConditionType == 'status'){
                    execConditionValue = $('#execConditionTable select[name=exec_condition_value_status]').val();
                }else{
                    execConditionValue = $('#execConditionTable input[name=exec_condition_value]').val();
                }

                if(execConditionValue != ''){
                    var newRowIdNumber = $('#execSelectTable tbody tr').length + 1;

                    cloneRow.append('<td>'+execConditionTypeText+'</td><td><span>'+execConditionValue+'</span><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td>').attr('id', 'exec-'+newRowIdNumber).appendTo('#execSelectTable');

                    var selectedMediaTd = tr.children('td').eq(0).text();
                    var selectedTypeTd = tr.children('td').eq(1).text();
                    var selectedNameTd = tr.children('td').eq(3).text();
                    var selectedStatusTd = tr.children('td').eq(4).text();

                    var newExecText = '<p id="text-exec-'+newRowIdNumber+'">* '+selectedTypeTd+' - '+selectedMediaTd+'<br>'+selectedNameTd+'<br>'+execConditionTypeText+' '+ execConditionValue+'</p>';
                    $('#preactice-tab').append(newExecText);
                }else{
                    alert("세부항목을 선택해주세요.");
                }
            }else{
                alert("실행항목을 선택해주세요.");
            } 
        } else {
            alert("중복된 항목은 추가할 수 없습니다.");
        }
    }
});

$('body').on('click', '.exec_condition_except_btn', function() {
    $(this).closest('tr').remove();
    var rowId = $(this).closest('tr').attr('id');
    $('#preactice-tab #text-'+rowId).remove();
});

$('body').on('click', '.deleteBtn', function() {
    $(this).closest('tr').remove();
    var rowId = $(this).closest('tr').attr('id');
    $('#condition-tab #text-'+rowId).remove();
});

$('body').on('focusout', '#detailTable input[name=subject]', function() {
    var detailTextSubject = $(this).val();
    $('#detailText #subjectText').text(detailTextSubject);
});

$('body').on('focusout', '#detailTable textarea[name=description]', function() {
    var detailTextDescription = $(this).val();
    $('#detailText #descriptionText').text(detailTextDescription);
});

$('body').on('click', '#createAutomationBtn', function() {
    if(validationData()){
        let $type_value = $('#scheduleTable input[name=type_value]').val();
        let $exec_type = $('#scheduleTable select[name=exec_type]').val();
        let $exec_week = $('#scheduleTable input[name=exec_week]').val();
        let $month_type = $('#scheduleTable select[name=exemonth_typec_week]').val();
        let $month_day = $('#scheduleTable select[name=month_day]').val();
        let $month_week = $('#scheduleTable select[name=month_week]').val();
        let $exec_time = $('#scheduleTable select[name=exec_time]').val();
        let $ignore_start_time = $('#scheduleTable select[name=ignore_start_time]').val();
        let $ignore_end_time = $('#scheduleTable select[name=ignore_end_time]').val();

        let $target_media = $('#targetSelectTable tbody tr').eq(0).text();
        let $target_type = $('#targetSelectTable tbody tr').eq(1).text();
        let $target_id = $('#targetSelectTable tbody tr').eq(2).text();

        let $conditions = [];
        let $executions = [];
        $('#conditionTable tbody tr[id^="condition-"]').each(function(){
            let $row = $(this);
            let order = $row.find('input[name=order]').val();
            let type = $row.find('select[name=type]').val();
            let type_value = '';
            if(type = 'status'){
                type_value = $row.find('select[name=type_value_status]').val();
            }else{
                type_value = $row.find('input[name=type_value]').val();
            }
            let compare = $row.find('select[name=compare]').val();
            let operation = $row.find('select[name=operation]').val();

            $conditions.push({
                order: order,
                type: type,
                type_value: type_value,
                compare: compare,
                operation: operation
            });
        });


        $('#execSelectTable tbody tr').each(function(){
            let $row = $(this);
            //let order = $row.find('td:eq(0)').text();
            let media = $row.find('td:eq(0)').text();
            let type = $row.find('td:eq(1)').text();
            let id = $row.find('td:eq(2)').text();
            let exec_type = $row.find('td:eq(5)').text();
            let exec_value = $row.find('td:eq(6)').text();

            $executions.push({
                //order: order,
                type: type,
                id: id,
                exec_type: exec_type,
                exec_value: exec_value
            });
        });

        let $subject = $('#detailTable input[name=subject]').val();
        let $description = $('#detailTable textarea[name=description]').val();

        $data = {
            'schedule': {
                'type_value': $type_value,
                'exec_type': $exec_type,
                'exec_week': $exec_week,
                'month_type': $month_type,
                'month_day': $month_day,
                'month_week': $month_week,
                'exec_time': $exec_time,
                'ignore_start_time': $ignore_start_time,
                'ignore_end_time': $ignore_end_time,
            },
            'target': {
                'type': $target_type,
                'media': $target_media,
                'id': $target_id,
            },
            'condition': $conditions,
            'execution': $executions,
            'detail': {
                'subject': $subject,
                'description': $description
            }
        };


    };
});
//등록 부분 끝
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>