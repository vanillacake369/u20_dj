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
    <title>Relay</title>
    <?php
    require_once "../backheader.php";

    $schedule_result = $_GET['schedule_result'];
    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];

    //live_result, live_reocrd, 선수이름, 선수 국가, 레인
    $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new
            FROM list_record 
            JOIN list_athlete 
            ON list_record.record_athlete_id = list_athlete.athlete_id 
            WHERE record_schedule_id = ? ORDER BY list_record.record_live_result ASC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    //풍속, 라운드
    $sql = "SELECT schedule_sports, list_record.record_wind, schedule_id, schedule_round
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
    $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics= 'w';";
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
    } else if ($schedule_gender == "c") {
        $gender = "MIXED";
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
        <!-- <p>풍속:<?php echo htmlspecialchars($row2["record_wind"]); ?>m/s</p> -->
        <p>WR:<?php echo $row4['worldrecord_record']; //세계기록 
                ?></p>
        <table>
            <colgroup>
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
                <col style="width: auto;" />
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">순위</th>
                    <th scope="col">이름</th>
                    <th scope="col">국가</th>
                    <th scope="col">사진</th>
                    <th scope="col">레인</th>
                    <th scope="col">기록</th>
                    <th scope="col">비고</th>
                    </br>
                </tr>
            </thead>
            <tbody>
                <?php
                $cnt = 0;

                while ($row = mysqli_fetch_array($result)) {
                    if ($cnt == 0) {
                        echo '<tr>';
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_live_result"]) . "</td>"; //순위(Live Result)
                        echo "<td style='text-align: center;'>";
                        echo $row["athlete_name"] . '<br>';
                        $cnt++;
                    } else if ($cnt == 3) {
                        echo $row["athlete_name"] . '<br>';
                        echo "</td>"; //선수 국가
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["athlete_country"]) . "</td>"; //선수 국가
                        echo "<td style='text-align: center;'><img src='/assets/images/u20_national_flag/" . htmlspecialchars($row["athlete_country"]) . ".png'/></tb>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_order"]) . "</td>"; //레인
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row["record_live_record"]) . "</td>"; //live record
                        if ($row["record_new"] == "y") {
                            echo "<td style='text-align: center;'>WR</td>";
                        }
                        echo "</tr>";
                        $cnt = 0;
                    } else {
                        echo $row["athlete_name"] . '<br>';
                        $cnt++;
                    }
                    // echo "</br>";      
                }
                ?>
            </tbody>

        </table>
        <form action="official_result.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?= $_GET['schedule_id'] ?>>
            <input type="submit" value="Official Result" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="startlist.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?= $_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?= $_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?= $_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?= $_GET['schedule_group'] ?>>
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