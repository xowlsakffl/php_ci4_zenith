<?=$this->extend('templates/front_example.php');?>

<?=$this->section('content');?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>출금요청 - 업체목록</title>
</head>
<body>
<div class="sub-contents-wrap">
        <div class="title-area">
            <h2 class="page-title">출금요청 - 업체목록</h2>
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

        <div class="section ">
            <div class="btn-wrap text-end mb-2">
                <button type="button" class="btn btn-danger">업체등록(출금요청)</button>
                <button type="button" class="btn btn-danger">리스트(출금요청)</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default table-sm">
                    <colgroup>
                        <col style="">
                        <col style="width:8%">
                        <col style="width:15%">
                        <col style="width:5%">
                        <col style="width:15%">
                        <col style="width:5%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">거래처명(예금주명)</th>
                            <th scope="col">은행</th>
                            <th scope="col">계좌번호</th>
                            <th scope="col">작성자</th>
                            <th scope="col">작성일</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>(주)당금페이_상상의원3_21783</td>
                            <td>기업은행</td>
                            <td>0492173819374</td>
                            <td>윤재진</td>
                            <td>2023-03-02 :11:34:23</td>
                            <td>
                                <button type="button" class="btn btn-dark btn-sm">DEL</button>
                            </td>
                        </tr>
                        <tr>
                            <td>(주)당금페이_상상의원3_21783</td>
                            <td>기업은행</td>
                            <td>0492173819374</td>
                            <td>윤재진</td>
                            <td>2023-03-02 :11:34:23</td>
                            <td>
                                <button type="button" class="btn btn-dark btn-sm">DEL</button>
                            </td>
                        </tr>
                        <tr>
                            <td>(주)당금페이_상상의원3_21783</td>
                            <td>기업은행</td>
                            <td>0492173819374</td>
                            <td>윤재진</td>
                            <td>2023-03-02 :11:34:23</td>
                            <td>
                                <button type="button" class="btn btn-dark btn-sm">DEL</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?=$this->endSection();?>
