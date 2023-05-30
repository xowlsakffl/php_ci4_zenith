<?=$this->extend('templates/front.php');?>
<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고주/광고대행사 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-dt/css/fixedHeader.dataTables.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<style>
    #ui-id-1{
        z-index: 10000000;
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
        <h2 class="page-title">광고주/광고대행사 관리</h2>
        <p class="title-disc">안하는 사람은 끝까지 할 수 없지만, 못하는 사람은 언젠가는 해 낼 수도 있다.</p>
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
            <button id="create-btn-modal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adv-create">생성</button>
        </div>
        <div class="row table-responsive">
            <table class="dataTable table table-striped table-hover table-default" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first" style="width:20px">#</th>
                        <th style="width:100px">소속대행사</th>
                        <th style="width:70px">타입</th>
                        <th style="width:100px">이름</th>
                        <th style="width:120px">전화번호</th>
                        <th style="width:100px">생성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!--수정/보기-->
        <div class="modal fade" id="adv-show" tabindex="-1" aria-labelledby="adv-show-label" aria-hidden="true">
            <div class="modal-dialog modal-lg sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="adv-show-label"><i class="bi bi-file-text"></i> <span class="title">광고주/광고대행사</span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <form name="modal-form">
                                <table class="table table-bordered table-modal" id="adv-show-table">
                                    <colgroup>
                                        <col style="width:20%;">
                                        <col style="width:10%;">
                                        <col style="width:23%;">
                                        <col style="width:23%;">
                                        <col style="width:14%;">
                                        <col style="width:10%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">소속대행사</th>
                                            <th scope="col">타입</th>
                                            <th scope="col">광고주명</th>
                                            <th scope="col">전화번호</th>
                                            <th scope="col">생성일</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="p_name">
                                                <input type="hidden" name="id">
                                                <input type="hidden" name="p_id">
                                                <input type="text" name="p_name"  class="form-control" id="show-p_name">
                                            </td>
                                            <td id="type">
                                                <span></span>
                                            </td>
                                            <td id="name">
                                                <input type="text" name="name" class="form-control">
                                            </td>
                                            <td id="tel">
                                                <input type="text" name="tel" class="form-control">
                                            </td>
                                            <td id="created_at">
                                                <span></span>
                                            </td>
                                            <td id="btns">
                                                <button class="btn btn-primary" id="modify_btn" type="submit">수정</button>
                                                <button class="btn btn-danger" id="delete_btn"  type="button">삭제</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--수정/보기-->
        <!--생성-->
        <div class="modal fade" id="adv-create" tabindex="-1" aria-labelledby="adv-create-label" aria-hidden="true">
            <div class="modal-dialog modal-lg sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="adv-create-label"><i class="bi bi-file-text"></i> <span class="title">광고주/광고대행사</span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <form name="modal-form-create">
                                <table class="table table-bordered table-modal" id="adv-create-table">
                                    <colgroup>
                                        <col style="width:20%;">
                                        <col style="width:10%;">
                                        <col style="width:23%;">
                                        <col style="width:23%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th scope="col">소속대행사</th>
                                            <th scope="col">타입</th>
                                            <th scope="col">광고주명</th>
                                            <th scope="col">전화번호</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="p_name">
                                                <input type="text" name="p_name"  class="form-control" id="create-p_name">
                                            </td>
                                            <td id="type">
                                                <select name="type" id="" class="form-control">
                                                    <option value="광고대행사">광고대행사</option>
                                                    <option value="광고주">광고주</option>
                                                </select>
                                            </td>
                                            <td id="name">
                                                <input type="text" name="name" class="form-control">
                                            </td>
                                            <td id="tel">
                                                <input type="text" name="tel" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary" id="create_btn" type="submit">생성</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--생성-->
    </div>
