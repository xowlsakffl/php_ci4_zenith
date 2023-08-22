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
<script src="/static/js/jquery.number.min.js"></script>
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
    .dt-buttons{
        position: initial !important;
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
        <div class="row d-flex">
            <div class="reportData detail d-flex minWd justify-content-center">

            </div> 
        </div>
    </div>

    <div class="section client-list media">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" value="facebook" id="media_btn" class="media_btn">페이스북</button>
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
        <div class="row" id="advertiser-list">
        </div>
    </div>

    <div class="section client-list media-advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체별 광고주</h3>
        <div class="row" id="media-advertiser-list">
        </div>
    </div>

    <div class="tab-wrap">
        <ul class="nav nav-tabs" id="tab-list" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="campaigns" type="button" id="campaign-tab">캠페인</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="adsets" type="button" id="set-tab">광고 세트</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="ads" type="button" role="tab" id="advertisement-tab">광고</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="btn-wrap">
                <button type="button" class="btn btn-outline-danger" id="update_btn">수동 업데이트</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#data-modal">데이터 비교</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#memo-check-modal"><i class="bi bi-file-text"></i> 메모확인</button>
            </div>
            <!-- <div class="btns-memo-style">
                <span class="btns-title">메모 표시:</span>
                <button type="button" class="btns-memo" value="modal" title="새창으로 표시"><i class="bi bi-window-stack"></i></button>
                <button type="button" class="btns-memo" value="table" title="테이블에 표시"><i class="bi bi-table"></i></button>
            </div> -->
            <div class="tab-pane active">
                <div class="table-responsive text-center">
                    <table class="dataTable table table-striped table-hover table-default" id="adv-table">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">체크</th>
                                <th scope="col">매체</th>
                                <th scope="col">제목</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산</th>
                                <th scope="col">현재<br>DB단가</th>
                                <th scope="col">유효<br>DB</th>
                                <th scope="col">지출액</th>
                                <th scope="col">수익</th>
                                <th scope="col">수익률</th>
                                <th scope="col">매출액</th>
                                <th scope="col">노출수</th>
                                <th scope="col">링크<br>클릭</th>
                                <th scope="col">CPC</th>
                                <th scope="col">CTR</th>
                                <th scope="col">DB <br>전환률</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr id="total">
                                <td></td>
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
        <!-- 메모 작성 -->
        <div class="modal fade" id="memo-write-modal" tabindex="-1" aria-labelledby="memo-write-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm sm-txt">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="memo-write-modal-label"><i class="bi bi-file-text"></i> 메모 작성</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form name="memo-regi-form" class="regi-form">
                        <input type="hidden" name="id">
                        <input type="hidden" name="media">
                        <input type="hidden" name="type">
                            <fieldset>
                                <legend>메모 작성</legend>
                                <textarea name="memo"></textarea>
                                <button type="submit" class="btn-regi">작성</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- //메모 작성 -->
        <!-- 메모 확인 -->
        <div class="modal fade" id="memo-check-modal" tabindex="-1" aria-labelledby="memo-check-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="memo-check-modal-label"><i class="bi bi-file-text"></i> 메모 확인</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="memo-list m-2"></ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- //메모 확인 -->
        <!-- 데이터 비교 -->
        <div class="modal fade" id="data-modal" tabindex="-1" aria-labelledby="data-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="data-modal-label">데이터 비교</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="sorting d-flex justify-content-between align-items-end">
                            <div class="d-flex" id="diffBtn">
                                <button type="button" class="active" value="7days">최근 7일</button>
                                <button type="button" value="14days">최근 14일</button>
                                <button type="button" value="30days">최근 30일</button>
                                <button type="button" value="prevmonth">지난달</button>
                                <button type="button" value="thismonth">이번달</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-data">
                                <colgroup>
                                    <col style="width:calc(100% / 6);">
                                    <col style="width:calc(100% / 6);">
                                    <col style="width:calc(100% / 6);">
                                    <col style="width:calc(100% / 6);">
                                    <col style="width:calc(100% / 6);">
                                    <col style="width:calc(100% / 6);">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">DB 단가</th>
                                        <th scope="col">DB 수</th>
                                        <th scope="col">수익률</th>
                                        <th scope="col">CPC</th>
                                        <th scope="col">전환률</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="dataDiffToday">
                                        <th scope="col">오늘</th>
                                        <td class="unique_one_price_sum"></td>
                                        <td class="unique_total_sum"></td>
                                        <td class="per_sum"></td>
                                        <td class="cpc"></td>
                                        <td class="conversion_ratio_sum"></td>
                                    </tr>
                                    <tr id="dataDiffYesterday">
                                        <th scope="col">어제</th>
                                        <td class="unique_one_price_sum"></td>
                                        <td class="unique_total_sum"></td>
                                        <td class="per_sum"></td>
                                        <td class="cpc"></td>
                                        <td class="conversion_ratio_sum"></td>
                                    </tr>
                                    <tr id="dataDiffPrev">
                                        <th scope="col" class="text-danger" id="customDiffTh">최근 7일</th>
                                        <td class="unique_one_price_sum"></td>
                                        <td class="unique_total_sum"></td>
                                        <td class="per_sum"></td>
                                        <td class="cpc"></td>
                                        <td class="conversion_ratio_sum"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="term"></p>
                    </div>
                </div>
            </div>
        </div>
    <!-- //데이터 비교 -->
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
var media_str = {
    "facebook":"페이스북", 
    "kakao":"카카오", 
    "google":"구글", 
};

var status_str = {
    "ON":"활성", 
    "OFF":"비활성", 
};

var exportCommon = {
    'exportOptions': { //{'columns': 'th:not(:last-child)'},
        'modifier': {'page':'all'},
    }
};

var today = moment().format('YYYY-MM-DD');
$('#sdate, #edate').val(today);

let dataTable, tableParam = {};
let loadData = false;
//if(typeof tableParam != 'undefined'){
    $('.tab-link[value="campaigns"]').addClass('active');
    $('#media_btn[value="facebook"]').addClass('active');

    tableParam.searchData = {
        'type': $('.tab-link.active').val(),
        'media' : $('#media_btn.active').map(function(){return $(this).val();}).get().join('|'), 
    };
//}

getList();
function setSearchData() {
    var data = tableParam;
    console.log(data.searchData);
    if(typeof data.searchData == 'undefined') return;

    $('#media_btn, #business_btn, #company_btn').removeClass('active');
    $('.check input[name="check01"]').prop('checked', false);

    if(data.searchData.media){
        data.searchData.media.split('|').map(function(txt){ $(`#media_btn[value="${txt}"]`).addClass('active'); });
    }
    
    if(data.searchData.company){
        data.searchData.company.split('|').map(function(txt){ $(`#company_btn[value="${txt}"]`).addClass('active'); });
    }

    if(data.searchData.account){
        data.searchData.account.split('|').map(function(txt){ $(`#media_account_btn[value="${txt}"]`).addClass('active'); });
    }

    if(data.searchData.check){
        data.searchData.check.map(function(txt){ $(`.check input[value="${txt}"]`).prop('checked', true); });
    }

    $('.tab-link').removeClass('active');
    $('.tab-link[value="'+data.searchData.type+'"]').addClass('active');
    //$('#sdate').val(data.searchData.sdate);
    //$('#edate').val(data.searchData.edate);
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
            $.each(result, function(i,row) {
                //arr.push(Object.keys(result[key]).map(function(k) {  return result[key][k] }));
                arr.push([row.id, media_str[row.media], row.name, status_str[row.status], row.budget, row.impressions, row.click, row.spend, row.sales, row.unique_total, row.margin, row.margin_ratio, row.cpc, row.ctr, row.cpa, row.cvr]);
            });
        },
        "async": false
    });
    return {body: arr , header: ["고유 ID","매체명","제목","상태","예산","현재 DB단가","유효 DB","지출액","수익","수익률","매출액","노출수","링크클릭","CPC","CTR","DB 전환률"]};
} );

