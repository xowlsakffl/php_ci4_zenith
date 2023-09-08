<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 자동화 관리
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<link href="/static/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"> 
<link href="/static/node_modules/datatables.net-staterestore-bs5/css/stateRestore.bootstrap5.min.css" rel="stylesheet"> 
<script src="/static/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/node_modules/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap eventmanage-container">

    <div class="mb-4">
        <h2 class="mb-4">자동화 등록</h2>
        <form action="/automation/create" method="POST">
            <dl>
                <dt>이름:</dt>
                <dd><input type="text" name="subject" class="form-control"></dd> 
            </dl>
            <dl>
                <dt>설명:</dt>
                <dd><textarea name="description" name="subject" class="form-control"></textarea></dd> 
            </dl>
            <input type="submit" value="등록" class="btn-primary">
        </form>
    </div>

    <div class="mb-4">
        <h2 class="mb-4">자동화 일정 등록</h2>
        <form action="/automation/create" method="POST">
            <dl>
                <dt>다음 시간마다 규칙적으로 실행 :</dt>
                <dd>
                    <input type="text" name="type_value" class="form-control">
                    <select name="exec_type" class="form-select">
                        <option value="minute">분</option>
                        <option value="hour">시간</option>
                        <option value="day">일</option>
                        <option value="week">주</option>
                        <option value="month">월</option>
                    </select>
                </dd> 
            </dl>
            <dl>
                <dt>요일 :</dt>
                <dd>
                    <input type="checkbox" name="exec_week" value="1">월
                    <input type="checkbox" name="exec_week" value="2">화
                    <input type="checkbox" name="exec_week" value="3">수
                    <input type="checkbox" name="exec_week" value="4">목
                    <input type="checkbox" name="exec_week" value="5">금
                    <input type="checkbox" name="exec_week" value="6">토
                    <input type="checkbox" name="exec_week" value="0">일
                </dd> 
            </dl>
            <dl>
                <dt>다음 날짜에 :</dt>
                <dd>
                    <select name="month_type" class="form-select">
                        <option value="start_day">매달 첫번째 날</option>
                        <option value="end_day">매달 마지막 날</option>
                        <option value="first">처음</option>
                        <option value="last">마지막</option>
                        <option value="day">날짜</option>
                    </select>
                    <select name="month_day" class="form-select">
                        <?php
                        for ($day = 1; $day <= 31; $day++) {
                            echo '<option value="' . $day . '">' . $day . '일</option>';
                        }
                        ?>
                    </select>
                    <select name="month_week" class="form-select">
                        <option value="1">월</option>
                        <option value="2">화</option>
                        <option value="3">수</option>
                        <option value="4">목</option>
                        <option value="5">금</option>
                        <option value="6">토</option>
                        <option value="0">일</option>
                    </select>
                </dd> 
            </dl>
            <dl>
                <dt>제외 시간 :</dt>
                <dd>
                    <select name="ignore_time_start" class="form-select">
                    <?php
                        $start_time = strtotime("00:00");
                        $end_time = strtotime("23:30");
                        $interval = 30 * 60; // 30분 간격
                        
                        for ($time = $start_time; $time <= $end_time; $time += $interval) {
                            $formatted_time = date("H:i", $time);
                            echo '<option value="' . $formatted_time . '">' . $formatted_time . '</option>';
                        }
                    ?>
                    </select>~
                    <select name="ignore_time_end" class="form-select">
                    <?php
                        $start_time = strtotime("00:00");
                        $end_time = strtotime("23:30");
                        $interval = 30 * 60; // 30분 간격
                        
                        for ($time = $start_time; $time <= $end_time; $time += $interval) {
                            $formatted_time = date("H:i", $time);
                            echo '<option value="' . $formatted_time . '">' . $formatted_time . '</option>';
                        }
                    ?>
                    </select>
                </dd> 
            </dl>
        </form>
    </div>

    <div class="mb-4">
        <h2 class="mb-4">자동화 등록</h2>
        <form action="/automation/create" method="POST">
            <dl>
                <dt>이름:</dt>
                <dd><input type="text" name="subject" class="form-control"></dd> 
            </dl>
            <dl>
                <dt>설명:</dt>
                <dd><textarea name="description" name="subject" class="form-control"></textarea></dd> 
            </dl>
            <input type="submit" value="등록" class="btn-primary">
        </form>
    </div>

    <div class="mb-4">
        자동화 조건 등록
        <form action="/automation/create" method="POST">
            이름 <input type="text" name="subject" id=""><br>
            설명 <input type="text" name="description" id="">
            <input type="submit" value="등록">
        </form>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('modal')?>

<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>

</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
