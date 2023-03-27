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
    <!--Data Tables-->
    <title>U20</title>
</head>

<body>
    <div class="page">
        <div class="top">
            20TH ASIAN U20 ATHLETICS CHAMPIONSHIPS YECHEON 2023
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" /></a>
        </div>

        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT</p>
            <?php
            require_once __DIR__ . "/../action/module/record_worldrecord.php";
            require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결
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
            <div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">Event: ' . $_POST['name'] . '</p>';
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
                    echo '<p style="font-size:12px; width:330px">Wind: ' . $row['record_wind'] . '</p>';
                    ?>
                </div>
            </div>
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
                            <th>PLACE</th>
                            <th>BIB</th>
                            <th>NAME</th>
                            <th>COUNTRY</th>
                            <th>LANE</th>
                            <th>RESULT</th>
                            <th>NOTE</th>
                            <th>RECORDS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j = 0;
                        $v = 0;
                        $b = 3;
                        for ($i = 0; $i < count($_POST['rain']); $i++) {
                            echo '<tr>';
                            echo '<td>' . $_POST['rank'][$i] . '</td>';
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
                            echo '<td>';
                            for ($d; $v < $s; $v++) {
                                echo $_POST['playerbib'][$v] . '<br>';
                            }
                            echo '</td>';
                            $k = $j + 4;
                            echo '<td>';
                            for ($j; $j < $k; $j++) {
                                echo $_POST['playername'][$j] . '<br>';
                            }
                            $country = $db->query("select athlete_country from list_athlete where athlete_name ='" . $_POST['playername'][$j - 1] . "'");
                            // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                            $row1 = mysqli_fetch_array($country);
                            echo '</td>';
                            echo '<td>' . $row1[0] . '</td>';
                            echo '<td>' . $_POST['rain'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult'][$i] . '</td>';
                            echo '<td>' . $_POST['bigo'][$i] . '</td>';
                            echo '<td>' . $_POST['newrecord'][$i] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <p style="margin:0px 30px 0px 0px; text-align:right;">referee signature :
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(signature)
            </p>
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
                                    echo "<td>WR</td>";
                                    break;
                                case 'u':
                                    echo "<td>UWR</td>";
                                    break;
                                case 's':
                                    echo "<td>AR</td>";
                                    break;
                                case 'a':
                                    echo "<td>UWR</td>";
                                    break;
                                case 'c':
                                    echo "<td>CR</td>";
                                    break;
                                default:
                                    echo "<td></td>";
                                    break;
                            }
                            echo '<td>' . restoreresult($k['record']) . '</td>';
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
                                    echo '<td>' . restoreresult($k[$j]['worldrecord_record']) . '</td>';
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