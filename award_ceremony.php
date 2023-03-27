<?php
    require_once __DIR__ . "/includes/auth/config.php";
    require_once __DIR__ . "/security/security.php";
    require_once __DIR__ . '/action/module/schedule_worldrecord.php';
    require_once __DIR__ . "/backheader.php";

    $schedule_sports = $_POST['schedule_sports'];
    $schedule_gender = $_POST['schedule_gender'];
    $schedule_round = $_POST['schedule_round'];
    $schedule_name = $_POST['schedule_name'];
    $schedule_group = $_POST['schedule_group'];
    $schedule_result = $_POST['schedule_result'];
    $schedule_id = $_POST['schedule_id'];

    if ($schedule_sports == "800m" || $schedule_sports == "1500m" || $schedule_sports == " 2000m" || $schedule_sports == " 3000m" || $schedule_sports == " 3000mSC" || $schedule_sports == " 5000m" || $schedule_sports == "10000m" || $schedule_round == "1500m" || $schedule_round == "100m" || $schedule_round == "200m" || $schedule_round == "400m" || $schedule_round == "800m" || $schedule_round == "110mh" || $schedule_round == "100mh" || $schedule_round == "discusthrow" || $schedule_round == "shotput" || $schedule_round == "javelinthrow" || $schedule_round == "polevault" || $schedule_round == "highjump" || $schedule_round == "longjump") {
        $long = 1; //동시출발종목
    } else {
        $long = 0;
    }
    //종합경기
    if ($schedule_round == "100m" || $schedule_round == "100mh" || $schedule_round == "110mh" || $schedule_round == "200m" || $schedule_round == "400m" || $schedule_round == "800m" || $schedule_round == "1500m" || $schedule_round == "discusthrow" || $schedule_round == "shotput" || $schedule_round == "polevault" || $schedule_round == "highjump" || $schedule_round == "longjump" || $schedule_round == "javelinthrow") {
        $round = 1; //종합경기
    } else {
        $round = 0;
    }

    if ($round == 1) { //종합경기
        $sql = "SELECT DISTINCT record_official_result, record_memo,athlete_id, record_official_record, record_weight,
                    record_wind, record_order, athlete_name,record_new,schedule_sports, athlete_country, record_multi_record
                    FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                    ON schedule_id = record_schedule_id WHERE schedule_id =? AND record_multi_record >0
                    ORDER BY record_official_result ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();
        //종합경기 총합점수 조회
        $sql1 = "SELECT DISTINCT athlete_id, record_official_record, athlete_name
                    FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                    ON schedule_id = record_schedule_id WHERE schedule_sports = 'decathlon'
                    AND schedule_gender = ? AND schedule_round = '결승' AND schedule_group = ? ORDER BY record_official_record ASC";
        $stmt = $db->prepare($sql1);
        $stmt->bind_param('ss', $schedule_gender, $schedule_group);
        $stmt->execute();
        $result1 = $stmt->get_result();

        if ($long == 1) { //1500m
            //페이지 수
            $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ? AND record_trial = 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $schedule_id);
            $stmt->execute();
            $result1 = $stmt->get_result();
            $count = mysqli_num_rows($result1);
            $num = $count / 8;
        }
    } else if ($long == 1) { //동시출발종목(long트랙)
        //페이지 수
        $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $schedule_id);
        $stmt->execute();
        $result1 = $stmt->get_result();
        $count = mysqli_num_rows($result1);
        $num = $count / 8;
    } else { //트랙, 릴레이, 점프, 던지기 shot put에 official result 값이 없어서 일단 live result 값으로 사용
        $sql = "SELECT DISTINCT record_official_result, record_memo,athlete_id, record_official_record, record_weight,
                    record_wind, record_order, athlete_name,record_new,schedule_sports, athlete_country, record_multi_record
                    FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                    WHERE record_official_result > 0 AND schedule_id =?
                    ORDER BY record_official_result ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    //풍속, 라운드
    $sql = "SELECT schedule_sports, record_wind, schedule_id FROM list_record
                JOIN list_schedule 
                ON list_record.record_schedule_id = list_schedule.schedule_id 
                WHERE record_schedule_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $schedule_id);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $row2 = mysqli_fetch_array($result2);

    //종목
    $sql = "SELECT list_sports.sports_name FROM list_sports WHERE sports_code = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $schedule_sports);
    $stmt->execute();
    $result3 = $stmt->get_result();
    $row3 = mysqli_fetch_array($result3);

    //성별 문자열로 기록
    if ($schedule_gender == "m") {
        $gender = "MEN";
    } else if ($schedule_gender == "f") {
        $gender = "WOMEN";
    } else if ($schedule_gender == "c") {
        $gender = "MIXED";
    }

    //세계기록(WR)
    $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics= 'u';";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $schedule_sports,  $schedule_gender);
    $stmt->execute();
    $result4 = $stmt->get_result();
    $row4 = mysqli_fetch_array($result4);

    if ($schedule_sports == "100m" || $schedule_sports == "100mh" || $schedule_sports == "110m" || $schedule_sports == "200m" || $schedule_round == "100m" || $schedule_round == "100m 허들" || $schedule_round == "110m 허들" || $schedule_round == "200m") {
        $sports = 1; //짧은 트랙경기-풍속(title)
    } else if ($schedule_sports == "longjump" || $schedule_sports == "triplejump") {
        $sports = 2; //멀리뛰기, 세단뛰기 -풍속
    } else if ($schedule_sports == "shotput" || $schedule_sports == "hammerthrow" || $schedule_sports == "javelinthrow" || $schedule_sports == "discusthrow" || $schedule_round == "dis" || $schedule_round == "투포환" || $schedule_round == "창던지기") {
        $sports = 0; //던지기
    } else {
        $sports = 3;
    }
    if ($schedule_sports == "4x400mR" || $schedule_sports == "4x100mR") {
        $relay = 1;
    } else {
        $relay = 0;
    }

    $cnt = 0;
    $flag_cnt = 0;
    $country = array();
    if ($schedule_sports == "4x400mR" || $schedule_sports == "4x100mR") { //릴레이
        while ($row = mysqli_fetch_array($result)) {
            if ($cnt == 0) {
                
                $cnt++;
            } else if ($cnt == 3) {
                $country[] = htmlspecialchars($row["athlete_country"]); //선수 국가
                //신기록
                $cnt = 0;
                $flag_cnt++;
                if ($flag_cnt == 3) break;
            } else {
                $cnt++;
            }
        }
    } else if ($long == 1) { //페이징
        if ($round == 1) {
            $sql = "SELECT athlete_name, athlete_country, record_order, record_official_record, athlete_bib, record_official_result, record_new, record_multi_record, athlete_id
                                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                        WHERE record_schedule_id = ? AND record_multi_record > 0 ORDER BY record_official_result ASC";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $schedule_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = mysqli_fetch_array($result)) {
                $country[] = htmlspecialchars($row["athlete_country"]); //선수 국가
                
                $flag_cnt++;
                if ($flag_cnt == 3) break;
            }
        } else {
            $sql = "SELECT athlete_name, athlete_country, record_order, record_official_record, athlete_bib, record_official_result, record_new, record_multi_record, athlete_id
                                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                        WHERE record_schedule_id = ? ORDER BY record_official_result ASC";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $schedule_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = mysqli_fetch_array($result)) {
                $country[] = htmlspecialchars($row["athlete_country"]); //선수 국가
                
                $flag_cnt++;
                if ($flag_cnt == 3) break;
            }
        }
    } else { //트랙, 던지기, 점프
        while ($row = mysqli_fetch_array($result)) {
            $country[] = htmlspecialchars($row["athlete_country"]); //선수 국가
                
            $flag_cnt++;
            if ($flag_cnt == 3) break;
        }
    }
    ?>
