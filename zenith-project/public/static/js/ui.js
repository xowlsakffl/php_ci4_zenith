$(function(){
    var btn_Array = [];
    var result = null;
    var last = btn_Array.at(-1);
    

    $('.left-side .btn-menu').on('click', function(){       
        $('.left-side .nav > li > button').attr('aria-expanded', false);
        $('.left-side .nav .collapse').removeClass('show');

        if($(window).width() <= 1024){
            $('.left-side').toggleClass('open');
            result = 'open';
        }else{
            $('.left-side').toggleClass('hide');
            result = 'hide';
        }
        $('.left-side').toggleClass('active');

        btn_Array.push(result);
    });  

   

    $('.nav-wrap').on('click',function(){       
        if(last == 'open' || last == undefined && $(window).width() <= 1024){
            $('.left-side').toggleClass('open');
        }   
        else if(last == 'hide' || last == undefined  ){
            $('.left-side').toggleClass('hide');
        }  
        $('.left-side').toggleClass('active');  
    });

    $('.btn-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0},500)
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