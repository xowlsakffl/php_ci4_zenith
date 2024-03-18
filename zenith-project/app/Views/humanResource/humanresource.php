<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 인사관리 / 시간차 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap dayOff-contanier">
    <div class="title-area">
        <h2 class="page-title">시간차 관리</h2>
        <button type="button" class="btn btn-dark ms-3" data-bs-toggle="modal" data-bs-target="#totalModal">전체조회</button>
    </div>
    <div class="row mt-5">
        <div class="col half">
            <h3 class="content-title">결재내역</h3>
            <div class="search-wrap">
                <form class="search d-flex justify-content-center">
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
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:10%">
                        <col>
                        <col style="width:20%">
                        <col style="width:17%">
                        <col style="width:15%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">작성자</th>
                            <th scope="col">작성일</th>
                            <th scope="col">문서번호</th>
                            <th scope="col">연차차감</th>
                            <th scope="col">발급쿠폰</th>
                            <th scope="col">결과</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="none">검색된 데이터가 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col half">
            <h3 class="content-title">신청현황</h3>
            <div class="search-wrap">
                <form class="search d-flex justify-content-center">
                    <div class="term d-flex align-items-center">
                        <input type="text" name="sdate" id="sdate" readonly="readonly">
                        <button type="button"><i class="bi bi-calendar2-week"></i></button>
                        <span> ~ </span>
                        <input type="text" name="edate" id="edate" readonly="readonly">
                        <button type="button"><i class="bi bi-calendar2-week"></i></button>
                    </div>
                    <div class="input">
                        <input class="" type="text" placeholder="검색어를 입력하세요">
                        <button class="btn-primary" type="submit">조회</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:20%">
                        <col>
                        <col style="width:30%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">날짜</th>
                            <th scope="col">쿠폰사용</th>
                            <th scope="col">사용시간</th>
                            <th scope="col">상태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <input type="text" class="form-control">
                                    <button type="button"><i class="bi bi-calendar2-week"></i></button>
                                </div>
                            </td>
                            <td></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <select class="form-select" aria-label="시간 선택">
                                        <option selected>-선택-</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                    <span class="m-1">시</span>
                                    <select class="form-select" aria-label="분 선택">
                                        <option selected>-선택-</option>
                                        <option value="1">00</option>
                                        <option value="2">10</option>
                                        <option value="3">20</option>
                                    </select>
                                    <span class="ms-1">분</span>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary">등록</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:15%">
                        <col>
                        <col style="width:20%">
                        <col style="width:15%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">사용자</th>
                            <th scope="col">쿠폰사용</th>
                            <th scope="col">사용일자</th>
                            <th scope="col">시간</th>
                            <th scope="col">결과</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="none">검색된 데이터가 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal');?>
<div class="modal fade" id="totalModal" tabindex="-1" aria-labelledby="totalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="totalModalLabel">전체 조회</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <form class="row g-1">
                        <div class="col-5 d-flex align-items-center">
                            <input type="text" class="form-control">
                            <button type="button"><i class="bi bi-calendar2-week"></i></button>
                            <span class="me-2"> ~ </span>
                            <input type="text" class="form-control">
                            <button type="button"><i class="bi bi-calendar2-week"></i></button>
                        </div>
                        <div class="col-7 d-flex">
                            <input type="password" class="form-control">
                            <button type="submit" class="btn btn-primary w-25 ms-2">조회</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-modal">
                        <colgroup>
                            <col>
                            <col style="width:11%;">
                            <col style="width:8%;">
                            <col style="width:10%;">
                            <col style="width:10%;">
                            <col style="width:8%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2" class="align-middle">사용자</th>
                                <th scope="col" colspan="2">검색기간</th>
                                <th scope="col" colspan="3">전체</th>
                            </tr>
                            <tr>
                                <th scope="col">발급</th>
                                <th scope="col">사용</th>
                                <th scope="col">발급</th>
                                <th scope="col">사용</th>
                                <th scope="col">잔여</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="none">검색된 데이터가 없습니다.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

setDate();

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
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>