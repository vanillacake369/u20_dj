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

$count = 0;
$num = 0;
$sports = $_GET['sports'];
$gender = $_GET['gender'];
$round = $_GET['round'];
$sports_sql = "SELECT schedule_sports, schedule_round, record_status, schedule_gender, schedule_name FROM list_schedule JOIN list_record  where schedule_sports='$sports' and schedule_gender ='$gender' and schedule_round='$round' AND record_sports=schedule_sports AND record_gender=schedule_gender AND record_round=schedule_round ORDER BY FIELD(record_status,'o','l','n');";
$sports_result = $db->query($sports_sql);
$sports_row = mysqli_fetch_array($sports_result);
$schedule_sports = $sports_row['schedule_sports'];
$schedule_result = $sports_row['record_status'];
$schedule_round = $sports_row['schedule_round'];
$schedule_gender = $sports_row['schedule_gender'];
$schedule_name = $sports_row['schedule_name'];
$relay_order = '';
$page_move = 'track';
if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') {
    $relay_order = ', r.record_team_order ASC';
    $page_move = 'relay';
}
if ($sports_row['record_status'] != 'n') {
    if ($sports_row['record_status'] == 'l') {
        $result_order = 'r.record_live_result';
    } else {
        $result_order = 'r.record_official_result';
    }
} else {
    $result_order = 'r.record_order';
}
$sql = "SELECT r.*,a.*,r.record_group,s.schedule_sports,r.record_status
from list_record AS r
JOIN list_schedule AS s on r.record_sports=s.schedule_sports AND r.record_gender=s.schedule_gender AND r.record_round=s.schedule_round
JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id AND r.record_sports='$sports' AND r.record_gender='$gender' AND r.record_round='$round'
ORDER BY r.record_group ASC, $result_order ASC $relay_order ;";
$result = $db->query($sql);
$total_count = mysqli_num_rows($result);
$athrecord = array();
if (empty($total_count)) {
    // echo "<script>alert('세부 경기 일정이 없습니다.');  location.href='./sport_schedulemanagement.php';</script>";
}

$sql2 = "SELECT distinct record_status,record_group from list_record where record_sports='$sports' AND record_gender='$gender' AND record_round='$round' order by record_group";
$result2 = $db->query($sql2);

function islane($schedule_sports, $what)
{

    if ($what == '상단') {
        if ($schedule_sports == '10000m' || $schedule_sports == '5000m' || $schedule_sports == '3000m' || $schedule_sports == '3000mSC')
            echo "순서";
        else
            echo "레인";
    } else if ($what == '변경') {
        if ($schedule_sports == '10000m' || $schedule_sports == '5000m' || $schedule_sports == '3000m' || $schedule_sports == '3000mSC')
            echo "순서 변경";
        else
            echo "조 구성원 변경";
    } else if ($what == '상세') {
        if ($schedule_sports == '10000m' || $schedule_sports == '5000m' || $schedule_sports == '3000m' || $schedule_sports == '3000mSC')
            echo "번";
        else
            echo "레인";
    }
}

?>
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
</head>

