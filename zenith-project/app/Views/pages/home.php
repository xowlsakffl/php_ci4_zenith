<?=$this->extend('templates/front.php');?>

<?=$this->section('header');?>
<script>
    console.log('header')
</script>
<?=$this->endSection();?>

<?=$this->section('body');?>
testbody
<?=$this->endSection();?>


<?=$this->section('content');?>
<h1 class="text-primary">Zenith 홈</h1>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script></script>
<?=$this->endSection();?>

<?=$this->section('footer');?>
testfooter
<?=$this->endSection();?>