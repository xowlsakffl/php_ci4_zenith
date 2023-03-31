<?=$this->extend('templates/front_example.php');?>

<?=$this->section('content');?>
<div class="sub-contents-wrap container-user-management">
    <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">사용자 수정</h5>
            </div>
            <div class="modal-body">
                <form id="frm">
                    <input type="hidden" name="id" id="hidden_id">
                    <div class="form-group">
                        <label for="username">이름</label>
                        <input type="text" name="username" id="username" class="form-control">
                        <span id="username_error" class="text-danger"></span>
                    </div>
                    <?php if(auth()->user()->inGroup('superadmin', 'admin', 'developer')){?>              
                    <div class="form-group">
                        <label for="group">권한</label>
                        <select name="group" class="form-control" multiple="multiple" id="userGroup">
                            <option value="superadmin">최고관리자</option>
                            <option value="admin">관리자</option>
                            <option value="developer">개발자</option>
                            <option value="agency">광고대행사</option>
                            <option value="advertiser">광고주</option>
                            <option value="user">사용자</option>
                            <option value="guest">게스트</option>
                        </select>
                        <span id="groups_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="permission">세부 권한</label>
                        <select name="permission" class="form-control" multiple="multiple" id="userPermission">
                            <option value="admin.access">관리자만 가능한 페이지 접근 가능</option>
                            <option value="admin.settings">관리자만 가능한 설정 접근 가능</option>
                            <option value="users.create">회원 생성</option>
                            <option value="users.edit">회원 수정</option>
                            <option value="users.delete">회원 삭제</option>
                            <option value="agency.access">대행사 목록 페이지</option>
                            <option value="agency.advertisers">대행사 하위 광고주 관리</option>
                            <option value="agency.create">대행사 생성</option>
                            <option value="agency.edit">대행사 수정</option>
                            <option value="agency.delete">대행사 삭제</option>
                            <option value="advertiser.access">광고주 목록 페이지</option>
                            <option value="advertiser.create">광고주 생성</option>
                            <option value="advertiser.edit">광고주 수정</option>
                            <option value="advertiser.delete">광고주 삭제</option>
                        </select>
                        <span id="permission_error" class="text-danger"></span>
                    </div>
                    <?php }?>
                </form>
            </div>
            <div class="modal-footer">            
                <button type="button" class="btn btn-primary" id="userUpdateBtn">저장</button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal">
            <div class="modal-content">
                <div class="modal-header justify-content-start">
                    <h5 class="modal-title"><i class="bi bi-person-fill"></i>사용자</h5>
                </div>         

                <div class="modal-body">
                    <table class="table">                  
                        <thead>
                            <tr>
                                <th scope="col">이름</th>
                                <th>소속</th>
                                <th>권한</th>
                                <th>가입일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span id="viewName"></span></td></td>
                                <td><span id="viewCompany"></span></td>
                                <td><span id="viewGroup"></span></td>
                                <td><span id="viewDate"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">     
                    <?php if(auth()->user()->inGroup('superadmin', 'admin', 'developer')){
                        echo '<a href="/users/belong" class="btn btn-primary" id="userBelong">소속 수정</a>';
                    }?>
                    <button type="button" class="btn btn-primary" id="userUpdateModal">수정</button>
                    <button type="button" class="btn btn-danger" id="userDelete">삭제</button>
                </div>
            </div>
        </div>
    </div>

    <div class="title-area">
        <h2 class="page-title">사용자 관리</h2>
    </div>
    
    <div class="search-wrap"> 
        <form class="search d-flex justify-content-center">
            <div class="term d-flex align-items-center">
                <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="날짜 선택" readonly="readonly">

                <button type="button"><i class="bi bi-calendar2-week"></i></button>
                <span> ~ </span>

                <input type="text" class="form-control" id="toDate" name="toDate" placeholder="날짜 선택" readonly="readonly">
                <button type="button"><i class="bi bi-calendar2-week"></i></button>
            </div>
            <div class="input">
                <input type="text" class="form-control" id="search" name="search" placeholder="검색어를 입력하세요">
                <button class="btn-primary" type="submit">조회</button>
            </div>
        </form> 
    
        <div class="row d-flex justify-space-between filter-btn-wrap">    
            <dl class="col">             
                <dt>
                    <select name="sort" id="sort" class="form-control text-center">
                        <option value="정렬">정렬</option>
                        <option value="recent">최근순</option>
                        <option value="old">오래된 순</option>
                    </select>
                </dt>
            </dl>
            <dl class="col">  
                <dt>
                    <select name="pageLimit" id="pageLimit" class="form-control text-center">
                        <option value="게시물수">게시물수</option>
                        <option value="10">10개</option>
                        <option value="50">50개</option>
                        <option value="100">100개</option>
                    </select>
                </dt>
            </dl>
            <div class="col">
                <button id="DataResetBtn" class="btn btn-reset">초기화</button>
            </div>
        </div>
    </div>

    <div class="client-list"><h3><i class="bi bi-chevron-down"></i> 검색결과:<span id="allCount"></span></h3></div> 

    <div class="table-wrap">
        <div class="table-content">        
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="user">
                    <colgroup>
                        <col style="width:10%">
                        <col style="width:*">
                        <col style="width:*">
                        <col style="width:*">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">번호</th>
                            <th scope="col">이름</th>
                            <th scope="col">권한</th>
                            <th scope="col">가입일</th>
                        </tr>
                    </thead>
                    <tbody>  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row pagination-container m-4">
        <ul class="pagination justify-content-center"></ul>            
    </div>    
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
    var alertMessage = "<?php echo session('message'); ?>";
    if(alertMessage) {
        alert(alertMessage);
    }

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
            item.groups = item.groups.map(function(group) {
                group = group.replace('superadmin', '최고관리자');
                group = group.replace('admin', '관리자');
                group = group.replace('developer', '개발자');
                group = group.replace('user', '사용자');
                group = group.replace('agency', '광고대행사');
                group = group.replace('advertiser', '광고주');
                group = group.replace('guest', '게스트');
                return group;
            });
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
            console.log(`이것은`, $('#sort').val())
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
                data.result.groups = data.result.groups.map(function(group) {
                    group = group.replace('superadmin', '최고관리자');
                    group = group.replace('admin', '관리자');
                    group = group.replace('developer', '개발자');
                    group = group.replace('user', '사용자');
                    group = group.replace('agency', '광고대행사');
                    group = group.replace('advertiser', '광고주');
                    group = group.replace('guest', '게스트');
                    return group;
                });
                $('#modalView .modal-body #viewName').html(data.result.username);
                $('#modalView .modal-body #viewCompany').html(data.result.companyType+" "+data.result.companyName);
                $('#modalView .modal-body #viewGroup').html(data.result.groups.join(","));       
                $('#modalView .modal-body #viewDate').html(data.result.created_at.substr(0, 16));
                $('#modalView #userBelong').attr('href', '/user/belong/'+data.result.id);
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
                for (let i = 0; i < data.result.permission.length; i++) {
                    $('#userPermission option[value="' + data.result.permission[i] + '"]').prop('selected', true);
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
            permission: $('#modalUpdate #userPermission').val()
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
    //reset 버튼
    $('body').on('click', '#DataResetBtn', function(){
        $('#sort option:first').prop('selected',true);
        $('#pageLimit option:first').prop('selected',true);
        $('#fromDate').val('');
        $('#toDate').val('');
        $('#search').val('');
        getUserList();
    })


    });

</script>
<?=$this->endSection();?>
