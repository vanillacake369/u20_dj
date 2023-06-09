<?php
    require_once __DIR__ . "/../head.php";
    // 접근 권한 체크 함수
    require_once __DIR__ . "/../backheader.php";
    // 수정 권한 시에만 기록 전환 접근 가능
    if (!authCheck($db, "authRecordsUpdate")) {
        exit("<script>
        alert('수정 권한이 없습니다.');
        history.back();
    </script>");
    }
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 
    $s_id = $_GET['id'];
    $sql = "SELECT DISTINCT * FROM list_record INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$s_id'";
    $result = $db->query($sql);
    
    $rows = mysqli_fetch_assoc($result);
    $schedule_round = $rows['schedule_round'];
    $schedule_result = $rows['schedule_result'];
    $schedule_sports = $rows['schedule_sports'];
    if ($rows['schedule_result'] == 'o') {
        $result_type = 'official';
    } else {
        $result_type = 'live';
    }
    $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$s_id'";
    $judgeresult = $db->query($judgesql);
    $judgerow = mysqli_fetch_array($judgeresult);
    ?>
<link rel="stylesheet" type="text/css" href="../assets/DataTables/datatables.min.css" />
<script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="../assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="../assets/js/useDataTables.js"></script>
<script type="text/javascript" src="../assets/js/onlynumber.js"></script>
<script type="text/javascript" src="../assets/js/change_athletics.js"></script>
<script type="text/javascript" src="../action/record/result_field_horizontal_execute_excel(exclude_wind).js"></script>

<script type="text/javascript">
window.onload = function() {
    for (k = 0; k < document.querySelectorAll('#name').length; k++) {
        let a = document.querySelectorAll('#name')[k];
        fieldFinal(a);
        console.log(k)
    }
    rankcal()
}
$(document).on("click", "button[name='addtempresult']", function() {
    $(".signup_submit").append("<input type='hidden' name=tempstore value='1'>");
});
$(document).on("click", "button[name='addresult']", function() {
    $(".signup_submit").append("<input type='hidden' name=tempstore value='0'>");
});

function input_time() {
    var today = new Date();
    var year = today.getFullYear();
    var month = ('0' + (today.getMonth() + 1)).slice(-2);
    var day = ('0' + today.getDate()).slice(-2);
    var dateString = year + '-' + month + '-' + day;
    var hours = ('0' + today.getHours()).slice(-2);
    var minutes = ('0' + today.getMinutes()).slice(-2);
    var seconds = ('0' + today.getSeconds()).slice(-2);
    var timeString = hours + ':' + minutes + ':' + seconds;
    let total = dateString + " " + timeString;
    let intime = document.querySelector("input[name='starttime']")
    intime.value = total
}
</script>
</head>

<body>
    <div class="schedule_container">
        <div class="result_tit">
            <div class="result_list2">
                <p class="tit_left_blue"><?= $rows['schedule_name'] ?>
                    <? echo $schedule_round=='final'?'결승전':($schedule_round=='semi-final'?'준결승전':'예선전')?>
                </p>
            </div>
            <div class="result_list">
                <? echo '<p class="defaultBtn'; 
                echo $schedule_result=='o'?' BTN_DarkBlue">마감중</p>':($schedule_result=='l'?'
                BTN_Blue">진행중</p>':' BTN_yellow ">대기중</p>');?>
            </div>
        </div>
        <form method="post" class="form">
            <input name="schedule_id" value="<?=$_GET['id'];?>" hidden>
            <input name="round" value="<?=$schedule_round?>" hidden>
            <input name="schedule_sports" value="<?=$schedule_sports?>" hidden>
            <div class="schedule schedule_flex">
                <div class="schedule_filed filed_list_item">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">조 편성</p>
                        <? echo '<span class="defaultBtn';
                            echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                        ?>
                    </div>
                    <h3 style="width:45%; display:inline-block; margin-right: 4.6%;">심판 이름</h3>
                    <h3 style="width:auto;  display:inline-block;">경기 시작 시간</h3>
                    <div class="btn_base base_mar" style="width:auto; margin-left:10px; display:inline-block;">
                        <?php
                        if ($rows['schedule_status'] != 'y') {
                            echo '<input type="button" onclick="input_time()" class="btn_add bold" value="현재 시간" />';
                        }
                        ?>
                    </div>
                    <input type="hidden" name="schedule_id" value="<?= $s_id ?>">
                    <div class="input_row" style="width:45%; margin-right: 4.6%;">
                        <?php
                        echo '<input placeholder="심판 이름" type="text" name="refereename" class="input_text" value="' . ($judgerow['judge_name']) . '"
                        maxlength="30" required="" readonly />';
                        ?>
                    </div>
                    <div class="input_row" style="width:50%;">
                        <?php
                        echo '<input placeholder="시작 시간" type="text" name="starttime" class="input_text" value="' . ($rows['schedule_start']) . '"
                        maxlength="30" required="" />';
                        ?>
                    </div>
                    <h3>용기구</h3>
                    <div class="input_row">
                        <?php
                        echo '<input placeholder="용기구" type="text" name="weight" class="input_text" value="' . $rows['record_weight'] . '" maxlength="16"
                    required="" />';
                        ?>
                    </div>

                    <table class="box_table">
                        <colgroup>
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 7%" />
                            <col style="width: 12%" />
                            <?php
                                if ($rows['schedule_name'] === 'Decathlon' || $rows['schedule_name'] === 'Heptathlon') {
                                    echo '<col style="width: 12%" />';
                                    echo '<col style="width: 12%" />';
                                    echo '<col style="width: 12%" />';
                                    echo '<col style="width: 12%" />';
                                } else {
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                }
                                ?>
                            <col style="width: 6%" />
                            <col style="width: 12%" />
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th>등수</th>
                                <th>순서</th>
                                <th>BIB</th>
                                <th>이름</th>
                                <th>1차 시기</th>
                                <th>2차 시기</th>
                                <th>3차 시기</th>
                                <?php
                                if ($rows['schedule_name'] === 'Decathlon' || $rows['schedule_name'] === 'Heptathlon') {
                                } else {
                                    echo '<th>4차 시기</th>';
                                    echo '<th>5차 시기</th>';
                                    echo '<th>6차 시기</th>';
                                }
                                ?>
                                <th>기록</th>
                                <th>비고</th>
                                <th>신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody entry_table" id="body">
                            <?php
                            $i = 1;
                            $count = 0; //신기록 위치 관련 변수
                            $trial = 1;
                            $num = 0;
                            $order = "record_order";
                            $obj = "record_" . $result_type . "_result,record_memo,record_" . $result_type . "_record,record_wind,";
                            if ($rows["schedule_status"] === "y") {
                                $order = "record_" . $result_type . "_result";
                                $check = 'record_' . $result_type . '_result>0';
                            } elseif ($_POST["check"] ?? null === "5") {
                                $trial = 6;
                                $check = 'record_trial =' . $trial . '';
                            } elseif ($_POST["check"] ?? null === "3") {
                                $trial = 4;
                                $check = 'record_trial =' . $trial . '';
                            } else {
                                $trial = 1;
                                $check = 'record_trial =' . $trial . '';
                            }
                            $sql2 =
                                "SELECT DISTINCT  " .
                                $obj .
                                "athlete_bib,athlete_id AS a_id,(SELECT record_order FROM list_record WHERE record_schedule_id = '$s_id' AND record_trial='$trial' AND record_athlete_id = a_id) AS record_order ,athlete_name,record_new,schedule_sports  FROM list_record
                                    INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                                    INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                                    where $check AND schedule_id = '$s_id'
                                    ORDER BY $order ASC";
                            $result2 = $db->query($sql2);
                            while ($id = mysqli_fetch_array($result2)) {
                                $num++;
                                echo '<tr';
                                if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                                echo '<td><input type="number" name="rank[]" class="input_text" id="rank" value="' . ($id['record_' . $result_type . '_result'] ?? null) . '" min="1" required="" /></td>';
                                echo '<td><input type="number" name="rain[]" class="input_text" value="';
                                if ($id['record_order'] >= 9 && $_POST["check"] ?? null >= 3) {
                                    echo '-';
                                } else {
                                    echo $id['record_order'];
                                }
                                echo '" min="1" max="12" required="" readonly /></td>';
                                echo '<td><input placeholder="등번호" type="text" id="bib" name="playerbib[]" class="input_text"
                                value="' . $id['athlete_bib'] . '" maxlength="30" required="" readonly /></td>';
                                echo '<td><input placeholder="선수 이름" type="text" id="name" name="playername[]" class="input_text"
                                value="' . $id['athlete_name'] . '" maxlength="30" required="" readonly /></td>';
                                $answer = $db->query("SELECT record_" . $result_type . "_record FROM list_record join list_athlete ON athlete_id = record_athlete_id where record_athlete_id = '" . $id['a_id'] . "' AND record_schedule_id = '$s_id' ORDER BY record_trial ASC");
                                while ($row = mysqli_fetch_array($answer)) {
                                    echo '<td>';
                                    echo '<input placeholder="경기 결과" type="text" name="gameresult' . ($i) . '[]" class="input_text" value="' . ($row['record_' . $result_type . '_record'] ?? null) . '"
                                        maxlength="5" onkeyup="field1Format(this)"
                                        style="float: left; width: auto; padding-right: 5px"/>';
                                    echo '</td>';
                                    $i++;
                                }
                                if ($rows['schedule_name'] === 'Decathlon' || $rows['schedule_name'] === 'Heptathlon') {
                                    $k = 3;
                                } else {
                                    $k = 6;
                                }
                                for ($j = $i; $j <= $k; $j++) {
                                    echo "<td>";
                                    echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                                        $j .
                                        '[]" class="input_text" value=""
                                    maxlength="5" onkeyup="field1Format(this)"';
                                    echo 'style="float: left; width: auto; padding-right: 5px" />';
                                    echo "</td>";
                                }
                                echo '<td>';
                                echo '<input placeholder="경기 결과" id="result" type="text" name="gameresult[]" class="input_text"
                                    value="' . ($id["record_" . $result_type . "_record"] ?? null) . '" maxlength="5" required="" onkeyup="field1Format(this)"
                                    style="float: left; width: auto; padding-right: 5px" />';
                                echo '<input type="hidden" name="compresult[]" value="' . ($id["record_" . $result_type . "_record"] ?? null) . '"/>';
                                echo '</td>';
                                echo '<td><input type="text" placeholder ="비고"name="bigo[]" class="input_text" value="' .
                                    ($id["record_memo"] ?? null) .
                                    '" maxlength="100" /></td>';
                                if ($rows['schedule_name'] === 'Decathlon' || $rows['schedule_name'] === 'Heptathlon') {
                                    $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
                                } else {
                                    $sport_code = $rows['schedule_sports'];
                                }
                                if ($id['record_new'] == 'y') {
                                    if ($rows['schedule_status'] != 'y') {
                                        $time = $rows['schedule_start'];
                                    } else {
                                        $time = $rows['schedule_end'];
                                    }
                                    $athletics = check_my_record($id['athlete_name'], $sport_code, $time);
                                    if ((key($athletics) ?? null) === 'w') {
                                        echo '<td><input placeholder=""  type="text" name="newrecord[]" class="input_text" value="세계신기록';
                                        echo '" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'u') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="세계U20신기록';
                                        echo '" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '"  record="' . $id["record_" . $result_type . "_record"] . '"readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'a') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아신기록';
                                        echo '" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 's') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아U20신기록';
                                        echo '" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'c') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="대회신기록';
                                        echo '" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                    } else {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                    }
                                } else {
                                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $id['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $s_id . '" record="' . $id["record_" . $result_type . "_record"] . '" readonly/></td>';
                                }
                                $i = 1;
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="filed_BTN">
                        <div>
                            <?php
                            if ($rows["schedule_status"] != "y") {
                                if (($rows["schedule_name"] == 'Decathlon' || $rows["schedule_name"] == 'Heptathlon')) {
                                } else {
                                    echo '<button type="submit" class="defaultBtn BIG_btn BTN_blue filedBTN" formaction="/action/record/three_try_after_reverse.php"
                                style="width:auto; padding-left:5px; padding-right:5px;"><span>순서 재정렬</span></button>';
                                }
                            } else {
                                echo ' <div class="btn_base base_mar col_left">
                                    <input type="button" onclick="execute_excel()" class="btn_excel bold" value="엑셀 출력" />
                                </div>
                                <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN" formaction="field_normal_result_pdf.php"><span>PDF 출력</span></button>
                                <button type="submit" class="defaultBtn BIG_btn excel_Print filedBTN" formaction="field_normal_result_word.php"><span>워드 출력</span></button>
                                ';
                            }
                            if ($_POST['check'] ?? null === '3') {
                                echo '<input type="hidden" name="count" value= "5">';
                            } else {
                                echo '<input type="hidden" name="count" value= "3">';
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <h3>경기 비고</h3>
                        <div class="input_row">
                            <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="input_text"
                                value="<?= ($rows['schedule_memo'] ?? null) ?>" maxlength=" 100" />
                        </div>
                        <div style='display:flex; width:100%;'>
                            <?php
                            if ($rows["schedule_status"] != "y") {
                                echo '<div class="signup_submit" style="width:49%; margin-right:1%">
                                <button type="submit" class="btn_login" name="addtempresult"
                                    formaction="../action/record/field_normal_result_insert.php">
                                    <span>임시저장</span>
                                </button>
                            </div>';
                                echo '<div class="signup_submit" style="width:49%;">
                                <button type="submit" class="btn_login" name="addresult"
                                    formaction="../action/record/field_normal_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>';
                        }else{
                            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                            <div class="signup_submit" style="width:100%;">
                                <button type="submit" class="btn_login" name="addresult"
                                    formaction="../action/record/field_normal_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>
                            <?php }
                            elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                            <div class="signup_submit" style="width:100%;">
                                <button type="submit" class="btn_login" name="addresult"
                                    formaction="../action/record/field_normal_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>
                            <?php } 
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>