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
            20TH ASIAN U20 ATHLETICS CHAMPIONSHIPS YECHEON 2023
            <!-- 제 20회 예천아시아 U20 육상경기선수권대회 -->
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" /></a>
        </div>

        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT</p>
            <?php
           require_once __DIR__ . "/../action/module/record_worldrecord.php";
           require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 
           $sports = $_POST['sports'];
           $round = $_POST['round'];
           $gender = $_POST['gender'];
           $group = $_POST['group'];
           
           $sql = "select *, record_weight, record_end from list_schedule join list_record ON (schedule_sports = record_sports)
                        where record_sports = schedule_sports and record_round = schedule_round and record_gender = schedule_gender
                         AND schedule_sports = '$sports' AND schedule_round = '$round' AND schedule_gender = '$gender'";
            $result = $db->query($sql);
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
                    echo '<p style="font-size:12px; width:330px">Event: ' . $row['schedule_name'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">Location: ' . $row['schedule_location'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">Gender: ' . $row['schedule_gender'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">Date: ' . $row['schedule_date'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">Round: ' . $_POST['round'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">Wind: ' . $row['record_weight'] . 'm/s'.'</p>'; //용기구 
                    ?>
                </div>
            </div>
            <div class="table_area" style="margin-bottom: 20px;">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab1">
                    <colgroup>
                        <col style="width: 7%" />
                        <col style="width: 5%" />
                        <col style="width: 8%" />
                        <col style="width: 10%" />
                        <col style="width: 6%" />
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <?php
                        if ($check_round != 'y') {
                            echo '<col style="width: 6%;">';
                            echo '<col style="width: 6%;">';
                            echo '<col style="width: 6%;">';
                        }
                        ?>
                        <?php
                        if ($check_round == 'y') {
                            echo '<col style="width: 8%;">';
                            echo '<col style="width: 8%;">';
                        }
                        ?>
                        <col style="width: 8%;">
                        <col style="width: 8%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">PLACE</th>
                            <th rowspan="2">BIB</th>
                            <th rowspan="2">NAME</th>
                            <th rowspan="2">COUNTRY</th>
                            <th rowspan="2">BIRTH</th>
                            <th rowspan="2">1st</th>
                            <th rowspan="2">2nd</th>
                            <th rowspan="2">3rd</th>
                            <?php
                            if ($check_round != 'y') {
                                echo '<th rowspan="2">4th</th>';
                                echo '<th rowspan="2">5th</th>';
                                echo '<th rowspan="2">6th</th>';
                            }
                            ?>
                            <th rowspan="2">RECORD</th>
                            <th>NOTE</th>
                            <?php
                            if ($check_round == 'y') {
                                echo '<th rowspan="2">POINTS</th>';
                                echo '<th rowspan="2">TOTAL</th>';
                            }
                            ?>

                        </tr>
                        <tr>
                            <th>NEW<br>RECORDS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($_POST['rank']); $i++) {
                            $country = $db->query("select athlete_bib, athlete_country, athlete_birth, athlete_id from list_athlete INNER JOIN list_record ON athlete_id = record_athlete_id  where athlete_name ='" . $_POST['playername'][$i] . "'");
                            // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                            $row1 = mysqli_fetch_array($country);
                            $point = $db->query("SELECT record_multi_record from list_record where record_athlete_id ='$row1[3]' and record_sports='$sports'  AND record_multi_record IS NOT null");
                            $pointrow = mysqli_fetch_array($point);
                            $totalid = $db->query("select schedule_sports from list_schedule where schedule_sports= '$sports' and schedule_round='$round'");
                            $totalrow = mysqli_fetch_array($totalid);
                            $totalpoint = $db->query("SELECT record_live_record from list_record where record_athlete_id ='$row1[3]' and record_sports = '$totalrow[0]'");
                            $totalrow1 = mysqli_fetch_array($totalpoint);
                            echo '<tr>';
                            echo '<td rowspan="2">' . $_POST['rank'][$i] . '</td>';
                            echo "<td rowspan='2'>$row1[0]</td>";
                            echo '<td rowspan="2">' . $_POST['playername'][$i] . '</td>';
                            echo "<td rowspan='2'>$row1[1]</td>";
                            echo "<td rowspan='2'>$row1[2]</td>";
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
                                echo '<td rowspan="2">' . $pointrow[0] . '</td>';
                                echo '<td rowspan="2">' . $totalrow1[0] . '</td>';
                            }
                            echo '<tr>';
                            echo '<td>' . (mb_strlen($_POST['newrecord'][$i]) > 0 ? $_POST['newrecord'][$i] : '&nbsp;') . '</td>';
                            echo '</tr>';
                            // echo '</tr>';
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