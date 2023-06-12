<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 통합
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.js"></script>
<style>
    .inner button.disapproval::after{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        content: "";
        background: #ce1922;
    }

    .inner button.tag-inactive{
        opacity: 0.5;
    }

    .campaign-total td{
        text-align: center !important;
    }
    .hl-red{
        color: red;
    }
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">통합 광고 관리</h2>
        <p class="title-disc">광고주별 매체의 기본적인 광고 합성/종료/수정의 기능을 제공하고 있으며, 추가적으로 CHAIN에서 개발한 스마트하게 광고를 최적화 시켜주는 기능도 함께 이용할 수 있습니다.</p>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="button">조회</button>
            </div>
        </form>
        <div class="detail row d-flex justify-content-center" id="reportData">
            <dl class="col">
                <dt>노출수</dt>
                <dd id="impressions_sum"></dd>
            </dl>
            <dl class="col">
                <dt>클릭수</dt>
                <dd id="clicks_sum"></dd>
            </dl>
            <dl class="col">
                <dt>클릭률</dt>
                <dd id="click_ratio_sum"></dd>
            </dl>
            <dl class="col">
                <dt>지출액</dt>
                <dd id="spend_sum"></dd>
            </dl>
            <dl class="col">
                <dt>매체비</dt>
                <dd id="spend_ratio_sum"></dd>
            </dl>
            <dl class="col">
                <dt>DB수</dt>
                <dd id="unique_total_sum"></dd>
            </dl>
            <dl class="col">
                <dt>DB당 단가</dt>
                <dd id="unique_one_price_sum"></dd>
            </dl>
            <dl class="col">
                <dt>전환율</dt>
                <dd id="conversion_ratio_sum"></dd>
            </dl>
            <dl class="col">
                <dt>수익률</dt>
                <dd id="per_sum"></dd>
            </dl>
            <dl class="col">
                <dt>매출</dt>
                <dd id="price_01_sum"></dd>
            </dl>
        </div>
    </div>

    <div class="section client-list media">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" value="facebook" id="media_btn" class="media_btn active">페이스북</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" value="kakao" id="media_btn" class="media_btn">카카오</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" value="google" id="media_btn" class="media_btn">구글</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" value="naver" id="media_btn" class="media_btn">네이버</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="section client-list googlebiz"></div>

    <div class="section client-list facebookbiz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-down"></i> 페이스북 비즈니스 계정</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" class="filter_btn" id="business_btn" value="316991668497111">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="filter_btn" id="business_btn" value="2859468974281473">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="filter_btn" id="business_btn" value="213123902836946">케어랩스7</button>
                </div>
            </div>
        </div>
    </div> -->

    <div class="section client-list advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 광고주</h3>
        <div class="row">
        </div>
    </div>

    <div class="tab-wrap">
        <ul class="nav nav-tabs" id="tab-list" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link active" value="campaigns" type="button" id="campaign-tab" data-bs-toggle="tab" data-bs-target="#campaign-tab-pane" role="tab" aria-controls="campaign-tab-pane" aria-selected="true">캠페인</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="adsets" type="button" role="tab" id="set-tab" data-bs-toggle="tab" data-bs-target="#set-tab-pane" aria-controls="set-tab-pane" aria-selected="false">광고 세트</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="ads" type="button" role="tab" id="advertisement-tab" data-bs-toggle="tab" data-bs-target="#advertisement-tab-pane"  aria-controls="advertisement-tab-pane" aria-selected="false">광고</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="btn-wrap">
                <button type="button" class="btn btn-outline-danger active">수동 업데이트</button>
                <button type="button" class="btn btn-outline-danger">데이터 비교</button>
                <button type="button" class="btn btn-outline-danger"><i class="bi bi-file-text"></i> 메모확인</button>
            </div>
            <div class="tab-pane active" id="campaign-tab-pane" role="tabpanel" aria-labelledby="campaign-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default" id="campaigns-table">
                        <thead class="table-dark campaign-head">
                            <tr>
                                <th scope="col">매체명</th>
                                <th scope="col">캠페인명</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산</th>
                                <th scope="col">현재 <br>DB단가</th>
                                <th scope="col">유효 <br>DB</th>
                                <th scope="col">지출액</th>
                                <th scope="col">수익</th>
                                <th scope="col">수익률</th>
                                <th scope="col">매출액</th>
                                <th scope="col">노출수</th>
                                <th scope="col">링크클릭</th>
                                <th scope="col">CPC</th>
                                <th scope="col">CTR</th>
                                <th scope="col">DB <br>전환률</th>
                            </tr>
                        </thead>
                        <thead class="campaign-total">
                            <tr id="total">
                                <td></td>
                                <td id="total-count"></td>
                                <td></td>
                                <td id="total-budget"></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-click"></td>
                                <td id="avg-cpc"></td>
                                <td id="avg-ctr"></td>
                                <td id="avg-cvr"></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="set-tab-pane" role="tabpanel" aria-labelledby="set-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default" id="adsets-table">
                        <thead class="table-dark adsets-head">
                            <tr>
                                <th scope="col">매체명</th>
                                <th scope="col">캠페인명</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산</th>
                                <th scope="col">현재 <br>DB단가</th>
                                <th scope="col">유효 <br>DB</th>
                                <th scope="col">지출액</th>
                                <th scope="col">수익</th>
                                <th scope="col">수익률</th>
                                <th scope="col">매출액</th>
                                <th scope="col">노출수</th>
                                <th scope="col">링크클릭</th>
                                <th scope="col">CPC</th>
                                <th scope="col">CTR</th>
                                <th scope="col">DB <br>전환률</th>
                            </tr>
                        </thead>
                        <thead class="adsets-total">
                            <tr id="total">
                                <td></td>
                                <td id="total-count"></td>
                                <td></td>
                                <td id="total-budget"></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-click"></td>
                                <td id="avg-cpc"></td>
                                <td id="avg-ctr"></td>
                                <td id="avg-cvr"></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="advertisement-tab-pane" role="tabpanel" aria-labelledby="advertisement-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default" id="ads-table">
                        <thead class="table-dark ads-head">
                            <tr>
                                <th scope="col">매체명</th>
                                <th scope="col">캠페인명</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산</th>
                                <th scope="col">현재 <br>DB단가</th>
                                <th scope="col">유효 <br>DB</th>
                                <th scope="col">지출액</th>
                                <th scope="col">수익</th>
                                <th scope="col">수익률</th>
                                <th scope="col">매출액</th>
                                <th scope="col">노출수</th>
                                <th scope="col">링크클릭</th>
                                <th scope="col">CPC</th>
                                <th scope="col">CTR</th>
                                <th scope="col">DB <br>전환률</th>
                            </tr>
                        </thead>
                        <thead class="ads-total">
                            <tr id="total">
                                <td></td>
                                <td id="total-count"></td>
                                <td></td>
                                <td id="total-budget"></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-click"></td>
                                <td id="avg-cpc"></td>
                                <td id="avg-ctr"></td>
                                <td id="avg-cvr"></td>
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
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let data = {};
let dataTable;
var tableId = '#campaigns-table';
setDate();
/*9
getReport();
getAccount(); 
*/
getList(tableId);

