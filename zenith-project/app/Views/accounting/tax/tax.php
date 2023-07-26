<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
CHAIN 열혈광고 - 회계 관리 / 세금계산서 요청
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
        <h2 class="page-title">세금계산서 요청</h2>
        <p class="title-disc">세금계산서 발행을 경영지원실에 요청하세요~ 나연님, 서진님 고생이 많으십니다.</p>
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
            <select class="form-select" aria-label="선택">
                <option selected>-선택-</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
            </select>
            <div class="input">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form>
    </div>

    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 담당자</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 결과</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 구분</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button">열혈 패밀리</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스5</button>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">케어랩스7</button>
                </div>
            </div>
        </div>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/accounting/taxList"><button type="button" class="btn btn-outline-danger">업체목록(세금계산서)</button></a>
            <a href="#"><button type="button" class="btn btn-outline-danger">글쓰기(세금계산서)</button></a>
            <a href="#"><button type="button" class="btn btn-outline-danger">엑셀백업</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <colgroup>
                    <col style="width:3%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:15%">
                    <col style="width:10%">
                    <col style="width:5%">
                    <col style="">
                    <col style="width:8%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">발행일자</th>
                        <th scope="col">작성자</th>
                        <th scope="col">구분</th>
                        <th scope="col">사업자명</th>
                        <th scope="col">사업자등록번호</th>
                        <th scope="col">대표자명</th>
                        <th scope="col">내역</th>
                        <th scope="col">공급가액</th>
                        <th scope="col">총금액<br>(VAT 포함)</th>
                        <th scope="col">비고</th>
                        <th scope="col">결과</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-0">845</td>
                        <td>2023-03-13</td>
                        <td>김혜린</td>
                        <td>입금완료</td>
                        <td>우주마켓_MOBON</td>
                        <td>234-45-12456</td>
                        <td>이주옥</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td>1,100,000</td>
                        <td></td>
                        <td>완료</td>
                    </tr>
                    <tr>
                        <td class="p-0">845</td>
                        <td>2023-03-13</td>
                        <td>김혜린</td>
                        <td>입금완료</td>
                        <td>우주마켓_MOBON</td>
                        <td>234-45-12456</td>
                        <td>이주옥</td>
                        <td>인라이플_모비온_우주마켓 광고비 지출결의</td>
                        <td>1,100,000</td>
                        <td>1,100,000</td>
                        <td>2/28 재발행 예정</td>
                        <td>완료</td>
                    </tr>
                </tbody>
            </table>
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