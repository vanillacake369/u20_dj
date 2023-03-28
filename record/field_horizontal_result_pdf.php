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
            require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 
            $sports = $_POST['sports'];
            $round = $_POST['round'];
            $gender = $_POST['gender'];
            $group = $_POST['group'];
            
            $sql = "SELECT DISTINCT *, schedule_name from list_record inner join list_schedule ON (schedule_sports = record_sports)
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
            <div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">종목명: ' . $row['schedule_name'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">위치: ' . $row['schedule_location'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">성별: ' . $row['schedule_gender'] . '</p>';
                    echo '<p style="font-size:12px; width:330px">일자: ' . $row['schedule_date'] . '</p>';
                    ?>
                </div>
                <div style="width: 100%; display: flex;">
                    <?php
                    echo '<p style="font-size:12px; width:330px">라운드: ' . $row['schedule_round'] . '</p>';
                    echo '<p style="font-size:12px; width:330px"></p>';
                    ?>
                </div>
            </div>
            <div class="table_area" style="margin-bottom: 20px;">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab1">
                    <colgroup>
                        <col style="width: 6%" />
                        <col style="width: 6%" />
                        <col style="width: 15%" />
                        <col style="width: 7%" />
                        <col style="width: 12%" />
                        <?php
                        if ($sports != 'decathlon' && $sports !='heptathlon') {
                            
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        echo '<col style="width: 7%;">';
                        }else{
                        echo '<col style="width: 8%;">';
                        echo '<col style="width: 8%;">';
                        echo '<col style="width: 8%;">';
                        echo '<col style="width: 8%;">';
                        echo '<col style="width: 10%;">';
                        echo '<col style="width: 8%;">';
                        }
                        ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">순위</th>
                            <th rowspan="2">등번호</th>
                            <th rowspan="2">성명</th>
                            <th rowspan="2">국가</th>
                            <th rowspan="2">출생년도</th>
                            <th>1차시기</th>
                            <th>2차시기</th>
                            <th>3차시기</th>
                            <?php
                            if ($sports != 'decathlon' && $sports !='heptathlon') {
                                echo'<th>4차시기</th>';
                                echo'<th>5차시기</th>';
                                echo'<th>6차시기</th>';
                                echo'<th>기록</th>';
                                echo'<th>비고</th>';
                            }else{
                                echo'<th>기록</th>';
                                echo'<th>비고</th>';
                                echo'<th>점수</th>';
                            }
                            ?>

                        </tr>
                        <tr>
                            <?php
                        if ($sports != 'decathlon' && $sports !='heptathlon') {
                            echo'<th colspan="7">풍속</th>';
                            echo'<th>신기록</th>';
                        }else{
                            echo'<th colspan="4">풍속</th>';
                            echo'<th>신기록</th>';
                            echo'<th>종합 점수</th>';
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($_POST['rank']); $i++) {
                            $country = $db->query("select athlete_bib, athlete_country, athlete_birth, athlete_id from list_athlete INNER JOIN list_record ON athlete_id = record_athlete_id where athlete_name ='" . $_POST['playername'][$i] . "'");
                            // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                            $row1 = mysqli_fetch_array($country);                    
                            $point = $db->query("SELECT record_multi_record from list_record where record_athlete_id ='$row1[3]' and record_round='$round' and record_gender ='$gender' and record_group='$group'  and record_multi_record IS NOT null");
                            $pointrow = mysqli_fetch_array($point);
                            $totalid = $db->query("SELECT schedule_sports from list_schedule where schedule_name='" . $row['schedule_name'] . "' and schedule_round='$round'");
                            $totalrow = mysqli_fetch_array($totalid);
                            $totalpoint = $db->query("SELECT record_live_record from list_record where record_athlete_id ='$row1[3]' and record_sports='$totalrow[0]'");
                            $totalrow1 = mysqli_fetch_array($totalpoint);
                            echo '<tr>';
                            echo '<td rowspan="2">' . $_POST['rank'][$i] . '</td>';
                            echo "<td rowspan='2'>$row1[0]</td>";
                            echo '<td rowspan="2">' . $_POST['playername'][$i] . '</td>';
                            echo "<td rowspan='2'>$row1[1]</td>";
                            echo "<td rowspan='2'>$row1[2]</td>";
                            echo '<td>' . $_POST['gameresult1'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult2'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult3'][$i] . '</td>';
                            if ($sports != 'decathlon' && $sports !='heptathlon') {
                            echo '<td>' . $_POST['gameresult4'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult5'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult6'][$i] . '</td>';
                            echo '<td>' . $_POST['gameresult'][$i] . '</td>';
                            echo '<td>';
                            if ($_POST['bigo'][$i] == '') {
                                echo '&nbsp;';
                            } else {
                                echo $_POST['bigo'][$i];
                            }
                            echo '</td>';
                            }else{

                                echo '<td>' . $_POST['gameresult'][$i] . '</td>';
                                echo '<td>';
                                if ($_POST['bigo'][$i] == '') {
                                    echo '&nbsp;';
                                } else {
                                    echo $_POST['bigo'][$i];
                                }
                                echo '</td>';
                                echo '<td>' . $pointrow[0] . '</td>';                               
                            }
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td>' . $_POST['wind1'][$i] . '</td>';
                            echo '<td>' . $_POST['wind2'][$i] . '</td>';
                            echo '<td>' . $_POST['wind3'][$i] . '</td>';
                            if ($sports != 'decathlon' && $sports !='heptathlon') {
                            echo '<td>' . $_POST['wind4'][$i] . '</td>';
                            echo '<td>' . $_POST['wind5'][$i] . '</td>';
                            echo '<td>' . $_POST['wind6'][$i] . '</td>';
                            echo '<td>' . $_POST['lastwind'][$i] . '</td>';
                            echo '<td>' . (mb_strlen($_POST['newrecord'][$i]) > 0 ? $_POST['newrecord'][$i] : '&nbsp;') . '</td>';
                            }else{
                            echo '<td>' . $_POST['lastwind'][$i] . '</td>';
                            echo '<td>' . (mb_strlen($_POST['newrecord'][$i]) > 0 ? $_POST['newrecord'][$i] : '&nbsp;') . '</td>';
                            echo '<td>' . $totalrow1[0] . '</td>';
                            }
                            // echo '<td>' . $_POST['lastwind'][$i] . '</td>';
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
                            <th>바람</th>
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
                                    echo "<td>UAR</td>";
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