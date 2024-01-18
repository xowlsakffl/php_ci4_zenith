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
    .ui-autocomplete{z-index: 10000000; max-height: 300px;overflow-y: auto;overflow-x: hidden;}
    .target-btn{background-color: #ce1922;color:#fff;border-radius: 5px;}
    .set_target_except_btn{margin-left: 10px;}
    .log-search-wrap{margin-bottom:0;}
    #logTable{margin-top: 0 !important;}
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap" id="automationContent">
    <div class="title-area">
        <h2 class="page-title">자동화 목록</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search">
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
                <button class="btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#logModal">로그 보기</button>
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
                        <th scope="col">예상 실행 시간</th>
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
                            </li>
                            <li class="tab-link" role="presentation" id="condition-tab" data-bs-toggle="tab" data-bs-target="#condition" type="button" role="tab" aria-controls="condition" aria-selected="false">
                                <strong>조건</strong>
                                <p id="text-condition-1">
                                    <span class="typeText"></span>
                                    <span class="typeValueText"></span>
                                    <span class="compareText"></span>
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
                        <input type="hidden" name="seq">
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
                                <tr id="criteriaTimeRow">
                                    <th scope="row">시작 일시</th>
                                    <td>
                                        <div class="form-flex">
                                            <input type="text" name="criteria_time" class="form-control" readonly>
                                        </div>
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
                                <li data-tab="account"><a href="#">매체광고주</a></li>
                                <li data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adgroup"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <form name="search-target-form" class="search d-flex justify-content-center w-100">
                                    <div class="input">
                                        <input type="text" placeholder="검색어를 입력하세요" id="showTargetAdv">
                                        <button class="btn-primary" id="search_target_btn" type="submit">조회</button>
                                    </div>
                                </form>
                            </div>
                            <table class="table tbl-header w-100" id="targetCheckedTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">선택 항목(선택 후 상단 탭 클릭 시 소속 항목 조회가 가능합니다)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <table class="table tbl-header w-100" id="targetTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:32%">
                                    <col style="width:10%">
                                    <col style="width:8%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">매체</th>
                                        <th scope="col" class="text-center">분류</th>
                                        <th scope="col" class="text-center">ID</th>
                                        <th scope="col" class="text-center">제목</th>
                                        <th scope="col" class="text-center">상태</th>
                                        <th scope="col" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="targetSelectTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">적용 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="condition" role="tabpanel" aria-labelledby="condition-tab" tabindex="2">
                            <div class="d-flex align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="and" id="operationAnd">
                                    <label class="form-check-label" for="operationAnd">
                                        모두 일치
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="or" id="operationOr">
                                    <label class="form-check-label" for="operationOr">
                                        하나만 일치
                                    </label>
                                </div>
                            </div>
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
                                                <!-- <input type="text" name="order" placeholder="순서(1~)" class="form-control conditionOrder" oninput="onlyNumber(this);" maxlength="3"> -->
                                                <select name="type" class="form-select conditionType">
                                                    <option value="">조건 항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                    <option value="dbcost">DB단가</option>
                                                    <option value="unique_total">유효DB</option>
                                                    <option value="spend">지출액</option>
                                                    <option value="margin">수익</option>
                                                    <option value="margin_rate">수익률</option>
                                                    <option value="sales">매출액</option>
                                                    <!-- <option value="impression">노출수</option>
                                                    <option value="click">링크클릭</option>
                                                    <option value="cpc">CPC</option>
                                                    <option value="ctr">CTR</option> -->
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
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="preactice" role="tabpanel" aria-labelledby="preactice-tab" tabindex="3">
                            <ul class="tab" id="execTab">
                                <li class="active" data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adgroup"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <div class="d-flex align-items-center mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="searchAll">
                                    <label class="form-check-label" for="searchAll">
                                        전체검색
                                    </label>
                                </div>
                                <form name="search-exec-form" class="search d-flex justify-content-center w-100">
                                   <div class="input">
                                        <input type="text" placeholder="검색어를 입력하세요" id="showExecAdv">
                                        <button class="btn-primary" id="search_exec_btn" type="submit">조회</button>
                                   </div>
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
                                                    <option value="">상태값 선택</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="exec_condition_value" class="form-control" id="execConditionValue" placeholder="예산">
                                                <select name="exec_condition_type_budget" class="form-select" id="execConditionTypeBudget">
                                                    <option value="">단위 선택</option>
                                                    <option value="won">원</option>
                                                    <option value="percent">%</option>
                                                </select>
                                                <button class="btn-special" id="execConditionBtn">적용</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table tbl-header w-100 mt-4 execSelectTable" id="execSelectTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="8"  class="text-center">선택 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4 slackSendTable" id="slackSendTable">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">슬랙 메세지 보내기</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <input type="text" name="slack_webhook" class="form-control" placeholder="웹훅 URL">
                                                <input type="text" name="slack_msg" class="form-control" placeholder="메세지">
                                            </div>
                                        </td>
                                    </tr>
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
                                <button type="button" id="updateAutomationBtn" class="btn-special" style="display: none;">수정</button>
                            </duv>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="logModal"><i class="ico-log"></i> 감사 로그</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="search-wrap log-search-wrap">
                    <form name="log-search-form" class="search">
                        <div class="input">
                            <input type="text" name="log_stx" id="stx" placeholder="검색어를 입력하세요">
                            <button class="btn-primary" id="search_btn" type="submit">조회</button>
                        </div>
                    </form>
                </div>
                <table class="table tbl-dark" id="logTable" style="width: 100%;">
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
                            <th scope="col">결과</th>
                            <th scope="col">마지막 실행</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
var saveTarget = null;

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
        "order": [[3,'desc']],
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
                "render": function(data){
                    subject = '<button type="button" data-bs-toggle="modal" data-bs-target="#automationModal" class="updateBtn">'+data+'</button>';
                    return subject;
                }
            },
            { 
                "data": "aa_nickname", 
                "width": "15%",
            },
            { 
                "data": "aa_mod_datetime", 
                "width": "15%",
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
                "data": "aar_exec_timestamp_success", 
                "width": "15%",
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
                "data": "expected_time", 
                "width": "15%",
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
                    checked = data == 1 ? 'checked' : '';
                    var status = '<div class="td-inner"><div class="ui-toggle"><input type="checkbox" name="status" id="status_' + row.aa_seq + '" ' + checked + ' value="'+row.aa_seq+'"><label for="status_' + row.aa_seq + '">사용</label></div><div class="more-action"><button type="button" class="btn-more"><span>더보기</span></button><ul class="action-list z-1"><li><a href="#" data-seq="' + row.aa_seq + '" class="copy-btn">복제하기</a></li><li><a href="#" data-seq="' + row.aa_seq + '" class="delete-btn">제거하기</a></li></ul></div></div>';

                    return status;
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.aa_seq);
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

