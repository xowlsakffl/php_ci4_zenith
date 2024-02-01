function setCriteriaTime(){
    let now = new Date();
    now.setMinutes(now.getMinutes() + 5);
    $('input[name=criteria_time]').daterangepicker({
        singleDatePicker: true,
        locale: {
                "format": 'YYYY-MM-DD HH:mm',     // 일시 노출 포맷
                "applyLabel": "확인",                    // 확인 버튼 텍스트
                "cancelLabel": "취소",                   // 취소 버튼 텍스트
                "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
        },
        timePicker: true,
        timePicker24Hour: true,
        minDate: now,
    });
}
function chkSchedule(){
    let selectedValue = $('#execType').val();
    $('#weekdayRow, #nextDateRow, #timeRow').show();

    if (selectedValue === "minute" || selectedValue === "hour") {
        $('#criteriaTimeRow').show();
        $('#weekdayRow, #nextDateRow, #timeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
        $('#timeRow select').val('');
    } else if (selectedValue === "day") {
        $('#weekdayRow, #nextDateRow').hide();
        $('#criteriaTimeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
        $('#nextDateRow select').val('');
    } else if (selectedValue === "week") {
        $('#nextDateRow').hide();
        $('#criteriaTimeRow').hide();
        $('#nextDateRow select').val('');
    } else if (selectedValue === "month"){
        $('#weekdayRow').hide();
        $('#criteriaTimeRow').hide();
        $('input[name=exec_week]').prop('checked', false);
    }
}

function chkScheduleMonthType(){
    let month_type = $('#monthType').val();
    $('#nextDateRow select').show();
    if(month_type == 'start_day' || month_type == 'end_day' ){
        $('#nextDateRow select[name=month_day], #nextDateRow select[name=month_week]').hide();
        $('#monthDay, #monthWeek').val('');
    }else if(month_type == 'first' || month_type == 'last'){
        $('#nextDateRow select[name=month_day]').hide();
        $('#monthDay').val('');
    }else if (month_type == 'day'){
        $('#nextDateRow select[name=month_week]').hide();
        $('#monthWeek').val('');
    }
}

//좌측 탭 일정 텍스트
function scheduleText(){
    let type_value = $('input[name="type_value"]').val();
    let exec_type = $('#execType').val();
    let exec_week = $('input[name="exec_week"]:checked').siblings('label').text();
    let month_type = $('#monthType').val();
    let month_day = $('#monthDay').val();
    let month_week = $('#monthWeek option:selected').text();
    let exec_time = $('#execTime').val();
    let scheduleTextParts= [];
    if(type_value) {
        switch(exec_type){
            case "minute":
                scheduleTextParts.push("매 "+type_value+"분 마다");
                break;
            case "hour":
                scheduleTextParts.push("매 "+type_value+"시간 마다");
                break;
            case "day":
                let dayTextPart_1 = type_value == 1 ? '매일 ' : '매 '+type_value+'일 마다 ';
                let dayTextPart_2 = exec_time ? exec_time+'에' : '';
                scheduleTextParts.push(dayTextPart_1+dayTextPart_2);
                break;
            case "week":
                let weekTextPart_1 = type_value == 1 ? '매주 ' : '매 '+type_value+'주 ';
                let weekTextPart_2 = exec_week ? exec_week+"요일 마다 " : '';
                let weekTextPart_3 = exec_time ? exec_time+'에' : '';
                if(exec_week){
                    scheduleTextParts.push(weekTextPart_1+weekTextPart_2+weekTextPart_3);
                }
                break;
            case "month":
                monthTextPart_1 = type_value == 1 ? '매월 ' : type_value+'달 마다 ';
                monthTextPart_3 = '';
                switch (month_type) {
                    case 'start_day':
                        monthTextPart_2 = '첫번째 날 ';
                        break;
                    case 'end_day':
                        monthTextPart_2 = '마지막 날 ';
                        break;
                    case 'first':
                        monthTextPart_2 = '처음 ';                  
                        if(month_week && month_week != '선택'){                          
                            monthTextPart_3 = month_week ? month_week+"요일 마다 " : '';
                        }
                        break;
                    case 'last':
                        monthTextPart_2 = '마지막 ';
                        if(month_week && month_week != '선택'){                          
                            monthTextPart_3 = month_week ? month_week+"요일 마다 " : '';
                        }
                        break;
                    case 'day':
                        monthTextPart_2 = month_day ? month_day+'일째 ' : '';
                        break;
                    default:
                        monthTextPart_2 = '';
                        break;
                }
                monthTextPart_4 = exec_time ? exec_time+'에' : '';

                scheduleTextParts.push(monthTextPart_1+monthTextPart_2+monthTextPart_3+monthTextPart_4);
                break;
            default:
                break;
        }
    }
    $("#scheduleText").html(scheduleTextParts.join(", "));
} 

//좌측 탭 조건 텍스트
function conditionText($this){
    let name = $this.attr('name');
    let trId = $this.closest('tr').attr('id');

    if(name == 'type'){
        value = $this.find('option:selected').text()+" - ";
        $("#text-"+trId+" .typeText").html(value);
    }
    
    if(name == 'type_value_status'){
        $("#text-"+trId+" .typeValueText").text('');
        value = $this.find('option:selected').text();
        $("#text-"+trId+" .typeValueText").html(value);
    }

    if(name == 'type_value'){
        $("#text-"+trId+" .typeValueText").text('');
        value = $this.val();
        $("#text-"+trId+" .typeValueText").html(value);
    }

    if(name == 'compare'){
        $("#text-"+trId+" .compareText").text('');
        value = $this.find('option:selected').text();
        $("#text-"+trId+" .compareText").html(value);
    }
}

function addConditionRow(uniqueId){
    var row = `
        <tr id="${uniqueId}">
        <td><div class="form-flex"><select name="type" class="form-select conditionType"><option value="">조건 항목</option><option value="status">상태</option><option value="budget">예산</option><option value="dbcost">DB단가</option><option value="unique_total">유효DB</option><option value="spend">지출액</option><option value="margin">수익</option><option value="margin_rate">수익률</option><option value="sale">매출액</option><option value="conversion">DB전환률</option></select><select name="type_value_status" class="form-select conditionTypeValueStatus" style="display: none;"><option value="">상태값 선택</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값" oninput="onlyNumberLeadingDashAndDot(this);"></div></td><td colspan="2"><div class="form-flex"><select name="compare" class="form-select conditionCompare"><option value="">일치여부</option><option value="greater">초과</option><option value="greater_equal">보다 크거나 같음</option><option value="less">미만</option><option value="less_equal">보다 작거나 같음</option><option value="equal">같음</option><option value="not_equal">같지않음</option></select><button class="deleteBtn" style="width:20px;flex:0"><i class="fa fa-times"></i></button></div></td>
        </tr>`; 
    var rowText = `<p id="text-${uniqueId}"><span class="typeText"></span><span class="typeValueText"></span><span class="compareText"></span></p>`;
    $('#conditionTable tbody').append(row);
    $('#condition-tab').append(rowText);
}

function getTargetAdvs(targetSearchData){
    targetTable = $('#targetTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "/automation/adv",
            "data": targetSearchData,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columnDefs": [
            { targets: [5], orderable: false},
        ],
        "columns": [
            { "data": "media", "width": "10%"},
            { "data": "type", "width": "10%"},
            { "data": "id", "width": "30%"},
            { "data": "name", "width": "35%"},
            { "data": "status", "width": "8%",},
            { 
                "data": null, 
                "width": "7%",
                "render": function(){
                    let button = '<button class="target-btn">적용</button>';
                    return button;
                }
            },
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.type+"_"+data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
        "drawCallback": function(settings) {
            if($('#targetCheckedTable tbody tr').length > 0){
                $selectedTargetRow = $('#targetCheckedTable tbody tr').data('id');
                $('#targetTable tbody tr[data-id="'+$selectedTargetRow+'"]').addClass('selected')
            }
        }
    });
}

function getExecAdvs(data){
    execTable = $('#execTable').DataTable({
        "destroy": true,
        "autoWidth": true,
        "processing" : true,
        "serverSide" : true,
        "responsive": true,
        "searching": false,
        "ordering": true,
        "deferRender": false,
        'lengthChange': false,
        'pageLength': 10,
        "info": false,
        "ajax": {
            "url": "/automation/adv",
            "data": data,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
            "dataSrc": function(res){
                return res.data;
            }
        },
        "columns": [
            { "data": "media", "width": "10%"},
            { "data": "type", "width": "10%"},
            { "data": "id", "width": "30%"},
            { "data": "name", "width": "40%"},
            { "data": "status", "width": "10%"},
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr("data-id", data.media+"_"+data.type+"_"+data.id);
        },
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ko.json',
        },
    });
}

