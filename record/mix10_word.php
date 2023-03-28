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
                <td style="text-align: right">
                    <img style="width: 80px; height: 40px" src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/assets/img/print_logo.png" ?>" alt="Logo" class="logo_img" />
                </td>
            </tr>
        </table>
    </div>
    <?php
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../action/module/schedule_worldrecord.php";
    require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결
    global $db;

    $sports = $_POST['sports'];
    $round = $_POST['round'];
    $gender = $_POST['gender'];
    $group = $_POST['group'];
    $sql = "select *, record_wind, record_end from list_schedule inner join list_record ON (schedule_sports = record_sports)
                where record_gender = schedule_gender AND schedule_sports = '$sports' AND schedule_round = 'final' AND schedule_gender = '$gender'";
    $result = $db->query($sql);
    $row = mysqli_fetch_assoc($result);
    if ($row['record_status'] == 'o') {
        $result_type = 'official';
    } else if ($row['record_status'] == 'l') {
        $result_type = 'live';
    } else {
        $result_type = 'live';
    }
    $schedule_result = '';
    switch ($row['record_status']) {
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

    $FILE_NAME = $sports . '_' . $gender . '_' . $group . 'group(' . $schedule_result . ').doc';
    /* word 다운을 위한 해더 */
        header("Content-type: application/vnd.ms-word;charset=UTF-8");
        header("Content-Disposition: attachment; filename=". $FILE_NAME);
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-word; charset=utf-8\">");

    $country = $db->query("select athlete_name,athlete_country,athlete_id,athlete_bib,athlete_birth from list_athlete join list_record where athlete_id=record_athlete_id and record_sports='$sports' and record_gender='$gender' and record_round='final'");
    $count=mysqli_num_rows($country);
    $check_round = 'n';
    ?>
    <div class="middle" style="display:inline-block">
        <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
        <div style="width: 100%; display: flex;">
            <?php
            echo '<p style="font-size:12px; width:330px">종목명: ' . $row['schedule_name'] . '</p>';
            echo '<p style="font-size:12px; width:330px">라운드: ' . $row['schedule_round'] . '</p>';
            ?>
        </div>
        <div class="table_area" style="margin-bottom: 50px;">
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                <thead>
                <tr>
                    <th style="font-size:8px;">순위</th>
                    <th style="font-size:8px;">등번호</th>
                    <th style="font-size:8px;">이름</th>
                    <th style="font-size:8px;">국가</th>
                    <th style="font-size:8px;">총점</th>
                    <th style="font-size:8px;">100m</th>
                    <th style="font-size:8px;">long<br>jump</th>
                    <th style="font-size:8px;">shot<br>put</th>
                    <th style="font-size:8px;">high<br>jump</th>
                    <th style="font-size:8px;">400m</th>
                    <th style="font-size:8px;">100mh</th>
                    <th style="font-size:8px;">discus<br>throw</th>
                    <th style="font-size:8px;">pole<br>vault</th>
                    <th style="font-size:8px;">javelin<br>throw</th>
                    <th style="font-size:8px;">1500m</th>
                    <th style="font-size:8px;">비고</th>
                    <th style="font-size:8px;">신기록</th>
                </tr>
                </thead>
                <?php
                $i = 1;
                $num = 0;
                $count = 0; //신기록시 셀렉트 박스 찾는 용도
                $people = 0;
                $table_count = 0;
                while ($row1 = mysqli_fetch_array($country)) {
                    $num++;
                    echo '<tbody class="table_tbody De_tbody entry_table';
                    if ($num % 2 == 0) echo ' Ranklist_Background">'; else echo "\">";
                    echo "<tr>";
                    echo "<td style='border-bottom: 1px solid gray' rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
                    echo "<td style='border-bottom: 1px solid gray' rowspan='4'>" . htmlspecialchars($row1['athlete_bib']) . "</td>";
                    echo "<td style='border-bottom: 1px solid gray' rowspan='4'>" . htmlspecialchars($row1['athlete_name']) . "</td>";
                    echo "<td style='border-bottom: 1px solid gray' rowspan='4'>" . htmlspecialchars($row1['athlete_country']) . "</td>";
                    echo "<td style='border-bottom: 1px solid gray' rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";
                    echo "</tr>";
                    echo "<tr>";

                    //@Potatoeunbi
                    //해당 경기의 모든 종목들 record 가져오는 sql문
                    $multi = "SELECT distinct r.record_multi_record, r.record_" . $result_type . "_record, r.record_wind from list_record AS r 
                                            join list_schedule AS s
                                            JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
                                            WHERE schedule_sports='$sports' and schedule_gender ='$gender' AND record_sports=schedule_sports AND record_gender=schedule_gender
                                            AND r.record_multi_record is not NULL AND record_live_result>0
                                            and athlete_id = '" . $row1['athlete_id'] . "' 
                                            ORDER BY FIELD(schedule_round, '100m', 'longjump', 'shotput','highjump','400m','110mh','discusthrow','polevault','javelinthrow','1500m'), athlete_name;";
                    $answer = $db->query($multi);
                    while ($sub = mysqli_fetch_array($answer)) {
                        echo "<td style='border-bottom: 1px solid gray'>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
                        $table_count++;
                    }
                    for ($i = 0; $i < (10-$table_count); $i++)
                    {
                        echo "<td style='border-bottom: 1px solid gray'>&nbsp</td>";
                    }

                    echo "<td style='border-bottom: 1px solid gray'>" . htmlspecialchars($row['record_memo']) . "</td>";

                    echo "</tr>";
                    echo "<tr>";
                    $answer = $db->query($multi);
                    while ($sub = mysqli_fetch_array($answer)) {
                        echo "<td style='border-bottom: 1px solid gray'>" . htmlspecialchars($sub['record_' . $result_type . '_record']) . "</td>";
                    }
                    for ($i = 0; $i < (10-$table_count); $i++)
                    {
                        echo "<td style='border-bottom: 1px solid gray'>&nbsp</td>";
                    }
                    //@Potatoeunbi
                    //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                    //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                    if ($row['record_' . $result_type . '_record']) world($db, $row['athlete_name'], $row['record_new'], $row['schedule_sports'], $row['record_' . $result_type . '_record']);

                    echo "</tr>";
                    echo "<tr>";
                    $answer = $db->query($multi);
                    while ($sub = mysqli_fetch_array($answer)) {
                        echo "<td style='border-bottom: 1px solid gray'>" . htmlspecialchars($sub['record_wind'] == null ? ' ' : $sub['record_wind']) . "</td>";
                    }
                    for ($i = 0; $i < (10-$table_count); $i++)
                    {
                        echo "<td style='border-bottom: 1px solid gray'>&nbsp</td>";
                    }
                    echo "</tr></tbody>";
                    $table_count = 0;
                    $people++;
                } ?>
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
                $world = check_worldrecord('decathlon','m','final', $check_round, $row['schedule_start']);
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
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' . $k['record'] . '</td>';
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
                                    echo "<td style='border-bottom: 1px solid gray'>&nbsp</td>";
                                    break;
                            }
                            echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">' .$k[$j]['worldrecord_record'] . '</td>';
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