<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 엑셀 업로드
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">엑셀 업로드</h2>
    </div>
    <div class="section ">
        <form name="eventfrm" enctype="multipart/form-data" method="POST" action="/eventmanage/excel/upload">
            <?= csrf_field() ?>
            <?php if(session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                    <?php echo session()->getFlashdata('success') ?>
                </div>
            <?php elseif (session()->getFlashdata('failed')) : ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                    <?php echo session()->getFlashdata('failed') ?>
                </div>
            <?php endif ?>
            <div class="table-responsive">
                <table class="table table-bordered table-left-header">
                    <colgroup>
                        <col style="width:20%;">
                        <col style="width:80%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row" rowspan="3" class="text-end align-top pt-3">엑셀</th>
                            <td>
                                <input class="form-control <?php if($validation->getError('upload_file')): ?>is-invalid<?php endif ?>" type="file" id="formFile" name="upload_file">
                                <?php if ($validation->getError('upload_file')): ?>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('upload_file') ?>
                                </div>                                
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-primary" >
                                    <button class="btn-show">[업로드 가이드]</button>
                                </span>
                                <table class="table table-bordered mb-0 excel_sample">
                                    <colgroup>
                                        <col style="width:20%;">
                                        <col style="width:80%;">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th scope="col" class="align-middle">연동가능 광고주</th>
                                            <td>
                                                리얼딥<br>
                                                하늘안과의원<br>
                                                밝은성모안과병원<br>
                                                리얼딥<br>
                                                하늘안과의원<br>
                                                밝은성모안과병원<br>
                                                리얼딥<br>
                                                하늘안과의원<br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col" id="myElement">샘플</th>
                                            <td><a href="https://static.hotblood.co.kr/DB_upload_sample.csv" class="text-primary">샘플 다운로드</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr class="excel_sample">
                            <td>
                                <b>※ 업로드 유의사항</b>
                                <ul>
                                    <li>1. 반드시 지정된 양식에 맞춘 csv 파일을 업로드 부탁드립니다.</li>
                                    <li>2. 신규 랜딩 번호로 DB 업로드 필요시 개발팀에 알림 부탁드립니다.</li>
                                    <li>3. 등록된 연동가능 광고주 외의 연동이 필요한 DB는 업로드 전 개발팀에 알림 부탁드립니다.</li>
                                    <li>4. 연동 시 랜딩번호, 광고주 항목 입력은 필수입니다.</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>                    
            </div>
            <div class="btn-area text-center">
                <button type="submit" class="btn btn-primary">등록</button>
                <a href="/eventmanage/event"><button type="button" class="btn btn-secondary">이벤트 목록</button></a>
            </div>
        </form>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

$(".btn-show").click(function(e) {
    $(".excel_sample").toggle();
    return false;
});
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>