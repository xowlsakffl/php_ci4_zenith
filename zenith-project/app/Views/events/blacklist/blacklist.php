<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 블랙리스트 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/datatables.css" rel="stylesheet">
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap eventmanage-container">
    <div class="title-area">
        <h2 class="page-title">블랙리스트 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#blackCreateModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section position-relative">
        <div class="btn-wrap">
            <a href="/eventmanage/event"><button type="button" class="btn btn-outline-danger">이벤트 관리</button></a>
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-outline-danger">광고주 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-outline-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-outline-danger">전환 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="blacklist-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">전화번호/아이피</th>
                        <th scope="col">메모</th>
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
<!-- 등록 -->
<div class="modal fade" id="blackCreateModal" tabindex="-1" aria-labelledby="blackCreateModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="blackCreateModal">블랙리스트</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="black-create-form" id="black-create-form">
                    <div class="table-responsive">
                        <table class="table table-bordered table-left-header" id="modalCreateTable">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-end">전화번호/아이피</th>
                                    <td><input class="form-control" type="text" name="data"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">메모</th>
                                    <td><textarea class="form-control" name="memo"></textarea>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="black-create-form" id="createActionBtn">생성</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 등록 -->
<!-- 보기/삭제 -->
<div class="modal fade" id="blackShowModal" tabindex="-1" aria-labelledby="blackShowModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="blackShowModal">블랙리스트</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="black-delete-form" id="black-delete-form">
                    <input type="hidden" name="seq" value="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-left-header" id="modalShowTable">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr class="data_show">
                                    <th scope="row" class="text-end">전화번호/아이피</th>
                                    <td></td>
                                </tr>
                                <tr class="memo_show">
                                    <th scope="row" class="text-end">메모</th>
                                    <td></td>
                                </tr>
                                <tr class="datetime_show">
                                    <th scope="row" class="text-end">생성일</th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="delete-btn-wrap">
                    <button type="button" class="btn btn-outline-secondary deleteActionBtn">삭제</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 보기/삭제 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
let data = {};
let dataTable;

getList();

function setData() {
    data = {
        'stx': $('#stx').val(),
    };

    return data;
}

function getList(){
    $.fn.DataTable.ext.pager.numbers_length = 10;
    dataTable = $('#blacklist-table').DataTable({
        "order": [[2,'desc']],
        "fixedHeader": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "scrollX": true,
        // "scrollY": 500,
        "scrollCollapse": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "ajax": {
            "url": "<?=base_url()?>/eventmanage/blacklist/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { 
                "data": "data", 
                "width": "30%",
                "render": function(data, type, row) {
                    return '<button type="button" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#blackShowModal">'+data+'</button>';
                }
            },
            { "data": "memo", "width": "50%"},
            { 
                "data": "datetime", 
                "width": "20%",
            }
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },

    });
}

function createBlack(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/blacklist/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#blackCreateModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function deleteBlack(seq){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/blacklist/delete", 
        type : "DELETE", 
        dataType: "JSON", 
        data : {'seq':seq}, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("삭제되었습니다.");
                $('#blackShowModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

$('#blackCreateModal').on('show.bs.modal', function(e) {
    //
})
.on('hidden.bs.modal', function(e) { 
    $('form[name="black-create-form"]')[0].reset();
});

$('#blackShowModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    var $tr = $btn.closest('tr');
    var seq = $tr.attr('id');
    data = {
        'seq':seq
    };
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/eventmanage/blacklist/view",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#black-delete-form input[name="seq"]').val(data.seq);
            $('.data_show td').text(data.data);
            $('.memo_show td').text(data.memo);
            $('.datetime_show td').text(data.datetime);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})
.on('hidden.bs.modal', function(e) { 
    $('#black-delete-form input[name="seq"]').val('');
});

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('form[name="black-create-form"]').bind('submit', function(e) {
    var data = {
        'data': $('form[name="black-create-form"] input[name="data"]').val(),
        'memo': $('form[name="black-create-form"] textarea[name="memo"]').val(),
    };

    createBlack(data);
    return false;
});

$('body').on('click', '.deleteActionBtn', function() {
    seq = $('#black-delete-form input[name="seq"]').val();
    deleteBlack(seq);
});

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>