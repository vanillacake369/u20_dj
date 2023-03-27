<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
<div class="page">
    <div class="top">
        <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
            <tr>
                <td style="width: 80%; text-align: right">제20회 예천아시아 U20 육상경기선수권대회</td>
                <td style="text-align: right">
                    <img style="width: 80px; height: 40px" src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/assets/img/print_logo.png" ?>" alt="Logo" class="logo_img" />
                </td>
            </tr>
        </table>
    </div>

    <div class="middle" style="display:inline-block">
        <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
        <?php
        require_once __DIR__ . "/../action/module/record_worldrecord.php";
        require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결
        global $db;

        $sports = $_POST['sports'];
        $round = $_POST['round'];
        $gender = $_POST['gender'];
        $group = $_POST['group'];
        $schedule_result = $_POST['result'];

        $FILE_NAME = $sports . '_' . $gender . '_' . $round . '_' . $group . 'group(' . $schedule_result . ').doc';
        /* word 다운을 위한 해더 */
        header("Content-type: application/vnd.ms-word;charset=UTF-8");
        header("Content-Disposition: attachment; filename=word_download_test.doc");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-word; charset=utf-8\">");

        $sql = "SELECT DISTINCT * FROM list_record  join list_schedule 
                    where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' 
                     and record_sports = schedule_sports and record_round = schedule_round and record_gender = schedule_gender";
        $result=$db->query($sql);
        $row = mysqli_fetch_assoc($result);
        if ($row['schedule_sports'] == 'decathlon' || $row['schedule_sports'] == 'heptathlon') {
            $check_round = 'y';
        } else {
            $check_round = 'n';
        }
        ?>
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
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em"></p></td>';
                ?>
            </tr>
        </table>
        <div class="table_area" style="margin-bottom: 20px;">
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab1">
                <colgroup>
                    <col style="width: 5%" />
                    <col style="width: 5%" />
                    <col style="width: 13%" />
                    <col style="width: 7%" />
                    <col style="width: 10%" />
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 13%" />
                </colgroup>
                <thead>
                <tr>
                    <th style="border-bottom: 1px solid gray"  rowspan="2">순위</th>
                    <th style="border-bottom: 1px solid gray"  rowspan="2">등번호</th>
                    <th style="border-bottom: 1px solid gray"  rowspan="2">성명</th>
                    <th style="border-bottom: 1px solid gray"  rowspan="2">국가</th>
                    <th style="border-bottom: 1px solid gray"  rowspan="2">출생년도</th>
                    <th style="border-bottom: 1px solid gray">1차시기</th>
                    <th style="border-bottom: 1px solid gray">2차시기</th>
                    <th style="border-bottom: 1px solid gray">3차시기</th>
                    <th style="border-bottom: 1px solid gray">4차시기</th>
                    <th style="border-bottom: 1px solid gray">5차시기</th>
                    <th style="border-bottom: 1px solid gray">6차시기</th>
                    <th style="border-bottom: 1px solid gray">기록</th>
                    <th style="border-bottom: 1px solid gray">비고</th><td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">
                </tr>
                <tr>
                    <th style="border-bottom: 1px solid gray" colspan="7">바람</th>
                    <th style="border-bottom: 1px solid gray">신기록</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i = 0; $i < count($_POST['rank']); $i++) {
                    $country = $db->query("select athlete_bib, athlete_country, athlete_birth from list_athlete INNER JOIN list_record ON athlete_id = record_athlete_id where athlete_name ='" . $_POST['playername'][$i] . "'");
                    // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                    $row1 = mysqli_fetch_array($country);
                    echo '<tr>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">' . $_POST['rank'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">' . $row1[0] . "</td>";
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">' . $_POST['playername'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">' . $row1[1] . "</td>";
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray" rowspan="2">' . $row1[2] . "</td>";
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult1'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult2'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult3'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult4'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult5'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult6'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">';
                    if ($_POST['bigo'][$i] == '') {
                        echo '&nbsp;';
                    } else {
                        echo $_POST['bigo'][$i];
                    }
                    echo '</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind1'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind2'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind3'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind4'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind5'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['wind6'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['lastwind'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . (mb_strlen($_POST['newrecord'][$i]) > 0 ? $_POST['newrecord'][$i] : '&nbsp;') . '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody><th style="border-bottom: 1px solid gray">
            </table>
        </div>
    </div>
    <div>
        <p style="margin:0px 30px 0px 0px; text-align:right;">심판 서명 :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(인)</p>
    </div>
    <div class="total">
        <p>종합신기록</p>
        <div class="table_area">
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab2">
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
                    <th style="border-bottom: 1px solid gray">바람</th>
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
                            echo "<td style='border-bottom: 1px solid gray'>UAR</td>";
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