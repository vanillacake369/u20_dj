<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/print.css" />
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css" />
    <script src="../assets/fontawesome/js/all.min.js"></script>
    <!-- <script>
    window.print()
    </script> -->
    <title>U20</title>
</head>

<body>
    <div class="page">
        <div class="top">
            20TH ASIAN U20 ATHLETICS CHAMPIONSHIPS YECHEON 2023
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" /></a>
        </div>
        <?php
        require_once __DIR__ . "/../action/module/record_worldrecord.php";
        require_once __DIR__ . "/../action/module/schedule_worldrecord.php";
        require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결 
        $sports = $_POST['sports'];
        $gender = $_POST['gender'];
        
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
        $country = $db->query("select athlete_name,athlete_country,athlete_id,athlete_bib,athlete_birth from list_athlete join list_record where athlete_id=record_athlete_id and record_sports='$sports' and record_gender='$gender' and record_round='final'");
        $count=mysqli_num_rows($country);
            $check_round = 'n';
        ?>
        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT</p>
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
                            <th style="font-size:8px;">PLACE</th>
                            <th style="font-size:8px;">BIB</th>
                            <th style="font-size:8px;">NAME</th>
                            <th style="font-size:8px;">Country</th>
                            <th style="font-size:8px;">TOTAL<br>RESULT</th>
                            <th style="font-size:8px;">100mh</th>
                            <th style="font-size:8px;">high<br>jump</th>
                            <th style="font-size:8px;">200m</th>
                            <th style="font-size:8px;">shot<br>put</th>
                            <th style="font-size:8px;">long<br>jump</th>
                            <th style="font-size:8px;">javelin<br>throw</th>
                            <th style="font-size:8px;">800m</th>
                            <th style="font-size:8px;">NOTE</th>
                            <th style="font-size:8px;">RECORDS</th>
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
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row1['athlete_bib']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row1['athlete_name']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row1['athlete_country']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";
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
                                            ORDER BY FIELD(schedule_round, '100mh','highjump','200m','shotput','longjump','javelinthrow','800m'), athlete_name;";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
                                    $table_count++;
                                }
                                for ($i = 0; $i < (7-$table_count); $i++)
                                {
                                    echo "<td></td>";
                                }

                                echo "<td>" . htmlspecialchars($row['record_memo']) . "</td>";
                                
                                echo "</tr>";
                                echo "<tr>";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_' . $result_type . '_record']) . "</td>";
                                }
                                for ($i = 0; $i < (7-$table_count); $i++)
                                {
                                    echo "<td></td>";
                                }
                                //@Potatoeunbi
                                //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                                //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                                if ($row['record_' . $result_type . '_record']) world($db, $row['athlete_name'], $row['record_new'], $row['schedule_sports'], $row['record_' . $result_type . '_record']);

                                echo "</tr>";
                                echo "<tr>";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_wind'] == null ? ' ' : $sub['record_wind']) . "</td>";
                                }
                                for ($i = 0; $i < (7-$table_count); $i++)
                                {
                                    echo "<td></td>";
                                }
                                echo "</tr></tbody>";
                                $table_count = 0;
                                $people++;
                            } ?>
                </table>
            </div>
        </div>
        <div>
            <p style="margin:0px 30px 0px 0px; text-align:right;">referee signature :
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(signature)</p>
        </div>
        <div class="total">
            <p>TOP LIST</p>
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
                            <th>RECORDS</th>
                            <th>RESULT</th>
                            <th>WIND</th>
                            <th>NAME</th>
                            <th>COUNTRY</th>
                            <th>DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $world = check_worldrecord('heptathlon','f','final', $check_round, $row['schedule_start']);
                        foreach ($world as $k) {
                            echo '<tr>';
                            switch ($k['athletics']) {
                                case 'w':
                                    echo "<td>WR</td>";
                                    break;
                                case 'u':
                                    echo "<td>UWR</td>";
                                    break;
                                case 's':
                                    echo "<td>AR</td>";
                                    break;
                                case 'a':
                                    echo "<td>UAR</td>";
                                    break;
                                case 'c':
                                    echo "<td>CR</td>";
                                    break;
                                default:
                                    echo "<td></td>";
                                    break;
                            }
                            echo '<td>' . $k['record'] . '</td>';
                            echo '<td>' . $k['wind'] . '</td>';
                            echo '<td>' . $k['athlete_name'] . '</td>';
                            echo '<td>' . $k['country_code'] . '</td>';
                            echo '<td>' . $k['datetime'] . '</td>';
                            echo '</tr>';
                            if (array_key_exists("0", $k)) {
                                for ($j = 0; $j < count($k) - 6; $j++) {
                                    echo '<tr>';
                                    switch ($k[$j]['worldrecord_athletics']) {
                                        case 'w':
                                            echo "<td>WR</td>";
                                            break;
                                        case 'u':
                                            echo "<td>UWR</td>";
                                            break;
                                        case 's':
                                            echo "<td>AR</td>";
                                            break;
                                        case 'a':
                                            echo "<td>UAR</td>";
                                            break;
                                        case 'c':
                                            echo "<td>CR</td>";
                                            break;
                                        default:
                                            echo "<td></td>";
                                            break;
                                    }
                                    echo '<td>' .$k[$j]['worldrecord_record'] . '</td>';
                                    echo '<td>' . $k[$j]['worldrecord_wind'] . '</td>';
                                    echo '<td>' . $k[$j]['worldrecord_athlete_name'] . '</td>';
                                    echo '<td>' . $k[$j]['worldrecord_country_code'] . '</td>';
                                    echo '<td>' . $k[$j]['worldrecord_datetime'] . '</td>';
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