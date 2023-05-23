$(function(){
    $('.left-side .btn-menu').on('click', function(){
        $('.left-side').toggleClass('open');
        $('.left-side .nav > li > button').attr('aria-expanded', false);
        $('.left-side .nav .collapse').removeClass('show');
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

     //slide up 효과
    let account = document.querySelector('.sub-contents-wrap');
    let effect = account.querySelectorAll('.sub-contents-wrap > div');
    let e=0;
    let timer = setInterval(function(){
        effect[e].classList.add('up');     
        e++;
    
        if(e >= effect.length){
            clearInterval(timer); 
        }              
    },300); 
});