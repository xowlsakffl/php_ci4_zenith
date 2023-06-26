<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
CHAIN 열혈광고 - 회계 관리 / 미수금 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script>
    console.log('header')
</script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">미수금 관리</h2>
    </div>
    <div class="row mt-5">
        <div class="col half">
            <h3 class="content-title">미수금 내역</h3>
            <div class="search-wrap">
                <form class="search d-flex justify-content-center">
                    <div class="input">
                        <input class="" type="text" placeholder="검색어를 입력하세요">
                        <button class="btn-primary" type="submit">조회</button>
                    </div>
                </form>
            </div>
            <p class="mb-4">* 2019년 9월 1일부터 세부내역 조회가 가능하며, 문의사항이 있을 경우 경영지원실로 문의하시기 바랍니다.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default caption-top">
                    <caption class="text-end">[단위: 원, 부가세포함]</caption>
                    <colgroup>
                        <col style="width:10%">
                        <col>
                        <col style="width:20%">
                        <col style="width:17%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">담당자</th>
                            <th scope="col">업체명</th>
                            <th scope="col">사업자 번호</th>
                            <th scope="col">최근 세금계산서</th>
                            <th scope="col">미수금</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>김혜린</td>
                            <td>우주마켓_MOBON</td>
                            <td>114-02-34856</td>
                            <td class="text-end">+865일</td>
                            <td class="text-end">2,345,789</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col half">
            <h3 class="content-title">입금 내역</h3>
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
            </div>
            <div class="regi-wrap">
                <form class="search d-flex justify-content-center">
                    <input type="text" class="form-control">
                    <button type="button"><i class="bi bi-calendar2-week"></i></button>
                    <input type="text" class="form-control" placeholder="광고주">
                    <input type="text" class="form-control" placeholder="사업자등록번호">
                    <input type="text" class="form-control" placeholder="입금액">
                    <input type="text" class="form-control" placeholder="적요/비고">
                    <button class="btn btn-dark ms-2" type="submit">등록</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:15%">
                        <col>
                        <col style="width:20%">
                        <col style="width:15%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">날짜</th>
                            <th scope="col">업체명</th>
                            <th scope="col">사업자 번호</th>
                            <th scope="col">입금액</th>
                            <th scope="col">비고</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2023-3-24</td>
                            <td>우주마켓_MOBON</td>
                            <td>114-02-34856</td>
                            <td class="text-end">34,4567,324</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script></script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>