<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 엑셀 업로드
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
        <h2 class="page-title">엑셀 업로드</h2>
    </div>
    <div class="section ">
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
                            <input class="form-control" type="file" id="formFile">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-primary">[업로드 가이드]</span>
                                <table class="table table-bordered mb-0">
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
                                            <th scope="col">샘플</th>
                                            <td><a href="#">샘플 다운로드</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                        </td>
                    </tr>
                    <tr>
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
            <button type="button" class="btn btn-primary">확인</button>
            <a href="/example/event_manage"><button type="button" class="btn btn-secondary">이벤트 목록</button></a>
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