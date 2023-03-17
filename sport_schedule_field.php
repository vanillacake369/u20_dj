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

    $ids = $_GET['id'];
    $num = 0;
    $count = 0;
    //@Potatoeunbi
    //대분류의 소분류들 record, athlete, schedule 정보들을 모두 가져오도록 하는 sql문
    $sql = "SELECT distinct a.athlete_name, a.athlete_country,a.athlete_id,a.athlete_bib,s.schedule_id,s.schedule_sports,s.schedule_result, s.schedule_status,s.schedule_round,s.schedule_group,
    (SELECT record_order FROM list_record WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND record_trial='1') AS r_order,
    (SELECT if(s.schedule_status='o',r.record_official_result,record_live_result) FROM list_record WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND if(s.schedule_status='o',r.record_official_result,record_live_result)>0) AS result,
    (SELECT if(s.schedule_status='o',r.record_official_record,record_live_record) FROM list_record WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND if(s.schedule_status='o',r.record_official_result,record_live_result)>0) AS record,
    (SELECT record_wind FROM list_record  WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND record_live_result>0) AS wind,
    (SELECT record_memo FROM list_record  WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND record_live_result>0) AS memo,
    (SELECT record_new FROM list_record  WHERE r.record_schedule_id=record_schedule_id AND record_athlete_id=a.athlete_id AND record_live_result>0) AS record_new 
    from list_record AS r 
    JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
    JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id 
    WHERE record_schedule_id 
    IN (SELECT s1.schedule_id 
    FROM list_schedule AS s1 
    right OUTER join list_schedule AS s2 
    ON (s2.schedule_id= '" . $ids . "'
    and s1.schedule_sports=s2.schedule_sports 
    AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender) WHERE s1.schedule_division='s')
    ORDER BY if(s.schedule_status='n',r.record_order,result);";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $schedule_id = $row['schedule_id'];
    $schedule_sports = $row['schedule_sports'];
    $schedule_result = $row['schedule_result'];
    $schedule_round = $row['schedule_round'];
    $total_count = mysqli_num_rows($result);
    if (empty($total_count)) {
        echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
    }
    ?>

