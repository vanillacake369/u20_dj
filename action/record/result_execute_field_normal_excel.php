<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . '/../../action/module/schedule_worldrecord.php';
global $db;
// TODO 이게되네 input 제거하고 <td>로 감싸기 졸려

$sports = $_POST['sports'];
$gender = $_POST['gender'];
$round = $_POST['round'];
$group = $_POST['group'];


$num = 0;
$count = 0;
//@Potatoeunbi
//대분류의 소분류들 record, athlete, schedule 정보들을 모두 가져오도록 하는 sql문
$sql = "SELECT *,if(r.record_status='o',r.record_official_result,record_live_result) as result,if(r.record_status='o',r.record_official_record,record_live_record) as record from list_record AS r
JOIN list_schedule AS s on r.record_sports=s.schedule_sports AND r.record_gender=s.schedule_gender AND r.record_round=s.schedule_round AND if(r.record_status ='n', r.record_trial='1',if(r.record_status='o',r.record_official_result>0,record_live_result>0))
JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id AND r.record_sports='$sports' AND r.record_gender='$gender' AND r.record_round='$round' AND r.record_group = '$group'
ORDER BY if(r.record_status='n',record_order,result);";

$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$schedule_sports = $row['schedule_sports'];
$schedule_result = $row['record_status'];
$schedule_round = $row['schedule_round'];
$schedule_name = $row['schedule_name'];
switch ($schedule_result) {
    case 'l':
        $schedule_result = "Live Result";
        break;
    case 'o':
        $schedule_result = "Official Result";
        break;
    case 'n':
        $schedule_result = "Not Start";
        break;
}
$total_count = mysqli_num_rows($result);
if (empty($total_count)) {
    echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
    exit();
}

$FILE_NAME = $sports . '_' . $gender . '_' . $round . '_' . $group . 'group(' . $schedule_result . ').xls';
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename = " . $FILE_NAME);     //filename = 저장되는 파일명을 설정합니다.
header("Content-Description: PHP4 Generated Data");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
?>
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
</style>

