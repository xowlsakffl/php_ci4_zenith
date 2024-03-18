<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 통합
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/css/dataTables.css" rel="stylesheet">
<script src="/static/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore/js/dataTables.stateRestore.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedcolumns/js/dataTables.fixedColumns.min.js"></script>
<script src="/static/node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<!-- bootstrap5 -->
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-staterestore-bs5/js/stateRestore.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js"></script>
<script src="/static/node_modules/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js"></script>
<script src="/static/js/jquery.number.min.js"></script>
<script src="/static/js/jszip.min.js"></script>
<script src="/static/js/pdfmake/pdfmake.min.js"></script>
<script src="/static/js/pdfmake/vfs_fonts.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap adv-manager">
    <div class="title-area">
        <h2 class="page-title">통합 광고 관리</h2>
        <p class="title-disc">광고주별 매체의 기본적인 광고 합성/종료/수정의 기능을 제공하고 있으며, 추가적으로 CHAIN에서 개발한 스마트하게 광고를 최적화 시켜주는 기능도 함께 이용할 수 있습니다.</p>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <label><input type="text" name="sdate" id="sdate" readonly="readonly"><i class="bi bi-calendar2-week"></i></label>
                <span> ~ </span>
                <label><input type="text" name="edate" id="edate" readonly="readonly"><i class="bi bi-calendar2-week"></i></label>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
            </div>
        </form>
        <div class="row d-flex">
            <div class="reportData detail minWd">
            </div> 
        </div>
    </div>
    <div class="section reset-btn-wrap d-flex">
		<?php if(getenv('MY_SERVER_NAME') === 'resta'){?>
		<div class="col">
			<div class="inner"><button type="button" value="carelabs" id="carelabs_btn" class="carelabs_btn">케어랩스</button></div>
		</div>
		<?php }?>
        <div class="reset-btn-box">
            <button type="button" class="reset-btn">필터 초기화</button>
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

    <div class="section client-list media custom-margin-box-1">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row">
            <div class="col">
                <div class="inner"><button type="button" value="facebook" data-btn="media" class="media_btn">페이스북</button></div>
            </div>
            <div class="col">
                <div class="inner"><button type="button" value="kakao" data-btn="media" class="media_btn">카카오</button></div>
            </div>
            <div class="col">
                <div class="inner"><button type="button" value="google" data-btn="media" class="media_btn">구글</button></div>
            </div>
        </div>
    </div>

    <div class="section client-list media-advertiser">
        <div class="d-flex" id="media-advertiser">
            <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체별 광고주</h3>
            <button class="btn-primary" id="dbCountBtn" data-bs-toggle="modal" data-bs-target="#dbcount-modal">목표수량 설정</button>
        </div>
        <div class="row" id="media-advertiser-list">
        </div>
    </div>

    <div class="tab-wrap">
        <ul class="nav nav-tabs" id="tab-list" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="campaigns" type="button" id="campaign-tab"><span>캠페인</span><div class="selected"><span>0</span>개 선택</div></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="adsets" type="button" id="set-tab"><span>광고 세트</span><div class="selected"><span>0</span>개 선택</div></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-link" value="ads" type="button" id="advertisement-tab"><span>광고</span><div class="selected"><span>0</span>개 선택</div></button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="btn-wrap">
                <button type="button" class="btn btn-outline-danger" id="update_btn">수동 업데이트</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#data-modal">데이터 비교</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#memo-check-modal"><i class="bi bi-file-text"></i> 메모확인</button>
                <button type="button" class="btn btn-outline-danger checkAdvAutomationCreateBtn" data-bs-toggle="modal" data-bs-target="#automationModal">자동화 등록</button>
                <?php 
                if(auth()->user()->inGroup('developer')){
                    echo '<button type="button" class="btn btn-outline-danger" id="allAdvChangeLogBtn" data-bs-toggle="modal" data-bs-target="#advChangeLogModal">변경 내역</button>';
                }
                ?>
            </div>
            <!-- <div class="btns-memo-style">
                <span class="btns-title">메모 표시:</span>
                <button type="button" class="btns-memo" value="modal" title="새창으로 표시"><i class="bi bi-window-stack"></i></button>
                <button type="button" class="btns-memo" value="table" title="테이블에 표시"><i class="bi bi-table"></i></button>
            </div> -->
            <div class="tab-pane active">
                <div class="table-responsive">
                    <table class="dataTable table table-default" id="adv-table">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">매체</th>
                                <th scope="col">제목</th>
                                <th scope="col">상태</th>
                                <th scope="col">ID</th>
                                <th scope="col">예산</th>
                                <th scope="col">입찰가</th>
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
                            <tr id="total">
                                <td></td>
                                <td id="total-count"></td>
                                <td colspan="2"></td>
                                <td id="total-budget"></td>
                                <td id="total-bidamount"></td>
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
        <!-- 목표수량 설정 -->
        <div class="modal fade" id="dbcount-modal" tabindex="-1" aria-labelledby="dbcount-modal-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="dbcount-modal-label"><i class="bi bi-file-text"></i> 목표수량 설정</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="search-wrap log-search-wrap">
                            <form name="account-search-form" class="search">
                                <div class="input">
                                    <input type="text" name="account_stx" id="stx" placeholder="검색어를 입력하세요">
                                    <button class="btn-primary" id="search_btn" type="submit">조회</button>
                                </div>
                            </form>
                        </div>
                        <table class="table tbl-dark" id="dbCountTable" style="width: 100%;">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:75%;">
                                <col style="width:20%;">
                            </colgroup>
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">제목</th>
                                    <th scope="col">목표수량</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- 목표수량 설정 -->
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
        <!-- 자동화 모달 -->
        <?=$this->include('templates/inc/automation_create_modal.php')?>
        <!-- 자동화 로그 모달 -->
        <?=$this->include('templates/inc/automation_log_modal.php')?>
        <!-- 변경내역 로그 모달 -->
        <?=$this->include('templates/inc/change_log_modal.php')?>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script src="/static/js/automation/automation.js"></script>
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
let loadedData = false;
$('.tab-link[value="campaigns"]').addClass('active');
//if(typeof tableParam != 'undefined'){
    tableParam.searchData = {
        'type': $('.tab-link.active').val(),
        'media' : $('.media_btn.active').map(function(){return $(this).val();}).get().join('|'), 
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
    };
