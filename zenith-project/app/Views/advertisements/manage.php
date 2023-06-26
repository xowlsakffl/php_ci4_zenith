<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 통합
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore/js/dataTables.stateRestore.min.js"></script>
<script src="/static/js/jszip.min.js"></script>
<script src="/static/js/pdfmake/pdfmake.min.js"></script>
<script src="/static/js/pdfmake/vfs_fonts.js"></script>
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
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
            </div>
        </form>
        <div class="detail row d-flex justify-content-center">
            <div class="reportData detail d-flex minWd"></div> 
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
                    <button type="button" value="kakao" id="media_btn" class="media_btn active">카카오</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" value="google" id="media_btn" class="media_btn">구글</button>
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
var exportCommon = {
    'exportOptions': { //{'columns': 'th:not(:last-child)'},
        'modifier': {'page':'all'},
    }
};

var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let dataTable, tableParam = {};
if(typeof tableParam != 'undefined'){
    tableParam.searchData = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'type': $('.tab-link.active').val(),
        'media' : $('#media_btn.active').map(function(){return $(this).val();}).get().join('|'), 
    };
}
console.log(tableParam.searchData.type);
switch (tableParam.searchData.type) {
    case 'campaigns':
        tableId = '#campaigns-table';
        break;
        
    default:
        break;
}
getList();
function setSearchData() {
    var data = tableParam;
    $('#media_btn, #business_btn, #company_btn, .reportData dl').removeClass('active');
    if(typeof data.searchData == 'undefined') return;

    if(data.searchData.media){
        data.searchData.media.split('|').map(function(txt){ $(`#media_btn[value="${txt}"]`).addClass('active'); });
    }
    
    if(data.searchData.company){
        data.searchData.company.split('|').map(function(txt){ $(`#company_btn[value="${txt}"]`).addClass('active'); });
    }

    if(data.searchData.report){
        data.searchData.report.split('|').map(function(txt){
            $('.reportData dt:contains("'+txt+'")').filter(function() { return $(this).text() === txt;}).parent().addClass('active');
        });
    }

    $('.tab-link[value="'+data.searchData.type+'"]').addClass('active');
    $('#sdate').val(data.searchData.sdate);
    $('#edate').val(data.searchData.edate);
    $('#stx').val(data.searchData.stx);
    debug('searchData 세팅')
    if(typeof dataTable != 'undefined') dataTable.state.save();
}

$.fn.DataTable.Api.register('buttons.exportData()', function (options) { //Serverside export
    var arr = [];
    $.ajax({
        "url": "<?=base_url()?>/advertisements/data",
        "data": {"searchData":tableParam.searchData, "noLimit":true},
        "type": "GET",
        "contentType": "application/json",
        "dataType": "json",
        "success": function (result) {
            console.log(result);
            $.each(result, function(i,row) {
                // arr.push(Object.keys(result[key]).map(function(k) {  return result[key][k] }));
                //arr.push([row.seq, row.info_seq, row.advertiser, row.media, row.tab_name, row.name, row.dec_phone, row.age, row.gender, row.add, row.site, row.reg_date, lead_status[row.status]]);
            });
        },
        "async": false
    });
    // return {body: arr , header: $("#deviceTable thead tr th").map(function() { return $(this).text(); }).get()};
    return {body: arr , header: ["고유번호","이벤트","광고주","매체","이벤트 구분","이름","전화번호","나이","성별","기타","사이트","등록일시","인정기준"]};
} );

