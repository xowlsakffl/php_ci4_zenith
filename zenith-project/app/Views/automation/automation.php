<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet">  
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
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
                <button class="btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#logModal">전체 로그 보기</button>
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
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModal" aria-hidden="true">
    <input type="hidden" name="log_seq">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="logModal"> <span></span>감사 로그</h1>
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
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default" id="logTable">
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
</div>
<?=$this->include('templates/inc/automation_create_modal.php')?>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script src="/static/js/automation/automation.js"></script>
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
    $.fn.DataTable.ext.pager.numbers_length = 10;
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
                    var status = '<div class="td-inner"><div class="ui-toggle"><input type="checkbox" name="status" id="status_' + row.aa_seq + '" ' + checked + ' value="'+row.aa_seq+'"><label for="status_' + row.aa_seq + '">사용</label></div><div class="more-action"><button type="button" class="btn-more"><span>더보기</span></button><ul class="action-list z-1"><li><a href="#" data-seq="' + row.aa_seq + '" class="log-btn" data-bs-target="#logModal" data-bs-toggle="modal">로그보기</a></li><li><a href="#" data-seq="' + row.aa_seq + '" class="copy-btn">복제하기</a></li><li><a href="#" data-seq="' + row.aa_seq + '" class="delete-btn">제거하기</a></li></ul></div></div>';

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

function setAutomationStatus(data){
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
function getLogs($seq = null){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    logTable = $('#logTable').DataTable({
        "destroy": true,
        "autoWidth": false,
        "processing" : true,
        "serverSide" : true,
        "responsive": false,
        "searching": false,
        "ordering": true,
        "order": [[3,'desc']],
        "deferRender": true,
        'lengthChange': true,
        'pageLength': 10,
        "scrollX": true,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/logs",
            "data": function(d) {
                d.stx = $('input[name=log_stx]').val();
                d.seq = $('input[name=log_seq]').val();
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
            { "data": "nickname", width:"15%"},
            { 
                "data": "result",
                "width": "15%",
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
            { "data": "exec_timestamp", "width":"15%"},
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
    let $btn = $(e.relatedTarget);
    if ($btn.hasClass('log-btn')) {
        let seq = $btn.attr('data-seq');
        $('input[name=log_seq]').val(seq);
        let title = $btn.closest('tr').find('.updateBtn').text();
        $('#logModal .modal-header h1 span').text(title+" - ");
        getLogs(seq);
    }else{      
        getLogs();
    }
    
})//모달 닫기
.on('hidden.bs.modal', function(e) { 
    $('input[name=log_stx]').val('');
    $('input[name=log_seq]').val('');
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