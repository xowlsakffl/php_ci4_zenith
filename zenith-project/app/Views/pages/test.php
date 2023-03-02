<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<h1 class="text-primary">테스트페이지</h1>
<div class="exam-wrap">
    <div class="container-fluid bg-dark text-white text-center h-100 d-flex flex-column justify-content-center">
        <main>
            <div class="bg-area">
                <h1>Cover your page.</h1>
                <p class="lead">This page is just a practice page.</p>
                <p>css는 _exam.linked to a scss file. _exam. in the main.scss fileThe scss file has been imported.</p>
            </div>               
        </main>
    </div>    
</div>
<?=$this->endSection();?>

<?=$this->section('content');?>
<div class="exam-container-wrap">
    <div class="row container-fluid">
        <header class="container-fluid d-flex justify-content-between align-items-center header-top">
            <a href="#" class="button"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>Overview Page</a>
            <div class="d-flex device">
                <a href="#" class="button"><i class="fa fa-desktop" aria-hidden="true"></i></a>
                <a href="#" class="button"><i class="fa fa-tablet" aria-hidden="true"></i></a>
                <a href="#" class="button"><i class="fa fa-mobile" aria-hidden="true"></i></a>
            </div>
            <div class="d-flex align-items-center gnb-btn">
                <i class="fa fa-github" aria-hidden="true"></i>        
                <a href="#" class="button"><i class="fa fa-download" aria-hidden="true">Free Download</i></a>                
                <button class="close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
        </header>        
    </div>  
    <div class="row container-fluid" style="display:none;">
        <header class="container-fluid d-flex justify-content-between align-items-center header-bottom">
            <h1>Start Bootstrap</h1>
            <ul class="gnb-btm">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">Services</a></li>
            </ul>
        </header>
    </div> 
</div>
<?=$this->endSection();?>