<body>
    <div class="schedule_container">
        <div class="result_tit">
            <div class="result_list2">
                <p class="tit_left_blue"><?php echo  $schedule_sports ?>
                    <?php echo $schedule_round == 'final' ? '결승전' : ($schedule_round == 'semi-final' ? '준결승전' : '예선전') ?>
                </p>
            </div>
            <div class="result_list">
                <?php
                    $row2 = mysqli_fetch_array($result2);
                    echo '<p class="defaultBtn';
                    echo $row2['record_status'] == 'o' ? ' BTN_DarkBlue">마감중</p>' : ($row2['record_status'] == 'l' ? '
                    BTN_Blue">진행중</p>' : ' BTN_yellow ">대기중</p>'); ?>
            </div>
        </div>
        <div class="schedule schedule_flex filed_high_flex">
            <div class="schedule_filed filed_list_item filed_container">
                <!-- class="contents something" -->
                <div class="schedule_filed_tit">
                    <p class="tit_left_yellow">1조</p>
                    <?php echo '<span class="defaultBtn';
                    echo $schedule_result == 'o' ? ' BTN_green">Official Result</span>' : ($schedule_result == 'l' ? ' BTN_yellow">Live Result</span>' : ' BTN_green">Start List</span>');
                    ?>
                </div>

                <form action="#" method="post" class="form">
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 7%;">
                            <col style="width: 7%;">
                            <col style="width: 7%;">
                            <col style="width: 23%;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <?php
                            if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR')
                                echo "<col style='width: auto;'>";
                            ?>
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th scope="col" colspan="1"><?php echo  islane($schedule_sports, '상단') ?></th>
                                <th scope="col" colspan="1">등수</th>
                                <th scope="col" colspan="1">등번호</th>
                                <th scope="col" colspan="1">이름</th>
                                <?php
                                if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR')
                                    echo "<th scope='col' colspan='1'>국가</th>";
                                ?>
                                <th scope="col" colspan="1">기록</th>
                                <th scope="col" colspan="1">Reaction Time</th>
                                <th scope="col" colspan="1">비고</th>
                                <th scope="col" colspan="1">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class=" table_tbody De_tbody entry_table">
                            <?php
                            $k = 1;
                            $j = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                if ($row['record_group'] != $k) {
                            ?>
                        </tbody>
                    </table>
                    <input type=hidden name=result value=<?php echo  $schedule_result ?>>
                    <input type=hidden name=sports value=<?php echo  $schedule_sports ?>>
                    <input type=hidden name=gender value=<?php echo  $schedule_gender ?>>
                    <input type=hidden name=name value=<?php echo  $schedule_name ?>>
                    <input type=hidden name=round value=<?php echo  $schedule_round ?>>
                    <input type=hidden name=group value=<?php echo  $k ?>>
                    <!-- <input type=hidden name=wind value=<?php echo  $row['record_wind'] ?>> -->
                    <div class="filed_BTN">
                        <div>
                            <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                formaction="electronic_display<?php echo $schedule_result == 'o' ? '_official' : ''; ?>.php">전광판
                                보기</button>
                            <?php if ($schedule_round == ''){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <?php } ?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_pdf.php">PDF(한)
                                출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_eng_pdf.php">PDF(영)
                                출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_word.php">워드
                                출력</button>
                            <?php /*<form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                                    <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>"
                            hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="schedule_management" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                </form>*/?>
            </div>
            <div>
                <?php
                            // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                            if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                                echo '<button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="';
                                if ( $schedule_sports == " 4x100mR" || $schedule_sports == "4x400mR") {
                                    echo "/record/track_relay_result_view.php";
                                } else {
                                    echo "/record/track_normal_result_view.php";
                                }
                                echo '">기록 입력</button>';
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id='.$schedule_id.'\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                            }
                            ?>
            </div>
        </div>
        </form>
    </div>

    <?php
                                    $k++;
                                    if ($total_count != $j) {
                                        $count = 0;

            ?>
    <div class="schedule_filed filed_list_item filed_container">
        <!-- class="contents something" -->
        <div class="schedule_filed_tit">
            <p class="tit_left_yellow"><?php echo  $k ?>조</p>
            <?php
                        $row2 = mysqli_fetch_array($result2);
                        echo '<span class="defaultBtn';
                        echo $row2['record_status'] == 'o' ? ' BTN_green">Official Result</span>' : ($row2['record_status'] == 'l' ? ' BTN_yellow">Live Result</span>' : ' BTN_green">Start List</span>');
                    ?>
        </div>
        <form action="#" method="post" class="form">
            <table class="box_table">
                <colgroup>
                    <col style="width: 7%;">
                    <col style="width: 7%;">
                    <col style="width: 7%;">
                    <col style="width: 23%;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <?php
                                        if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR')
                                            echo "<col style='width: auto;'>";
                                ?>
                </colgroup>
                <thead class="result_table entry_table">
                    <tr>
                        <th scope="col" colspan="1"><?php echo  islane($schedule_sports, '상단') ?></th>
                        <th scope="col" colspan="1">등수</th>
                        <th scope="col" colspan="1">등번호</th>
                        <th scope="col" colspan="1">이름</th>
                        <?php
                                        if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR')
                                            echo "<th scope='col' colspan='1'>국가</th>";
                                    ?>
                        <th scope="col" colspan="1">기록</th>
                        <th scope="col" colspan="1">Reaction Time</th>
                        <th scope="col" colspan="1">비고</th>
                        <th scope="col" colspan="1">신기록</th>
                    </tr>
                    <tr class="filed2_bottom">
                    </tr>
                </thead>
                <tbody class=" table_tbody De_tbody entry_table">
                    <?php   }
                                }

                                if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') {

                                    //@Potatoeunbi
                                    //팀원의 모든 기록은 팀 기록이 들어감. 개인의 기록이 들어가지 않음 

                                    if ($row['record_status'] == 'l') {
                                        $athrecord[$count % 4] = $row['record_live_record'];
                                    } else if ($row['record_status'] == 'o') {
                                        $athrecord[$count % 4] = $row['record_official_record'];
                                    }
                                    $athname[$count] = $row['athlete_name'];
                                    //@Potatoeunbi
                                    //릴레이 팀의 첫 주자인 경우


                                    if ($count % 4 == 0) {
                                        $num++;
                                        echo '<tr id="rane' . $row['record_order'] . '"';

                                        if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                        else echo '>';
                                        echo '<td><input type="number"  name="rain[]" value="' . $row['record_order'] . '" min="1" required="" readonly /></td>';
                                        echo "<td><input type='number' name='rank[]' id='rank' value=";
                                        echo $row['record_status'] == 'o' ? $row['record_official_result'] : ($row['record_status'] == 'l' ? $row['record_live_result'] : '');
                                        echo " min='1' /></td>";
                                        echo '<td>';
                                    }
                                    //@Potatoeunbi
                                    //릴레이 팀의 마지막 주자인 경우
                                    if ($count % 4 == 3) {
                                        echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                     value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly/></td>';
                                        for ($t = $count - 3; $t <= $count; $t++) {
                                            if ($t == $count - 3) {
                                                echo '<td>';
                                            }
                                            if ($t == $count) {
                                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                             value="' . $athname[$t] . '" maxlength="30" required="" readonly/></td>';
                                            } else {
                                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                             value="' . $athname[$t] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                            }
                                        }
                                        echo '<td><input placeholder="소속" type="text" name="division"  value="' . $row['athlete_country'] . '"maxlength="50" required="" readonly/></td>';
                                        echo '<td>
                                <input placeholder="경기 결과" type="text" id="result" name="gameresult[]" 
                                    value="' . (($athrecord[3] ?? null) ? $athrecord[3] : '') . '" maxlength="8"  onkeyup="trackFinal(this)" readonly/>
                                    </div>
                                    </div></td>';
                                        echo '<td>
                                <input placeholder="" type="text" id="result" 
                                    value="' . (($row['record_reaction_time'] ?? null) ? $row['record_reaction_time'] : '') . '" maxlength="8"  onkeyup="trackFinal(this)" readonly/>
                                    </div>
                                    </div></td>';

                                        //@Potatoeunbi
                                        //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                                        //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                                        echo '<td><input placeholder="비고" type="text"  name="bigo[]" value="' . $row['record_memo'] . '" maxlength="100" /></td>';
                                        world($db, $row['athlete_country'], $row['record_new'], $schedule_sports, (($athrecord[3] ?? null) ? $athrecord[3] : ''));
                                        $athrecord[3] = null;
                                    } else {
                                        //@Potatoeunbi
                                        //릴레이 팀의 2, 3번째 주자인 경우
                                        echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                         value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                    }
                                    $count++;
                                } else {
                                    //@Potatoeunbi
                                    //릴레이가 아닌 트랙일 경우
                            ?>

                    <tr>
                        <td><input type="number" name="rain[]"
                                value="<?php echo htmlspecialchars($row['record_order']) ?>" min="1" required=""
                                readonly />
                        </td>
                        <td><input type="number" name="rank[]"
                                value="<?php echo ($row['record_status'] == 'o') ? htmlspecialchars($row['record_official_result']) : htmlspecialchars($row['record_live_result']) ?>"
                                min="1" /></td>
                        <td><input placeholder="등번호" type="text" name="playerbib[]"
                                value="<?php echo htmlspecialchars($row['athlete_bib']) ?>" maxlength="30" required=""
                                readonly />
                        </td>
                        <td><input placeholder="선수 이름" type="text" name="playername[]"
                                value="<?php echo htmlspecialchars($row['athlete_name']) ?>" maxlength="30" required=""
                                readonly />
                        </td>
                        <td><input placeholder="경기 결과" type="text" name="gameresult[]"
                                value="<?php echo ($row['record_status'] == 'o') ? htmlspecialchars($row['record_official_record']) : htmlspecialchars($row['record_live_record']) ?>"
                                maxlength="3" style="
                                        " /></td>
                        <td><input placeholder="경기 결과" type="text" name="reactiontime[]"
                                value="<?php echo htmlspecialchars($row['record_reaction_time']) ?>" maxlength="3"
                                style="
                                        " /></td>
                        <td><input placeholder="비고" type="text" name="bigo[]"
                                value="<?php echo  htmlspecialchars($row['record_memo']) ?>" maxlength="100" /></td>
                        <?php
                                    world($db, $row['athlete_name'], $row['record_new'], $schedule_sports, ($row['record_status'] == 'o') ? htmlspecialchars($row['record_official_record']) : htmlspecialchars($row['record_live_record']));
                                ?>
                    </tr>
                    <?php
                                }
                                $j++;
                                if ($j == $total_count) { ?>
                </tbody>
            </table>
            <input type=hidden name=result value=<?php echo  $schedule_result ?>>
            <input type=hidden name=sports value=<?php echo  $schedule_sports ?>>
            <input type=hidden name=gender value=<?php echo  $schedule_gender ?>>
            <input type=hidden name=name value=<?php echo  $schedule_name ?>>
            <input type=hidden name=round value=<?php echo  $schedule_round ?>>
            <input type=hidden name=group value=<?php echo  $k ?>>
            <!-- <input type=hidden name=wind value=<?php echo  $row['record_wind'] ?>> -->
            <div class="filed_BTN">
                <div>
                    <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                        formaction="electronic_display<?php echo $schedule_result == 'o' ? '_official' : ''; ?>.php">전광판
                        보기</button>
                    <?php if($schedule_round == 'final'){?>
                    <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                        formaction="award_ceremony.php">시상식 보기</button>
                    <?php } ?>
                    <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                        formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_pdf.php">PDF(한)
                        출력</button>
                    <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                        formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_eng_pdf.php">PDF(영)
                        출력</button>
                    <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                        formaction="/record/track_<?php if  ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') echo 'relay'; else echo 'normal';?>_result_word.php">워드
                        출력</button>
                    <?php /*<form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                                    <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>"
                    hidden />
                    <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                    <input type="text" name="role" value="schedule_management" hidden />
                    <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                        출력</label>
        </form>*/ ?>
    </div>
    <div>
        <?php
                            // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                            if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                                echo '<button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="';
                                if ( $schedule_sports == " 4x100mR" || $schedule_sports == "4x400mR") {
                                    echo "/record/track_relay_result_view.php";
                                } else {
                                    echo "/record/track_normal_result_view.php";
                                }
                                echo '">기록 입력</button>';
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id='.$schedule_id.'\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                            }
                            ?>
    </div>
    </div>
    </form>
    </div>
    <?php }
            }
        ?>
    </div>
    <?php if ($row2['record_status'] === 'o' && $round !== 'final') {
            // 경기 마감 상황일 때 그리고 라운드가 결승이 아닐 때 다음 조 편성 페이지 버튼 활성화
            // TODO 조 생성 권한 함수 추가 필요 ?>
    <button type="button" class="nextBTN BTN_blue2 defaultBtn"
        onclick="window.open('/sport_schedule_group_next.php?<?php echo 'sports=' . $sports . '&gender=' . $gender . '&round=' . $round . '\'' ?>, 'window_name', 'width=800, height=750, location=no, status=no, scrollbars=yes')">
        다음 조 편성
    </button>
    <?php } if (!in_array($schedule_round, ["preliminary-round", "qualification"]) && $row2['record_status'] === 'n') {
            // 자격라운드, 예선 경기를 제외한 경기일 때 그리고 경기 상태가 not start일 때 모든 조 초기화 버튼 활성화
            // TODO 조 수성 권한 함수 추가 필요 ?>
    <form action="#" method="post" class="filed2_form">
        <input type="hidden" name="sports" value="<?php echo $sports ?>">
        <input type="hidden" name="current_round" value="<?php echo $round ?>">
        <input type="hidden" name="gender" value="<?php echo $gender ?>">
        <input type="hidden" name="reset" value="reset">
        <button type="submit" class="resetBTN BTN_Orange2 defaultBtn"
            formaction="/action/sport/sport_create_next_round_group.php">모든 조 초기화</button>
    </form>
    <?php } ?>
    </form>
    <button type="button" class="changePwBtn defaultBtn">확인</button>
    </div>
    <script src="/assets/js/main.js?ver=9"></script>
    <script src="assets/js/restrict.js"></script>
</body>

</html>