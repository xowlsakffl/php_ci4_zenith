<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 자동화 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap eventmanage-container">

    <div>
        <form action="/"></form>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal')?>

<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
