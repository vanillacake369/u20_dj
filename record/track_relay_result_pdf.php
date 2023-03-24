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
            제 20회 예천아시아 U20 육상경기선수권대회
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" /></a>
        </div>

        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
            <?php
            require_once __DIR__ . "/../action/module/record_worldrecord.php";
            require_once __DIR__ . "/../database/dbconnect.php"; //B:데이터베이스 연결 
            $sports=$_POST['sports'];
            $round=$_POST['round'];
            $gender=$_POST['gender'];
            $group=$_POST['group'];
            //B:데이터베이스 연결
            $sql = "SELECT DISTINCT * FROM list_record join list_schedule where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' and record_sports=schedule_sports and record_round=schedule_round and record_gender =schedule_gender";
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
                    echo '<p style="font-size:12px; width:330px">종목명: ' . $row['schedule_name'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">위치: ' . $row['schedule_location'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">성별: ';
                    echo $row['schedule_gender'] == 'm' ? 'MEN' : ($row['schedule_gender'] == 'f' ? 'WOMEN' : 'MIXED');
                    echo '</p>';
                    echo '<p style="font-size:12px; width:330px">일자: ' . $row['schedule_date'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">라운드: ' . $row['schedule_round'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">풍속: ' . $row['record_wind'] . 'm/s</p>';
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
                            <th>순위</th>
                            <th>등번호</th>
                            <th>성명</th>
                            <th>국가</th>
                            <th>레인</th>
                            <th>기록</th>
                            <th>비고</th>
                            <th>신기록</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j = 0;
                        $b = 3;
                        for ($i = 0; $i < count($_POST['rain']); $i++) {
                            echo '<tr>';
                            echo '<td>' . $_POST['rank'][$i] . '</td>';
                            echo '<td>';
                            if ($b = 3) {
                                echo $row['athlete_bib'] . '<br>';
                            }
                            for ($a = 0; $a < $b; $a++) {
                                $row = mysqli_fetch_array($result);
                                echo $row['athlete_bib'] . '<br>';
                            }
                            $b = 4;
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
                            <th>구분</th>
                            <th>기록</th>
                            <th>풍속</th>
                            <th>성명</th>
                            <th>소속</th>
                            <th>일자</th>
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