//}

getList();
function setTableParam() {
    tableParam.searchData = {
        'carelabs': $("#carelabs_btn").hasClass("active") ? 1 : 0,
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'type': $('.tab-link.active').val(),
        'media' : $('.media_btn.active').map(function(){return $(this).val();}).get().join('|'),
        'company' : $('.company_btn.active').map(function(){return $(this).val();}).get().join('|'),
        'account' : $('.media_account_btn.active').map(function(){return $(this).val();}).get().join('|'),
    };
}
function setSearchData() {
    tab = $('.tab-link.active').val();
    if(tab == 'adsets' || tab == 'ads'){
        $('#update_btn').hide();
    }else{
        $('#update_btn').show();
    }
    var data = tableParam;
    if(typeof data.searchData == 'undefined') return;
    $('.media_btn, .business_btn, .company_btn').removeClass('active');

    if(data.searchData.media){
        data.searchData.media.split('|').map(function(txt){ $(`.media_btn[value="${txt}"]`).addClass('active'); });
    }
    if(data.searchData.company){
        data.searchData.company.split('|').map(function(txt){ $(`.company_btn[value="${txt}"]`).addClass('active'); });
    }
    if(data.searchData.account){
        data.searchData.account.split('|').map(function(txt){ $(`.media_account_btn[value="${txt}"]`).addClass('active'); });
    }
	
	if(data.searchData.carelabs == 1){
		$("#carelabs_btn").addClass("active")
	}else{
		$("#carelabs_btn").removeClass("active")
	}
    $('.tab-link').removeClass('active');
    $('.tab-link[value="'+data.searchData.type+'"]').addClass('active');
    $('#stx').val(data.searchData.stx);
    debug('searchData 세팅')
    // if(typeof dataTable != 'undefined') dataTable.state.save();
}
/*
function setDrawData() {
    tab = $('.tab-link.active').val();
    if(typeof tableParam.searchData == 'undefined') return;
    if(tableParam.searchData.data){
        if(tab == 'campaigns' && tableParam.searchData.data.campaigns){
            tableParam.searchData.data.campaigns.map(function(txt){ 
                $(`#adv-table tbody tr[data-id="${txt}"]`).addClass('selected'); 
                if(!$(`#adv-table tbody tr`).is(`[data-id="${txt}"]`)) {
                    tableParam.searchData.data.campaigns = tableParam.searchData.data.campaigns.filter(function(e) { return e !== txt });
                    debug(`캠페인 ${txt} 삭제`);
                }
            });
            if(tableParam.searchData.data.campaigns.length)
                $('#campaign-tab .selected span').text(tableParam.searchData.data.campaigns.length).parent().fadeIn();
            else
                $('#campaign-tab .selected').fadeOut();
        }
        if(tab == 'adsets' && tableParam.searchData.data.adsets){
            tableParam.searchData.data.adsets.map(function(txt){ 
                $(`#adv-table tbody tr[data-id="${txt}"]`).addClass('selected'); 
                if(!$(`#adv-table tbody tr`).is(`[data-id="${txt}"]`)) {
                    tableParam.searchData.data.adsets = tableParam.searchData.data.adsets.filter(function(e) { return e !== txt });
                    debug(`광고그룹 ${txt} 삭제`);
                }
            });
            if(tableParam.searchData.data.adsets.length)
                $('#set-tab .selected span').text(tableParam.searchData.data.adsets.length).parent().fadeIn();
            else
                $('#set-tab .selected').fadeOut();
        }
        if(tab == 'ads' && tableParam.searchData.data.ads){
            tableParam.searchData.data.ads.map(function(txt){ 
                $(`#adv-table tbody tr[data-id="${txt}"]`).addClass('selected'); 
                if(!$(`#adv-table tbody tr`).is(`[data-id="${txt}"]`)) {
                    tableParam.searchData.data.ads = tableParam.searchData.data.ads.filter(function(e) { return e !== txt });
                    debug(`광고 ${txt} 삭제`);
                }
            });
            if(tableParam.searchData.data.ads.length)
                $('#advertisement-tab .selected span').text(tableParam.searchData.data.ads.length).parent().fadeIn();
            else
                $('#advertisement-tab .selected').fadeOut();
        }
    }
}
*/
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
    loadedData = false;
    dataTable = $('#adv-table').DataTable({
        "dom": '<Bfrip<t>>',
        "fixedHeader": {
            "header" : true,
            "footer" : true,
        },
        "fixedColumns": {
            "leftColumns": 3
        },
        "deferRender": true,
        "autoWidth": true,
        "order": [[1,'asc']],
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "search" : {
            "return": true
        },
        "ordering": true,
        "orderMulti": true,
        "orderCellsTop": true,
        "paging": false,
        "info": false,
        "scroller": false,
        "scrollX": true,
        // "scrollY": "50%",
        // "scrollCollapse": true,
        "stateSave": true,
        "deferRender": false,
        "buttons": [
            {
                'extend': 'collection',
                'text': "Menu",
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
            { targets: '_all', visible: true },
        ],
        "columns": [
            { 
                "data": "media", //매체
                "width": "40px",
                "render": function (data, type, row) {
                    media = '<div class="media_box"><i class="'+row.media+'"></i></div>';
                    return media;
                },
            },
            { 
                "data": "name", //제목
                "width": "250px",
                "render": function (data, type, row) {
                    if(tableParam.searchData.type == 'ads'){
                        name = '<div class="view_btn">'+(row.landingUrl ? '<a href="'+row.landingUrl+'" target="_blank">보기</a>' : '')+'</div><div class="name_wrap">'+(row.thumbnail ? '<div class="name_left_wrap"><img src="'+row.thumbnail+'"></div>' : '')+'<div class="name_right_wrap"><div class="codeBox d-flex align-items-center mb-2"><button class="codeBtn"><i class="bi bi-braces-asterisk"></i></button><span style="font-size:80%"><p data-editable="true" class="modify_tag">'+(row.code ?? '')+'</p></span></div><div class="mediaName"><p data-editable="true" class="modify_tag">'+row.name.replace(/(\@[0-9]+)/, '<span class="hl-red">$1</span>', row.name)+'</p></div></div></div>';
                    }else{
                        name = '<div class="mediaName"><p data-editable="true" class="modify_tag">'+row.name.replace(/(\@[0-9]+)/, '<span class="hl-red">$1</span>', row.name)+'</p></div>';
                    }

                    name += '<button class="btn_box_open"><span></span><span></span><span></span></button><ul class="btn_box"><li><button class="advAutomationCreateBtn" data-bs-toggle="modal" data-bs-target="#automationModal">자동화 등록</button></li><li><button class="advAutomationLogBtn"data-bs-toggle="modal" data-bs-target="#advLogModal">자동화 로그</button></li><li class="memo_btn_box"><button class="btn_memo text-dark position-relative" data-bs-toggle="modal" data-bs-target="#memo-write-modal">메모 작성</button></li><li><button id="advChangeLogBtn" data-bs-toggle="modal" data-bs-target="#advChangeLogModal">변경 내역</button></li></ul>';
                    return name;
                },
            },
            { 
                "data": "status", //상태
                "width": "40px",
                "render": function (data, type, row) {
                    status = '<select name="status" class="form-select form-select-sm" id="status_btn"><option value="OFF" '+(row.status === "OFF" ? 'selected' : '')+'>비활성</option><option value="ON" '+(row.status === "ON" ? 'selected' : '')+'>활성</option></select>';
                    return status;
                },
            },
            { 
                "data": "id", //ID
                "width": "150px"
            },
            { 
                "data": "budget", //예산
                "width": "80px",
                "render": function (data, type, row) {
                    budget = '<div class="budget">'+(row.budget == 0 ? (row.media == 'kakao' && (tableParam.searchData.type == 'campaigns' || tableParam.searchData.type == 'adsets') ? '<p data-editable="true" class="modify_tag">미설정</p>' : '-') : '<p data-editable="true" class="modify_tag">\u20A9'+row.budget+'</p>')+'</div>';
                    if(row.budget != 0)
                        budget += '<div class="btn-budget"><button class="btn-budget-up"><span class="material-symbols-outlined">arrow_circle_up</span></button><button class="btn-budget-down"><span class="material-symbols-outlined">arrow_circle_down</span></button></div>';
                    return budget;
                },
            },
            { 
                "data": "bidamount", //입찰가
                "width": "80px",
                "render": function (data, type, row) {
                    bidamount = '';
                    if(row.bidamount == 0){
                        if(row.biddingStrategyType != '타겟 CPA'){
                            bidamount = '<div class="bidamount">-</div>';
                        }else{
                            bidamount = '<div class="bidamount"><p data-editable="true" class="modify_tag">\u20A9'+row.campaign_bidamount+'</p></div>';
                        }
                    }else{
                        bidamount = '<div class="bidamount"><p data-editable="true" class="modify_tag">\u20A9'+row.bidamount+'</p></div>';
                    }
                    
                    if(row.bidamount_type){
                        bidamount+= '<div class="bidamount_type">'+row.bidamount_type+'</div>';
                    }
                    if(row.biddingStrategyType){
                        bidamount+= '<div class="bidamount_strategy">'+row.biddingStrategyType+'</div>';
                    }
                    if(row.campaign_bidamount && row.campaign_bidamount != 0){
                        bidamount+= '<div class="campaign_bidamount">캠페인 입찰가 사용</div>';
                    }

                    return bidamount;
                },
            },
            { 
                "data": "cpa", //현재 DB단가
                "width": "70px",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { 
                "data": "unique_total", //유효 DB
                "width": "40px"
            },
            {
                "data": "spend", //지출액
                "width": "100px",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { 
                "data": "margin", //수익
                "width": "100px",
                "render": function (data, type, row) {
                    if(parseInt(data) < 0){
                        margin = '\u20A9'+data; 
                        return '<span style="color:red">'+margin+'</span>';
                    }else{
                        margin = '\u20A9'+data; 
                    }
                    return margin;
                },
            },
            { 
                "data": "margin_ratio", //수익률
                "width": "50px",
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
                "data": "sales", //매출액
                "width": "100px",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            },
            { 
                "data": "impressions", //노출수
                "width": "80px"
            },
            { 
                "data": "click", //링크클릭
                "width": "50px"
            },
            { 
                "data": "cpc", 
                "width": "50px",
                "render": function (data, type, row) {
                    return '\u20A9'+data;
                },
            }, //클릭당단가 (1회 클릭당 비용)
            { 
                "data": "ctr", 
                "width": "50px",
                "render": function (data, type, row) {
                    return data+'\u0025';
                },
            }, //클릭율 (노출 대비 클릭한 비율)
            { 
                "data": "cvr",  //DB전환률
                "width": "50px",
                "render": function (data, type, row) {
                    return data+'\u0025';
                },
            }, //전환율
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.id);
            $(row).attr("data-customerId", data.customerId ? data.customerId : '');

            if(data.class != ''){
                $(row).addClass(data.class)
            }
        },
        "language": {
            "url": '/static/js/dataTables.i18n.json' //CDN 에서 한글화 수신
        },
        "stateSaveParams": function (settings, data) { //LocalStorage 저장 시
            debug('state 저장');
            data.memoView = $('.btns-memo.active').val();
            data.searchData = tableParam.searchData;
        },
        "stateLoadParams": function (settings, data) { //LocalStorage 호출 시
            debug('state 로드');
            tableParam = data;
        },
    }).on('preXhr.dt', function (e, settings, data) {
        if(!$('.reportData').find('.zenith-loading').is(':visible')) $('.reportData').prepend('<div class="zenith-loading"/>')
        $('.client-list:not(.media) .row').filter(function(i, el) {
            if(!$(el).find('.zenith-loading').is(':visible')) $(el).prepend('<div class="zenith-loading"/>')
        });
    }).on('xhr.dt', function( e, settings, data, xhr ) {
        if(data){
            debug('ajax loaded');
            setDate();
            setTotal(data);
            /*
            setReport(data.report);
            setAccount(data.accounts);
            setMediaAccount(data.media_accounts);
            setDate();
            setSearchData();
            */
        }
    }).on('draw', function() {
        debug('draw');
        getData('report');
        getData('accounts');
        getData('mediaAccounts');
        /*
        loadedData = true;
        setDrawData();
        */
    })
}

function setTotal(res){
    
    if(!res.total){
        return false;
    }else{
        let margin = res.total.margin.replace(/,/g, '');
        margin = Number(margin);

        if(margin < 0){
            $('#total-margin').css('color', 'red');
        }

        if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
            $('#avg_margin_ratio').css('color', 'red');
        }

        $('#total-count').text(res.data.length+"건 결과");
        $('#total-budget').text('\u20A9'+res.total.budget);//예산
        $(' #total-bidamount').text('\u20A9'+res.total.bidamount);//입찰가
        $('#avg-cpa').text('\u20A9'+res.total.avg_cpa);//현재 DB 단가
        //$('#total-unique_total').html('<div>'+res.total.unique_total+'</div><div style="color:blue">'+res.total.expect_db+'</div>');
        $('#total-unique_total').text(res.total.unique_total);
        $('#total-spend').text('\u20A9'+res.total.spend);
        $('#total-margin').text('\u20A9'+res.total.margin);
        $('#avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
        $('#total-sales').text('\u20A9'+res.total.sales);
        $('#total-impressions').text(res.total.impressions);
        $('#total-click').text(res.total.click);
        $('#avg-cpc').text('\u20A9'+res.total.avg_cpc);
        $('#avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100 +'\u0025');
        $('#avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');
    };
}
function getData(type) {
    $.ajax({
        type: "GET",
        url: `<?=base_url()?>advertisements/${type}`,
        data: {
            "searchData":tableParam.searchData,
        },
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            eval(`set${type.charAt(0).toUpperCase()+type.slice(1)}`)(data); 
            // setReport(data.report);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function setReport(data){
    $('.reportData').empty().find('.zenith-loading').fadeOut(function(){$(this).remove();});
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
    setSearchData();
}

function getCheckData(check){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/check-data",
        data: {
            "searchData":tableParam.searchData,
            "check": check
        },
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setReport(data.report);
            // setAccount(data.account);
            // setMediaAccount(data.media_accounts);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}
function setAccounts(data) {
    let $row = $('.advertiser .row');
    $('.advertiser').find('.zenith-loading').fadeOut(function(){$(this).remove();});
    let existingIds = [];
    $row.find('.filter_btn').each(function() {
        existingIds.push($(this).val());
    });

    let html = '';
    $.each(data, function(idx, v) {
        let companyId = v.company_id.toString();

        if (existingIds.includes(companyId)) {
            existingIds = existingIds.filter(id => id !== companyId);
        } else {
            html += '<div class="col"><div class="inner"><button type="button" value="' + companyId + '" data-btn="company" class="company_btn filter_btn"><span class="account_name">' + v.company_name + '</span></button></div></div>';
        }
    });

    $row.find('.filter_btn').filter(function() {
        return existingIds.includes($(this).val());
    }).parent().parent().remove();

    $row.append(html);

    //정렬
    let buttons = $row.find('.filter_btn');
    buttons.sort(function(a, b) {
        let textA = $(a).find('.account_name').text().toUpperCase();
        let textB = $(b).find('.account_name').text().toUpperCase();
        return textA.localeCompare(textB, 'en', { sensitivity: 'base' });
    });

    $.each(buttons, function(_, button) {
        $row.append(button.parentNode.parentNode);
    });
    setSearchData();
}

function setMediaAccounts(data) {
    let $row = $('.media-advertiser .row');
    $('.media-advertiser').find('.zenith-loading').fadeOut(function(){$(this).remove();});
    let mediaExistingIds = [];
    $row.find('.filter_media_btn').each(function() {
        mediaExistingIds.push($(this).val());
    });

    let html = '';
    $.each(data, function(idx, v) {
        let media_account_id = v.media_account_id.toString();

        if (mediaExistingIds.includes(media_account_id)) {
            mediaExistingIds = mediaExistingIds.filter(id => id !== media_account_id);
        } else {
            html += `
            <div class="col">
            <div class="inner ${v.tag_inactive ? v.tag_inactive : ''} ${v.approved_limited ? v.approved_limited : ''} ${v.disapproval ? v.disapproval : ''}">
                <button type="button" value="${media_account_id}" data-btn="media-account" class="filter_media_btn media_account_btn">
                <div class="media_account_txt d-flex align-items-center">
                    <span class="media_account_icon ${v.media}"></span>
                    <span class="media_account_name">${v.media_account_name}</span>
                </div>
                </button>
                ${v.db_count ? 
                `<div class="db_ratio">
                    <p class="${v.over ? v.over : ''}">
                    ${v.db_sum}/${v.db_count}
                    </p>
                    <div class="bar" style="width:${v.db_ratio}%"></div>
                </div>` : '' 
                }
            </div>
            </div>`;
        }
    });

    $row.find('.filter_media_btn').filter(function() {
        return mediaExistingIds.includes($(this).val());
    }).parent().parent().remove();

    $row.append(html);

    //정렬
    let buttons = $row.find('.filter_media_btn');
    buttons.sort(function(a, b) {
        let textA = $(a).find('.media_account_name').text().toUpperCase();
        let textB = $(b).find('.media_account_name').text().toUpperCase();
        
        return textA.localeCompare(textB, 'en', { sensitivity: 'base' });
    });

    $.each(buttons, function(_, button) {
        $row.append(button.parentNode.parentNode);
    });
    setSearchData();
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
        let checkOutPicker = $checkoutInput.data('daterangepicker');
        checkOutPicker.setStartDate(startDate);
        checkOutPicker.setEndDate(endDate);

        // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
        let checkInPicker = $checkinInput.data('daterangepicker');
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
                let $new_p = $('<p data-editable="true" class="modify_tag">');
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
    let $old_p = $('<p data-editable="true" class="modify_tag">');
    $old_p.text(text);
    inputElement.replaceWith($old_p);
}

function handleInput(tab, id, tmp_name, inputElement) {
    let new_name = inputElement.val();
    let data = {
        'old_name' : tmp_name,
        'name': new_name,
        'tab': tab,
        'id': id,
    };
    let customerId = inputElement.closest("tr").data("customerid");
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
        type: "put",
        url: "<?=base_url()?>/advertisements/set-adv",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            if (data == true) {
                alert('수동 업데이트 완료');
            }else{
                alert('수동 업데이트 실패');
            }
        },
        complete: function () {
			$('.fa-spinner').remove();
		},
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

function getOnlyAdAccount(){
    dbCountTable = $('#dbCountTable').DataTable({
        "destroy": true,
        "autoWidth": false,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "order": [[0,'desc']],
        "deferRender": false,
        'lengthChange': false,
        "scrollY": "70vh",
        "scrollCollapse": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/adaccounts",
            "data": function(d) {
                d.stx = $('input[name=account_stx]').val();
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res;
            }
        },
        "columns": [
            { 
                "data": "null",
                "render": function(data, type, row){
                    let check = '';
                    if(row.media == 'google'){
                        check = `<input type="checkbox" name="expose_check" class="expose_check" ${row.is_exposed == 1 ? 'checked' : ''}>`;
                    }

                    return check;
                },
                "width": "5%"
            },
            { 
                "data": "media_account_name",
                "render": function(data, type, row){
                    let name = '<div class="media_box d-flex"><i class="'+row.media+'"></i><p'+(row.canManageClients == "1" ? 'class="canmanage"' : '')+'>'+data+'</p></div>';
                    return name;
                },
                "width": "75%"
            },
            { 
                "data": "db_count",
                "render": function(data, type, row){
                    let db_count = `<input type="text" class="form-control db-count-input" value="${data = data === null ? '' : data}">`;
                    return db_count;
                },
                "width": "20%"
            }
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.media_account_id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    })
}

$('form[name="account-search-form"]').bind('submit', function() {
    dbCountTable.draw();
    return false;
});

$('#dbCountTable').on('blur', '.db-count-input', function() {
    let id = $(this).closest("tr").data("id");
    let db_count = $(this).val();
    $.ajax({
        type: 'PUT',
        url: "<?=base_url()?>/advertisements/set-dbcount",
        data: {
            "id": id,
            "db_count": db_count
        },
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                alert('변경되었습니다.');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

$('#dbCountTable').on('change', '.expose_check', function() {
    let id = $(this).closest("tr").data("id");
    let is_exposed = $(this).is(":checked") ? 1 : 0; 
    $.ajax({
        type: 'PUT',
        url: "<?=base_url()?>/advertisements/set-exposed",
        data: {
            "id": id,
            "is_exposed": is_exposed
        },
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            if(data == true){
                alert('변경되었습니다.');
            }
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
});

$('#dbcount-modal').on('show.bs.modal', function(e) { 
    getOnlyAdAccount();
})
.on('hidden.bs.modal', function(e) { 
    $('input[name=account_stx]').val('');
    dbCountTable.destroy();
});

$('.btns-memo-style button').bind('click', function() { //메모 표시타입
    $('.btns-memo-style button').removeClass('active');
    $(this).addClass('active');
    debug('메모표시방식 설정');
});

$('body').on('click', '.media_btn, .company_btn, .media_account_btn', function() {
    $(this).toggleClass('active');
    debug('필터링 탭 클릭');
    setTableParam();
    dataTable.draw();
});
<?php if(getenv('MY_SERVER_NAME') === 'resta'){?>
$('body').on('click', '#carelabs_btn', function() {
    $(this).toggleClass('active');
	if (!$(this).hasClass('active')) {
		$('.advertiser .row').empty();
		$('.media-advertiser .row').empty();
        $('.reset-btn').trigger('click');
    }
    debug('필터링 탭 클릭');
    dataTable.state.save();
    dataTable.draw();
});
<?php }?>
$('body').on('click', '.tab-link', function() {
    $('.tab-link').removeClass('active');
    $(this).addClass('active');
    debug('tab-link 클릭');
    dataTable.state.save();
    dataTable.draw();
});

/*체크 항목 수동 업데이트*/
$('body').on('click', '#update_btn', function() {
    let selected = $('.dataTable tbody tr.selected').map(function(){return $(this).data('id');}).get();
    /* checkedInputs.each(function() {
        var icon = $('<i>').addClass('fa fa-spinner fa-spin'); 
        $(this).parent('label.check').before(icon);
    }); */
    let data = {
        'check' : selected,
    }

    if(!data.check.length){
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
//Row Selected
$('.dataTable').on('click', 'tbody tr td:first-child', function(e) {
    $(this.parentNode).toggleClass('selected');
    let selected = $('.dataTable tbody tr.selected').map(function(){return $(this).data('id');}).get();
    if(typeof tableParam.searchData.data == 'undefined') tableParam.searchData.data = {};
    tableParam.searchData.data[$('.tab-link.active').val()] = selected;
    $('.tab-link.active .selected span').text(tableParam.searchData.data[$('.tab-link.active').val()].length).parent().fadeIn();
    if(tableParam.searchData.data[$('.tab-link.active').val()].length < 1)
    $('.tab-link.active .selected').fadeOut();
})

var prevVal;
$('.dataTable').on('focus', 'select[name="status"]', function(){
    prevVal = $(this).val();
}).on('change', 'select[name="status"]', function() {
    data = {
        'old_status' : prevVal,
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
})

$(".dataTable").on("click", '.mediaName p[data-editable="true"]', function(){
    tab = $('.tab-link.active').val();
    id = $(this).closest("tr").data("id");
    if((tab == 'ads' && id.includes('google')) || (tab == 'adsets' && id.includes('kakao'))){
        return false;
    }else{
        $('.mediaName p[data-editable="true"]').attr("data-editable", "false");
        let tmp_name = $(this).text();
        let $input = $('<input type="text" style="width:100%">');
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
    let html =  '';
    $.each(data, function(i,row) {
        let name = '';
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
    let clickedButton = $(document.activeElement).closest('tr').data('id');
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
    let formData = $(this).serialize();
    registerMemo(formData);
    return false;
});

$(".dataTable").on("click", '.memo-list input[name="is_done"]', function(){
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

function getChangeLogList(id = null) {
    $.ajax({
        type: "get",
        url: "<?=base_url()?>advertisements/change-log",
        dataType: "json",
        data: {'id': id},
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setChangeLogList(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setChangeLogList(data) {
    let html =  '';
    $.each(data, function(i,row) {
        let media = '';
        switch (row.media) {
            case 'facebook':
                media = '페이스북';
                break;
            case 'kakao':
                media = '카카오';
                break;
            case 'google':
                media = '구글';
                break;
            default:
                break;
        }

        switch (row.change_type) {
            case 'status':
                change_type = '상태';
                break;
            case 'name':
                change_type = '제목';
                break;
            case 'budget':
                change_type = '예산';
                break;
            case 'bidamount':
                change_type = '입찰가';
                break;
            default:
                break;
        }
        html += '    <li class="d-flex justify-content-between align-items-start" data-id="'+row.seq+'">';
        html += '        <div class="detail d-flex align-items-start">';
        html += '            <p class="ms-1">['+ media +'] ['+ change_type +'] ['+ row.id +'] '+ (row.old_value != '' ? row.old_value + '-> ' : '') + row.change_value+'</p>';
        html += '        </div>';
        html += '        <div class="d-flex">';
        html += '            <p style="width:50px;margin-right:15px">'+ row.nickname +'</p>';
        html += '            <p style="min-width:150px">'+ row.datetime +'</p>';
        html += '        </div>';
        html += '    </li>';
    });
    
    $('ul.change-list').prepend(html);
}

$('#advChangeLogModal').on('show.bs.modal', function(e) { 
    let $btn = $(e.relatedTarget);
    if ($btn.attr('id') === 'allAdvChangeLogBtn') {
        getChangeLogList();
    }else{
        let id = $(e.relatedTarget).closest("tr").data("id");
        getChangeLogList(id);
    }
})
.on('hidden.bs.modal', function(e) { 
    $('ul.change-list').empty();
});


$(".dataTable").on("click", '.btn-budget button', function(){
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
    let budget = Math.round(c_budget * rate);
    
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
        'budget': budget,
        'old_budget' : c_budget
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

$(".dataTable").on("click", '.budget p[data-editable="true"]', function(){  
    let $this = $(this);
    let $id = $this.closest("tr").data('id');
    let media = $id.split("_")[0];
    let tab = $('.tab-link.active').val();
    let $customer = $this.closest("tr").data('customerid');
    let budgetTxt = $this.text();
    let c_budget = budgetTxt.replace(/[^0-9,]/g, "");
    if (!c_budget && (media == 'kakao' && tab == 'ads')) return;

    $('.budget p[data-editable="true"]').attr("data-editable", "false");
    let $input = $('<input type="text" style="width:100%">');
    $input.val(c_budget);
    $(this).replaceWith($input);
    $input.focus();
    $input.on('keydown blur', function(e) {
        if (e.type === 'keydown') {
            $input.number(true);
            if (e.keyCode == 27) {
                // ESC Key
                if(media == 'kakao' && budgetTxt == '미설정'){
                    c_budget = '미설정';
                }else{
                    c_budget = '\u20A9'+c_budget;
                }
                $('.budget p').attr("data-editable", "true");
                restoreElement(c_budget, $input);
            } else if (e.keyCode == 13) {
                new_budget = $input.val();
                data = {
                    'id': $id,
                    'customer': $customer,
                    'tab': tab,
                    'budget': new_budget,
                    'old_budget' : c_budget
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
                                let $new_p = $('<p data-editable="true" class="modify_tag">');
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
                'tab': tab,
                'budget': new_budget,
                'old_budget' : c_budget
            };

            if (c_budget.replace(",", "") === new_budget) {
                if(media == 'kakao' && budgetTxt == '미설정'){
                    c_budget = '미설정';
                }else{
                    c_budget = '\u20A9'+c_budget;
                }
                $('.budget p').attr("data-editable", "true");
                restoreElement(c_budget, $input);
            } else {
                $.ajax({
                    type: "put",
                    url: "<?=base_url()?>/advertisements/set-budget",
                    data: data,
                    dataType: "json",
                    success: function(response){  
                        if(response == true) {
                            $('.budget p').attr("data-editable", "true");
                            let $new_p = $('<p data-editable="true" class="modify_tag">');
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

$(".dataTable").on("click", '.bidamount p[data-editable="true"]', function(){  
    let $this = $(this);
    let $id = $this.closest("tr").data('id');
    let $customer = $this.closest("tr").data('customerid');
    let $amount_type = $this.closest("tr").find('.bidamount_type').text();
    //console.log($amount_type);
    let budgetTxt = $this.text();
    let c_bidamount = budgetTxt.replace(/[^0-9,]/g, "");
    if (!c_bidamount) return;

    $('.bidamount p[data-editable="true"]').attr("data-editable", "false");
    let $input = $('<input type="text" style="width:100%">');
    $input.val(c_bidamount);
    $(this).replaceWith($input);
    $input.focus();
    $input.on('keydown blur', function(e) {
        if (e.type === 'keydown') {
            $input.number(true);
            if (e.keyCode == 27) {
                // ESC Key
                $('.bidamount p').attr("data-editable", "true");
                restoreElement('\u20A9'+c_bidamount, $input);
            } else if (e.keyCode == 13) {
                new_bidamount = $input.val();
                data = {
                    'id': $id,
                    'customer': $customer,
                    'tab': $('.tab-link.active').val(),
                    'bidamount': new_bidamount,
                    'bidamount_type': $amount_type ? $amount_type : '',
                    'old_bidamount': c_bidamount
                };

                if (c_bidamount.replace(",", "") === new_bidamount) {
                    $('.bidamount p').attr("data-editable", "true");
                    restoreElement('\u20A9'+c_bidamount, $input);
                } else {
                    $.ajax({
                        type: "put",
                        url: "<?=base_url()?>/advertisements/set-bidamount",
                        data: data,
                        dataType: "json",
                        success: function(response){  
                            if(response == true) {
                                $('.bidamount p').attr("data-editable", "true");
                                let $new_p = $('<p data-editable="true" class="modify_tag">');
                                $new_p.text('\u20A9'+new_bidamount.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","));
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
            new_bidamount = $input.val();
            data = {
                'id': $id,
                'customer': $customer,
                'tab': $('.tab-link.active').val(),
                'bidamount': new_bidamount,
                'bidamount_type': $amount_type ? $amount_type : '',
                'old_bidamount': c_bidamount
            };

            if (c_bidamount.replace(",", "") === new_bidamount) {
                $('.bidamount p').attr("data-editable", "true");
                restoreElement('\u20A9'+c_bidamount, $input);
            } else {
                $.ajax({
                    type: "put",
                    url: "<?=base_url()?>/advertisements/set-bidamount",
                    data: data,
                    dataType: "json",
                    success: function(response){  
                        if(response == true) {
                            $('.bidamount p').attr("data-editable", "true");
                            let $new_p = $('<p data-editable="true" class="modify_tag">');
                            $new_p.text('\u20A9'+new_bidamount.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","));
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

function sendCode(data, inputElement) {
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/advertisements/set-code",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data) {
            //console.log(data);
            if (data.response == true) {
                let $new_p = $('<p data-editable="true" class="modify_tag">');
                $new_p.text(data.code);
                inputElement.replaceWith($new_p);
            }
        },
        error: function(error, status, msg) {
            alert("상태코드 " + status + "에러메시지" + msg);
        }
    });
}

$(".dataTable").on("click", '.codeBtn', function(){
    tab = $('.tab-link.active').val();
    id = $(this).closest("tr").data("id");
    $('.codeBox p[data-editable="true"]').attr("data-editable", "false");
    let code = $(this).siblings('span').find('p').text();
    let $input = $('<input type="text" style="width:100%;">');
    $input.val(code);
    $(this).siblings('span').find('p').replaceWith($input);
    $input.focus();
    
    $input.on('keydown blur', function(e) {
        if (e.type === 'keydown') {
            if (e.keyCode == 27) {
                // ESC Key
                restoreElement(code, $input);
            } else if (e.keyCode == 13) {
                let new_code = $input.val();
                let data = {
                    'code': new_code,
                    'tab': tab,
                    'id': id,
                };

                if (code === new_code) {
                    restoreElement(code, $input);
                } else {
                    sendCode(data, $input)
                }
            }
        } else if (e.type === 'blur') {
            let new_code = $input.val();
            let data = {
                'code': new_code,
                'tab': tab,
                'id': id,
            };

            if (code === new_code) {
                restoreElement(code, $input);
            } else {
                sendCode(data, $input)
            }
        }
    });

    $('.codeBox p[data-editable="false"]').attr("data-editable", "true");
});

$('body').on('click', '.reset-btn', function() {
    $('#sdate, #edate').val(today);
    $('.media_btn, #business_btn, .company_btn, .tab-link, .media_account_btn, #carelabs_btn').removeClass('active');
    $('.tab-link[value="campaigns"]').addClass('active');
    $('#total td').empty();
    $('#stx').val('');
    dataTable.state.clear();
    dataTable.state.save();
    dataTable.order([2, 'asc']).draw();
});

function getAdvLog(id){
    advLogTable = $('#advLogTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "order": [[3,'desc']],
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/automation/log",
            "data": function(d) {
                d.id = id;
                d.aa_seq = selectedaaId ?? [];
            },
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columns": [
            { "data": "subject"},
            { "data": "nickname"},
            { 
                "data": "result",
                "render": function(data, type, row){
                    let result;
                    if(data == '실행됨'){
                        result = '<b class="em">'+data+'</b>';
                    }else if(data == '실패'){
                        result = '<b class="fail">'+data+'</b>';
                    }else{
                        result = data;
                    }

                    return result;
                }
            },
            { "data": "exec_timestamp"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.id);
            let detailRow = '<h2>작업 세부 정보</h2>'+
            '<div class="detail-log p-1">'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">일정</dt>'+
                    '<dd>'+(data.schedule_desc ? data.schedule_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">대상</dt>'+
                    '<dd>'+(data.target_desc ? data.target_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item mb-3">'+
                    '<dt class="mb-1">조건</dt>'+
                    '<dd>'+(data.conditions_desc ? data.conditions_desc : "")+'</dd>'+
                '</dl>'+
                '<dl class="log-item">'+
                    '<dt class="mb-1">실행</dt>'+
                    '<dd>'+(data.executions_desc != null ? (Array.isArray(data.executions_desc) ? data.executions_desc.join('<br>') : data.executions_desc) : "")+'</dd>'+
                '</dl>'+
            '</div>';
            advLogTable.row(row).child(detailRow).hide();
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    }).on('xhr.dt', function( e, settings, data, xhr ) {
        if(data && $('#automation-list').is(':empty')){
            setAutomationBtn(data.automation);
        }
    });
}

function setAutomationBtn(data) {
    let $row = $('#automation-list');

    let html = '';
    $.each(data, function(idx, v) {
        html += '<div class="col"><div class="inner"><button type="button" value="' + v.aa_seq + '" id="automation_btn" class="filter_btn"><span class="automation_name">' + v.aa_subject + '</span></button></div></div>';
    });

    $row.append(html);
}

$('#advLogModal').on('show.bs.modal', function(e) {  
    let title = $(e.relatedTarget).parent().siblings(".mediaName").find("p").text();
    let id = $(e.relatedTarget).closest("tr").data("id");
    $('#advLogModal .modal-title span').text(" - "+title);
    getAdvLog(id);
})
.on('hidden.bs.modal', function(e) { 
    $('#advLogModal .modal-title span').text('');
    $('#automation-list').empty();
    advLogTable.destroy();
});

$('body').on('click', '#advLogModal tbody tr', function(){
    let tr = $(this).closest('tr');
    let row = advLogTable.row(tr);

    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        row.child.hide();
        tr.removeClass('shown');
    }else {
        $(this).addClass('selected');
        row.child.show();
        tr.addClass('shown');
    }
});
var selectedaaId = [];
$('body').on('click', '#automation_btn', function() {
    $(this).toggleClass('active');
    let val = $(this).val();

    let index = selectedaaId.indexOf(val);
    if (index > -1) {
        selectedaaId.splice(index, 1); // 삭제
    } else {
        selectedaaId.push(val); // 추가
    }
    advLogTable.draw();
});

$('body').on('click', '.btn_box_open', function() {
    let box = $(this).siblings('.btn_box');
    $('.btn_box').not(box).hide();
    box.fadeToggle();
});

function debug(msg) {
    console.log(msg);
}
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>