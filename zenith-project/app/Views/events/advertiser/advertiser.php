<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 광고주 관리
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
        <h2 class="page-title">광고주 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#clientModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/eventmanage/event"><button type="button" class="btn btn-danger">이벤트 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-danger">전환 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default" id="advertiser-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">광고주명</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">매출</th>
                        <th scope="col">남은잔액</th>
                        <th scope="col">랜딩수</th>
                        <th scope="col">사업자명</th>
                        <th scope="col">외부연동</th>
                        <th scope="col">개인정보 전문</th>
                        <th scope="col">사용여부</th>
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
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="clientModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="adv-register-form" id="adv-register-form">
                <div class="table-responsive">
                    <input type="hidden" name="seq" value="">
					<input type="hidden" name="checkname" value="">
                    <input type="hidden" name="watch_list" value="">
                    <table class="table table-bordered table-left-header" id="modalTable">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">광고주명</th>
                                <td>
                                    <input type="text" class="form-control" name="name" placeholder="광고주명을 입력하세요." title="광고주" <?php 
                                    if(!auth()->user()->inGroup('superadmin', 'admin', 'developer')){
                                        echo "readonly disabled";
                                    };
                                    ?>>
                                    <p class="mt-2 text-secondary">※ 한번 등록 된 광고주는 수정이 불가능합니다. 띄어쓰기, 오타 확인 꼭 해주세요.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집주체(사업자명)</th>
                                <td>
                                    <input type="text" class="form-control" name="agent" placeholder="수집주체(사업자명)를 입력하세요." title="수집주체(사업자명)">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">외부연동 주소</th>
                                <td><input type="text" class="form-control" name="interlock_url" placeholder="외부연동 주소를 입력하세요." title="외부연동 주소"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">개인정보 전문 주소</th>
                                <td><input type="text" class="form-control" name="agreement_url" placeholder="개인정보 전문 주소를 입력하세요." title="개인정보 전문 주소"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">입금액</th>
                                <td><input type="text" class="form-control" name="account_balance" placeholder="광고주 입금액을 입력해주세요." title="광고주 입금액"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="is_stop" value="0" id="is_stop01" checked>
                                            <label class="form-check-label" for="is_stop01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_stop" value="1" id="is_stop02">
                                            <label class="form-check-label" for="is_stop02">사용중지</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 사용중지로 변경할 경우 해당 광고주의 모든 랜딩이 중지됩니다.</p>
                                </td>
                            </tr>
                            <tr class="ow_update">
                                <th scope="row" class="text-end">문자 알림 사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="sms_alert" value="1" id="sms_radio01">
                                            <label class="form-check-label" for="sms_radio01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sms_alert" value="0" id="sms_radio02">
                                            <label class="form-check-label" for="sms_radio02">미사용</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="ow_info ow_update">
                                <th scope="row" class="text-end">Notice</th>
                                <td>
                                    <ul>
                                        <li>* 00시 ~ 06시에는 문자를 발송하지 않습니다.</li>
                                        <li>* 알림 문자는 매체별로 1일 1회 발송됩니다.</li>
                                        <li>* 기타 문의사항은 [개발팀-정문숙]에게 문의 부탁드립니다.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="ow_info ow_update">
                                <th scope="row" class="text-end">알림 연락처</th>
                                <td>
                                    <input type="text" class="form-control mb-2" name="contact[]" id="contact_0" placeholder="숫자만 입력해주세요">
                                    <input type="text" class="form-control mb-2" name="contact[]" id="contact_1" placeholder="숫자만 입력해주세요">
                                    <input type="text" class="form-control" name="contact[]" id="contact_2" placeholder="숫자만 입력해주세요">
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="create-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="adv-register-form" id="createActionBtn">생성</button>
                </div>
                <div class="update-btn-wrap">
                    <button type="submit" class="btn btn-primary" form="adv-register-form" id="updateActionBtn">수정</button>
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
    dataTable = $('#advertiser-table').DataTable({
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
            "url": "<?=base_url()?>/eventmanage/advertiser/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": "seq", "width": "3%" },
            { 
                "data": "name", 
                "width": "15%",
                "render": function(data, type, row) {
                    return '<button type="button" id="updateBtn" data-bs-toggle="modal" data-bs-target="#clientModal">'+data+'</button>';
                }
            },
            { "data": "sum_db", "width": "5%"},
            { "data": "sum_price", "width": "8%"},
            { "data": "remain_balance","width": "8%"},
            { "data": "total", "width": "5%"},
            { "data": "agent","width": "15%"},
            { "data": "interlock_url","width": "5%"},
            { "data": "agreement_url","width": "5%"},
            { "data": "is_stop","width": "5%"},
            { 
                "data": "ea_datetime", 
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

function createAdv(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/advertiser/create", 
        type : "POST", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("생성되었습니다.");
                $('#clientModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function updateAdv(data){
    $.ajax({
        url : "<?=base_url()?>/eventmanage/advertiser/update", 
        type : "PUT", 
        dataType: "JSON", 
        data : data, 
        contentType: 'application/json; charset=utf-8',
        success : function(data){
            if(data == true){
                dataTable.draw();
                alert("수정되었습니다.");
                $('#clientModal').modal('hide');
            }
        }
        ,error : function(error){
            var errorMessages = error.responseJSON.messages;
            var firstErrorMessage = Object.values(errorMessages)[0];
            alert(firstErrorMessage);
        }
    });
}

function setAdv(data){
    $('input[name="seq"]').val(data.advertiser.seq);
    $('input[name="checkname"]').val(data.advertiser.seq);
    $('input[name="name"]').val(data.advertiser.name);
    $('input[name="agent"]').val(data.advertiser.agent);
    $('input[name="interlock_url"]').val(data.advertiser.interlock_url);
    $('input[name="agreement_url"]').val(data.advertiser.agreement_url);
    $('input[name="account_balance"]').val(data.advertiser.account_balance);
    $('input[name="agreement_url"]').val(data.advertiser.agreement_url);
    $('input:radio[name="is_stop"][value="'+data.advertiser.is_stop+'"]').prop('checked', true);
    if(data.ow){
        $('input:radio[name="sms_alert"][value="1"]').prop('checked', true);
        $('input:hidden[name="watch_list"]').val(data.ow.watch_list);
        var contact = data.ow.contact.split(';');
        
        for (let i = 0; i < 2; i++) {
            $('#contact_'+i+'').val(contact[i]);
        }
    }else{
        $('input:radio[name="sms_alert"][value="0"]').prop('checked', true);
    }
    
    if(data.wl){
        if(data.ow){
            console.log(data.ow.watch_list);
            watch_list = JSON.parse(data.ow.watch_list);
        }else{
            watch_list = 0;
        }
        for (let i = 0; i < data.wl.length; i++) {
            html = '<tr class="ow_info ow_update watch_list"><th scope="row" class="text-end">'+data.wl[i].media+'</th><td><input type="hidden" name="media_seq[]" value="'+data.wl[i].seq+'"><input type="text" class="form-control mb-2" name="strain[]" value="'+(watch_list[data.wl[i].seq] ? watch_list[data.wl[i].seq] : 0)+'"></td></tr>';
            $('#modalTable tbody').append(html)
        }
    }
}

function chkInput() {
    if($('input:radio[name="sms_alert"][value="1"]').is(':checked')){
        $('.ow_info').show();
    }else{
        $('.ow_info').hide();
    }
}

$('input[name="sms_alert"]').bind('change', function() {
    chkInput();
});

$('#clientModal').on('show.bs.modal', function(e) {
    var $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'updateBtn') {
        var $tr = $btn.closest('tr');
        var seq = $tr.attr('id');
        $('#clientModalLabel').text('광고주 수정');
        $('.ow_update').show();
        $('.update-btn-wrap').show();
        $('.create-btn-wrap').hide();
        $.ajax({
            type: "GET",
            url: "<?=base_url()?>/eventmanage/advertiser/view",
            data: {'seq':seq},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setAdv(data);
                chkInput();
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
        
    }else{
        $('#clientModalLabel').text('광고주 등록');
        $('.ow_update').hide();
        $('.update-btn-wrap').hide();
        $('.create-btn-wrap').show();
        $('.watch_list').remove();
        chkInput();
    }
})
.on('hidden.bs.modal', function(e) { 
    $('input[name="seq"]').val('');
    $('input[name="checkname"]').val('');
    $('form[name="adv-register-form"]')[0].reset();
    $('.watch_list').remove();
});

$('form[name="search-form"]').bind('submit', function() {
    dataTable.draw();
    return false;
});

$('form[name="adv-register-form"]').bind('submit', function(e) {
    var clickedButton = $(document.activeElement).attr('id');
    if(clickedButton == 'createActionBtn'){
        var data = $(this).serialize();
        createAdv(data);
    }
    
    if(clickedButton == 'updateActionBtn'){
        var ja = {};
        for(var i=0;i<$("input[name='strain[]']").length;i++){
            ja[$("input[name='media_seq[]']").eq(i).val()] = Number($("input[name='strain[]']").eq(i).val());
        }
        $('input[name=watch_list]').val(JSON.stringify(ja));
        var data = $(this).serialize();
        updateAdv(data);
    }
    
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>