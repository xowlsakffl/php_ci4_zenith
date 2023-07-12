<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 매체 관리
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
        <h2 class="page-title">매체 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#mediaModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/eventmanage/event"><button type="button" class="btn btn-danger">이벤트 관리</button></a>
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-danger">광고주 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-danger">전환 관리</button></a>
            <a href="/eventmanage/blacklist"><button type="button" class="btn btn-danger">블랙리스트 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="media-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">매체명</th>
                        <th scope="col">대상</th>
                        <th scope="col">랜딩수</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal')?>
<!-- 매체 등록 -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="mediaModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="media-register-form" id="media-register-form">
                    <div class="table-responsive">
                        <input type="hidden" name="seq" value="">
                        <input type="hidden" name="checkname" value="">
                        <table class="table table-bordered table-left-header">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-end">매체명</th>
                                    <td><input type="text" name="media" class="form-control"></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-end">대상</th>
                                    <td><input type="text" name="target" class="form-control"></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="media-register-form" id="createActionBtn">생성</button>
                </div>
                <div class="update-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="media-register-form" id="updateActionBtn">수정</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- //매체 등록 -->
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
    dataTable = $('#media-table').DataTable({
        "order": [[0,'desc']],
        "autoWidth": false,
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
            "url": "<?=base_url()?>/eventmanage/media/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": "seq", "width": "5%" },
            { 
                "data": "media", 
                "width": "75%",
                "render": function(data, type, row) {
                    return '<button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#mediaModal">'+data+'</button>';
                }
            },
            { "data": "target", "width": "15%"},
            { "data": "total", "width": "5%"},
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
    });
}

function setMedia(data){
    $('input[name="seq"]').val(data.seq);
    $('input[name="checkname"]').val(data.seq);
    $('input[name="media"]').val(data.media);
    $('input[name="target"]').val(data.target);
}

function createMedia(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/media/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#mediaModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function updateMedia(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/media/update", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("수정되었습니다.");
                $('#mediaModal').modal('hide');
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

$('#mediaModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'updateBtn') {
        var $tr = $btn.closest('tr');
        var seq = $tr.attr('id');
        $('#mediaModalLabel').text('매체 수정');
        $('.update-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/eventmanage/media/view",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setMedia(data);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
        
    }else{
        $('#mediaModalLabel').text('매체 등록');
        $('.update-btn-wrap').hide();
        $('.create-btn-wrap').show();
    }
})
.on('hidden.bs.modal', function(e) { 
    $('input[name="seq"]').val('');
    $('input[name="checkname"]').val('');
    $('form[name="media-register-form"]')[0].reset();
});

$('form[name="media-register-form"]').bind('submit', function(e) {
    var data = $(this).serialize();
    var clickedButton = $(document.activeElement).attr('id');
    if(clickedButton == 'createActionBtn'){
        createMedia(data);
    }
    
    if(clickedButton == 'updateActionBtn'){
        updateMedia(data);
    }
    
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
