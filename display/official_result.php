<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="js/main.js"></script>
    <style>
        h2 {
            text-transform: uppercase;
        }
    </style>
    <title>Official Result</title>
    <?php
    include_once $_SERVER["DOCUMENT_ROOT"] . '/database/dbconnect.php';

    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];

    if ($schedule_sports == "800m" || $schedule_sports == "1500m" || $schedule_sports == " 2000m" || $schedule_sports == " 3000m" || $schedule_sports == " 3000mSC" || $schedule_sports == " 5000m" || $schedule_sports == "10000m" || $schedule_round == "1500m" || $schedule_round == "100m" || $schedule_round == "200m" || $schedule_round == "400m" || $schedule_round == "800m" || $schedule_round == "110mh" || $schedule_round == "100mh" || $schedule_round == "discusthrow" || $schedule_round == "shotput" || $schedule_round == "javelinthrow" || $schedule_round == "polevault" || $schedule_round == "highjump" || $schedule_round == "longjump") {
        $long = 1; //동시출발종목
    } else {
        $long = 0;
    }
    if ($long == 1) {
        $page = $_GET['page'];
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
        $sql1 = "SELECT DISTINCT athlete_id, record_live_record, athlete_name
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
                    WHERE record_live_result > 0 AND schedule_id =?
                    ORDER BY record_live_result ASC";
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
    ?>
</head>

<body>
    <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]); //종목 
                                ?></h2>
    <h2 style="display:inline;"><?php echo htmlspecialchars($gender); //성별 
                                ?></h2>
    <h2 style="display:inline;"><?php echo htmlspecialchars($schedule_round); //라운드 
                                ?></h2>
    <h3>Official Result</h3>
    <?php
    if ($sports == 1) {
        echo '<p>풍속: ' . htmlspecialchars($row2["record_wind"]) . 'm/s</p>';
    } //짧은 트랙경기에 표시할 풍속
    if ($round == 0) {
    ?>
        <p>WR: <?php echo $row4['worldrecord_record']; //세계기록 
                ?></p>
    <?php
    } else {
        echo '<p>WR: -</p>';
    }
    ?>
    <table>
        <colgroup>
            <col style="width: auto;" />
            <?php if ($relay == 0) { ?>
                <col style="width: auto;" />
            <?php } ?>
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <?php
            if ($sports == 2) { //멀리뛰기, 세단뛰기 풍속
                echo '<col style="width: auto;"/>';
            } else if ($sports == 0) { //던지기
                echo '<col style="width: auto;"/>';
            }
            if ($round == "1") {
                '<col style="width: auto;"/>';
                '<col style="width: auto;"/>';
            }
            ?>
            <col style="width: auto;" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col">순위</th>
                <?php if ($relay == 0) { ?>
                    <th scope="col">성명</th>
                <?php } ?>
                <th scope="col">국가</th>
                <th scope="col">사진</th>
                <th scope="col">레인</th>
                <th scope="col">기록</th>
                <?php
                if ($sports == 2) { //멀리뛰기, 세단뛰기 풍속
                    echo '<th scope="col">풍속</th>';
                } else if ($sports == 0) { //던지기
                    echo '<th scope="col">용기구</th>';
                }
                if ($round == "1") {
                    echo '<th scope="col">POINT</th>';
                    echo '<th scope="col">종합점수</th>';
                }
                ?>
                <th scope="col">비고</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cnt = 0;
            if ($schedule_sports == "4x400mR" || $schedule_sports == "4x100mR") { //릴레이
                while ($row = mysqli_fetch_array($result)) {
                    if ($cnt == 0) {
                        echo '<tr>';
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_result"]) . "</td>"; //순위(official Result)
                        $cnt++;
                    } else if ($cnt == 3) {
                        echo "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>국기</tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_record"]) . "</td>"; //official record
                        //신기록
                        $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_official_record'] . "'");
                        $row6 = mysqli_fetch_array($sql0);
                        if (!empty($row6)) {
                            if ($row6['worldrecord_athletics'] == 'w') {
                                echo "<td style='text-align: center;'>WR</td>";
                            } else if ($row6['worldrecord_athletics'] == 'a') {
                                echo "<td style='text-align: center;'>AR</td>";
                            } else if ($row6['worldrecord_athletics'] == 'u') {
                                echo "<td style='text-align: center;'>UR</td>";
                            } else if ($row6['worldrecord_athletics'] == 's') {
                                echo "<td style='text-align: center;'>SR</td>";
                            }
                        }
                        echo "</tr>";
                        $cnt = 0;
                    } else {
                        $cnt++;
                    }
                }
            } else if ($long == 1) { //페이징
                if ($round == 1) {
                    $a = $page - 1;
                    $b = $a * 8;
                    $sql = "SELECT athlete_name, athlete_country, record_order, record_official_record, athlete_bib, record_official_result, record_new, record_multi_record, athlete_id
                                                FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                                WHERE record_schedule_id = ? AND record_multi_record > 0 ORDER BY record_official_result ASC LIMIT ?,8";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('ss', $schedule_id, $b);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_result"]) . "</td>"; //순위(official Result)
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_name"]) . "</td>"; //선수 이름
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>국기</tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_record"]) . "</td>"; //official record
                        if ($round == "1") {
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_multi_record"]) . "</td>";
                            $sql0 = $db->query("SELECT record_live_record from list_record INNER JOIN list_schedule ON schedule_id = record_schedule_id 
                                                        AND schedule_sports = 'decathlon' AND schedule_round='final' AND schedule_division='s' AND record_athlete_id='" . $row['athlete_id'] . "'"); //기록변환점수
                            $row6 = mysqli_fetch_array($sql0);
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row6['0']) . "</td>"; //기록변환점수
                            //신기록
                            $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_official_record'] . "'");
                            $row6 = mysqli_fetch_array($sql0);
                            if (!empty($row6)) {
                                if ($row6['worldrecord_athletics'] == 'w') {
                                    echo "<td style='text-align: center;'>WR</td>";
                                } else if ($row6['worldrecord_athletics'] == 'a') {
                                    echo "<td style='text-align: center;'>AR</td>";
                                } else if ($row6['worldrecord_athletics'] == 'u') {
                                    echo "<td style='text-align: center;'>UR</td>";
                                } else if ($row6['worldrecord_athletics'] == 's') {
                                    echo "<td style='text-align: center;'>SR</td>";
                                }
                            }
                        }
                        echo "</tr>";
                    }
                } else {
                    $a = $page - 1;
                    $b = $a * 8;
                    $sql = "SELECT athlete_name, athlete_country, record_order, record_official_record, athlete_bib, record_official_result, record_new, record_multi_record, athlete_id
                                                FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                                WHERE record_schedule_id = ? ORDER BY record_official_result ASC LIMIT ?,8";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('ss', $schedule_id, $b);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_result"]) . "</td>"; //순위(official Result)
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_name"]) . "</td>"; //선수 이름
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>국기</tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_record"]) . "</td>"; //official record
                        if ($round == "1") {
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_multi_record"]) . "</td>";
                            $sql0 = $db->query("SELECT record_live_record from list_record INNER JOIN list_schedule ON schedule_id = record_schedule_id 
                                                        AND schedule_sports = 'decathlon' AND schedule_round='final' AND schedule_division='s' AND record_athlete_id='" . $row['athlete_id'] . "'"); //기록변환점수
                            $row6 = mysqli_fetch_array($sql0);
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row6['0']) . "</td>"; //기록변환점수
                            //신기록
                            $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_official_record'] . "'");
                            $row6 = mysqli_fetch_array($sql0);
                            if (!empty($row6)) {
                                if ($row6['worldrecord_athletics'] == 'w') {
                                    echo "<td style='text-align: center;'>WR</td>";
                                } else if ($row6['worldrecord_athletics'] == 'a') {
                                    echo "<td style='text-align: center;'>AR</td>";
                                } else if ($row6['worldrecord_athletics'] == 'u') {
                                    echo "<td style='text-align: center;'>UR</td>";
                                } else if ($row6['worldrecord_athletics'] == 's') {
                                    echo "<td style='text-align: center;'>SR</td>";
                                }
                            }
                        }
                        echo "</tr>";
                    }
                }
            } else { //트랙, 던지기, 점프
                while ($row = mysqli_fetch_array($result)) {
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_result"]) . "</td>"; //순위(official Result)
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_name"]) . "</td>"; //선수 이름
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                    echo "<td style='text-align: center;'>국기</tb>";
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_official_record"]) . "</td>"; //official record
                    if ($sports == 2) { //멀리뛰기, 세단뛰기-풍속
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_wind"]) . "m/s</td>"; //풍속
                    } else if ($sports == 0) {
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_weight"]) . "</td>"; //용기구
                    }
                    //신기록
                    $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                            FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                            AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_official_record'] . "'");
                    $row6 = mysqli_fetch_array($sql0);
                    if (!empty($row6)) {
                        if ($row6['worldrecord_athletics'] == 'w') {
                            echo "<td style='text-align: center;'>WR</td>";
                        } else if ($row6['worldrecord_athletics'] == 'a') {
                            echo "<td style='text-align: center;'>AR</td>";
                        } else if ($row6['worldrecord_athletics'] == 'u') {
                            echo "<td style='text-align: center;'>UR</td>";
                        } else if ($row6['worldrecord_athletics'] == 's') {
                            echo "<td style='text-align: center;'>SR</td>";
                        }
                    }
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <?php
    if ($long == 1) {
        if ($num > 1) {
            for ($i = 0; $i < $num; $i++) {
                $j = $i + 1; ?>
                <button type="button" onclick="location.href='./track_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&page=<?php echo $j; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>'">page
                    <?php echo $j; ?></button>
        <?php }
        }
    }
    echo '</br>';
    if ($round == 1) { ?>
        <form action="decathlon.php" method="GET" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?= $_GET['schedule_id'] ?>>
            <input type=hidden name=page value=1>
            <input type="submit" value="총합점수" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
    <?php }
    ?>
    <form action="live_result.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
        <input type=hidden name=schedule_id value=<?= $_GET['schedule_id'] ?>>
        <input type=hidden name=page value=1>
        <input type="submit" value="이전으로" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <form action="schedule_id_insert.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
        <input type="submit" value="처음으로" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <?php
    if ($schedule_round == "final") { ?>
        <form action="medal.php" method="GET" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?= $_GET['schedule_id'] ?>>
            <input type=hidden name=page value=3>
            <input type="submit" value="Medal" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
    <?php } ?>
</body>

</html>