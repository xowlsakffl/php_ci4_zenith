<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 이벤트 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">이벤트 관리</h2>
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
                <button class="btn-special ms-2" type="button" data-bs-toggle="modal" data-bs-target="#regiModal">등록</button>
            </div>
        </form>
    </div>

    <div class="section">
        <div class="btn-wrap text-end mb-2">
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-danger">광고주 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-danger">전환 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default"  id="event-table">
                <!-- <colgroup>
                    <col style="width:6%">
                    <col style="width:10%">
                    <col style="width:5%">
                    <col>
                    <col style="width:12%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                </colgroup> -->
                <thead class="table-dark">
                    <tr>
                        <th scope="col">이벤트번호</th>
                        <th scope="col">광고주</th>
                        <th scope="col">매체</th>
                        <th scope="col">브라우저 타이틀</th>
                        <th scope="col">이벤트 구분</th>
                        <th scope="col">외부연동</th>
                        <th scope="col">사용여부</th>
                        <th scope="col">유입수</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">DB단가</th>
                        <th scope="col">작성자</th>
                        <th scope="col">작업자</th>
                        <th scope="col">작성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal');?>
<!-- 이벤트 등록 -->
<div class="modal fade" id="regiModal" tabindex="-1" aria-labelledby="regiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="regiModalLabel">이벤트 등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h2 class="body-title">이벤트 정보</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">랜딩번호</th>
                                <td>6124</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩주소</th>
                                <td>
                                    <a href="#">https://event.asdkljad.com</a>
                                    <button type="button" class="btn btn-secondary btn-sm">복사하기</button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">광고주</th>
                                <td><input type="text" class="form-control" value="예쁨주의상상의원" disabled></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">매체</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">이벤트 구분</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">DB 단가</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩구분</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio01">
                                            <label class="form-check-label" for="landing_radio01">일반</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio02">
                                            <label class="form-check-label" for="landing_radio02">잠재고객</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio03">
                                            <label class="form-check-label" for="landing_radio03">비즈폼</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio04">
                                            <label class="form-check-label" for="landing_radio04">엑셀업로드</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio05">
                                            <label class="form-check-label" for="landing_radio05">API수신</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">외부연동 사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio01">
                                            <label class="form-check-label" for="linkage_radio01">사용</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio02">
                                            <label class="form-check-label" for="linkage_radio02">미사용</label>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <input type="text" class="form-control me-2">
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="d-flex">
                                        <input type="text" class="form-control me-2">
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩 고정값</th>
                                <td>
                                    <div class="d-flex mb-2">
                                        <select class="form-select me-2" aria-label="선택">
                                            <option selected>-선택-</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="d-flex mb-2">
                                        <select class="form-select me-2" aria-label="선택">
                                            <option selected>-선택-</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                        <input type="text" class="form-control">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm float-end">추가</button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">브라우저 제목(타이틀)</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">키워드</th>
                                <td>
                                    <input type="text" class="form-control">
                                    <p class="mt-2 text-secondary">※ 이벤트의 핵심 키워드를 입력해주세요.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">설명</th>
                                <td><input type="text" class="form-control" placeholder="이벤트를 구체적으로 설명해주세요. (약 40자내외, 최대 100자이내)"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집목적</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집항목</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">페이스북 Pixel ID</th>
                                <td><input type="text" class="form-control" placeholder="페이스북 픽셀을 사용하는 경우 체인쏘우에 등록된 픽셀ID를 입력해 주세요."></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">View Script</th>
                                <td><textarea placeholder="추적스크립트(View)"></textarea></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">Complete Script</th>
                                <td><textarea placeholder="추적스크립트(Complete)"></textarea></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio01">
                                            <label class="form-check-label" for="linkage_radio01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio02">
                                            <label class="form-check-label" for="linkage_radio02">미사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 광고주가 사용중지로 되어있을 경우 랜딩은 노출되지 않습니다.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>

                <h2 class="body-title">이벤트 정보</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">성별</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio01">
                                            <label class="form-check-label" for="gender_radio01">무관</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio02">
                                            <label class="form-check-label" for="gender_radio02">남</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio03">
                                            <label class="form-check-label" for="gender_radio03">여</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">나이</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control">
                                        <span class="m-2">~</span>
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">중복기간</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio01">
                                            <label class="form-check-label" for="term_radio01">1일</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio02">
                                            <label class="form-check-label" for="term_radio02">1주</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio03">
                                            <label class="form-check-label" for="term_radio03">1개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio04">
                                            <label class="form-check-label" for="term_radio04">2개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio05">
                                            <label class="form-check-label" for="term_radio05">3개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio06">
                                            <label class="form-check-label" for="term_radio06">4개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio07">
                                            <label class="form-check-label" for="term_radio07">5개월</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio08">
                                            <label class="form-check-label" for="term_radio08">전체</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 중복기간 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">전화번호 중복</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="phone_radio" id="phone_radio01">
                                            <label class="form-check-label" for="phone_radio01">미사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="phone_radio" id="phone_radio02">
                                            <label class="form-check-label" for="phone_radio02">사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 전화번호 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">이름 중복</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="name_radio" id="name_radio01">
                                            <label class="form-check-label" for="name_radio01">미사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="name_radio" id="name_radio02">
                                            <label class="form-check-label" for="name_radio02">사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 이름 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사전 중복 체크</th>
                                <td>
                                    <select class="form-select me-2" aria-label="선택">
                                        <option selected>-선택-</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">복사</button>
                <button type="button" class="btn btn-danger">삭제</button>
                <button type="button" class="btn btn-secondary">목록</button>
                <button type="button" class="btn btn-primary">목록 수정하기</button>
            </div>
        </div>
    </div>
