$(function(){
    $('.nav li button').on('click', function(){
        $('.left-side').addClass('open');  

        let width = window.innerWidth;
        let open = $('.left-side');     

        if(width<= 500 && open.hasClass('open')){
            $('.nav li button').on('click', function(){
                $('.left-side').toggleClass('open');   
            }           
        )}
    });    
});


