<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<style>
    .section .active{
        border: 1px solid red !important;
    }
    .section .active2{
        background-color: red !important;
    }
</style>
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
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="button">조회</button>
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
                        <th class="first" style="width:20px">#</th>
                        <th>SEQ</th>
                        <th>이벤트</th>
                        <th>광고주</th>
                        <th>매체</th>
                        <th style="width:110px">이벤트 구분</th>
                        <th style="width:50px" >이름</th>
                        <th style="width:90px">전화번호</th>
                        <th style="width:30px">나이</th>
                        <th style="width:30px">성별</th>
                        <th>기타</th>
                        <th style="width:60px">사이트</th>
                        <th style="width:90px">등록일</th>
                        <th style="width:30px">메모</th>
                        <th class="last" style="width:60px">인정기준</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let data = {};
let dataTable;
getLead(data);
getStatusCount(data);
getList(data);
function setData() {
    data = {
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
        "autoWidth": true,
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
        "fixedHeader": true,
        "deferRender": false,
        "buttons": [
            {
                extend: 'copy',
                text: 'Copy to clipboard'
            },
            'excel',
            'pdf'
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
            { "data": null },
            { "data": "seq", "name": "seq" },
            { "data": "info_seq",
              "render": function(data) {
                return data?'<a href="https://event.hotblood.co.kr/'+data+'" target="event_pop">'+data+'</a>':'';
              }
            },
            { "data": "advertiser" },
            { "data": "media" },
            { "data": "tab_name" },
            { "data": "name",
              "render": function(data) {
                return '<span title="'+data+'">'+data+'</span>';
              } 
            },
            { "data": "dec_phone" },
            { "data": "age" },
            { "data": "gender" },
            { "data": "add" },
            { "data": "site" },
            { "data": "reg_date", },
            { "data": "memo_cnt",
              "render" : function(data) {
                return '<a href="#" class="btn_memo text-dark position-relative"><i class="bi bi-chat-square-text h4"></i><span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger">'+data+'</span></a>';
              }
            },
            { 
                "data": 'status',
                "render": function (data, type, row) {
                    return '<select class="form-select form-select-sm data-del"><option value="1" '+(data=="1"?" selected":"")+'>인정</option><option value="2" '+(data=="2"?" selected":"")+'>중복</option><option value="3" '+(data=="3"?" selected":"")+'>성별불량</option><option value="4" '+(data=="4"?" selected":"")+'>나이불량</option><option value="6" '+(data=="6"?" selected":"")+'>번호불량</option><option value="7" '+(data=="7"?" selected":"")+'>테스트</option><option value="5" '+(data=="5"?" selected":"")+'>콜불량</option><option value="8" '+(data=="8"?" selected":"")+'>이름불량</option><option value="9" '+(data=="9"?" selected":"")+'>지역불량</option><option value="10" '+(data=="10"?" selected":"")+'>업체불량</option><option value="11" '+(data=="11"?" selected":"")+'>미성년자</option><option value="12" '+(data=="12"?" selected":"")+'>본인아님</option><option value="13" '+(data=="13"?" selected":"")+'>쿠키중복</option><option value="99" '+(data=="99"?" selected":"")+'>확인</option></select>';
                }
            },
        ],
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        // "language": {
        //     "emptyTable": "데이터가 존재하지 않습니다.",
        //     "lengthMenu": "페이지당 _MENU_ 개씩 보기",
        //     "info": "현재 _START_ - _END_ / _TOTAL_건",
        //     "infoEmpty": "데이터 없음",
        //     "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
        //     "search": "에서 검색: ",
        //     "zeroRecords": "일치하는 데이터가 없어요.",
        //     "loadingRecords": "로딩중...",
        //     "paginate": {
        //         "next": "다음",
        //         "previous": "이전"
        //     }
        // },
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

function getLeadCount(data = []){
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

function getStatusCount(data = []){
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
		
function getLead(data = []){
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

    
/*
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'adv': advertiser,
        'media': media,
        'event': event
    };

    getLeadCount(data);
    getStatusCount(data);
    $('#deviceTable').DataTable().destroy();
    getList(data);
*/
    dataTable
        .column('#deviceTable thead th:contains("광고주")')
        .search(data.advertiser,true,false)
        .column('#deviceTable thead th:contains("매체")')
        .search(data.media,true,false)
        .column('#deviceTable thead th:contains("이벤트 구분")')
        .search(data.event,true,false)
        .draw();
});

$('body').on('click', '#search_btn', function() {
    
    getLeadCount(data);
    getStatusCount(data);
    $('#deviceTable').DataTable().destroy();
    getList(data);
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>