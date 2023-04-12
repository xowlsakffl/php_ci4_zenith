<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.js"></script>
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
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">통합 DB 관리</h2>
        <p class="title-disc">안하는 사람은 끝까지 할 수 없지만, 못하는 사람은 언젠가는 해 낼 수도 있다.</p>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate" readonly="readonly" value="<?=date('Y-m-d')?>">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate" readonly="readonly" value="<?=date('Y-m-d')?>">
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
        <div class="statusCount">
        </div>
        <div class="row">
            <table class="dataTable" id="deviceTable">
                <thead>
                    <tr>
                        <th style="width:40px" class="first">#</th>
                        <th style="width:80px">이벤트번호</th>
                        <th style="width:130px">광고주</th>
                        <th style="width:70px">매체</th>
                        <th style="width:120px">이벤트 구분</th>
                        <th style="width:70px" >이름</th>
                        <th style="width:100px">전화번호</th>
                        <th style="width:50px">나이</th>
                        <th style="width:50px" >성별</th>
                        <th>기타</th>
                        <th style="width:200px">상담내용</th>
                        <th style="width:60px">사이트</th>
                        <th style="width:80px">등록일</th>
                        <th class="last" style="width:60px">삭제</th>
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
$(function(){
    var data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
    };

    getLead(data);
    getList(data);
    getStatusCount(data);
    function getList(data = []){
        $('#deviceTable').DataTable({
            "processing" : true,
			"serverSide" : true,
            "responsive": true,
            "searching": false,
            "ordering": false,
            "ajax": {
                "url": "<?=base_url()?>/integrate/list",
                "data": data,
                "type": "GET",
                "contentType": "application/json",
                "dataType": "json",
            },
            "columns": [
                { "data": null },
                { "data": "info_seq" },
                { "data": "advertiser" },
                { "data": "media" },
                { "data": "tab_name" },
                { "data": "name" },
                { "data": "dec_phone" },
                { "data": "age" },
                { "data": "gender" },
                { "data": "add" },
                { "data": null, "defaultContent": ""},
                { "data": "site" },
                { "data": "reg_date", },
            ],
            "language": {
                "emptyTable": "데이터가 존재하지 않습니다.",
                "lengthMenu": "페이지당 _MENU_ 개씩 보기",
                "info": "현재 _START_ - _END_ / _TOTAL_건",
                "infoEmpty": "데이터 없음",
                "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
                "search": "에서 검색: ",
                "zeroRecords": "일치하는 데이터가 없어요.",
                "loadingRecords": "로딩중...",
                "paginate": {
                    "next": "다음",
                    "previous": "이전"
                }
            },
            "rowCallback": function(row, data, index) {
                var api = this.api();
                var startIndex = api.page() * api.page.len();
                var seq = startIndex + index + 1;
                $('td:eq(0)', row).html(seq);
            }
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
        let cnt = {
            'advertiser':[],
            'media':[],
            'event':[],
        };
        console.log(data)
        console.log(data.length, $('.client-list button').length);
        $('.client-list button').removeClass('on');
        $.each(data, function(idx, row) {
            $('#advertiser-list .col[data-seq="'+ row.adv_seq +'"] button, #media-list .col[data-seq="'+ row.med_seq +'"] button, #event-list .col[data-seq="'+ row.info_seq +'"] button').addClass('on');
            if(typeof(cnt['advertiser'][row.adv_seq]) == 'undefined') cnt['advertiser'][row.adv_seq] = 0;
            if(typeof(cnt['media'][row.med_seq]) == 'undefined') cnt['media'][row.med_seq] = 0;
            if(typeof(cnt['event'][row.info_seq]) == 'undefined') cnt['event'][row.info_seq] = 0;
            cnt['advertiser'][row.adv_seq] += parseInt(row.countAll);
            cnt['media'][row.med_seq] += parseInt(row.countAll);
            cnt['event'][row.info_seq] += parseInt(row.countAll);
        });
        $('.client-list .col .txt').empty();
        $.each(cnt, function(type, row) {
            var $container = $('#'+type+'-list');
            $.each(row, function(seq, cnt) {
                if(typeof(cnt) == 'undefined') return true;
                $('.col[data-seq="'+ seq +'"] .txt', $container).html(cnt);
            })
        })
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
                    $('.statusCount').append('<dl><dt>' + key + '</dt><dd>' + value + '</dd></dl>');
                });
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }

    function setButtons(data) { //광고주,매체,이벤트명 버튼 세팅
        $.each(data, function(type, row) {
            var html = "";
            $.each(row, function(idx, v) {
                html += '<div class="col" data-seq="'+v.seq+'"><div class="inner">';
                html += '<button type="button" value="'+v.seq+'">' + v.name + '</button>';
                html += '<div class="progress">';
                html += '<div class="txt">' + v.total + '</div>';
                html += '</div>';
                html += '</div></div>';
            });
            $('#'+type+'-list').html(html);
        });
    }

    function getLead(data = []){
        $.ajax({
            type: "get",
            url: "<?=base_url()?>/integrate/lead",
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

    $('body').on('click', '#advertiser-list button, #media-list button, #event-list button', function() {
        $(this).toggleClass('active');

        advertiser = $('#advertiser-list button.active').map(function(){return $(this).val();}).get();
		media = $('#media-list button.active').map(function(){return $(this).val();}).get();
		event = $('#event-list button.active').map(function(){return $(this).val();}).get();

        activeArray = {
            'adv_seq': advertiser,
            'media': media,
            'event': event
        };
        data = Object.assign(data, activeArray);

		getLeadCount(data, $(this).attr('id'));
        getStatusCount(data);
        $('#deviceTable').DataTable().destroy();
        getList(data);
	});

    $('body').on('click', '#search_btn', function() {
        stx = $('#stx').val();
        activeArray = {
            'stx': stx,
        };

        data = Object.assign(data, activeArray);

		getLeadCount(data);
        getStatusCount(data);
        $('#deviceTable').DataTable().destroy();
        getList(data);
	});

    if($('#sdate, #edate').length){
        var currentDate = moment().format("YYYY-MM-DD");
        $('#sdate, #edate').daterangepicker({
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
})

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
