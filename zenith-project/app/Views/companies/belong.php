<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 광고주/광고대행사
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
    <h1 class="font-weight-bold mb-5">소속 변경</h1>
    <div class="row">
        <div class="col-6 m-auto">
            <div class="card">
                <div class="card-body">
                    <form id="frm">
                        <input type="hidden" name="cdx" id="hidden_id" value="<?=$company->cdx?>">
                        <div class="form-group mb-3">
                            현재 소속 : <span><?=$company->parent_company_name?></span>
                        </div>
                        <div class="form-group mb-3">
                            타입 : <span><?=$company->companyType?></span>
                        </div>
                        <div class="form-group mb-3">
                            이름 : <span><?=$company->companyName?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="agency">소속 선택</label>
                            <select name="agency" class="form-control" id="agency">
                                <?php foreach ($agencies as $agency) : ?>
                                    <option value="<?= $agency['cdx']?>"><?= $agency['companyName'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <span id="agency_error" class="text-danger"></span>
                        </div>
                    </form>
                    <div class="modal-footer">            
                        <button type="button" class="btn btn-primary" id="companyBelongUpdate">저장</button>
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

$('body').on('click', '#companyBelongUpdate', function(){
    data = {
        parent_cdx: $('#agency').val(),
        cdx: $("input:hidden[name=cdx]").val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/company/belong",
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

