<?=$this->extend('templates/front.php');?>
<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고주/광고대행사 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
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
    .modal .dataTable{
        width: 100% !important;
    }
</style>
<?=$this->endSection();?>
<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap company-contaniner">
    <div class="title-area">
        <h2 class="page-title">광고주/광고대행사 관리</h2>
        <!-- <p class="title-disc">내가 더 멀리 보았다면 이는 거인들의 어깨 위에 올라 서있었기 때문이다.</p> -->
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
        <div id="create-button-wrap" class="position-absolute top-0 end-0" style="z-index:1">
            <button id="create-btn-modal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adv-create">등록</button>
        </div>
        <div class="row table-responsive">
            <table class="dataTable table table-striped table-hover table-default" id="deviceTable">
                <colgroup>
                        <col style="width:5%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        <col style="width:35%;">
                        <col style="width:20%;">
                        <col style="width:10%;">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">소속대행사</th>
                        <th scope="col">타입</th>
                        <th scope="col">이름</th>
                        <th scope="col">작성자</th>
                        <th scope="col">생성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!--광고대행사 정보, 소속 수정-->
        <div class="modal fade" id="adv-show" tabindex="-1" aria-labelledby="adv-show-label" aria-hidden="true">
            <div class="modal-dialog modal-lg sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="adv-show-label"><i class="bi bi-file-text"></i> <span class="title">광고주/광고대행사</span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <h2 class="body-title">광고대행사/광고주 정보 수정</h2>
                            <form name="adv-show-form">
                                <table class="table table-bordered table-modal" id="adv-show-table">
                                    <colgroup>
                                        <col style="width:12%;">
                                        <col style="width:15%;">
                                        <col style="width:20%;">
                                        <col style="width:20%;">
                                        <col style="width:16%;">
                                        <col style="width:*;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">타입</th>
                                            <th scope="col">소속대행사</th>
                                            <th scope="col">광고주명</th>
                                            <th scope="col">작성자</th>
                                            <th scope="col">생성일</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>                                       
                                            <td id="type">
                                                <span></span>
                                            </td>
                                            <td id="p_name">
                                                <input type="hidden" name="id">
                                                <input type="hidden" name="p_id">
                                                <input type="text" name="p_name"  class="form-control" id="show-p_name" autocomplete="off">
                                            </td>
                                            <td id="name">
                                                <input type="text" name="name" class="form-control">
                                            </td>
                                            <td id="nickname">
                                                <span></span>
                                            </td>
                                            <td id="created_at">
                                                <span></span>
                                            </td>
                                            <td id="btns" class="d-flex">
                                                <button class="btn btn-primary" id="modify_btn" type="submit">수정</button>
                                                <button class="btn btn-danger" id="delete_btn"  type="button">삭제</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <hr class="my-5">
                        <div>
                            <h2 class="body-title">매체별 연결 광고주</h2>
                            <form name="adaccount-form">
                                <table class="table table-bordered table-modal" id="adaccount-table">
                                    <colgroup>
                                        <col style="width:100%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">연결 광고주 검색/추가</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="adaccount" class="d-flex">
                                                <input type="hidden" name="company_id">
                                                <input type="hidden" name="ad_account_id">
                                                <input type="text" name="ad_account_name"  class="form-control" id="show-adaccount" autocomplete="off">
                                                <input type="text" style="display:none">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                            <!--매체별 연결 광고주 리스트-->
                            <table class="dataTable table table-striped table-hover adAccountListTable" id="adAccountListTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="first">#</th>
                                        <th>아이디</th>
                                        <th>매체</th>
                                        <th>광고주 이름</th>
                                        <th>상태</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <!-- 매체별 연결 광고주 리스트-->
                        </div>                
                        <hr class="my-5">
                        <div>
                            <h2 class="body-title">소속 사용자</h2>
                            <form name="belong-user-form">
                                <table class="table table-bordered table-modal" id="belong-user-table">
                                    <colgroup>
                                        <col style="width:100%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">사용자 검색/추가</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="username" class="d-flex">
                                                <input type="hidden" name="company_id">
                                                <input type="hidden" name="user_id">
                                                <input type="text" name="username"  class="form-control" id="show-user-name" autocomplete="off">
                                                <input type="text" style="display:none">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                            <!--유저리스트-->
                            <table class="dataTable table table-striped table-hover" id="userTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="first">#</th>
                                        <th>아이디</th>
                                        <th>이름</th>
                                        <th>생성일</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <!--유저리스트-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--광고대행사 정보, 소속 수정-->
        <!--광고대행사 생성-->
        <div class="modal fade" id="adv-create" tabindex="-1" aria-labelledby="adv-create-label" aria-hidden="true">
            <div class="modal-dialog modal-lg sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="adv-create-label"><i class="bi bi-file-text"></i> <span class="title">광고주/광고대행사</span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <form name="adv-create-form">
                                <table class="table table-bordered table-modal" id="adv-create-table">
                                    <colgroup>
                                        <col style="width:30%;">
                                        <col style="width:30%;">
                                        <col style="width:40%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">타입</th>
                                            <th scope="col">소속대행사</th>
                                            <th scope="col">광고주명</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="type">
                                                <select name="type" id="" class="form-select">
                                                    <option value="광고대행사">광고대행사</option>
                                                    <option value="광고주">광고주</option>
                                                </select>
                                            </td>
                                            <td id="p_name">
                                                <input type="text" name="p_name"  class="form-control" id="create-p_name" autocomplete="off">
                                            </td>
                                            <td id="name">
                                                <input type="text" name="name" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="float-end"><button class="btn btn-primary" id="create_btn" type="submit">생성</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--생성-->
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
let data = {};
let dataTable;
let userTable;
let adaccountTable;
let companyId;

