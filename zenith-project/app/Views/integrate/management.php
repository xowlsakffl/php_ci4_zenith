<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-dt/css/buttons.dataTables.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="/static/js/jszip.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap db-manage-contaniner">
    <div class="title-area">
        <h2 class="page-title">통합 DB 관리</h2>
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
    <div class="section client-list">
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
            <div class="statusCount detail d-flex flex-wrap"></div>     
        </div>

        <div class="row table-responsive">
            <table class="dataTable table table-striped table-hover table-default" id="deviceTable">
                <thead class="table-dark">
                    <tr>
                        <th class="first">#</th>
                        <th>SEQ</th>
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
        <div class="modal fade" id="integrate-memo" tabindex="-1" aria-labelledby="integrate-memo-label" aria-hidden="true">
            <div class="modal-dialog modal-sm sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="integrate-memo-label"><i class="bi bi-file-text"></i> 개별 메모<span class="title"></span></h1>
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
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let dataTable;
setDate();
getLead();
getStatusCount();
getList();
function setData() {
    var data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'advertiser' : $('#advertiser-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'media' : $('#media-list button.active').map(function(){return $(this).val();}).get().join('|'),
        'event' : $('#event-list button.active').map(function(){return $(this).val();}).get().join('|'),
    };

    return data;
}
function getList(data = []){
    dataTable = $('#deviceTable').DataTable({
        "dom": '<Bfr<t>ip>',
        "autoWidth": false,
        "columnDefs": [
            { targets: [0], orderable: false},
            { targets: [1], visible: false},
            { targets: '_all', visible: true },
            { targets: [6], className: 'nowrap'}
        ],
        "order": [[1,'desc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "scrollX": true,
        "scrollY": 500,
        "scrollCollapse": true,
        "stateSave": true,
        "deferRender": true,
        "lengthMenu": [
            [ 25, 10, 50, -1 ],
            [ '25개', '10개', '50개', '전체' ]
        ],
        "buttons": [
            'pageLength', 
            {
                'extend': 'excelHtml5',
                'exportOptions': { //{'columns': 'th:not(:last-child)'},
                    'customizeData': function(data) {
                        var header = ["고유번호","이벤트","광고주","매체","이벤트 구분","이름","전화번호","나이","성별","기타","사이트","등록일시","인정기준"];
                        var body = [];
                        $.each(data['body'], function(i, row) {
                            var row = row[0];
                            body[i] = [row.seq, row.info_seq, row.advertiser, row.media, row.event, row.name, row.dec_phone, row.age, row.gender, row.add, row.site, row.reg_date, row.status];
                        });
                        data.header = header;
                        data.body = body;
                        //return은 하면 안됨. data 오브젝트를 변형시켜서만 사용
                    }
                }
            }
        ],
        "ajax": {
            "url": "<?=base_url()?>/integrate/list",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": null, "width": "40px" },
            { "data": "seq" },
            { "data": "info_seq", "width": "40px",
              "render": function(data) {
                return data?'<a href="https://event.hotblood.co.kr/'+data+'" target="event_pop">'+data+'</a>':'';
              }
            },
            { "data": "advertiser" },
            { "data": "media" },
            { "data": "tab_name" },
            { "data": "name", "width": "50px",
              "render": function(data) {
                return '<span title="'+data+'">'+data+'</span>';
              } 
            },
            { "data": "dec_phone", "width": "90px" },
            { "data": "age", "width": "30px" },
            { "data": "gender", "width": "30px" },
            { "data": "add" },
            { "data": "site", "width": "50px" },
            { "data": "reg_date", "width": "70px" },
            { "data": "memo_cnt", "width": "30px",
              "render" : function(data) {
                var html = '<a href="#" class="btn_memo text-dark position-relative" data-bs-toggle="modal" data-bs-target="#integrate-memo"><i class="bi bi-chat-square-text h4"></i>';
                if(data > 0)
                    html += '<span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger">'+data+'</span>';
                html += '</a>';
                return html;
              }
            },
            { 
                "data": 'status', "width": "60px",
                "render": function (data, type, row) {
                    return '<select class="form-select form-select-sm data-del"><option value="1" '+(data=="1"?" selected":"")+'>인정</option><option value="2" '+(data=="2"?" selected":"")+'>중복</option><option value="3" '+(data=="3"?" selected":"")+'>성별불량</option><option value="4" '+(data=="4"?" selected":"")+'>나이불량</option><option value="6" '+(data=="6"?" selected":"")+'>번호불량</option><option value="7" '+(data=="7"?" selected":"")+'>테스트</option><option value="5" '+(data=="5"?" selected":"")+'>콜불량</option><option value="8" '+(data=="8"?" selected":"")+'>이름불량</option><option value="9" '+(data=="9"?" selected":"")+'>지역불량</option><option value="10" '+(data=="10"?" selected":"")+'>업체불량</option><option value="11" '+(data=="11"?" selected":"")+'>미성년자</option><option value="12" '+(data=="12"?" selected":"")+'>본인아님</option><option value="13" '+(data=="13"?" selected":"")+'>쿠키중복</option><option value="99" '+(data=="99"?" selected":"")+'>확인</option></select>';
                }
            },
        ],
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
            "buttons": {
                "excel" : '<i class="bi bi-file-earmark-spreadsheet"></i> 엑셀'
            }
        },
        "rowCallback": function(row, data, index) {
            var api = this.api();
            var startIndex = api.page() * api.page.len();
            var seq = startIndex + index + 1;
            $('td:eq(0)', row).html(seq);
            $(row).attr('data-seq', data.seq);
        },
        "infoCallback": function(settings, start, end, max, total, pre){
            return "<i class='bi bi-check-square'></i>현재" + "<span class='now'>" +start +" - " + end + "</span>" + " / " + "<span class='total'>" + total + "</span>" + "건";
        },
    });
    dataTable.buttons().container()
    .appendTo( $('.dataTables_length', dataTable.table().container() ) );
}
$('#integrate-memo')
    .on('show.bs.modal', function(e) { //create memo data
        var $btn = $(e.relatedTarget);
        var $row = $btn.closest('tr')
        var seq = $row.data('seq');
        var name = $('td:eq(5)', $row).text();
        $(this).attr('data-seq', seq);
        $('h1 .title', this).html(name);
        $('#integrate-memo .memo-list').html('');
        $.ajax({
            type: "get",
            url: "<?=base_url()?>/integrate/getmemo",
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
    })
    .on('hidden.bs.modal', function(e) { //modal Reset
        $(this).removeAttr('data-seq');
        $('.memo-list, h1 .title', '#integrate-memo').html('');
        $('#integrate-memo form')[0].reset();
    });
function setMemoList(data) {
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
    $('#integrate-memo .memo-list').html(html);
}
function getLeadCount(){
    var data = setData();
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/integrate/leadcount",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setLeadCount(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setLeadCount(data) {
    $('.client-list button').removeClass('on');
    $('.client-list .col .txt').empty();
    $.each(data, function(type, row) {
        var $container = $('#'+type+'-list');
        $.each(row, function(name, v) {
            button = $('#'+type+'-list .col[data-name="'+ name +'"] button');
            button.siblings('.progress').children('.txt').text(v.countAll);
            if($('#'+type+'-list .col').length == Object.keys(data[type]).length) return true;
            button.addClass('on');
        });
    });
}

function getStatusCount(){
    var data = setData();
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/integrate/statuscount",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(result){     
            $('.statusCount').empty();
            $.each(result[0], function(key, value) {
                $('.statusCount').append('<dl class="col"><dt>' + key + '</dt><dd>' + value + '</dd></dl>');
            });
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function fontAutoResize() { //.client-list button 항목 가변폰트 적용
    $('.client-list .col').each(function(i, el) {
        var $el = $(el);
        var button = $('button', el);
        button.css({
            'white-space': 'nowrap',
            'overflow-x': 'auto',
            'font-size': '100%'
        });
        var i = 0;
        var btn_width = Math.round(button.width());
        // console.log(button.val(), btn_scr_w, btn_width);
        while((button[0].scrollWidth+10) / 2 >= btn_width) {
            var size = parseFloat(button.css('font-size')) / 16 * 100;
            button.css({'font-size': --size+'%'});
            // console.log(button.css('font-size'), size)
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
        var html = "";
        $.each(row, function(idx, v) {
            html += '<div class="col" data-name="'+idx+'"><div class="inner">';
            html += '<button type="button" value="'+idx+'">' + idx + '</button>';
            html += '<div class="progress">';
            html += '<div class="txt">' + v.countAll + '</div>';
            html += '</div>';
            html += '</div></div>';
        });
        $('#'+type+'-list').html(html);
    });
    fontAutoResize();
}
		
function getLead(){
    var data = setData();
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/integrate/leadcount",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            setButtons(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setDate(){
    $('#sdate, #edate').val(today);
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

$('body').on('click', '#advertiser-list button, #media-list button, #event-list button', function() {
    $(this).toggleClass('active');
    getLeadCount();
    getStatusCount();
    dataTable.draw();
});

$('form[name="search-form"]').bind('submit', function() {
    getLeadCount();
    getStatusCount();
    dataTable.draw();
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>