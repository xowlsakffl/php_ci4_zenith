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
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Title</th>
                        <th scope="col">Body</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>

                    </tr>
                </tbody>
            </table>
            <div class="row">
                <?php //$pager->links() ?>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-end">
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Add Post
                </button>
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
        url: "<?=base_url()?>/api/user",
        dataType: "json",
        success: function (response) {
            console.log(response);
        },
        error: function(response){

        }
    });
}

});

</script>
<?=$this->endSection();?>