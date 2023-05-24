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
                <input type="text" name="sdate" id="sdate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
            </div>
        </form>
    </div>

    <div>
        <div class="search-wrap my-5">
            <div class="statusCount detail d-flex flex-wrap"></div>     
        </div>

        <div class="row table-responsive">
            <table class="dataTable table table-striped table-hover table-default" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first" style="width:20px">#</th>
                        <th>소속대행사</th>
                        <th style="width:50px">타입</th>
                        <th>이름</th>
                        <th>전화번호</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
    <!--content-->
    <div class="container-md">
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
    </div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

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
        "fixedHeader": true,
        "deferRender": false,
        "lengthMenu": [
            [ 25, 10, 50, -1 ],
            [ '25', '10', '50', '전체' ]
        ],
        "ajax": {
            "url": "<?=base_url()?>/companies",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": null },
            { "data": "seq", "name": "seq" },
            { 
                "data": "info_seq",
                "render": function(data) {
                    return data;
                }
            },
            { "data": "advertiser" },
            { "data": "media" },
            { "data": "tab_name" },
        ],
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

function setPaging(xhr){
    if(xhr.pager.pageCount == 0){
        xhr.pager.pageCount = 1;
    }
    $('.pagination').twbsPagination('destroy');
    $('.pagination').twbsPagination({
        totalPages: xhr.pager.pageCount,	// 총 페이지 번호 수
        visiblePages: 5,	// 하단에서 한번에 보여지는 페이지 번호 수
        startPage : xhr.pager.currentPage, // 시작시 표시되는 현재 페이지
        initiateStartPageClick: false,	// 플러그인이 시작시 페이지 버튼 클릭 여부 (default : true)
        first : "첫 페이지",	// 페이지네이션 버튼중 처음으로 돌아가는 버튼에 쓰여 있는 텍스트
        prev : "이전 페이지",	// 이전 페이지 버튼에 쓰여있는 텍스트
        next : "다음 페이지",	// 다음 페이지 버튼에 쓰여있는 텍스트
        last : "마지막 페이지",	// 페이지네이션 버튼중 마지막으로 가는 버튼에 쓰여있는 텍스트
        nextClass : "page-item next",	// 이전 페이지 CSS class
        prevClass : "page-item prev",	// 다음 페이지 CSS class
        lastClass : "page-item last",	// 마지막 페이지 CSS calss
        firstClass : "page-item first",	// 첫 페이지 CSS class
        pageClass : "page-item",	// 페이지 버튼의 CSS class
        activeClass : "active",	// 클릭된 페이지 버튼의 CSS class
        disabledClass : "disabled",	// 클릭 안된 페이지 버튼의 CSS class
        anchorClass : "page-link",	//버튼 안의 앵커에 대한 CSS class
        
        onPageClick: function (event, page) {
            console.log(xhr.pager.limit);
            getBoardList(page, xhr.pager.limit, xhr.pager.search, xhr.pager.sort, xhr.pager.startDate, xhr.pager.endDate)
        }
    });
}

function setTable(xhr){
    $('#companies tbody').empty();
    $.each(xhr.result, function(index, item){   
        index++
        if(item.parent_company_name == null){
            item.parent_company_name = '';
        }
        $('<tr id="companyView" data-id="'+item.cdx+'">').append('<td>'+item.cdx+'</td>')
        .append('<td>'+item.parent_company_name+'</td>')
        .append('<td>'+item.companyType+'</td>')
        .append('<td>'+item.companyName+'</td>')
        .append('<td>'+item.companyTel+'</td>')
        .appendTo('#companies'); 
    });
}
function setAllCount(xhr){
    console.log(xhr.pager.total);
    if(xhr.pager.total == 0){
        $total = 0;
    }else{
        $total = xhr.pager.total;
    }
    $('#allCount').text("총 "+$total+"개");
}

function setDate(xhr){
    if($('#fromDate, #toDate').length){
        var currentDate = moment().format("YYYY-MM-DD");
        $('#fromDate, #toDate').daterangepicker({
            locale: {
                    "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                    "applyLabel": "확인",                    // 확인 버튼 텍스트
                    "cancelLabel": "취소",                   // 취소 버튼 텍스트
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
            },
            "alwaysShowCalendars": true,                        // 시간 노출 여부
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
            // console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
            // Lets update the fields manually this event fires on selection of range
            startDate = start.format('YYYY-MM-DD'); // selected start
            endDate = end.format('YYYY-MM-DD'); // selected end

            $checkinInput = $('#fromDate');
            $checkoutInput = $('#toDate');

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
            
            getBoardList(1, $('#pageLimit').val(), $('#search').val(), $('#sort').val(), startDate, endDate);
        });
    }
}

