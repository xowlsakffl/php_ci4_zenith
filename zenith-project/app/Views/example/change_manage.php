<?=$this->extend('templates/front_example.php');?>
<?=$this->section('title');?>
    CHAIN 열혈광고 - 전환 관리
<?=$this->endSection();?>

<?=$this->section('content');?>
<body>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">전환 관리</h2>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="input">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
                <button class="btn-special ms-2" type="button" data-bs-toggle="modal" data-bs-target="#changeModal">등록</button>
            </div>
        </form>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <button type="button" class="btn btn-danger">이벤트 관리</button>
            <button type="button" class="btn btn-danger">광고주 관리</button>
            <button type="button" class="btn btn-danger">매체 관리</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <colgroup>
                    <col style="width:20%">
                    <col>
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">전환ID</th>
                        <th scope="col">전환명</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12378436743535</td>
                        <td>전국상상 무제한카톡5</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal')?>
<!-- 전환 등록 -->
<div class="modal fade" id="changeModal" tabindex="-1" aria-labelledby="changeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="changeModalLabel">등록</h1>
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
                                <th scope="row" class="text-end">전환ID</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">전환명</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">Access Token</th>
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
<!-- //전환 등록 -->
</body>
</html>
<?=$this->endSection();?>
