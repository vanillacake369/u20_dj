<?php

require_once "includes/auth/config.php";
require_once "security/security.php";
require_once 'action/module/schedule_worldrecord.php';
require_once "backheader.php";

$schedule_result = $_GET['schedule_result'];
$schedule_sports = $_GET['schedule_sports'];
$schedule_gender = $_GET['schedule_gender'];
$schedule_round = $_GET['schedule_round'];
$schedule_group = $_GET['schedule_group'];
$schedule_name = $_GET['schedule_name'];
$schedule_id = $_GET['schedule_id'];

$gender = "";
if ($schedule_sports == "800m" || $schedule_sports == "1500m" || $schedule_sports == " 2000m" || $schedule_sports == " 3000m" || $schedule_sports == " 3000mSC" || $schedule_sports == " 5000m" || $schedule_sports == "10000m" || $schedule_round == "1500m" || $schedule_round == "100m" || $schedule_round == "200m" || $schedule_round == "400m" || $schedule_round == "800m" || $schedule_round == "110mh" || $schedule_round == "100mh") {
    $long = 1; //종합경기 페이징
} else {
    $long = 0;
}
if ($long == 1) {
    $page = $_GET['page'];
}

if ($long == 1) {
    //페이지 수
    $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $schedule_id);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $count = mysqli_num_rows($result1);
    $num = $count / 8;
} else {
    //순위, 선수이름, 국가, 레인, 기록, 종합점수(종합경기), 등번호
    $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new, athlete_bib
                FROM list_record 
                JOIN list_athlete 
                ON list_record.record_athlete_id = list_athlete.athlete_id 
                WHERE record_schedule_id = ? ORDER BY list_record.record_live_result ASC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

//풍속, 라운드
$sql = "SELECT schedule_sports, record_wind, schedule_id, schedule_round
                    FROM list_record 
                    JOIN list_schedule 
                    ON list_record.record_schedule_id = list_schedule.schedule_id 
                    WHERE record_schedule_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $schedule_id);
$stmt->execute();
$result2 = $stmt->get_result();
$row2 = mysqli_fetch_array($result2);

//종목(한글)
//$schedule_sports = $row2["schedule_sports"];
$sql = "SELECT list_sports.sports_name FROM list_sports WHERE sports_code = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $schedule_sports);
$stmt->execute();
$result3 = $stmt->get_result();
$row3 = mysqli_fetch_array($result3);

//세계기록(WR)
$sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics = 'w'";
$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $schedule_sports,  $schedule_gender);
$stmt->execute();
$result4 = $stmt->get_result();
$row4 = mysqli_fetch_array($result4);

//성별 문자열로 기록
if ($schedule_gender == "m") {
    global $gender;
    $gender = "MEN";
} else if ($schedule_gender == "f") {
    global $gender;
    $gender = "WOMEN";
} else if ($schedule_gender == "c") {
    global $gender;
    $gender = "MIXED";
}

//100m, 100mh, 110mh, 200m ->풍속
if ($schedule_sports == "100m" || $schedule_sports == "100mh" || $schedule_sports == "110mh" || $schedule_sports == "200m") {
    $sports = 1;
} else {
    $sports = 0;
}

//종합경기 ->POINT
if ($schedule_round == "100m" || $schedule_round == "100mh" || $schedule_round == "110m" || $schedule_round == "110mh" || $schedule_round == "200m" || $schedule_round == "400m" || $schedule_round == "400mh" || $schedule_round == "800m" || $schedule_round == "1500m") {
    $round = 1; //10종경기(종합경기)
} else {
    $round = 0;
}
?>
<link rel="stylesheet" href="assets/css/electronic_reset.css">
<link rel="stylesheet" href="assets/css/electronic_display.css">
<link rel="stylesheet" href="assets/css/animate.css">
<title>electronic display</title>

</head>

