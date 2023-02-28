<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<div class="container-md">
    <h1 class="font-weight-bold mb-5">사용자 소속 변경</h1>
    <div class="row">
        <div class="col-6 m-auto">
            <div class="card">
                <div class="card-body">
                    <form id="frm">
                        <input type="hidden" name="id" id="hidden_id" value="<?=$user->id?>">
                        <div class="form-group">
                            아이디 : <span><?=$user->username?></span>
                        </div>
                        <div class="form-group">
                            현재 소속 : <span><?=$user->companyName?></span>
                        </div>
                        <div class="form-group">
                            <label for="company">소속</label>
                            <select name="company" class="form-control" id="userCompany"></select>
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
getCompanyList();
function getCompanyList(){
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/user/belong/companies",
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            addOption(data);
        }
    });
}

function addOption(data){
    const select = $('#userCompany');
    const currentData = <?=$user->id?>;
    $.each(data.companies, function(index, option) {
        select.append($('<option>', {
            value: option.cdx,
            text: option.companyName
        }));
    });

    $('#userCompany').val(currentData);
}

$('body').on('click', '#userCompanyUpdate', function(){
    data = {
        company_id: $('#userCompany').val(),
        user_id: $("input:hidden[name=id]").val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/user/belong/companies",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(data){
            console.log(data)
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