//페이지 게시글 갯수
$('body').on('change', '#pageLimit', function(){
    getBoardList(
        1, 
        $(this).val(), 
        $('#search').val(), 
        $('#sort').val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

//검색
$('body').on('keyup', '#search', function(){
    getBoardList(
        1, 
        $('#pageLimit').val(), 
        $(this).val(), 
        $('#sort').val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

//분류
$('body').on('change', '#sort', function(){
    getBoardList(
        1, 
        $('#pageLimit').val(), 
        $('#search').val(), 
        $(this).val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

$('#dateRange').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    getBoardList(1, $('#pageLimit').val(), $('#search').val(), $('#sort').val());
});

//글쓰기 버튼
$('body').on('click', '#companyNewBtn', function(){
    $('#modalWrite #frm').trigger("reset");
    var myModal = new bootstrap.Modal(document.getElementById('modalWrite'))
    myModal.show()
})

//저장 버튼
$('body').on('click', '#companyInsertBtn', function(){
    data = {
        companyType: $('#modalWrite select[name=companyType] option').filter(':selected').val(),
        companyName: $('#modalWrite input:text[name=companyName]').val(),
        companyTel: $('#modalWrite input:text[name=companyTel]').val(),
    };
    console.log(data);
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/companies",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(response){
            $('#modalWrite').modal('hide');
            $('#modalWrite').find('input').val('');  
            $('#modalWrite #frm span').text('');  
            getBoardList();
        },
        error: function(error){
            var errorText = error.responseJSON.messages;
            $.each(errorText, function(key, val){
                $("#modalWrite #" + key + "Error").text(val);
            })
        }
    });
})

//글보기
$('body').on('click', '#companyView', function(){
    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/companies/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            console.log(data);
            $('#modalView .modal-title').html(data.result.companyName);
            $('#modalView .modal-body #companyBelong').html(data.result.parent_company_name);
            $('#modalView .modal-body #companyType').html(data.result.companyType);
            $('#modalView .modal-body #companyName').html(data.result.companyName);
            $('#modalView .modal-body #companyTel').html(data.result.companyTel);
            $('#modalView #companyBelong').attr('href', '/company/belong/'+data.result.cdx);
            $('#modalView #companyUpdateModal').attr('data-id', data.result.cdx);
            $('#modalView #companyDelete').attr('data-id', data.result.cdx);
            $.each(data.result.users, function(index, item){   
                $('<div id="userList">')
                .append('<p>id : '+item.id+" username : "+item.username+'</p>')
                .appendTo('#userListWrap'); 
            });
            var myModal = new bootstrap.Modal(document.getElementById('modalView'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//글수정
$('body').on('click', '#companyUpdateModal', function(){
    $('#modalView').modal('hide');

    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/companies/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            $('#modalUpdate #companyType option[value="'+data.result.companyType+'"]').prop('selected', true);  
            $('#modalUpdate #companyName').val(data.result.companyName);
            $('#modalUpdate #companyTel').val(data.result.companyTel);
            $('#modalUpdate #hidden_id').val(data.result.cdx);
            $('#modalUpdate #frm span').text('');  
            var myModal = new bootstrap.Modal(document.getElementById('modalUpdate'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//글수정
$('body').on('click', '#companyUpdateBtn', function(){
    let id = $("#modalUpdate input:hidden[name=id]").val();
    data = {
        companyType: $('#modalUpdate select[name=companyType] option').filter(':selected').val(),
        companyName: $('#modalUpdate input:text[name=companyName]').val(),
        companyTel: $('#modalUpdate input:text[name=companyTel]').val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/companies/"+id,
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(response){
            $('#modalUpdate').modal('hide');
            $('#modalUpdate').find('input').val('');  
            $('#modalUpdate #frm span').text(''); 
            getBoardList();
            console.log(response);
        },
        error: function(error){
            var errorText = error.responseJSON.messages;
            $.each(errorText, function(key, val){
                $("#modalWrite #" + key + "Error").text(val);
            })
        }
    });
})

//글삭제
$('body').on('click', '#companyDelete', function(){
    let id = $(this).attr('data-id');
    if(confirm('정말 삭제하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/companies/"+id,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                $('#modalView').modal('hide');
                getBoardList();
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
})

$('body').on('change', '#companyType', function(){
    $(this).siblings('span').text("");
});

$('body').on('keyup', '#companyName', function(){
    $(this).siblings('span').text("");
});

$('body').on('keyup', '#companyTel', function(){
    $(this).siblings('span').text("");
});

$("#modalView").on("hidden.bs.modal", function () {
    $('#userListWrap').html('')
});

$('body').on('click', '#DataResetBtn', function(){
    $('#sort option:first').prop('selected',true);
    $('#pageLimit option:first').prop('selected',true);
    $('#fromDate').val('');
    $('#toDate').val('');
    $('#search').val('');
    getBoardList();
});

</script>
<?=$this->endSection();?>
<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
