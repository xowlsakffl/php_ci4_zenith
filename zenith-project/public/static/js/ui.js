$(function(){
    $('.btn-menu').on('click', function(){
        $('.left-side').toggleClass('open');
        $('.nav > li > button').attr('aria-expanded', false);
        $('.nav .collapse').removeClass('show');
    });
    $('.toggle').on('click', function(){
        $(this).toggleClass('folded');
    });

    $(window).resize(function(){
        var width = $(this).width();
        if(width <= 1024){
            $('.nav > li > button').attr('aria-expanded', false);
            $('.nav .collapse').removeClass('show');
        }
    });
});