setDate();
getCompanyList();

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    return data;
}

function getCompanyList(){
    dataTable = $('#deviceTable').DataTable({
        "autoWidth": true,
        "order": [[5, 'desc']],
        "columnDefs": [
            { targets: [0], orderable: false},
        ],
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
            "url": "<?=base_url()?>/company/get-companies",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": null },
            { "data": "p_name"},
            { "data": "type"},
            { "data": "name" },
            { "data": "nickname" },
            { 
                "data": "created_at",
                "render": function(data){
                    return data.substr(0, 10);
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
            $(row).attr("data-bs-toggle", "modal");
            $(row).attr("data-bs-target", "#adv-show");
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },  
    });
}

function getBelongUsers(){
    userTable = $('#userTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "deferRender": false,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/company/get-belong-users",
            "data": {"company_id": companyId},
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res;
            }
        },
        "columns": [
            { "data": null, "width": "10%"},
            { "data": "username", "width": "30%"},
            { "data": "nickname", "width": "30%"},
            { 
                "data": "created_at",
                "width": "20%",
                "render": function(data){
                    return data.substr(0, 10);
                }
            },
            { 
                "data": "null",
                "width": "10%",
                "render": function(){
                    return '<button class="btn btn-danger" id="exceptUserBelongBtn">제외</button>';
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
        },
    });
}

function getCompanyAdAccounts(){
    adaccountTable = $('#adAccountListTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "deferRender": false,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/company/get-company-adaccounts",
            "data": {"company_id": companyId},
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res;
            }
        },
        "columns": [
            { "data": null, "width": "8%"},
            { "data": "accountId", "width": "20%"},
            { 
                "data": "media",
                "width": "20%",
                "render": function(data){
                    switch (data) {
                        case 'facebook':
                            media = '페이스북';
                        break;
                        case 'google':
                            media = '구글';
                        break;
                        case 'kakao':
                            media = '카카오';
                        break;
                        default:
                            break;
                    }
                    return media;
                }
            },
            { "data": "name", "width": "30%"},
            { "data": "status", "width": "10%"},
            { 
                "data": "null",
                "width": "10%",
                "render": function(){
                    return '<button class="btn btn-danger" id="exceptAdAccountBtn">제외</button>';
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-accountid", data.media+"_"+data.accountId);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
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

function setCompanyShow(data) {
    $('#adv-show-title').text(data.name);
    $('#adv-show-table #p_name input[name="id"]').val(data.id);
    $('#adv-show-table #p_name input[type="text"]').val(data.p_name);
    $('#adv-show-table #type span').text(data.type);
    $('#adv-show-table #name input').val(data.name);
    $('#adv-show-table #nickname span').text(data.nickname);
    $('#adv-show-table #created_at span').text(data.created_at.substr(0, 10));
    $('#adv-show-table #btns #delete_btn').val(data.id);
    $('#belong-user-table #username input[name="company_id"]').val(data.id);
    $('#adaccount-table #adaccount input[name="company_id"]').val(data.id);
}

function updateCompany(data){
    $.ajax({
        url : "/company/set-company", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("변경되었습니다.");
                $('#adv-show').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function createCompany(data){
    $.ajax({
        url : "/company/create-company", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#adv-create').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function addBelongUser(data){
    $.ajax({
        url : "/company/set-belong-user", 
        type : "put", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){       
                alert("추가되었습니다.");
                userTable.draw();
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function addAdAccount(data){
    $.ajax({
        url : "/company/set-adaccounts", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){       
                alert("추가되었습니다.");
                adaccountTable.draw();
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function getAgencies(inputId){
    $(inputId).autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "/company/get-search-agencies", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(
                        $.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                            };
                        })
                    );
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        }
        ,focus : function(event, ui) {	
            return false;
        },
        minLength: 1,
        autoFocus : true,
        delay: 100
    });
}

function getUsers(){
    $('#show-user-name').autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "/company/get-search-users", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(
                        $.map(data, function(item) {
                            return {
                                label: item.username,
                                value: item.username,
                                id: item.id
                            };
                        })
                    );
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        },
        select: function(event, ui) {
            $(event.target).val(ui.item.value);
            $(event.target).siblings('input[name="user_id"]').val(ui.item.id);
            $(event.target).closest('form[name="belong-user-form"]').trigger('submit');
        },
        focus : function(event, ui) {	
            return false;
        },
        minLength: 1,
        autoFocus : true,
        delay: 100
    });
}

function getAdAccounts(){
    $('#show-adaccount').autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "/company/get-search-adaccounts", 
                type : "GET", 
                dataType: "JSON", 
                data : {'stx': request.term}, 
                contentType: 'application/json; charset=utf-8',
                success : function(data){
                    response(
                        $.map(data, function(item) {
                            switch (item.media) {
                                case 'facebook':
                                    media = '페이스북';
                                break;
                                case 'google':
                                    media = '구글';
                                break;
                                case 'kakao':
                                    media = '카카오';
                                break;
                                default:
                                    break;
                            }
                            return {
                                label: media+" / "+item.account_id+" / "+item.name+" / "+item.status,
                                value: item.name,
                                id: item.media+"_"+item.account_id,
                            };
                        })
                    );
                }
                ,error : function(){
                    alert("에러 발생");
                }
            });
        },
        select: function(event, ui) {
            $(event.target).val(ui.item.value);
            $(event.target).siblings('input[name="ad_account_id"]').val(ui.item.id);
            $(event.target).closest('form[name="adaccount-form"]').trigger('submit');
        },
        focus : function(event, ui) {	
            return false;
        },
        minLength: 2,
        autoFocus : true,
        delay: 100
    });
}

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('#adv-create').on('hidden.bs.modal', function(e) { //modal Reset
    $('form[name="adv-create-form"]')[0].reset();
});

