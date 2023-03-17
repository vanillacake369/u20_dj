<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    h2 {
        text-transform: uppercase;
    }
    </style>
    <title>Track</title>
    <?php 
    
    require_once __DIR__ . "/../includes/auth/config.php";
    require_once __DIR__ . "/../security/security.php";
    require_once __DIR__ . '/../action/module/schedule_worldrecord.php';
    require_once __DIR__ . "/../backheader.php";

    $schedule_result = $_GET['schedule_result'];
    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];
    $gender = "";
    if($schedule_sports=="800m"||$schedule_sports=="1500m"||$schedule_sports==" 2000m"||$schedule_sports==" 3000m"||$schedule_sports==" 3000mSC"||$schedule_sports==" 5000m"||$schedule_sports =="10000m"||$schedule_round == "1500m"||$schedule_round == "100m"||$schedule_round == "200m"||$schedule_round == "400m"||$schedule_round == "800m"||$schedule_round == "110mh"||$schedule_round == "100mh"){
        $long = 1;//종합경기 페이징
    }else{
        $long = 0;
    }
    if($long == 1){
        $page = $_GET['page'];
    }

    if($long == 1){
        //페이지 수
        $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('s', $schedule_id);
        $stmt -> execute();
        $result1 = $stmt -> get_result();
        $count = mysqli_num_rows($result1);
        $num = $count/8;
    }else{
        //순위, 선수이름, 국가, 레인, 기록, 종합점수(종합경기), 등번호
        $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_multi_record, record_new, athlete_bib
                FROM list_record 
                JOIN list_athlete 
                ON list_record.record_athlete_id = list_athlete.athlete_id 
                WHERE record_schedule_id = ? ORDER BY list_record.record_live_result ASC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
    }

            //풍속, 라운드
            $sql = "SELECT schedule_sports, record_wind, schedule_id, schedule_round
                    FROM list_record 
                    JOIN list_schedule 
                    ON list_record.record_schedule_id = list_schedule.schedule_id 
                    WHERE record_schedule_id = ?";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('s', $schedule_id);
            $stmt -> execute();
            $result2 = $stmt -> get_result();
            $row2 = mysqli_fetch_array($result2);   

            //종목(한글)
            //$schedule_sports = $row2["schedule_sports"];
            $sql = "SELECT list_sports.sports_name FROM list_sports WHERE sports_code = ?";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('s', $schedule_sports);
            $stmt -> execute();
            $result3 = $stmt -> get_result();
            $row3 = mysqli_fetch_array($result3);   

            //세계기록(WR)
            $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics = 'w'";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('ss', $schedule_sports,  $schedule_gender);
            $stmt -> execute();
            $result4 = $stmt -> get_result();
            $row4 = mysqli_fetch_array($result4);
            
            //성별 문자열로 기록
            if($schedule_gender == "m"){
                global $gender;
                $gender = "MEN";
            }else if($schedule_gender == "f"){
                global $gender;
                $gender = "WOMEN";
            }else if($schedule_gender == "c"){
                global $gender;
                $gender = "MIXED";
            }

            //100m, 100mh, 110mh, 200m ->풍속
            if($schedule_sports == "100m"||$schedule_sports == "100mh"||$schedule_sports == "110mh"||$schedule_sports == "200m"){
                $sports = 1;
            }else{
                $sports = 0;
            }

            //종합경기 ->POINT
            if($schedule_round == "100m"||$schedule_round == "100mh"||$schedule_round == "110m"||$schedule_round == "110mh"||$schedule_round == "200m"||$schedule_round == "400m"||$schedule_round == "400mh"||$schedule_round == "800m"||$schedule_round == "1500m"){
                $round = 1;//10종경기(종합경기)
            }else{
                $round = 0;
            }
    ?>
</head>

