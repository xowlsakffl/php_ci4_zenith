$(function(){
    var btn_Array = [];
    var result = null;
    var last = btn_Array.at(-1);
    $('.left-side .btn-menu').on('click', function(){       
        $('.left-side .nav > li > button').attr('aria-expanded', false);
        $('.left-side .nav .collapse').removeClass('show');
        $('.left-side').toggleClass('active');
    });  

    $('.nav>li>button').on('click', function(){
        
        let allExpand = $('.nav>li>button[aria-expanded="true"]').length;
        let expand = $(this).attr('aria-expanded');
        if(expand == 'true' && allExpand == 1){
            $('.left-side').addClass('active');
        }

        if(expand == 'false' && !allExpand){
            $('.left-side').removeClass('active');
        }
    })

    /* $('.nav-wrap').on('click',function(e){       
        e.preventDefault();
        if(last == 'open' || last == undefined && $(window).width() <= 1024){
            $('.left-side').toggleClass('open');
        }   
        else if(last == 'hide' || last == undefined  ){
            $('.left-side').toggleClass('hide');
        }  
        $('.left-side').toggleClass('active');  
    }); */

    $('.btn-top .head').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0},500)
    })
    $('.btn-top .list').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:$('.dataTables_wrapper').offset().top},500)
    })
    $(document).ready(function() {
        setTimeout(function() {
            if(!$('.dataTables_wrapper').is(':visible')) {
                $('.btn-top .list').fadeOut();
            }
        },1000);
    })

    // $('.nav-wrap').on('mouseenter',function(){  
    //     if($('.left-side').hasClass('active') && $('.left-side').hasClass('hide')){
    //         $('.left-side').removeClass('hide');  
    //     }    
    // })   

    $('.nav-wrap').on(function(){  
        if($('.left-side').hasClass('active') && $('.left-side').hasClass('hide')){
            $('.left-side').removeClass('hide');  
        }  
        else if($('.left-side').hasClass('active') && $('.left-side').hasClass('open')){
            $('.left-side').removeClass('open');  
        }    
    })  

    $('.nav-wrap').on('mouseleave','touchend',function(){    
        if($('.left-side').hasClass('active') && $(window).width() > 1024){
            $('.left-side').addClass('hide');  
        }
        else if($('.left-side').hasClass('active') && $(window).width() <= 1024) {
            $('.left-side').addClass('open');  
        }  
    })
   
   

    $('.toggle').on('click', function(){
        $(this).toggleClass('folded');
    });

    $(window).resize(function(){
        var width = $(this).width();
        if(width <= 1024){
            $('.left-side .nav > li > button').attr('aria-expanded', false);
            $('.left-side .nav .collapse').removeClass('show');
        }
        $('.left-side').removeClass('open');
        $('.left-side').removeClass('hide');
        $('.left-side').removeClass('active');
    });

    $('.dataTables_info > i').on('click',function(){
        $('.txt-info').toggle();
    });
});