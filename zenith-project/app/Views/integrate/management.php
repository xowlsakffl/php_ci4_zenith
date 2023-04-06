<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 통합 DB 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-dt/css/jquery.dataTables.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.js"></script>
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
    $('#deviceTable').DataTable({
        "responsive": true,
        "paging": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?=base_url()?>/integrate/list",
            "type": "GET",
            "data": function (d) {
                d.start = d.start;
                d.length = d.length;
                d.draw = d.draw;
                d.search = d.search.value;
                d.recordsTotal = d.recordsTotal;
            },
            "contentType": "application/json",
            "dataType": "json"
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { "data": "event_seq" },
            { "data": "advertiser" },
            { "data": "media" },
            { "data": "tab_name" },
            { "data": "name" },
            { "data": "dec_phone" },
            { "data": "age" },
            { "data": "gender" },
            { "data": "add1" },
            { "data": "add2" },
            { "data": "site" },
            { "data": "reg_date" },
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
            "processing":     "잠시만 기다려 주세요...",
            "paginate": {
                "next": "다음",
                "previous": "이전"
            }

        },
    });
})

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
