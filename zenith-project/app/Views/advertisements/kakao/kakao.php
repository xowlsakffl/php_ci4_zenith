<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 카카오 모먼트
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
</style>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">카카오 모먼트 광고관리</h2>
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
                        <colgroup>
                            <col>
                            <col style="width:7%">
                            <col style="width:7%">
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
                    <table class="table table-striped table-hover table-default" id="adsets-table">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
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
                                <th scope="col">광고그룹명</th>
                                <th scope="col">상태</th>
                                <th scope="col">Ai</th>
                                <th scope="col">예산</th>
                                <th scope="col">입찰금액</th>
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
            <div class="tab-pane" id="advertisement-tab-pane" role="tabpanel" aria-labelledby="advertisement-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default" id="ads-table">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:7%">
                            <col style="width:7%">
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
                                <th scope="col">광고명</th>
                                <th scope="col">상태</th>
                                <th scope="col">Ai</th>
                                <th scope="col">프리퀀시캡</th>
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
getReport(args);
getAccount(args);
getCampaigns(args);

function getReport(args){
    $.ajax({
        type: "GET",
        url: "<?=base_url()?>/advertisements/kakao/report",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('#impressions_sum').text(data.impressions_sum.toLocaleString('ko-KR'));
            $('#clicks_sum').text(data.clicks_sum.toLocaleString('ko-KR'));
            $('#click_ratio_sum').text(data.click_ratio_sum);
            $('#spend_sum').text(data.spend_sum.toLocaleString('ko-KR'));
            $('#spend_ratio_sum').text(data.spend_ratio_sum.toLocaleString('ko-KR'));
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
        url: "<?=base_url()?>/advertisements/kakao/accounts",
        data: args,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            $('.advertiser .row').empty();
            var html = '';
            $.each(data, function(idx, v) {       
                c = v.class;
                c = c.join(' ');

                html += '<div class="col"><div class="inner"><button type="button" value="'+v.id+'" id="account_btn" class="filter_btn '+c+'">'+v.name+'</button></div></div>';
            });

            $('.advertiser .row').html(html);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setDataTable(tableId, columns, args){
    $(tableId).DataTable({
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "bDestroy": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/kakao/data",
            "data": args,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                if(res.total.margin < 0){
                    $(tableId+' #total-margin').css('color', 'red');
                }
                
                if(res.total.avg_margin_ratio < 20 && res.total.avg_margin_ratio != 0){
                    $(tableId+' #avg_margin_ratio').css('color', 'red');
                }

                $(tableId+' #total-count').text(res.data.length+"건 결과");
                $(tableId+' #avg-cpa').text(Math.round(res.total.avg_cpa).toLocaleString('ko-KR'));

                if(tableId == '#campaigns-table'){
                    $(tableId+' #total-unique_total').html('<div>'+res.total.unique_total+'</div><div style="color:blue">'+res.total.expect_db+'</div>');
                    $(tableId+' #total-budget').text('\u20A9'+res.total.budget.toLocaleString('ko-KR'));
                }else if(tableId == '#adsets-table'){
                    $(tableId+' #total-unique_total').text(res.total.unique_total);
                    $(tableId+' #total-budget').text('\u20A9'+res.total.budget.toLocaleString('ko-KR'));
                }else{
                    $(tableId+' #total-unique_total').text(res.total.unique_total);
                }

                $(tableId+' #total-spend').text('\u20A9'+res.total.spend.toLocaleString('ko-KR'));
                $(tableId+' #total-margin').text('\u20A9'+res.total.margin.toLocaleString('ko-KR'));
                $(tableId+' #avg_margin_ratio').text(Math.round(res.total.avg_margin_ratio * 100) / 100 +'\u0025');
                $(tableId+' #total-sales').text('\u20A9'+res.total.sales.toLocaleString('ko-KR'));
                $(tableId+' #total-impressions').text(res.total.impressions.toLocaleString('ko-KR'));
                $(tableId+' #total-inline_link_clicks').text(res.total.click.toLocaleString('ko-KR'));
                $(tableId+' #avg-cpc').text('\u20A9'+Math.round(res.total.avg_cpc).toLocaleString('ko-KR'));
                $(tableId+' #avg-ctr').text(Math.round(res.total.avg_ctr * 100) / 100);
                $(tableId+' #avg-cvr').text(Math.round(res.total.avg_cvr * 100) / 100 +'\u0025');

                return res.data;
            }
        },
        "columns": columns,
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

function getCampaigns(args) {
    setDataTable('#campaigns-table', [
            { "data": "name" },
            { "data": "status" },
            { "data": "autoBudget" },
            { 
                "data": "dailyBudgetAmount", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        dailyBudgetAmount = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        dailyBudgetAmount = "";
                    }
                    return dailyBudgetAmount;
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
                "data": "cost",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cost = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cost = "";
                    }
                    return cost;
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
                            return '<span style="color:red">'+margin_ratio+'</span>';
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
                "data": "impression", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impression = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impression = "";
                    }
                    return impression;
                }
            }, //노출수
            { 
                "data": "click", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        click = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        click = "";
                    }
                    return click;
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
        ], args);
}

