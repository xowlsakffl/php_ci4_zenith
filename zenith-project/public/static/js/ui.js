$(function(){
    $('.btn-menu').on('click', function(){
        $('.left-side').toggleClass('open');
        $('.nav > li > button').attr('aria-expanded', false);
        $('.nav .collapse').removeClass('show');
    });
});