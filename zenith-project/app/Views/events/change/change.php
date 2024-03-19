<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 전환 관리
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
        <h2 class="page-title">전환 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#changeModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section position-relative">
        <div class="btn-wrap">
            <a href="/eventmanage/event"><button type="button" class="btn btn-outline-danger">이벤트 관리</button></a>
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-outline-danger">광고주 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-outline-danger">매체 관리</button></a>
            <a href="/eventmanage/blacklist"><button type="button" class="btn btn-outline-danger">블랙리스트 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="change-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">전환ID</th>
                        <th scope="col">전환명</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12378436743535</td>
                        <td>전국상상 무제한카톡5</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal')?>
<!-- 전환 등록 -->
<div class="modal fade" id="changeModal" tabindex="-1" aria-labelledby="changeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="changeModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="change-register-form" id="change-register-form">
                    <input type="hidden" name="old_id" value="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-left-header">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-end">전환ID</th>
                                    <td><input type="text" name="id" class="form-control" placeholder="전환 ID를 입력하세요." title="전환 ID"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">전환명</th>
                                    <td><input type="text" name="name" class="form-control" placeholder="전환명 입력하세요." title="전환명"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">Access Token</th>
                                    <td><input type="text" name="token" class="form-control" placeholder="Access Token을 입력하세요." title="Access Token"></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="change-register-form" id="createActionBtn">생성</button>
                </div>
                <div class="update-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="change-register-form" id="updateActionBtn">수정</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- //전환 등록 -->
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
    dataTable = $('#change-table').DataTable({
        "autoWidth": true,
        "fixedHeader": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": false,
        "scrollX": true,
        // "scrollY": 500,
        "scrollCollapse": true,
        "deferRender": true,
        "rowId": "id",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "ajax": {
            "url": "<?=base_url()?>/eventmanage/change/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { 
                "data": "id", 
                "width": "20%",
                "render": function(data, type, row) {
                    return '<button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#changeModal">'+data+'</button>';
                }
            },
            { "data": "name", "width": "80%"},
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
    });
}

function setChange(data){
    $('input[name="old_id"]').val(data.id);
    $('input[name="id"]').val(data.id);
    $('input[name="name"]').val(data.name);
    $('input[name="token"]').val(data.token);
}

function createChange(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/change/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                
                $('#changeModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function updateChange(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/change/update", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("수정되었습니다.");
                
                $('#changeModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('#changeModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'updateBtn') {
        var $tr = $btn.closest('tr');
        var id = $tr.attr('id');
        $('#changeModalLabel').text('전환 수정');
        $('.update-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/eventmanage/change/view",
            data: {'id':id},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setChange(data);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }else{
        $('#changeModalLabel').text('전환 등록');
        $('.update-btn-wrap').hide();
        $('.create-btn-wrap').show();
    }
})
.on('hidden.bs.modal', function(e) { 
    $('form[name="change-register-form"]')[0].reset();
});

$('form[name="change-register-form"]').bind('submit', function(e) {
    var data = $(this).serialize();
    var clickedButton = $(document.activeElement).attr('id');
    if(clickedButton == 'createActionBtn'){
        createChange(data);
    }
    
    if(clickedButton == 'updateActionBtn'){
        updateChange(data);
    }
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>