//유효성 검사
function validationData(){
    let $type_value = $('#scheduleTable input[name=type_value]').val();
    let $criteria_time = $('#scheduleTable input[name=criteria_time]').val();
    let $exec_type = $('#scheduleTable select[name=exec_type]').val();
    let $exec_time = $('#scheduleTable select[name=exec_time]').val();
    let $exec_week = $('#scheduleTable input[name=exec_week]:checked').length;
    let $month_type = $('#scheduleTable select[name=month_type]').val();
    let $month_week = $('#scheduleTable select[name=month_week]').val();
    let $month_day = $('#scheduleTable select[name=month_day]').val();

    let $operation = $('input[name=operation]:checked').length;
    let $selectTarget = $('#targetSelectTable tbody tr').length;
    let $selectExec = $('#execSelectTable tbody tr').length;
    let $target_create_type = $('input[name=target_create_type]:checked').val();
    let $subject = $('#detailTable input[name=subject]').val();
    let $slack_webhook = $('#slackSendTable input[name="slack_webhook"]').val();
    let $slack_msg = $('#slackSendTable input[name="slack_msg"]').val();

    if (!$type_value) {
        alert('시간 조건값을 입력해주세요');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable input[name=type_value]').focus();
        return false;
    }

    if (($exec_type === 'minute' || $exec_type === 'hour') && !$criteria_time) {
        alert('시작 일시를 입력해주세요.');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable input[name=criteria_time]').focus();
        return false;
    }

    if ($exec_type === 'day' && !$exec_time) {
        alert('시간을 선택해주세요.');
        $('#schedule-tab').trigger('click');
        $('#scheduleTable select[name=exec_time]').focus();
        return false;
    }

    if ($exec_type === 'week') {
        if(!$exec_week > 0){
            alert('요일을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable input[name=exec_week]').focus();
            return false;
        }

        if(!$exec_time){
            alert('시간을 입력해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=exec_time]').focus();
            return false;
        }
    }

    if ($exec_type === 'month') {
        if(!$month_type){
            alert('월 조건값을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_type]').focus();
            return false;
        }

        if(($month_type === 'first' || $month_type === 'last') && !$month_week){
            alert('요일을 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_week]').focus();
            return false;
        }

        if($month_type === 'day' && !$month_day){
            alert('일자를 선택해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=month_day]').focus();
            return false;
        }

        if(!$exec_time){
            alert('시간을 입력해주세요.');
            $('#schedule-tab').trigger('click');
            $('#scheduleTable select[name=exec_time]').focus();
            return false;
        }
    }
    //대상 선택항목이 있을경우
    if($selectTarget > 0){
        if(!$operation){
            alert('일치조건을 선택해주세요.');
            $('#condition-tab').trigger('click');
            $('input[name=operation]').focus();
            return false;
        }

        var eachValid = true;
        $('tr[id^="condition-"]').each(function() {
            var $row = $(this);
            var $conditionType = $row.find('select[name=type]').val();
            var $conditionTypeValueStatus = $row.find('select[name=type_value_status]').val();
            var $conditionTypeValue = $row.find('input[name=type_value]').val();
            var $conditionCompare = $row.find('select[name=compare]').val();

            if(!$conditionType){
                alert('조건항목을 선택해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('select[name=type]').focus();
                eachValid = false;
                return false;
            }

            if($conditionType == 'status'){
                if(!$conditionTypeValueStatus){
                    alert('상태값을 선택해주세요.');
                    $('#condition-tab').trigger('click');
                    $row.find('select[name=type_value_status]').focus();
                    eachValid = false;
                    return false;
                }
            }else{
                if(!$conditionTypeValue){
                    alert('조건값을 입력해주세요.');
                    $('#condition-tab').trigger('click');
                    $row.find('input[name=type_value]').focus();
                    eachValid = false;
                    return false;
                }
            }

            if(!$conditionCompare){
                alert('일치여부를 선택해주세요.');
                $('#condition-tab').trigger('click');
                $row.find('select[name=compare]').focus();
                eachValid = false;
                return false;
            }
        });

        if(!eachValid){
            return false;
        }
    }else{
        var hasValue = false;
        $('tr[id^="condition-"]').find('input, select').each(function() {
            if($(this).val()){
                hasValue = true;
                return false;
            }
        });

        if(hasValue){
            alert('조건을 설정하기 위해서 대상을 적용해주세요.');
            $('#condition-tab').trigger('click');
            return false;
        }
    }

    if(!$selectExec > 0){
        alert('실행항목을 추가해주세요.');
        $('#preactice-tab').trigger('click');
        $('#showExecAdv').focus();
        return false;
    }

    var execCheck = true;
    var execCheckMsg = '';
    var focusTarget = null;
    $('#execSelectTable tbody tr').each(function() {
        let exec_order = $(this).find('td:first input[name=exec_order]');
        let exec_condition_type = $(this).find('select[name=exec_condition_type]');
        let exec_condition_type_budget = $(this).find('select[name=exec_condition_type_budget]');
        let exec_condition_value = '';
        if(exec_order.val() == '') {      
            execCheck = false;
            execCheckMsg = '순서를 입력해주세요.';
            focusTarget = exec_order;
            return false;
        }

        if(exec_condition_type.val() == 'status'){
            exec_condition_value_input = $(this).find('select[name=exec_condition_value_status]');
            exec_condition_value = exec_condition_value_input.val();
        }else if(exec_condition_type.val() == 'budget'){
            exec_condition_value_input = $(this).find('input[name=exec_condition_value]');
            exec_condition_value = exec_condition_value_input.val();
        }else{
            execCheck = false;
            execCheckMsg = '실행항목을 선택해주세요.';
            focusTarget = exec_condition_type;
            return false;
        }

        if(exec_condition_value == '') {      
            execCheck = false;
            execCheckMsg = '세부항목을 입력해주세요.';
            focusTarget = exec_condition_value_input;
            return false;
        }

        if(exec_condition_type.val() == 'budget'){
            if(exec_condition_type_budget.val() == ''){
                execCheck = false;
                execCheckMsg = '단위를 선택해주세요.';
                focusTarget = exec_condition_type_budget;
                return false;
            }
        }
    });

    if(!execCheck){
        $('#preactice-tab').trigger('click');
        alert(execCheckMsg);
        focusTarget.focus();
        return false;
    }

    if($target_create_type == 'target_seperate'){
        let isMatchingRows = true;
        let targetRows = $('#targetSelectTable tbody tr');
        let execRows = $('#execSelectTable tbody tr');

        if(targetRows.length === 0 || execRows.length === 0) {
            $('#preactice-tab').trigger('click');
            alert('대상 또는 실행 항목이 존재하지 않습니다.');
            return false;
        }
        
        if(targetRows.length !== execRows.length) {
            $('#preactice-tab').trigger('click');
            alert('개별 적용의 경우 대상과 실행 테이블의 모든 항목이 동일해야 합니다.');
            return false;
        }

        targetRows.each(function() {
            let targetId = $(this).find('td:eq(2)').text();
            let correspondingExecRow = execRows.filter(function() {
                return $(this).find('td:eq(3)').text() === targetId;
            });
    
            if(correspondingExecRow.length === 0) {
                isMatchingRows = false;
                return false;
            }
        });

        if (!isMatchingRows) {
            $('#preactice-tab').trigger('click');
            alert('개별 적용의 경우 대상과 실행의 모든 항목이 동일해야합니다.');
            return false;
        }
    }

    if (($slack_webhook && !$slack_msg) || (!$slack_webhook && $slack_msg)) {     
        alert('웹훅 URL과 메세지 둘 다 입력해주세요.');
        $('#preactice-tab').trigger('click');
        if(!$('#slackSendTable input[name="slack_webhook"]').val()){
            $('#slackSendTable input[name="slack_webhook"]').focus();
        } else if(!$('#slackSendTable input[name="slack_msg"]').val()){
            $('#slackSendTable input[name="slack_msg"]').focus();
        }
        return false;
    }

    if(!$subject){
        alert('제목을 추가해주세요.');
        $('#detailTable input[name=subject]').focus();
        return false;
    }

    return true;
}

function onlyNumber(inputElement) {
    inputElement.value = inputElement.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
}

function onlyNumberLeadingDashAndDot(inputElement) {
    inputElement.value = inputElement.value.replace(/[^0-9.-]/g, '');
}

//모달 수정 세팅
function setModalData(data){
    $('#createAutomationBtn').hide();
    $('#updateAutomationBtn').show();
    $('#automationModal input[name=seq]').val(data.aa_seq);
    $('#scheduleTable select[name=exec_type]').val(data.aas_exec_type);
    $('#scheduleTable input[name=type_value]').val(data.aas_type_value);
    if(data.aas_criteria_time){
        $('#scheduleTable input[name=criteria_time]').val(data.aas_criteria_time);
    }else{
        $('#scheduleTable input[name=criteria_time]').val('');
    }
    if(data.aas_exec_week){
        $('#scheduleTable input[name=exec_week][value="' + data.aas_exec_week + '"]').prop('checked', true);
    }
    if(data.aas_month_type){
        $('#scheduleTable select[name=month_type]').val(data.aas_month_type);
    }
    if(data.aas_month_day){
        $('#scheduleTable select[name=month_day]').val(data.aas_month_day);
    }
    if(data.aas_month_week){
        $('#scheduleTable select[name=month_week]').val(data.aas_month_week);
    }
    if(data.aas_exec_time){
        $('#scheduleTable select[name=exec_time]').val(data.aas_exec_time);
    }
    if(data.aas_ignore_start_time){
        $('#scheduleTable select[name=ignore_start_time]').val(data.aas_ignore_start_time);
    }
    if(data.aas_ignore_end_time){
        $('#scheduleTable select[name=ignore_end_time]').val(data.aas_ignore_end_time);
    }

    if(data.targets){
        data.targets.forEach(function(target, index) {
            let targetIndex = index+1;
            let targetData = '<tr data-id="'+target.media+"_"+target.type+"_"+target.id+'" id="target-'+targetIndex+'"><td>' + target.media + '</td><td>' + target.type + '</td><td>' 
        + target.id + '</td><td>' + target.name + '</td><td>'
        + target.status  +'<button class="set_target_except_btn"><i class="fa fa-times"></i></button></td></tr>';
            let newTargetText = '<p id="text-target-'+targetIndex+'">* '+target.type+' - '+target.media+'<br>'+target.name+'</p>';
            $('#targetSelectTable tbody').append(targetData);
            $('#target-tab').append(newTargetText);
        });
    }

    //조건
    if (data.conditions) {
        data.conditions.forEach(function(condition, index) {
            if(index === 0) {
                if(condition.operation == 'and'){
                    $('input[type="radio"][value="and"]').prop('checked', true);
                }else{
                    $('input[type="radio"][value="or"]').prop('checked', true);
                }
                $('#condition-1 .conditionOrder').val(condition.order);
                $('#condition-1 .conditionOrder').val(condition.order);
                $('#condition-1 .conditionType').val(condition.type);
                if(condition.type == 'status'){
                    $('#condition-1 .conditionTypeValue').hide();
                    $('#condition-1 .conditionTypeValueStatus').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValueStatus option:selected').text();
                }else{
                    $('#condition-1 .conditionTypeValueStatus').hide();
                    $('#condition-1 .conditionTypeValue').val(condition.type_value).show();
                    var conditionTypeValueText = $('#condition-1 .conditionTypeValue').val();
                }
                
                $('#condition-1 .conditionTypeValue').val(condition.type_value);
                var conditionTypeValueText = $('#condition-1 .conditionTypeValue').val();

                $('#condition-1 .conditionCompare').val(condition.compare);
                $("#text-condition-1 .typeText").html($('#condition-1 .conditionType option:selected').text());
                $("#text-condition-1 .typeValueText").html(conditionTypeValueText);
                $("#text-condition-1 .compareText").html($('#condition-1 .conditionCompare option:selected').text());
            } else { // 그 외의 항목일 경우
                var uniqueId = 'condition-' + (index + 1);
                addConditionRow(uniqueId);
                //$(`#${uniqueId} .conditionOrder`).val(condition.order);
                $(`#${uniqueId} .conditionType`).val(condition.type);
                if(condition.type == 'status'){
                    $(`#${uniqueId} .conditionTypeValue`).hide();
                    $(`#${uniqueId} .conditionTypeValueStatus`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValueStatus option:selected").text();
                }else{
                    $(`#${uniqueId} .conditionTypeValueStatus`).hide();
                    $(`#${uniqueId} .conditionTypeValue`).val(condition.type_value).show();
                    var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValue").val();
                }
                $(`#${uniqueId} .conditionTypeValue`).val(condition.type_value);
                var conditionTypeValueText = $("#"+uniqueId+" .conditionTypeValue").val();

                $(`#${uniqueId} .conditionCompare`).val(condition.compare);
                $("#text-"+uniqueId+" .typeText").html($("#"+uniqueId+" .conditionType option:selected").text());
                $("#text-"+uniqueId+" .typeValueText").html(conditionTypeValueText);
                $("#text-"+uniqueId+" .compareText").html($("#"+uniqueId+" .conditionCompare option:selected").text());
            }
        });
    }
    //실행
    if (data.executions && Array.isArray(data.executions)) {
        data.executions.forEach(function(execution, index) {
            var execIndex = index+1;
            var executionData = $('<tr data-id="'+execution.media+"_"+execution.type+"_"+execution.id+'" id="exec-'+execIndex+'"><td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2" value="'+execution.order+'"></td><td>' +execution.media+ '</td><td>'+execution.type+'</td><td>'+execution.id+'</td><td>'+execution.name+'</td><td><div class="form-flex"><select name="exec_condition_type" class="form-select"><option value="">실행항목</option><option value="status">상태</option><option value="budget">예산</option></select></td><td><select name="exec_condition_value_status" class="form-select"><option value="">상태값</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="exec_condition_value" class="form-control"placeholder="예산"></td><td><select name="exec_condition_type_budget" class="form-select"><option value="">단위</option><option value="won">원</option><option value="percent">%</option></select></div><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td></tr>');

            if(execution.exec_type == 'status'){
                executionData.find('input[name="exec_condition_value"]').hide();
                executionData.find('select[name="exec_condition_type_budget"]').hide();
                executionData.find('select[name="exec_condition_type"]').val(execution.exec_type);
                executionData.find('select[name="exec_condition_value_status"]').val(execution.exec_value).show();
            }else{
                executionData.find('select[name="exec_condition_value_status"]').hide();
                executionData.find('select[name="exec_condition_type"]').val(execution.exec_type);
                executionData.find('input[name="exec_condition_value"]').val(execution.exec_value).show();
                executionData.find('select[name="exec_condition_type_budget"]').val(execution.exec_budget_type).show();
            }
            
            $('#execSelectTable tbody').append(executionData);

            var newExecText = '<p id="text-exec-'+execIndex+'">* '+execution.type+' - '+execution.media+'<br>'+execution.name+'</p>';
            $('#preactice-tab').append(newExecText);
        });
    }

    if(data.aa_slack_webhook){
        $('#slackSendTable input[name=slack_webhook]').val(data.aa_slack_webhook); 
    }

    if(data.aa_slack_msg){
        $('#slackSendTable input[name=slack_msg]').val(data.aa_slack_msg); 
    }

    $('#detailTable input[name=subject]').val(data.aa_subject);
    if(data.aa_description){
        $('#detailTable textarea[name=description]').val(data.aa_description); 
    }

    $('#detailText #subjectText').text(data.aa_subject);
    $('#detailText #descriptionText').text(data.aa_description);

    conditionStatusHide();
    chkSchedule();
    if(data.aas_month_type){
        chkScheduleMonthType();
    }
    scheduleText();
}

//모달 초기화
function reset(){
    $('#automationModal input[name=seq]').val('');
    $('#conditionTable tbody tr:not(#condition-1)').remove()
    $('#targetCheckedTable tbody tr, #targetSelectTable tbody tr, #execSelectTable tbody tr').remove();
    $('#condition-1 input[name=type_value]').show();
    $('#condition-1 select[name=type_value_status]').hide();
    $('#myTab li').each(function(index){
        let $pTags = $(this).find('p');
        if(index === 1 || index === 3){
            $pTags.remove();
        }else if (index === 2 || index === 4) {
            $pTags.first().find('span').text('');
        }else{
            $pTags.first().text('');
        }
        $pTags.not(':first').remove();
    })

    $('#automationModal').find('select').each(function() {
        $(this).prop('selectedIndex', 0);
    });
    $('#automationModal').find('input[type=text], input[type=hidden], textarea').each(function() {
        $(this).val('');
    }); 
    
    $('input[name=operation]').prop('checked', false);
    $('#searchAll').prop('checked', false);
    $('#showTargetAdv').prop('disabled', false);
    
    $('#condition-tab p').show();
    
    if ($.fn.DataTable.isDataTable('#targetTable')) {
        targetTable = $('#targetTable').DataTable();
        targetTable.destroy();
    }
    if ($.fn.DataTable.isDataTable('#execTable')) {
        execTable = $('#execTable').DataTable();
        execTable.destroy();
    }

    $('#targetTable tbody tr, #execTable tbody tr').remove();
    $('#schedule-tab').trigger('click');
}

function setProcData(){
    let $type_value = $('#scheduleTable input[name=type_value]').val();
    let $exec_type = $('#scheduleTable select[name=exec_type]').val();
    let $criteria_time = $('#scheduleTable input[name=criteria_time]').val();
    let $exec_week = $('#scheduleTable input[name=exec_week]:checked').val();
    let $month_type = $('#scheduleTable select[name=month_type]').val();
    let $month_day = $('#scheduleTable select[name=month_day]').val();
    let $month_week = $('#scheduleTable select[name=month_week]').val();
    let $exec_time = $('#scheduleTable select[name=exec_time]').val();
    let $ignore_start_time = $('#scheduleTable select[name=ignore_start_time]').val();
    let $ignore_end_time = $('#scheduleTable select[name=ignore_end_time]').val();
    let $target_create_type = $('input[name=target_create_type]:checked').val();
    let $operation = $('input[name=operation]:checked').val();

    let $targets = [];
    let $conditions = [];
    let $executions = [];

    $('#targetSelectTable tbody tr').each(function(){
        let $row = $(this);
        let media = $row.find('td:eq(0)').text();
        let type = $row.find('td:eq(1)').text();
        let id = $row.find('td:eq(2)').text();

        $targets.push({
            media: media,
            type: type,
            id: id,
        });
    });

    $('#conditionTable tbody tr[id^="condition-"]').each(function(){
        let $row = $(this);
        let type = $row.find('select[name=type]').val();
        let type_value = '';
        if(type == 'status'){
            type_value = $row.find('select[name=type_value_status]').val();
        }else{
            type_value = $row.find('input[name=type_value]').val();
        }
        let compare = $row.find('select[name=compare]').val();
        
        if(type){
            $conditions.push({
                type: type,
                type_value: type_value,
                compare: compare,
                operation: $operation
            });
        }
    });

    $('#execSelectTable tbody tr').each(function(){
        let $row = $(this);
        let order = $row.find('input[name=exec_order]').val();
        let media = $row.find('td:eq(1)').text();
        let type = $row.find('td:eq(2)').text();
        let id = $row.find('td:eq(3)').text();
        let exec_type = $row.find('select[name=exec_condition_type]').val();
        let exec_value = '';
        if(exec_type == 'status'){
            exec_value = $row.find('select[name=exec_condition_value_status]').val();
        }else if(exec_type == 'budget'){
            exec_value = $row.find('input[name=exec_condition_value]').val();
        }
        let exec_budget_type = $row.find('select[name=exec_condition_type_budget]').val();

        $executions.push({
            order: order,
            media: media,
            type: type,
            id: id,
            exec_type: exec_type,
            exec_value: exec_value,
            exec_budget_type: exec_budget_type
        });
    });

    let $subject = $('#detailTable input[name=subject]').val();
    let $description = $('#detailTable textarea[name=description]').val();
    let $slack_webhook = $('#slackSendTable input[name=slack_webhook]').val();
    let $slack_msg = $('#slackSendTable input[name=slack_msg]').val();

    let $data = {
        'schedule': {
            'type_value': $type_value,
            'exec_type': $exec_type,
            'criteria_time': $criteria_time,
            'exec_week': $exec_week,
            'month_type': $month_type,
            'month_day': $month_day,
            'month_week': $month_week,
            'exec_time': $exec_time,
            'ignore_start_time': $ignore_start_time,
            'ignore_end_time': $ignore_end_time,
        },
        'execution': $executions,
        'detail': {
            'subject': $subject,
            'description': $description,
            'slack_webhook': $slack_webhook,
            'slack_msg': $slack_msg,
        }
    };

    if ($('#targetSelectTable tbody tr').length > 0) {
        $data['target_create_type'] = $target_create_type;
        $data['target'] = $targets;
        $data['condition'] = $conditions;
    }

    return $data;
}

function conditionStatusHide(){
    let targetCount = $('#targetSelectTable tbody tr').length;
    if (targetCount > 1) {
        $('#conditionTable select option[value=status]').hide();
    }else{
        $('#conditionTable select option[value=status]').show();
    }
}

function setTargetExec(data)
{
    data.forEach(function(target, index) {
        let targetIndex = index+1;
        let targetData = '<tr data-id="'+target.media+"_"+target.type+"_"+target.id+'" id="target-'+targetIndex+'"><td>' + target.media + '</td><td>' + target.type + '</td><td>' 
    + target.id + '</td><td>' + target.name + '</td><td>'
    + target.status  +'<button class="set_target_except_btn"><i class="fa fa-times"></i></button></td></tr>';
    
        let newTargetText = '<p id="text-target-'+targetIndex+'">* '+target.type+' - '+target.media+'<br>'+target.name+'</p>';
        $('#targetSelectTable tbody').append(targetData);
        $('#target-tab').append(newTargetText);
    });
}

function getTargetAdv(data){
    $.ajax({
        type: "GET",
        url: "/advertisements/get-adv",
        data: data,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function(data){  
            setTargetExec(data);
        },
        error: function(error, status, msg){
            alert("상태코드 " + status + "에러메시지" + msg );
        }
    });
}

//모달 보기
$('#automationModal').on('show.bs.modal', function(e) {
    setCriteriaTime();
    $('#execSearchWrap').show();
    var $btn = $(e.relatedTarget);
    if ($btn.hasClass('updateBtn')) {
        $('#automationModal #targetCreateType').hide();
        var id = $btn.closest('tr').data('id');
        $.ajax({
            type: "GET",
            url: "/automation/get-automation",
            data: {'id':id},
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                setModalData(data);
            },
            error: function(error, status, msg){
                alert("상태코드 " + status + "에러메시지" + msg );
            }
        });
    }else{      
        if ($btn.hasClass('advAutomationCreateBtn')) {
            let selected = [];
            let clickBtn = $($btn).closest('tr').data('id');
            selected.push(clickBtn);
            let data = {
                'check': selected,
                'type': $('.tab-link.active').val(),
            };
            getTargetAdv(data);
        }

        if ($btn.hasClass('checkAdvAutomationCreateBtn')) {
            let selected = $('.dataTable tbody tr.selected').map(function(){return $(this).data('id');}).get();
            let data = {
                'check': selected,
                'type': $('.tab-link.active').val(),
            };
            getTargetAdv(data);
        }
        chkSchedule();
        conditionStatusHide();
        $('#automationModal #targetCreateType').show();
        $('#automationModal input[type="radio"][value="target_sum"]').prop('checked', true);
        $('#automationModal input[type="radio"][value="and"]').prop('checked', true);
        $('#createAutomationBtn').show();
        $('#updateAutomationBtn').hide();
    }
})//모달 닫기
.on('hidden.bs.modal', function(e) { 
    reset();
});

//등록 부분 시작
$('body').on('change', '#execType', function() {
    chkSchedule();
});

$('body').on('change', '#monthType', function() {
    chkScheduleMonthType();
});

$('body').on('change', '#scheduleTable input, #scheduleTable select', function() {
    scheduleText();
});

$('form[name="search-target-form"]').bind('submit', function() {
    let data = {
        'tab': $('#targetTab li.active').data('tab'),
        'stx': $('#showTargetAdv').val(),
    }
    getTargetAdvs(data);
    
    return false;
});

$('form[name="search-exec-form"]').bind('submit', function() {
    let searchAll = $('#searchAll').is(':checked');
    let targets = $('#targetSelectTable tbody tr').length;
    let adv = [];

    if (!searchAll && !targets) {
        alert('대상이 없을 경우 전체검색을 체크해주세요.');
        $('#searchAll').focus();
        return false;
    }

    $('#targetSelectTable tbody tr').each(function() {
        let dataId = $(this).data('id');
        adv.push(dataId);
    });

    let data = {
        'tab': $('#execTab li.active').data('tab'),
        'stx': $('#showExecAdv').val(),
        'adv': searchAll ? null : adv,
    }

    getExecAdvs(data);
    return false;
});

$('body').on('click', '#targetTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }else {
        $('#targetTable tr.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#targetCheckedTable tbody').empty();
        let cloneRow = $(this).clone();
        cloneRow.find('td:last-child').remove();
        cloneRow.appendTo('#targetCheckedTable tbody');
    }
});

