<div class="modal fade" id="advLogModal" tabindex="-1" aria-labelledby="logModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="logModal"><i class="ico-log"></i> 광고별 자동화 로그 <span></span></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="section client-list automationBtnWrap">
                    <h3 class="content-title toggle">
                        <i class="bi bi-chevron-up"></i> 
                        소속 자동화 목록
                    </h3>
                    <div class="row" id="automation-list"></div>
                </div>
                <table class="table tbl-dark" id="advLogTable" style="width: 100%;">
                    <colgroup>
                        <col>
                        <col style="width:22%;">
                        <col style="width:18%;">
                        <col style="width:22%;">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">제목</th>
                            <th scope="col">작성자</th>
                            <th scope="col">결과</th>
                            <th scope="col">마지막 실행</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>