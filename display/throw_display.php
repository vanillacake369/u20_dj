<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        h2 {
            text-transform: uppercase;
        }
    </style>
    <title>Throw</title>
    <?php
    include_once $_SERVER["DOCUMENT_ROOT"] . '/database/dbconnect.php';

    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];
    $trial = $_GET['trial'];

    //종합경기->POINT
    if ($schedule_round == "shotput" || $schedule_round == "discusthrow" || $schedule_round == "javelinthrow") {
        $round = "1"; //10종경기(종합경기)
    } else {
        $round = 0;
    }

    if ($schedule_round == "discusthrow" || $schedule_round == "shotput" || $schedule_round == "javelinthrow") {
        $long = 1; //종합경기 페이징
    } else {
        $long = 0;
    }
    if ($long == 1) {
        $page = $_GET['page'];
    }

    if ($long == 1) {
        //live_result, live_reocrd, 선수이름, 선수 국가, 레인, 종합점수(종합경기)
        $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new, record_weight
                FROM list_record 
                JOIN list_athlete 
                ON list_record.record_athlete_id = list_athlete.athlete_id 
                WHERE record_schedule_id = ? AND record_trial = ? ORDER BY list_record.record_order ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $schedule_id, $trial);
        $stmt->execute();
        $result = $stmt->get_result();
        //페이지 수
        $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ? AND record_trial = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $schedule_id, $trial);
        $stmt->execute();
        $result1 = $stmt->get_result();
        $count = mysqli_num_rows($result1);
        $num = $count / 8;
    } else {
        //live_result, live_reocrd, 선수이름, 선수 국가, 레인, 종합점수(종합경기)
        $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new, record_weight
                FROM list_record 
                JOIN list_athlete 
                ON list_record.record_athlete_id = list_athlete.athlete_id 
                WHERE record_schedule_id = ? AND record_trial = ? ORDER BY list_record.record_order ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $schedule_id, $trial);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    //풍속, 라운드
    $sql = "SELECT schedule_sports, list_record.record_wind, schedule_id,schedule_round
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
    $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics= 'u';"; //세계기록없어서 u20기록으로 넣음
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $schedule_sports,  $schedule_gender);
    $stmt->execute();
    $result4 = $stmt->get_result();
    $row4 = mysqli_fetch_array($result4);

    //성별 문자열로 기록
    if ($schedule_gender == "m") {
        $gender = "MEN";
    } else if ($schedule_gender == "f") {
        $gender = "WOMEN";
    }
    ?>
</head>

<body>
    <div class="all">
        <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]); //종목 
                                    ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($gender); //성별 
                                    ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($schedule_round); //라운드 
                                    ?></h2>
        <h3>Live Result</h3>
        <h4>TRIAL <?php echo $trial; ?></h4>
        <?php
        if ($round != 1) {
            echo '<p>WR: ' . $row4['worldrecord_record'] . '</p>';
        }
        ?>
        <table>
            <colgroup>
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <?php
                if ($round == "1") {
                    '<col style="width: auto;"/>';
                } else {
                    '<col style="width: auto;"/>';
                }
                ?>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">성명</th>
                    <th scope="col">국가</th>
                    <th scope="col">사진</th>
                    <th scope="col">순번</th>
                    <th scope="col">기록</th>
                    <th scope="col">용기구</th>
                    <?php
                    if ($round == "1") {
                        echo '<th scope="col">POINT</th>';
                    } else {
                        echo '<th scope="col">비고</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($long == 1) {
                    $a = $page - 1;
                    $b = $a * 8;
                    $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new, record_weight
                                    FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                    WHERE record_schedule_id = ? AND record_trial = ? ORDER BY list_record.record_order ASC LIMIT ?,8";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('sss', $schedule_id, $trial, $b);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = mysqli_fetch_array($result)) {
                        echo '<tr>';
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_name"]) . "</td>"; //선수이름
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>국기</tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_live_record"]) . "</td>"; //live record
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_weight"]) . "</td>"; //live record
                        if ($round == "1") {
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_multi_record"]) . "</td>"; //종합점수
                        }
                        //신기록
                        $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                        AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_live_record'] . "'");
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
                } else {
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<tr>';
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_name"]) . "</td>"; //선수이름
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>국기</tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_live_record"]) . "</td>"; //live record
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_weight"]) . "</td>"; //live record
                        if ($round == "1") {
                            echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_multi_record"]) . "</td>"; //종합점수
                        }
                        //신기록
                        $sql0 = $db->query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '" . $schedule_sports . "' AND worldrecord_gender = '" . $schedule_gender . "' 
                                        AND worldrecord_athlete_name = '" . $row['athlete_name'] . "' AND worldrecord_record= '" . $row['record_live_record'] . "'");
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
        $j = 1;
        if ($long == 1) {
            if ($num > 1) {
                $j = 0;
                for ($i = 0; $i < $num; $i++) {
                    $j = $i + 1; ?>
                    <button type="button" onclick="location.href='./track_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&page=<?php echo $j; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&trial=<?php echo $trial; ?>'">page
                        <?php echo $j; ?></button>
        <?php }
            }
        }
        ?>
        </br>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=1'">TRIAL
            1</button>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=2'">TRIAL
            2</button>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=3'">TRIAL
            3</button>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=4'">TRIAL
            4</button>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=5'">TRIAL
            5</button>
        <button type="button" onclick="location.href='./throw_display.php?schedule_sports=<?php echo $schedule_sports; ?>&schedule_round=<?php echo $schedule_round; ?>&schedule_group=<?php echo $schedule_group; ?>&schedule_gender=<?php echo $schedule_gender; ?>&schedule_id=<?php echo $schedule_id; ?>&page=<?php echo $j; ?>&trial=6'">TRIAL
            6</button>
        </br>
        <form action="official_result.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?= $_GET['schedule_id'] ?>>
            <?php if ($long == 1) { ?>
                <input type=hidden name=page value=1>
            <?php } ?>
            <input type="submit" value="Official Result" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="startlist.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type=hidden name=page value=1>
            <input type="submit" value="Start list" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="schedule_id_insert.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type="submit" value="처음으로" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
    </div>
</body>

</html>