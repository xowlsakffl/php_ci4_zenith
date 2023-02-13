<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<div class="container">
    <div class="row">
        <div class="card mx-auto" style="width: 18rem;">
            <div class="card-body">
                <div class="card-title">회원 추가</div>
                <form id="frm" method="POST">
                    <div class="form-group">
                        <label for="password">이메일</label>
                        <input type="email" name="email" class="form-control email">
                    </div>
                    <div class="form-group">
                        <label for="password">아이디</label>
                        <input type="text" name="username" class="form-control username">
                    </div>
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" name="password" class="form-control password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">비밀번호 확인</label>
                        <input type="password" name="password_confirm" class="form-control password_confirm">
                    </div>
                </form>
                <div class="d-grid gap-2 d-md-flex justify-content-end mt-2">
                    <button class="btn btn-primary" id="userAddBtn">회원 추가</button>
                </div>
            </div>
        </div>
    </div>
</div>


            

<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){

userAdd();
function userAdd(){
    $('body').on('click', '#userAddBtn', function(){
        data = {
            ['<?=csrf_token()?>']: '<?=csrf_hash()?>',
            username: $('input:text[name=username]').val(),
            email: $('input[type=email][name=email]').val(),
            password: $('input:password[name=password]').val(),
            password_confirm: $('input:password[name=password_confirm]').val(),
        };

        $.ajax({
            type: "post",
            url: "<?=base_url()?>/users",
            data: data,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            headers:{'X-Requested-With':'XMLHttpRequest'},
            success: function(response){
                $('#Modal').modal('hide');
                $('#Modal').find('input').val('');  
                window.location.href='/users-list';
            },
            error: function(error, status, msg){
                alert("에러코드: " + status + " 메시지: " + msg );
            }
        });
    })
}

});

</script>
<?=$this->endSection();?>