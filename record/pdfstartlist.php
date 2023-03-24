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
            제 20회 예천아시아 U20 육상경기선수권대회
            <img src="../assets/img/logo.png" alt="Logo" class="logo_img" />
        </div>
        <?php
        $s_id = $_GET['id'];
        require_once __DIR__ . "/../action/module/record_worldrecord.php";
        include_once(__DIR__ . "/../database/dbconnect.php"); //B:데이터베이스 연결 
        $sql = "select * ,
                    record_order,
                    athlete_bib, 
                    athlete_name, 
                    athlete_country, 
                    athlete_birth,
                    athlete_sb,
                    athlete_pb
                from list_schedule
                JOIN list_record on record_schedule_id = schedule_id
                JOIN list_athlete on record_athlete_id = athlete_id 
                where schedule_id = '" . $_GET['id'] . "'";
        $result = $db->query($sql);
        $row = mysqli_fetch_assoc($result);
        if ($row['schedule_sports'] == 'decathlon' || $row['schedule_sports'] == 'heptathlon') {
            $check_round = 'y';
        } else {
            $check_round = 'n';
        }
        ?>

        <div class="middle" style="display:inline-block;">
            <p style="margin:10px 0px 0px 0px; text-align:center;">START LIST[레인별 선수 목록]</p>
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
            <!-- <div style="width: 100%; display: flex">
                        <p style="font-size: 12px; width: 330px">
                            종목명: 100m
                        </p>
                        <p style="font-size: 12px; width: 330px">
                            위치: 예천 공설운동장
                        </p>
                    </div>
                    <div style="width: 100%; display: flex">
                        <p style="font-size: 12px; width: 330px">성별: 남</p>
                        <p style="font-size: 12px; width: 330px">
                            일자: 2023년 6월 4일
                        </p>
                    </div>
                    <div style="width: 100%; display: flex">
                        <p style="font-size: 12px; width: 330px">
                            라운드: 결승
                        </p>
                        <p style="font-size: 12px; width: 330px">풍속: 1.1m</p>
                    </div> -->
            <div class="table_area">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                    <colgroup>
                        <col style="width: 5%" />
                        <col style="width: 5%" />
                        <col style="width: 30%" />
                        <col style="width: 10%" />
                        <col style="width: 20%" />
                        <col style="width: 20%" />
                        <col style="width: 20%" />
                        <!--<col style="width: 10%" />-->
                    </colgroup>
                    <thead>
                        <tr>
                            <th>순서</th>
                            <th>등번호</th>
                            <th>이름</th>
                            <th>국가</th>
                            <th>생년월일</th>
                            <th>개인 최고기록</th>
                            <th>시즌 최고기록</th>
                            <!--<th>비고</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $db->query($sql);
                        while ($row = mysqli_fetch_array($result)) {

                            $athlete_pb_array = array();
                            $athlete_sb_array = array();

                            $athlete_pb = explode(':', $row["athlete_pb"]);
                            $athlete_pb_del = preg_replace("/[^A-Za-z0-9:.]/", "", $athlete_pb);
                            for ($i = 0; $i <= count($athlete_pb_del); $i = $i + 2) {
                                if ($athlete_pb_del == $row['schedule_name']) {
                                    array_push($athlete_pb_array, $athlete_pb_del[$i + 1]);
                                }
                            }
                            print_r($athlete_pb);
                            echo '<br>';
                            echo '<br>';
                            print_r($athlete_pb_del);
                            echo '<br>';
                            echo '<br>';
                            print_r($row['schedule_name']);
                            echo '<br>';
                            echo '<br>';
                            print_r($athlete_pb_array);
                            echo '<br>';
                            echo '<br>';

                            // foreach($athlete_pb = $pd){
                            //     if($pd == )
                            // }    
                            //$athlete_pb = preg_replace("/[^A-Za-z0-9:.]/", "", $row["athlete_pb"]);
                            $athlete_sb = explode(':', $row["athlete_sb"]);
                            //echo $athlete_pb[0]." "; 
                            //echo $athlete_pb[1]; 
                            echo '<tr>';
                            echo "<td>" . $row['record_order'] . "</td>";
                            echo "<td>" . $row['athlete_bib'] . "</td>";
                            echo "<td>" . $row['athlete_name'] . "</td>";
                            echo "<td>" . $row['athlete_country'] . "</td>";
                            echo "<td>" . $row['athlete_birth'] . "</td>";
                            echo "<td>" . $row['athlete_pb'] . "</td>";
                            echo "<td>" . $row['athlete_sb'] . "</td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <p style="margin:0px 30px 0px 0px; text-align:right;">참여심판 :
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
        </div>
        <!-- <div>
            <p style="margin:0px 30px 0px 0px; text-align:right;">심판 서명 :
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(인)</p>
        </div> -->
    </div>

</body>

</html>