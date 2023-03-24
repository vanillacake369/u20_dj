<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
</head>

<body>
    <?php
    session_start();
    include __DIR__ . "/database/dbconnect.php";
    //헤더파일로 따로 구현
    ?>

    <h1>경기 생성</h1>
    <form id="scheduleAdd_form" method="post" action="sport/addSchedule.php">
        경기 종목<br>
        <input type="text" name="scheduleType" id="scheduleType"><br>
        경기 이름 <br>
        <input type="text" name="scheduleName" id="scheduleName"><br>
        경기 성별<br>
        <input type="text" name="scheduleGender" id="scheduleGender"><br>
        경기 라운드<br>
        <input type="text" name="scheduleRound" id="scheduleRound"><br>
        경기 장소<br>
        <input type="text" name="scheduleLocation" id="scheduleLocation"><br>
        경기 시작시간<br>
        <input type="text" name="scheduleStart" id="scheduleStart"><br>
        경기 마감시간<br>
        <input type="text" name="scheduleFinish" id="scheduleFinish"><br>
        경기 상태<br>
        <input type="text" name="scheduleStatus" id="scheduleStatus"><br>
        경기 날짜<br>
        <input type="text" name="scheduleDate" id="scheduleDate"><br>

        <input type="button" value="경기 생성" onclick="schaddchk_submit()">
    </form>

    <br>
    <br>
    <?php

    $sql = "SELECT * FROM list_schedule WHERE schedule_id = 1";
    $row = $db->query($sql);
    $data = mysqli_fetch_array($row);

    $type = $data['schedule_sports'];
    $name = $data['schedule_name'];
    $gender = $data['schedule_gender'];
    $round = $data['schedule_round'];
    $location = $data['schedule_location'];
    $start = $data['schedule_start'];
    $finish = $data['schedule_finish'];
    $status = $data['schedule_status'];
    $date = $data['schedule_date'];

    ?>


    <h1>경기 수정</h1>
    <form id="scheduleUpdate_form" method="post" action="sport/updateSchedule.php?scheduleid=<?php echo 1; ?>">
        경기 종목<br>
        <input type="text" name="scheduleType" id="scheduleType1" value="<?php echo $type; ?>"><br>
        경기 이름 <br>
        <input type="text" name="scheduleName" id="scheduleName1" value="<?php echo $name; ?>"><br>
        경기 성별<br>
        <input type="text" name="scheduleGender" id="scheduleGender1" value="<?php echo $gender; ?>"><br>
        경기 라운드<br>
        <input type="text" name="scheduleRound" id="scheduleRound1" value="<?php echo $round; ?>"><br>
        경기 장소<br>
        <input type="text" name="scheduleLocation" id="scheduleLocation1" value="<?php echo $location; ?>"><br>
        경기 시작시간<br>
        <input type="text" name="scheduleStart" id="scheduleStart1" value="<?php echo $start; ?>"><br>
        경기 마감시간<br>
        <input type="text" name="scheduleFinish" id="scheduleFinish1" value="<?php echo $finish; ?>"><br>
        경기 상태<br>
        <input type="text" name="scheduleStatus" id="scheduleStatus1" value="<?php echo $status; ?>"><br>
        경기 날짜<br>
        <input type="text" name="scheduleDate" id="scheduleDate1" value="<?php echo $date; ?>"><br>

        <input type="button" value="경기 수정" onclick="schupdatechk_submit()">
    </form>

    <br>
    <br>

    <h1>경기 삭제</h1>
    <form method="post" action="sport/deleteSchedule.php?scheduleid=<?php echo 3; ?>">
        <button type="submit" value="">경기 삭제</button>
    </form>

    <br>
    <br>

    <h1>참가 국가 생성</h1>
    <form id="countryAdd_form" method="post" action="sport/addCountry.php">
        국가 코드<br>
        <input type="text" name="countryCode" id="countryCode"><br>
        국가 이름 <br>
        <input type="text" name="countryName" id="countryName"><br>
        국가 이름 한글<br>
        <input type="text" name="countryNameKr" id="countryNameKr"><br>

        <input type="button" value="국가 생성" onclick="countryaddchk_submit()">
    </form>

    <br>
    <br>


    <?php

    $sql = "SELECT * FROM list_country WHERE country_id = 37";
    $row = $db->query($sql);
    $country = mysqli_fetch_array($row);

    $code = $country['country_code'];
    $name = $country['country_name'];
    $namekr = $country['country_name_kr'];

    ?>


    <h1>참가 국가 수정</h1>
    <form id="countryUpdate_form" method="post" action="sport/updateCountry.php?countryid=<?php echo 37; ?>">
        국가 코드<br>
        <input type="text" name="countryCode" id="countryCode1" value="<?php echo $code; ?>"><br>
        국가 이름 <br>
        <input type="text" name="countryName" id="countryName1" value="<?php echo $name; ?>"><br>
        국가 이름 한글<br>
        <input type="text" name="countryNameKr" id="countryNameKr1" value="<?php echo $namekr; ?>"><br>

        <input type="button" value="국가 수정" onclick="countryupdatechk_submit()">
    </form>

    <br>
    <br>

    <h1>참가 국가 삭제</h1>
    <form method="post" action="sport/deleteCountry.php?countryid=<?php echo 43; ?>">
        <button type="submit" value="">국가 삭제</button>
    </form>

    <br>
    <br>

    <?php

    $sql = "SELECT * FROM list_schedule WHERE schedule_id = 1";
    $row = $db->query($sql);
    $date = mysqli_fetch_array($row);

    $startedit = $date['schedule_start'];
    $finishedit = $date['schedule_finish'];

    ?>


    <h1>경기 일정 수정</h1>
    <form id="scheduledateUpdate_form" method="post" action="sport/updateScheduleDate.php?scheduleid=<?php echo 1; ?>">
        경기 시작시간<br>
        <input type="text" name="scheduleStart" id="scheduleStart2" value="<?php echo $startedit; ?>"><br>
        경기 마감시간<br>
        <input type="text" name="scheduleFinish" id="scheduleFinish2" value="<?php echo $finishedit; ?>"><br>

        <input type="button" value="경기일정 수정" onclick="schdateupdatechk_submit()">
    </form>

    <script type=text/javascript src="js/sport.js"></script>

    <br>
    <br>
    <br>
    <br>


    <form action="country_search.php" method="get">
        <input type="text" name="search" size="40" required="required" /> <button>검색</button>
    </form>



</body>