$('body').on('click', '.target-btn', function(e){
    e.stopPropagation();
    let targetId = $(this).closest('tr').data('id');
    let rowMedia = $(this).closest('tr').children('td').eq(0).text();
    let rowType = $(this).closest('tr').children('td').eq(1).text();
    let rowName = $(this).closest('tr').children('td').eq(3).text();
    let newRowIdNumber = $('#targetSelectTable tbody tr').length + 1;
    if ($('#targetSelectTable tbody td:contains("' + rowName + '")').length > 0) {
        alert("중복된 행이 존재합니다.");
        return;
    }

    let clonedRow = $(this).closest('tr').clone();
    clonedRow.children('td:last').remove();
    clonedRow.find('td:last').append('<button class="set_target_except_btn"><i class="fa fa-times"></i></button>');
    clonedRow.removeClass('selected');
    clonedRow.attr('id', 'target-'+newRowIdNumber).appendTo('#targetSelectTable');

    let newTargetText = '<p id="text-target-'+newRowIdNumber+'">* '+rowType+' - '+rowMedia+'<br>'+rowName+'</p>';
    $('#target-tab').append(newTargetText);

    conditionStatusHide();
});

$('body').on('click', '.callTargetBtn', function(){
    let isTargetPresent = false;
    $('#targetSelectTable tbody tr').each(function() {
        let media = $(this).find('td:eq(1)').text();
        if (media === '캠페인' || media === '광고그룹' || media === '광고') {
            isTargetPresent = true;
            return false;
        }
    });
    
    if (!isTargetPresent) {
        alert('불러올 대상이 존재하지 않습니다.');
        return false;
    }

    $('#targetSelectTable tbody tr').each(function() {
        let trId = $(this).data('id');
        let existingIds = $('#execSelectTable tbody tr').map(function() {
            return $(this).data('id');
        }).get();
    
        if (existingIds.includes(trId)) {
            alert('중복된 행이 존재합니다.');
            return false;
        }

        let media = $(this).find('td:eq(0)').text();
        let type = $(this).find('td:eq(1)').text();
        let newRowIdNumber = $('#execSelectTable tbody tr').length + 1;
        if (type == '캠페인' || type == '광고그룹' || type == '광고') {
            let cloneRow = $(this).clone();
            cloneRow.attr('id', 'exec-'+newRowIdNumber);
            cloneRow.find('td:last-child').remove();
            let orderTd = `<td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2" value="${newRowIdNumber}"></td>`;
            if($('input[name=target_create_type]:checked').val() == 'target_seperate'){
                orderTd = `<td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2" value="1"></td>`;
            }
            let newTd = $('<td><div class="form-flex"><select name="exec_condition_type" class="form-select"><option value="">실행항목</option><option value="status">상태</option><option value="budget">예산</option></select></td><td><select name="exec_condition_value_status" class="form-select"><option value="">상태값</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="exec_condition_value" class="form-control"placeholder="예산"></td><td><select name="exec_condition_type_budget" class="form-select"><option value="">단위</option><option value="won">원</option><option value="percent">%</option></select></div><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td>');
            if (type === '광고' || (media == '구글' && type == '광고그룹')) {
                newTd.find('select[name="exec_condition_type"] option[value="budget"]').hide();
                newTd.find('input[name="exec_condition_value"]').hide();
                newTd.find('select[name="exec_condition_type_budget"]').hide();
                newTd.find('select[name="exec_condition_value_status"]').show();
            }else{
                newTd.find('select[name="exec_condition_value_status"]').hide();
            }
            cloneRow.prepend(orderTd);
            cloneRow.append(newTd);
            cloneRow.appendTo('#execSelectTable tbody');

            let selectedMediaTd = $(this).children('td').eq(0).text();
            let selectedTypeTd = $(this).children('td').eq(1).text();
            let selectedNameTd = $(this).children('td').eq(3).text();
            let newExecText = '<p id="text-exec-'+newRowIdNumber+'">* '+selectedTypeTd+' - '+selectedMediaTd+'<br>'+selectedNameTd+'';
            $('#preactice-tab').append(newExecText);
        }
    });

});

