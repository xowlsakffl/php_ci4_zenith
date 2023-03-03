<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>

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
<div class="exam-container-wrap">
    <header class="d-flex justify-content-between align-items-center header-top container-fluid">
        <a href="#" class="button"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>Overview Page</a>
        <div class="d-flex device">
            <a href="#" class="button"><i class="fa fa-desktop" aria-hidden="true"></i></a>
            <a href="#" class="button"><i class="fa fa-tablet" aria-hidden="true"></i></a>
            <a href="#" class="button"><i class="fa fa-mobile" aria-hidden="true"></i></a>
        </div>
        <div class="d-flex align-items-center gnb-btn">
            <i class="fa fa-github" aria-hidden="true"></i>
            <a href="#" class="button" download><i class="fa fa-download" aria-hidden="true">Free Download</i></a>                
            <button class="close"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </header>  


    <nav class="container-fluid d-flex justify-content-between align-items-center header-bottom">
        <h6>Start Bootstrap</h6>  
        <ul class="nav-tabs d-flex align-items-center justify-content-between">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">Services</a></li>
        </ul>
    </nav>
    <main class="row container-fluid">
        <article class="con-01 flex-layout">
            <div class="col-lg-7 img">
                <img src="https://dummyimage.com/900x400/dee2e6/6c757d.jpg" class="img-fluid">              
            </div>  
            <div class="col-lg-5 article-card">
                <h1 class="fs-3">Business Name or Tagline</h1>
                <p class="fs-6">This is a template that is great for small businesses. It doesn't have too much fancy flare to it, but it makes a great use of the standard Bootstrap core components. Feel free to use this template for any project you want!</p>
                <button type="button" class="btn btn-primary">Call to Action!</button>
            </div>          
        </article>

        <article class="banner row container-fluid">
            <p class="fs-6 text-center">This call to action card is a great place to showcase some important information or display a clever tagline!</p>
        </article>
        
        <article class="con-02 container-fluid">
            <div class="row">
                <div class="col-md-4 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Card One</h2>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem magni quas ex numquam, maxime minus quam molestias corporis quod, ea minima accusamus.</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">More Info</a></div>
                    </div>
                </div> 
                <div class="col-md-4 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Card Two</h2>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem magni quas ex numquam, maxime minus quam molestias corporis quod, ea minima accusamus.</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">More Info</a></div>
                    </div>
                </div> 
                <div class="col-md-4 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Card Three</h2>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem magni quas ex numquam, maxime minus quam molestias corporis quod, ea minima accusamus.</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">More Info</a></div>
                    </div>
                </div> 
            </div>
        </article>        
    </main> 

    <footer>
        <div class="container pt-5 pb-5 bg-dark text-white text-center">
            <p>Copyright © Your Website 2023</p>
        </div>
    </footer>
</div>


<div class="features-container-wrap">
    <div class="container-fluid">        
        <main>
            <article>
                <div class="row nav_bar">
                    <h1>Columns with icons</h1>
                </div>
                <div class="row featured-card py-4">
                    <div class="col-md-4 card p-3">
                        <div class="icon"></div>
                        <h2>Featured title</h2>
                        <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                        <a href="#">Call to action <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                    </div>   
                    <div class="col-md-4 card p-3">
                        <div class="icon"></div>
                        <h2>Featured title</h2>
                        <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                        <a href="#">Call to action <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                    </div> 
                    <div class="col-md-4 card p-3">
                        <div class="icon"></div>
                        <h2>Featured title</h2>
                        <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                        <a href="#">Call to action <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                    </div>    
                </div>
            </article>
            <div class="row underline p-4"></div>
            <article>
                <div class="row nav_bar">
                    <h1>Hanging icons</h1>
                </div>
                <div class="row heading-card py-4">
                    <div class="col-md-4 card-containner p-3 flex-layout">
                        <div class="icon">
                            <i class="fa fa-toggle-on" aria-hidden="true"></i>
                        </div>
                        <div class="card px-2">     
                            <h2>Featured title</h2>
                            <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                            <button class="btn btn-primary btn-sm">Primary button</button>
                        </div>        
                    </div> 
                    <div class="col-md-4 card-containner p-3 flex-layout">
                        <div class="icon">
                            <i class="fa fa-address-book" aria-hidden="true"></i>
                        </div>
                        <div class="card px-2">     
                            <h2>Featured title</h2>
                            <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                            <button class="btn btn-primary btn-sm">Primary button</button>
                        </div>        
                    </div> 
                    <div class="col-md-4 card-containner p-3 flex-layout">
                        <div class="icon">
                            <i class="fa fa-recycle" aria-hidden="true"></i>
                        </div>
                        <div class="card px-2">     
                            <h2>Featured title</h2>
                            <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                            <button class="btn btn-primary btn-sm">Primary button</button>
                        </div>        
                    </div> 
                </div>
            </article>
            <div class="row underline p-4"></div>
            <article>
                <div class="row nav_bar">
                    <h1>Custom cards</h1>
                </div>
                <div class="row custom-card py-4">
                    <div class="col-md-4 card p-3">
                        <p>Short title, long jacket</p>                        
                    </div>  
                </div>
            </article>
        </main>
    </div>
</div>
<?=$this->endSection();?>
