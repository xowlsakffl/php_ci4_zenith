<?=$this->extend('templates/front.php');?>
<?=$this->section('content');?>
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
                test
            </div>
        </div>
    </div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){

getUserList();
function getUserList(){
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/users",
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: userList,
        error: function(response){

        }
    });
}

function userList(xhr){
    $('#userTable tbody').empty();
    $.each(xhr, function(index, item){
        $('<tr>').append('<td>'+index+'</td>')
        .append('<td>'+item.username+'</td>')
        .append('<td>'+item.status+'</td>')
        .append('<td>'+item.status+'</td>')
        .appendTo('#userTable');
    });
}

});

</script>
<?=$this->endSection();?>