$('body').on('click', '#execTable tbody tr', function(){
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }else {
        $(this).addClass('selected');
        let trId = $(this).data('id');
        let newRowIdNumber = $('#execSelectTable tbody tr').length + 1;
        let existingIds = $('#execSelectTable tbody tr').map(function() {
            return $(this).data('id');
        }).get();
    
        if (existingIds.includes(trId)) {
            alert('중복된 행이 존재합니다.');
            return false;
        }
        
        let cloneRow = $(this).clone();
        let media = cloneRow.find('td:eq(0)').text();
        let type = cloneRow.find('td:eq(1)').text();
        cloneRow.attr('id', 'exec-'+newRowIdNumber);
        cloneRow.find('td:last-child').remove();
        let orderTd = `<td><input type="text" class="form-control" name="exec_order" placeholder="순서" oninput="onlyNumber(this);" maxlength="2" value="${newRowIdNumber}"></td>`;
        let newTd = $('<td><div class="form-flex"><select name="exec_condition_type" class="form-select"><option value="">실행항목</option><option value="status">상태</option><option value="budget">예산</option></select></td><td><select name="exec_condition_value_status" class="form-select" style="display:none;"><option value="">상태값</option><option value="ON">ON</option><option value="OFF">OFF</option></select><input type="text" name="exec_condition_value" class="form-control"placeholder="예산"></td><td><select name="exec_condition_type_budget" class="form-select"><option value="">단위</option><option value="won">원</option><option value="percent">%</option></select></div><button class="exec_condition_except_btn"><i class="fa fa-times"></i></button></td>');
        
        if (type === '광고' || (media == '구글' && type == '광고그룹')) {
            newTd.find('select[name="exec_condition_type"] option[value="budget"]').hide();
            newTd.find('input[name="exec_condition_value"]').hide();
            newTd.find('select[name="exec_condition_type_budget"]').hide();
            newTd.find('select[name="exec_condition_value_status"]').show();
        }else{
            newTd.find('select[name="exec_condition_value_status"]').hide();
        }

        cloneRow.prepend(orderTd);
        cloneRow.append(newTd);
        cloneRow.appendTo('#execSelectTable tbody');

        let selectedMediaTd = $(this).children('td').eq(0).text();
        let selectedTypeTd = $(this).children('td').eq(1).text();
        let selectedNameTd = $(this).children('td').eq(3).text();
        let newExecText = '<p id="text-exec-'+newRowIdNumber+'">* '+selectedTypeTd+' - '+selectedMediaTd+'<br>'+selectedNameTd+'';
        $('#preactice-tab').append(newExecText);
    }
});

