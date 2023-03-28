<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../security/security.php";
require_once __DIR__ . '/../../action/module/schedule_worldrecord.php';

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
        alert('잘못된 접근입니다.');
        history.back();
    </script>");
}
$sports = $_POST['sports'];
$round = $_POST['schedule_round'];
$gender = $_POST['gender'];
$group = $_POST['schedule_group'];

$round=['100mh','highjump','200m','shotput','longjump','javelinthrow','800m'];
$statussql = "SELECT distinct record_round, schedule_sports,record_status, schedule_gender FROM list_schedule JOIN list_record  
where schedule_sports='$sports' and schedule_gender ='$gender' AND record_sports=schedule_sports AND record_gender=schedule_gender
ORDER BY FIELD(record_round,'final','100mh','highjump','200m','shotput','longjump','javelinthrow','800m'),FIELD(record_status,'o','l','n')";
$statusresult = $db->query($statussql);
$statusrow = mysqli_fetch_array($statusresult);
$schedule_sports = $statusrow['schedule_sports'];
$schedule_result = $statusrow['record_status'];
$schedule_round = $statusrow['record_round'];
$schedule_gender = $statusrow['schedule_gender'];
if ($schedule_result == 'o') {
    $result_type = 'official';
    $order_val = 'record_' . $result_type . '_result';
} else if ($schedule_result == 'l') {
    $result_type = 'live';
    $order_val = 'record_' . $result_type . '_result';
} else {
    $result_type = 'live';
    $order_val = 'athlete_name';
}
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

$FILE_NAME = $sports . '_' . $gender . '_' . $group . 'group(' . $schedule_result . ').xls';
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename = " . $FILE_NAME);     //filename = 저장되는 파일명을 설정합니다.
header("Content-Description: PHP4 Generated Data");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");


$sql = "SELECT DISTINCT r.record_live_result,r.record_live_record,r.record_memo,r.record_new,
a.athlete_name, 
a.athlete_country,
a.athlete_id,
s.schedule_sports
from list_record AS r 
JOIN list_schedule AS s on r.record_sports=s.schedule_sports AND r.record_gender=s.schedule_gender
JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
WHERE schedule_sports='heptathlon' and schedule_gender ='f' AND r.record_round = 'final'
ORDER BY " . $order_val . ",record_trial;";
$result = $db->query($sql);

$total_count = mysqli_num_rows($result);
$groupsql="SELECT distinct record_round AS r,(select count(distinct record_group) FROM list_record WHERE record_sports='heptathlon' and record_gender ='f' AND record_round= r)AS cnt FROM list_record WHERE record_sports='heptathlon' and record_gender ='f' 
AND record_sports=record_sports AND record_gender=record_gender AND record_round!='final'
ORDER BY FIELD(record_round, '100mh','highjump','200m','shotput','longjump','javelinthrow','800m')";
$groupresult = $db->query($groupsql);
// if (empty($total_count)) {
//     echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
// }
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
        <col style="width: 3%" />
        <col style="width: 13%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 9%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
    </colgroup>
    <thead class="result_table entry_table">
    <tr>
        <th rowspan='2'>순서</th>
        <th rowspan='2'>이름</th>
        <th rowspan='2'>총점</th>
        <?php
        //@Potatoeunbi
        //기록입력 버튼
        // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
        if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
            for ($i = 0; $i < 7; $i++) {
                echo '<th rowspan="2" >';
                $grouprow = mysqli_fetch_assoc($groupresult);
                for($t=1;$t<=($grouprow['cnt'] ?? 0);$t++){
                    echo '<form action="" method="post">';
                    echo '<input name="sports" value="' . $sports . '" hidden>';
                    echo '<input name="gender" value="' . $gender . '" hidden>';
                    echo '<input name="round" value="' . $round[$i] . '" hidden>';
                    echo '<input name="group" value="'.$t.'" hidden>';
                    echo '<button type="submit" formaction="';
                    if ($round[$i] == "100mh" || $round[$i] == "200m" || $round[$i] == "800m") {
                        echo "/record/track_normal_result_view.php";
                    } else if ($round[$i] == "javelinthrow" || $round[$i] == "shotput") {
                        echo "/record/field_normal_result_view.php";
                    } else if ($round[$i] == "highjump") {
                        echo "/record/field_vertical_result_view.php";
                    } else if ($round[$i] == "longjump") {
                        echo "/record/field_horizontal_result_view.php";
                    }
                    echo '"class="result_tableBTN BTN_DarkBlue">기록 입력</button>';
                    echo '</br>';
                    echo '<input type="submit" formaction="';
                    echo './record_change_type.php"';
                    echo 'class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                    echo '</form>';
                }
                echo '<br>' . $round[$i] . '</th>';
            }
        } ?> <th>비고</th>
    </tr>
    <tr>
        <th>신기록</th>
    </tr>
    </thead>

    <?php
    $i = 1;
    $count = 0; //신기록시 셀렉트 박스 찾는 용도
    $people = 0;
    $num = 0;
    $table_count = 0;
    while ($row = mysqli_fetch_array($result)) {
        $num++;
        echo '<tbody class="table_tbody De_tbody entry_table">';
        echo "<tr";
        if ($num % 2 == 0) echo ' class="Ranklist_Background">';
        else echo ">";
        echo "<td rowspan='3'>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
        echo "<td rowspan='3'>" . htmlspecialchars($row['athlete_name']) . "</td>";
        echo "<td rowspan='3'>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";

        $multi = "SELECT distinct r.record_multi_record, r.record_" . $result_type . "_record, r.record_wind from list_record AS r 
                                            join list_schedule AS s
                                            JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
                                            WHERE schedule_sports='$sports' and schedule_gender ='$gender' AND record_sports=schedule_sports AND record_gender=schedule_gender
                                            AND r.record_multi_record is not NULL AND record_live_result>0
                                            and athlete_id = '" . $row['athlete_id'] . "' 
                                            ORDER BY FIELD(schedule_round, '100mh','highjump','200m','shotput','longjump','javelinthrow','800m'), athlete_name;";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
            $table_count++;
        }
        for ($i = 0; $i < (7-$table_count); $i++)
        {
            echo "<td></td>";
        }
        echo "<td>" . htmlspecialchars($row['record_memo']) . "</td>";
        echo "</tr>";
        echo "<tr>";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_' . $result_type . '_record']) . "</td>";
        }
        for ($i = 0; $i < (7-$table_count); $i++)
        {
            echo "<td>&nbsp</td>";
        }
        //@Potatoeunbi
        //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
        //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
        if ($row['record_' . $result_type . '_record']) {world($db, $row['athlete_name'], $row['record_new'], $sports, $row['record_' . $result_type . '_record']);}
        else echo "<td rowspan=2></td>";
        echo "</tr>";
        echo "<tr>";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_wind'] == null ? ' ' : $sub['record_wind']) . "</td>";
        }
        for ($i = 0; $i < (7-$table_count); $i++)
        {
            echo "<td>&nbsp</td>";
        }
        echo "</tr>";
        echo "</tbody>";
        $table_count = 0;
        $people++;
    } ?>

</table>