function getList(data = []){
    dataTable = $('#campaigns-table').DataTable({
        "autoWidth": false,
        "columnDefs": [
            { targets: '_all', visible: true },
        ],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "paging": false,
        "info": false,
        "scrollX": true,
        "scrollY": 500,
        "scrollCollapse": true,
        "stateSave": true,
        "stateSaveParams": function (settings, data) { //LocalStorage 저장 시
            debug('state 저장')
            //data.memoView = $('.btns-memo.active').val();
            data.searchData = {
                'sdate': $('#sdate').val(),
                'edate': $('#edate').val(),
                'stx': $('#stx').val(),
                'type': $('.tab-link.active').val(),
                'media' : $('#media_btn.active').map(function(){return $(this).val();}).get().join('|'),
                'company' : $('#company_btn.active').map(function(){return $(this).val();}).get().join('|'),
                'report' : $('.reportData dl.active').map(function(){return $('dt',this).text();}).get().join('|')
            };
            tableParam = data;
            debug(tableParam.searchData);
        },
        "stateLoadParams": function (settings, data) { //LocalStorage 호출 시
            debug('state 로드')
            //$(`.btns-memo[value="${data.memoView}"]`).addClass('active');
            tableParam = data;
            setSearchData();
            debug(tableParam.searchData);
        },
        "deferRender": true,
        "buttons": [
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
                    '<h3>내보내기</h3>',
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
                    } )
                ]
            },
            
        ],
        "ajax": {
            "url": "<?=base_url()?>/advertisements/data",
            "data": function(d) {
                if(typeof tableParam != 'undefined')
                    d.searchData = tableParam.searchData;
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": [
            { "data": "media", "width": "6%"},
            { "data": "name", "width": "10%"},
            { "data": "status", "width": "4%"},
            { "data": "budget", "width": "9%"},
            { "data": "cpa","width": "7%"},
            { "data": "unique_total", "width": "3%"},
            { "data": "spend","width": "9%"},
            { "data": "margin","width": "9%"},
            { "data": "margin_ratio","width": "5%"},
            { "data": "sales","width": "9%"},
            { "data": "impressions", "width": "7%"},
            { "data": "click", "width": "5%"},
            { "data": "cpc", "width": "5%"}, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", "width": "5%"}, //클릭율 (노출 대비 클릭한 비율)
            { "data": "cvr", "width": "3%"}, //전환율
        ],
        /* "columnDefs": [
            {
                "render": function (data, type, row) {
                    switch (row.media) {
                        case 'facebook':
                            media = '페이스북';
                            break;
                        case 'google':
                            media = '구글';
                            break;
                        case 'kakao':
                            media = '카카오';
                            break;
                        default:
                            break;
                    }
                    media = '<div class="check"><input type="checkbox" name="check01" data="'+row.id+'" id="label_'+row.id+'"><label for="label_'+row.id+'">체크</label></div><label for="label_'+row.id+'">'+media+'</label>';
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
                    budget = '<div class="budget">'+(row.budget == 0 ? '-' : '\u20A9'+row.budget)+'</div><div class="btn-budget"><button class="btn-budget-up"><span class="">상향아이콘</span></button><button class="btn-budget-down"><span class="">하향아이콘</span></button></div>';
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
        ], */
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.id);
            $(row).attr("data-customerId", data.customerId ? data.customerId : '');
        },
        "language": {
            "url": '/static/js/dataTables.i18n.json' //CDN 에서 한글화 수신
        },
    }).on('xhr.dt', function( e, settings, data, xhr ) {
        /* setButtons(data.buttons);
        setLeadCount(data.buttons)
        setStatusCount(data.buttons.status); */
        setReport(data.report);
        setAccount(data.accounts)
        setDataTableTotal(data, tableId)
        setDate();
        setSearchData();
    });;
}

function setDataTableTotal(res, tableId){
    console.log(tableId);
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
    //$(tableId+' #total-margin').text('\u20A9'+res.total.margin);
    $(tableId+' #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
    $(tableId+' #total-sales').text('\u20A9'+res.total.sales);
    $(tableId+' #total-impressions').text(res.total.impressions);
    $(tableId+' #total-click').text(res.total.click);
    $(tableId+' #avg-cpc').text('\u20A9'+res.total.avg_cpc);
    $(tableId+' #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
    $(tableId+' #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');
}

/* function getReport(){
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
} */

function setReport(data){
    $('.reportData').empty();
    $.each(data, function(key, value) {
        switch (key) {
            case 'impressions_sum':
                newKey = '노출수';
                break;
            case 'clicks_sum':
                newKey = '클릭수';
                break;
            case 'click_ratio_sum':
                newKey = '클릭률';
                break;
            case 'spend_sum':
                newKey = '지출액';
                break;
            case 'spend_ratio_sum':
                newKey = '매체비';
                break;
            case 'unique_total_sum':
                newKey = 'DB수';
                break;
            case 'unique_one_price_sum':
                newKey = 'DB당 단가';
                break;
            case 'conversion_ratio_sum':
                newKey = '전환율';
                break;
            case 'per_sum':
                newKey = '수익률';
                break;
            case 'profit_sum':
                newKey = '수익';
                break;
            case 'price_sum':
                newKey = '매출';
                break;
            case 'cpc':
                newKey = 'CPC';
                break;
            default:
                break;
        }
        $('.reportData').append('<dl class="col"><dt>' + newKey + '</dt><dd>' + value + '</dd></dl>');
    });
}

function setAccount(data) {
    var $row = $('.advertiser .row');

    var existingIds = [];
    $row.find('.filter_btn').each(function() {
        existingIds.push($(this).val());
    });

    var html = '';
    $.each(data, function(idx, v) {
        var companyId = v.company_id.toString();

        if (existingIds.includes(companyId)) {
            existingIds = existingIds.filter(id => id !== companyId);
        } else {
            html += '<div class="col"><div class="inner"><button type="button" value="' + companyId + '" id="company_btn" class="filter_btn">' + v.company_name + '</button></div></div>';
        }
    });

    $row.find('.filter_btn').filter(function() {
        return existingIds.includes($(this).val());
    }).parent().parent().remove();

    $row.append(html);
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

$('body').on('click', '#media_btn, #company_btn, .tab-link', function() {
    $(this).toggleClass('active');
    debug('필터링 탭 클릭');
    dataTable.state.save();
    dataTable.draw();
});

$('form[name="search-form"]').bind('submit', function() {
    debug('검색 전송')
    dataTable.state.save();
    dataTable.draw();
    return false;
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

function debug(msg) {
    console.log(msg);
}
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>