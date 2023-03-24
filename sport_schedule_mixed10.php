<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";
require_once 'action/module/schedule_worldrecord.php';
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}

$id = cleanInput($_GET['id']);

$statussql = "SELECT schedule_result, schedule_round, schedule_sports, schedule_group, schedule_gender FROM list_schedule WHERE schedule_id='" . $id . "'";
$statusresult = $db->query($statussql);
$statusrow = mysqli_fetch_array($statusresult);
$schedule_result = $statusrow['schedule_result'];
$schedule_round = $statusrow['schedule_round'];
$schedule_sports = $statusrow['schedule_sports'];
$schedule_group = $statusrow['schedule_group'];
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

//@Potatoeunbi
//해당 경기의 record의 개수를 알기 위해서 사용하는 sql문
$sql = "SELECT r.*,a.athlete_name, a.athlete_country,a.athlete_id,s.schedule_group,s.schedule_sports,s.schedule_result from list_record AS r 
    JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
    JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
    WHERE record_schedule_id 
    IN (SELECT s1.schedule_id 
    FROM list_schedule AS s1 
    right OUTER join list_schedule AS s2 
    ON (s2.schedule_id= '" . $id . "' 
    and s1.schedule_sports=s2.schedule_sports 
    AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender 
    AND s1.schedule_round='final') WHERE s1.schedule_division='s') 
    ORDER BY " . $order_val . ";";
$result = $db->query($sql);

$total_count = mysqli_num_rows($result);
if (empty($total_count)) {
    echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
}

//@Potatoeunbi
//기록 입력 버튼을 위해서 소분류의 schedule_id, schedule_round를 가져옴, 기록 입력 버튼 삭제 시 해당 sql문도 필요없음.
$idsql = "SELECT s1.schedule_id,s1.schedule_round, s1.schedule_sports, s1.schedule_gender,  FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON (s2.schedule_id='" . $id . "' and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round!='final' ) WHERE s1.schedule_division='s' ORDER BY FIELD(s1.schedule_round, '100m', 'longjump', 'shotput','highjump','400m','110mh','discusthrow','polevault','javelinthrow','1500m');";
$idresult = $db->query($idsql);
while ($idrow = mysqli_fetch_array($idresult)) {
    $schedule_ids[] = $idrow['schedule_id'];
    $schedule_rounds[] = $idrow['schedule_round'];
}
$margin_left = array('10px', '20px', '35px', '42px', '35px', '23px', '40px', '70px', '60px', '30px');

?>
<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
</head>

