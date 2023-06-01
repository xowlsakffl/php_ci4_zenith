<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 회원 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-dt/css/fixedHeader.dataTables.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<style>
    .ui-autocomplete{
        z-index: 10000000;
        max-height: 300px;
        overflow-y: auto; /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
        
    hr{
        display: block !important;
    }

    .ui-widget{
        font-family: "NanumSquareNeo", "Noto Sans", dotum, Gulim, sans-serif;
    }
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">회원 관리</h2>
        <p class="title-disc">혼자서는 작은 한 방울이지만 함께 모이면 바다를 이룬다.</p>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate" autocomplete="off">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate" autocomplete="off">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
            </div>
        </form>
    </div>

    <div class="position-relative">
        <div class="row table-responsive">
            <table class="dataTable table table-striped table-hover table-default" id="userTable">
                <colgroup>
                        <col style="width:5%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        <col style="width:25%;">
                        <col style="width:10%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">소속</th>
                        <th scope="col">아이디</th>
                        <th scope="col">이메일</th>
                        <th scope="col">상태</th>
                        <th scope="col">권한</th>
                        <th scope="col">생성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="modal fade" id="disbursementModal" tabindex="-1" aria-labelledby="disbursementModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="disbursementModalLabel">지출결의서</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="approval">
                            <ol class="d-flex">
                                <li>
                                    <span>담당자</span>
                                    <div></div>
                                </li>
                                <li>
                                    <span>경영지원실장</span>
                                    <div></div>
                                </li>
                                <li>
                                    <span>사업부대표</span>
                                    <div></div>
                                </li>
                            </ol>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-left-header">
                                <colgroup>
                                    <col style="width:30%;">
                                    <col style="width:70%;">
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <th scope="row">담당자</th>
                                        <td>
                                            <select class="form-select" aria-label="담당자 선택">
                                                <option selected>-선택-</option>
                                                <option value="1">One</option>
                                                <option value="2">Two</option>
                                                <option value="3">Three</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">문서번호</th>
                                        <td>
                                            <textarea></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">거래처명(예금주명)</th>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">은행명</th>
                                        <td>
                                            <input type="text" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">계좌번호</th>
                                        <td>
                                            <input type="text" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">구분</th>
                                        <td>
                                            <select class="form-select" aria-label="구분 선택">
                                                <option selected>-선택-</option>
                                                <option value="1">One</option>
                                                <option value="2">Two</option>
                                                <option value="3">Three</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">내역(자세히)</th>
                                        <td>
                                            <textarea></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">총금액(VAT 포함)</th>
                                        <td>
                                            <input type="text" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">비고</th>
                                        <td>
                                            <textarea></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">작성완료</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- //업체 등록 -->
        <!--사용자 수정-->
        <div class="modal fade" id="user-show" tabindex="-1" aria-labelledby="user-show-label" aria-hidden="true">
            <div class="modal-dialog modal-lg sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="user-show-label"><i class="bi bi-file-text"></i> <span class="title">회원 관리</span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <form name="user-show-form">
                                <h2 class="body-title">회원정보 수정</h2>
                                <table class="table table-bordered table-left-header">
                                    <colgroup>
                                        <col style="width:30%;">
                                        <col style="width:70%;">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th scope="row">소속</th>
                                            <td>
                                                <input type="hidden" name="company_id">
                                                <input type="text" class="form-control" id="belongCompany" placeholder="광고주/광고대행사 검색">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">아이디</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">이메일</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">상태</th>
                                            <td>
                                                <select class="form-select" aria-label="상태 선택">
                                                    <option selected hidden>-선택-</option>
                                                    <option value="ON">활성</option>
                                                    <option value="OFF">비활성</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">권한</th>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="superadmin" class="form-check-input" id="superadmin"> 
                                                    <label for="superadmin" class="form-check-label">최고관리자</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="admin" class="form-check-input" id="admin"> 
                                                    <label for="admin" class="form-check-label">관리자</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="developer" class="form-check-input" id="developer"> 
                                                    <label for="developer" class="form-check-label">개발자</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="agency" class="form-check-input" id="agency"> 
                                                    <label for="agency" class="form-check-label">광고대행사</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="advertiser" class="form-check-input" id="advertiser"> 
                                                    <label for="advertiser" class="form-check-label">광고주</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="user" class="form-check-input" id="userCheck"> 
                                                    <label for="userCheck" class="form-check-label">사용자</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="group" value="guest" class="form-check-input" id="guest"> 
                                                    <label for="guest" class="form-check-label">게스트</label>
                                                </div>                                    
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">세부권한</th>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="admin.access" class="form-check-input" id="adminAccess"> 
                                                    <label for="adminAccess" class="form-check-label">관리자만 가능한 페이지 접근 가능</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="admin.settings" class="form-check-input" id="adminSettings"> 
                                                    <label for="adminSettings" class="form-check-label">관리자만 가능한 설정 접근 가능</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="users.create" class="form-check-input" id="usersCreate"> 
                                                    <label for="usersCreate" class="form-check-label">회원 생성</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="users.edit" class="form-check-input" id="usersEdit"> 
                                                    <label for="usersEdit" class="form-check-label">회원 수정</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="users.delete" class="form-check-input" id="usersDelete"> 
                                                    <label for="usersDelete" class="form-check-label">회원 삭제</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="agency.access" class="form-check-input" id="agencyAccess"> 
                                                    <label for="agencyAccess" class="form-check-label">대행사 목록 페이지</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="agency.advertisers" class="form-check-input" id="agencyAdvertisers"> 
                                                    <label for="agencyAdvertisers" class="form-check-label">대행사 하위 광고주 관리</label>
                                                </div> 
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="agency.create" class="form-check-input" id="agencyCreate"> 
                                                    <label for="agencyCreate" class="form-check-label">대행사 생성</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="agency.edit" class="form-check-input" id="agencyEdit"> 
                                                    <label for="agencyEdit" class="form-check-label">대행사 수정</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="agency.delete" class="form-check-input" id="agencyDelete"> 
                                                    <label for="agencyDelete" class="form-check-label">대행사 삭제</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="advertiser.access" class="form-check-input" id="advertiserAccess"> 
                                                    <label for="advertiserAccess" class="form-check-label">광고주 목록 페이지</label>
                                                </div> 
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="advertiser.create" class="form-check-input" id="advertiserCreate"> 
                                                    <label for="advertiserCreate" class="form-check-label">광고주 생성</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="advertiser.edit" class="form-check-input" id="advertiserEdit"> 
                                                    <label for="advertiserEdit" class="form-check-label">광고주 수정</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permission" value="advertiser.delete" class="form-check-input" id="advertiserDelete"> 
                                                    <label for="advertiserDelete" class="form-check-label">광고주 삭제</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">작성완료</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
let data = {};
let dataTable;

setDate();
getUserList();

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    return data;
}

