<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal2">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">회원</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" method="post">
                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="text" name="email" class="form-control email">
                        <span id="error_email" class="text-danger ms-3"></span>
                    </div>
                    <div class="form-group">
                        <label for="username">이름</label>
                        <input type="text" name="username" class="form-control username">
                        <span id="error_username" class="text-danger ms-3"></span>
                    </div>
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" name="password" class="form-control password">
                        <span id="error_password" class="text-danger ms-3"></span>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">비밀번호 확인</label>
                        <input type="password" name="password_confirm" class="form-control password_confirm">
                        <span id="error_password_confirm" class="text-danger ms-3"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                
            </div>
            </div>
        </div>
    </div>
    <!--content-->
    <div class="container-md">
        <div class="row">
            <div class="col">
                <form action="/posts" id="frm" method="get" class="d-flex mb-3">
                    <input type="text" name="searchData" class="form-control mx-3" id="searchText">
                    <input type="submit" value="Search" class="btn btn-primary">                  
                </form>
            </div>
        </div>
        <div class="row">
            <table class="table" id="userTable">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">이름</th>
                        <th scope="col">상태</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="row">
                <?php //$pager->links() ?>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-end">
                <button class="btn btn-primary" id="userAddBtn"  data-bs-toggle="modal" data-bs-target="#Modal">회원 추가</button>
            </div>
        </div>
    </div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){


getUserList();
getUser();
userUpdate();
userDelete();

function getUserList(){
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/users",
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: userList,
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function getUser(){
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
}

function userList(xhr){
    $('#userTable tbody').empty();
    $.each(xhr, function(index, item){
        $('<tr id="userSelect" data-id="'+item.id+'">').append('<td>'+index+'</td>')
        .append('<td>'+item.username+'</td>')
        .append('<td>'+item.status+'</td>')
        .append('<td><button class="btn btn-primary" id="userView" data-id="'+item.id+'" data-bs-toggle="modal" data-bs-target="#Modal">수정</button><button class="btn btn-danger" id="userDelete" data-id="'+item.id+'">삭제</button></td>')
        .appendTo('#userTable');
    });
}

function userModal(xhr){
    $('#frm')[0].reset();
    $('#frm').append($("<input type='hidden' name='id' id='hidden_id'>").val(xhr.id))
    $('.modal2 .modal-footer').append('<button type="button" class="btn btn-dark userUpdateBtn">수정</button>')
    $('input:text[name=username]').val(xhr.username);
};

function userAdd(){
    $('body').on('click', '.userAddBtn', function(){
        data = {
            ['<?=csrf_token()?>']: '<?=csrf_hash()?>',
            username: $('input:text[name=username]').val(),
            email: $('input:text[name=email]').val(),
            password: $('input:text[name=password]').val(),
        };

        $.ajax({
            type: "post",
            url: "<?=base_url()?>/users",
            data: data,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            headers:{'X-Requested-With':'XMLHttpRequest'},
            success: function(response){
                console.log(response);
                $('#Modal').modal('hide');
                $('#Modal').find('input').val('');  
                getUserList();
            },
            error: function(error, status, msg){
                console.log("에러코드: " + status + " 메시지: " + msg );
            }
        });
    })
}

function userUpdate(){
    $('body').on('click', '.userUpdateBtn', function(){
        let id = $("input:hidden[name=id]").val();
        data = {
            ['<?=csrf_token()?>']: '<?=csrf_hash()?>',
            username: $('input:text[name=username]').val(),
        };

        $.ajax({
            type: "put",
            url: "<?=base_url()?>/users/"+id,
            data: data,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            headers:{'X-Requested-With':'XMLHttpRequest'},
            success: function(response){
                console.log(response);
                $('#Modal').modal('hide');
                $('#Modal').find('input').val('');  
                getUserList();
            },
            error: function(error, status, msg){
                console.log("에러코드: " + status + " 메시지: " + msg );
            }
        });
    })
}

function userDelete(){
    $('body').on('click', '#userDelete', function(){
        let id = $(this).attr('data-id');

        if(confirm('정말 삭제하시겠습니까?')){
            $.ajax({
                type: "delete",
                url: "<?=base_url()?>/users/"+id,
                dataType: "json",
                headers:{'X-Requested-With':'XMLHttpRequest'},
                success: function(response){
                    getUserList();
                    location.reload();
                }
            });
        } 
    })
}


$("#Modal").on("hidden.bs.modal", function () {
    $('#frm')[0].reset();
    $('.modal-footer .userUpdateBtn').hide();
});

$('#userAddBtn').on('click', function(){
    $('#modal1 .modal-footer').append('<button type="button" class="btn btn-dark userAddBtn">추가</button>')
})
});

</script>
<?=$this->endSection();?>