<body>
    <div class="all">
        <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]);//종목 ?></h2>
        <h2 style="display:inline;"><?php echo $gender;//성별 ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($schedule_round);//라운드 ?></h2>
        <h3>
            <? echo $schedule_result=='o'?'Official Result':($schedule_result=='l'?'Live Result':'Start List')?>
        </h3>
        <?php
        if($sports == 1||$schedule_round == "100m"||$schedule_round == "100mh"||$schedule_round == "110mh"||$schedule_round == "200m"){
            echo '<p>풍속: '.htmlspecialchars($row2["record_wind"]).'m/s</p>';
        }
        ?>
        <?php 
            if($round != 1){   
                echo '<p>WR: ';
                if (!isset($row4['worldrecord_record'])) echo "";
                else echo $row4['worldrecord_record'];
                echo '</p>';
            }
        ?>
        <div class="live_result">
            <table>
                <colgroup>
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <?php 
                    if($round == "1"){
                        '<col style="width: auto;"/>';
                    }else{
                        '<col style="width: auto;"/>';
                    }
                ?>
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">순위</th>
                        <th scope="col">성명</th>
                        <th scope="col">국가</th>
                        <th scope="col">사진</th>
                        <?php
                    if($sports == 1){
                        echo '<th scope="col">레인</th>';
                    }else{
                        echo '<th scope="col">등번호</th>';
                    }?>
                        <th scope="col">기록</th>
                        <?php 
                    if($round == "1"){
                        echo '<th scope="col">POINT</th>';  
                    }else{
                        echo '<th scope="col">비고</th>'; 
                    }
                    ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if($long == 0){
                            while($row = mysqli_fetch_array($result)){
                                echo '<tr>';
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_result"])."</td>";//순위(Live Result)
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center; background-image:url(/assets/images/u20_national_flag/".htmlspecialchars($row["athlete_country"]) . ".png ); background-repeat: no-repeat;
                                background-size: cover;' ></td>";
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_order"])."</td>";//레인
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_record"])."</td>";//live record
                                if($round == "1"){
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["record_multi_record"])."</td>";//기록변환점수
                                }

                                //신기록
                                $sql0 = $db -> query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '".$schedule_sports."' AND worldrecord_gender = '".$schedule_gender."' 
                                        AND worldrecord_athlete_name = '".$row['athlete_name']."' AND worldrecord_record= '".$row['record_live_record']."'");
                                $row6 = mysqli_fetch_array($sql0);
                                if(!empty($row6)){
                                    if($row6['worldrecord_athletics'] == 'w'){
                                        echo "<td style='text-align: center;'>WR</td>";
                                    }else if($row6['worldrecord_athletics'] == 'a'){
                                        echo "<td style='text-align: center;'>AR</td>";
                                    }else if($row6['worldrecord_athletics'] == 'u'){
                                        echo "<td style='text-align: center;'>UR</td>";
                                    }else if($row6['worldrecord_athletics'] == 's'){
                                        echo "<td style='text-align: center;'>SR</td>";
                                    }
                                }    
                                echo "</tr>"; 
                            }                            
                        }else{
                            $a = $page -1;
                                    $b = $a*8;
                                    $sql = "SELECT athlete_name, athlete_country, record_order, record_live_record, athlete_bib, record_live_result, record_new, record_multi_record
                                            FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                            WHERE record_schedule_id = ? ORDER BY record_live_result ASC LIMIT ?,8";
                                    $stmt = $db -> prepare($sql);
                                    $stmt -> bind_param('ss', $schedule_id, $b);
                                    $stmt -> execute();
                                    $result = $stmt -> get_result();

                            while($row = mysqli_fetch_array($result)){
                                echo '<tr>';
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_result"])."</td>";//순위(Live Result)
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center;'><img src='/assets/images/u20_national_flag/".htmlspecialchars($row["athlete_country"]) . ".png'/></tb>";
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_bib"])."</td>";//등번호
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_record"])."</td>";//live record
                                if($round == "1"){
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["record_multi_record"])."</td>";//기록변환점수
                                }
                                //신기록
                                $sql0 = $db -> query("SELECT worldrecord_record, worldrecord_sports, worldrecord_location, worldrecord_athlete_name, worldrecord_athletics, worldrecord_record 
                                        FROM list_worldrecord WHERE worldrecord_sports = '".$schedule_sports."' AND worldrecord_gender = '".$schedule_gender."' 
                                        AND worldrecord_athlete_name = '".$row['athlete_name']."' AND worldrecord_record= '".$row['record_live_record']."'");
                                $row6 = mysqli_fetch_array($sql0);
                                if(!empty($row6)){
                                    if($row6['worldrecord_athletics'] == 'w'){
                                        echo "<td style='text-align: center;'>WR</td>";
                                    }else if($row6['worldrecord_athletics'] == 'a'){
                                        echo "<td style='text-align: center;'>AR</td>";
                                    }else if($row6['worldrecord_athletics'] == 'u'){
                                        echo "<td style='text-align: center;'>UR</td>";
                                    }else if($row6['worldrecord_athletics'] == 's'){
                                        echo "<td style='text-align: center;'>SR</td>";
                                    }
                                }  
                                echo "</tr>"; 
                            }                            
                        }    
                    ?>
                </tbody>
        </div>
        </table>
        <?php 
        if($long == 1){
            if($num>1){
                for($i=0;$i<$num;$i++){
                    $j=$i+1;?>
        <button type="button"
            onclick="location.href='./track_display.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&page=<?php echo $j;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>'">page
            <?php echo $j;?></button>
        <?php }
            }
        }
        ?>
        </br>
        <form action="official_result.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?=$_GET['schedule_id'] ?>>
            <?php 
            if($long == 1){
                echo '<input type=hidden name=page value='.$_GET['page'].'>';
            }?>
            <input type="submit" value="Official Result" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="startlist.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type="hidden" name="page" value="1">
            <input type="submit" value="Start list" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="schedule_id_insert.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type="submit" value="처음으로" style="width:100px; height:30px; margin-bottom: 30px;">
        </form>

    </div>
</body>

</html>