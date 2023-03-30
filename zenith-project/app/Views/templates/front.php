<?=$this->include('templates/header.php')?>
<?=$this->renderSection('body')?>
<div class="app">  
    <div class="wrap d-flex">
        <?=$this->include('templates/inc/navbar.php')?>
        <?=$this->renderSection('content')?>
    </div>
    <?=$this->renderSection('modal')?>
</div>
<?=$this->renderSection('script')?>

<?=$this->include('templates/footer.php')?>
