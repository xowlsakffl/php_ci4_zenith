<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 블랙리스트 관리
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
        <h2 class="page-title">블랙리스트 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#blackModal" type="button">등록</button>
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
                        <th scope="col">#</th>
                        <th scope="col">아이피</th>
                        <th scope="col">등록자</th>
                        <th scope="col">기간</th>
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
<!-- 광고주 등록 -->
<div class="modal fade" id="blackModal" tabindex="-1" aria-labelledby="blackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="blackModalLabel">블랙리스트</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="black-register-form" id="black-register-form">
                    <div class="table-responsive">
                        <input type="hidden" name="seq" value="">
                        <table class="table table-bordered table-left-header" id="modalTable">
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr class="ip">
                                    <th scope="row" class="text-end">아이피</th>
                                    <td></td>
                                </tr>
                                <tr class="username">
                                    <th scope="row" class="text-end">등록자</th>
                                    <td></td>
                                </tr>
                                <tr class="term">
                                    <th scope="row" class="text-end">차단 기간</th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="black-register-form" id="createActionBtn">생성</button>
                </div>
                <div class="delete-btn-wrap">
                    <button type="submit" class="btn btn-outline-secondary" form="black-register-form" id="deleteActionBtn">삭제</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- //광고주 등록 -->
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
    dataTable = $('#blacklist-table').DataTable({
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
            "url": "<?=base_url()?>/eventmanage/blacklist/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": "seq", "width": "3%"},
            { 
                "data": "ip", 
                "width": "40%",
                "render": function(data, type, row) {
                    return '<button type="button" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#blackModal">'+data+'</button>';
                }
            },
            { "data": "username", "width": "7%"},
            { "data": "term", "width": "20%"},
            { 
                "data": "reg_date", 
                "width": "10%",
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
                $('#blackModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function deleteBlack(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/blacklist/delete", 
        type : "DELETE", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("삭제되었습니다.");
                $('#blackModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function setBlack(data){
    $('input[name="seq"]').val(data.seq);
    $('.ip td').text(data.ip);
    $('.username td').text(data.username);
    $('.term td').text(data.term);
}

$('#blackModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'deleteBtn') {
        var $tr = $btn.closest('tr');
        var seq = $tr.attr('id');
        $('.delete-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $('.username').show();
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/eventmanage/blacklist/view",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setBlack(data);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }else{
        ip = '<input class="form-control" type="text" name="ip" placeholder="아이피를 입력하세요." title="아이피">';
        $('.ip td').append(ip)

        term = '<select class="form-select me-2" name="term"><option value="1d">1일</option><option value="7d">1주</option><option value="14d">2주</option><option value="1m">1개월</option><option value="3m">3개월</option><option value="forever">영구차단</option><option value="" disabled selected hidden>선택</option></select>';
        $('.term td').append(term)
        $('.delete-btn-wrap').hide();
        $('.create-btn-wrap').show();
        $('.username').hide();
    }
})
.on('hidden.bs.modal', function(e) { 
    $('input[name="seq"]').val('');
    $('.ip td').empty();
    $('.username td').empty();
    $('.term td').empty();
    $('form[name="black-register-form"]')[0].reset();
});

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('form[name="black-register-form"]').bind('submit', function(e) {
    var clickedButton = $(document.activeElement).attr('id');
    if(clickedButton == 'createActionBtn'){
        var data = {
            'seq': $('form[name="black-register-form"] input[name="seq"]').val(),
            'ip': $('form[name="black-register-form"] input[name="ip"]').val(),
            'term': $('form[name="black-register-form"] select[name="term"]').val(),
        };
        createBlack(data);
    }
    
    if(clickedButton == 'deleteActionBtn'){
        var data = {
            'seq': $('form[name="black-register-form"] input[name="seq"]').val(),
        };
        deleteBlack(data);
    }
    
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>