<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 광고주 관리
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
        <h2 class="page-title">광고주 관리</h2>
    </div>

    <div class="search-wrap">
        <form class="search d-flex justify-content-center">
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" id="createBtn" data-bs-toggle="modal" data-bs-target="#clientModal" type="button">등록</button>
            </div>
        </form>
    </div>

    <div class="section ">
        <div class="btn-wrap text-end mb-2">
            <a href="/eventmanage/event"><button type="button" class="btn btn-danger">이벤트 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-danger">전환 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">광고주명</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">매출</th>
                        <th scope="col">남은잔액</th>
                        <th scope="col">랜딩수</th>
                        <th scope="col">사업자명</th>
                        <th scope="col">외부연동</th>
                        <th scope="col">개인정보 전문</th>
                        <th scope="col">사용여부</th>
                        <th scope="col">작성일</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>5054</td>
                        <td class="text-start">아이시티안과의원</td>
                        <td>0</td>
                        <td class="text-end">0</td>
                        <td class="text-end">2,342,656</td>
                        <td>1</td>
                        <td>체인지미의원</td>
                        <td>ㅇ</td>
                        <td>ㅇ</td>
                        <td>사용중</td>
                        <td>02-07</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal');?>
<!-- 광고주 등록 -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="clientModalLabel">광고주 등록</h1>
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
                                <th scope="row" class="text-end">광고주명</th>
                                <td>
                                    <input type="text" class="form-control">
                                    <p class="mt-2 text-secondary">※ 한번 등록 된 광고주는 수정이 불가능합니다. 띄어쓰기, 오타 확인 꼭 해주세요.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집주체(사업자명)</th>
                                <td>
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">외부연동 주소</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">개인정보 전문 주소</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">입금액</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio01">
                                            <label class="form-check-label" for="linkage_radio01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio02">
                                            <label class="form-check-label" for="linkage_radio02">사용중지</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 사용중지로 변경할 경우 해당 광고주의 모든 랜딩이 중지됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">문자 알림 사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="sms_radio" id="sms_radio01">
                                            <label class="form-check-label" for="sms_radio01">사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sms_radio" id="sms_radio02">
                                            <label class="form-check-label" for="sms_radio02">미사용</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">Notice</th>
                                <td>
                                    <ul>
                                        <li>* 00시 ~ 06시에는 문자를 발송하지 않습니다.</li>
                                        <li>* 알림 문자는 매체별로 1일 1회 발송됩니다.</li>
                                        <li>* 기타 문의사항은 [개발팀-정문숙]에게 문의 부탁드립니다.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">알림 연락처</th>
                                <td>
                                    <input type="text" class="form-control mb-2">
                                    <input type="text" class="form-control mb-2">
                                    <input type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">페이스북</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">GDN</th>
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
<!-- //광고주 등록 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script></script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>