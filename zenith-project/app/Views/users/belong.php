<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 사용자 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script src="/static/js/twbsPagination.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="container-md">
    <h1 class="font-weight-bold mb-5">사용자 소속 변경</h1>
    <div class="row">
        <div class="col-6 m-auto">
            <div class="card">
                <div class="card-body">
                    <form id="frm">
                        <input type="hidden" name="id" id="hidden_id" value="<?=$user->id?>">
                        <div class="form-group mb-3">
                            아이디 : <span><?=$user->username?></span>
                        </div>
                        <div class="form-group mb-3">
                            현재 소속 : <span><?=$user->companyName?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="company">소속 선택</label>
                            <select name="company" class="form-control" id="userCompany">
                                <?php foreach ($companies as $company) :?>
                                    <option value="<?= $company['cdx']?>"><?= $company['companyName'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <span id="company_error" class="text-danger"></span>
                        </div>
                    </form>
                    <div class="modal-footer">            
                        <button type="button" class="btn btn-primary" id="userCompanyUpdate">저장</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){

$('body').on('click', '#userCompanyUpdate', function(){
    data = {
        company_id: $('#userCompany').val(),
        user_id: $("input:hidden[name=id]").val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/user/belong",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(data){
            location.reload();
        },
        error: function(error){
            var errorText = error.responseJSON.messages;
            $.each(errorText, function(key, val){
                $("#modalUpdate #" + key + "_error").text(val);
            })
        }
    });
})
});

</script>
<?=$this->endSection();?>
<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
