$(function(){
    //원본
    // $('.left-side .btn-menu').on('click', function(){
    //     $('.left-side').toggleClass('open');
    //     $('.left-side .nav > li > button').attr('aria-expanded', false);
    //     $('.left-side .nav .collapse').removeClass('show');
    // });

    let sel = null;
    let result = null;
    $('.left-side .btn-menu').on('click', function(){
        $('.left-side .nav > li > button').attr('aria-expanded', false);
        $('.left-side .nav .collapse').removeClass('show');

        menu_class();

        if(localStorage.btn != 'open' || sel == null){
            $('.left-side').removeClass('hide');
          
        }else{
            $('.left-side').addClass('hide');
        }
    });

    $('.left-side .nav li button').on('click', function(){
        let width = window.innerWidth;
        let open = $('.left-side');   
    }); 
   
    $(function(){
       // localStorage.clear();
        console.log(localStorage.btn,result);
        
        if(localStorage.btn == 'open'){
            $('.left-side').addClass('hide');  
        }else{
            $('.left-side').removeClass('hide');  
        }

        console.log(localStorage.btn,result);
    });

    function menu_class(){
        localStorage.setItem("btn", sel);
       // $('.left-side').toggleClass('hide');

        if( $('.left-side').hasClass('hide')){
            sel = 'hide';
        }
        else{
            sel = 'open';
        }
        result = localStorage.getItem("btn");
        console.log(localStorage.btn,result);
        //alert(sel);
    }


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

     //slide up 효과
    // let account = document.querySelector('.sub-contents-wrap');
    // let effect = account.querySelectorAll('.sub-contents-wrap > div');
    // let e=0;
    // let timer = setInterval(function(){
    //     effect[e].classList.add('up');     
    //     e++;
    
    //     if(e >= effect.length){
    //         clearInterval(timer); 
    //     }              
    // },300); 
});