function setCriteriaTime(){
    var now = new Date();
    now.setMinutes(now.getMinutes() + 5);
    $('input[name=criteria_time]').daterangepicker({
        singleDatePicker: true,
        locale: {
                "format": 'YYYY-MM-DD HH:mm',     // 일시 노출 포맷
                "applyLabel": "확인",                    // 확인 버튼 텍스트
                "cancelLabel": "취소",                   // 취소 버튼 텍스트
                "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
        },
        timePicker: true,
        timePicker24Hour: true,
        minDate: now,
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
        $('#criteriaTimeRow').show();
        $('#weekdayRow, #nextDateRow, #timeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
        $('#timeRow select').val('');
    } else if (selectedValue === "day") {
        $('#weekdayRow, #nextDateRow').hide();
        $('#criteriaTimeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
    } else if (selectedValue === "week") {
        $('#nextDateRow').hide();
        $('#criteriaTimeRow').hide();
        $('#nextDateRow select').val('');
    } else if (selectedValue === "month"){
        $('#weekdayRow').hide();
        $('#criteriaTimeRow').hide();
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
function scheduleText(){
    var type_value = $('input[name="type_value"]').val();
    var exec_type = $('#execType').val();
    var exec_week = $('input[name="exec_week"]:checked').siblings('label').text();
    var month_type = $('#monthType').val();
    var month_day = $('#monthDay').val();
    var month_week = $('#monthWeek option:selected').text();
    var exec_time = $('#execTime').val();
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
                let dayTextPart_1 = type_value == 1 ? '매일 ' : '매 '+type_value+'일 마다 ';
                let dayTextPart_2 = exec_time ? exec_time+'에' : '';
                scheduleTextParts.push(dayTextPart_1+dayTextPart_2);
                break;
            case "week":
                let weekTextPart_1 = type_value == 1 ? '매주 ' : '매 '+type_value+'주 ';
                let weekTextPart_2 = exec_week ? exec_week+"요일 마다 " : '';
                let weekTextPart_3 = exec_time ? exec_time+'에' : '';
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
}

function addConditionRow(uniqueId){
    var row = `
        <tr id="${uniqueId}">
        <td><div class="form-flex"><select name="type" class="form-select conditionType"><option value="">조건 항목</option><option value="status">상태</option><option value="budget">예산</option><option value="dbcost">DB단가</option><option value="unique_total">유효DB</option><option value="spend">지출액</option><option value="margin">수익</option><option value="margin_rate">수익률</option><option value="sale">매출액</option><option value="conversion">DB전환률</option></select><select name="type_value_status" class="form-select conditionTypeValueStatus" style="display: none;"><option value="">상태값 선택</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값"></div></td><td colspan="2"><div class="form-flex"><select name="compare" class="form-select conditionCompare"><option value="">일치여부</option><option value="greater">초과</option><option value="greater_equal">보다 크거나 같음</option><option value="less">미만</option><option value="less_equal">보다 작거나 같음</option><option value="equal">같음</option><option value="not_equal">같지않음</option></select><button class="deleteBtn" style="width:20px;flex:0"><i class="fa fa-times"></i></button></div></td>
        </tr>`; 
    /* var row = `
        <tr id="${uniqueId}">
        <td><div class="form-flex"><select name="type" class="form-select conditionType"><option value="">조건 항목</option><option value="budget">예산</option><option value="dbcost">DB단가</option><option value="unique_total">유효DB</option><option value="spend">지출액</option><option value="margin">수익</option><option value="margin_rate">수익률</option><option value="sales">매출액</option><option value="conversion">DB전환률</option></select><input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값"></div></td><td colspan="2"><div class="form-flex"><select name="compare" class="form-select conditionCompare"><option value="">일치여부</option><option value="greater">초과</option><option value="greater_equal">보다 크거나 같음</option><option value="less">미만</option><option value="less_equal">보다 작거나 같음</option><option value="equal">같음</option><option value="not_equal">같지않음</option></select><button class="deleteBtn" style="width:20px;flex:0"><i class="fa fa-times"></i></button></div></td>
        </tr>`;  */
    var rowText = `<p id="text-${uniqueId}"><span class="typeText"></span><span class="typeValueText"></span><span class="compareText"></span></p>`;
    $('#conditionTable tbody').append(row);
    $('#condition-tab').append(rowText);
}

function getTargetAdvs(searchData){
    targetTable = $('#targetTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/adv",
            "data": searchData,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columnDefs": [
            { targets: [5], orderable: false},
        ],
        "columns": [
            { "data": "media", "width": "10%"},
            { "data": "type", "width": "10%"},
            { "data": "id", "width": "30%"},
            { "data": "name", "width": "35%"},
            { "data": "status", "width": "8%",},
            { 
                "data": null, 
                "width": "7%",
                "render": function(){
                    let button = '<button class="target-btn">적용</button>';
                    return button;
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.type+"_"+data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "drawCallback": function(settings) {
            if($('#targetCheckedTable tbody tr').length > 0){
                $selectedTargetRow = $('#targetCheckedTable tbody tr').data('id');
                $('#targetTable tbody tr[data-id="'+$selectedTargetRow+'"]').addClass('selected')
            }
        }
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
        "ordering": true,
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

//유효성 검사
function validationData(){
    let $type_value = $('#scheduleTable input[name=type_value]').val();
    let $criteria_time = $('#scheduleTable input[name=criteria_time]').val();
    let $exec_type = $('#scheduleTable select[name=exec_type]').val();
    let $exec_time = $('#scheduleTable select[name=exec_time]').val();
    let $exec_week = $('#scheduleTable input[name=exec_week]:checked').length;
    let $month_type = $('#scheduleTable select[name=month_type]').val();
    let $month_week = $('#scheduleTable select[name=month_week]').val();
    let $month_day = $('#scheduleTable select[name=month_day]').val();

    let $operation = $('input[name=operation]:checked').length;
    let $selectTarget = $('#targetSelectTable tbody tr').length;
    let $selectExec = $('#execSelectTable tbody tr').length;

    let $subject = $('#detailTable input[name=subject]').val();
    let $slack_webhook = $('#slackSendTable input[name="slack_webhook"]').val();
    let $slack_msg = $('#slackSendTable input[name="slack_msg"]').val();

    if (!$type_value) {
        alert('시간 조건값을 입력해주세요');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable input[name=type_value]').focus();
        return false;
    }

    if (($exec_type === 'minute' || $exec_type === 'hour') && !$criteria_time) {
        alert('시작 일시를 입력해주세요.');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable input[name=criteria_time]').focus();
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
    //대상 선택항목이 있을경우
    if($selectTarget > 0){
        if(!$operation){
            alert('일치조건을 선택해주세요.');
            $('#condition-tab').trigger('click');
            $('input[name=operation]').focus();
            return false;
        }

        var eachValid = true;
        $('tr[id^="condition-"]').each(function() {
            var $row = $(this);
            //var $conditionOrder = $row.find('input[name=order]').val();
            var $conditionType = $row.find('select[name=type]').val();
            var $conditionTypeValueStatus = $row.find('select[name=type_value_status]').val();
            var $conditionTypeValue = $row.find('input[name=type_value]').val();
            var $conditionCompare = $row.find('select[name=compare]').val();

            /* if(!$conditionOrder){
                alert('순서를 입력해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('input[name=order]').focus();
                eachValid = false;
                return false;
            } */

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
        });

        if(!eachValid){
            return false;
        }
    }else{
        var hasValue = false;
        $('tr[id^="condition-"]').find('input, select').each(function() {
            if($(this).val()){
                hasValue = true;
                return false;
            }
        });

        if(hasValue){
            alert('대상이 존재하지 않는데 조건값이 설정되어 있습니다.');
            $('#condition-tab').trigger('click');
            return false;
        }
    }

    if(!$selectExec > 0){
        alert('실행항목을 추가해주세요.');
        $('#preactice-tab').trigger('click');
        $('#showExecAdv').focus();
        return false;
    }

    var execOrderCheck = true;
    $('#execSelectTable tbody tr').each(function() {
        var input = $(this).find('td:first input');
        if(input.val() == '') {      
            execOrderCheck = false;
            return false;
        }
    });

    if(!execOrderCheck){
        $('#preactice-tab').trigger('click');
        alert('순서를 입력해주세요.');
        return false;
    }


    if (($slack_webhook && !$slack_msg) || (!$slack_webhook && $slack_msg)) {     
        alert('웹훅 URL과 메세지 둘 다 입력해주세요.');
        $('#preactice-tab').trigger('click');
        if(!$('#slackSendTable input[name="slack_webhook"]').val()){
            $('#slackSendTable input[name="slack_webhook"]').focus();
        } else if(!$('#slackSendTable input[name="slack_msg"]').val()){
            $('#slackSendTable input[name="slack_msg"]').focus();
        }
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
//모달 수정 세팅
function setModalData(data){
    $('#createAutomationBtn').hide();
    $('#updateAutomationBtn').show();
    $('#automationModal input[name=seq]').val(data.aa_seq);
    $('#scheduleTable select[name=exec_type]').val(data.aas_exec_type);
    $('#scheduleTable input[name=type_value]').val(data.aas_type_value);
    if(data.aas_criteria_time){
        $('#scheduleTable input[name=criteria_time]').val(data.aas_criteria_time);
    }else{
        $('#scheduleTable input[name=criteria_time]').val('');
    }
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

    if(data.targets){
        data.targets.forEach(function(target, index) {
            let targetIndex = index+1;
            let targetData = '<tr data-id="'+target.media+"_"+target.type+"_"+target.id+'" id="target-'+targetIndex+'"><td>' + target.media + '</td><td>' + target.type + '</td><td>' 
        + target.id + '</td><td>' + target.name + '</td><td>'
        + target.status  +'<button class="set_target_except_btn"><i class="fa fa-times"></i></button></td></tr>';
            let newTargetText = '<p id="text-target-'+targetIndex+'">'+target.media+'<br>'+target.type+'<br>'+target.name+'</p>';
            $('#targetSelectTable tbody').append(targetData);
            $('#target-tab').append(newTargetText);
        });
    }

    //조건
    if (data.conditions) {
        data.conditions.forEach(function(condition, index) {
            if(index === 0) {
                if(condition.operation == 'and'){
                    $('input[type="radio"][value="and"]').prop('checked', true);
                }else{
                    $('input[type="radio"][value="or"]').prop('checked', true);
                }
                $('#condition-1 .conditionOrder').val(condition.order);
                $('#condition-1 .conditionOrder').val(condition.order);
                $('#condition-1 .conditionType').val(condition.type);
                if(condition.type == 'status'){
                    $('#condition-1 .conditionTypeValue').hide();
                    $('#condition-1 .conditionTypeValueStatus').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValueStatus option:selected').text();
                }else{
                    $('#condition-1 .conditionTypeValueStatus').hide();
                    $('#condition-1 .conditionTypeValue').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValue').val();
                }
                
                $('#condition-1 .conditionTypeValue').val(condition.type_value);
                var conditionTypeValueText = $('#condition-1 .conditionTypeValue').val();

                $('#condition-1 .conditionCompare').val(condition.compare);
                $("#text-condition-1 .typeText").html($('#condition-1 .conditionType option:selected').text());
                $("#text-condition-1 .typeValueText").html(conditionTypeValueText);
                $("#text-condition-1 .compareText").html($('#condition-1 .conditionCompare option:selected').text());
            } else { // 그 외의 항목일 경우
                var uniqueId = 'condition-' + (index + 1);
                addConditionRow(uniqueId);
                //$(`#${uniqueId} .conditionOrder`).val(condition.order);
                $(`#${uniqueId} .conditionType`).val(condition.type);
                if(condition.type == 'status'){
                    $(`#${uniqueId} .conditionTypeValue`).hide();
                    $(`#${uniqueId} .conditionTypeValueStatus`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValueStatus option:selected").text();
                }else{
                    $(`#${uniqueId} .conditionTypeValueStatus`).hide();
                    $(`#${uniqueId} .conditionTypeValue`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValue").val();
                }
                $(`#${uniqueId} .conditionTypeValue`).val(condition.type_value);
                var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValue").val();

                $(`#${uniqueId} .conditionCompare`).val(condition.compare);
                $("#text-"+uniqueId+" .typeText").html($("#"+uniqueId+" .conditionType option:selected").text());
                $("#text-"+uniqueId+" .typeValueText").html(conditionTypeValueText);
                $("#text-"+uniqueId+" .compareText").html($("#"+uniqueId+" .conditionCompare option:selected").text());
            }
        });
    }
    //실행
    if (data.executions && Array.isArray(data.executions)) {
        data.executions.forEach(function(execution, index) {
            let execConditionBudgetTypeText = '';
            if(execution.exec_budget_type == 'won'){
                execConditionBudgetTypeText = '원';
            }else if(execution.exec_budget_type == 'percent'){
                execConditionBudgetTypeText = '%';
            }
            var execIndex = index+1;
            var executionData = '<tr data-id="'+execution.media+"_"+execution.type+"_"+execution.id+'" id="exec-'+execIndex+'"><td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2" value="'+execution.order+'"></td><td>' + execution.media + '</td><td>'
                + execution.type  +'</td><td>'
                + execution.id  +'</td><td>'
                + execution.name  +'</td><td>'
                + execution.status  +'</td><td>'
                + execution.exec_type  +'</td><td><span class="exec_value">'+execution.exec_value+'</span><span class="exec_condition_select_budget_type">'+execConditionBudgetTypeText+'</span><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td></tr>';
            var newExecText = '<p id="text-exec-'+execIndex+'">* '+execution.type+' - '+execution.media+'<br>'+execution.name+'<br>'+execution.exec_type+' '+ execution.exec_value+execConditionBudgetTypeText+'</p>';
            $('#execSelectTable tbody').append(executionData);
            $('#preactice-tab').append(newExecText);
        });
    }

    if(data.aa_slack_webhook){
        $('#slackSendTable input[name=slack_webhook]').val(data.aa_slack_webhook); 
    }

    if(data.aa_slack_msg){
        $('#slackSendTable input[name=slack_msg]').val(data.aa_slack_msg); 
    }

    $('#detailTable input[name=subject]').val(data.aa_subject);
    if(data.aa_description){
        $('#detailTable textarea[name=description]').val(data.aa_description); 
    }

    $('#detailText #subjectText').text(data.aa_subject);
    $('#detailText #descriptionText').text(data.aa_description);

    conditionStatusHide();
    chkSchedule();
    if(data.aas_month_type){
        chkScheduleMonthType()
    }
    scheduleText();
}

//모달 초기화
function reset(){
    $('#conditionTable tbody tr:not(#condition-1)').remove()
    $('#targetCheckedTable tbody tr, #targetSelectTable tbody tr, #execSelectTable tbody tr').remove();
    $('#condition-1 input[name=type_value]').show();
    $('#condition-1 select[name=type_value_status]').hide();
    $('#myTab li').each(function(index){
        let $pTags = $(this).find('p');
        if(index === 1 || index === 3){
            $pTags.remove();
        }else if (index === 2 || index === 4) {
            $pTags.first().find('span').text('');
        }else{
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
    
    $('input[name=operation]').prop('checked', false);
    $('#searchAll').prop('checked', false);
    $('#showTargetAdv').prop('disabled', false);
    
    $('#condition-tab p').show();
    
    if ($.fn.DataTable.isDataTable('#targetTable')) {
        targetTable = $('#targetTable').DataTable();
        targetTable.destroy();
    }
    if ($.fn.DataTable.isDataTable('#execTable')) {
        execTable = $('#execTable').DataTable();
        execTable.destroy();
    }

    $('#targetTable tbody tr, #execTable tbody tr').remove();
    $('#schedule-tab').trigger('click');
}

function setProcData(){
    let $type_value = $('#scheduleTable input[name=type_value]').val();
    let $exec_type = $('#scheduleTable select[name=exec_type]').val();
    let $criteria_time = $('#scheduleTable input[name=criteria_time]').val();
    let $exec_week = $('#scheduleTable input[name=exec_week]:checked').val();
    let $month_type = $('#scheduleTable select[name=month_type]').val();
    let $month_day = $('#scheduleTable select[name=month_day]').val();
    let $month_week = $('#scheduleTable select[name=month_week]').val();
    let $exec_time = $('#scheduleTable select[name=exec_time]').val();
    let $ignore_start_time = $('#scheduleTable select[name=ignore_start_time]').val();
    let $ignore_end_time = $('#scheduleTable select[name=ignore_end_time]').val();
    
    let operation = $('input[name=operation]:checked').val();

    let $targets = [];
    let $conditions = [];
    let $executions = [];

    $('#targetSelectTable tbody tr').each(function(){
        let $row = $(this);
        let media = $row.find('td:eq(0)').text();
        let type = $row.find('td:eq(1)').text();
        let id = $row.find('td:eq(2)').text();

        $targets.push({
            media: media,
            type: type,
            id: id,
        });
    });

    $('#conditionTable tbody tr[id^="condition-"]').each(function(){
        let $row = $(this);
        //let order = $row.find('input[name=order]').val();
        let type = $row.find('select[name=type]').val();
        let type_value = '';
        if(type == 'status'){
            type_value = $row.find('select[name=type_value_status]').val();
        }else{
            type_value = $row.find('input[name=type_value]').val();
        }

        //let type_value = $row.find('input[name=type_value]').val();
        let compare = $row.find('select[name=compare]').val();
        
        if(type){
            $conditions.push({
                //order: order,
                type: type,
                type_value: type_value,
                compare: compare,
                operation: operation
            });
        }
    });

    $('#execSelectTable tbody tr').each(function(){
        let $row = $(this);
        let order = $row.find('td:eq(0) input').val();
        let media = $row.find('td:eq(1)').text();
        let type = $row.find('td:eq(2)').text();
        let id = $row.find('td:eq(3)').text();
        let exec_type = $row.find('td:eq(6)').text();
        let exec_value = $row.find('td:eq(7) .exec_value').text();
        let exec_budget_type = $row.find('td:eq(7) .exec_condition_select_budget_type').text();

        $executions.push({
            order: order,
            media: media,
            type: type,
            id: id,
            exec_type: exec_type,
            exec_value: exec_value,
            exec_budget_type: exec_budget_type
        });
    });

    let $subject = $('#detailTable input[name=subject]').val();
    let $description = $('#detailTable textarea[name=description]').val();
    let $slack_webhook = $('#slackSendTable input[name=slack_webhook]').val();
    let $slack_msg = $('#slackSendTable input[name=slack_msg]').val();

    let $data = {
        'schedule': {
            'type_value': $type_value,
            'exec_type': $exec_type,
            'criteria_time': $criteria_time,
            'exec_week': $exec_week,
            'month_type': $month_type,
            'month_day': $month_day,
            'month_week': $month_week,
            'exec_time': $exec_time,
            'ignore_start_time': $ignore_start_time,
            'ignore_end_time': $ignore_end_time,
        },
        'execution': $executions,
        'detail': {
            'subject': $subject,
            'description': $description,
            'slack_webhook': $slack_webhook,
            'slack_msg': $slack_msg,
        }
    };

    if ($('#targetSelectTable tbody tr').length > 0) {
        $data['target'] = $targets;
        $data['condition'] = $conditions;
    }

    return $data;
}

function conditionStatusHide(){
    let targetCount = $('#targetSelectTable tbody tr').length;
    if (targetCount > 1) {
        $('#conditionTable select option[value=status]').hide();
    }else{
        $('#conditionTable select option[value=status]').show();
    }
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
    setCriteriaTime();
    var $btn = $(e.relatedTarget);
    if ($btn.hasClass('updateBtn')) {
        var id = $btn.closest('tr').data('id');
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/automation/get-automation",
            data: {'id':id},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setModalData(data);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }else{      
        chkSchedule();
        conditionStatusHide();
        $('#createAutomationBtn').show();
        $('#updateAutomationBtn').hide();
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
    let searchAll = $('#searchAll').is(':checked');
    let targets = $('#targetSelectTable tbody tr').length;
    let adv = [];

    if (!searchAll && !targets) {
        alert('대상이 없을 경우 전체검색을 체크해주세요.');
        $('#searchAll').focus();
        return false;
    }

    $('#targetSelectTable tbody tr').each(function() {
        let dataId = $(this).data('id');
        adv.push(dataId);
    });

    let data = {
        'tab': $('#execTab li.active').data('tab'),
        'stx': $('#showExecAdv').val(),
        'adv': searchAll ? null : adv,
    }

    getExecAdvs(data);
    return false;
});

$('body').on('click', '#targetTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }else {
        $('#targetTable tr.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#targetCheckedTable tbody').empty();
        let cloneRow = $(this).clone();
        cloneRow.find('td:last-child').remove();
        cloneRow.appendTo('#targetCheckedTable tbody');
    }
});

$('body').on('click', '.target-btn', function(e){
    e.stopPropagation();
    let targetId = $(this).closest('tr').data('id');
    let rowMedia = $(this).closest('tr').children('td').eq(0).text();
    let rowType = $(this).closest('tr').children('td').eq(1).text();
    let rowName = $(this).closest('tr').children('td').eq(3).text();
    let newRowIdNumber = $('#targetSelectTable tbody tr').length + 1;
    if ($('#targetSelectTable tbody td:contains("' + rowName + '")').length > 0) {
        alert("이미 같은 제목의 행이 적용 항목에 존재합니다.");
        return;
    }

    let clonedRow = $(this).closest('tr').clone();
    clonedRow.children('td:last').remove();
    clonedRow.find('td:last').append('<button class="set_target_except_btn"><i class="fa fa-times"></i></button>');
    clonedRow.removeClass('selected');
    clonedRow.attr('id', 'target-'+newRowIdNumber).appendTo('#targetSelectTable');

    var newExecText = '<p id="text-target-'+newRowIdNumber+'">* '+rowType+' - '+rowMedia+'<br>'+rowName+'</p>';
    $('#target-tab').append(newExecText);

    conditionStatusHide();
});

$('body').on('click', '#execTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }else {
        $(this).addClass('selected');
    }
    let selectedRows = $('#execTable tbody tr.selected');
    if(selectedRows.length > 0){
        selectedRows.each(function() {
            let media = $(this).find('td:first');
            let type = $(this).find('td:nth-child(2)');

            if (media.text() === '구글' && (type.text() === '광고그룹' || type.text() === '광고')) {
                $('#execConditionTable select').val('');
                $('#execConditionTable select[name=exec_condition_type] option[value=budget]').hide();
                $('#execConditionTable input[name=exec_condition_value]').val('').hide();
                $('#execConditionTable select[name=exec_condition_type_budget]').val('').hide();
                $('#execConditionTable select[name=exec_condition_value_status]').show();
            }
        });
    }else{
        $('#execConditionTable select[name=exec_condition_type] option').show();
        $('#execConditionTable input[name=exec_condition_value]').show();
        $('#execConditionTable select[name=exec_condition_type_budget]').show();
        $('#execConditionTable select[name=exec_condition_value_status]').val('').hide();
    }
});

    

$('body').on('click', '#targetTab li', function(){
    $('#targetTab li').removeClass('active');
    $(this).addClass('active');
    let $selectRow = $('#targetCheckedTable tbody tr').data('id');
    let $selectRowCount = $('#targetCheckedTable tbody tr').length;

    if($selectRowCount > 0){
        let adv = [];
        adv.push($selectRow);
        let data = {
        'tab': $('#targetTab li.active').data('tab'),
        'adv': adv
    }

        getTargetAdvs(data);
    }
})

$('body').on('click', '#execTab li', function(){
    $('#execTab li').removeClass('active');
    $(this).addClass('active');
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
        $(this).siblings('select[name=exec_condition_type_budget]').val('').hide();
        $(this).siblings('select[name=exec_condition_value_status]').show();
    }else{
        $(this).siblings('select[name=exec_condition_value_status]').val('').hide();  
        $(this).siblings('input[name=exec_condition_value]').show();   
        $(this).siblings('select[name=exec_condition_type_budget]').show();
    }
});

$('body').on('click', '#execConditionBtn', function() {
    var trs = $('#execTable tbody tr.selected');
    if(trs.length == 0){
        alert("항목을 선택해주세요.");
    }else{
        var execConditionType = $('#execConditionTable select[name=exec_condition_type]').val();
        var execConditionTypeText = $('#execConditionTable select[name=exec_condition_type] option:selected').text();
        var execConditionValue = '';
        var execConditionBudgetType = null;
        if(execConditionType == 'status'){
            execConditionValue = $('#execConditionTable select[name=exec_condition_value_status]').val();
        }else{
            execConditionValue = $('#execConditionTable input[name=exec_condition_value]').val();
            execConditionBudgetType = $('#execConditionTable select[name=exec_condition_type_budget]').val();
        }
        var selectedCount = trs.length;
        if(!execConditionType){
            alert("실행항목을 선택해주세요.");
            $('#execConditionTable select[name=exec_condition_type]').focus();
            return false;
        }

        if(!execConditionValue){
            alert("세부항목을 선택해주세요.");
            $('#execConditionTable select[name=exec_condition_value_status]').focus();
            $('#execConditionTable input[name=exec_condition_value]').focus();
            return false;
        }

        if((execConditionType != 'status') && (!execConditionBudgetType)){
            alert("단위를 선택해주세요");
            $('#execConditionTable select[name=exec_condition_type_budget]').focus();
            return false;
        }

        trs.each(function() {
            let execConditionBudgetTypeText = '';
            if(execConditionBudgetType == 'won'){
                execConditionBudgetTypeText = '원';
            }else if(execConditionBudgetType == 'percent'){
                execConditionBudgetTypeText = '%';
            }
            var tr = $(this);
            var trId = tr.data('id');
            var cloneRow = tr.clone();
            var newRowIdNumber = $('#execSelectTable tbody tr').length + 1;
            cloneRow.prepend('<td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2"></td>');
            cloneRow.append('<td>'+execConditionTypeText+'</td><td><span class="exec_value">'+execConditionValue+'</span><span class="exec_condition_select_budget_type">'+execConditionBudgetTypeText+'</span><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td>').attr('id', 'exec-'+newRowIdNumber).appendTo('#execSelectTable');

            var selectedMediaTd = tr.children('td').eq(0).text();
            var selectedTypeTd = tr.children('td').eq(1).text();
            var selectedNameTd = tr.children('td').eq(3).text();
            var selectedStatusTd = tr.children('td').eq(4).text();

            var newExecText = '<p id="text-exec-'+newRowIdNumber+'">* '+selectedTypeTd+' - '+selectedMediaTd+'<br>'+selectedNameTd+'<br>'+execConditionTypeText+' '+ execConditionValue+execConditionBudgetTypeText+'</p>';
            $('#preactice-tab').append(newExecText);
        })

        $('#execTable tbody tr').removeClass('selected');
        $('#execConditionTable select[name=exec_condition_type] option').show();
        $('#execConditionTable input[name=exec_condition_value]').show();
        $('#execConditionTable select[name=exec_condition_type_budget]').show();
        $('#execConditionTable select[name=exec_condition_value_status]').val('').hide();
    }
});

$('body').on('click', '.set_target_except_btn', function() {
    let rowId = $(this).closest('tr').attr('id');
    $(this).closest('tr').remove();
    $('#target-tab #text-'+rowId).remove();

    conditionStatusHide();
});

$('body').on('click', '.exec_condition_except_btn', function() {
    $(this).closest('tr').remove();
    let rowId = $(this).closest('tr').attr('id');
    $('#preactice-tab #text-'+rowId).remove();
});

$('body').on('click', '.deleteBtn', function() {
    $(this).closest('tr').remove();
    let rowId = $(this).closest('tr').attr('id');
    $('#condition-tab #text-'+rowId).remove();
});

$('body').on('focusout', '#detailTable input[name=subject]', function() {
    let detailTextSubject = $(this).val();
    $('#detailText #subjectText').text(detailTextSubject);
});

$('body').on('focusout', '#detailTable textarea[name=description]', function() {
    let detailTextDescription = $(this).val();
    $('#detailText #descriptionText').text(detailTextDescription);
});

$('body').on('click', '#createAutomationBtn', function() {
    if(validationData()){
        let procData = setProcData();
        $.ajax({
            type: "POST",
            url: "<?=base_url()?>/automation/create",
            data: procData,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    dataTable.draw();
                    $('#automationModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                var errorMessages = error.responseJSON.messages.msg;
                var firstErrorMessage = Object.values(errorMessages)[0];
                alert(firstErrorMessage);
            }
        });
    };
});

$('body').on('click', '#updateAutomationBtn', function() {
    if(validationData()){
        let procData = setProcData();
        procData.seq = $('input[name=seq]').val();
        $.ajax({
            type: "PUT",
            url: "<?=base_url()?>/automation/update",
            data: procData,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    dataTable.draw();
                    $('#automationModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                var errorMessages = error.responseJSON.messages.msg;
                var firstErrorMessage = Object.values(errorMessages)[0];
                alert(firstErrorMessage);
            }
        });
    };
});
//등록 부분 끝
//복제하기
$('body').on('click', '.copy-btn', function() {
    let seq = $(this).data('seq');
    $.ajax({
        type: "POST",
        url: "<?=base_url()?>/automation/copy",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                dataTable.draw();
                $('#automationModal').modal('hide');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});
//삭제하기
$('body').on('click', '.delete-btn', function() {
    let seq = $(this).data('seq');
    $.ajax({
        type: "DELETE",
        url: "<?=base_url()?>/automation/delete",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                dataTable.draw();
                $('#automationModal').modal('hide');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});
//로그
function getLogs(){
    logTable = $('#logTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "order": [[3,'desc']],
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/logs",
            "data": function(d) {
                d.stx = $('input[name=log_stx]').val();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columns": [
            { "data": "subject"},
            { "data": "nickname"},
            { 
                "data": "result",
                "render": function(data, type, row){
                    let result;
                    if(data == '실행됨'){
                        result = '<b class="em">'+data+'</b>';
                    }else if(data == '실패'){
                        result = '<b class="fail">'+data+'</b>';
                    }else{
                        result = data;
                    }

                    return result;
                }
            },
            { "data": "exec_timestamp"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
            let detailRow = '<h2>작업 세부 정보</h2>'+
            '<div class="detail-log p-1">'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">일정</dt>'+
                    '<dd>'+(data.schedule_desc ? data.schedule_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">대상</dt>'+
                    '<dd>'+(data.target_desc ? data.target_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">조건</dt>'+
                    '<dd>'+(data.conditions_desc ? data.conditions_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item">'+
                    '<dt class="mb-1">실행</dt>'+
                    '<dd>'+(data.executions_desc != null ? (Array.isArray(data.executions_desc) ? data.executions_desc.join('<br>') : data.executions_desc) : "")+'</dd>'+
                '</dl>'+
            '</div>';
            logTable.row(row).child(detailRow).hide();
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    });
}
//모달 보기
$('#logModal').on('show.bs.modal', function(e) {
    getLogs();
})//모달 닫기
.on('hidden.bs.modal', function(e) { 
    $('input[name=log_stx]').val('');
    logTable = $('#logTable').DataTable();
    logTable.destroy();
});

$('form[name="log-search-form"]').bind('submit', function() {
    logTable.draw();
    return false;
});

$('body').on('click', '#logModal tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = logTable.row(tr);

    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        row.child.hide();
        tr.removeClass('shown');
    }else {
        $(this).addClass('selected');
        row.child.show();
        tr.addClass('shown');
    }
});

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>