$('#adv-show').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var id = $btn.data('id');
    $(this).attr('data-id', id);
    companyId = id;
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/company/get-company",
        data: {'id': id},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setCompanyShow(data);
            getBelongUsers(companyId); 
            getCompanyAdAccounts(companyId); 
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})
.on('hidden.bs.modal', function(e) { //modal Reset
    companyId = '';
    $(this).removeAttr('data-id');
    $('#userTable').DataTable().destroy(); 
    $('#adAccountListTable').DataTable().destroy();
    $('#adAccountListTable tbody').empty();
    $('#userTable tbody').empty(); 
    $('form[name="adv-show-form"]')[0].reset();
    $('form[name="adaccount-form"]')[0].reset();
    $('form[name="belong-user-form"]')[0].reset();
    $('#adv-show-table tbody tr td span').text('');
});

$('#show-p_name').on("focus", function(){
    getAgencies("#show-p_name");
})

$('#create-p_name').on("focus", function(){
    getAgencies("#create-p_name");
})

$('#show-user-name').on("focus", function(){
    getUsers();
})

$('#show-adaccount').on("focus", function(){
    getAdAccounts();
})

$('form[name="adv-show-form"]').bind('submit', function() {
    var data = $(this).serialize();
    updateCompany(data);
    return false;
});

$('form[name="adv-create-form"]').bind('submit', function() {
    var data = $(this).serialize();
    createCompany(data);
    return false;
});

$('form[name="belong-user-form"]').bind('submit', function() {
    var data = $(this).serialize();
    addBelongUser(data);
    return false;
});

$('form[name="adaccount-form"]').bind('submit', function() {
    var data = $(this).serialize();
    addAdAccount(data);
    return false;
});

$('body').on('click', '#exceptUserBelongBtn', function(){
    data = {
        'company_id': $('#adv-show').attr('data-id'),
        'user_id': $(this).closest('tr').attr('data-id'),
    };

    if(confirm('현재 소속에서 제외하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/company/except-belong-user",
            dataType: "JSON",
            data : data, 
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                if(data == true){
                    getBelongUsers(companyId); 
                }
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
});

$('body').on('click', '#exceptAdAccountBtn', function(){
    data = {
        'company_id': $('#adv-show').attr('data-id'),
        'ad_account_id': $(this).closest('tr').attr('data-accountid'),
    };

    if(confirm('현재 소속에서 제외하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/company/except-company-adaccount",
            dataType: "JSON",
            data : data, 
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                if(data == true){
                    getCompanyAdAccounts(companyId); 
                }
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
});

$('body').on('click', '#exceptUserBelongBtn', function(){
    data = {
        'company_id': $('#adv-show').attr('data-id'),
        'user_id': $(this).closest('tr').attr('data-id'),
    };

    if(confirm('현재 소속에서 제외하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/company/except-belong-user",
            dataType: "JSON",
            data : data, 
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                if(data == true){
                    getBelongUsers(companyId); 
                }
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
})

$('body').on('click', '#delete_btn', function(){
    let id = $(this).val();
    if(confirm('정말 삭제하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/company/delete-company",
            dataType: "JSON",
            data : {'id': id}, 
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                if(data == true){
                    dataTable.draw();
                    alert("삭제되었습니다.");
                    $('#adv-show').modal('hide');
                }
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
})

</script>
<?=$this->endSection();?>
<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
