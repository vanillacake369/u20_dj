<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track</title>
    <?php 
    require_once "../backheader.php";
    
    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];
    $page = $_GET['page'];
    
    //갯수 구하기
    $sql = "SELECT record_schedule_id FROM list_record WHERE record_schedule_id = ?";
    $stmt = $db -> prepare($sql);
    $stmt -> bind_param('s', $schedule_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $count = mysqli_num_rows($result);
    $num = $count/8;
    $s = 0;
        //순위, 선수이름, 국가, 레인, 기록
        $sql = "SELECT record_live_result, athlete_name, athlete_country, record_order, record_live_record, record_new
            FROM list_record 
            JOIN list_athlete 
            ON list_record.record_athlete_id = list_athlete.athlete_id 
            WHERE record_schedule_id = ? ORDER BY list_record.record_live_result ASC LIMIT ?, 8";
            $stmt = $db -> prepare($sql);
            $stmt -> bind_param('ss', $schedule_id, $s);
            $stmt -> execute();
            $result = $stmt -> get_result();

            
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
                $gender = "MAN";
            }else if($schedule_gender == "f"){
                $gender = "WOMAN";
            }else if($schedule_gender == "c"){
                $gender = "MIXED";
            }
            //라운드 영어로 출력
            switch($schedule_round){
                case ("결승"):
                    $roundEng = "FIANL";
                    break;
                case ("예선"):
                    $roundEng = "PRELIMINARY";
                    break;
                case ("100m 허들")://종합경기
                    $roundEng = "100m HURDLES";
                    break;
                case ("110m 허들"):
                    $roundEng = "110m HURDLES";
                    break;
                case ("멀리뛰기"):
                    $roundEng = "LONG JUMP";
                    break;
                case ("투포환"):
                    $roundEng = "SHOT PUT";
                    break;
                case ("높이뛰기"):
                    $roundEng = "HIGH JUMP";
                    break;
                case ("원반던지기"):
                    $roundEng = "DISCUS THROW";
                    break;
                case ("장대높이뛰기"):
                    $roundEng = "POLE VAULT";
                    break;
                case ("창던지기"):
                    $roundEng = "JAVELIN THORW";
                    break;
                default:
                    $roundEng = $schedule_round;
                    break;
                }
                ?>
</head>
<body>
    <div class="all">
        <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]);//종목 ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($gender);//성별 ?></h2>
        <h2 style="display:inline;"><?php echo htmlspecialchars($roundEng);//라운드 ?></h2>
        <h3>Live Result</h3>
        <?php
            echo '<p>WR: '.$row4['worldrecord_record'].'</p>';
        ?>
        <div class="live_result">
        <table>
            <colgroup> 
                <col style="width: auto;"/> 
                <col style="width: auto;"/> 
                <col style="width: auto;"/> 
                <col style="width: auto;"/> 
                <col style="width: auto;"/> 
                <col style="width: auto;"/>
                <?php
                    '<col style="width: auto;"/>';
                ?>
            </colgroup> 
            <thead>
            <tr>
                <th scope="col">순위</th>
                <th scope="col">성명</th>
                <th scope="col">국가</th>
                <th scope="col">사진</th>
                <th scope="col">레인</th>
                <th scope="col">기록</th>
                <?php 
                    echo '<th scope="col">비고</th>'; 
                ?>
                </br>
            </tr>
            </thead>
                <tbody>
                    <?php
                        while($row = mysqli_fetch_array($result)){
                            echo '<tr>';
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_result"])."</td>";//순위(Live Result)
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수 이름
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                            echo "<td style='text-align: center;'>국기</tb>";
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_order"])."</td>";//레인
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_live_record"])."</td>";//live record
                            if($row["record_new"] == "y"){
                                echo "<td style='text-align: center;'>WR</td>";
                            }
                            echo "</br>";      
                            echo "</tr>"; 
                        }    
                    ?>
                </tbody>
            </div>
        </table>
        <button type="button" onclick="location.href='./racewalk_display.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>&page=<?php echo $page;?>'">HEAT 1</button>
        <form action="official_result.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type=hidden name=schedule_id value=<?=$_GET['schedule_id'] ?>>
            <input type="submit" value="Official Result"  style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="startlist.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type="submit" value="Start list"  style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        <form action="schedule_id_insert.php" method="get" name="form" style="display:inline;">
            <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
            <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
            <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
            <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
            <input type="submit" value="처음으로"  style="width:100px; height:30px; margin-bottom: 30px;">
        </form>
        
    </div>
</body>
</html>