$('body').on('click', '#targetTab li', function(){
    $('#targetTab li').removeClass('active');
    $(this).addClass('active');
    let $selectRow = $('#targetCheckedTable tbody tr').data('id');
    let $selectRowCount = $('#targetCheckedTable tbody tr').length;

    if($selectRowCount > 0){
        let adv = [];
        adv.push($selectRow);
        let data = {
            'tab': $('#targetTab li.active').data('tab'),
            'adv': adv
        }

        getTargetAdvs(data);
    }
});

//합산적용, 개별적용
$('body').on('change', '#targetCreateType input[name=target_create_type]', function() {
    //상태 선택
    let type = $(this).val();
    if(type == 'target_seperate'){
        $('#execSearchWrap').hide();
    }else{
        $('#execSearchWrap').show();
    }
});

$('body').on('click', '#execTab li', function(){
    $('#execTab li').removeClass('active');
    $(this).addClass('active');
});

$('body').on('click', '#conditionTable .btn-add', function(){
    let currentRowCount = $('#conditionTable tbody tr').length;
    let uniqueId = 'condition-' + (currentRowCount + 1);
    addConditionRow(uniqueId);
});

$('body').on('change', '#conditionTable select[name=type]', function() {
    //상태 선택
    let type = $(this).val();
    let rowId = $(this).closest('tr').attr('id');
    if(type == 'status'){
        $('#text-'+rowId+" .typeValueText").text('');
        $(this).siblings('input[name=type_value]').val('').hide();
        $(this).closest('tr').find('select[name=compare]').children('option:not([value="equal"], [value="not_equal"])').hide();
        $(this).siblings('select[name=type_value_status]').show();
    }else{
        if($('#text-'+rowId+" .typeValueText").text() == 'ON' || $('#text-'+rowId+" .typeValueText").text() == 'OFF'){
            $('#text-'+rowId+" .typeValueText").text('');
        }
        $(this).siblings('select[name=type_value_status]').val('').hide();
        $(this).closest('tr').find('select[name=compare]').children('option').show();   
        $(this).siblings('input[name=type_value]').show();   
    }
});