function getAdsets(args) {
    setDataTable('#adsets-table', [
            { "data": "name" },
            { "data": "status" },
            { 
                "data": "aiConfig",
                "render": function (data, type, row) {
                    html = '';
                    html += `<select name="aiConfig" data-id="${row.id}" class="form-select ai-select active-select ai-${row.aiConfig}">`;
                    html += `<option value="OFF" ${row.aiConfig === 'OFF' ? 'selected' : ''}>X</option><option value="ON" ${row.aiConfig === 'ON' ? 'selected' : ''}>O</option></select>`;
                    html += `<select name="aiConfig" data-id="${row.id}" class="form-select ai-select active-select ai-${row.aiConfig2}">`;
                    html += `<option value="OFF" ${row.aiConfig2 === 'OFF' ? 'selected' : ''}>X</option><option value="ON" ${row.aiConfig === 'ON' ? 'selected' : ''}>O</option></select>`;

                    return html;
                }    
            },
            { 
                "data": "dailyBudgetAmount", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        dailyBudgetAmount = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        dailyBudgetAmount = "";
                    }
                    return dailyBudgetAmount;
                }
            }, //예산
            { 
                "data": "bidAmount", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        bidAmount = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        bidAmount = "";
                    }
                    return bidAmount;
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
                "data": "cost",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cost = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cost = "";
                    }
                    return cost;
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
                            return '<span style="color:red">'+margin_ratio+'</span>';
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
                "data": "impression", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impression = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impression = "";
                    }
                    return impression;
                }
            }, //노출수
            { 
                "data": "click", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        click = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        click = "";
                    }
                    return click;
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
        ], args);
}

function getAds(args) {
    setDataTable('#ads-table', [
            { "data": "name" },
            { 
                "data": "status",
                "render": function (data, type, row) {
                    html = '';
                    html += `<select name="status" data-id="${row.id}" class="form-select active-select">`;
                    html += `<option value="OFF" ${row.status === 'OFF' ? 'selected' : ''}>비활성</option><option value="ON" ${row.status === 'ON' ? 'selected' : ''}>활성</option></select>`;

                    return html;
                }  
            },
            { 
                "data": "aiConfig",
                "render": function (data, type, row) {
                    html = '';
                    html += `<select name="aiConfig" data-id="${row.id}" class="form-select active-select ai-${row.aiConfig}">`;
                    html += `<option value="OFF" ${row.aiConfig === 'OFF' ? 'selected' : ''}>비활성</option><option value="ON" ${row.aiConfig === 'ON' ? 'selected' : ''}>활성</option></select>`;

                    return html;
                }    
            },
            { "data": "frequencyCap" }, //프리퀀시캡
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
                "data": "cost",
                "render": function (data, type, row) {
                    if (data !== null) {
                        cost = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        cost = "";
                    }
                    return cost;
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
                            return '<span style="color:red">'+margin_ratio+'</span>';
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
                "data": "impression", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        impression = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        impression = "";
                    }
                    return impression;
                }
            }, //노출수
            { 
                "data": "click", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        click = parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        click = "";
                    }
                    return click;
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
        ], args);
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

$('body').on('click', '#account_btn', function(){

    $(this).toggleClass("active");
    tab = $('.tab-link.active').val();
    accounts = $('#account_btn.active').map(function(){return $(this).val();}).get();
    args.accounts = accounts;
    
    getReport(args);
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
    
    getReport(args);
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