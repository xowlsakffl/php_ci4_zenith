<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
CHAIN 열혈광고 - 세금계산서 요청
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

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bizModal">업체등록(세금계산서)</button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#taxModal">리스트(세금계산서)</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <colgroup>
                    <col style="width:15%">
                    <col style="width:10%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:8%">
                    <col style="">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:8%">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">사업자명</th>
                        <th scope="col">사업자등록번호</th>
                        <th scope="col">대표자명</th>
                        <th scope="col">업태</th>
                        <th scope="col">종목</th>
                        <th scope="col">주소</th>
                        <th scope="col">메일주소1</th>
                        <th scope="col">메일주소2</th>
                        <th scope="col">작성자</th>
                        <th scope="col">담당자</th>
                        <th scope="col">작성일</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>우주마켓_MOBON</td>
                        <td>234-45-12456</td>
                        <td>김혜린</td>
                        <td>도소매</td>
                        <td>화장품</td>
                        <td>서울특별시 강남구 테헤란로 123길 22, b동 3층(신사동, 케이티타워신사)</td>
                        <td>b-uwqej@nave.rcom</td>
                        <td></td>
                        <td>고영석</td>
                        <td>고영석</td>
                        <td>2023-01-23 14:14:34</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>


<?=$this->section('modal');?>
<!-- 업체 등록 -->
<div class="modal fade" id="bizModal" tabindex="-1" aria-labelledby="bizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="bizModalLabel">업체 등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row">사업자명</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">사업자등록번호</th>
                                <td>
                                    <div class="d-flex">
                                        <input type="text" class="form-control">
                                        <button type="button" class="btn btn-secondary btn-sm">중복확인</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">대표자</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">업태</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">종목</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">주소</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">이메일1</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row">당당자</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-danger btn-sm">이메일 추가</button>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">작성완료</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
            </div>
        </div>
    </div>
</div>
<!-- //업체 등록 -->

<!-- 세금계산서 -->
<div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="taxModalLabel">세금계산서 발행 요청서</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="approval">
                    <ol class="d-flex">
                        <li>
                            <span>담당자</span>
                            <div></div>
                        </li>
                        <li>
                            <span>경영지원실장</span>
                            <div></div>
                        </li>
                        <li>
                            <span>사업부대표</span>
                            <div></div>
                        </li>
                    </ol>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:25%;">
                            <col style="width:75%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row">담당자</th>
                                <td>
                                    <select class="form-select" aria-label="담당자 선택">
                                        <option selected>-선택-</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">사업자명</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">사업자 등록번호</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">대표자</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">업태</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">종목</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">주소</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">이메일1</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">이메일2</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">발행일자</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">구분</th>
                                <td>
                                    <select class="form-select" aria-label="구분 선택">
                                        <option selected>-선택-</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">내역</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">1.</span>
                                        <input type="text" class="form-control" placeholder="내역">
                                        <input type="text" class="form-control ms-2 me-2 w-50" placeholder="공급가액">
                                        <input type="text" class="form-control w-50" placeholder="부가세">
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="button">- 헹 제거</button>
                                        <button type="button">+ 행 추가</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">총금액(VAT포함)</th>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-dark btn-sm me-2">자동계산</button>
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">공급가액</th>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-dark btn-sm me-2">자동계산</button>
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">비고</th>
                                <td>
                                    <textarea></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">작성완료</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
            </div>
        </div>
    </div>
</div>
<!-- //세금계산서 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script></script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>