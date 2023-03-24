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
$schedule_sports=$POST['sports'];
$schedule_round=$POST['round'];
$gender=$POST['gender'];
$group=$POST['group'];
require_once __DIR__ . "/../action/module/record_worldrecord.php";
require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 

$sql = "SELECT DISTINCT * FROM list_record  join list_schedule where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
$result=$db->query($sql);
$rows = mysqli_fetch_assoc($result);
if ($rows['schedule_sports'] == 'decathlon' || $rows['schedule_sports'] == 'heptathlon') {
    $check_round = 'y';
} else {
    $check_round = 'n';
}
$schedule_result = $rows['record_status'];
if ($rows['record_status'] == 'o') {
    $result_type = 'official';
} else {
    $result_type = 'live';
}
$judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
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

<div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="../assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    <?=$rows['schedule_name']?>
                </p>
                <form action="../action/record/track_normal_result_insert.php" method="post">
                    <input type="hidden" name="sports" value="<?= $schedule_sports ?>">
                    <input type="hidden" name="gender" value="<?= $gender ?>">
                    <input type="hidden" name="round" value="<?= $schedule_round ?>">
                    <input type="hidden" name="group" value="<?= $group ?>">
                    <div class="UserProfile_modify UserProfile_input thorw_main">
                        <div>
                            <ul class="UserDesc throwDesc">
                                <li class="row input_row throw_row">
                                    <span>경기 이름</span>
                                    <input placeholder="경기 이름" type="text" name="gamename"
                                        value="<?=$rows['schedule_name']?>" maxlength="16" required="" readonly />
                                </li>
                                <li class="row input_row throw_row">
                                    <span>라운드</span>
                                    <?php
                                    echo '<input placeholder="라운드" type="text" name="round" value="' . $rows['schedule_round'] . '"
                                    maxlength="16" required="" readonly />';
                                    ?>
                                </li>
                                <li class="row input_row throw_row">
                                    <span>심판 이름</span>
                                    <?php
                                    echo '<input placeholder="심판 이름" type="text" name="refereename"value="' . ($judgerow['judge_name']??null) . '"
                                        maxlength="30" required="" readonly />';
                                    ?>
                                </li>
                                <li class="row input_row throw_row">
                                    <span>경기 시작 시간</span>
                                  <?php
                                  echo '<input placeholder="시작 시간" type="text" name="starttime" value="'. ($rows['record_start']) .'"
                                  maxlength="30" required="" />';
                                if ($schedule_result != 'y') {
                                    echo '<input type="button" onclick="input_time()" class="btn_add bold" value="현재 시간" />';
                                }
                                ?>
                                </li>
                                <li class="row input_row throw_row">
                                    <span>용기구</span>
                                    <?php
                                        echo '<input placeholder="용기구" type="text" name="weight" class="input_text" value="' . $rows['record_weight'] . 'KG" maxlength="16"
                                    required="" />';
                                    ?>
                                </li>
                                
                              </div>
                            </ul>
                        </div>
                    </div>
                    <div class="Thorw_result">
                        <div class="relay_result">
                            <div class="result_BTN">
                                <h1 class="tit_padding tit_left_green">결과</h1>
                                <div>
                                <?php
                                    if (($rows["schedule_name"] == 'Decathlon' || $rows["schedule_name"] == 'Heptathlon')) {
                                    } else {
                                        echo '<button class="defaultBtn BIG_btn BTN_blue4" type="submit" formaction="/action/record/three_try_after_reverse.php">순서 재정렬</button>';
                                    }
                                    if ($rows['record_state'] != 'y') {
                                        echo '<button type="button" onclick="openTextFile()" class="defaultBtn BIG_btn pdf_BTN2">자동 입력</button>';
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="box_table">
                    <colgroup>
                            <col style="width: 5%" />
                            <col style="width: 5%" />
                            <col style="width: 14%" />
                            <col style="width: 6%" />
                            <col style="width: 10%" />
                            <col style="width: 9%;">
                            <col style="width: 9%;">
                            <col style="width: 9%;">
                            <?php
                            if ($check_round != 'y') {
                                echo '<col style="width: 9%;">';
                                echo '<col style="width: 9%;">';
                                echo '<col style="width: 9%;">';
                            }
                            ?>
                            <col style="width: 9%;">
                            <col style="width: 9%" />
                            <?php
                            if ($check_round == 'y') {
                                echo '<col style="width: 9%;">';
                                echo '<col style="width: 9%;">';
                            }
                            ?>
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th rowspan="2">순위</th>
                                <th rowspan="2">등번호</th>
                                <th rowspan="2">성명</th>
                                <th rowspan="2">국가</th>
                                <th rowspan="2">출생년도</th>
                                <th rowspan="2">1차시기</th>
                                <th rowspan="2">2차시기</th>
                                <th rowspan="2">3차시기</th>
                                <?php
                                if ($check_round != 'y') {
                                    echo '<th rowspan="2">4차시기</th>';
                                    echo '<th rowspan="2">5차시기</th>';
                                    echo '<th rowspan="2">6차시기</th>';
                                }
                                ?>
                                <th rowspan="2">기록</th>
                                <th>비고</th>
                                <?php
                                if ($check_round == 'y') {
                                    echo '<th rowspan="2">점수</th>';
                                    echo '<th rowspan="2">종합 점수</th>';
                                }
                                ?>
                            </tr>
                            <tr>
                                <th>신기록</th>
                            </tr>
                        </thead>
                        <tbody class="table_tbody entry_table">
                        <?php
                            $i = 1;
                            $count = 0; //신기록 위치 관련 변수
                            $trial = 1;
                            $num = 0;
                            $order = "record_order";
                            $obj = "record_" . $result_type . "_result,record_memo,record_" . $result_type . "_record,record_wind,";
                            if ($rows["record_state"] === "y") {
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
                                "athlete_bib,athlete_id AS a_id,(SELECT record_order FROM list_record WHERE record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' AND record_trial='$trial' AND record_athlete_id = a_id) AS record_order ,athlete_name,record_new FROM list_record
                                    INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                                    where $check AND record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'
                                    ORDER BY $order ASC";
                            $result2 = $db->query($sql2);
                            while ($id = mysqli_fetch_array($result2)) {
                                $num++;
                                echo '<tr';
                                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                else echo ">";
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
                                    if ($rows['record_state'] != 'y') {
                                        $time = $rows['record_start'];
                                    } else {
                                        $time = $rows['record_end'];
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
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'. ($row['record_'.$result_type.'_record'] ?? null).'" readonly/></td>';
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
                    </div>
                    <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
                    <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="note_text"
                        value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                    <div class="modify_Btn input_Btn result_Btn">
                        <?php
                            if ($rows["record_state"] != "y") {
                                if (($rows["schedule_name"] == 'Decathlon' || $rows["schedule_name"] == 'Heptathlon')) {
                                } else {
                                    echo '<button type="submit" class="BTN_Red" formaction="/action/record/three_try_after_reverse.php"><span>순서 재정렬</span></button>';
                                }
                            }
                            if ($_POST['check'] ?? null === '3') {
                                echo '<input type="hidden" name="count" value= "5">';
                            } else {
                                echo '<input type="hidden" name="count" value= "3">';
                            }
                         ?>
                        <button type="submit" class="BTN_Blue" name="addresult">확인</button>
                    </div>
                        
                    <div class="modify_Btn input_Btn result_Btn" >
                        <button type="submit" class="BTN_Blue" name="addresult" style="width:100%;">확인</button>
                    </div>
                    <?php
                    if ($rows["record_state"] != "y") {
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
                    }else {
                        if (authCheck($db, "authSchedulesUpdate")) {  ?>
                            <div class="signup_submit" style="width:100%;">
                                <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_normal_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>
                        <?php } elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                            <div class="signup_submit" style="width:100%;">
                                <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_normal_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>
                    <?php }
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js?ver=7"></script>
</body>

</html>