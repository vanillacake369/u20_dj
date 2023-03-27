<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--Data Tables-->
    <title>U20</title>
</head>

<body>
<div class="page">
    <div class="top">
        <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
            <tr>
                <td style="width: 80%; text-align: right">제20회 예천아시아 U20 육상경기선수권대회</td>
                <td style="text-align: right"><img style="width: 80px; height: 40px" src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/assets/img/print_logo.png" ?>" alt="Logo" class="logo_img" /></td>
            </tr>
        </table>
    </div>

    <div class="middle" style="display:inline-block">
        <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
        <?php
        require_once __DIR__ . "/../action/module/record_worldrecord.php";
        require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결
        /* word 다운을 위한 해더 */
        header("Content-type: application/vnd.ms-word;charset=UTF-8");
        header("Content-Disposition: attachment; filename=word_download_test.doc");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-word; charset=utf-8\">");

        $sports = $_POST['sports'];
        $round = $_POST['round'];
        $gender = $_POST['gender'];
        $group = $_POST['group'];
        $sql = "select 
                            *,athlete_bib, athlete_country, athlete_birth, record_end, record_wind
                            from list_schedule JOIN list_athlete  JOIN list_record
                            where record_gender = schedule_gender AND schedule_sports = '$sports' AND schedule_round = '$round' AND schedule_gender = '$gender'";

        $result = $db->query($sql);
        $row = mysqli_fetch_assoc($result);
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
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">성별: ';
                echo $row['schedule_gender'] == 'm' ? 'MEN' : ($row['schedule_gender'] == 'f' ? 'WOMEN' : 'MIXED');
                echo '</p></td>';
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
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab1">
                <colgroup>
                    <col style="width: 10%" />
                    <col style="width: 10%" />
                    <col style="width: 30%" />
                    <col style="width: 10%" />
                    <col style="width: 10%" />
                    <col style="width: 15%" />
                    <col style="width: 10%" />
                    <col style="width: 15%" />
                </colgroup>
                <thead>
                <tr>
                    <th style="border-bottom: 1px solid gray">순위</th>
                    <th style="border-bottom: 1px solid gray">등번호</th>
                    <th style="border-bottom: 1px solid gray">성명</th>
                    <th style="border-bottom: 1px solid gray">국가</th>
                    <th style="border-bottom: 1px solid gray">레인</th>
                    <th style="border-bottom: 1px solid gray">기록</th>
                    <th style="border-bottom: 1px solid gray">비고</th>
                    <th style="border-bottom: 1px solid gray">신기록</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $j = 0;
                $v = 0;
                $b = 3;
                for ($i = 0; $i < count($_POST['rain']); $i++) {
                    echo '<tr>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['rank'][$i] . '</td>';
                    // echo '<td>';
                    // if ($b = 3) {
                    //     echo $row['athlete_bib'] . '<br>';
                    // }
                    // for ($a = 0; $a < $b; $a++) {
                    //     $row = mysqli_fetch_array($result);
                    //     echo $row['athlete_bib'] . '<br>';
                    // }
                    // $b = 4;
                    // echo '</td>';
                    $s = $v + 4;
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">';
                    for ($d; $v < $s; $v++) {
                        echo $_POST['playerbib'][$v] . '<br>';
                    }
                    echo '</td>';
                    $k = $j + 4;
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">';
                    for ($j; $j < $k; $j++) {
                        echo $_POST['playername'][$j] . '<br>';
                    }
                    $country = $db->query("select athlete_country from list_athlete where athlete_name ='" . $_POST['playername'][$j - 1] . "'");
                    // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                    $row1 = mysqli_fetch_array($country);
                    echo '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $row1[0] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['rain'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['gameresult'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['bigo'][$i] . '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">' . $_POST['newrecord'][$i] . '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
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
                    <th style="border-bottom: 1px solid gray">풍속</th>
                    <th style="border-bottom: 1px solid gray">성명</th>
                    <th style="border-bottom: 1px solid gray">소속</th>
                    <th style="border-bottom: 1px solid gray">일자</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($row['schedule_sports'] == 'decathlon' || $row['schedule_sports'] == 'heptathlon') {
                    $check_round = 'y';
                } else {
                    $check_round = 'n';
                }
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