</div>
    <!--content-->
    <!-- <div class="container-md">
        <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">글수정</h5>
                </div>
                <div class="modal-body">
                    <form id="frm">
                        <input type="hidden" name="id" id="hidden_id">
                        <div class="form-group">
                            <label for="companyType">타입</label>
                            <select name="companyType" id="companyType" class="form-control">
                                <option value="" hidden selected disabled>선택</option>
                                <option value="광고대행사">광고대행사</option>
                                <option value="광고주">광고주</option>
                            </select>
                            <span id="companyTypeError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="companyName">이름</label>
                            <input type="text" name="companyName" class="form-control companyName" id="companyName">
                            <span id="companyNameError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="companyTel">전화번호</label>
                            <input type="text" name="companyTel" class="form-control companyTel" id="companyTel">
                            <span id="companyTelError" class="text-danger"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-primary" id="companyUpdateBtn">저장</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <dl>
                        <dt>소속 대행사</dt>
                        <dd id="companyBelong"></dd>
                    </dl>
                    <dl>
                        <dt>타입</dt>
                        <dd id="companyType"></dd>
                    </dl>
                    <dl>
                        <dt>이름</dt>
                        <dd id="companyName"></dd>
                    </dl>
                    <dl>
                        <dt>전화번호</dt>
                        <dd id="companyTel"></dd>
                    </dl>
                    <h5 class="mt-5">소속 유저 리스트</h5>
                    <div id="userListWrap">
                    </div>
                </div>
                <div class="modal-footer">          
                    <?php if(auth()->user()->inGroup('superadmin', 'admin', 'developer')){
                        echo '<a href="/company/belong" class="btn btn-primary" id="companyBelong">소속 수정</a>';
                    }?>  
                    <button type="button" class="btn btn-primary" id="companyUpdateModal">수정</button>
                    <button type="button" class="btn btn-danger" id="companyDelete">삭제</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalWrite" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">글쓰기</h5>
                </div>
                <div class="modal-body">
                    <form id="frm">
                        <div class="form-group">
                            <label for="companyType">타입</label>
                            <select name="companyType" id="companyType" class="form-control">
                                <option value="" hidden selected disabled>선택</option>
                                <option value="광고대행사">광고대행사</option>
                                <option value="광고주">광고주</option>
                            </select>
                            <span id="companyTypeError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="companyName">이름</label>
                            <input type="text" name="companyName" class="form-control companyName" id="companyName">
                            <span id="companyNameError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="companyTel">전화번호</label>
                            <input type="text" name="companyTel" class="form-control companyTel" id="companyTel">
                            <span id="companyTelError" class="text-danger"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-primary" id="companyInsertBtn">저장</button>
                </div>
                </div>
            </div>
        </div>
        <h1 class="font-weight-bold">광고주, 광고대행사 관리</h1>
        <div class="row mb-2 flex justify-content-end">
            <div class="col-5">
                <label for="fromDate">시작날짜</label>
                <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="날짜 선택" readonly="readonly">
                <label for="toDate">종료날짜</label>
                <input type="text" class="form-control" id="toDate" name="toDate" placeholder="날짜 선택" readonly="readonly">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col" id="allCount"></div>
            <div class="col">
                <select name="sort" id="sort" class="form-control">
                    <option value="recent">최근순</option>
                    <option value="old">오래된 순</option>
                </select>
            </div>
            <div class="col-3">
                <select name="pageLimit" id="pageLimit" class="form-control">
                    <option value="10">10개</option>
                    <option value="50">50개</option>
                    <option value="100">100개</option>
                </select>
            </div>
            <div class="col-3">
                <input type="text" class="form-control" id="search" name="search" placeholder="검색">
            </div>
        </div>
        <button id="DataResetBtn" class="btn btn-primary">초기화</button>
        <div class="row">
            <table class="table" id="companies">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">소속대행사</th>
                        <th scope="col">타입</th>
                        <th scope="col">이름</th>
                        <th scope="col">전화번호</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="row pagination-container">
                <ul class="pagination">

                </ul>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-end">
                <button class="btn btn-primary" id="companyNewBtn">광고주 생성</button>
            </div>
        </div>
    </div> -->
<?=$this->endSection();?>

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
    dataTable = $('#deviceTable').DataTable({
        "autoWidth": true,
        "columnDefs": [
            { targets: [0], orderable: false},
        ],
        "order": [[1,'desc']],
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
            { "data": "tel" },
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

function setCompanyShow(data) {
    $('#adv-show-title').text(data.name);
    $('#adv-show-table #p_name input[name="id"]').val(data.id);
    $('#adv-show-table #p_name input[type="text"]').val(data.p_name);
    $('#adv-show-table #type span').text(data.type);
    $('#adv-show-table #name input').val(data.name);
    $('#adv-show-table #tel input').val(data.tel);
    $('#adv-show-table #created_at span').text(data.created_at.substr(0, 10));
    $('#adv-show-table #btns #delete_btn').val(data.id);
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
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function autoComplete(inputId){
    $(inputId).autocomplete({
        source : function(request, response) {
            $.ajax({
                url : "/company/get-agencies", 
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
$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('#adv-show').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var id = $btn.data('id');
    $('form[name="modal-form"]')[0].reset();
    $('#adv-show-table tbody tr td span').text('');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/company/get-company",
        data: {'id': id},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setCompanyShow(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

$('#show-p_name').on("focus", function(){
    autoComplete("#show-p_name");
})

$('#create-p_name').on("focus", function(){
    autoComplete("#create-p_name");
})


$('form[name="modal-form"]').bind('submit', function() {
    var data = $(this).serialize();
    updateCompany(data);
    $('#adv-show').modal('hide');
    return false;
});

$('form[name="modal-form-create"]').bind('submit', function() {
    var data = $(this).serialize();
    console.log(data);
    createCompany(data);
    $('#adv-create').modal('hide');
    return false;
});

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
