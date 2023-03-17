<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="js/main.js"></script>
    <link href="/css/display.css" rel="stylesheet" type="text/css" />
    <style>
    h2 {
        text-transform: uppercase;
    }
    </style>
    <title>Start List</title>
    <?php
            include_once $_SERVER["DOCUMENT_ROOT"].'/database/dbconnect.php';

            $schedule_sports = $_GET['schedule_sports'];
            $schedule_gender = $_GET['schedule_gender'];
            $schedule_round = $_GET['schedule_round'];
            $schedule_group = $_GET['schedule_group'];
            $page = $_GET['page'];
        
            if($schedule_sports=="800m"||$schedule_sports=="1500m"||$schedule_sports==" 2000m"||$schedule_sports==" 3000m"||$schedule_sports==" 3000mSC"||$schedule_sports==" 5000m"||$schedule_sports =="10000m"||$schedule_round=="1500m"){
                $backnum =1;
            }else{
                $backnum = 0;
            }
            if($schedule_sports=="800m"||$schedule_sports=="1500m"||$schedule_sports==" 2000m"||$schedule_sports==" 3000m"||$schedule_sports==" 3000mSC"||$schedule_sports==" 5000m"||$schedule_sports =="10000m"||$schedule_round=="1500m"||$schedule_round == "100m"||$schedule_round == "200m"||$schedule_round == "400m"||$schedule_round == "800m"||$schedule_round == "110mh"||$schedule_round == "100mh"||$schedule_round=="discusthrow"||$schedule_round=="shotput"||$schedule_round=="javelinthrow"||$schedule_round=="polevault"||$schedule_round=="highjump"||$schedule_round=="longjump"){
                $long = 1;//페이징
            }else{
                $long = 0;
            }
            
            if($schedule_sports == "4x400mR"||$schedule_sports == "4x100mR"){
                $relay = 1;
            }else{
                $relay = 0;
            }
            //schedule_id 
            $sql1 = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ?
                    AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ?;";
            $stmt = $db -> prepare($sql1);
            $stmt -> bind_param('ssss', $schedule_sports,  $schedule_gender, $schedule_round, $schedule_group);
            $stmt -> execute();
            $result1 = $stmt -> get_result();
            $row2 = mysqli_fetch_array($result1);
            
            $schedule_id = $row2['schedule_id'];

            //schedule_id값 null일 때, 되돌아가기
            if(empty($schedule_id)){
                echo "<script>alert('등록되지 않은 경기입니다.');history.back();</script>";
            }
            
            //시기가 존재하는 종목 선수이름 중복 출력 방지
            if($schedule_sports=="shotput"||$schedule_sports=="javelinthrow"||$schedule_sports=="hammerthrow"||$schedule_sports=="discusthrow"||$schedule_round=="discusthrow"||$schedule_round=="shotput"||$schedule_round=="javelinthrow"||$schedule_sports=="longjump"||$schedule_round=="longjump"||$schedule_sports=="triplejump"){
                $t = 1;//투포환 창던지기 해머던지기 멀리뛰기 세단뛰기
            }else if($schedule_sports=="highjump"||$schedule_sports=="polevault"||$schedule_round=="highjump"||$schedule_round=="polevault"){
                $t = 2;//높이뛰기 장대높이뛰기
            }else{
                $t = 0;
            }
            if($t == 1){//투포환 창던지기 해머던지기 멀리뛰기 세단뛰기
                //선수이름, 선수 국가, 레인
                $sql = "SELECT athlete_name, athlete_country, record_order
                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                        WHERE record_schedule_id = ? AND record_trial = 1 ORDER BY record_order ASC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                //페이지 수
                $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ? AND record_trial = 1";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result1 = $stmt -> get_result();
                $count = mysqli_num_rows($result1);
                $num = $count/8;
            }else if($t == 2){//수직도약경기(높이뛰기 장대높이뛰기)
                //선수이름, 선수 국가, 레인
                if($long == 1){

                }
                $sql = "SELECT athlete_name, athlete_country, record_order
                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                        WHERE record_schedule_id = ? ORDER BY record_order ASC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                //페이지 수
                $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result1 = $stmt -> get_result();
                $count = mysqli_num_rows($result1);
                $num = $count/8;
            }else if($relay ==1){
                //선수이름, 선수 국가, 레인
                $sql = "SELECT athlete_name, athlete_country, record_order
                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                        WHERE record_schedule_id = ? ORDER BY record_order ASC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                $num =0;
            }else{
                //선수이름, 선수 국가, 레인
                $sql = "SELECT athlete_name, athlete_country, record_order
                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                        WHERE record_schedule_id = ? ORDER BY record_order ASC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                //페이지 수
                $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result1 = $stmt -> get_result();
                $count = mysqli_num_rows($result1);
                $num = $count/8;
            }

            // //성별, 풍속, 라운드
            $sql = "SELECT schedule_gender, record_wind, schedule_id, schedule_round FROM list_record 
                    JOIN list_schedule ON list_record.record_schedule_id = list_schedule.schedule_id 
                    WHERE record_schedule_id = ?";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('s', $schedule_id);
            $stmt -> execute();
            $result2 = $stmt -> get_result();
            $row2 = mysqli_fetch_array($result2);   

            //종목
            $sql = "SELECT sports_name FROM list_sports WHERE sports_code = ?";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('s', $schedule_sports);
            $stmt -> execute();
            $result3 = $stmt -> get_result();
            $row3 = mysqli_fetch_array($result3);   
            
            //성별 문자열로 기록
            if($schedule_gender == "m"){
                $gender = "MAN";
            }else if($schedule_gender == "f"){
                $gender = "WOMAN";
            }else if($schedule_gender == "c"){
                $gender = "MIXED";
            }
                
            //세계기록(WR)
            $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ?;";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('ss', $schedule_sports,  $schedule_gender);
            $stmt -> execute();
            $result4 = $stmt -> get_result();
            $row4 = mysqli_fetch_array($result4);
            ?>
