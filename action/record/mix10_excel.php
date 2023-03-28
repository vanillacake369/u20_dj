<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../security/security.php";
require_once __DIR__ . '/../../action/module/schedule_worldrecord.php';
global $db;

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}

$sports = $_POST['sports'];
$gender = $_POST['gender'];
$round = $_POST['round'];
$group = $_POST['group'];

$statussql = "SELECT distinct record_round, schedule_sports,record_status, schedule_gender FROM list_schedule JOIN list_record  
where schedule_sports='$sports' and schedule_gender ='$gender' AND record_sports=schedule_sports AND record_gender=schedule_gender
ORDER BY FIELD(record_round,'final','100m','longjump','shotput','highjump','400m','100mh','discusthrow','polevault','javelinthrow','1500m'),FIELD(record_status,'o','l','n')";
$statusresult = $db->query($statussql);
$statusrow = mysqli_fetch_array($statusresult);
$schedule_result = $statusrow['record_status'];
$schedule_round = $statusrow['record_round'];
// $schedule_rounds=array();
// $schedule_rounds[]=$statusrow['record_round'];
$schedule_sports = $statusrow['schedule_sports'];
$schedule_gender = $statusrow['schedule_gender'];
// while($statusrow = mysqli_fetch_array($statusresult)){
//     $schedule_rounds[]=$statusrow['record_round'];
// }
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

$round=['100m','longjump','shotput','highjump','400m','100mh','discusthrow','polevault','javelinthrow','1500m'];
//@Potatoeunbi
//해당 경기의 record의 개수를 알기 위해서 사용하는 sql문
$sql = "SELECT DISTINCT r.record_live_result,r.record_live_record,r.record_memo,r.record_new,
a.athlete_name, 
a.athlete_country,
a.athlete_id,
s.schedule_sports
from list_record AS r 
JOIN list_schedule AS s on r.record_sports=s.schedule_sports AND r.record_gender=s.schedule_gender
JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
WHERE schedule_sports='decathlon' and schedule_gender ='m' AND r.record_round = 'final'
ORDER BY " . $order_val . ",record_trial;";
$result = $db->query($sql);
$total_count = mysqli_num_rows($result);
// if (empty($total_count)) {
//     echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
// }

$groupsql="SELECT distinct record_round AS r,(select count(distinct record_group) FROM list_record WHERE record_sports='decathlon' and record_gender ='m' AND record_round= r)AS cnt FROM list_record WHERE record_sports='decathlon' and record_gender ='m' 
AND record_sports=record_sports AND record_gender=record_gender AND record_round!='final'
ORDER BY FIELD(record_round, '100m', 'longjump', 'shotput','highjump','400m','110mh','discusthrow','polevault','javelinthrow','1500m')";

$groupresult = $db->query($groupsql);

$margin_left = array('10px', '20px', '35px', '42px', '35px', '23px', '40px', '70px', '60px', '30px');

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
        <col style="width: 3%"/>
        <col style="width: 13%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 6%"/>
        <col style="width: 6%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 7%"/>
        <col style="width: 8%"/>
        <col style="width: 6%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
    </colgroup>
    <thead class="result_table entry_table">
    <tr>
        <th rowspan="2">순위</th>
        <th rowspan="2">이름</th>
        <th rowspan="2">총점</th>
        <?php
        //@Potatoeunbi
        //기록입력 버튼
        // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
        if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
            for ($i = 0; $i < 10; $i++) {
                echo '<th rowspan="2" >';
                $grouprow = mysqli_fetch_assoc($groupresult);
                echo $round[$i] . '</th>';
            }
        } ?>
        <th>비고</th>
    </tr>
    <tr>
        <th>신기록</th>
    </tr>
    </thead>
    <?php
    $i = 1;
    $num = 0;
    $count = 0; //신기록시 셀렉트 박스 찾는 용도
    $people = 0;
    $table_count = 0;
    while ($row = mysqli_fetch_array($result)) {
        $num++;
        echo '<tbody class="table_tbody De_tbody entry_table';
        if ($num % 2 == 0) echo ' Ranklist_Background">'; else echo "\">";
        echo "<tr>";
        echo "<td rowspan='3'>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
        echo "<td rowspan='3'>" . htmlspecialchars($row['athlete_name']) . "</td>";
        echo "<td rowspan='3'>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";

        //@Potatoeunbi
        //해당 경기의 모든 종목들 record 가져오는 sql문
        $multi = "SELECT distinct r.record_multi_record, r.record_" . $result_type . "_record, r.record_wind from list_record AS r 
                                            join list_schedule AS s
                                            JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
                                            WHERE schedule_sports='$sports' and schedule_gender ='$gender' AND record_sports=schedule_sports AND record_gender=schedule_gender
                                            AND r.record_multi_record is not NULL AND record_live_result>0
                                            and athlete_id = '" . $row['athlete_id'] . "' 
                                            ORDER BY FIELD(schedule_round, '100m', 'longjump', 'shotput','highjump','400m','110mh','discusthrow','polevault','javelinthrow','1500m'), athlete_name;";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
            $table_count++;
        }
        for ($i = 0; $i < (10 - $table_count); $i++) {
            echo "<td>&nbsp</td>";
        }

        echo "<td>" . htmlspecialchars($row['record_memo']) . "</td>";

        echo "</tr>";
        echo "<tr>";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_' . $result_type . '_record']) . "</td>";
        }
        for ($i = 0; $i < (10 - $table_count); $i++) {
            echo "<td>&nbsp</td>";
        }
        //@Potatoeunbi
        //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
        //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
        if ($row['record_' . $result_type . '_record']) world($db, $row['athlete_name'], $row['record_new'], $row['schedule_sports'], $row['record_' . $result_type . '_record']);

        echo "</tr>";
        echo "<tr>";
        $answer = $db->query($multi);
        while ($sub = mysqli_fetch_array($answer)) {
            echo "<td>" . htmlspecialchars($sub['record_wind'] == null ? ' ' : $sub['record_wind']) . "</td>";
        }
        for ($i = 0; $i < (10 - $table_count); $i++) {
            echo "<td>&nbsp</td>";
        }
        echo "</tr></tbody>";
        $table_count = 0;
        $people++;
    }
?>

</table>