function setData() {
    data = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'type': $('.tab-link.active').val(),
        'media': $('#media_btn.active').map(function(){return $(this).val();}).get(),
        'business': $('#business_btn.active').map(function(){return $(this).val();}).get().join('|'),
        'accounts': $('#account_btn.active').map(function(){return $(this).val();}).get().join('|'),
    };
    return data;
}

function getReport(){
    var data = setData();
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/report",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setReport(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setReport(data){
    $('#reportData dl dd').text('')
    $('#impressions_sum').text(data.impressions_sum);//노출수
    $('#clicks_sum').text(data.clicks_sum);//클릭수
    $('#click_ratio_sum').text(data.click_ratio_sum);//클릭률
    $('#spend_sum').text(data.spend_sum);//지출액
    $('#spend_ratio_sum').text(data.spend_ratio_sum);//매체비
    $('#unique_total_sum').text(data.unique_total_sum);//DB수
    $('#unique_one_price_sum').text(data.unique_one_price_sum);//DB당 단가
    $('#conversion_ratio_sum').text(data.conversion_ratio_sum);//전환율
    $('#per_sum').text(data.per_sum);//수익률
    $('#price_01_sum').text(data.price_sum);//매출
}

/* function getGoogleManageAccount(){
    var data = setData();
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/google/manageaccounts",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('.googlebiz .row').empty();
            var html = '';
            var set_ratio = '';
            $.each(data, function(idx, v) {     
                html += '<div class="col"><div class="inner"><button type="button" value="'+v.customerId+'" id="business_btn" class="filter_btn">'+v.name+'</button></div></div>';
            });

            $('.googlebiz .row').html(html);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
} */

