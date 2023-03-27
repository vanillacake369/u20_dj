<?php
require_once __DIR__ . "/../action/module/record_worldrecord.php";
require_once __DIR__ . "/../includes/auth/config.php";
$sports = $_POST['sports'];
$round = $_POST['round'];
$gender = $_POST['gender'];
$group = $_POST['group'];
//B:데이터베이스 연결
$sql = "SELECT DISTINCT * FROM list_record join list_schedule where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' and record_sports=schedule_sports and record_round=schedule_round and record_gender =schedule_gender";
$result = $db->query($sql);
$rows = mysqli_fetch_assoc($result);
$judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'";
$judgeresult = $db->query($judgesql);
$judgerow = mysqli_fetch_array($judgeresult);
if ($rows['record_state'] == 'o') {
    $result_type = 'official';
} else {
    $result_type = 'live';
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/xeicon.min.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=37">
    <title>u20 관리자 페이지</title>
    <!--Data Tables-->
    <link rel="stylesheet" type="text/css" href="../assets/DataTables/datatables.min.css" />
    <script type="text/javascript" src="../assets/js/onlynumber.js"></script>
    <script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="../assets/js/change_athletics.js"></script>
    <script type="text/javascript" src="../action/record/result_track_team_execute_excel.js"></script>
    <script>
    function openTextFile() {
        var input = document.createElement("input");
        input.type = "file";
        input.accept = "text/plain"; // 확장자가 xxx, yyy 일때, ".xxx, .yyy"
        input.onchange = function(event) {
            processFile(event.target.files[0]);
        };
        input.click();
    }

    function processFile(file) {
        var reader = new FileReader();
        reader.onload = function() {
            let ddd = reader.result.split("\r\n");
            let wind = document.querySelector('[name=\"wind\"]')
            // let check = document.getElementsByTagName('th')[1].textContent;
            let val = ddd[0].split(',')[4];
            if (val != '') {
                wind.value = val;
            } else {
                wind.value = '0'
            }
            console.log(ddd.length)
            for (i = 1; i < ddd.length; i++) {
                let k = ddd[i].split(",")
                console.log(k)
                let on;
                console.log("k1: " + k[2])
                if (!document.querySelector("#id" + k[1]) && !document.querySelector(
                        "#rane" + k[2])) {
                    console.log("없는 레인")
                    continue;
                }
                if (!document.querySelector("#id" + k[1])) {
                    on = document.querySelector("#rane" + k[2]).children
                } else {
                    on = document.querySelector("#id" + k[1]).children
                }
                console.log(on[6])
                if (k[6]) {
                    on[5].firstElementChild.value = 'p'
                    on[6].firstElementChild.value = k[6]
                } else if (k[0] == 'DNS') {
                    on[5].firstElementChild.value = 'n'
                    on[6].firstElementChild.value = 0
                    on[8].firstElementChild.value = k[0]
                } else if (k[0] == 'DNF') {
                    on[5].firstElementChild.value = 'n'
                    on[6].firstElementChild.value = 0
                    on[8].firstElementChild.value = k[0]
                } else {
                    on[5].firstElementChild.value = 'd'
                    on[6].firstElementChild.value = 0
                    on[8].firstElementChild.value = 'DQ'
                }
                // if (k[3]) {
                //     on[7].firstElementChild.value = k[3]
                // } else {
                //     on[7].firstElementChild.value = '';
                // }
            }
            rankcal1()
        };
        reader.readAsText(file, /* optional */ "utf-8");
    }

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
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    <?php echo $rows['schedule_name'] ?>
                </p>
                <form action="../action/record/track_relay_result_insert.php" method="post">
                    <input type="hidden" name="sports" value="<?php echo  $sports ?>">
                    <input type="hidden" name="gender" value="<?php echo  $gender ?>">
                    <input type="hidden" name="round" value="<?php echo  $round ?>">
                    <input type="hidden" name="group" value="<?php echo  $group ?>">
                    <div class="UserProfile_modify UserProfile_input thorw_main">
                        <div>
                            <ul class="UserDesc throwDesc">
                                <li class="row input_row throw_row">
                                    <span>경기 이름</span>
                                    <input placeholder="경기 이름" type="text" name="gamename"
                                        value="<?php echo $rows['schedule_name'] ?>" maxlength="16" required=""
                                        readonly />
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
                                    <input placeholder="심판 이름" type="text" name="refereename" value="<?php  echo ($judgerow[0] ?? null) 
                                    ?>" maxlength="30" required="" readonly />
                                </li>
                                <li class="row input_row throw_row">
                                    <span>풍속</span>
                                    <?php
                                    if ($rows['record_state'] === 'y') {

                                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind"  value="' . $rows['record_wind'] . '" maxlength="16"
                                                required="" />';
                                    } else {

                                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind"  value="" maxlength="16"
                                                required="" />';
                                    }
                                    ?>
                                </li>
                                <li class="row input_row throw_row">
                                    <span>경기 시작 시간</span>
                                    <?php
                                    echo '<input placeholder="시작 시간" type="text" name="starttime" value="' . ($rows['record_start']) . '"
                                maxlength="30" required="" />';
                                    if ($rows['record_state'] != 'y') {
                                        echo '<input type="button" onclick="input_time()" class="btn_add bold" value="현재 시간" />';
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="Thorw_result">
                        <div class="relay_result">
                            <div class="result_BTN">
                                <h1 class="tit_padding tit_left_green">결과</h1>
                                <div>
                                    <?php
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
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 25%" />
                            <col style="width: 7%" />
                            <col style="width: 15%" />
                            <col style="width: 14%" />
                            <col style="width: 10%" />
                            <col style="width: 10%" />
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th style="background: none">등수</th>
                                <th style="background: none">레인</th>
                                <th style="background: none">등번호</th>
                                <th style="background: none">이름</th>
                                <th style="background: none">국가</th>
                                <th style="background: none">경기 결과</th>
                                <th style="background: none">Reaction Time</th>
                                <th style="background: none">비고</th>
                                <th style="background: none">신기록</th>
                            </tr>
                        </thead>
                        <tbody class="input_table De_tbody entry_table">
                            <?php
                            $count = 0;
                            $num = 0;
                            $relm = 'athlete_country, athlete_bib, record_' . $result_type . '_result,record_' . $result_type . '_record,record_pass,record_memo,record_new,athlete_name,record_team_order,record_reaction_time,record_order';
                            if ($rows['record_state'] == 'y') {
                                $order = 'record_' . $result_type . '_result';
                            } else {
                                $order = 'record_order';
                            }
                            $sql = "SELECT  " . $relm . " FROM list_record 
                                INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                                and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'
                                ORDER BY " . $order . " ASC,record_team_order ASC ";
                            $result = $db->query($sql);
                            $count = 0;
                            $athrecord = array();
                            $athname = array();
                            while ($row = mysqli_fetch_array($result)) {
                                $athrecord[$count % 4] = $row['record_' . $result_type . '_record'];
                                $athname[$count] = $row['athlete_name'];

                                if ($count % 4 == 0) {
                                    echo '<tr id="rane' . $row['record_order'] . '">';
                                    echo '<td><input type="number" name="rank[]" id="rank"  value="' . $row['record_' . $result_type . '_result'] . '" min="1" max="12" required="" /></td>';
                                    echo '<td><input type="number" name="rain[]"  value="' . $row['record_order'] . '" min="1" max="12" required="" readonly /></td>';
                                    echo '<td>';
                                }
                                if ($count % 4 == 3) {
                                    echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                         value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly/></td>';
                                    for ($k = $count - 3; $k <= $count; $k++) {
                                        if ($k == $count - 3) {
                                            echo '<td>';
                                        }
                                        if ($k == $count) {
                                            echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                                 value="' . $athname[$k] . '" maxlength="30" required="" readonly/></td>';
                                        } else {
                                            echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                                 value="' . $athname[$k] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                        }
                                    }
                                    echo '<td><input placeholder="국가" type="text" name="division"  value="' . $row['athlete_country'] . '"maxlength="50" required="" readonly/></td>';
                                    echo '<td style="display:none"><input placeholder="경기 통과 여부" type="text" name="gamepass[]"  value="' . $row['record_pass'] . '" maxlength="1" required="" /></td>';
                                    echo '<td>
                                        <input placeholder="경기 결과" type="text" name="gameresult[]" id="result" 
                                        value="' . (isset($athrecord[3]) ? $athrecord[3] : null) . '" maxlength="9" required="" onkeyup="trackFinal(this)" />
                                        </td>';
                                    echo '<input type="hidden" name="compresult[]" value="' . (isset($athrecord[3]) ? $athrecord[3] : 0) . '"/>';
                                    echo '<td>
                                        <input placeholder="reactiontime" type="text" name="reactiontime[]" id="reactiontime" 
                                        value="' . ($row['record_reaction_time'] ?? null) . '" maxlength="9" required="" onkeyup="trackFinal(this)" />
                                        </td>';
                                    echo '<td><input placeholder="비고" type="text" name="bigo[]"  value="' . ($row['record_memo'] ? $row['record_memo'] : '') . '" maxlength="100" /></td>';
                                    $sport_code = $rows['schedule_sports'];
                                    if ($rows['record_state'] != 'y') {
                                        $time = $rows['record_start'];
                                    } else {
                                        $time = $rows['record_end'];
                                    }
                                    $athletics = check_my_record($row['athlete_country'], $sport_code, $time);
                                    if ((key($athletics) ?? null) === 'w') {
                                        echo '<td><input placeholder=""  type="text" name="newrecord[]"  value="WR';
                                        echo '" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></li>';
                                    } else if ((key($athletics) ?? null) === 'u') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]"  value="UWR';
                                        echo '" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></li>';
                                    } else if ((key($athletics) ?? null) === 'a') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]"  value="AR';
                                        echo '" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></li>';
                                    } else if ((key($athletics) ?? null) === 's') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]"  value="UAR';
                                        echo '" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></li>';
                                    } else if ((key($athletics) ?? null) === 'c') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]"  value="CR';
                                        echo '" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></li>';
                                    } else {
                                        echo '<td><input placeholder="선택" type="text" name="newrecord[]" value="" maxlength="100" ath="' . $row['athlete_country'] . '" sports=' . $rows['schedule_sports'] . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $athrecord[3] . '" readonly/></td>';
                                    }
                                } else {
                                    echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                         value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                                }
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
            </div>
        </div>
        <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
        <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="note_text"
            value="<?php echo ($rows['schedule_memo'] ?? null) ?>" maxlength=" 100" />
        <div class="modify_Btn input_Btn result_Btn">
            <?php
            if ($rows["record_state"] != "y") {
                echo '<div class="signup_submit" style="width:49%;">
                                    <button type="submit" class="BTN_Blue full_width" name="addresult"
                                        formaction="../action/record/track_relay_result_insert.php">
                                        <span>확인</span>
                                    </button>
                                </div>';
            } else {
                if (authCheck($db, "authSchedulesUpdate")) {  ?>
            <div class="modify_Btn input_Btn result_Btn">
                <button type="submit" class="BTN_Blue full_width" name="addresult"
                    formaction="../action/record/track_relay_result_insert.php">
                    <span>확인</span>
                </button>
            </div>
            <?php } elseif (authCheck($db, "authSchedulesDelete")) {  ?>
            <div class="modify_Btn input_Btn result_Btn">
                <button type="submit" class="BTN_Blue full_width" name="addresult"
                    formaction="../action/record/track_relay_result_insert.php">
                    <span>확인</span>
                </button>
            </div>
            <?php }
            }
            ?>
        </div>
        </form>
    </div>
    </div>
    </div>
    <script src="/assets/js/main.js?ver=6"></script>
</body>

</html>