</head>

<body>
    <div>
        <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]);//종목 ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($gender);//성별 ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($schedule_round);//라운드 ?></h2>
        <h3>START LIST</h3>

        <div>
            <table>
                <colgroup>
                    <col style="width: auto;" />
                    <?php if($relay == 0){ ?>
                    <col style="width: auto;" />
                    <?php }?>
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                </colgroup>
                <thead>
                    <tr>
                        <?php
                if($backnum == 1){
                    echo '<th scope="col">등번호</th>';
                }else if($t == 1){
                    echo '<th scope="col">순번</th>';
                }else{
                    echo '<th scope="col">레인</th>';
                }    
                if($relay == 0){ ?>
                        <th scope="col">성명</th>
                        <?php }?>
                        <th scope="col">국가</th>
                        <th scope="col">사진</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $cnt=0; 
                        if($schedule_sports == "4x400mR"||$schedule_sports == "4x100mR"){
                            while($row = mysqli_fetch_array($result)){
                                if($cnt==0){
                                    echo '<tr>';
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["record_order"])."</td>";//start list(record_order)
                                    $cnt++;
                                }else if($cnt==3){
                                    echo "</td>";//선수 국가
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                    echo "<td style='text-align: center;'>국기</tb>";
                                    echo "</tr>"; 
                                    $cnt=0;
                                }else{
                                    $cnt++;
                                }
                            }    
                        }else if($num > 1){
                                $a = $page -1;
                                $b = $a*8;
                                if($long == 1){
                                    $c = "athlete_bib";
                                }else{
                                    $c = "record_order";
                                }
                                $sql ="SELECT athlete_name, athlete_country, record_order, record_live_record, athlete_bib
                                        FROM list_record JOIN list_athlete ON list_record.record_athlete_id = list_athlete.athlete_id 
                                        WHERE record_schedule_id = ? ORDER BY ? ASC LIMIT ?,8";
                                $stmt = $db -> prepare($sql);
                                $stmt -> bind_param('sss', $schedule_id, $c, $b);
                                $stmt -> execute();
                                $result = $stmt -> get_result();
                                if($long == 1){
                                    while($row = mysqli_fetch_array($result)){
                                    echo '<tr>';
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_bib"])."</td>";//등번호
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                                    echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                    echo "<td style='text-align: center;'>국기</tb>";
                                    echo "</tr>"; 
                                    }  
                                }else{
                                    while($row = mysqli_fetch_array($result)){
                                        echo '<tr>';
                                        echo "<td style='text-align: center;'>".htmlspecialchars($row["record_order"])."</td>";//레인
                                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                        echo "<td style='text-align: center;'>국기</tb>";
                                        echo "</tr>"; 
                                        }  
                                }
                        }else{
                            while($row = mysqli_fetch_array($result)){
                                echo '<tr>';
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["record_order"])."</td>";//레인
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center;'>국기</tb>";
                                echo "</tr>"; 
                            } 
                        }
                    ?>
                </tbody>
        </div>
        </table>
        <?php 
            if($num>1){
                for($i=0;$i<$num;$i++){
                    $j=$i+1;?>
        <button type="button"
            onclick="location.href='./startlist.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&page=<?php echo $j;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>'">page
            <?php echo $j;?></button>
        <?php
                }
            }?>
        <input type=hidden name=page value=1>
        </br>
        <form action="live_result.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type="submit" value="Live Result" style="width:100px; height:30px; margin-bottom: 30px;">
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