function getAccount(){
    var data = setData();
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/accounts",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data){
                setAccount(data);
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setAccount(data){
    $('.advertiser .row').empty();
    var html = '';
    var set_ratio = '';
    $.each(data, function(idx, v) {     
        if(v.class){
            c = v.class;
            c = c.join(' ');
        }  
        if(v.db_count){
            set_ratio = '<div class="progress"><div class="progress-bar" role="progressbar" style="width:'+v.db_ratio+'%"></div><div class="txt">'+v.db_sum+'/'+v.db_count+'</div></div>';
        }
        html += '<div class="col"><div class="inner"><button type="button" value="'+v.id+'" id="account_btn" class="filter_btn '+(c ? c : '')+'">'+v.name+(set_ratio ? set_ratio : '')+'</button></div></div>';
    });

    $('.advertiser .row').html(html);
}

function getList(tableId){
    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }

    dataTable = $(tableId).DataTable({
        "autoWidth": false,
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "deferRender": false,
        "serverSide" : true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/data",
            "data": function(d) {
                d.searchData = setData();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                if(res.total == null){
                    return false;
                }else{
                    setDataTableTotal(res, tableId);
                    return res.data;
                }
            }
        },
        "columns": [
            { "data": "media", "width": "6%"},
            { "data": "name", "width": "10%"},
            { "data": "status", "width": "4%"},
            { "data": "budget", "width": "9%"},//예산
            { "data": "cpa","width": "7%"},//현재 DB단가
            { "data": "unique_total", "width": "3%"},//유효DB
            { "data": "spend","width": "9%"},//지출액
            { "data": "margin","width": "9%"},//수익
            { "data": "margin_ratio","width": "5%"},//수익률
            { "data": "sales","width": "9%"},//매출액
            { "data": "impressions", "width": "7%"},//노출수
            { "data": "click", "width": "5%"},//클릭수
            { "data": "cpc", "width": "5%"}, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", "width": "5%"}, //클릭율 (노출 대비 클릭한 비율)
            { "data": "cvr", "width": "3%"}, //전환율
        ],
        "columnDefs": [
            {
                "render": function (data, type, row) {
                    media = '<div class="check"><input type="checkbox" name="check01" data="'+row.id+'" id="label_'+row.id+'"><label for="label_'+row.id+'">체크</label></div><label for="label_'+row.id+'">'+row.media+'</label>';
                    return media;
                },
                targets: 0,
            },
            {
                "render": function (data, type, row) {
                    str = row.name.replace(/(\@[0-9]+)/, '<span class="hl-red">$1</span>', row.name);
                    name = '<div id="mediaName"><p data-editable="true">'+str+'</p><button class="btn-memo"><span class="blind">메모</span></button></div>';
                    return name;
                },
                targets: 1,
            },
            {
                "render": function (data, type, row) {
                    status = '<select name="status" class="active-select" id="status_btn"><option value="OFF" '+(row.status === "OFF" ? 'selected' : '')+'>비활성</option><option value="ON" '+(row.status === "ON" ? 'selected' : '')+'>활성</option></select><button class="btn-history"><span class="hide">내역확인아이콘</span></button>';
                    return status;
                },
                targets: 2,
            },
            {
                "render": function (data, type, row) {
                    budget = '<div class="budget">'+(row.budget || row.ai2_status == 'ON' ? '\u20A9'+row.budget : '-')+'</div><div class="btn-budget"><button class="btn-budget-up"><span class="">상향아이콘</span></button><button class="btn-budget-down"><span class="">하향아이콘</span></button></div>';
                    return budget;
                },
                targets: 3,
            },
            {
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
                targets: 4,
            },
            {
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
                targets: 6,
            },
            {
                "render": function (data, type, row) {
                    if(data < 0){
                        margin = '\u20A9'+data; 
                        return '<span style="color:red">'+margin+'</span>';
                    }else{
                        margin = '\u20A9'+data; 
                    }
                    return margin;
                },
                targets: 7,
            },
            {
                "render": function (data, type, row) {
                    if(data < 20 && data != 0){
                        margin_ratio = data+'\u0025';   
                        return '<span style="color:red">'+margin_ratio+'</span>';
                    }else{
                        margin_ratio = data+'\u0025';   
                    }

                    return margin_ratio;
                },
                targets: 8,
            },
            {
                "render": function (data, type, row) {
                    return '\u20A9'+data;  
                },
                targets: 9,
            },
            {
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
                targets: 12,
            },
            {
                "render": function (data, type, row) {
                    return data+'\u0025';
                },
                targets: 13,
            },
            {
                "render": function (data, type, row) {
                    return data+'\u0025';
                },
                targets: 14,
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
            $(row).attr("data-customerId", data.customerId ? data.customerId : '');
        },
        "language": {
            "emptyTable": "데이터가 존재하지 않습니다.",
            "infoEmpty": "데이터가 존재하지 않습니다.",
            "zeroRecords": "데이터가 존재하지 않습니다.",
            "loadingRecords": "로딩중...",
        }
    });
}

