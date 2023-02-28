<?=$this->include('templates/header.php')?>
<div class="app">
    <?=$this->include('templates/inc/navbar.php')?>
    <div class="container mb-5 mt-5">
        <?=$this->renderSection('content')?>
    </div>
</div>
<?=$this->renderSection('script')?>

<?=$this->include('templates/footer.php')?>

<?=$this->include('templates/exam.php')?>
<?=$this->include('templates/exam02.php')?>