$('body').on('change', '#conditionTable input, #conditionTable select', function() {
    let $this = $(this);
    conditionText($this);
});

$('body').on('change', '#execSelectTable tbody tr select[name=exec_condition_type]', function() {
    let value = $(this).val();
    if(value == 'status'){
        $(this).closest('tr').find('input[name=exec_condition_value]').val('').hide();
        $(this).closest('tr').find('select[name=exec_condition_type_budget]').val('').hide();
        $(this).closest('tr').find('select[name=exec_condition_value_status]').show();
    }else if(value == 'budget'){
        $(this).closest('tr').find('select[name=exec_condition_value_status]').val('').hide();  
        $(this).closest('tr').find('input[name=exec_condition_value]').show();   
        $(this).closest('tr').find('select[name=exec_condition_type_budget]').show();
    }
});

$('body').on('change', '#execSelectTable tfoot select[name=all_exec_condition_type]', function(){
    let value = $(this).val();
    if(value == 'status'){
        $('#execSelectTable tbody select[name=exec_condition_type]').val(value);
        $('#execSelectTable tbody select[name=exec_condition_type]').trigger('change');
        $(this).closest('tr').find('input[name=all_exec_condition_value]').val('').hide();
        $(this).closest('tr').find('select[name=all_exec_condition_type_budget]').val('').hide();
        $(this).closest('tr').find('select[name=all_exec_condition_value_status]').show();
    }else if(value == 'budget'){
        $('#execSelectTable tbody tr').each(function() {
            let media = $(this).find('td').eq(1).text();
            let type = $(this).find('td').eq(2).text();
            if(type == '광고' || (media == '구글' && type == '광고그룹')) {
                return;
            }else{
                $(this).find('select[name=exec_condition_type]').val(value).trigger('change');
            }
        });
    
        $(this).closest('tr').find('select[name=all_exec_condition_value_status]').val('').hide();  
        $(this).closest('tr').find('input[name=all_exec_condition_value]').show();   
        $(this).closest('tr').find('select[name=all_exec_condition_type_budget]').show();
    }
});

