<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="js/main.js"></script>
    <style>
    h2 {
        text-transform: uppercase;
    }
    </style>
    <title>Official Result</title>
    <?php
        //종합경기 총합점수 페이지
        include_once $_SERVER["DOCUMENT_ROOT"].'/database/dbconnect.php';
        
        $schedule_sports = $_GET['schedule_sports'];
        $schedule_gender = $_GET['schedule_gender'];
        $schedule_round = $_GET['schedule_round'];
        $schedule_group = $_GET['schedule_group'];
        $schedule_id = $_GET['schedule_id'];

        //페이지 수
        $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ? AND record_multi_record >0";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('s', $schedule_id);
        $stmt -> execute();
        $result1 = $stmt -> get_result();
        $count = mysqli_num_rows($result1);
        $num = $count/8;

        if($num > 1){
            $long = 1;
        }else{
            $long = 0;
        }
        if($long == 1){
            $page = $_GET['page'];
        }

        $sql = "SELECT DISTINCT record_official_result, record_memo,athlete_id, record_official_record, record_weight,
                record_wind, record_order, athlete_name,record_new,schedule_sports, athlete_country, record_multi_record
                FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                ON schedule_id = record_schedule_id WHERE schedule_id =? AND record_multi_record >0
                ORDER BY record_official_result ASC";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('s', $schedule_id);
        $stmt -> execute();
        $result = $stmt -> get_result();
        //종합경기 총합점수 조회
        $sql1 = "SELECT DISTINCT athlete_id, record_live_record, athlete_name
                FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                ON schedule_id = record_schedule_id WHERE schedule_sports = 'decathlon'
                AND schedule_gender = ? AND schedule_round = '결승' AND schedule_group = ? ORDER BY record_official_record ASC";
        $stmt = $db -> prepare($sql1);
        $stmt -> bind_param('ss', $schedule_gender, $schedule_group);
        $stmt -> execute();
        $result1 = $stmt -> get_result();

//트랙, 릴레이, 점프, 던지기
            // $sql = "SELECT DISTINCT record_official_result, record_memo,athlete_id, record_official_record, record_weight,
            //         record_wind, record_order, athlete_name,record_new,schedule_sports, athlete_country, record_multi_record
            //         FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule ON schedule_id= record_schedule_id 
            //         WHERE record_official_result > 0 AND schedule_id =?
            //         ORDER BY record_official_result ASC";
            // $stmt = $db -> prepare($sql);
            // $stmt -> bind_param('s', $schedule_id);
            // $stmt -> execute();
            // $result = $stmt -> get_result();
        
        //풍속, 라운드
        $sql = "SELECT schedule_sports, record_wind, schedule_id FROM list_record
                JOIN list_schedule 
                ON list_record.record_schedule_id = list_schedule.schedule_id 
                WHERE record_schedule_id = ?";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('s', $schedule_id);
        $stmt -> execute();
        $result2 = $stmt -> get_result();
        $row2 = mysqli_fetch_array($result2);   

        //종목
        $sql = "SELECT list_sports.sports_name FROM list_sports WHERE sports_code = ?";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('s', $schedule_sports);
        $stmt -> execute();
        $result3 = $stmt -> get_result();
        $row3 = mysqli_fetch_array($result3);   

        //성별 문자열로 기록
        if($schedule_gender == "m"){
            $gender = "MEN";
        }else if($schedule_gender == "f"){
            $gender = "WOMEN";
        }else if($schedule_gender == "c"){
            $gender = "MIXED";
        }

        //세계기록(WR)
        $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics= 'u';";
        $stmt = $db -> prepare($sql);
        $stmt -> bind_param('ss', $schedule_sports,  $schedule_gender);
        $stmt -> execute();
        $result4 = $stmt -> get_result();
        $row4 = mysqli_fetch_array($result4);

        if($schedule_round == "100m"||$schedule_round == "100mh"||$schedule_round == "110mh"||$schedule_round == "200m"){
            $sports = 1;//짧은 트랙경기-풍속(title)
        }else if($schedule_round == "longjump"){
            $sports = 2;//멀리뛰기, 세단뛰기 -풍속
        }else if($schedule_round == "discusthrow"||$schedule_round == "shotput"||$schedule_round == "javelinthrow"){
            $sports = 0;//던지기
        }else{
            $sports = 3;
        }
    ?>
</head>

<body>
    <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]);//종목 ?></h2>
    <h2 style="display:inline;"><?php echo htmlspecialchars($gender);//성별 ?></h2>

    <h3>Official Result</h3>
    <?php
    if($sports == 1){
        echo '<p>풍속: '.htmlspecialchars($row2["record_wind"]).'m/s</p>';}//짧은 트랙경기에 표시할 풍속
    ?>
    <p>WR: <?php echo "-";//$row4['worldrecord_record'];//세계기록 ?></p>
    <table>
        <colgroup>
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col">순위</th>
                <th scope="col">성명</th>
                <th scope="col">국가</th>
                <th scope="col">사진</th>
                <th scope="col">종합점수</th>
                <th scope="col">비고</th>
                </br>
            </tr>
        </thead>
        <tbody>
            <?php
                    while($row = mysqli_fetch_array($result)){
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                        echo "<td style='text-align: center;'>국기</tb>"; 
                        $sql = $db->query("SELECT record_live_record from list_record INNER JOIN list_schedule ON schedule_id = record_schedule_id 
                                            AND schedule_sports = 'decathlon' AND schedule_round='final' AND schedule_division='s' AND record_athlete_id='".$row["athlete_id"]."'");//기록변환점수
                        $row6=mysqli_fetch_array($sql);
                        echo "<td style='text-align: center;'>".htmlspecialchars($row6[0])."</td>";//기록변환점수
                        if($row["record_new"] == "y"){
                            echo "<td style='text-align: center;'>WR</td>";
                        }
                        echo "</br>";      
                        echo "</tr>"; 
                    }  
                    ?>
        </tbody>
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
        echo '</br>';
        ?>
    <form action="official_result.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
        <input type=hidden name=schedule_id value=<?=$_GET['schedule_id'] ?>>
        <input type=hidden name=page value=1>
        <input type="submit" value="이전으로" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <form action="schedule_id_insert.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
        <input type="submit" value="처음으로" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <?php 
        if(!($schedule_round == "예선")){?>
    <form action="medal.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
        <input type=hidden name=schedule_id value=<?=$_GET['schedule_id'] ?>>
        <input type=hidden name=page value=0>
        <input type="submit" value="Medal" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <?php } ?>
</body>

</html>