function getList(data = []){
    dataTable = $('#adv-table').DataTable({
        "dom": '<Bfr<t>ip>',
        "autoWidth": false,
        "order": [[1,'desc']],
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
            //if($('#advertiser-list>div').is(':visible')) {
                data.searchData = {
                    'sdate': $('#sdate').val(),
                    'edate': $('#edate').val(),
                    'stx': $('#stx').val(),
                    'type': $('.tab-link.active').val(),
                    'media' : $('#media_btn.active').map(function(){return $(this).val();}).get().join('|'),
                    'company' : $('#company_btn.active').map(function(){return $(this).val();}).get().join('|'),
                    'account' : $('#media_account_btn.active').map(function(){return $(this).val();}).get().join('|'),
                    'check' : $('.check input[name=check01]:checked').map(function(){return $(this).val();}).get(),
                };

                tableParam = data;
                debug(tableParam.searchData);
            //}
        },
        "stateLoadParams": function (settings, data) { //LocalStorage 호출 시
            debug('state 로드')
            //$(`.btns-memo[value="${data.memoView}"]`).addClass('active');
            tableParam = data;
            tableParam.searchData.sdate = today;
            tableParam.searchData.edate = today;
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
        "columnDefs": [
            { targets: [0], orderable: false},
            { targets: [2], orderable: false},
            { targets: '_all', visible: true },
        ],
        "columns": [
            { 
                "data": null, 
                "width": "5%",
                "render": function (data, type, row) {
                    media = '<div class="check"><input type="checkbox" class="form-check-input" name="check01" value="'+row.media+"_"+row.id+'"></div>';
                    return media;
                },
            },
            { 
                "data": "media", 
                "width": "5%",
                "render": function (data, type, row) {
                    media = '<div class="media_box"><i class="'+row.media+'"></i></div>';
                    return media;
                },
            },
            { 
                "data": "name", 
                "width": "15%",
                "render": function (data, type, row) {
                    name = '<div class="mediaName"><p data-editable="true">'+row.name.replace(/(\@[0-9]+)/, '<span class="hl-red">$1</span>', row.name)+'</p><button class="btn_memo text-dark position-relative" data-bs-toggle="modal" data-bs-target="#memo-write-modal"><i class="bi bi-chat-square-text h4"></i><span class="position-absolute top--10 start-100 translate-middle badge rounded-pill bg-danger badge-"></span></button></div>';
                    return name;
                },
            },
            { 
                "data": "status", 
                "width": "10%",
                "render": function (data, type, row) {
                    status = '<select name="status" class="form-select form-select-sm" id="status_btn"><option value="OFF" '+(row.status === "OFF" ? 'selected' : '')+'>비활성</option><option value="ON" '+(row.status === "ON" ? 'selected' : '')+'>활성</option></select><button class="btn-history"><span class="hide"></span><span class="material-symbols-outlined">fact_check</span></button>';
                    return status;
                },
            },
            { 
                "data": "budget", 
                "width": "10%",
                "render": function (data, type, row) {
                    budget = '<div class="budget">'+(row.budget == 0 ? '-' : '<p data-editable="true">\u20A9'+row.budget)+'</div><div class="btn-budget"><button class="btn-budget-up"><span class="material-symbols-outlined">arrow_circle_up</span></button><button class="btn-budget-down"><span class="material-symbols-outlined">arrow_circle_down</span></button></div>';
                    return budget;
                },
            },
            { 
                "data": "cpa",
                "width": "7%",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { "data": "unique_total", "width": "5%"},
            {
                "data": "spend",
                "width": "8%",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { 
                "data": "margin",
                "width": "8%",
                "render": function (data, type, row) {
                    if(data < 0){
                        margin = '\u20A9'+data; 
                        return '<span style="color:red">'+margin+'</span>';
                    }else{
                        margin = '\u20A9'+data; 
                    }
                    return margin;
                },
            },
            { 
                "data": "margin_ratio",
                "width": "5%",
                "render": function (data, type, row) {
                    if(data < 20 && data != 0){
                        margin_ratio = data+'\u0025';   
                        return '<span style="color:red">'+margin_ratio+'</span>';
                    }else{
                        margin_ratio = data+'\u0025';   
                    }

                    return margin_ratio;
                },
            },
            { 
                "data": "sales",
                "width": "9%",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { "data": "impressions", "width": "7%"},
            { "data": "click", "width": "5%"},
            { 
                "data": "cpc", 
                "width": "5%",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            }, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", "width": "5%"}, //클릭율 (노출 대비 클릭한 비율)
            { 
                "data": "cvr", 
                "width": "5%",
                "render": function (data, type, row) {
                    return data+'\u0025';
                },
            }, //전환율
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.id);
            $(row).attr("data-customerId", data.customerId ? data.customerId : '');
        },
        "language": {
            "url": '/static/js/dataTables.i18n.json' //CDN 에서 한글화 수신
        }
    }).on('xhr.dt', function( e, settings, data, xhr ) {
        if(data){
            setReport(data.report);
            setAccount(data.accounts);
            setMediaAccount(data.media_accounts)
            setTotal(data);
            setDate();
            setSearchData();
        }
    });
}

function setTotal(res){
    
    if(!res.total){
        return false;
    }else{
        if(res.total.margin < 0){
            $('#total-margin').css('color', 'red');
        }
        
        if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
            $('#avg_margin_ratio').css('color', 'red');
        }

        $('#total-count').text(res.data.length+"건 결과");
        $(' #total-budget').text('\u20A9'+res.total.budget);//예산
        $('#avg-cpa').text('\u20A9'+res.total.avg_cpa);//현재 DB 단가
        $('#total-unique_total').html('<div>'+res.total.unique_total+'</div><div style="color:blue">'+res.total.expect_db+'</div>');
        $('#total-spend').text('\u20A9'+res.total.spend);
        $('#total-margin').text('\u20A9'+res.total.margin);
        $('#avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
        $('#total-sales').text('\u20A9'+res.total.sales);
        $('#total-impressions').text(res.total.impressions);
        $('#total-click').text(res.total.click);
        $('#avg-cpc').text('\u20A9'+res.total.avg_cpc);
        $('#avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
        $('#avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');
    };
}

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

function setMediaAccount(data) {
    var $row = $('.media-advertiser .row');

    var mediaExistingIds = [];
    $row.find('.filter_media_btn').each(function() {
        mediaExistingIds.push($(this).val());
    });

    var html = '';
    $.each(data, function(idx, v) {
        var media_account_id = v.media_account_id.toString();

        if (mediaExistingIds.includes(media_account_id)) {
            mediaExistingIds = mediaExistingIds.filter(id => id !== media_account_id);
        } else {
            html += '<div class="col"><div class="inner"><button type="button" value="' + media_account_id + '" id="media_account_btn" class="filter_media_btn"><div class="media_account_txt d-flex align-items-center"><span class="media_account_icon '+v.media+'"></span><span class="media_account_name">' + v.media_account_name + '</span></div></button></div></div>';
        }
    });

    $row.find('.filter_media_btn').filter(function() {
        return mediaExistingIds.includes($(this).val());
    }).parent().parent().remove();

    $row.append(html);
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

function setManualUpdate(data){
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/advertisements/set-adv",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            if (data.response == true) {
                
            }
        },
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

$('.btns-memo-style button').bind('click', function() { //메모 표시타입
    $('.btns-memo-style button').removeClass('active');
    $(this).addClass('active');
    debug('메모표시방식 설정');
    dataTable.state.save();
});

$('body').on('click', '#media_btn, #company_btn, #media_account_btn', function() {
    $(this).toggleClass('active');
    debug('필터링 탭 클릭');
    dataTable.state.save();
    dataTable.draw();
});

$('body').on('click', '.tab-link', function() {
    $('.tab-link').removeClass('active');
    $(this).addClass('active');
    debug('필터링 탭 클릭');
    dataTable.state.save();
    dataTable.draw();
});

$('body').on('click', '#update_btn', function() {
    data = {
        'ids' : tableParam.searchData.check,
        'type' : tableParam.searchData.type
    }
    if(!data.ids.length){
        alert("업데이트 할 항목을 선택해주세요.");
		return;
    }
    setManualUpdate(data);
});

$('form[name="search-form"]').bind('submit', function() {
    debug('검색 전송')
    dataTable.state.save();
    dataTable.draw();
    return false;
});

$('body').on('change', '.check input[name=check01]', function() {
    debug('체크 선택')
    dataTable.state.save();
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/check-report",
        data: {"searchData":tableParam.searchData},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setReport(data.report);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

var prevVal;
$('body').on('focus', '#status_btn', function(){
    prevVal = $(this).val();
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

$("body").on("click", '.mediaName p[data-editable="true"]', function(){
    tab = $('.tab-link.active').val();
    id = $(this).closest("tr").data("id");
    if((tab == 'ads' && id.includes('google')) || (tab == 'adsets' && id.includes('kakao'))){
        return false;
    }else{
        $('.mediaName p[data-editable="true"]').attr("data-editable", "false");
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

        $('.mediaName p[data-editable="false"]').attr("data-editable", "true");
    }
});

function getDiffData(){
    data = tableParam.searchData;
    sortingBtnVal = $('.sorting button.active').val();
    data.diff = sortingBtnVal;

    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/diff-report",
        data: {"searchData":data},
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setDiffData(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function setDiffData(data) {
    $('#dataDiffToday td, #dataDiffYesterday td, #dataDiffPrev td').text('');
    $('#data-modal .term').text(data.customDate.date.sdate+" ~ "+data.customDate.date.edate);
    const dataFields = ['unique_one_price_sum', 'unique_total_sum', 'per_sum', 'cpc', 'conversion_ratio_sum'];
    dataFields.forEach(field => {
        $('#dataDiffToday .' + field).text(data.today[field]);
        $('#dataDiffYesterday .' + field).text(data.yesterday[field]);
        $('#dataDiffPrev .' + field).text(data.customDate[field]);
    });
}

$('#data-modal').on('show.bs.modal', function(e) {
    getDiffData();
}).on('hidden.bs.modal', function(e) { 
    $('#dataDiffToday td, #dataDiffYesterday td, #dataDiffPrev td').text('');
});

$("body").on("click", '.sorting button', function(){
    $('.sorting button').removeClass('active');
    $(this).addClass('active');
    $('#customDiffTh').text($(this).text());
    getDiffData();
});

function getMemoList() { //메모 수신
    $.ajax({
        type: "get",
        url: "<?=base_url()?>advertisements/getmemo",
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
        url: "<?=base_url()?>advertisements/addmemo",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response == true) {
                alert("등록되었습니다.");
                $('#memo-write-modal').modal('hide');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setMemoList(data) { //메모 리스트 생성
    var html =  '';
    $.each(data, function(i,row) {
        var name = '';
        switch (row.type) {
            case 'campaigns':
                name = row.campaign_name;
                break;
            case 'adsets':
                name = row.adset_name;
                break;
            case 'ads':
                name = row.ad_name;
                break;
            default:
                break;
        }
        switch (row.media) {
            case 'facebook':
                row.media = '페이스북';
                break;
            case 'google':
                row.media = '구글';
                break;
            case 'kakao':
                row.media = '카카오';
                break;
            default:
                break;
        }

        switch (row.type) {
            case 'campaigns':
                row.type = '캠페인';
                break;
            case 'adsets':
                row.type = '광고 세트';
                break;
            case 'ads':
                row.type = '광고';
                break;
            default:
                break;
        }
        html += '    <li class="d-flex justify-content-between align-items-start" data-id="'+row.seq+'">';
        html += '        <div class="detail d-flex align-items-start">';
        html += '           <input type="checkbox" name="is_done" '+(row.is_done == 1 ? "checked" : "")+'>';
        html += '            <p class="ms-1 '+(row.is_done == 1 ? "text-decoration-line-through" : "")+'">['+ row.media +'] ['+ row.type +'] '+ row.memo +'</p>';
        html += '        </div>';
        html += '        <div class="d-flex">';
        html += '            <p style="width:50px;margin-right:15px">'+ row.nickname +'</p>';
        html += '            <p style="min-width:150px">'+ row.datetime +'</p>';
        html += '        </div>';
        html += '    </li>';
    });
    
    $('ul.memo-list').prepend(html);
}

$('#memo-check-modal').on('show.bs.modal', function(e) { 
    getMemoList();
})
.on('hidden.bs.modal', function(e) { 
    $('ul.memo-list').empty();
});

$('#memo-write-modal').on('show.bs.modal', function(e) { 
    var clickedButton = $(document.activeElement).closest('tr').data('id');
    idMedia = clickedButton.split("_");
    memoType = $('.tab-link.active').val();
    $('form[name="memo-regi-form"] input[name=id]').val(idMedia[1]);
    $('form[name="memo-regi-form"] input[name=media]').val(idMedia[0]);
    $('form[name="memo-regi-form"] input[name=type]').val(memoType);
})
.on('hidden.bs.modal', function(e) { 
    $('form[name="memo-regi-form"] input[name=id]').val('');
    $('form[name="memo-regi-form"] input[name=media]').val('');
    $('form[name="memo-regi-form"] input[name=type]').val('');
    $('form[name="memo-regi-form"] textarea').val('');
});

$('form[name="memo-regi-form"]').bind('submit', function() {
    var formData = $(this).serialize();
    registerMemo(formData);
    return false;
});

$("body").on("click", '.memo-list input[name="is_done"]', function(){
    $this = $(this);
    $li = $this.closest("li");
	$seq = $li.data("id");
    $is_done = $this.is(':checked');
    if($is_done == true){
        $is_done = 1;
    }else{
        $is_done = 0;
    }
    data = {
        'is_done': $is_done,
        'seq': $seq
    };
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/advertisements/checkmemo",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response == true) {
                alert("처리되었습니다.");
                if ($is_done == 1){
                    $this.siblings('p').addClass('text-decoration-line-through');
                }else {
                    $this.siblings('p').removeClass('text-decoration-line-through');
                }
                
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

$("body").on("click", '.btn-budget button', function(){
    $this = $(this);
    $id = $this.closest("tr").data('id');
    $customer = $this.closest("tr").data('customerid');
    clickBtn = $(document.activeElement).attr('class');
    budgetTxt = $this.parent('div.btn-budget').siblings('div.budget').children('p');
    c_budget = budgetTxt.text().replace(/[^0-9]/g, "");
    if (!c_budget) return;
    
    switch (clickBtn) {
        case "btn-budget-up":
            typeName = "상향";
            rate = 1.19;
            break;
        case "btn-budget-down":
            typeName = "하향";
            rate = 0.81;
            break;
    }
    var budget = Math.round(c_budget * rate);
    console.log(budget);
    if (
        !confirm(
            "현재 예상을 " +
                typeName +
                "조정하여 " +
                (budget).toLocaleString() +
                "원으로 수정합니다."
        )
    ){
        return;
    }

    data = {
        'id': $id,
        'customer': $customer,
        'tab': $('.tab-link.active').val(),
        'budget': budget
    };
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/advertisements/set-budget",
        data: data,
        dataType: "json",
        success: function(response){  
            if(response == true) {
                $('.budget p[data-editable="true"]').attr("data-editable", "true");
                budgetTxt.text('\u20A9'+budget.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","));
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

$("body").on("click", '.budget p[data-editable="true"]', function(){  
    $this = $(this);
    $id = $this.closest("tr").data('id');
    $customer = $this.closest("tr").data('customerid');
    budgetTxt = $this.text();
    c_budget = budgetTxt.replace(/[^0-9,]/g, "");
    if (!c_budget) return;

    $('.budget p[data-editable="true"]').attr("data-editable", "false");
    $input = $('<input type="text" style="width:100%">');
    $input.val(c_budget);
    $(this).replaceWith($input);
    $input.focus();
    $input.on('keydown blur', function(e) {
        if (e.type === 'keydown') {
            $input.number(true);
            if (e.keyCode == 27) {
                // ESC Key
                $('.budget p').attr("data-editable", "true");
                restoreElement('\u20A9'+c_budget, $input);
            } else if (e.keyCode == 13) {
                new_budget = $input.val();
                data = {
                    'id': $id,
                    'customer': $customer,
                    'tab': $('.tab-link.active').val(),
                    'budget': new_budget
                };

                if (c_budget.replace(",", "") === new_budget) {
                    $('.budget p').attr("data-editable", "true");
                    restoreElement('\u20A9'+c_budget, $input);
                } else {
                    $.ajax({
                        type: "put",
                        url: "<?=base_url()?>/advertisements/set-budget",
                        data: data,
                        dataType: "json",
                        success: function(response){  
                            if(response == true) {
                                $('.budget p').attr("data-editable", "true");
                                var $new_p = $('<p data-editable="true">');
                                $new_p.text('\u20A9'+new_budget.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","));
                                $input.replaceWith($new_p);
                            }
                        },
                        error: function(error, status, msg){
                            alert("상태코드 " + status + "에러메시지" + msg );
                        }
                    });
                }
            }
        } else if (e.type === 'blur') {
            $input.number(true);
            new_budget = $input.val();
            data = {
                'id': $id,
                'customer': $customer,
                'tab': $('.tab-link.active').val(),
                'budget': new_budget
            };

            if (c_budget.replace(",", "") === new_budget) {
                $('.budget p').attr("data-editable", "true");
                restoreElement('\u20A9'+c_budget, $input);
            } else {
                $.ajax({
                    type: "put",
                    url: "<?=base_url()?>/advertisements/set-budget",
                    data: data,
                    dataType: "json",
                    success: function(response){  
                        if(response == true) {
                            $('.budget p').attr("data-editable", "true");
                            var $new_p = $('<p data-editable="true">');
                            $new_p.text('\u20A9'+new_budget.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","));
                            $input.replaceWith($new_p);
                        }
                    },
                    error: function(error, status, msg){
                        alert("상태코드 " + status + "에러메시지" + msg );
                    }
                });
            }
        }
    });
});

function debug(msg) {
    //console.log(msg);
}
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>