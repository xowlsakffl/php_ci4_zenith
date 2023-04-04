<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/tablesorter/dist/css/theme.default.min.css" rel="stylesheet"> 
<script src="/static/node_modules/tablesorter/dist/js/jquery.tablesorter.js"></script>
<script src="/static/node_modules/tablesorter/dist/js/extras/jquery.tablesorter.pager.min.js"></script>
<script src="/static/node_modules/tablesorter/dist/js/jquery.tablesorter.widgets.js"></script>
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
                <input type="text">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form>
        <div class="detail row d-flex justify-content-center">
            <dl class="col">
                <dt>노출수</dt>
                <dd>34,456</dd>
            </dl>
            <dl class="col">
                <dt>클릭수</dt>
                <dd>809</dd>
            </dl>
            <dl class="col">
                <dt>클릭율</dt>
                <dd>1.09</dd>
            </dl>
            <dl class="col">
                <dt>지출액</dt>
                <dd>1,234,123</dd>
            </dl>
            <dl class="col">
                <dt>DB수</dt>
                <dd>61</dd>
            </dl>
            <dl class="col">
                <dt>DB당 단가</dt>
                <dd>45,234</dd>
            </dl>
            <dl class="col">
                <dt>전환율</dt>
                <dd>7.34</dd>
            </dl>
            <dl class="col">
                <dt>매출</dt>
                <dd>23,456,900</dd>
            </dl>
        </div>
    </div>

    <div class="section client-list advertiser">
        <h3 class="content-title toggle">
            <i class="bi bi-chevron-up"></i> 
            광고주
        </h3>
        <div class="row">

        </div>
        <!-- <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">거무타</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">마디척병원○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div> -->
    </div>
    <!-- <div class="section client-list advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매체</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" class="active alert">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="alert">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="active">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">거무타</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">마디척병원○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 이벤트 구분</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" class="active alert">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="alert">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="active">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">거무타</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">마디척병원○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="section client-list advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 이벤트 구분</h3>
        
    </div>

    <div>
        <div class="row">
            <table class="tablesorter">
                <caption>목록</caption>
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
            <div id="pager" class="pager">
                <button class="first">맨 처음</button>
                <button class="prev">이전</button>
                <span class="pagedisplay" data-pager-output-filtered="{startRow:input} &ndash; {endRow} / 전체 {totalRows}개 중 {filteredRows}개"></span>
                <button class="next">다음</button>
                <button class="last">마지막</button>
                <select class="pagesize">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="all">All Rows</option>
                </select>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
$(function(){
    $('.tablesorter').tablesorter({
        showProcessing: true,
        headerTemplate : '{content} {icon}',
        widthFixed: true,
        widgets: ['uitheme', 'filter', 'stickyHeaders'],
        widgetOptions: {
            sortReset   : true,
            sortRestart : true,
            resizable: true,
            scroller_height : 300,
            stickyHeaders_includeCaption : false,
            // scroll tbody to top after sorting
            scroller_upAfterSort: true,
            scroller_jumpToHeader: true,
            scroller_barWidth : null,
            scroller_addFixedOverlay : false,
            filter_external : '#stx',
            filter_columnFilters: false,
            filter_saveFilters : true,
            filter_searchFiltered : true,
        }
    }).tablesorterPager({
        container: $(".pager"),
        ajaxUrl: "<?=base_url()?>/integrate/list",
        customAjaxUrl: function(table, url) {
            var pageNumber = table.config.pager.page;
            var pageSize = table.config.pager.size;
            return url + '?page=' + pageNumber + '&size=' + pageSize;
        },
        ajaxError: null,
        ajaxObject: { 
            type: 'GET',
            dataType: 'json' 
        },
        ajaxProcessing: function(data) {
            if (data && data.hasOwnProperty('result')) {
                var indx, r, row, c, d = data.result,
                total = data.total_rows,
                headers = data.headers,
                headerXref = headers.join(',').replace(/\s+/g,'').split(','),
                rows = [],
                len = d.length;
                for ( r=0; r < len; r++ ) {
                    row = [];
                    for ( c in d[r] ) {
                    if (typeof(c) === "string") {
                        indx = $.inArray( c, headerXref );
                        if (indx >= 0) {
                        row[indx] = d[r][c];
                        }
                    }
                    }
                    rows.push(row);
                }
                return [ total, rows];
            }
        },
        processAjaxOnInit: true,
        output: '{startRow:input} – {endRow} / {totalRows} 개',
        updateArrows: true,
        page: 0,
        size: 20,
        savePages : true,
        storageKey:'tablesorter-pager',
        pageReset: 0,
        fixedHeight: true,
        removeRows: false,
        countChildRows: false,
        cssNext: '.next', // next page arrow
        cssPrev: '.prev', // previous page arrow
        cssFirst: '.first', // go to first page arrow
        cssLast: '.last', // go to last page arrow
        cssGoto: '.gotoPage', // select dropdown to allow choosing a page
        cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
        cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option
        cssDisabled: 'disabled', // Note there is no period "." in front of this class name
        cssErrorRow: 'tablesorter-errorRow' // ajax error information row
    });

})

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
