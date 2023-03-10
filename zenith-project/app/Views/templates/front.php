<?=$this->include('templates/header.php')?>
<?=$this->renderSection('body')?>
<div class="app">
    <?=$this->include('templates/inc/navbar.php')?>
    <div>
        <?=$this->renderSection('content')?>
    </div>
</div>
<?=$this->renderSection('script')?>

<?=$this->include('templates/footer.php')?>
