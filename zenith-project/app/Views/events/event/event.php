<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 이벤트 / 이벤트 관리
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
        <h2 class="page-title">이벤트 관리</h2>
    </div>

    <div class="search-wrap">
        <form name="search-form" class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" name="sdate" id="sdate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>
                <input type="text" name="edate" id="edate">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" name="stx" id="stx" placeholder="검색어를 입력하세요">
                <button class="btn-primary" id="search_btn" type="submit">조회</button>
                <button class="btn-special ms-2" type="button" data-bs-toggle="modal" data-bs-target="#regiModal">등록</button>
            </div>
        </form>
    </div>

    <div class="section">
        <div class="btn-wrap text-end mb-2">
            <a href="/eventmanage/advertiser"><button type="button" class="btn btn-danger">광고주 관리</button></a>
            <a href="/eventmanage/media"><button type="button" class="btn btn-danger">매체 관리</button></a>
            <a href="/eventmanage/change"><button type="button" class="btn btn-danger">전환 관리</button></a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-default">
                <colgroup>
                    <col style="width:6%">
                    <col style="width:10%">
                    <col style="width:5%">
                    <col>
                    <col style="width:12%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:8%">
                    <col style="width:5%">
                    <col style="width:5%">
                    <col style="width:5%">
                </colgroup>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">이벤트번호</th>
                        <th scope="col">광고주</th>
                        <th scope="col">매체</th>
                        <th scope="col">브라우저 타이틀</th>
                        <th scope="col">이벤트 구분</th>
                        <th scope="col">외부연동</th>
                        <th scope="col">사용여부</th>
                        <th scope="col">유입수</th>
                        <th scope="col">유효DB</th>
                        <th scope="col">DB단가</th>
                        <th scope="col">작성자</th>
                        <th scope="col">작업자</th>
                        <th scope="col">작성일</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
                        <td><i class="ico red"></i> 5054</td>
                        <td>아이시티안과의원</td>
                        <td>페이스북</td>
                        <td>아이시티안과 라식라섹 최대 할인</td>
                        <td>라식라섹 최대 할인</td>
                        <td></td>
                        <td>사용중</td>
                        <td>0</td>
                        <td>0</td>
                        <td>50,000</td>
                        <td>전유빈</td>
                        <td></td>
                        <td>02-14</td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal');?>
<!-- 이벤트 등록 -->
<div class="modal fade" id="regiModal" tabindex="-1" aria-labelledby="regiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="regiModalLabel">이벤트 등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h2 class="body-title">이벤트 정보</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">랜딩번호</th>
                                <td>6124</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩주소</th>
                                <td>
                                    <a href="#">https://event.asdkljad.com</a>
                                    <button type="button" class="btn btn-secondary btn-sm">복사하기</button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">광고주</th>
                                <td><input type="text" class="form-control" value="예쁨주의상상의원" disabled></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">매체</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">이벤트 구분</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">DB 단가</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩구분</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio01">
                                            <label class="form-check-label" for="landing_radio01">일반</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio02">
                                            <label class="form-check-label" for="landing_radio02">잠재고객</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio03">
                                            <label class="form-check-label" for="landing_radio03">비즈폼</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio04">
                                            <label class="form-check-label" for="landing_radio04">엑셀업로드</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="landing_radio" id="landing_radio05">
                                            <label class="form-check-label" for="landing_radio05">API수신</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">외부연동 사용여부</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio01">
                                            <label class="form-check-label" for="linkage_radio01">사용</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="linkage_radio" id="linkage_radio02">
                                            <label class="form-check-label" for="linkage_radio02">미사용</label>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <input type="text" class="form-control me-2">
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="d-flex">
                                        <input type="text" class="form-control me-2">
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">랜딩 고정값</th>
                                <td>
                                    <div class="d-flex mb-2">
                                        <select class="form-select me-2" aria-label="선택">
                                            <option selected>-선택-</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="d-flex mb-2">
                                        <select class="form-select me-2" aria-label="선택">
                                            <option selected>-선택-</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                        <input type="text" class="form-control">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm float-end">추가</button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">브라우저 제목(타이틀)</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">키워드</th>
                                <td>
                                    <input type="text" class="form-control">
                                    <p class="mt-2 text-secondary">※ 이벤트의 핵심 키워드를 입력해주세요.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">설명</th>
                                <td><input type="text" class="form-control" placeholder="이벤트를 구체적으로 설명해주세요. (약 40자내외, 최대 100자이내)"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집목적</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">수집항목</th>
                                <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">페이스북 Pixel ID</th>
                                <td><input type="text" class="form-control" placeholder="페이스북 픽셀을 사용하는 경우 체인쏘우에 등록된 픽셀ID를 입력해 주세요."></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">View Script</th>
                                <td><textarea placeholder="추적스크립트(View)"></textarea></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">Complete Script</th>
                                <td><textarea placeholder="추적스크립트(Complete)"></textarea></td>
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
                                            <label class="form-check-label" for="linkage_radio02">미사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 광고주가 사용중지로 되어있을 경우 랜딩은 노출되지 않습니다.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>

                <h2 class="body-title">이벤트 정보</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-left-header">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th scope="row" class="text-end">성별</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio01">
                                            <label class="form-check-label" for="gender_radio01">무관</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio02">
                                            <label class="form-check-label" for="gender_radio02">남</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender_radio" id="gender_radio03">
                                            <label class="form-check-label" for="gender_radio03">여</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">나이</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control">
                                        <span class="m-2">~</span>
                                        <input type="text" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">중복기간</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio01">
                                            <label class="form-check-label" for="term_radio01">1일</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio02">
                                            <label class="form-check-label" for="term_radio02">1주</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio03">
                                            <label class="form-check-label" for="term_radio03">1개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio04">
                                            <label class="form-check-label" for="term_radio04">2개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio05">
                                            <label class="form-check-label" for="term_radio05">3개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio06">
                                            <label class="form-check-label" for="term_radio06">4개월</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio07">
                                            <label class="form-check-label" for="term_radio07">5개월</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="term_radio" id="term_radio08">
                                            <label class="form-check-label" for="term_radio08">전체</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 중복기간 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">전화번호 중복</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="phone_radio" id="phone_radio01">
                                            <label class="form-check-label" for="phone_radio01">미사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="phone_radio" id="phone_radio02">
                                            <label class="form-check-label" for="phone_radio02">사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 전화번호 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">이름 중복</th>
                                <td>
                                    <div class="d-flex radio-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="name_radio" id="name_radio01">
                                            <label class="form-check-label" for="name_radio01">미사용</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="name_radio" id="name_radio02">
                                            <label class="form-check-label" for="name_radio02">사용</label>
                                        </div>
                                    </div>
                                    <p class="text-secondary">※ 이름 중복 사용/미사용 변경이후 시점의 DB부터 적용됩니다.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-end">사전 중복 체크</th>
                                <td>
                                    <select class="form-select me-2" aria-label="선택">
                                        <option selected>-선택-</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">복사</button>
                <button type="button" class="btn btn-danger">삭제</button>
                <button type="button" class="btn btn-secondary">목록</button>
                <button type="button" class="btn btn-primary">목록 수정하기</button>
            </div>
        </div>
    </div>
</div>
<!-- //이벤트 등록 -->
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script></script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
