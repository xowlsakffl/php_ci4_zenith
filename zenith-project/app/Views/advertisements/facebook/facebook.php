<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 페이스북
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">페이스북 광고관리</h2>
        <p class="title-disc">숫자 중심의 퍼포먼스 마케팅에 집중! 사람들이 어떤 콘텐츠에 반응하는가? 비용 대비 얼만큼의 성과가 있는가?</p>
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
        <div class="detail row d-flex justify-content-center">
            <dl class="col">
                <dt>노출수</dt>
                <dd id="impressions_sum"></dd>
            </dl>
            <dl class="col">
                <dt>클릭수</dt>
                <dd id="clicks_sum"></dd>
            </dl>
            <dl class="col">
                <dt>클릭율</dt>
                <dd id="click_ratio_sum"></dd>
            </dl>
            <dl class="col">
                <dt>지출액</dt>
                <dd id="spend_sum"></dd>
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

    <div class="client-list biz">
        <h3><i class="bi bi-chevron-down"></i> 비즈니스 계정</h3>
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
    </div>
    <div class="client-list advertiser">
        <h3><i class="bi bi-chevron-down"></i> 광고주</h3>
        <div class="row">
            <!-- <div class="col">
                <div class="inner">
                    <button type="button" class="active alert">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div> -->
            
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
                    <table class="table table-striped table-hover" id="campaigns-table">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:5.5%">
                            <col style="width:5%">
                            <col style="width:5%">
                            <col style="width:7%">
                            <col style="width:6%">
                            <col style="width:4%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:6.5%">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">캠페인명</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산 <br>ai</th>
                                <th scope="col">목표 <br>ai</th>
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
                        <thead>
                            <tr id="total">
                                <td id="total-count"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td id="total-budget"></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-inline_link_clicks"></td>
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
                    <table class="table table-striped table-hover" id="adsets-table">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:6%">
                            <col style="width:4%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:6.5%">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">광고세트명</th>
                                <th scope="col">상태</th>
                                <th scope="col">예산 <br>ai</th>
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
                        <thead>
                            <tr id="total">
                                <td id="total-count"></td>
                                <td></td>
                                <td></td>
                                <td id="total-budget"></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-inline_link_clicks"></td>
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

            <div class="tab-pane" role="tabpanel" id="advertisement-tab-pane" aria-labelledby="advertisement-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="ads-table">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:5.5%">
                            <col style="width:6%">
                            <col style="width:4%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:6.5%">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">광고명</th>
                                <th scope="col">상태</th>
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
                        <thead>
                            <tr id="total">
                                <td id="total-count"></td>
                                <td></td>
                                <td id="avg-cpa"></td>
                                <td id="total-unique_total"></td>
                                <td id="total-spend"></td>
                                <td id="total-margin"></td>
                                <td id="avg_margin_ratio"></td>
                                <td id="total-sales"></td>
                                <td id="total-impressions"></td>
                                <td id="total-inline_link_clicks"></td>
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
var args = {
    'sdate': $('#sdate').val(),
    'edate': $('#edate').val(),
};
args.type = 'campaigns';

setDate();
getChartReport(args);
getAccount(args);
getCampaigns(args);

function getChartReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/facebook/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#click_ratio_sum').text(data.click_ratio_sum);
            $('#spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#unique_total_sum').text(data.unique_total_sum);
            $('#unique_one_price_sum').text(data.unique_one_price_sum.toLocaleString('ko-KR'));
            $('#conversion_ratio_sum').text(data.conversion_ratio_sum);
            $('#per_sum').text(data.per_sum);
            $('#price_01_sum').text(data.price_sum.toLocaleString('ko-KR'));
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function getAccount(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/facebook/accounts",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('.advertiser .row').empty();
            var html = '';
            var set_ratio = '';
            $.each(data, function(idx, v) {               
                if(v.db_count){
                    set_ratio = '<div class="progress"><div class="progress-bar" role="progressbar" style="width:'+v.db_ratio+'%"></div><div class="txt">'+v.db_sum+'/'+v.db_count+'</div></div>';
                }

                html += '<div class="col"><div class="inner"><button type="button" value="'+v.ad_account_id+'" id="account_btn" class="filter_btn">'+v.name+set_ratio+'</button></div></div>';
            });

            $('.advertiser .row').html(html);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function getCampaigns(args) {
	$('#campaigns-table').DataTable({
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "bDestroy": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/facebook/data",
            "data": args,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                if(res.total.margin < 0){
                    $('#campaigns-table #total-margin').css('color', 'red');
                }
                
                if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
                    $('#campaigns-table #avg_margin_ratio').css('color', 'red');
                }

                $('#campaigns-table #total-count').text("캠페인 "+res.campaigns.length+"건 결과");
                $('#campaigns-table #total-budget').text('\u20A9'+res.total.budget.toLocaleString('ko-KR'));
                $('#campaigns-table #avg-cpa').text(Math.round(res.total.avg_cpa).toLocaleString('ko-KR'));
                $('#campaigns-table #total-unique_total').text(res.total.unique_total);
                $('#campaigns-table #total-spend').text('\u20A9'+res.total.spend.toLocaleString('ko-KR'));
                $('#campaigns-table #total-margin').text('\u20A9'+res.total.margin.toLocaleString('ko-KR'));
                $('#campaigns-table #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
                $('#campaigns-table #total-sales').text('\u20A9'+res.total.sales.toLocaleString('ko-KR'));
                $('#campaigns-table #total-impressions').text(res.total.impressions.toLocaleString('ko-KR'));
                $('#campaigns-table #total-inline_link_clicks').text(res.total.inline_link_clicks.toLocaleString('ko-KR'));
                $('#campaigns-table #avg-cpc').text('\u20A9'+Math.round(res.total.avg_cpc).toLocaleString('ko-KR'));
                $('#campaigns-table #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
                $('#campaigns-table #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');

                return res.campaigns;
            }
        },
        "columns": [
            { "data": "name" },
            { "data": "status" },
            { "data": null, "defaultContent": ""},
            { "data": "ai2_status" },
            { 
                "data": "budget", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        budget = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        budget = "";
                    }
                    return budget;
                }
            }, //예산
            { 
                "data": "cpa",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpa = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpa = "";
                    }
                    return cpa;
                }
            }, //현재 DB단가
            { "data": "unique_total" }, //유효DB수
            { 
                "data": "spend",
                "render": function (data, type, row) {
                    if (data !== null) {
                        spend = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        spend = "";
                    }
                    return spend;
                }
            }, //지출액
            { 
                "data": "margin",
                "render": function (data, type, row) {
                    if (data !== null) {
                        if(data < 0){
                            margin = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                            return '<span style="color:red">'+margin+'</span>';
                        }else{
                            margin = '\u20A9'+parseInt(data).toLocaleString('ko-KR'); 
                        }
                    }else{
                        margin = "";
                    }
                    return margin;
                }
            }, //수익
            { 
                "data": "margin_ratio",
                "render": function (data, type, row) {
                    if (data !== null) {
                        if(data < 20 && data != 0){
                            margin_ratio = parseInt(data).toLocaleString('ko-KR')+'\u0025';   
                            return '<span style="color:red">'+margin+'</span>';
                        }else{
                            margin_ratio = parseInt(data).toLocaleString('ko-KR')+'\u0025';  
                        }
                    }else{
                        margin_ratio = "";
                    }
                    return margin_ratio;
                }
            }, //수익률
            { 
                "data": "sales",
                "render": function (data, type, row) {
                    if (data !== null) {
                        sales = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        sales = "";
                    }
                    return sales;
                }
            }, //매출액
            { 
                "data": "impressions", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impressions = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impressions = "";
                    }
                    return impressions;
                }
            }, //노출수
            { 
                "data": "inline_link_clicks", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        inline_link_clicks = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        inline_link_clicks = "";
                    }
                    return inline_link_clicks;
                }
            }, //링크클릭
            { 
                "data": "cpc", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpc = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpc = "";
                    }
                    return cpc;
                }
            }, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", }, //클릭율 (노출 대비 클릭한 비율)
            { 
                "data": "cvr", 
                "render": function (data, type, row) {
                    return data+'\u0025';
                }
            }, //전환율
        ],
        "language": {
            "emptyTable": "데이터가 존재하지 않습니다.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "infoEmpty": "데이터 없음",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "에서 검색: ",
            "zeroRecords": "일치하는 데이터가 없어요.",
            "loadingRecords": "로딩중...",
        },
    });
}

