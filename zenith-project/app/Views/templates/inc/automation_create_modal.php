<div class="modal fade" id="automationModal" tabindex="-1" aria-labelledby="automationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="regi-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="step">
                        <ol id="myTab" role="tablist">
                            <li class="tab-link active" role="presentation" type="button" id="schedule-tab"  data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="true">
                                <strong>일정</strong>
                                <p id="scheduleText"></p>
                            </li>
                            <li class="tab-link" role="presentation" id="target-tab" data-bs-toggle="tab" data-bs-target="#target" type="button" role="tab" aria-controls="target" aria-selected="false">
                                <strong>대상</strong>   
                            </li>
                            <li class="tab-link" role="presentation" id="condition-tab" data-bs-toggle="tab" data-bs-target="#condition" type="button" role="tab" aria-controls="condition" aria-selected="false">
                                <strong>조건</strong>
                                <p id="text-condition-1">
                                    <span class="typeText"></span>
                                    <span class="typeValueText"></span>
                                    <span class="compareText"></span>
                                </p>
                            </li>
                            <li class="tab-link" role="presentation" id="preactice-tab" data-bs-toggle="tab" data-bs-target="#preactice" type="button" role="tab" aria-controls="preactice" aria-selected="false">
                                <strong>실행</strong>
                            </li>
                            <li class="tab-link" role="presentation" id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                <strong>상세정보</strong>
                                <p id="detailText">
                                    <span id="subjectText"></span><br>
                                    <span id="descriptionText"></span>
                                </p>
                            </li>
                        </ol>
                    </div>
                    <div class="detail-wrap">
                        <input type="hidden" name="seq">
                        <div class="detail show active" id="schedule" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0"> 
                            <table class="table tbl-side" id="scheduleTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">다음 시간마다 규칙적으로 실행</th>
                                    <td>
                                        <input type="text" name="type_value" class="form-control short" oninput="onlyNumber(this);" maxlength="3" />
                                        <select name="exec_type" class="form-select short" id="execType">
                                            <option value="minute">분</option>
                                            <option value="hour">시간</option>
                                            <option value="day">일</option>
                                            <option value="week">주</option>
                                            <option value="month">월</option>
                                        </select>
                                        <p></p>
                                    </td>
                                </tr>
                                <tr id="weekdayRow">
                                    <th scope="row">요일</th>
                                    <td>
                                        <div class="week-radio">
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="2" id="day01">
                                                <label for="day01">월</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="3" id="day02">
                                                <label for="day02">화</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="4" id="day03">
                                                <label for="day03">수</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="5" id="day04">
                                                <label for="day04">목</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="6" id="day05">
                                                <label for="day05">금</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="7" id="day06">
                                                <label for="day06">토</label>
                                            </div>
                                            <div class="day">
                                                <input type="radio" name="exec_week" value="1" id="day07">
                                                <label for="day07">일</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="nextDateRow">
                                    <th scope="row">다음 날짜에</th>
                                    <td>
                                        <select name="month_type" class="form-select" id="monthType">
                                            <option value="" selected>선택</option>
                                            <option value="start_day">매달 첫번째 날</option>
                                            <option value="end_day">매달 마지막 날</option>
                                            <option value="first">처음</option>
                                            <option value="last">마지막</option>
                                            <option value="day">날짜</option>
                                        </select>
                                        <select name="month_day" class="form-select" id="monthDay">
                                            <option value="" selected>선택</option>
                                            <?php
                                            for ($i = 1; $i <= 31; $i++) {
                                                echo "<option value='$i'>".$i."일</option>";
                                            }
                                            ?>
                                        </select>
                                        <select name="month_week" class="form-select" id="monthWeek">
                                            <option value="" selected>선택</option>
                                            <option value="2">월</option>
                                            <option value="3">화</option>
                                            <option value="4">수</option>
                                            <option value="5">목</option>
                                            <option value="6">금</option>
                                            <option value="7">토</option>
                                            <option value="1">일</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="timeRow">
                                    <th scope="row">시간</th>
                                    <td>
                                    <?php
                                        $interval = new DateInterval('PT30M');
                                        $time = new DateTime('00:00');
                                        $end = new DateTime('24:00');

                                        ob_start();

                                        while ($time < $end) {
                                            $value = $time->format('H:i');
                                            echo "<option value='$value'>$value</option>";
                                            $time->add($interval);
                                        }

                                        $timeOptions = ob_get_clean();
                                        ?>
                                        <select name="exec_time" id="execTime" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php echo $timeOptions; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="criteriaTimeRow">
                                    <th scope="row">시작 일시</th>
                                    <td>
                                        <div class="form-flex">
                                            <input type="text" name="criteria_time" class="form-control" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">제외 시간</th>
                                    <td>
                                        <div class="form-flex">
                                        <select name="ignore_start_time" class="form-select middle">
                                            <option value="" selected>선택</option>
                                            <?php echo $timeOptions; ?>
                                        </select>
                                            <span>~</span>
                                            <select name="ignore_end_time" class="form-select middle">
                                                <option value="">선택</option>
                                                <?php echo $timeOptions; ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="detail" id="target" role="tabpanel"  aria-labelledby="target-tab" tabindex="1">
                            <ul class="tab" id="targetTab">
                                <li class="active" data-tab="advertiser"><a href="#">광고주</a></li>
                                <li data-tab="account"><a href="#">매체광고주</a></li>
                                <li data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adgroup"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <form name="search-target-form" class="search d-flex justify-content-center w-100">
                                    <div class="input">
                                        <input type="text" placeholder="검색어를 입력하세요" id="showTargetAdv">
                                        <button class="btn-primary" id="search_target_btn" type="submit">조회</button>
                                    </div>
                                </form>
                            </div>
                            <table class="table tbl-header w-100" id="targetCheckedTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">선택 항목(선택 후 상단 탭 클릭 시 소속 항목 조회가 가능합니다)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <table class="table tbl-header w-100" id="targetTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:32%">
                                    <col style="width:10%">
                                    <col style="width:8%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">매체</th>
                                        <th scope="col" class="text-center">분류</th>
                                        <th scope="col" class="text-center">ID</th>
                                        <th scope="col" class="text-center">제목</th>
                                        <th scope="col" class="text-center">상태</th>
                                        <th scope="col" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="targetSelectTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="5"  class="text-center">적용 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="condition" role="tabpanel" aria-labelledby="condition-tab" tabindex="2">
                            <div class="d-flex align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="and" id="operationAnd">
                                    <label class="form-check-label" for="operationAnd">
                                        모두 일치
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="operation" value="or" id="operationOr">
                                    <label class="form-check-label" for="operationOr">
                                        하나만 일치
                                    </label>
                                </div>
                            </div>
                            <table class="table tbl-header" id="conditionTable">
                                <colgroup>
                                    <col>
                                    <col style="width:45%">
                                    <col style="width:5%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                        <th scope="col">구분</th>
                                        <th scope="col"><button type="button" class="btn-add">추가</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="condition-1">
                                        <td>
                                            <div class="form-flex">
                                                <!-- <input type="text" name="order" placeholder="순서(1~)" class="form-control conditionOrder" oninput="onlyNumber(this);" maxlength="3"> -->
                                                <select name="type" class="form-select conditionType">
                                                    <option value="">조건 항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                    <option value="dbcost">DB단가</option>
                                                    <option value="unique_total">유효DB</option>
                                                    <option value="spend">지출액</option>
                                                    <option value="margin">수익</option>
                                                    <option value="margin_rate">수익률</option>
                                                    <option value="sales">매출액</option>
                                                    <!-- <option value="impression">노출수</option>
                                                    <option value="click">링크클릭</option>
                                                    <option value="cpc">CPC</option>
                                                    <option value="ctr">CTR</option> -->
                                                    <option value="conversion">DB전환률</option>
                                                </select>
                                           
                                                <select name="type_value_status" class="form-select conditionTypeValueStatus" style="display: none;">
                                                    <option value="">상태값 선택</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="type_value" class="form-control conditionTypeValue" placeholder="조건값">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-flex">
                                            <select name="compare" class="form-select conditionCompare">
                                                <option value="">일치여부</option>
                                                <option value="greater">초과</option>
                                                <option value="greater_equal">보다 크거나 같음</option>
                                                <option value="less">미만</option>
                                                <option value="less_equal">보다 작거나 같음</option>
                                                <option value="equal">같음</option>
                                                <option value="not_equal">같지않음</option>
                                            </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="preactice" role="tabpanel" aria-labelledby="preactice-tab" tabindex="3">
                            <ul class="tab" id="execTab">
                                <li class="active" data-tab="campaign"><a href="#">캠페인</a></li>
                                <li data-tab="adgroup"><a href="#">광고그룹</a></li>
                                <li data-tab="ad"><a href="#">광고</a></li>
                            </ul>
                            <div class="search">
                                <div class="d-flex align-items-center mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="searchAll">
                                    <label class="form-check-label" for="searchAll">
                                        전체검색
                                    </label>
                                </div>
                                <form name="search-exec-form" class="search d-flex justify-content-center w-100">
                                   <div class="input">
                                        <input type="text" placeholder="검색어를 입력하세요" id="showExecAdv">
                                        <button class="btn-primary" id="search_exec_btn" type="submit">조회</button>
                                   </div>
                                </form>
                            </div>
                            <table class="table tbl-header w-100 mt-4" id="execTable">
                                <colgroup>
                                    <col style="width:10%">
                                    <col style="width:10%">
                                    <col style="width:30%">
                                    <col style="width:40%">
                                    <col style="width:10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">매체</th>
                                        <th scope="col">분류</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">제목</th>
                                        <th scope="col">상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4" id="execConditionTable">
                                <thead>
                                    <tr>
                                        <th scope="col">항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <select name="exec_condition_type" class="form-select" id="execConditionType">
                                                    <option value="">실행항목</option>
                                                    <option value="status">상태</option>
                                                    <option value="budget">예산</option>
                                                </select>
                                                <select name="exec_condition_value_status" class="form-select" style="display: none;" id="execConditionValueStatus">
                                                    <option value="">상태값 선택</option>
                                                    <option value="ON">ON</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                                <input type="text" name="exec_condition_value" class="form-control" id="execConditionValue" placeholder="예산">
                                                <select name="exec_condition_type_budget" class="form-select" id="execConditionTypeBudget">
                                                    <option value="">단위 선택</option>
                                                    <option value="won">원</option>
                                                    <option value="percent">%</option>
                                                </select>
                                                <button class="btn-special" id="execConditionBtn">적용</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table tbl-header w-100 mt-4 execSelectTable" id="execSelectTable">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="8"  class="text-center">선택 항목</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tbl-header w-100 mt-4 slackSendTable" id="slackSendTable">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">슬랙 메세지 보내기</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-flex">
                                                <input type="text" name="slack_webhook" class="form-control" placeholder="웹훅 URL">
                                                <input type="text" name="slack_msg" class="form-control" placeholder="메세지">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail" id="detailed" role="tabpanel" aria-labelledby="detailed-tab" tabindex="4">
                            <table class="table tbl-side" id="detailTable">
                                <colgroup>
                                    <col style="width:35%">
                                    <col>
                                </colgroup>
                                <tr>
                                    <th scope="row">제목*</th>
                                    <td>
                                        <input type="text" name="subject" class="form-control bg">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">설명</th>
                                    <td>
                                        <textarea name="description" class="form-control"></textarea>
                                    </td>
                                </tr>
                            </table>
                            <duv class="btn-area">
                                <button type="button" id="createAutomationBtn" class="btn-special">저장</button>
                                <button type="button" id="updateAutomationBtn" class="btn-special" style="display: none;">수정</button>
                            </duv>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>