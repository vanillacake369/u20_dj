<?php
    require_once "head.php";
    require_once __DIR__ . "/includes/auth/config.php";
    require_once __DIR__ . "/security/security.php";
    require_once __DIR__ . '/action/module/schedule_worldrecord.php';
    require_once __DIR__ . "/backheader.php";

    if (!authCheck($db, "authSchedulesRead")) {
        exit("<script>
        alert('잘못된 접근입니다.');
        history.back();
    </script>");
    }
    
    $count = 0;
    $num = 0;
    $id = $_GET['id'];

    $sports_sql = "SELECT schedule_sports, schedule_result, schedule_round, schedule_result, schedule_gender, schedule_name FROM list_schedule where schedule_id=$id;";
    $sports_result = $db->query($sports_sql);
    $sports_row = mysqli_fetch_array($sports_result);
    $schedule_sports = $sports_row['schedule_sports'];
    $schedule_result = $sports_row['schedule_result'];
    $schedule_round = $sports_row['schedule_round'];
    $schedule_gender = $sports_row['schedule_gender'];
    $schedule_name = $sports_row['schedule_name'];
    $relay_order='';
    $page_move = 'track';
    if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') { 
    $relay_order=', r.record_team_order ASC';
    $page_move = 'relay';
    }
    if ($sports_row['schedule_result'] != 'n') {
        if ($sports_row['schedule_result'] == 'l') {
            $result_order = 'r.record_live_result';
        } else {
            $result_order = 'r.record_official_result';
        }
    } else {
        $result_order = 'r.record_order';
    }
    $sql = "SELECT r.*,a.*,s.schedule_group,s.schedule_sports,s.schedule_result from list_record AS r JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id WHERE record_schedule_id 
    IN (SELECT s1.schedule_id FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON (s2.schedule_id= '" . $id . "' and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round=s2.schedule_round ) WHERE s1.schedule_division='s') ORDER BY s.schedule_group ASC, $result_order ASC $relay_order ;";
    $result = $db->query($sql);
    $total_count = mysqli_num_rows($result);
    $athrecord = array();
    if (empty($total_count)) {
        echo "<script>alert('세부 경기 일정이 없습니다.');  location.href='./sport_schedulemanagement.php';</script>";
    }


    $idsql = "SELECT s1.schedule_id FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON (s2.schedule_id= '" . $id . "' and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round=s2.schedule_round ) WHERE s1.schedule_division='s' ORDER BY s1.schedule_group ASC";
    $idresult = $db->query($idsql);
    while($idrow = mysqli_fetch_array($idresult)){
        $schedule_ids[] = $idrow['schedule_id'];
    }
    

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
                <p class="tit_left_blue"><?=$schedule_sports?>
                    <? echo $schedule_round=='final'?'결승전':($schedule_round=='semi-final'?'준결승전':'예선전')?>
                </p>
            </div>
            <div class="result_list">
                <? echo '<p class="defaultBtn'; 
                echo $schedule_result=='o'?' BTN_DarkBlue">마감중</p>':($schedule_result=='l'?'
                BTN_Blue">진행중</p>':' BTN_yellow ">대기중</p>');?>
            </div>
        </div>
        <div class="schedule schedule_flex filed_high_flex">
            <div class="schedule_filed filed_list_item filed_container">
                <!-- class="contents something" -->
                <div class="schedule_filed_tit">
                    <p class="tit_left_yellow">1조</p>
                    <? echo '<span class="defaultBtn';
                        echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                    ?>
                </div>

                <form action="#" method="get" class="form">
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
                                <th scope="col" colspan="1"><?= islane($schedule_sports, '상단') ?></th>
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
                            if ($row['schedule_group'] != $k) {
                        ?>
                        </tbody>
                    </table>
                    <input type=hidden name=schedule_result value=<?=$schedule_result ?>>
                    <input type=hidden name=schedule_sports value=<?=$schedule_sports ?>>
                    <input type=hidden name=schedule_gender value=<?=$schedule_gender ?>>
                    <input type=hidden name=schedule_name value=<?=$schedule_name ?>>
                    <input type=hidden name=schedule_round value=<?=$schedule_round ?>>
                    <input type=hidden name=schedule_group value=<?=$k ?>>
                    <input type=hidden name=schedule_id value=<?=$id ?>>
                    <div class="filed_BTN">
                        <div>
                            <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                formaction="electronic_display.php">전광판 보기</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Red filedBTN">PDF 출력</button>
                            <button type="button" class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                        </div>
                        <div>
                            <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                onclick="window.open(' <? $record_insert=$k-1; if ($schedule_sports==" 4x100mR" ||
                                $schedule_sports=="4x400mR" ) { echo "/record/track_relay_result_view.php?id="
                                .$schedule_ids[$record_insert] ; } else {
                                echo "/record/track_normal_result_view.php?id=" .$schedule_ids[$record_insert] ; }
                                ?>')">기록 입력</button>
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
                    <p class="tit_left_yellow"><?=$k?>조</p>
                    <? echo '<span class="defaultBtn';
                            echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                            ?>
                </div>
                <form action="#" method="get" class="form">
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
                            <?
                        if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR')
                            echo "<col style='width: auto;'>";
                    ?>
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th scope="col" colspan="1"><?= islane($schedule_sports, '상단') ?></th>
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
                                        
                                        if ($num%2==0) echo ' class="Ranklist_Background">'; else echo '>';
                                        echo '<td><input type="number" class="input_text" value="' . $row['record_order'] . '" min="1" max="12" required="" readonly /></td>';
                                        echo "<td><input type='number' id='rank' class='input_text' value=";
                                        echo $row['record_status'] == 'o' ? $row['record_official_result'] : ($row['record_status'] == 'l' ? $row['record_live_result'] : '');
                                        echo " min='1' max='12' required='' /></td>";
                                        echo '<td>';
                                    }
                                    //@Potatoeunbi
                                    //릴레이 팀의 마지막 주자인 경우
                                    if ($count % 4 == 3) {
                                        echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                    class="input_text" value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly/></td>';
                                        for ($t = $count - 3; $t <= $count; $t++) {
                                            if ($t == $count - 3) {
                                                echo '<td>';
                                            }
                                            if ($t == $count) {
                                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                            class="input_text" value="' . $athname[$t] . '" maxlength="30" required="" readonly/></td>';
                                            } else {
                                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                            class="input_text" value="' . $athname[$t] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                            }
                                        }
                                        echo '<td><input placeholder="소속" type="text" name="division" class="input_text" value="' . $row['athlete_country'] . '"maxlength="50" required="" readonly/></td>';
                                        echo '<td>
                                <input placeholder="경기 결과" type="text" id="result" class="input_text"
                                    value="' . (($athrecord[3] ?? null) ? $athrecord[3] : '') . '" maxlength="8" required="" onkeyup="trackFinal(this)"
                                        style="float: left; width: 80px; padding-right: 5px" readonly/>
                                    </div>
                                    </div></td>';
                                        echo '<td>
                                <input placeholder="" type="text" id="result" class="input_text"
                                    value="' . (($row['record_reaction_time'] ?? null) ? $row['record_reaction_time'] : '') . '" maxlength="8" required="" onkeyup="trackFinal(this)"
                                        style="float: left; width: 80px; padding-right: 5px" readonly/>
                                    </div>
                                    </div></td>';

                                        //@Potatoeunbi
                                        //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                                        //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                                        echo '<td><input placeholder="비고" type="text" class="input_text" value="' . $row['record_memo'] . '" maxlength="100" /></td>';
                                        world($db, $row['athlete_country'], $row['record_new'], $schedule_sports, (($athrecord[3] ?? null) ? $athrecord[3] : ''));
                                        $athrecord[3] = null;
                                    } else {
                                        //@Potatoeunbi
                                        //릴레이 팀의 2, 3번째 주자인 경우
                                        echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                        class="input_text" value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                    }
                                    $count++;
                                } else {
                                    //@Potatoeunbi
                                    //릴레이가 아닌 트랙일 경우
                        ?>

                            <tr>
                                <td><input type="number" name="rain[]" class="input_text"
                                        value="<?php echo htmlspecialchars($row['record_order']) ?>" min="1" max="12"
                                        required="" readonly />
                                </td>
                                <td><input type="number" name="rank[]" class="input_text"
                                        value="<?php echo ($row['record_status'] == 'o') ? htmlspecialchars($row['record_official_result']) : htmlspecialchars($row['record_live_result']) ?>"
                                        min="1" max="12" required="" /></td>
                                <td><input placeholder="등번호" type="text" name="playerbib[]" class="input_text"
                                        value="<?php echo htmlspecialchars($row['athlete_bib']) ?>" maxlength="30"
                                        required="" readonly />
                                </td>
                                <td><input placeholder="선수 이름" type="text" name="playername[]" class="input_text"
                                        value="<?php echo htmlspecialchars($row['athlete_name']) ?>" maxlength="30"
                                        required="" readonly />
                                </td>
                                <td><input placeholder="경기 결과" type="text" name="gameresult[]" class="input_text"
                                        value="<?php echo ($row['record_status'] == 'o') ? htmlspecialchars($row['record_official_record']) : htmlspecialchars($row['record_live_record']) ?>"
                                        maxlength="3" required="" style="
                                        " /></td>
                                <td><input placeholder="경기 결과" type="text" name="reactiontime[]" class="input_text"
                                        value="<?php echo htmlspecialchars($row['record_reaction_time']) ?>"
                                        maxlength="3" required="" style="
                                        " /></td>
                                <td><input placeholder="비고" type="text" name="bigo" class="input_text"
                                        value="<?= htmlspecialchars($row['record_memo']) ?>" maxlength="100" /></td>
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
                    <input type=hidden name=schedule_result value=<?=$schedule_result ?>>
                    <input type=hidden name=schedule_sports value=<?=$schedule_sports ?>>
                    <input type=hidden name=schedule_gender value=<?=$schedule_gender ?>>
                    <input type=hidden name=schedule_name value=<?=$schedule_name ?>>
                    <input type=hidden name=schedule_round value=<?=$schedule_round ?>>
                    <input type=hidden name=schedule_group value=<?=$k ?>>
                    <input type=hidden name=schedule_id value=<?=$id ?>>
                    <div class="filed_BTN">
                        <div>
                            <button type="button" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                onclick="window.open('/award_ceremony.html')">전광판 보기</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Red filedBTN">PDF
                                출력</button>
                            <button type="button" class="defaultBtn BIG_btn excel_Print filedBTN">엑셀
                                출력</button>
                        </div>
                        <div>
                            <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN">기록
                                입력</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php }
                }
        ?>
            <?
                        
    if ($schedule_sports != '10000m' && $schedule_sports != '5000m' && $schedule_sports != '3000m' && $schedule_sports != '3000mSC'){ ?>
            <div class="schedule_filed changefiled">
                <div class="profile_logo">
                    <img src="/assets/images/logo.png">
                </div>
                <div class="schedule_filed_tit schedule_Orange">
                    <p class="tit_left_Orange"><?= islane($schedule_sports, '변경') ?></p>
                </div>
                <table cellspacing="0" cellpadding="0" class="team_table">
                    <tr class="filed_change">
                        <th></th>
                        <?php
                $groupsql = "SELECT max(s.schedule_group) from list_schedule AS s 
                WHERE schedule_id 
                IN (SELECT s1.schedule_id FROM list_schedule AS s1 
                right OUTER join list_schedule AS s2 
                ON (s2.schedule_id= '" . $id . "' 
                and s1.schedule_sports=s2.schedule_sports 
                AND s1.schedule_name=s2.schedule_name 
                AND s1.schedule_gender=s2.schedule_gender 
                AND s1.schedule_round=s2.schedule_round ) 
                WHERE s1.schedule_division='s');";

                $groupresult = $db->query($groupsql);
                $row = mysqli_fetch_array($groupresult);

                for ($i = 1; $i <= $row[0]; $i++) {
                    echo "<th>" . $i . "조</th>";
                }
                ?>
                    </tr>

                    <tr>

                        <?php 
                
                $lanesql="SELECT MIN(record_order) from list_schedule AS s INNER JOIN list_record AS r ON r.record_schedule_id=s.schedule_id
                WHERE s.schedule_id 
                IN (SELECT s1.schedule_id FROM list_schedule AS s1 
                right OUTER join list_schedule AS s2 
                ON (s2.schedule_id= '" . $id . "' 
                and s1.schedule_sports=s2.schedule_sports 
                AND s1.schedule_name=s2.schedule_name 
                AND s1.schedule_gender=s2.schedule_gender 
                AND s1.schedule_round=s2.schedule_round ) 
                WHERE s1.schedule_division='s');";
                $laneresult = $db->query($lanesql);
                $lanerow = mysqli_fetch_array($laneresult);
                
                ?>
                        <td><?=$lanerow[0]?><?= islane($schedule_sports, '상세') ?></td>
                        <?php
                $Subsql = "SELECT r.record_id, r.record_order, a.athlete_country,a.athlete_id, s.schedule_group from list_record AS r 
                    JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
                    JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
                    WHERE record_schedule_id 
                        IN (SELECT s1.schedule_id FROM list_schedule AS s1 
                        right OUTER join list_schedule AS s2 
                        ON (s2.schedule_id= '" . $id . "' 
                        and s1.schedule_sports=s2.schedule_sports 
                        AND s1.schedule_name=s2.schedule_name 
                        AND s1.schedule_gender=s2.schedule_gender 
                        AND s1.schedule_round=s2.schedule_round ) 
                        WHERE s1.schedule_division='s') 
                        ORDER BY r.record_order ASC, s.schedule_group ASC;";

                $Subresult = $db->query($Subsql);
                $i = 1;
                $j = $lanerow[0];
                while ($row = mysqli_fetch_array($Subresult)) {
                    if ($row["record_order"] == $j && $row["schedule_group"] == $i) {
                ?>
                        <td><a <?php if ($schedule_result == 'n') {
                                    if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') { ?>
                                onclick="createPopupWin('sport_change_relay_member.php?country=<?php echo $row['athlete_country'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $id ?>&sport=<?php echo $schedule_sports ?>','창 이름',900,512)"
                                <?php } else { ?>
                                onclick="createPopupWin('sport_change_member.php?athlete=<?php echo $row['athlete_id'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $id ?>&sport=track','창 이름',900,512)"
                                <?php }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } ?>><?php echo htmlspecialchars($row['athlete_country']) ?></a>
                        </td>
                        <?php
                        $i++;
                    } else if ($row["record_order"] != $j) {
                        $j++;
                        $i = 2;
                    ?>
                    </tr>
                    <tr>
                        <td><?php echo $j ?><?= islane($schedule_sports, '상세') ?></td>
                        <td><a <?php if ($schedule_result == 'n') {
                            if ($schedule_sports == '4x400mR' || $schedule_sports == '4x100mR') { ?>
                                onclick="createPopupWin('sport_change_relay_member.php?country=<?php echo $row['athlete_country'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $id ?>&sport=<?php echo $schedule_sports ?>','창 이름',900,512)"
                                <?php } else { ?>
                                onclick="createPopupWin('sport_change_member.php?athlete=<?php echo $row['athlete_id'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $id ?>&sport=track','창 이름',900,512)"
                                <?php }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } ?>><?php echo htmlspecialchars($row['athlete_country']) ?></a>
                        </td>
                        <?php }
                } ?>
                    </tr>

                </table>
                <div class="signup_submit">
                    <button type="button" class="btn_login" name="addresult" onclick="window.close()">
                        <span>확인</span>
                    </button>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="BTNform">
            <button type="button" class="nextBTN BTN_blue2 defaultBtn"
                onclick="window.open('/forming_group.html', 'window_name', 'width=800, height=750, location=no, status=no, scrollbars=yes')">다음
                조 편성</button>
            <button type="button" class="resetBTN BTN_Orange2 defaultBtn">모든 조 초기화</button>
        </div>
        <button type="button" class="changePwBtn defaultBtn">확인</button>
    </div>
    <script src="/assets/js/main.js"></script>
    <script src="assets/js/restrict.js"></script>
</body>

</html>