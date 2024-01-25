<div class="modal fade" id="oneLogModal" tabindex="-1" aria-labelledby="logModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="logModal"><i class="ico-log"></i> 개별 로그</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="section client-list">
                    <h3 class="content-title toggle">
                        <i class="bi bi-chevron-up"></i> 
                    </h3>
                    <div class="row" id="media-list"></div>
                </div>
                <div class="search-wrap log-search-wrap">
                    <form name="log-search-form" class="search">
                        <div class="input">
                            <input type="text" name="log_stx" id="stx" placeholder="검색어를 입력하세요">
                            <button class="btn-primary" id="search_btn" type="submit">조회</button>
                        </div>
                    </form>
                </div>
                <table class="table tbl-dark" id="logTable" style="width: 100%;">
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