$('body').on('change', '#execSelectTable tfoot select[name=all_exec_condition_value_status]', function(){
    let value = $(this).val();
    $('#execSelectTable tbody tr').each(function() {
        let $select = $(this).find('select[name=exec_condition_type]');
        let selectedOption = $select.find('option:selected').val();

        if(selectedOption === 'status') {
            $(this).find('select[name=exec_condition_value_status]').val(value);
        }
    });
});

$('body').on('input', '#execSelectTable tfoot input[name=all_exec_condition_value]', function(){
    let value = $(this).val();
    $('#execSelectTable tbody tr').each(function() {
        let $select = $(this).find('select[name=exec_condition_type]');
        let selectedOption = $select.find('option:selected').val();

        if(selectedOption === 'budget') {
            $(this).find('input[name=exec_condition_value]').val(value);
        }
    });
});

$('body').on('change', '#execSelectTable tfoot select[name=all_exec_condition_type_budget]', function(){
    let value = $(this).val();
    $('#execSelectTable tbody tr').each(function() {
        let $select = $(this).find('select[name=exec_condition_type]');
        let selectedOption = $select.find('option:selected').val();

        if(selectedOption === 'budget') {
            $(this).find('select[name=exec_condition_type_budget]').val(value);
        }
    });
});


$('body').on('click', '.set_target_except_btn', function() {
    let rowId = $(this).closest('tr').attr('id');
    $(this).closest('tr').remove();
    $('#target-tab #text-'+rowId).remove();

    conditionStatusHide();
});

