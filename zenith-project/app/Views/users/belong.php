<?=$this->extend('templates/front.php');?>

<?=$this->section('content');?>
<div class="container-md">
    <h1 class="font-weight-bold mb-5">사용자 관리</h1>
    
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
$(document).ready(function(){

getUserList();
function getUserList(page, limit, search, sort, startDate, endDate){
    data = {
        'page': page ? page : 1,
        'limit': limit ? limit : 10,
        'search': search ? search : '',
        'sort': sort ? sort : 'recent',
        'startDate': startDate ? startDate : '',
        'endDate': endDate ? endDate : '',
    }
    console.log(data);
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/users",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(xhr){
            setTable(xhr);       
            setPaging(xhr);
            setAllCount(xhr);
            setDate(xhr);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

function setPaging(xhr){
    if(xhr.pager.pageCount == 0){
        xhr.pager.pageCount = 1;
    }
    $('.pagination').twbsPagination('destroy');
    $('.pagination').twbsPagination({
        totalPages: xhr.pager.pageCount,	// 총 페이지 번호 수
        visiblePages: 5,	// 하단에서 한번에 보여지는 페이지 번호 수
        startPage : xhr.pager.currentPage, // 시작시 표시되는 현재 페이지
        initiateStartPageClick: false,	// 플러그인이 시작시 페이지 버튼 클릭 여부 (default : true)
        first : "첫 페이지",	// 페이지네이션 버튼중 처음으로 돌아가는 버튼에 쓰여 있는 텍스트
        prev : "이전 페이지",	// 이전 페이지 버튼에 쓰여있는 텍스트
        next : "다음 페이지",	// 다음 페이지 버튼에 쓰여있는 텍스트
        last : "마지막 페이지",	// 페이지네이션 버튼중 마지막으로 가는 버튼에 쓰여있는 텍스트
        nextClass : "page-item next",	// 이전 페이지 CSS class
        prevClass : "page-item prev",	// 다음 페이지 CSS class
        lastClass : "page-item last",	// 마지막 페이지 CSS calss
        firstClass : "page-item first",	// 첫 페이지 CSS class
        pageClass : "page-item",	// 페이지 버튼의 CSS class
        activeClass : "active",	// 클릭된 페이지 버튼의 CSS class
        disabledClass : "disabled",	// 클릭 안된 페이지 버튼의 CSS class
        anchorClass : "page-link",	//버튼 안의 앵커에 대한 CSS class
        
        onPageClick: function (event, page) {
            console.log(xhr.pager.limit);
            getUserList(page, xhr.pager.limit, xhr.pager.search, xhr.pager.sort, xhr.pager.startDate, xhr.pager.endDate)
        }
    });
}

function setTable(xhr){
    $('#user tbody').empty();
    console.log(xhr);

    $.each(xhr.result, function(index, item){   
        index++
        $('<tr id="userView" data-id="'+item.id+'">').append('<td>'+item.id+'</td>')
        .append('<td>'+item.username+'</td>')
        .append('<td>'+item.groups+'</td>')
        .append('<td>'+item.created_at.date.substr(0, 16)+'</td>')
        .appendTo('#user'); 
    });
}

function setAllCount(xhr){
    console.log(xhr.pager.total);
    if(xhr.pager.total == 0){
        $total = 0;
    }else{
        $total = xhr.pager.total;
    }
    $('#allCount').text("총 "+$total+"개");
}

function setDate(xhr){
    if($('#fromDate, #toDate').length){
        var currentDate = moment().format("YYYY-MM-DD");
        $('#fromDate, #toDate').daterangepicker({
            locale: {
                    "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                    "applyLabel": "확인",                    // 확인 버튼 텍스트
                    "cancelLabel": "취소",                   // 취소 버튼 텍스트
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
            },
            "alwaysShowCalendars": true,                        // 시간 노출 여부
            showDropdowns: true,                     // 년월 수동 설정 여부
            autoApply: true,                         // 확인/취소 버튼 사용여부
            maxDate: new Date(),
            autoUpdateInput: false,
            ranges: {
                '오늘': [moment(), moment()],
                '어제': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '지난 일주일': [moment().subtract(6, 'days'), moment()],
                '지난 한달': [moment().subtract(29, 'days'), moment()],
                '이번달': [moment().startOf('month'), moment().endOf('month')],
            }
        }, function(start, end, label) {
            // console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
            // Lets update the fields manually this event fires on selection of range
            startDate = start.format('YYYY-MM-DD'); // selected start
            endDate = end.format('YYYY-MM-DD'); // selected end

            $checkinInput = $('#fromDate');
            $checkoutInput = $('#toDate');

            // Updating Fields with selected dates
            $checkinInput.val(startDate);
            $checkoutInput.val(endDate);

            // Setting the Selection of dates on calender on CHECKOUT FIELD (To get this it must be binded by Ids not Calss)
            var checkOutPicker = $checkoutInput.data('daterangepicker');
            checkOutPicker.setStartDate(startDate);
            checkOutPicker.setEndDate(endDate);

            // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
            var checkInPicker = $checkinInput.data('daterangepicker');
            checkInPicker.setStartDate($checkinInput.val(startDate));
            checkInPicker.setEndDate(endDate);
            
            getUserList(1, $('#pageLimit').val(), $('#search').val(), $('#sort').val(), startDate, endDate);
        });
    }
}

//페이지 게시글 갯수
$('body').on('change', '#pageLimit', function(){
    getUserList(
        1, 
        $(this).val(), 
        $('#search').val(), 
        $('#sort').val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

//검색
$('body').on('keyup', '#search', function(){
    getUserList(
        1, 
        $('#pageLimit').val(), 
        $(this).val(), 
        $('#sort').val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

//분류
$('body').on('change', '#sort', function(){
    getUserList(
        1, 
        $('#pageLimit').val(), 
        $('#search').val(), 
        $(this).val(),
        $('#fromDate').val(),
        $('#toDate').val(),
    );
})

$('#dateRange').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    getUserList(1, $('#pageLimit').val(), $('#search').val(), $('#sort').val());
});

//유저보기
$('body').on('click', '#userView', function(){
    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/users/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            console.log(data);
            $('#modalView .modal-body #viewName dd').html(data.result.username);
            $('#modalView .modal-body #viewCompany dd').html(data.result.companyType+" "+data.result.companyName);
            $('#modalView .modal-body #viewGroup dd').html(data.result.groups.join(", "));       
            $('#modalView .modal-body #viewDate dd').html(data.result.created_at.substr(0, 16));
            $('#modalView #userUpdateModal').attr('data-id', data.result.id);
            $('#modalView #userDelete').attr('data-id', data.result.id);
            var myModal = new bootstrap.Modal(document.getElementById('modalView'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//유저 수정
$('body').on('click', '#userUpdateModal', function(){
    $('#modalView').modal('hide');

    let id = $(this).attr('data-id');
    $.ajax({
        type: "get",
        url: "<?=base_url()?>/users/"+id,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){
            console.log(data.result.groups);
            $('#userGroup').val('');
            $('#modalUpdate #username').val(data.result.username);
            for (let i = 0; i < data.result.groups.length; i++) {
                $('#userGroup option[value="' + data.result.groups[i] + '"]').prop('selected', true);
            }      
            $('#modalUpdate #hidden_id').val(data.result.id);
            $('#modalUpdate #frm span').text(''); 
            var myModal = new bootstrap.Modal(document.getElementById('modalUpdate'))
            myModal.show()
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
})

//글수정
$('body').on('click', '#userUpdateBtn', function(){
    let id = $("#modalUpdate input:hidden[name=id]").val();
    data = {
        username: $('#modalUpdate input:text[name=username]').val(),
        groups: $('#modalUpdate #userGroup').val(),
    };
    console.log(data);
    $.ajax({
        type: "put",
        url: "<?=base_url()?>/users/"+id,
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        success: function(response){
            $('#modalUpdate').modal('hide');
            $('#modalUpdate').find('input').val('');  
            $('#modalUpdate #frm span').text(''); 
            getUserList();
            console.log(response);
        },
        error: function(error){
            var errorText = error.responseJSON.messages;
            $.each(errorText, function(key, val){
                $("#modalUpdate #" + key + "_error").text(val);
            })
        }
    });
})

//글삭제
$('body').on('click', '#userDelete', function(){
    let id = $(this).attr('data-id');
    if(confirm('정말 삭제하시겠습니까?')){
        $.ajax({
            type: "delete",
            url: "<?=base_url()?>/users/"+id,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){
                $('#modalView').modal('hide');
                getUserList();
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }
})

$('body').on('keyup', '#username', function(){
    $(this).siblings('span').text("");
});

$('body').on('change', '#userGroup', function(){
    $(this).siblings('span').text("");
});


});

</script>
<?=$this->endSection();?>
