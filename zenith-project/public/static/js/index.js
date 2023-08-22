//원복
// $(function(){
//     $('.left-side .nav li button').on('click', function(){
//         $('.left-side').addClass('open');  

//         let width = window.innerWidth;
//         let open = $('.left-side');     

//         if(width<= 500 && open.hasClass('open')){
//             $('.left-side .nav li button').on('click', function(){
//                 $('.left-side').toggleClass('open');   
//             }           
//         )}
//     });    
// });


$(function(){
    $('.left-side .nav li button').on('click', function(){
        $('.left-side').toggleClass('hide');  

        let width = window.innerWidth;
        let open = $('.left-side');     

        if(width<= 500 && open.hasClass('hide')){
            $('.left-side .nav li button').on('click', function(){
                $('.left-side').toggleClass('hide');  
            } 
        )}
    });    
});


