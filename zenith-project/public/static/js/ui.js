$(function(){
    $('.left-side .btn-menu').on('click', function(){       
        $('.left-side .nav > li > button').attr('aria-expanded', false);
        $('.left-side .nav .collapse').removeClass('show');

        if($(window).width() <= 1024){
            $('.left-side').toggleClass('open');
        }else{
            $('.left-side').toggleClass('hide');
        }
    });  


    $('.toggle').on('click', function(){
        $(this).toggleClass('folded');
    });

    $(window).resize(function(){
        var width = $(this).width();
        if(width <= 1024){
            $('.left-side .nav > li > button').attr('aria-expanded', false);
            $('.left-side .nav .collapse').removeClass('show');
        }
    });

    $('.dataTables_info > i').on('click',function(){
        $('.txt-info').toggle();
    });
});