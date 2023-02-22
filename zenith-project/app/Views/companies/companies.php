<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>

    <!--content-->
    <div class="container-md">
        <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">글수정</h5>
                </div>
                <div class="modal-body">
                    <form id="frm">
                        <input type="hidden" name="id" id="hidden_id">
                        <div class="form-group">
                            <label for="file">이미지</label>
                            <input type="file" name="file[]" class="form-control" id="board_file" multiple>
                        </div>
                        <div class="form-group">
                            <label for="board_title">제목</label>
                            <input type="text" name="board_title" class="form-control" id="board_title">
                        </div>
                        <div class="form-group">
                            <label for="board_description">본문</label>
                            <textarea name="board_description" class="form-control" id="board_description" style="min-height:300px"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-primary" id="boardUpdateBtn">저장</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-primary" id="boardUpdateModal">수정</button>
                    <button type="button" class="btn btn-danger" id="boardDelete">삭제</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalWrite" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">글쓰기</h5>
                </div>
                <div class="modal-body">
                    <form id="frm">
                        <input type="hidden" name="id" id="hidden_id">
                        <div class="form-group">
                            <label for="file">이미지</label>
                            <input type="file" name="file[]" class="form-control file" multiple>
                        </div>
                        <div class="form-group">
                            <label for="board_title">제목</label>
                            <input type="text" name="board_title" class="form-control board_title">
                        </div>
                        <div class="form-group">
                            <label for="board_description">본문</label>
                            <textarea name="board_description" class="form-control board_description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-primary" id="boardInsertBtn">저장</button>
                </div>
                </div>
            </div>
        </div>
        <h1 class="font-weight-bold">광고주, 광고대행사 관리</h1>
        <div class="row">
            <div class="col-3">
                <select name="pageLimit" id="pageLimit" class="form-control">
                    <option value="10">10개</option>
                    <option value="50">50개</option>
                    <option value="100">100개</option>
                </select>
            </div>
            <div class="col-5">
                <input type="text" class="form-control" id="search" name="search" placeholder="검색">
            </div>
            <table class="table" id="board">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">제목</th>
                        <th scope="col">생성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="row pagination-container">
                <ul class="pagination">

                </ul>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-end">
                <button class="btn btn-primary" id="boardNewBtn">글쓰기</button>
            </div>
        </div>
    </div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script src="/static/js/twbsPagination.js"></script>
<script>
$(document).ready(function(){

getBoardList(1);
function getBoardList(page, limit, search){
    data = {
        'page': page ? page : 10,
        'limit': limit ? limit : 10,
        'search': search ? search : '',
    };
    
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/boards",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(xhr){
            setTable(xhr);       
            setPaging(xhr);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setPaging(xhr){
    $('.pagination').twbsPagination('destroy');
    $('.pagination').twbsPagination({
        totalPages: xhr.pager.pageCount,	// 총 페이지 번호 수
        visiblePages: 20,	// 하단에서 한번에 보여지는 페이지 번호 수
        startPage : xhr.pager.currentPage, // 시작시 표시되는 현재 페이지
        initiateStartPageClick: false,	// 플러그인이 시작시 페이지 버튼 클릭 여부 (default : true)
        first : "첫 페이지",	// 페이지네이션 버튼중 처음으로 돌아가는 버튼에 쓰여 있는 텍스트
        prev : "이전 페이지",	// 이전 페이지 버튼에 쓰여있는 텍스트
        next : "다음 페이지",	// 다음 페이지 버튼에 쓰여있는 텍스트
        last : "마지막 페이지",	// 페이지네이션 버튼중 마지막으로 가는 버튼에 쓰여있는 텍스트
        nextClass : "page-item next",	// 이전 페이지 CSS class
        prevClass : "page-item prev",	// 다음 페이지 CSS class
        lastClass : "page-item last",	// 마지막 페이지 CSS calss
        firstClass : "page-item first",	// 첫 페이지 CSS class
        pageClass : "page-item",	// 페이지 버튼의 CSS class
        activeClass : "active",	// 클릭된 페이지 버튼의 CSS class
        disabledClass : "disabled",	// 클릭 안된 페이지 버튼의 CSS class
        anchorClass : "page-link",	//버튼 안의 앵커에 대한 CSS class
        
        onPageClick: function (event, page) {
            console.log(xhr.pager.limit);
            getBoardList(page, xhr.pager.limit)
        }
    });
}

function setTable(xhr){
    $('#board tbody').empty();
    $.each(xhr.result, function(index, item){   
        index++
        $('<tr id="boardView" data-id="'+item.bdx+'">').append('<td>'+item.bdx+'</td>')
        .append('<td>'+item.board_title+'</td>')
        .append('<td>'+item.created_at+'</td>')
        .appendTo('#board'); 
    });
}

//검색
$('body').on('keyup', '#search', function(){
    getBoardList(1, 50,$(this).val());
})

//페이지 게시글 갯수
$('body').on('change', '#pageLimit', function(){
    getBoardList(1, $(this).val());
})


//글쓰기 버튼
$('body').on('click', '#boardNewBtn', function(){
    $('#modalWrite #frm').trigger("reset");
    var myModal = new bootstrap.Modal(document.getElementById('modalWrite'))
    myModal.show()
})

//저장 버튼
$('body').on('click', '#boardInsertBtn', function(){
    data = {
        file: $('.file').val(),
        board_title: $('input:text[name=board_title]').val(),
        board_description: $('textarea[name=board_description]').val(),
    };
    console.log(data);
    $.ajax({
        type: "post",
        url: "<?=base_url()?>/boards",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(response){
            $('#modalWrite').modal('hide');
            $('#modalWrite').find('input').val('');  
            $('#board').DataTable().ajax.reload();
            console.log(response);
        },
        error: function(error){
            alert(error.responseJSON.messages.error);
            //alert("에러코드: " + xhr.status + " 메시지: " + xhr.responseText );
        }
    });
})

//글보기
$('body').on('click', '#boardView', function(){
    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/boards/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            $('#modalView .modal-title').html(data.result.board_title);
            $('#modalView .modal-body').html(data.result.board_description);
            $('#modalView #boardUpdateModal').attr('data-id', data.result.bdx);
            $('#modalView #boardDelete').attr('data-id', data.result.bdx);
            var myModal = new bootstrap.Modal(document.getElementById('modalView'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//글수정
$('body').on('click', '#boardUpdateModal', function(){
    $('#modalView').modal('hide');

    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/boards/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            $('#modalUpdate #board_title').val(data.result.board_title);
            $('#modalUpdate #board_description').val(data.result.board_description);
            $('#modalUpdate #hidden_id').val(data.result.bdx);
            var myModal = new bootstrap.Modal(document.getElementById('modalUpdate'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//글수정
$('body').on('click', '#boardUpdateBtn', function(){
    let id = $("#modalUpdate input:hidden[name=id]").val();
    data = {
        //file: $('.file').val(),
        board_title: $('input:text[name=board_title]').val(),
        board_description: $('textarea[name=board_description]').val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/boards/"+id,
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(response){
            $('#modalUpdate').modal('hide');
            $('#modalUpdate').find('input').val('');  
            getBoardList(1);
            console.log(response);
        },
        error: function(error){
            alert(error.responseJSON.messages.error);
            //alert("에러코드: " + xhr.status + " 메시지: " + xhr.responseText );
        }
    });
})

//글삭제
$('body').on('click', '#boardDelete', function(){
    let id = $(this).attr('data-id');
    if(confirm('정말 삭제하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/boards/"+id,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                $('#modalView').modal('hide');
                getBoardList(1);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
})

});

</script>
<?=$this->endSection();?>