function getAdsets(args) {
	$('#adsets-table').DataTable({
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "bDestroy": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/facebook/data",
            "data": args,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                if(res.total.margin < 0){
                    $('#adsets-table #total-margin').css('color', 'red');
                }
                
                if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
                    $('#adsets-table #avg_margin_ratio').css('color', 'red');
                }
                
                $('#adsets-table #total-count').text("광고세트 "+res.adsets.length+"건 결과");
                $('#adsets-table #total-budget').text('\u20A9'+res.total.budget.toLocaleString('ko-KR'));
                $('#adsets-table #avg-cpa').text(Math.round(res.total.avg_cpa).toLocaleString('ko-KR'));
                $('#adsets-table #total-unique_total').text(res.total.unique_total);
                $('#adsets-table #total-spend').text('\u20A9'+res.total.spend.toLocaleString('ko-KR'));
                $('#adsets-table #total-margin').text('\u20A9'+res.total.margin.toLocaleString('ko-KR'));
                $('#adsets-table #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
                $('#adsets-table #total-sales').text('\u20A9'+res.total.sales.toLocaleString('ko-KR'));
                $('#adsets-table #total-impressions').text(res.total.impressions.toLocaleString('ko-KR'));
                $('#adsets-table #total-inline_link_clicks').text(res.total.inline_link_clicks.toLocaleString('ko-KR'));
                $('#adsets-table #avg-cpc').text('\u20A9'+Math.round(res.total.avg_cpc).toLocaleString('ko-KR'));
                $('#adsets-table #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
                $('#adsets-table #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');

                return res.adsets;
            }
        },
        "columns": [
            { "data": "name" },
            { "data": "status" },
            { "data": null, "defaultContent": ""},
            { 
                "data": "budget", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        budget = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        budget = "";
                    }
                    return budget;
                }
            }, //예산
            { 
                "data": "cpa",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpa = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpa = "";
                    }
                    return cpa;
                }
            }, //현재 DB단가
            { "data": "unique_total" }, //유효DB수
            { 
                "data": "spend",
                "render": function (data, type, row) {
                    if (data !== null) {
                        spend = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        spend = "";
                    }
                    return spend;
                }
            }, //지출액
            { 
                "data": "margin",
                "render": function (data, type, row) {
                    if (data !== null) {
                        margin = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        margin = "";
                    }
                    return margin;
                }
            }, //수익
            { 
                "data": "margin_ratio",
                "render": function (data, type, row) {
                    if (data !== null) {
                        margin_ratio = parseInt(data).toLocaleString('ko-KR')+'\u0025';  
                    }else{
                        margin_ratio = "";
                    }
                    return margin_ratio;
                }
            }, //수익률
            { 
                "data": "sales",
                "render": function (data, type, row) {
                    if (data !== null) {
                        sales = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        sales = "";
                    }
                    return sales;
                }
            }, //매출액
            { 
                "data": "impressions", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impressions = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impressions = "";
                    }
                    return impressions;
                }
            }, //노출수
            { 
                "data": "inline_link_clicks", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        inline_link_clicks = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        inline_link_clicks = "";
                    }
                    return inline_link_clicks;
                }
            }, //링크클릭
            { 
                "data": "cpc", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpc = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpc = "";
                    }
                    return cpc;
                }
            }, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", }, //클릭율 (노출 대비 클릭한 비율)
            { 
                "data": "cvr", 
                "render": function (data, type, row) {
                    return data+'\u0025';
                }
            }, //전환율
        ],
        "language": {
            "emptyTable": "데이터가 존재하지 않습니다.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "infoEmpty": "데이터 없음",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "에서 검색: ",
            "zeroRecords": "일치하는 데이터가 없어요.",
            "loadingRecords": "로딩중...",
        },
    });
}

