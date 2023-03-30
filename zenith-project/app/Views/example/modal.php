<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    모달 전체
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">모달</h2>
    </div>

    <!-- 메모 확인 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#memoModal">메모 확인</button>
    <div class="modal fade" id="memoModal" tabindex="-1" aria-labelledby="memoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="memoModalLabel"><i class="bi bi-file-text"></i> 메모 확인</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea></textarea>
                </div>
            </div>
        </div>
    </div>
    <!-- //메모 확인 -->

    <!-- 개별 메모 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#memoModal2">개별 메모</button>
    <div class="modal fade" id="memoModal2" tabindex="-1" aria-labelledby="memoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-sm sm-txt">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="memoModalLabel2"><i class="bi bi-file-text"></i> 개별 메모</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="regi-form">
                        <fieldset>
                            <legend>메모 작성</legend>
                            <textarea></textarea>
                            <button type="button" class="btn-regi">작성</button>
                        </fieldset>
                    </form>
                    <ul class="memo-list m-2">
                        <li class="d-flex justify-content-between align-items-start">
                            <div class="detail d-flex align-items-start">
                                <input type="checkbox" value="" id="check01">
                                <p class="ms-1" aria-labelledby="check01">오후 4시 라이브</p>
                            </div>
                            <div class="info">
                                <span>김민정</span>
                                <span>2023-03-03 10:40:30</span>
                            </div>
                        </li>
                        <li class="d-flex justify-content-between align-items-start">
                            <div class="detail d-flex align-items-start">
                                <input type="checkbox" value="" id="check02">
                                <p class="ms-1" aria-labelledby="check02">오후 4시 라이브 2줄 <br>메모내용 테스트 입니다.</p>
                            </div>
                            <div class="info">
                                <span>김민정</span>
                                <span>2023-03-03 10:40:30</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- //개별 메모 -->

    <!-- 광고 변경 내역 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeModal">광고 변경 내역</button>
    <div class="modal fade" id="changeModal" tabindex="-1" aria-labelledby="changeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm sm-txt">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="changeModalLabel"><i class="bi bi-arrow-counterclockwise"></i> 광고 변경 내역</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="ad-status">진행 상태</p>
                    <ul class="status-type">
                        <li class="d-flex justify-content-between">
                            <div>비활성 <b class="text-danger">→ 활성</b> 으로 변경</div>
                            <div class="info">
                                <span>김민정</span>
                                <span>2023-03-03 10:40:30</span>
                            </div>
                        </li>
                        <li class="d-flex justify-content-between">
                            <div>활성 <b class=text-dark">→ 비활성</b> 으로 변경</div>
                            <div class="info">
                                <span>김민정</span>
                                <span>2023-03-03 10:40:30</span>
                            </div>
                        </li>
                        <li class="d-flex justify-content-between">
                            <div>비활성 <b class="text-danger">→ 활성</b> 으로 변경</div>
                            <div class="info">
                                <span>김민정</span>
                                <span>2023-03-03 10:40:30</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- //광고 변경 내역 -->

    <br><br><br>

    <!-- 데이터 비교 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dataModal">데이터 비교</button>
    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="dataModalLabel">데이터 비교</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="sorting d-flex justify-content-between align-items-end">
                        <div class="d-flex">
                            <button type="button" class="active">최근 7일</button>
                            <button type="button">최근 14일</button>
                            <button type="button">최근 30일</button>
                            <button type="button">지난달</button>
                        </div>
                        <p class="term">2023.02.11~ 2023.02.18</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-data">
                            <colgroup>
                                <col style="width:calc(100% / 6);">
                                <col style="width:calc(100% / 6);">
                                <col style="width:calc(100% / 6);">
                                <col style="width:calc(100% / 6);">
                                <col style="width:calc(100% / 6);">
                                <col style="width:calc(100% / 6);">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">DB 단가</th>
                                    <th scope="col">DB 수</th>
                                    <th scope="col">수익률</th>
                                    <th scope="col">CPC</th>
                                    <th scope="col">전환률</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col">오늘</th>
                                    <td>9,326</td>
                                    <td>140</td>
                                    <td>93.26</td>
                                    <td>9,326.50</td>
                                    <td>93.26</td>
                                </tr>
                                <tr>
                                    <th scope="col">어제</th>
                                    <td>9,326</td>
                                    <td>140</td>
                                    <td>93.26</td>
                                    <td>9,326.50</td>
                                    <td>93.26</td>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-danger">최근 7일</th>
                                    <td>9,326</td>
                                    <td>140</td>
                                    <td>93.26</td>
                                    <td>9,326.50</td>
                                    <td>93.26</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //데이터 비교 -->

    <br><br><br>

    <!-- AI 작동내역 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aiModal">AI 작동내역</button>
    <div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable sm-txt">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="aiModalLabel">AI 작동내역</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <colgroup>
                                <col style="width:15%;">
                                <col style="width:25%;">
                                <col style="width:25%;">
                                <col style="width:20%;">
                                <col style="width:15%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">광고주</th>
                                    <th scope="col">캠페인명</th>
                                    <th scope="col">광고그룹명</th>
                                    <th scope="col">내역</th>
                                    <th scope="col">실행일시</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                                <tr>
                                    <th scope="col">패밀리유</th>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td>1887_402 (남여, 카서, 네트워크 40~59)</td>
                                    <td>Level 1 (210 => 180)</td>
                                    <td>2023-02-19 05:38:50</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //AI 작동내역 -->

    <br><br><br>

    <!-- 광고 코드 추가 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">광고 코드 추가</button>
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="addModalLabel">광고 코드 추가</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2 class="body-title">[병원] 유로스메디컬의원</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <colgroup>
                                <col style="width:15%;">
                                <col style="width:15%;">
                                <col style="width:30%;">
                                <col style="width:40%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">광고명</th>
                                    <th scope="col">광고그룹</th>
                                    <th scope="col">타입</th>
                                    <th scope="col">인식코드</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="ico green"></i> 5054</td>
                                    <td>5054</td>
                                    <td>VIDEO_RESPONSIVE_AD</td>
                                    <td><input type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <a href="#">https://event.asdjk.co.kr/asdjk?asduw=asd</a>
                                        <button type="button" class="btn btn-dark">코드입력</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //광고 코드 추가 -->

    <br><br><br>

    <!-- 계정 예산 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">계정 예산</button>
    <div class="modal fade" id="budgetModal" tabindex="-1" aria-labelledby="budgetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="budgetModalLabel">계정 예산</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="body-title">[패밀리유] 비즈보드 X 룰렛</h2>
                        <button type="button" class="btn btn-primary btn-list">광고주 잔여예산 부족 목록</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <colgroup>
                                <col style="width:60%;">
                                <col style="width:20%;">
                                <col style="width:20%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">광고주명</th>
                                    <th scope="col">계정예산</th>
                                    <th scope="col">잔여예산</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>[패밀리유] 비즈보드 X 룰렛</td>
                                    <td class="text-end">50,000,000</td>
                                    <td class="text-end">50,000,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //계정 예산 -->

    <!-- 계정 통계 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statisticstModal">계정 통계</button>
    <div class="modal fade" id="statisticstModal" tabindex="-1" aria-labelledby="statisticstModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="statisticstModalLabel">계정 통계</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="body-title">계정별 통계 (2023-02-18 ~ 2023-02-23)</h2>
                        <form class="row g-1">
                            <div class="col-auto">
                                <input type="password" class="form-control">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">검색</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-modal">
                            <colgroup>
                                <col style="width:15%;">
                                <col>
                                <col style="width:11%;">
                                <col style="width:8%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:8%;">
                                <col style="width:9%;">
                                <col style="width:9%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">계정ID</th>
                                    <th scope="col">광고주명</th>
                                    <th scope="col">현재DB단계</th>
                                    <th scope="col">유효DB</th>
                                    <th scope="col">지출액</th>
                                    <th scope="col">수익</th>
                                    <th scope="col">수익률</th>
                                    <th scope="col">매출액</th>
                                    <th scope="col">매출비중</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>302-934-2394</td>
                                    <td class="text-end">W2_랜덤팡_2</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end">25,234</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end">%</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end">0.00%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //계정 통계 -->

    <br><br><br>

    <!-- 업체 등록 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bizModal">업체 등록</button>
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
                                    <th scope="row">거래처명(입금자명)</th>
                                    <td>
                                        <input type="text" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">은행명</th>
                                    <td>
                                        <select class="form-select" aria-label="은행명 선택">
                                            <option selected>-선택-</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">계좌번호</th>
                                    <td><input type="text" class="form-control"></td>
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
    <!-- //업체 등록 -->

    <br><br><br>

    <!-- 지출결의서 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disbursementModal">지출결의서</button>
    <div class="modal fade" id="disbursementModal" tabindex="-1" aria-labelledby="disbursementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="disbursementModalLabel">지출결의서</h1>
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
                                <col style="width:30%;">
                                <col style="width:70%;">
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
                                    <th scope="row">문서번호</th>
                                    <td>
                                        <textarea></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">거래처명(예금주명)</th>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">은행명</th>
                                    <td>
                                        <input type="text" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">계좌번호</th>
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
                                    <th scope="row">내역(자세히)</th>
                                    <td>
                                        <textarea></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">총금액(VAT 포함)</th>
                                    <td>
                                        <input type="text" class="form-control">
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
    <!-- //업체 등록 -->

    <br><br><br>

    <!-- 전체화면 모달 -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fullModal">전체화면 모달</button>
    <div class="modal fade" id="fullModal" tabindex="-1" aria-labelledby="fullModalLabel"  aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="fullModalLabel">전체화면 모달</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                ...
                </div>
            </div>
        </div>
    </div>
    <!-- //전체화면 모달 -->
</div>
<?=$this->endSection();?>
