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
$sports = $_GET['sports'];
$gender = $_GET['gender'];
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
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
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
        <ul class="changeTableList">
            <li class="changeTableItem"><button class="changeBtn_color changeTableBtn" type="button">7종</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('100mh', '/sport_schedule_track.php')">100m 허들</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('highjump', '/sport_schedule_high_jump.php')">높이뛰기</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('200m', '/sport_schedule_track.php')">200m 허들</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('shotput', '/sport_schedule_field.php')">포환 던지기</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('longjump', '/sport_schedule_high_jump.php')">멀리뛰기</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('javelinthrow', '/sport_schedule_field.php')">창던지기</button></li>
            <li class="changeTableItem"><button class="changeTableBtn" type="button"
                    onclick="result_ajax('800m', '/sport_schedule_track.php')">800m</button></li>
        </ul>
        <div class="schedule schedule_flex filed_high_flex  TableList">
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
                    <input name="schedule_group" value="1" hidden>
                    <input name="schedule_sports" value="<?=$schedule_sports?>" hidden>
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
                                <th>순서</th>
                                <th>이름</th>
                                <th>총점</th>
                                <?php
                                //@Potatoeunbi
                                //기록입력 버튼
                                // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                                if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                                    for ($i = 0; $i < 7; $i++) {
                                        echo '<th>';
                                        echo $round[$i] . '</th>';
                                    }
                                } ?>
                                <th>비고</th>
                                <th>신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
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
                                echo "<td>" . htmlspecialchars($row['record_' . $result_type . '_result']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['athlete_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['record_' . $result_type . '_record']) . "</td>";

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
                                }
                                for ($i = 0; $i < (7-$table_count); $i++)
                                {
                                    echo "<td></td>";
                                }
                                echo "<td>" . htmlspecialchars($row['record_memo']) . "</td>";
                                //@Potatoeunbi
                                //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                                //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                                if ($row['record_' . $result_type . '_record']) {world($db, $row['athlete_name'], $row['record_new'], $sports, $row['record_' . $result_type . '_record']);}
                                else echo "<td></td>";
                                echo "</tr>";
                                echo "</tbody>";
                                $table_count = 0;
                                $people++;
                            } ?>

                    </table>
                    <div class="filed_BTN">
                        <div>
                            <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                formaction="electronic_display<?php echo $schedule_result == 'o' ? '_official' : ''; ?>.php">전광판
                                보기</button>
                            <?php if ($schedule_round == 'final') { ?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <?php } ?>
                            <?php
                            echo '<form action="" method="post">';
                                            echo '<input name="sports" value="' . $sports . '" hidden>';
                                            echo '<input name="gender" value="' . $gender . '" hidden>';                                    
                                            echo '<button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"';
                                            echo 'formaction =';
                                            echo '\'/record/mix7_pdf.php\'';
                                            echo '}" class="result_tableBTN BTN_Blue" value="기록 전환">PDF(한) 출력</button>';
                                            echo '<button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN" formaction="/record/mix7_pdf_eng.php">PDF(영) 출력</button>';
                                            echo '</form>';
                            ?>
                            <button type="submit" class="defaultBtn BIG_btn excel_Print filedBTN" formaction="">엑셀
                                출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="">워드
                                출력</button>
                        </div>
                    </div>
            </div>
        </div>
        </form>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="100mh_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="highjump_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="200m_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="shotput_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="longjump_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="javelinthrow_target">

        </div>
        <div class="schedule schedule_flex filed_high_flex  TableList" id="800m_target">

        </div>
        <button type="button" class="changePwBtn defaultBtn" onclick='window.close()'>확인</button>
    </div>
    <script src="assets/js/main.js?ver=10"></script>
    <script src="assets/js/restrict.js"></script>
    <script>
    function result_ajax(data, url) {
        $.ajax({
            url: url,
            type: "GET",
            data: {
                "sports": "heptathlon",
                "gender": "f",
                "round": data
            },
            success: function(result) {
                let regex = /<form[^>]*>((.|[\n\r])*)<\/form>/i;
                let match = regex.exec(result);
                $("#" + data + "_target").html(match[0]);
            },
        })
    }
    </script>
</body>

</html>