<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script src="/assets/js/jquery-1.12.4.min.js"></script>
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
        <form action="">
            <input name="id" value="<?=$_GET['id'];?>" hidden>
            <div class="schedule schedule_flex">
                <div class="schedule_filed filed_list_item">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">1조</p>
                        <? echo '<span class="defaultBtn';
                            echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                        ?>
                    </div>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 15%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 10%" />
                        </colgroup>
                        <thead class="result_table entry_table">
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
                                    echo  "<th colspan='7'>풍속</th>";
                                }
                                echo "<th> 신기록</th>";
                                echo "</tr>";
                                ?>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table">
                            <?php

                        $i = 1;
                        $k = 1;
                        $j = 0;
                        $result = $db->query($sql);
                        while ($row = mysqli_fetch_array($result)) {
                            if ($row['schedule_group'] != $k) {
                        ?>
                        </tbody>
                    </table>
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
                                echo '<button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN" onclick="window.open(\'';
                                if ($schedule_sports == "polevault" || $schedule_sports == "highjump") {
                                    echo "/record/field_vertical_result_view.php?id=" . $schedule_id;
                                } else if ($schedule_sports == "longjump" || $schedule_sports == "triplejump") {
                                    echo "/record/field_horizontal_result_view.php?id=" . $schedule_id;
                                } else {
                                    echo "/record/field_normal_result_view.php?id=" . $schedule_id;
                                }
                                echo '\')">기록 입력</button>';
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id='.$schedule_id.'\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?
            $k++;
            if ($total_count != $j){
                $count=0;
        ?>
        <form action="">
            <input name="schedule_id" value="<?=$ids;?>" hidden>
            <input name="round" value="<?=$schedule_round?>" hidden>
            <input name="schedule_sports" value="<?=$schedule_sports?>" hidden>
            <div class="schedule_filed filed_list_item">
                <div class="schedule_filed_tit">
                    <p class="tit_left_yellow"><?=$k?>조</p>
                    <? echo '<span class="defaultBtn';
                            echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                        ?>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width: 4%" />
                        <col style="width: 4%" />
                        <col style="width: 4%" />
                        <col style="width: 15%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 10%" />
                    </colgroup>
                    <thead class="result_table entry_table">
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
                                    echo  "<th colspan='7'>풍속</th>";
                                }
                                echo "<th> 신기록</th>";
                                echo "</tr>";
                                ?>
                        </tr>
                        <tr class="filed2_bottom">
                        </tr>
                    </thead>
                    <tbody class="table_tbody De_tbody entry_table">
                        <?}
                            }
                            if ($row['schedule_group'] == $k) {
                            $num++;
                            echo "<tr";
                            if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                            echo "<td rowspan='2'><input type='number' value='" .
                                ($row['r_order'] ?? null) .
                                "' min='1' max='12' required='' readonly /></td>";
                            echo "<td rowspan='2'><input placeholder='등수' type='number' name='rank[]' id='rank' value='" .
                                ($row['result'] ?? null) .
                                "' min='1' max='12' required=''  readonly/></td>";

                            echo "<td rowspan='2'><input placeholder='BIB' type='text' name='playerbib[]' value='" .
                                $row["athlete_bib"] .
                                "' maxlength='30' required='' readonly /></td>";
                            echo "<td rowspan='2'><input placeholder='선수 이름' type='text' name='playername[]' value='" .
                                $row["athlete_name"] .
                                "' maxlength='30' required='' readonly /></td>";
                            $answer = $db->query(
                                "SELECT record_live_record,record_wind,record_new FROM list_record
                                INNER JOIN list_athlete ON record_athlete_id=" .
                                    $row["athlete_id"] .
                                    " AND athlete_id= record_athlete_id
                                INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                AND schedule_id = $schedule_id
                                ORDER BY record_trial ASC"
                            );
                            while ($id = mysqli_fetch_array($answer)) {
                                echo "<td rowspan=";
                                jump($schedule_sports);
                                echo ">";
                                echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                                    $i .
                                    '[]" value="' .
                                    ($id["record_live_record"] ?? null) .
                                    '"
                                maxlength="5" onkeyup="field1Format(this)" readonly />';
                                echo "</td>";
                                $i++;
                            }
                            echo "<td rowspan=";
                            jump($schedule_sports);
                            echo ">";
                            echo '<input placeholder="경기 결과" id="result" type="text" name="gameresult[]"
                                value="' .
                                ($row["record"] ?? null) .
                                '" maxlength="5" required="" onkeyup="field1Format(this)"
                                    style="float: left; width: auto; padding-right: 5px"  readonly/>';
                            echo "</td>";
                            echo '<td><input type="text" placeholder ="비고"name="bigo[]" value="' .
                                ($row["memo"] ?? null) .
                                '" maxlength="100"  readonly/></td>';
                            echo "<tr>";

                            if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                                for ($j = 0; $j <= 6; $j++) {
                                    $wind = $db->query("SELECT record_wind FROM list_record
                                    INNER JOIN list_athlete ON record_athlete_id=" .
                                        $row["athlete_id"] .
                                        " AND athlete_id= record_athlete_id
                                    INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                    AND schedule_id = '" . $schedule_id . "'
                                    ORDER BY record_live_record ASC limit 6 ");
                                    $windrow = mysqli_fetch_array($wind);
                                    if ($j % 7 == 6) {
                                        echo "<td>";
                                        echo '<input placeholder="풍속" type="text" name="lastwind[]" class="input_text" value="' .
                                            ($row["wind"] ?? null) .
                                            '"
                                                maxlength="5" required="" onkeyup="windFormat(this)" readonly/>';
                                        echo "</td>";
                                    } else {
                                        echo "<td>";
                                        echo '<input placeholder="풍속" type="text" name="wind' .
                                            ($j + 1) .
                                            '[]" value="' .
                                            ($windrow["record_wind"] ?? null) .
                                            '"
                                    maxlength="5" required="" onkeyup="windFormat(this)" readonly />';
                                        echo "</td>";
                                    }
                                }
                            }

                            //@Potatoeunbi
                            //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                            //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                            world($db, $row['athlete_name'], $row['record_new'], $schedule_sports, ($row["record"] ?? null));

                            echo "</tr>";
                            echo "</tr>";
                            $count++;
                        $j++;
                        if ($j == $total_count) { ?>
                    </tbody>
                </table>
                <div class="filed_BTN">
                    <div>
                        <button type="button" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                            onclick="window.open('/award_ceremony.html')">전광판 보기</button>
                        <button type="button" class="defaultBtn BIG_btn BTN_purple filedBTN"
                            onclick="window.open('/electronic_display.html')">시상식 보기</button>
                        <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                            formaction="/record/field_normal_result_pdf.php">PDF 출력</button>
                        <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                            formaction="/record/field_normal_result_word.php">워드 출력</button>
                        <button type="submit" class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                    </div>
                    <div>
                        <?php
                            // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                            if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                                echo '<button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN" onclick="window.open(\'';
                                if ($schedule_sports == "polevault" || $schedule_sports == "highjump") {
                                    echo "/record/field_vertical_result_view.php?id=" . $schedule_id;
                                } else if ($schedule_sports == "longjump" || $schedule_sports == "triplejump") {
                                    echo "/record/field_horizontal_result_view.php?id=" . $schedule_id;
                                } else {
                                    echo "/record/field_normal_result_view.php?id=" . $schedule_id;
                                }
                                echo '\')">기록 입력</button>';
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id='.$schedule_id.'\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                            }?>
                        <?      }
            }
        }?>
                    </div>
                </div>
            </div>
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
                            <th>선수 국가</th>
                        </tr>
                        <tr class="filed2_bottom">
                        </tr>
                    </thead>
                    <tbody class="table_tbody De_tbody entry_table">
                        <?php
                    $Subsql = "SELECT record_id, athlete_country, athlete_id, record_order,schedule_result FROM list_record 
                    INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                    INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                    WHERE schedule_id = $schedule_id and record_trial=1 ORDER BY record_order ASC;";

                    $Subresult = $db->query($Subsql);
                    $i = 1;
                    global $num;
                    $num = 0;
                    while ($row = mysqli_fetch_array($Subresult)) {
                        echo "<tr";
                        if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                        echo "<td>" . $i . "번</td>";
                        ?>
                        <td><a <?php if ($row['schedule_result'] == 'n') { ?>
                                onclick="createPopupWin('sport_change_member.php?athlete=<?php echo $row['athlete_id'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $schedule_id ?>&sport=field','창 이름',900,512)"
                                <?php } ?>><?php echo htmlspecialchars($row['athlete_country']) ?></a>
                        </td>
                        </tr>
                        <?php
                            $i++;
                        } ?>
                    </tbody>
                </table>
                <?php
            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                <div class="signup_submit">
                    <button type="button" class="btn_login" name="addresult" onclick="window.close()">
                        <span>확인</span>
                    </button>
                </div>
                <?php }
                    elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                <div class="signup_submit">
                    <button type="button" class="btn_login" name="addresult" onclick="window.close()">
                        <span>확인</span>
                    </button>
                </div>
                <?php } ?>
            </div>
    </div>
    <div class="BTNform">
        <button type="button" class="nextBTN BTN_blue2 defaultBtn"
            onclick="window.open('/forming_group.html', 'window_name', 'width=800, height=750, location=no, status=no, scrollbars=yes')">다음
            조 편성</button>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $ids ?>">
            <button type="submit" class="resetBTN BTN_Orange2 defaultBtn" name="">모든 조 초기화</button>
        </form>
    </div>
    <button type="button" class="changePwBtn defaultBtn">확인</button>
    </div>
</body>


</html>