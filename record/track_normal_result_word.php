<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>U20</title>
</head>

<body>
    <div class="page">
        <div class="top">
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                <tr>
                    <td style="width: 80%; text-align: right">제20회 예천아시아 U20 육상경기선수권대회</td>
                    <td style="text-align: right"><img style="width: 80px; height: 40px"
                            src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/assets/img/print_logo.png" ?>"
                            alt="Logo" class="logo_img" /></td>
                </tr>
            </table>
        </div>
        <?php
        require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결 
        require_once __DIR__ . "/../action/module/record_worldrecord.php";
        $sports = $_POST['sports'];
        $round = $_POST['round'];
        $gender = $_POST['gender'];
        $group = $_POST['group'];$schedule_result = $_POST['result'];
        switch ($schedule_result) {
            case 'l':
                $schedule_result = "Live Result";
                break;
            case 'o':
                $schedule_result = "Official Result";
                break;
            case 'n':
                $schedule_result = "Not Start";
                break;
        }


        $FILE_NAME = $sports . '_' . $gender . '_' . $round . '_' . $group . 'group(' . $schedule_result . ').doc';
        /* word 다운을 위한 해더 */
        header("Content-type: application/vnd.ms-word;charset=UTF-8");
        header("Content-Disposition: attachment; filename=" . $FILE_NAME);
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-word; charset=utf-8\">");



        $sql = "select *, record_wind, record_end from list_schedule inner join list_record ON (schedule_sports = record_sports)
                where record_gender = schedule_gender AND schedule_sports = '$sports' AND schedule_round = '$round' AND schedule_gender = '$gender'";
        $result = $db->query($sql);
        $row = mysqli_fetch_assoc($result);
        if ($row['schedule_sports'] == 'decathlon' || $row['schedule_sports'] == 'heptathlon') {
            $check_round = 'y';
        } else {
            $check_round = 'n';
        }
        ?>
        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                <tr>
                    <?php
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">종목명: ' . $row['schedule_name'] . '</p></td>';
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">위치: ' . $row['schedule_location'] . '</p></td>';
                    ?>
                </tr>
                <tr>
                    <?php
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">성별: ' . $row['schedule_gender'] . '</p></td>';
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">일자: ' . $row['schedule_date'] . '</p></td>';
                    ?>
                </tr>
                <tr>
                    <?php
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">라운드: ' . $row['schedule_round'] . '</p></td>';
                    echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">풍속: ' . $row['record_wind'] . 'm/s</p></td>';
                    ?>
                </tr>
            </table>
            <div class="table_area" style="margin-bottom: 50px;">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab2"
                    style="border-top: 2px solid black">
                    <colgroup>
                        <?php
                        if ($check_round == 'y') {
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 10%" />';
                            echo '<col style="width: 20%" />';
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 10%" />';
                            echo '<col style="width: 13%" />';
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 13%" />';
                            echo '<col style="width: 7%" />';
                            echo '<col style="width: 7%" />';
                        } else {
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 6%" />';
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 20%" />';
                            echo '<col style="width: 7%" />';
                            echo '<col style="width: 13%" />';
                            echo '<col style="width: 17%" />';
                            echo '<col style="width: 5%" />';
                            echo '<col style="width: 22%" />';
                        }
                        ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid gray">순위</th>
                            <th style="border-bottom: 1px solid gray">등번호</th>
                            <th style="border-bottom: 1px solid gray">레인</th>
                            <th style="border-bottom: 1px solid gray">성명</th>
                            <th style="border-bottom: 1px solid gray">국가</th>
                            <th style="border-bottom: 1px solid gray">생년월일</th>
                            <th style="border-bottom: 1px solid gray">기록</th>
                            <th style="border-bottom: 1px solid gray">비고</th>
                            <th style="border-bottom: 1px solid gray">신기록</th>
                            <?php
                            if ($check_round == 'y') {
                                echo '<th style="border-bottom: 1px solid gray">점수</th>';
                                echo '<th style="border-bottom: 1px solid gray">종합 점수</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($_POST['rank']); $i++) {
                            $country = $db->query("select athlete_country,athlete_id,athlete_bib,athlete_birth from list_athlete where athlete_name ='" . $_POST['playername'][$i] . "'");
                            $row1 = mysqli_fetch_array($country);
                            // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                            echo '<tr>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['rank'][$i] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $row1[2] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['rain'][$i] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['playername'][$i] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $row1[0] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $row1[3] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult'][$i] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['bigo'][$i] . '</td>';
                            echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['newrecord'][$i] . '</td>';
                            if ($check_round == 'y') {
                                $point = $db->query("SELECT record_multi_record from list_record where record_athlete_id ='$row1[1]' and record_schedule_id=" . $_POST['schedule_id'] . "");
                                $pointrow = mysqli_fetch_array($point);
                                $totalid = $db->query("select schedule_id from list_schedule where schedule_name='" . $_POST['gamename'] . "' and schedule_round='final' and schedule_division='s'");
                                $totalrow = mysqli_fetch_array($totalid);
                                $totalpoint = $db->query("SELECT record_live_record from list_record where record_athlete_id ='$row1[1]' and record_schedule_id=$totalrow[0]");
                                $totalrow1 = mysqli_fetch_array($totalpoint);
                                echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $pointrow[0] . '</td>';
                                echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $totalrow1[0] . '</td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="total">
            <p>종합신기록</p>
            <div class="table_area">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab2"
                    style="border-top: 2px solid black">
                    <colgroup>
                        <col style="width: 20%" />
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 30%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid gray">구분</th>
                            <th style="border-bottom: 1px solid gray">기록</th>
                            <th style="border-bottom: 1px solid gray">풍속</th>
                            <th style="border-bottom: 1px solid gray">성명</th>
                            <th style="border-bottom: 1px solid gray">소속</th>
                            <th style="border-bottom: 1px solid gray">일자</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $world = check_worldrecord($sports, $gender, $round, $check_round, $row['record_end']);
                        foreach ($world as $k) {
                            echo '<tr>';
                            switch ($k['athletics']) {
                                case 'w':
                                    echo "<td style='border-bottom: 1px solid gray'>WR</td>";
                                    break;
                                case 'u':
                                    echo "<td style='border-bottom: 1px solid gray'>UWR</td>";
                                    break;
                                case 's':
                                    echo "<td style='border-bottom: 1px solid gray'>AR</td>";
                                    break;
                                case 'a':
                                    echo "<td style='border-bottom: 1px solid gray'>UWR</td>";
                                    break;
                                case 'c':
                                    echo "<td style='border-bottom: 1px solid gray'>CR</td>";
                                    break;
                                default:
                                    echo "<td style='border-bottom: 1px solid gray'></td>";
                                    break;
                            }
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . restoreresult($k['record']) . '</td>';
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k['wind'] . '</td>';
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k['athlete_name'] . '</td>';
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k['country_code'] . '</td>';
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k['datetime'] . '</td>';
                            echo '</tr>';
                            if (array_key_exists("0", $k)) {
                                for ($j = 0; $j < count($k) - 6; $j++) {
                                    echo '<tr>';
                                    switch ($k[$j]['worldrecord_athletics']) {
                                        case 'w':
                                            echo "<td style='border-bottom: 1px solid gray'>WR</td>";
                                            break;
                                        case 'u':
                                            echo "<td style='border-bottom: 1px solid gray'>UWR</td>";
                                            break;
                                        case 's':
                                            echo "<td style='border-bottom: 1px solid gray'>AR</td>";
                                            break;
                                        case 'a':
                                            echo "<td style='border-bottom: 1px solid gray'>UAR</td>";
                                            break;
                                        case 'c':
                                            echo "<td style='border-bottom: 1px solid gray'>CR</td>";
                                            break;
                                        default:
                                            echo "<td style='border-bottom: 1px solid gray'></td>";
                                            break;
                                    }
                                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . restoreresult($k[$j]['worldrecord_record']) . '</td>';
                                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k[$j]['worldrecord_wind'] . '</td>';
                                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k[$j]['worldrecord_athlete_name'] . '</td>';
                                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k[$j]['worldrecord_country_code'] . '</td>';
                                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k[$j]['worldrecord_datetime'] . '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>