<table class="box_table">
    <colgroup>
        <col style="width: 4%"/>
        <col style="width: 4%"/>
        <col style="width: 4%"/>
        <col style="width: 15%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 7%"/>
        <col style="width: 10%"/>
    </colgroup>
    <thead class="result_table De_tbody entry_table">
    <tr>
        <?php
        echo "<th rowspan='2'>순서</th>";
        echo "<th rowspan='2'>등수</th>";
        echo "<th rowspan='2'>BIB</th>";
        echo "<th rowspan='2'>이름</th>";
        echo "<th rowspan=";
        //@Potatoeunbi
        //jump() 함수는 풍속이 있는 경기, 없는 경기에 따른 rowspan을 주기 위해서이다.
        jump($schedule_sports);
        echo ">1차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">2차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">3차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">4차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">5차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">6차 시기</th>";
        echo "<th rowspan=";
        jump($schedule_sports);
        echo ">기록</th>";
        echo "<th>비고</th>";
        echo "</tr>";
        if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
            echo "<tr>";
            echo "<th colspan='7'>풍속</th>";
        }
        echo "<th> 신기록</th>";
        echo "</tr>";
        ?>
    </tr>
    </thead>
    <tbody class="table_tbody De_tbody entry_table">
    <?php

    $i = 1;
    $k = 1;
    $j = 0;
    $result = $db->query($sql);
    while ($row = mysqli_fetch_array($result)) {
    if ($row['record_group'] != $k) {
    ?>
    </tbody>
</div>
</div>
</form>
<?php
$k++;
if ($total_count != $j){
$count = 0;
?>
        <table class="box_table">
            <colgroup>
                <col style="width: 4%"/>
                <col style="width: 4%"/>
                <col style="width: 4%"/>
                <col style="width: 15%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 7%"/>
                <col style="width: 10%"/>
            </colgroup>
            <thead class="result_table De_tbody entry_table">
            <tr>
                <?php
                echo "<th rowspan='2'>순서</th>";
                echo "<th rowspan='2'>등수</th>";
                echo "<th rowspan='2'>BIB</th>";
                echo "<th rowspan='2'>이름</th>";
                echo "<th rowspan=";
                //@Potatoeunbi
                //jump() 함수는 풍속이 있는 경기, 없는 경기에 따른 rowspan을 주기 위해서이다.
                jump($schedule_sports);
                echo ">1차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">2차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">3차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">4차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">5차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">6차 시기</th>";
                echo "<th rowspan=";
                jump($schedule_sports);
                echo ">기록</th>";
                echo "<th>비고</th>";
                echo "</tr>";
                if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                    echo "<tr>";
                    echo "<th colspan='7'>풍속</th>";
                }
                echo "<th> 신기록</th>";
                echo "</tr>";
                ?>
            </tr>
            <tr class="filed2_bottom">
            </tr>
            </thead>
            <tbody class="table_tbody De_tbody entry_table">
            <?php }
            }
            if ($row['record_group'] == $k) {
                $num++;
                echo "<tr";
                if ($num % 2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                echo "<td rowspan='2'>" . ($row['record_order'] ?? "&nbsp") . "</td>";
                echo "<td rowspan='2'>" . ($row['result'] ?? "&nbsp") . "</td>";
                echo "<td rowspan='2'>" . $row["athlete_bib"] . "</td>";
                echo "<td rowspan='2'>" . $row["athlete_name"] . "</td>";
                $answer = $db->query(
                    "SELECT record_live_record,record_wind,record_new FROM list_record 
                                        INNER JOIN list_athlete ON record_athlete_id='" . $row['athlete_id'] . "' 
                                        AND athlete_id= record_athlete_id and record_sports='$sports' 
                                        and record_round='$round' and record_gender='$gender' and record_group=" . $row['record_group'] . " ORDER BY record_trial ASC"
                );
                while ($id = mysqli_fetch_array($answer)) {
                    echo "<td rowspan=";
                    jump($schedule_sports);
                    echo ">";
                    echo ($id["record_live_record"] ?? "&nbsp");
                    echo "</td>";
                    if ($i == 6)
                        $i = 1;
                    else
                        $i++;
                }
                echo "<td rowspan=";
                jump($schedule_sports);
                echo ">";
                echo ($row["record"] ?? "&nbsp");
                echo "</td>";
                echo '<td>'.  ($row["memo"] ?? "&nbsp") . '</td>';
                echo "<tr";
                if ($num % 2 == 0) echo ' class="Ranklist_Background">'; else echo ">";

                if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                    for ($t = 0; $t <= 6; $t++) {
                        $wind = $db->query("SELECT record_wind FROM list_record
                                    INNER JOIN list_athlete ON record_athlete_id=" .
                            $row["athlete_id"] .
                            " AND athlete_id= record_athlete_id
                                    and record_sports='$sports' 
                                        and record_round='$round' and record_gender='$gender' and record_group=" . $row['record_group'] . "
                                    ORDER BY record_live_record ASC limit 6 ");
                        $windrow = mysqli_fetch_array($wind);
                        if ($t % 7 == 6) {
                            echo "<td>" . ($row["wind"] ?? "&nbsp") . "</td>";
                        } else {
                            echo "<td>" . ($windrow["record_wind"] ?? "&nbsp") . "</td>";
                        }
                    }
                }

                //@Potatoeunbi
                //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                if ($row['record_new'] == 'y') {
                    $newrecord = $db->query("SELECT worldrecord_athletics FROM list_worldrecord WHERE worldrecord_athlete_name ='" . $row['athlete_name'] . "' AND worldrecord_sports='".$schedule_sports."' and worldrecord_record='".($row["record"] ?? null)."'");
                    while ($athletics = mysqli_fetch_array($newrecord)) {
                        $newathletics[] = $athletics[0];
                    }
                    if(($newathletics[0]??null)==='w'){
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '>세계신기록';
                        echo '</td>';
                    }else if(($newathletics[0]??null)==='u'){
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '>세계U20신기록';
                        echo '</td>';
                    }else if(($newathletics[0]??null)==='a'){
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '>아시아신기록';
                        echo '</td>';
                    }else if(($newathletics[0]??null)==='s'){
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '>아시아U20신기록';
                        echo '</td>';
                    }else if(($newathletics[0]??null)==='c'){
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '>대회신기록';
                        echo '</td>';
                    }else{
                        echo '<td rowspan=';
                        overall($schedule_sports);
                        echo '></td>';
                    }
                } else {
                    echo '<td rowspan=';
                    echo overall($schedule_sports);
                    echo ' ></td>';
                }
                echo "</tr>";
                echo "</tr>";
                $count++;
                $j++;
            }
    }
            ?>
        </tbody>
</table>
</tbody>
</table>