function getUserList(){
    dataTable = $('#userTable').DataTable({
        "autoWidth": true,
        "columnDefs": [
            { targets: [0], orderable: false},
        ],
        "order": [[6, 'desc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "deferRender": false,
        "lengthMenu": [
            [ 25, 10, 50, -1 ],
            [ '25개', '10개', '50개', '전체' ]
        ],
        "ajax": {
            "url": "<?=base_url()?>/user/get-users",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": null },
            { "data": "belong" },
            { "data": "username"},
            { "data": "email" },
            { "data": "status" },
            { "data": "group" },
            { 
                "data": "created_at",
                "render": function(data){
                    return data.substr(0, 10);
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-user-id", data.user_id);
            $(row).attr("data-bs-toggle", "modal");
            $(row).attr("data-bs-target", "#user-show");
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var startIndex = api.page() * api.page.len();
            var seq = startIndex + index + 1;
            $('td:eq(0)', row).html(seq);
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },  
    });
}

function setDate(){
    var today = new Date();
    var startDate = null;
    var endDate = null;
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
        maxDate: moment().endOf('day').toDate(),
        autoUpdateInput: false,
        ranges: {
            '오늘': [today, today],
            '어제': [new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1), new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1)],
            '지난 일주일': [new Date(today.getFullYear(), today.getMonth(), today.getDate() - 6), today],
            '지난 한달': [new Date(today.getFullYear(), today.getMonth(), today.getDate() - 29), today],
            '이번달': [new Date(today.getFullYear(), today.getMonth(), 1), new Date(today.getFullYear(), today.getMonth() + 1, 0)]
        }
    }, function(start, end, label) {
        // Lets update the fields manually this event fires on selection of range
        startDate = start.format('YYYY-MM-DD'); // selected start
        endDate = end.format('YYYY-MM-DD'); // selected end

        checkinInput = $('#sdate');
        checkoutInput = $('#edate');

        // Updating Fields with selected dates
        checkinInput.val(startDate);
        checkoutInput.val(endDate);

        // Setting the Selection of dates on calender on CHECKOUT FIELD (To get this it must be binded by Ids not Calss)
        var checkOutPicker = checkoutInput.data('daterangepicker');
        checkOutPicker.setStartDate(startDate);
        checkOutPicker.setEndDate(endDate);

        // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
        var checkInPicker = checkinInput.data('daterangepicker');
        checkInPicker.setStartDate(checkinInput.val(startDate));
        checkInPicker.setEndDate(endDate);
    });
}

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