<body>
    <div class="schedule_container">
        <div class="result_tit">
            <div class="result_list2">
                <p class="tit_left_blue"><?= $schedule_sports ?>
                    <?php echo $schedule_round == 'final' ? '결승전' : ($schedule_round == 'semi-final' ? '준결승전' : '예선전') ?>
                </p>
            </div>
            <div class="result_list">
                <?php echo '<p class="defaultBtn';
                echo $schedule_result == 'o' ? ' BTN_DarkBlue">마감중</p>' : ($schedule_result == 'l' ? '
                BTN_Blue">진행중</p>' : ' BTN_yellow ">대기중</p>'); ?>
            </div>
        </div>
            
            <div class="schedule schedule_flex filed_high_flex decathlon_flex">
                <div class="schedule_filed filed_list_item decathlon_container">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">1조</p>
                        <?php echo '<span class="defaultBtn';
                        echo $schedule_result == 'o' ? ' BTN_green">Official Result</span>' : ($schedule_result == 'l' ? ' BTN_yellow">Live Result</span>' : ' BTN_green">Start List</span>');
                        ?>
                    </div>
                    <form action="" method="post">
                    
                    <input name="schedule_round" value="<?=$schedule_round?>" hidden>
                    <input name="schedule_gender" value="<?=$schedule_gender?>" hidden>
                    <input name="schedule_group" value="<?=$schedule_group?>" hidden>
                    <input name="schedule_sports" value="<?=$schedule_sports?>" hidden>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 3%" />
                            <col style="width: 13%" />
                            <col style="width: 5%" />
                            <col style="width: 5%" />
                            <col style="width: 5%" />
                            <col style="width: 6%" />
                            <col style="width: 6%" />
                            <col style="width: 5%" />
                            <col style="width: 5%" />
                            <col style="width: 7%" />
                            <col style="width: 8%" />
                            <col style="width: 6%" />
                            <col style="width: 5%" />
                            <col style="width: 5%" />
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
                                        echo '<form action="" method="post">';
                                        echo '<input name="schedule_round" value="' . $schedule_rounds[$i] . '" hidden>';
                                        echo '<input name="schedule_round" value="' . $schedule_rounds[$i] . '" hidden>';
                                        echo '<th rowspan="2" >';
                                        echo '<button type="submit" formaction="';
                                        if ($schedule_rounds[$i] == "100m" || $schedule_rounds[$i] == "100mh" || $schedule_rounds[$i] == "110mh" || $schedule_rounds[$i] == "200m" || $schedule_rounds[$i] == "400m" || $schedule_rounds[$i] == "800m" || $schedule_rounds[$i] == "1500m") {
                                            echo "/record/track_normal_result_view.php?id=" . $schedule_ids[$i];
                                        } else if ($schedule_rounds[$i] == "discusthrow" || $schedule_rounds[$i] == "javelinthrow" || $schedule_rounds[$i] == "shotput") {
                                            echo "/record/field_normal_result_view.php?id=" . $schedule_ids[$i];
                                        } else if ($schedule_rounds[$i] == "polevault" || $schedule_rounds[$i] == "highjump") {
                                            echo "/record/field_vertical_result_view.php?id=" . $schedule_ids[$i];
                                        } else if ($schedule_rounds[$i] == "longjump") {
                                            echo "/record/field_horizontal_result_view.php?id=" . $schedule_ids[$i];
                                        }
                                        echo '"class="result_tableBTN BTN_DarkBlue">기록 입력</button>';
                                        echo '<input name="schedule_id" value="'.  $schedule_ids[$i] . '" hidden>';
                                        echo '</br>';
                                        echo '<button type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                        echo 'location.href =';
                                        echo '\'./record_change_type.php?id=' . $schedule_ids[$i] . '\'';
                                        echo '}" class="result_tableBTN BTN_Blue" value="기록 전환">기록전환</button>';
                                        echo '<br>' . $schedule_rounds[$i] . '</th>';
                                    }
                                } ?>
                                <th>비고</th>
                            </tr>
                            <tr>
                                <th rowspan="4">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
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
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['athlete_name']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";
                                echo "</tr>";
                                echo "<tr>";

                                //@Potatoeunbi
                                //해당 경기의 모든 종목들 record 가져오는 sql문
                                $multi = "SELECT r.record_multi_record, r.record_" . $result_type . "_record, r.record_wind, s.schedule_id from list_record AS r 
                                            join list_schedule AS s on r.record_schedule_id=s.schedule_id 
                                            JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
                                            WHERE record_schedule_id 
                                            IN (SELECT s1.schedule_id FROM list_schedule AS s1 
                                            right OUTER join list_schedule AS s2 
                                            ON (s2.schedule_id= '" . $id . "' 
                                            and s1.schedule_sports=s2.schedule_sports 
                                            AND s1.schedule_name=s2.schedule_name 
                                            AND s1.schedule_gender=s2.schedule_gender 
                                            AND s1.schedule_round!='final') 
                                            WHERE s1.schedule_division='s') 
                                            AND r.record_multi_record is not NULL 
                                            and athlete_id = '" . $row['athlete_id'] . "' 
                                            ORDER BY FIELD(schedule_round, '100m', 'longjump', 'shotput','highjump','400m','110mh','discusthrow','polevault','javelinthrow','1500m'), athlete_name;";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
                                    $table_count++;
                                }
                                for ($i = 0; $i < (10-$table_count); $i++)
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
                                for ($i = 0; $i < (10-$table_count); $i++)
                                {
                                    echo "<td></td>";
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
                                for ($i = 0; $i < (10-$table_count); $i++)
                                {
                                    echo "<td></td>";
                                }
                                echo "</tr></tbody>";
                                $table_count = 0;
                                $people++;
                            } ?>
                    </table>
                    <div class="filed_BTN">
                        <div>
                            <button type="button" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN" onclick="window.open('/award_ceremony.html')">전광판 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_purple filedBTN" onclick="window.open('/electronic_display.html')">시상식 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Red filedBTN">PDF 출력</button>
                            <button type="button" class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
                <button type="button" class="changePwBtn defaultBtn">확인</button>
        </div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/restrict.js"></script>
</body>

</html>