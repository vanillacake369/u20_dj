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
    <title>medal</title>
    <?php
    include_once $_SERVER["DOCUMENT_ROOT"].'/database/dbconnect.php';
    $schedule_sports = $_GET['schedule_sports'];
    $schedule_gender = $_GET['schedule_gender'];
    $schedule_round = $_GET['schedule_round'];
    $schedule_group = $_GET['schedule_group'];
    $schedule_id = $_GET['schedule_id'];
    $page = $_GET['page'];

    // //schedule_id 
    // $sql1 = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ?
    //         AND schedule_gender = ? AND schedule_round = ?";
    // $stmt = $db -> prepare($sql1);
    // $stmt -> bind_param('sss', $schedule_sports,  $schedule_gender, $schedule_round);
    // $stmt -> execute();
    // $result1 = $stmt -> get_result();
    // $row2 = mysqli_fetch_array($result1);
    // $schedule_id = $row2['schedule_id'];


    $sql = "SELECT DISTINCT record_official_result, athlete_name, record_new, athlete_country, record_medal
            FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
            ON schedule_id= record_schedule_id WHERE record_medal > 0 AND schedule_id = ?
            ORDER BY record_medal DESC";
    $stmt = $db -> prepare($sql);
    $stmt -> bind_param('s', $schedule_id);
    $stmt -> execute();
    $result = $stmt -> get_result();

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
    $sql = "SELECT worldrecord_record FROM list_worldrecord WHERE worldrecord_sports = ? AND worldrecord_gender = ? AND worldrecord_athletics= 'w';";
    $stmt = $db -> prepare($sql);
    $stmt -> bind_param('ss', $schedule_sports,  $schedule_gender);
    $stmt -> execute();
    $result4 = $stmt -> get_result();
    $row4 = mysqli_fetch_array($result4);
?>
</head>

<body>
    <h2 style="display:inline;"><?php echo htmlspecialchars($row3["sports_name"]);//종목 ?></h2>
    <h2 style="display:inline;"><?php echo htmlspecialchars($gender);//성별 ?></h2>
    <h3>VICTORY CEREMONY</h3>
    <table>
        <colgroup>
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col">순위</th>
                <th scope="col">국가</th>
                <th scope="col">국기</th>
                <th scope="col">이름</th>

            </tr>
        </thead>
        <tbody>
            <?php
            if($page == 3){
                $sql = "SELECT DISTINCT record_official_result, athlete_name, record_new, athlete_country, record_medal
                        FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                        ON schedule_id= record_schedule_id WHERE record_medal = 1 AND schedule_id = ?
                        ORDER BY record_medal DESC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                if($schedule_sports == "4x400mR"||$schedule_sports == "4x100mR"){
                    $cnt = 0;
                    while($row = mysqli_fetch_array($result)){
                        if($cnt==0){
                            echo '<tr>';
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                            echo "<td style='text-align: center;'>";
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }else if($cnt == 3){
                                echo $row["athlete_name"].'<br>';
                                echo "</td>";//선수 국가
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center;'>국기</tb>";
                                echo "</tr>"; 
                                $cnt=0;
                            }else{
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }   
                        }
                }else{
                    while($row = mysqli_fetch_array($result)){
                        echo '<tr>';
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수국가(athlete country)
                        echo "<td style='text-align: center;'>국기</td>";//국기 이미지
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수이름(athlete name)

                        echo '</tr>';
                    }
                }
            }else if($page == 2){
                $sql = "SELECT DISTINCT record_official_result, athlete_name, record_new, athlete_country, record_medal
                        FROM list_record INNER JOIN list_athlete ON athlete_id = record_athlete_id INNER JOIN list_schedule 
                        ON schedule_id= record_schedule_id WHERE (record_medal < 101 AND record_medal >0) AND schedule_id = ?
                        ORDER BY record_medal DESC";
                $stmt = $db -> prepare($sql);
                $stmt -> bind_param('s', $schedule_id);
                $stmt -> execute();
                $result = $stmt -> get_result();
                if($schedule_sports == "4x400mR"||$schedule_sports == "4x100mR"){
                    $cnt = 0;
                    while($row = mysqli_fetch_array($result)){
                        if($cnt==0){
                            echo '<tr>';
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                            echo "<td style='text-align: center;'>";
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }else if($cnt == 3){
                                echo $row["athlete_name"].'<br>';
                                echo "</td>";//선수 국가
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center;'>국기</tb>";
                                echo "</tr>"; 
                                $cnt=0;
                            }else{
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }   
                        }
                }else{
                    while($row = mysqli_fetch_array($result)){
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수국가(athlete country)
                        echo "<td style='text-align: center;'>국기</td>";//국기 이미지
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수이름(athlete name)

                        echo '</tr>';
                    }
                }
            }else{
                if($schedule_sports == "4x400mR"||$schedule_sports == "4x100mR"){
                    $cnt = 0;
                    while($row = mysqli_fetch_array($result)){
                        if($cnt==0){
                            echo '<tr>';
                            echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                            echo "<td style='text-align: center;'>";
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }else if($cnt == 3){
                                echo $row["athlete_name"].'<br>';
                                echo "</td>";//선수 국가
                                echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수 국가
                                echo "<td style='text-align: center;'>국기</tb>";
                                echo "</tr>"; 
                                $cnt=0;
                            }else{
                                echo $row["athlete_name"].'<br>';
                                $cnt++;
                            }   
                        }
                }else{
                    while($row = mysqli_fetch_array($result)){
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["record_official_result"])."</td>";//순위(official Result)
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_country"])."</td>";//선수국가(athlete country)
                        echo "<td style='text-align: center;'>국기</td>";//국기 이미지
                        echo "<td style='text-align: center;'>".htmlspecialchars($row["athlete_name"])."</td>";//선수이름(athlete name)

                        echo '</tr>';
                    }
                }
            }
                ?>
        </tbody>
    </table>
    <button type="button"
        onclick="location.href='./medal.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>&page=3'">동메달</button>
    <button type="button"
        onclick="location.href='./medal.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>&page=2'">은메달</button>
    <button type="button"
        onclick="location.href='./medal.php?schedule_sports=<?php echo $schedule_sports;?>&schedule_round=<?php echo $schedule_round;?>&schedule_group=<?php echo $schedule_group;?>&schedule_gender=<?php echo $schedule_gender;?>&schedule_id=<?php echo $schedule_id;?>&page=1'">금메달</button>
    </br>
    <form action="official_result.php" method="get" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
        <?php if($schedule_round == "110m 허들"){
                echo '<input type=hidden name=schedule_round value="110m 허들">';
            }else if($schedule_round == "100m 허들"){
                echo '<input type=hidden name=schedule_round value="100m 허들">';
            }else{
                echo '<input type=hidden name=schedule_round value='.$_GET['schedule_round'] .'>';
            }?>
        <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
        <input type=hidden name=schedule_id value=<?=$_GET['schedule_id'] ?>>
        <input type="submit" value="Official Result" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
    <form action="schedule_id_insert.php" method="GET" name="form" style="display:inline;">
        <input type=hidden name=schedule_sports value=<?=$_GET['schedule_sports'] ?>>
        <input type=hidden name=schedule_gender value=<?=$_GET['schedule_gender'] ?>>
        <input type=hidden name=schedule_round value=<?=$_GET['schedule_round'] ?>>
        <input type=hidden name=schedule_group value=<?=$_GET['schedule_group'] ?>>
        <input type="submit" value="처음으로" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
</body>

</html>