<link rel="stylesheet" href="/assets/css/animate.css">
<link rel="stylesheet" href="/assets/css/award_reset.css">
<link rel="stylesheet" href="/assets/css/award_ceremony.css?ver=1">
<script src="/assets/js/jquery-1.12.4.min.js"></script>
</head>

<body>
    <div class="awardCeremony">
        <div class="flag-container first">
            <canvas id="flag1" class="flag"></canvas>
        </div>

        <div class="flag-container second">
            <canvas id="flag2" class="flag"></canvas>
        </div>

        <div class="flag-container third">
            <canvas id="flag3" class="flag"></canvas>
        </div>

        <div class="banner-container">
            <div class="banner-element"></div>
            <div class="bannerLogoContainer">
                <div class="bannerLogo"></div>
            </div>
        </div>

        <video loop="" autoplay="" muted="">
            <source src="/assets/images/background_1.mp4" type="video/ogg">
        </video>
    </div>
    <script>
    var h = new Image();
    h.src = "/assets/images/u20_national_flag/<?= $country[0] ?>.png";
    var h2 = new Image();
    h2.src = "/assets/images/u20_national_flag/<?= $country[1] ?>.png";
    var h3 = new Image();
    h3.src = "/assets/images/u20_national_flag/<?= $country[2] ?>.png";
    </script>
    <script src="/assets/js/award.js?ver=7"></script>
</body>

</html>