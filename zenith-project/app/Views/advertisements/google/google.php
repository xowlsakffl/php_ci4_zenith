<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고 관리 / 구글 애드워즈
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
        <h2 class="page-title">구글 애드워즈 광고관리</h2>
        <p class="title-disc">광고주별 매체의 기본적인 광고 합성/종료/수정의 기능을 제공하고 있으며, 추가적으로 CHAIN에서 개발한 스마트하게 광고를 최적화 시켜주는 기능도 함께 이용할 수 있습니다.</p>
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
            <div class="input">
                <input class="" type="text" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form>
        <div class="detail row d-flex justify-content-center">
            <dl class="col">
                <dt>노출수</dt>
                <dd>34,456</dd>
            </dl>
            <dl class="col">
                <dt>클릭수</dt>
                <dd>809</dd>
            </dl>
            <dl class="col">
                <dt>클릭율</dt>
                <dd>1.09</dd>
            </dl>
            <dl class="col">
                <dt>지출액</dt>
                <dd>1,234,123</dd>
            </dl>
            <dl class="col">
                <dt>DB수</dt>
                <dd>61</dd>
            </dl>
            <dl class="col">
                <dt>DB당 단가</dt>
                <dd>45,234</dd>
            </dl>
            <dl class="col">
                <dt>전환율</dt>
                <dd>7.34</dd>
            </dl>
            <dl class="col">
                <dt>수익</dt>
                <dd>1,234,123</dd>
            </dl>
            <dl class="col">
                <dt>수익율</dt>
                <dd>7.34</dd>
            </dl>
            <dl class="col">
                <dt>매출</dt>
                <dd>23,456,900</dd>
            </dl>
        </div>
    </div>

    <div class="section client-list biz">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 매니저 계정</h3>
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

    <div class="section client-list advertiser">
        <h3 class="content-title toggle"><i class="bi bi-chevron-up"></i> 광고주</h3>
        <div class="row">
            <div class="col">
                <div class="inner">
                    <button type="button" class="active alert">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="alert">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button" class="active">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">거무타</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">마디척병원○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[전국]상상의원_주름보톡스2*</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">[대전]상상의원_가나다라마마다먀라</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">플란치과_임플○</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="inner">
                    <button type="button">비결뷰티센터</button>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:calc(14 / 170 * 100%)"></div>
                        <div class="txt">14/170</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-wrap">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="campaign-tab" data-bs-toggle="tab" data-bs-target="#campaign-tab-pane" type="button" role="tab" aria-controls="campaign-tab-pane" aria-selected="true">캠페인</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="set-tab" data-bs-toggle="tab" data-bs-target="#set-tab-pane" type="button" role="tab" aria-controls="set-tab-pane" aria-selected="false">광고 그룹</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="advertisement-tab" data-bs-toggle="tab" data-bs-target="#advertisement-tab-pane" type="button" role="tab" aria-controls="advertisement-tab-pane" aria-selected="false">소재</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="campaign-tab-pane" role="tabpanel" aria-labelledby="campaign-tab">
                <div class="btn-wrap">
                    <button type="button" class="btn btn-outline-danger active">수동 업데이트</button>
                    <button type="button" class="btn btn-outline-danger">데이터 비교</button>
                    <button type="button" class="btn btn-outline-danger"><i class="bi bi-file-text"></i> 메모확인</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-default">
                        <colgroup>
                            <col>
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:6%">
                            <col style="width:4%">
                            <col style="width:7.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:7.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:5.5%">
                            <col style="width:6.5%">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">캠페인명</th>
                                <th scope="col">상태</th>
                                <th scope="col">입찰 <br>유형</th>
                                <th scope="col">예산</th>
                                <th scope="col">현재 <br>DB단가</th>
                                <th scope="col">유효 <br>DB</th>
                                <th scope="col">지출액</th>
                                <th scope="col">수익</th>
                                <th scope="col">수익률</th>
                                <th scope="col">매출액</th>
                                <th scope="col">노출수</th>
                                <th scope="col">링크클릭</th>
                                <th scope="col">CPC</th>
                                <th scope="col">CTR</th>
                                <th scope="col">DB <br>전환률</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col" class="text-center">캠페인 11건 결과</th>
                                <td></td>
                                <td></td>
                                <td>￦22,345,222</td>
                                <td>￦30,342</td>
                                <td>515</td>
                                <td>￦18,345,342</td>
                                <td>￦6,235,666</td>
                                <td>29.45%</td>
                                <td>￦21,345,678</td>
                                <td>654,345</td>
                                <td>9,457</td>
                                <td>￦1,778</td>
                                <td>1.13%</td>
                                <td>6.34%</td>
                            </tr>
                            <tr>
                                <th scope="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="check01">
                                        <label class="form-check-label" for="check01">[02.22][케랩7_마디척병원_부위별통증] evt_5721 @15 진선_주말OFF_규칙</label>
                                    </div>
                                    <div class="btn-area">
                                        <button type="button" class="btn btn-outline-secondary btn-paper"><i class="bi bi-file-text"></i></button>
                                    </div>
                                </th>
                                <td>
                                    <select name="" class="form-select">
                                        <option value="">활성</option>
                                        <option value=""></option>
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td></td>
                                <td>￦100,000</td>
                                <td>0</td>
                                <td>0</td>
                                <td>￦15,345</td>
                                <td>￦-15,678</td>
                                <td>0%</td>
                                <td>￦0</td>
                                <td>631</td>
                                <td>3</td>
                                <td>￦5,345</td>
                                <td>0.48%</td>
                                <td>0%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="set-tab-pane" role="tabpanel" aria-labelledby="set-tab">
                광고 그룹
            </div>
            <div class="tab-pane" id="advertisement-tab-pane" role="tabpanel" aria-labelledby="advertisement-tab">
                소재
            </div>
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