<body>
    <div class="container">
        <div class="titleBox">
            <div class="titleArea">
                <p class="title">
                    <?= $schedule_gender == 'm' ? "Men's" : ($schedule_gender == 'f' ? "Women's" : "Mixed") ?> </p>
                <p class="title"><?= $schedule_name ?></p>
                <p class="recordType">
                    <?= $schedule_result == 'o' ? 'Official Result' : ($schedule_result == 'l' ? 'Live Result' : 'Start List') ?>
                </p>
            </div>
            <p class="round">1st Round</p>
            <p class="roundDetail">1 / 1</p>
            <p class="windSpeed">Wind Speed</p>
            <p class="windSpeedValue">
                <? if (isset($row2["record_wind"])) echo htmlspecialchars($row2["record_wind"]); else echo "-";  ?>m/s
            </p>
            <p class="page">Page 1 / 1</p>
            <?php
            echo '<div class="pictogram" style="background: url(assets/images/U20_pictogram/';
            echo $schedule_gender == 'f' ? "female/" : ($schedule_gender == 'm' ? "male/" : "mixed/");
            echo $schedule_sports . '.png) 0% 0% / contain no-repeat;"></div>';
            ?>

        </div>
        <ul class="rankingBox">
            <?php
            if ($long == 0) {
                while ($row = mysqli_fetch_array($result)) {
                    echo "<li class='rankingItem'>";
                    echo "<div class='rankingItemWrapper'>";
                    echo "<span class='wrapperLank'>" . htmlspecialchars($row["record_live_result"]) . "</span>"; //순위(Live Result)
                    echo "<span>" . htmlspecialchars($row["athlete_name"]) . "</span>"; //선수 이름
                    $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                        AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_live_record'] . "'");
                    $row6 = mysqli_fetch_array($sql0);
                    echo "<span>";
                    if (!empty($row6)) {
                        if ($row6['worldrecord_athletics'] == 'w') {
                            echo "<span class='CR'>WR</span>";
                        } else if ($row6['worldrecord_athletics'] == 'a') {
                            echo "<span class='CR'>AR</span>";
                        } else if ($row6['worldrecord_athletics'] == 'u') {
                            echo "<span class='CR'>UR</span>";
                        } else if ($row6['worldrecord_athletics'] == 's') {
                            echo "<span class='CR'>SR</span>";
                        }
                    }
                    echo htmlspecialchars($row["record_live_record"]) . "</span>"; //live record
                    if ($round == "1") {
                        echo "<span>" . htmlspecialchars($row["record_multi_record"]) . "</span>"; //기록변환점수
                    }
                    echo '<div style="background-image:url(/assets/images/u20_national_flag/' . htmlspecialchars($row["athlete_country"]) . '.png); background-repeat: no-repeat; background-size: cover;"></div>';
                    echo "</div></li>";

                    //신기록

                }
            } else {
                $a = $page - 1;
                $b = $a * 8;
                $sql = "SELECT athlete_name, athlete_country, record_order, record_live_record, athlete_bib, record_live_result, record_new, record_multi_record
                            FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                            WHERE record_schedule_id = ? ORDER BY record_live_result ASC LIMIT ?,8";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('ss', $schedule_id, $b);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = mysqli_fetch_array($result)) {
                    echo "<li class='rankingItem'>";
                    echo "<div class='rankingItemWrapper'>";
                    echo "<span class='wrapperLank'>" . htmlspecialchars($row["record_live_result"]) . "</span>"; //순위(Live Result)
                    echo "<span>" . htmlspecialchars($row["athlete_bib"]) . "</span>"; //등번호
                    echo "<span>" . htmlspecialchars($row["athlete_name"]) . "</span>"; //선수 이름
                    $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                        AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_live_record'] . "'");
                    $row6 = mysqli_fetch_array($sql0);
                    if (!empty($row6)) {
                        if ($row6['worldrecord_athletics'] == 'w') {
                            echo "<span><span class='CR'>WR</span>";
                        } else if ($row6['worldrecord_athletics'] == 'a') {
                            echo "<span><span class='CR'>AR</span>";
                        } else if ($row6['worldrecord_athletics'] == 'u') {
                            echo "<span><span class='CR'>UR</span>";
                        } else if ($row6['worldrecord_athletics'] == 's') {
                            echo "<span><span class='CR'>SR</span>";
                        }
                    }
                    echo htmlspecialchars($row["record_live_record"]) . "</span>"; //live record
                    if ($round == "1") {
                        echo "<span>" . htmlspecialchars($row["record_multi_record"]) . "</span>"; //기록변환점수
                    }
                    echo "<div style='background-image:url(/assets/images/u20_national_flag/" . htmlspecialchars($row["athlete_country"]) . ".png ) 0% 0% / cover no-repeat;' ></div>";
                    echo "</div></li>";
                }
            }
            ?>
        </ul>

        <img class="containerImg" src="assets/images/default_display.png">
    </div>

    <script src="assets/js/electronic.js?ver=1"></script>
</body>

</html>