<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/print.css" />
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css" />
    <script src="../assets/fontawesome/js/all.min.js"></script>
    <script>
        window.print()
    </script>
    <title>U20</title>
</head>

<body>
    <div class="page">
        <div class="top">
            20TH ASIAN U20 ATHLETICS CHAMPIONSHIPS YECHEON 2023<!-- 제 20회 예천아시아 U20 육상경기선수권대회 -->
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" /></a>
        </div>

        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT</p>
            <?php
           $schedule_sports=$POST['sports'];
           $schedule_round=$POST['round'];
           $gender=$POST['gender'];
           $group=$POST['group'];
           require_once __DIR__ . "/../action/module/record_worldrecord.php";
           require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 
           
           $sql = "SELECT DISTINCT * FROM list_record  join list_schedule where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
           $result=$db->query($sql);
           $row = mysqli_fetch_assoc($result);
           if ($row['schedule_sports'] == 'decathlon' || $row['schedule_sports'] == 'heptathlon') {
               $check_round = 'y';
           } else {
               $check_round = 'n';
           }
            ?>
            <div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">SPORTS: ' . $row['schedule_name'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">LOCATION: ' . $row['schedule_location'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">GENDER: ' . $row['schedule_gender'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">DATE: ' . $row['schedule_date'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">ROUND: ' . $row['schedule_round'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">WEIGHT: ' . $row['schedule_weight'] . '</p>'; //용기구 
                    ?>
                </div>
            </div>
            <div class="table_area" style="margin-bottom: 20px;">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab1">
                    <colgroup>
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 14%" />
                        <col style="width: 6%" />
                        <col style="width: 9%;">
                        <col style="width: 9%;">
                        <col style="width: 9%;">
                        <?php
                        if ($check_round != 'y') {
                            echo '<col style="width: 9%;">';
                            echo '<col style="width: 9%;">';
                            echo '<col style="width: 9%;">';
                        }
                        ?>
                        <col style="width: 9%;">
                        <col style="width: 10%" />
                        <?php
                        if ($check_round == 'y') {
                            echo '<col style="width: 10%;">';
                            echo '<col style="width: 10%;">';
                        }
                        ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">RANK</th>
                            <th rowspan="2">NUM</th>
                            <th rowspan="2">NAME</th>
                            <th rowspan="2">COU<br>NTRY</th>
                            <th rowspan="2">First<br>period</th>
                            <th rowspan="2">Second<br>period</th>
                            <th rowspan="2">Third<br>period</th>
                            <?php
                            if ($check_round != 'y') {
                                echo '<th rowspan="2">Fourth<br>period</th>';
                                echo '<th rowspan="2">Fifth<br>period</th>';
                                echo '<th rowspan="2">Sixth<br>period</th>';
                            }
                            ?>
                            <th rowspan="2">Record</th>
                            <th>Note</th>
                            <?php
                            if ($check_round == 'y') {
                                echo '<th rowspan="2">Score</th>';
                                echo '<th rowspan="2">Overall<br>Score</th>';
                            }
                            ?>

                        </tr>
                        <tr>
                            <th>New<br>Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($_POST['rank']); $i++) {
                            $country = $db->query("select athlete_country,athlete_id from list_athlete where athlete_name ='" . $_POST['playername'][$i] . "'");
                            // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                            $row1 = mysqli_fetch_array($country);
                            echo '<tr>';
                            echo '<td rowspan="2">' . $_POST['rank'][$i] . '</td>';
                            echo '<td rowspan="2">' . $_POST['rain'][$i] . '</td>';
                            echo '<td rowspan="2">' . $_POST['playername'][$i] . '</td>';
                            echo "<td rowspan='2'>$row1[0]</td>";
                            echo '<td rowspan="2">' . $_POST['gameresult1'][$i] . '</td>';
                            echo '<td rowspan="2">' . $_POST['gameresult2'][$i] . '</td>';
                            echo '<td rowspan="2">' . $_POST['gameresult3'][$i] . '</td>';
                            if ($check_round != 'y') {
                                echo '<td rowspan="2">' . $_POST['gameresult4'][$i] . '</td>';
                                echo '<td rowspan="2">' . $_POST['gameresult5'][$i] . '</td>';
                                echo '<td rowspan="2">' . $_POST['gameresult6'][$i] . '</td>';
                            }
                            echo '<td rowspan="2">' . $_POST['gameresult'][$i] . '</td>';
                            echo '<td>';
                            if ($_POST['bigo'][$i] == '') {
                                echo '&nbsp;';
                            } else {
                                echo $_POST['bigo'][$i];
                            }
                            echo '</td>';
                            if ($check_round == 'y') {
                                $point = $db->query("SELECT record_multi_record from list_record where record_athlete_id ='$row1[1]' and and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' AND record_multi_record IS NOT null");
                                $pointrow = mysqli_fetch_array($point);
                                $totalid = $db->query("select schedule_id from list_schedule where schedule_name='" . $row['schedule_name'] . "' and schedule_round='final' and schedule_division='s'");
                                $totalrow = mysqli_fetch_array($totalid);
                                $totalpoint = $db->query("SELECT record_live_record from list_record where record_athlete_id ='$row1[1]' and record_schedule_id=$totalrow[0]");
                                $totalrow1 = mysqli_fetch_array($totalpoint);
                                echo '<td>' . $pointrow[0] . '</td>';
                                echo '<td>' . $totalrow1[0] . '</td>';
                            }
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td>' . (mb_strlen($_POST['newrecord'][$i]) > 0 ? $_POST['newrecord'][$i] : '&nbsp;') . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="total">
            <p>Overall Record</p>
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
                            <th>Division</th>
                            <th>Record</th>
                            <th>Wind</th>
                            <th>Name</th>
                            <th>Affiliation</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $world = check_worldrecord($sports, $gender, $round, $check_round, $row['record_end']);
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
                                     echo '<td>' . $k[$j]['worldrecord_record'] . '</td>';
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