$('body').on('click', '.exec_condition_except_btn', function() {
    $(this).closest('tr').remove();
    let rowId = $(this).closest('tr').attr('id');
    $('#preactice-tab #text-'+rowId).remove();
});

$('body').on('click', '.deleteBtn', function() {
    $(this).closest('tr').remove();
    let rowId = $(this).closest('tr').attr('id');
    $('#condition-tab #text-'+rowId).remove();
});

$('body').on('focusout', '#detailTable input[name=subject]', function() {
    let detailTextSubject = $(this).val();
    $('#detailText #subjectText').text(detailTextSubject);
});

$('body').on('focusout', '#detailTable textarea[name=description]', function() {
    let detailTextDescription = $(this).val();
    $('#detailText #descriptionText').text(detailTextDescription);
});

$('body').on('click', '#createAutomationBtn', function() {
    if(validationData()){
        let procData = setProcData();
        $.ajax({
            type: "POST",
            url: "/automation/create",
            data: procData,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    //dataTable.draw();
                    $('#automationModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                var errorMessages = error.responseJSON.messages.msg;
                var firstErrorMessage = Object.values(errorMessages)[0];
                alert(firstErrorMessage);
            }
        });
    };
});

$('body').on('click', '#updateAutomationBtn', function() {
    if(validationData()){
        let procData = setProcData();
        procData.seq = $('input[name=seq]').val();
        $.ajax({
            type: "PUT",
            url: "/automation/update",
            data: procData,
            dataType: "json",
            contentType: 'application/json; charset=utf-8',
            success: function(data){  
                if(data == true){
                    //dataTable.draw();
                    $('#automationModal').modal('hide');
                }
            },
            error: function(error, status, msg){
                var errorMessages = error.responseJSON.messages.msg;
                var firstErrorMessage = Object.values(errorMessages)[0];
                alert(firstErrorMessage);
            }
        });
    };
});
//등록 부분 끝