</div>
<!-- //이벤트 등록 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let data = {};
let dataTable;

setDate();
getList()

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    return data;
}

function getList(){
    dataTable = $('#event-table').DataTable({
        "autoWidth": false,
        "order": [[0,'desc']],
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
        "lengthMenu": [
            [ 25, 10, 50, -1 ],
            [ '25개', '10개', '50개', '전체' ]
        ],
        "ajax": {
            "url": "<?=base_url()?>/eventmanage/event/data",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { 
                "data": "seq",
                "width": "6%",
                "render": function(data, type, row) {
                    config = '';
                    if(row.config == 'disabled'){
                        config = '<span class="ads_status '+row.config+'" title="광고 비활성화">X</span>';
                    }

                    if(row.config == 'enabled'){
                        config = '<span class="ads_status '+row.config+'" title="광고 운영중">O</span>';
                    }
                    return config+'<button data-clipboard-text="https://event.hotblood.co.kr/'+data+'">'+data+'</button>';
                }
            },
            { 
                "data": "advertiser_name", 
                "width": "10%",
                "render": function(data, type, row) {
                    return '<a href="">'+data+'</a>'+'<a href="https://event.hotblood.co.kr/'+row.seq+'" data-filename="v_'+row.seq+'">[랜딩보기]</a>';
                }
            },
            { "data": "media_name", "width": "5%"},
            { "data": "title", "width": "23%"},
            { "data": "description","width": "12%"},
            { "data": "interlock", "width": "5%"},
            { "data": "is_stop","width": "5%"},
            { "data": "impressions","width": "5%"},
            { "data": "db","width": "5%"},
            { "data": "db_price","width": "8%"},
            { "data": "mb_name", "width": "5%"},
            { 
                "data": "mantis", "width": "5%",
                "render": function(data, type, row) {
                    name = '';
                    if(data.designer){
                        name += data.designer+" / ";
                    }
                    if(data.developer){
                        name += data.developer;
                    }
                    return '<a href="https://mantis.chainsaw.co.kr/view.php?id='+data.id+'">'+name+'</a>';
                }
            },
            { 
                "data": "ei_datetime", 
                "width": "5%",
                "render": function(data){
                    return data.substr(0, 10);
                }
            }
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        }
    });
}

function setDate(){
    $('#sdate, #edate').val(today);
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