function getAds(args) {
	$('#ads-table').DataTable({
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "bDestroy": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/facebook/data",
            "data": args,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                if(res.total.margin < 0){
                    $('#ads-table #total-margin').css('color', 'red');
                }
                
                if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
                    $('#ads-table #avg_margin_ratio').css('color', 'red');
                }

                $('#ads-table #total-count').text("광고 "+res.ads.length+"건 결과");
                $('#ads-table #avg-cpa').text(Math.round(res.total.avg_cpa).toLocaleString('ko-KR'));
                $('#ads-table #total-unique_total').text(res.total.unique_total);
                $('#ads-table #total-spend').text('\u20A9'+res.total.spend.toLocaleString('ko-KR'));
                $('#ads-table #total-margin').text('\u20A9'+res.total.margin.toLocaleString('ko-KR'));
                $('#ads-table #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
                $('#ads-table #total-sales').text('\u20A9'+res.total.sales.toLocaleString('ko-KR'));
                $('#ads-table #total-impressions').text(res.total.impressions.toLocaleString('ko-KR'));
                $('#ads-table #total-inline_link_clicks').text(res.total.inline_link_clicks.toLocaleString('ko-KR'));
                $('#ads-table #avg-cpc').text('\u20A9'+Math.round(res.total.avg_cpc).toLocaleString('ko-KR'));
                $('#ads-table #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
                $('#ads-table #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');

                return res.ads;
            }
        },
        "columns": [
            { "data": "name" },
            { "data": "status" },
            { 
                "data": "cpa",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpa = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpa = "";
                    }
                    return cpa;
                }
            }, //현재 DB단가
            { "data": "unique_total" }, //유효DB수
            { 
                "data": "spend",
                "render": function (data, type, row) {
                    if (data !== null) {
                        spend = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        spend = "";
                    }
                    return spend;
                }
            }, //지출액
            { 
                "data": "margin",
                "render": function (data, type, row) {
                    if (data !== null) {
                        margin = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        margin = "";
                    }
                    return margin;
                }
            }, //수익
            { 
                "data": "margin_ratio",
                "render": function (data, type, row) {
                    if (data !== null) {                    
                        margin_ratio = parseInt(data).toLocaleString('ko-KR')+'\u0025';  
                    }else{
                        margin_ratio = "";
                    }
                    return margin_ratio;
                }
            }, //수익률
            { 
                "data": "sales",
                "render": function (data, type, row) {
                    if (data !== null) {
                        sales = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        sales = "";
                    }
                    return sales;
                }
            }, //매출액
            { 
                "data": "impressions", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impressions = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impressions = "";
                    }
                    return impressions;
                }
            }, //노출수
            { 
                "data": "inline_link_clicks", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        inline_link_clicks = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        inline_link_clicks = "";
                    }
                    return inline_link_clicks;
                }
            }, //링크클릭
            { 
                "data": "cpc", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        cpc = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cpc = "";
                    }
                    return cpc;
                }
            }, //클릭당단가 (1회 클릭당 비용)
            { "data": "ctr", }, //클릭율 (노출 대비 클릭한 비율)
            { 
                "data": "cvr", 
                "render": function (data, type, row) {
                    return data+'\u0025';
                }
            }, //전환율
        ],
        "language": {
            "emptyTable": "데이터가 존재하지 않습니다.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "infoEmpty": "데이터 없음",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "에서 검색: ",
            "zeroRecords": "일치하는 데이터가 없어요.",
            "loadingRecords": "로딩중...",
        },
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

$('body').on('click', '.tab-link', function(){
    args.stx = "";
    var tabVal = $(this).val();
    args.type = tabVal;

    switch (args.type) {
    case "ads":
        getAds(args);
        break;
    case "adsets":
        getAdsets(args);
        break;
    default:
        getCampaigns(args);
    } 
});

$('body').on('click', '.client-list h3', function(){
    $(this).siblings('.row').slideToggle();
});

$('body').on('click', '#business_btn, #account_btn', function(){
    if ($(this).attr('id') === 'business_btn') {
        $('#account_btn').removeClass('active')
    }

    $(this).toggleClass("active");
    tab = $('.tab-link.active').val();
    business = $('#business_btn.active').map(function(){return $(this).val();}).get();
    accounts = $('#account_btn.active').map(function(){return $(this).val();}).get();
    args.businesses = business;
    args.accounts = accounts;
    
    if ($(this).attr('id') === 'business_btn') {
        args.accounts = [];
        getAccount(args);
    }
    getChartReport(args);
    switch (tab) {
    case "ads":
        getAds(args);
        break;
    case "adsets":
        getAdsets(args);
        break;
    default:
        getCampaigns(args);
    } 
});


$('body').on('click', '#search_btn', function() {
    tab = $('.tab-link.active').val();
    args = {
        'sdate': $('#sdate').val(),
        'edate': $('#edate').val(),
        'stx': $('#stx').val(),
        'type': tab,
    };
    
    getChartReport(args);
    getAccount(args);

    switch (tab) {
    case "ads":
        getAds(args);
        break;
    case "adsets":
        getAdsets(args);
        break;
    default:
        getCampaigns(args);
    } 
});

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>