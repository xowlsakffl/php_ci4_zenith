<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
    <!--content-->
    <div class="container-md">
        <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
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
            <div class="col">
                <!-- <form action="/posts" id="frm" method="get" class="d-flex mb-3">
                    <input type="text" name="searchData" class="form-control mx-3" id="searchText">
                    <input type="submit" value="Search" class="btn btn-primary">                  
                </form> -->
            </div>
        </div>
        <div class="row">
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

$.ajaxSetup({
    headers: {
        ['<?=csrf_token()?>']: '<?=csrf_hash()?>',
    }
});

getBoardList();
/* getUser();
userUpdate();
userDelete(); */

function getBoardList(){
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/boards",
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: boardList,
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function boardList(xhr){
    console.log(xhr);
    $('#board tbody').empty();
    $.each(xhr.result, function(index, item){   
        index++
        $('<tr id="boardSelect" data-id="'+item.bdx+'">').append('<td>'+item.bdx+'</td>')
        .append('<td>'+item.board_title+'</td>')
        .append('<td>'+item.created_at+'</td>')
        .appendTo('#board'); 
    });
}

/* function getUser(){
    $('body').on('click', '#userView', function(){
        let id = $(this).attr('data-id');
        $.ajax({
            type: "get",
            url: "<?=base_url()?>/users/"+id,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: userModal,
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    })
} */

//글쓰기 버튼
$('body').on('click', '#boardNewBtn', function(){
    $('#frm').trigger("reset");
    $('.modal-title').html("글쓰기");
    var myModal = new bootstrap.Modal(document.getElementById('Modal'))
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
            $('#Modal').modal('hide');
            $('#Modal').find('input').val('');  
            getBoardList();
            console.log(response);
        },
        error: function(error){
            alert(error.responseJSON.messages.error);
            //alert("에러코드: " + xhr.status + " 메시지: " + xhr.responseText );
        }
    });
})

});

</script>
<?=$this->endSection();?>