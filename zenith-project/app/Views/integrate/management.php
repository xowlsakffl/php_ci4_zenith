<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore/js/dataTables.stateRestore.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/js/jszip.min.js"></script>
<script src="/static/js/pdfmake/pdfmake.min.js"></script>
<script src="/static/js/pdfmake/vfs_fonts.js"></script>
<style>
    #advertiserBtn{display: none;}
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">통합 DB 관리</h2>
        <!-- <p class="title-disc">안하는 사람은 끝까지 할 수 없지만, 못하는 사람은 언젠가는 해 낼 수도 있다.</p> -->
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
    <div class="section reset-btn-wrap">
        <div class="reset-btn-box">
            <button type="button" class="reset-btn">필터 초기화</button>
        </div>
    </div>
    <?php if(getenv('MY_SERVER_NAME') === 'resta'){?>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 분류
        </h3>
        <div class="row" id="company-list"></div>
    </div>
    <?php }?>
    <div class="section client-list custom-margin-box-1" <?php if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){echo 'id="advertiserBtn"'; }?>>
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 광고주
        </h3>
        <div class="row" id="advertiser-list"></div>
    </div>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 매체
        </h3>
        <div class="row" id="media-list"></div>
    </div>
    <div class="section client-list">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 이벤트 구분
        </h3>
        <div class="row" id="event-list"></div>
    </div>

    <div>
        <div class="search-wrap my-5">
            <div class="statusCount detail d-flex minWd"></div>     
        </div>

        <div class="table-responsive">
            <div class="btns-memo-style">
                <span class="btns-title">메모 표시:</span>
                <button type="button" class="btns-memo" value="modal" title="새창으로 표시"><i class="bi bi-window-stack"></i></button>
                <button type="button" class="btns-memo" value="table" title="테이블에 표시"><i class="bi bi-table"></i></button>
            </div>
            <table class="dataTable table table-striped table-hover table-default" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first">#</th>
                        <th>SEQ</th>
                        <th>분류</th>
                        <th>이벤트</th>
                        <th>광고주</th>
                        <th>매체</th>
                        <th>이벤트 구분</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>나이</th>
                        <th>성별</th>
                        <th>기타</th>
                        <th>사이트</th>
                        <th>등록일</th>
                        <th>메모</th>
                        <th class="last">인정기준</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <!-- 개별 메모 -->
        <div class="modal fade" id="modal-integrate-memo" tabindex="-1" aria-labelledby="modal-integrate-memo-label" aria-hidden="true">
            <div class="modal-dialog modal-sm sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="modal-integrate-memo-label"><i class="bi bi-file-text"></i> 개별 메모<span class="title"></span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="regi-form">
                            <fieldset>
                                <legend>메모 작성</legend>
                                <textarea></textarea>
                                <button type="button" class="btn-regi">작성</button>
                            </fieldset>
                        </form>
                        <ul class="memo-list m-2"></ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- //개별 메모 -->
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var lead_status = {"1":"인정", "2":"중복", "3":"성별불량", "4":"나이불량", "5":"콜불량", "6":"번호불량", "7":"테스트", "8":"이름불량", "9":"지역불량", "10":"업체불량", "11":"미성년자", "12":"본인아님", "13":"쿠키중복", "99":"확인"};
var exportCommon = {
    'exportOptions': { //{'columns': 'th:not(:last-child)'},
        'modifier': {'page':'all'},
    }
};
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let dataTable, tableParam = {};
getList();
function setSearchData() { //state 에 저장된 내역으로 필터 active 세팅
    var data = tableParam;
    $('#company-list button, #advertiser-list button, #media-list button, #event-list button, .statusCount dl').removeClass('active');
    if(typeof data.searchData == 'undefined') return;
    if(typeof data.searchData.company != 'undefined') {
        data.searchData.company.split('|').map(function(txt){ $(`#company-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.advertiser != 'undefined') {
        data.searchData.advertiser.split('|').map(function(txt){ $(`#advertiser-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.media != 'undefined') {
        data.searchData.media.split('|').map(function(txt){ $(`#media-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.event != 'undefined') {
        data.searchData.event.split('|').map(function(txt){ $(`#event-list button[value="${txt}"]`).addClass('active'); });
    }
    if(typeof data.searchData.status != 'undefined') {
        data.searchData.status.split('|').map(function(txt){
            $('.statusCount dt:contains("'+txt+'")').filter(function() { return $(this).text() === txt;}).parent().addClass('active');
        });
    }
    
    //$('#sdate').val(data.searchData.sdate);
    //$('#edate').val(data.searchData.edate);
    $('#stx').val(data.searchData.stx);
    debug('searchData 세팅')
    if(typeof dataTable != 'undefined') dataTable.state.save();
}
$.fn.DataTable.Api.register('buttons.exportData()', function (options) { //Serverside export
    var arr = [];
    $.ajax({
        "url": "<?=base_url()?>/integrate/list",
        "data": {"searchData":tableParam.searchData, "noLimit":true},
        "type": "GET",
        "contentType": "application/json",
        "dataType": "json",
        "success": function (result) {
            $.each(result, function(i,row) {
                // arr.push(Object.keys(result[key]).map(function(k) {  return result[key][k] }));

                var regex = /<i[^>]*>([^<]+)<\/i>/g;
                var phone = row.dec_phone.replace(regex, '$1');
                var name = row.name.replace(regex, '$1');

                arr.push([row.seq, row.info_seq, row.advertiser, row.media, row.tab_name, name, phone, row.age, row.gender, row.add, row.site, row.reg_date, row.memos, lead_status[row.status]]);
            });
        },
        "async": false
    });
    // return {body: arr , header: $("#deviceTable thead tr th").map(function() { return $(this).text(); }).get()};
    return {body: arr , header: ["고유번호","이벤트","광고주","매체","이벤트 구분","이름","전화번호","나이","성별","기타","사이트","등록일시","메모","인정기준"]};
} );
function getList(data = []) { //리스트 세팅
    dataTable = $('#deviceTable').DataTable({
        "dom": '<Bfr<t>ip>',
        "fixedHeader": true,
        "autoWidth": true,
        "order": [[12,'desc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "stateSave": true,
        "deferRender": true,
        "rowId": "seq",
        "lengthMenu": [[ 25, 10, 50, -1 ],[ '25개', '10개', '50개', '전체' ]],
        "language": {"url": '/static/js/dataTables.i18n.json'}, //한글화 파일
        "stateSaveParams": function (settings, data) { //LocalStorage 저장 시
            debug('state 저장')
            data.memoView = $('.btns-memo.active').val();
            //if($('#advertiser-list>div').is(':visible')) {
                data.searchData = {
                    'sdate': $('#sdate').val(),
                    'edate': $('#edate').val(),
                    'stx': $('#stx').val(),
                    'company' : $('#company-list button.active').map(function(){return $(this).val();}).get().join('|'),
                    'advertiser' : $('#advertiser-list button.active').map(function(){return $(this).val();}).get().join('|'),
                    'media' : $('#media-list button.active').map(function(){return $(this).val();}).get().join('|'),
                    'event' : $('#event-list button.active').map(function(){return $(this).val();}).get().join('|'),
                    'status' : $('.statusCount dl.active').map(function(){return $('dt',this).text();}).get().join('|')
                };
                tableParam = data;
                debug(tableParam.searchData);
            //}
        },
        "stateLoadParams": function (settings, data) { //LocalStorage 호출 시
            debug('state 로드')
            $(`.btns-memo[value="${data.memoView}"]`).addClass('active');
            tableParam = data;
            if(typeof tableParam.searchData == 'undefined') tableParam.searchData = [];
            tableParam.searchData.sdate = today;
            tableParam.searchData.edate = today;
            setSearchData();
            debug(tableParam.searchData);
        },
        "buttons": [ //Set Button
            {
                'extend': 'collection',
                'text': "<i class='bi bi-list'></i>",
                'className': 'custom-btn-collection',
                'fade': true,
                'buttons': [
                    'pageLength',
                    'colvis',
                    {
                        'extend':'savedStates',
                        'buttons': [
                            'createState',
                            'removeAllStates'
                        ]
                    },
                    '<div class="export">내보내기</div>',
                    $.extend( true, {}, exportCommon, {
                        extend: 'copyHtml5'
                    } ),
                    $.extend( true, {}, exportCommon, {
                        extend: 'excelHtml5'
                    } ),
                    $.extend( true, {}, exportCommon, {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    } ),
                ]
            },
        ],
        "columnDefs": [
            { targets: [0], orderable: false},
            { targets: [1], visible: false},
            { targets: [2], visible: false},
            { targets: '_all', visible: true },
            { targets: [6], className: ''}
        ],
        "columns": [
            { "data": null , "width": "35px"},
            { "data": "seq" , "width": "40px"},
            { 
                "data": null , 
                "width": "35px",
                "render": function(data, type, row) {
                    let company = '';
                    if(row.company == '케어랩스'){
                        company = '케어랩스';
                    }

                    if(row.company == '테크랩스'){
                        company = '테크랩스';
                    }
                    return company;
                }
            },
            { "data": "info_seq", "width": "45px",
                "render": function(data) {
                return data?'<a href="https://event.hotblood.co.kr/'+data+'" target="event_pop">'+data+'</a>':'';
                }
            },
            { "data": "advertiser","width": "100px",
                "render": function(data) {
                    return '<span title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            { "data": "media" , "width" : "42px",
                "render": function(data) {
                    return '<span title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            { "data": "tab_name" },
            { "data": "name", "width": "50px",
                "render": function(data) {
                    return '<span style="display:inline-block;width:50px;max-height:15px;overflow:hidden" title="'+$(`<span>${data}</span>`).text()+'">'+(data ? data : '')+'</span>';
                } 
            },
            { "data": "dec_phone", "width": "90px" },
            { "data": "age", "width": "27px" },
            { "data": "gender", "width": "27px" },
            { "data": "add" },
            { "data": "site", "width": "45px" },
            { "data": "reg_date", "width": "70px" },
            { "data": "memo_cnt", "width": "30px", "className": "memo",
              "render" : function(data) { // data-bs-toggle="modal"
                var html = '<button class="btn_memo text-dark position-relative" data-bs-target="#modal-integrate-memo"><i class="bi bi-chat-square-text h4"></i>';
                html += '<span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger badge-'+data+'">'+data+'</span>';
                html += '</button>';
                    return html;
                }
            },
            { 
                "data": 'status', "width": "60px",
                "render": function (data, type, row) {
                    <?php if(auth()->user()->hasPermission('integrate.status')){?>
                    return '<select class="lead-status form-select form-select-sm data-del"><option value="1" '+(data=="1"?" selected":"")+'>인정</option><option value="2" '+(data=="2"?" selected":"")+'>중복</option><option value="3" '+(data=="3"?" selected":"")+'>성별불량</option><option value="4" '+(data=="4"?" selected":"")+'>나이불량</option><option value="6" '+(data=="6"?" selected":"")+'>번호불량</option><option value="7" '+(data=="7"?" selected":"")+'>테스트</option><option value="5" '+(data=="5"?" selected":"")+'>콜불량</option><option value="8" '+(data=="8"?" selected":"")+'>이름불량</option><option value="9" '+(data=="9"?" selected":"")+'>지역불량</option><option value="10" '+(data=="10"?" selected":"")+'>업체불량</option><option value="11" '+(data=="11"?" selected":"")+'>미성년자</option><option value="12" '+(data=="12"?" selected":"")+'>본인아님</option><option value="13" '+(data=="13"?" selected":"")+'>쿠키중복</option><option value="99" '+(data=="99"?" selected":"")+'>확인</option></select>';
                    <?php }else{?>
                        return '<span>' + (data == "1" ? '인정' : (data == "2" ? '중복' : (data == "3" ? '성별불량' : (data == "4" ? '나이불량' : (data == "5" ? '콜불량' : (data == "6" ? '번호불량' : (data == "7" ? '테스트' : (data == "8" ? '이름불량' : (data == "9" ? '지역불량' : (data == "10" ? '업체불량' : (data == "11" ? '미성년자' : (data == "12" ? '본인아님' : (data == "13" ? '쿠키중복' : (data == "99" ? '확인' : '')))))))))))))) + '</span>';
                    <?php }?>
                }
            },
        ],
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var totalRecords = api.page.info().recordsTotal;
            var pageSize = api.page.len();
            var currentPage = api.page();
            var totalPages = Math.ceil(totalRecords / pageSize);
            
            var seqNumber = totalRecords - (currentPage * pageSize) - index; // 계산된 순번 (내림차순)
            
            $('td:eq(0)', row).html(seqNumber);
        },
        "infoCallback": function(settings, start, end, max, total, pre){ //페이지현황 세팅
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
        "ajax": { //ServerSide Load
            "url": "<?=base_url()?>/integrate/list",
            "data": function(d) {
                if(typeof tableParam != 'undefined')
                    d.searchData = tableParam.searchData;
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        }
    }).on('xhr.dt', function( e, settings, data, xhr ) { //ServerSide On Load Event
        setButtons(data.buttons);
        setLeadCount(data.buttons)
        setStatusCount(data.buttons.status);
        setDate();
        setSearchData();
    });
}
$('.btns-memo-style button').bind('click', function() { //메모 표시타입
    $('.btns-memo-style button').removeClass('active');
    $(this).addClass('active');
    debug('메모표시방식 설정');
    dataTable.state.save();
});
$('#deviceTable').on('click', 'td.memo', function(e) { //메모셀 클릭 시
    var type = $('.btns-memo.active').val();
    var tr = $(this).closest('tr');
    var row = dataTable.row(tr);
    var seq = row.data().seq;
    if(type == 'table') {
        if (row.child.isShown()) { // 메모가 열려있을 때
            row.child.hide();
        } else { // 메모가 닫혀있을 때
            var html = '<form class="regi-form"><fieldset><legend>메모 작성</legend><textarea></textarea><button type="button" class="btn-regi">작성</button></fieldset></form>';
            html += '<ul class="memo-list">';
            row.child(html).show();
            getMemoList(seq);
        }
    } else {
        dataTable.rows().every(function(){ //모든 메모 닫음
            if(this.child.isShown()) this.child.hide();
        });
        $('#modal-integrate-memo').attr('data-seq', seq).modal('show'); //modal 호출
    }
});
$(document).on('click', '.regi-form button', function(e) { //메모 작성
    var type = $('.btns-memo.active').val();
    if(!$.trim($('textarea', $(this).parents('.regi-form')).val())) {
        alert('메모 내용을 입력해주세요.');
        return false;
    }
    var data = {
        'memo': $('textarea', $(this).parents('.regi-form')).val()
    };
    if(type == 'table') {
        data['leads_seq'] =  $(this).parents('tr').prev('tr').attr('id');
    } else {
        data['leads_seq'] = $('#modal-integrate-memo').attr('data-seq');
    }
    registerMemo(data);
});
$('#modal-integrate-memo')
    .on('show.bs.modal', function(e) { //create memo data
        var seq = $(this).attr('data-seq');
        var $row = dataTable.row($(`#${seq}`));
        var name = $row.data().name;
        $('h1 .title', this).html(name);
        $('.memo-list', this).html('');
        getMemoList(seq);
    })
    .on('hidden.bs.modal', function(e) { //modal Reset
        $(this).removeAttr('data-seq');
        $('.memo-list, h1 .title', '#modal-integrate-memo').html('');
        $('#modal-integrate-memo form')[0].reset();
    });
    
function getMemoList(seq) { //메모 수신
    $.ajax({
        type: "get",
        url: "<?=base_url()?>integrate/getmemo",
        data: {'seq': seq},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setMemoList(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function registerMemo(data) { //메모 등록
    $.ajax({
        type: "post",
        url: "<?=base_url()?>integrate/addmemo",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response.result == true) {
                setMemoList(response.data);
                var cnt = parseInt($(`tr[id="${response.data[0].leads_seq}"] td .btn_memo .badge`).text()) || 0;
                $(`tr[id="${response.data[0].leads_seq}"] td .btn_memo .badge`).removeClass('badge-0').text(++cnt); //뱃지 변경
            }
            $('.regi-form textarea').val('');
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function setMemoList(data) { //메모 리스트 생성
    if(typeof data[0] == "undefined") return;
    var seq = data[0].leads_seq;
    var html =  '';
    $.each(data, function(i,row) {
        html += '    <li class="d-flex justify-content-between align-items-start">';
        html += '        <div class="detail d-flex align-items-start">';
        html += '            <p class="ms-1">'+ row.memo +'</p>';
        html += '        </div>';
        html += '        <div class="info">';
        html += '            <span>'+ row.username +'</span>';
        html += '            <span>'+ row.reg_date +'</span>';
        html += '        </div>';
        html += '    </li>';
    });
    var wrap;
    var type = $('.btns-memo.active').val();
    if(type == 'table') {
        wrap = $(`#${seq}`).next('tr').find('.memo-list');
    } else {
        wrap = $(`[data-seq="${seq}"] .memo-list`);
    }
    wrap.prepend(html);
    
}
function setLeadCount(data) { //Filter Count 표시
    $('.client-list button').removeClass('on');
    $('.client-list .col .txt').empty();
    $.each(data, function(type, row) {
        if(type == 'status') return true;
        var $container = $('#'+type+'-list');
        $.each(row, function(idx, v) {
            var cnt_txt = v.total;
            if(v.count != v.total) cnt_txt = v.count + "/" + v.total;
            button = $(`#${type}-list .col[data-name="${v.label}"] button`);
            button.siblings('.progress').children('.txt').text(`${cnt_txt}`);
            if(typeof tableParam.searchData == 'undefined' || (tableParam.searchData.advertiser == "" && tableParam.searchData.media == "" && tableParam.searchData.event == "")) return true;
            if(v.count) button.addClass('on');
        });
    });
}

function setStatusCount(data){ //상태 Count 표시
    $('.statusCount').empty();
    $.each(data, function(key, value) {
        $('.statusCount').append('<dl class="col"><dt>' + key + '</dt><dd>' + value + '</dd></dl>');
    });
}
function fontAutoResize() { //.client-list button 항목 가변폰트 적용
    $('.client-list .col').each(function(i, el) {
        var $el = $(el);
        var button = $('button', el);
        button.css({
            'white-space': 'nowrap',
            'overflow-x': 'auto',
            'font-size': '85%'
        });
        var i = 0;
        var btn_width = Math.round(button.width());
        // debug(button.val(), btn_scr_w, btn_width);
        while((button[0].scrollWidth+10) / 2 >= btn_width) {
            var size = parseFloat(button.css('font-size')) / 16 * 100;
            button.css({'font-size': --size+'%'});
            // debug(button.css('font-size'), size)
            if(button.css('font-size') < 8 || i > 60) break;
            i++;
        }
        button.css({
            'white-space': 'normal',
            'overflow-x': 'auto'
        });
    });
}
$(window).resize(function() {
    fontAutoResize();
});

function setButtons(data) { //광고주,매체,이벤트명 버튼 세팅       
    $.each(data, function(type, row) {
        if(type == 'status') return true;
        <?php if(!auth()->user()->inGroup('superadmin', 'admin', 'developer', 'user')){?>
                if(type == 'advertiser'){
                    if(data.advertiser.length > 1){
                        $('#advertiserBtn').show();
                    }else{
                        $('#advertiserBtn').hide();
                        return true;
                    }
                } 
        <?php }?>
        
        var html = "";
        $.each(row, function(idx, v) {
            html += '<div class="col" data-name="'+v.label+'"><div class="inner">';
            html += '<button type="button" value="'+v.label+'">' + v.label + '</button>';
            html += '<div class="progress">';
            html += '<div class="txt">'+v.count+'/'+v.total+'</div>';
            html += '</div>';
            html += '</div></div>';
        });
        $('#'+type+'-list').html(html);
    });
    fontAutoResize();
}

function setDate(){
    //$('#sdate, #edate').val(today);
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

$('body').on('click', '#company-list button, #advertiser-list button, #media-list button, #event-list button', function() {
    $(this).toggleClass('active');
    debug('필터링 탭 클릭');
    dataTable.state.save();
    dataTable.draw();
});

$('form[name="search-form"]').bind('submit', function() {
    debug('검색 전송');
    dataTable.state.save();
    dataTable.draw();
    return false;
});

$('.statusCount').on('click', 'dl', function(e) {
    debug('인정기준 필터')
    $(this).toggleClass('active');
    dataTable.state.save();
    dataTable.draw();
});

$('body').on('click', '.reset-btn', function() {
    $('#sdate, #edate').val(today);
    $('#company-list button, #advertiser-list button, #media-list button, #event-list button, .statusCount dl').removeClass('active');
    $('#stx').val('');
    dataTable.state.clear();
    dataTable.state.save();
    dataTable.order([12, 'desc']).draw();
});
// 인정기준 변경처리
function setStatus(t) {
    var oldvalue = $(t).attr('data-oldvalue');
    var data = {
        'seq': $(t).closest('tr').attr('id'),
        'oldstatus' : oldvalue,
        'status' : t.value
    };
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/integrate/setstatus",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response.result == true) {
                var r = response.data;
                var data = {
                    'seq' : r.seq,
                    'memo' : `"${lead_status[r.oldstatus]}"에서 "${lead_status[r.status]}"(으)로 상태변경`
                };
                var $o_obj = $('.statusCount dt:contains("'+lead_status[r.oldstatus]+'")').filter(function() { return $(this).text() === lead_status[r.oldstatus];}).next('dd');
                var o_cnt = parseInt($o_obj.text());
                $o_obj.text(--o_cnt);
                var $n_obj = $('.statusCount dt:contains("'+lead_status[r.status]+'")').filter(function() { return $(this).text() === lead_status[r.status];}).next('dd');
                var n_cnt = parseInt($n_obj.text());
                $n_obj.text(++n_cnt);
                registerMemo(data);
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
$('#deviceTable')
    .on('focus click', '.lead-status', function(e) {
        $(this).attr('data-oldvalue', this.value);
    })
    .on('change', '.lead-status', function(e) {
        setStatus(this);
    });
function debug(msg) {
    //console.log(msg);
}
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>