function setDataTableTotal(res, tableId){
    if(res.total.margin < 0){
        $(tableId+' #total-margin').css('color', 'red');
    }
    
    if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
        $(tableId+' #avg_margin_ratio').css('color', 'red');
    }

    $(tableId+' #total-count').text(res.data.length+"건 결과");
    $(tableId+' #total-budget').text('\u20A9'+res.total.budget);//예산
    $(tableId+' #avg-cpa').text('\u20A9'+res.total.avg_cpa);//현재 DB 단가
    $(tableId+' #total-unique_total').html('<div>'+res.total.unique_total+'</div><div style="color:blue">'+res.total.expect_db+'</div>');
    $(tableId+' #total-spend').text('\u20A9'+res.total.spend);
    $(tableId+' #total-margin').text('\u20A9'+res.total.margin);
    $(tableId+' #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
    $(tableId+' #total-sales').text('\u20A9'+res.total.sales);
    $(tableId+' #total-impressions').text(res.total.impressions);
    $(tableId+' #total-click').text(res.total.click);
    $(tableId+' #avg-cpc').text('\u20A9'+res.total.avg_cpc);
    $(tableId+' #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
    $(tableId+' #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');
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

function sendStatus(data){
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/advertisements/set-status",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            if(data.response == true){
                alert("변경되었습니다.");
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function sendName(data, inputElement) {
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/advertisements/set-name",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            if (data.response == true) {
                var $new_p = $('<p data-editable="true">');
                $new_p.text(data.name);
                inputElement.replaceWith($new_p);
            }
        },
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

function restoreElement(text, inputElement) {
    var $old_p = $('<p data-editable="true">');
    $old_p.text(text);
    inputElement.replaceWith($old_p);
}

function handleInput(tab, id, tmp_name, inputElement) {
    var new_name = inputElement.val();
    var data = {
        'name': new_name,
        'tab': tab,
        'id': id,
    };
    var customerId = inputElement.closest("tr").data("customerid");
    if (customerId) {
        data['customerId'] = customerId;
    }

    if (tmp_name === new_name) {
        restoreElement(tmp_name, inputElement);
    } else {
        sendName(data, inputElement);
    }
}

$('body').on('click', '.media_btn', function(){
    $(this).toggleClass("active");
    var data = setData();
    /* if(data.media.includes('google')){
        html = '<h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 구글 매니저 계정</h3><div class="row"></div>'
        $('.googlebiz').html(html);
        getGoogleManageAccount(data);
    }else{
        $('.googlebiz').empty();
    } */

    /* if(data.media.includes('facebook')){
        html = '<h3 class="content-title toggle"><i class="bi bi-chevron-down"></i> 페이스북 비즈니스 계정</h3><div class="row"><div class="col"><div class="inner"><button type="button" class="filter_btn" id="business_btn" value="316991668497111">열혈 패밀리</button></div></div><div class="col"><div class="inner"><button type="button" class="filter_btn" id="business_btn" value="2859468974281473">케어랩스5</button></div></div><div class="col"><div class="inner"><button type="button" class="filter_btn" id="business_btn" value="213123902836946">케어랩스7</button></div></div></div>';
        $('.facebookbiz').html(html);
    }else{
        $('.facebookbiz').empty();
    } */

    getReport(data);
    getAccount(data);
    dataTable.draw();
});

$('body').on('click', '.tab-link', function(){
    tab = $(this).val();
    switch (tab) {
    case "ads":
        tableId = '#ads-table'
        break;
    case "adsets":
        tableId = '#adsets-table'
        break;
    default:
        tableId = '#campaigns-table'
    }
    var data = setData();
    getList(tableId);
});

$('body').on('click', '#business_btn, #account_btn', function(){
    $(this).toggleClass("active");
    
    if ($(this).attr('id') === 'business_btn') {
        getAccount();
    }

    getReport(data);
    dataTable.draw();
});

$('body').on('click', '#search_btn', function() {
    getReport(data);
    getAccount(data);
    dataTable.draw();
});

$('body').on('focus', '#status_btn', function(){
    var prevVal = $(this).val();
}).on('change', '#status_btn', function() {
    data = {
        'status' : $(this).val(),
        'tab' : $('.tab-link.active').val(),
        'id' : $(this).closest("tr").data("id"),
    };

    customerId = $(this).closest("tr").data("customerid");
    if (customerId) {
        data['customerId'] = customerId;
    }

    if(confirm("상태를 변경하시겠습니까?")){
        sendStatus(data);
    }else{
        $(this).val(prevVal);
    }
});

$("body").on("click", '#mediaName p[data-editable="true"]', function(){
    tab = $('.tab-link.active').val();
    id = $(this).closest("tr").data("id");
    if((tab == 'ads' && id.includes('google')) || (tab == 'adsets' && id.includes('kakao'))){
        return false;
    }else{
        $('#mediaName p[data-editable="true"]').attr("data-editable", "false");
        var tmp_name = $(this).text();
        var $input = $('<input type="text" style="width:100%">');
        $input.val(tmp_name);
        $(this).replaceWith($input);
        $input.focus();
        
        $input.on('keydown blur', function(e) {
            if (e.type === 'keydown') {
                if (e.keyCode == 27) {
                    // ESC Key
                    restoreElement(tmp_name, $input);
                } else if (e.keyCode == 13) {
                    handleInput(tab, id, tmp_name, $input);
                }
            } else if (e.type === 'blur') {
                handleInput(tab, id, tmp_name, $input);
            }
        });

        $('#mediaName p[data-editable="false"]').attr("data-editable", "true");
    }
});


</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>