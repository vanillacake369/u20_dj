<?
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

    $statussql = "SELECT schedule_result, schedule_sports, schedule_round FROM list_schedule WHERE schedule_id='" . $id . "'";
    $statusresult = $db->query($statussql);
    $statusrow = mysqli_fetch_array($statusresult);
    $schedule_result = $statusrow['schedule_result'];
    $schedule_sports = $statusrow['schedule_sports'];
    $schedule_round = $statusrow['schedule_round'];
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
?>
    <!--Data Tables-->
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css" />
    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="js/useDataTables.js"></script>
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
        <form action="" method="post">
            <input name="id" value="<?=$_GET['id'];?>" hidden>
            <div class="schedule schedule_flex">
                <div class="schedule_filed filed_list_item">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">조 편성</p>
                        <? echo '<span class="defaultBtn';
                            echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                        ?>
                    </div>
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
                                <th rowspan="2">100mH</th>
                                <th rowspan="2">높이뛰기</th>
                                <th rowspan="2">포환던지기</th>
                                <th rowspan="2">200m</th>
                                <th rowspan="2">멀리뛰기</th>
                                <th rowspan="2">창던지기</th>
                                <th rowspan="2">800m</th>
                                <th>비고</th>
                            </tr>
                            <tr>
                                <th rowspan="4">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table" id='body'>
                        <?php
                            $i = 1;
                            $count = 0; //신기록시 셀렉트 박스 찾는 용도
                            $people = 0;
                            $num = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $num++;
                                echo "<tr";
                                if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $order_val . '_result']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['athlete_name']) . "</td>";
                                echo "<td rowspan='4'>" . htmlspecialchars($row['record_' . $order_val . '_record']) . "</td>";
                                echo "</tr>";
                                echo "<tr>";

                                $multi = "SELECT r.record_multi_record, r.record_" . $order_val . "_record, r.record_wind from list_record AS r 
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
                                            ORDER BY FIELD(schedule_round, '100mh', 'highjump', 'shotput','200m','longjump','javelinthrow','800m'), athlete_name;";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_multi_record']) . "</td>";
                                }
                                echo "<td>" . htmlspecialchars($row['record_memo']) . "</td>";
                                echo "</tr>";
                                echo "<tr>";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_' . $result_type . '_record']) . "</td>";
                                }

                                if ($row['record_' . $result_type . '_record']) world($db, $row['athlete_name'], $row['record_new'], $row['schedule_sports'], $row['record_' . $result_type . '_record']);

                                echo "</tr>";
                                echo "<tr>";
                                $answer = $db->query($multi);
                                while ($sub = mysqli_fetch_array($answer)) {
                                    echo "<td>" . htmlspecialchars($sub['record_wind'] == null ? ' ' : $sub['record_wind']) . "</td>";
                                }
                                echo "</tr>";
                                $people++;
                            } ?>
                        </tbody>
                    </table>
                    <h3>경기 비고</h3>
                    <div class="input_row">
                        <input placeholder="비고" type="text" name="gamepass" class="input_text" value="" maxlength="100" />
                    </div>
                    <div class="filed_BTN">
                        <div>
                            <button type="button" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                onclick="window.open('/award_ceremony.html')">전광판 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                onclick="window.open('/electronic_display.html')">시상식 보기</button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Red filedBTN">PDF 출력</button>
                            <button type="button" class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                        </div>
                        <div>
                        <?php
                            // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                            if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                                echo '<button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN" onclick="">기록 입력</button>';
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id=' . $schedule_id . '\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                            }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="schedule_filed changefiled">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="schedule_filed_tit schedule_Orange">
                <p class="tit_left_Orange">순서 변경</p>
            </div>
            <table class="box_table">
                <thead class="result_table entry_table">
                    <tr>
                        <th>순서</th>
                        <?php

                            $roundsql = "SELECT s1.schedule_id, s1.schedule_round FROM list_schedule AS s1 
                                            right OUTER join list_schedule AS s2 
                                            ON (s2.schedule_id= '" . $id . "' 
                                            and s1.schedule_sports=s2.schedule_sports 
                                            AND s1.schedule_name=s2.schedule_name 
                                            AND s1.schedule_gender=s2.schedule_gender 
                                            AND s1.schedule_round!='final') 
                                            WHERE s1.schedule_division='s'
                                            ORDER BY FIELD(s1.schedule_round, '100mh', 'highjump', 'shotput','200m','longjump','javelinthrow','800m');";
                            $roundresult = $db->query($roundsql);
                            $round_order = array('100m 허들', '높이뛰기', '투포환', '200m', '멀리뛰기', '창던지기', '800m');
                            $order = 0;
                            while ($roundrow = mysqli_fetch_array($roundresult)) {
                                echo "<th>" . $round_order[$order] . "</th>";
                                $schedule_ids[] = $roundrow['schedule_id'];
                                $order++;
                            }

                        ?>
                    </tr>
                    <tr class="filed2_bottom">
                    </tr>
                </thead>
                <tbody class="table_tbody entry_table">
                    <tr class="Ranklist_Background">
                    <?php
                    $k = 0;
                    $num = 0;
                    $schedule_str = implode(', ', $schedule_ids);
                    $subsql = "SELECT distinct athlete_country, athlete_id,record_order,record_status,schedule_round,schedule_sports FROM list_record 
                    INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                    INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                    WHERE schedule_id IN ( $schedule_str ) and record_order BETWEEN 1 AND $people GROUP BY athlete_country, athlete_id,record_order,schedule_round,schedule_sports ORDER BY record_order ASC, FIELD(schedule_round, '100mh', 'highjump', 'shotput','200m','longjump','javelinthrow','800m');";

                    $subresult = $db->query($subsql);
                    while ($subrow = mysqli_fetch_array($subresult)) {
                        $num++;
                        if ($k != $subrow['record_order']) {
                            echo "</tr>";
                            echo "<tr";
                            if ($num%2 == 1) echo ' class="Ranklist_Background">'; else echo ">";
                            echo "<td>" . ($k + 1) . "번</td>";
                            $k++;
                        } ?>
                        <td><a <?php if (trim($subrow['record_status']) == 'n') { ?> style="cursor:hand;" onclick="createPopupWin('sport_change_member_mix.php?athlete=<?= $subrow['athlete_id'] ?>&round=<?= $subrow['schedule_round'] ?>&code=<?= $subrow['schedule_sports'] ?>&sport=10종','창 이름',900,512)" <?php } ?>>
                                <?php echo htmlspecialchars($subrow['athlete_country']) ?></a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
                if (authCheck($db, "authSchedulesUpdate")) {  ?>
                <div class="signup_submit">
                  <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_vertical_result_insert.php">
                    <span>확인</span>
                  </button>
                </div>
            <?php } elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                <div class="signup_submit">
                    <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_vertical_result_insert.php">
                        <span>확인</span>
                    </button>
                </dlvi>
            <?php } ?>
        </div>
    <div class="BTNform">
        <button type="button" class="nextBTN BTN_blue2 defaultBtn"
            onclick="window.open('/sport_schedule_group_next.php?id=<?=$id?>', 'window_name', 'width=800, height=750, location=no, status=no, scrollbars=yes')">다음
            조 편성</button>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="resetBTN BTN_Orange2 defaultBtn" name="">모든 조 초기화</button>
        </form>
    </div>
    <button type="button" class="changePwBtn defaultBtn">확인</button>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/restrict.js"></script>
</body>

</html>