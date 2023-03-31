<?=$this->extend('templates/front_example.php');?>

<?=$this->section('content');?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>매체 관리</title>
</head>
<body>
<div class="sub-contents-wrap">
        <div class="title-area">
            <h2 class="page-title">매체 관리</h2>
        </div>

        <div class="search-wrap">
            <form class="search d-flex justify-content-center">
                <div class="input">
                    <input class="" type="text" placeholder="검색어를 입력하세요">
                    <button class="btn-primary" type="submit">조회</button>
                    <button class="btn-special ms-2" type="button" data-bs-toggle="modal" data-bs-target="#mediaModal">등록</button>
                </div>
            </form>
        </div>

        <div class="section ">
            <div class="btn-wrap text-end mb-2">
                <button type="button" class="btn btn-danger">이벤트 관리</button>
                <button type="button" class="btn btn-danger">광고주 관리</button>
                <button type="button" class="btn btn-danger">전환 관리</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-default">
                    <colgroup>
                        <col style="width:6%">
                        <col>
                        <col style="width:12%">
                        <col style="width:5%">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">번호</th>
                            <th scope="col">매체명</th>
                            <th scope="col">대상</th>
                            <th scope="col">랜딩수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>5054</td>
                            <td class="text-start">아이시티안과의원</td>
                            <td>본 메일 수신자</td>
                            <td>1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



</body>
</html>
<?=$this->endSection();?>

<?=$this->section('modal')?>
<!-- 매체 등록 -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="mediaModalLabel">매체 등록</h1>
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
                                <th scope="row" class="text-end">매체명</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">대상</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">확인</button>
                <button type="button" class="btn btn-secondary">목록</button>
            </div>
        </div>
    </div>
</div>
<!-- //매체 등록 -->
<?=$this->endSection();?>
