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
        <h1 class="font-weight-bold">게시판</h1>
        <div class="row">
            <table class="table" id="board">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">제목</th>
                        <th scope="col">본문</th>
                        <th scope="col">생성일</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="row pageNum">

            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-end">
                <button class="btn btn-primary" id="boardNewBtn">글쓰기</button>
            </div>
        </div>
    </div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){

getBoardList();
function getBoardList(){
    data = [
        
    ];
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/boards",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: boardList,
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function boardList(xhr){
    boardPagination(xhr);
    $('#board tbody').empty();
    $.each(xhr.result, function(index, item){   
        index++
        $('<tr id="boardSelect" data-id="'+item.bdx+'">').append('<td>'+item.bdx+'</td>')
        .append('<td>'+item.board_title+'</td>')
        .append('<td>'+item.created_at+'</td>')
        .appendTo('#board'); 
    });
}

function boardPagination(xhr){
    let pagingHtml = $('#board').parent(); //append시킬 부모 요소

    const pageBlock = parseInt(xhr.pager.pageCount / 3);//페이지 버튼 수
    console.log(pageBlock);
    let pages = [];
    let curBlockNum = parseInt((xhr.pager.currentPage - 1) / 3);//페이지 버튼 숫자

    if(xhr.pager.total > 0){
        for(let i = xhr.pager.firstPage; i <= xhr.pager.lastPage; i++){
            pages.push(i);
        }
    }
    
    let html = '<div class="pagination">';

    if(xhr.pager.current !== 1){
        html += '<a href="#" id="fiest">처음</a>';
        html += '<a href="#" id="prev">이전글</a>';
    }

    if (pages.length > 0) {
		for (let i = 0; i < pages.length; i++) {
			html += "<a href='#' id=" + (pages[i] + 1) + ">" + (pages[i] + 1) + "</a>";
		}
	}

    if (xhr.pager.pageCount > 1 && xhr.pager.current !== xhr.pager.pageCount) {
		html += '<a href=# id="next">다음글</a>';
		html += '<a href=# id="last">마지막</a>';
	}
    html += '</div>';

    $(pagingHtml).append(html);
    $(".pagination a").css("color", "black");
	$(".pagination a#" + xhr.pager.current).css({ "text-decoration": "none", "font-weight": "bold" }); 
}

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
            $('#modalView .modal-title').html(data.board_title);
            $('#modalView .modal-body').html(data.board_description);
            $('#modalView #boardUpdateModal').attr('data-id', data.bdx);
            $('#modalView #boardDelete').attr('data-id', data.bdx);
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
            $('#modalUpdate #board_title').val(data.board_title);
            $('#modalUpdate #board_description').val(data.board_description);
            $('#modalUpdate #hidden_id').val(data.bdx);
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
            $('#board').DataTable().ajax.reload();
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
                $('#board').DataTable().ajax.reload();
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