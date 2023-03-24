<?php
    require_once __DIR__ . "/../backheader.php";
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../includes/auth/config.php";//B:데이터베이스 연결 
    // 수정 권한 시에만 기록 전환 접근 가능
    if (!authCheck($db, "authRecordsUpdate")) {
        exit("<script>
        alert('수정 권한이 없습니다.');
        history.back();
    </script>");
    }
    $sports = $_POST['sports'];
    $round = $_POST['round'];
    $gender = $_POST['gender'];
    $group = $_POST['group'];
    $sql = "SELECT DISTINCT * FROM list_record  join list_schedule where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' AND schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round";
    $result = $db->query($sql);
    $rows = mysqli_fetch_assoc($result);
    $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'";
    $judgeresult = $db->query($judgesql);
    $judgerow = mysqli_fetch_array($judgeresult);
    $longname = ['1500m', '3000m', '3000mSC', '5000m', '10000m', 'racewalk'];
    if ($rows['record_status'] == 'o') {
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
    <link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
    <script type="text/javascript" src="/assets/js/onlynumber.js"></script>
    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="/assets/js/change_athletics.js"></script>
    <script type="text/javascript" src="/action/record/result_track_single_execute_excel.js"></script>
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
                let on;
                console.log("k1: " + k[2])
                if (!document.querySelector("#id" + k[1]) && !document.querySelector(
                        "#rane" + k[2])) {
                    console.log("없는 레인")
                    continue;
                }
                if (!document.querySelector("#id" + k[1])) {
                    if (!k[1]) continue;
                    on = document.querySelector("#rane" + k[2]).children
                } else {
                    if (!k[1]) continue;
                    on = document.querySelector("#id" + k[1]).children
                }
                if (k[6]) {
                    on['gamepass[]'].value = 'p'
                    on[5].firstElementChild.value = k[6]
                } else if (k[0] == 'DNS') {
                    on['gamepass[]'].value = 'n'
                    on[5].firstElementChild.value = 0
                    on[7].firstElementChild.value = k[0]
                } else if (k[0] == 'DNF') {
                    on['gamepass[]'].value = 'n'
                    on[5].firstElementChild.value = 0
                    on[7].firstElementChild.value = k[0]
                } else {
                    on['gamepass[]'].value = 'd'
                    on[5].firstElementChild.value = 0
                    on[7].firstElementChild.value = 'DQ'
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
    </script>
</head>

<body>
    <!-- contents 본문 내용 -->
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
                    <input type="hidden" name="sports" value="<?= $sports ?>">
                    <input type="hidden" name="gender" value="<?= $gender ?>">
                    <input type="hidden" name="round" value="<?= $round ?>">
                    <input type="hidden" name="group" value="<?= $group ?>">
                    <div class="UserProfile_modify UserProfile_input thorw_main">
                        <div>
                            <ul class="UserDesc throwDesc">
                                <li class="row input_row throw_row">
                                    <span>경기 이름</span>
                                    <input placeholder="경기 이름" type="text" name="gamename"
                                        value="<?=$rows['record_sports']?>" maxlength="16" required="" readonly />
                                </li>
                                <li class="row input_row throw_row">
                                    <span>라운드</span>
                                    <?php
                                    echo '<input placeholder="라운드" type="text" name="round" value="' . $rows['record_round'] . '"
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
                                    <span>풍속</span>
                                    <?php
                                    if($rows['record_state']==='y'){
                                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" value="'.$rows['record_wind'].'" maxlength="16"
                                            required="" />';
                                    }else{
                                        
                                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" value="" maxlength="16"
                                            required="" />';
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
                            <?php
                                if (in_array($rows['schedule_sports'], $longname)) {
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 25%" />
                                    <col style="width: 7%" />
                                    <col style="width: 15%" />
                                    <col style="width: 10%" />
                                    <col style="width: 16%" />
                                    <col style="width: 10%" />';
                                } else {
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 10%" />';
                                    echo '<col style="width: 21%" />';
                                    echo '<col style="width: 7%" />';
                                    echo '<col style="width: 15%" />';
                                    echo '<col style="width: 10%" />';
                                    echo '<col style="width: 10%" />';
                                    echo '<col style="width: 10%" />';
                                }
                                ?>
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th style="background: none">등수</th>
                                <?php
                                if (in_array($rows['schedule_sports'], $longname)) {
                                } else {
                                    echo '<th style="background: none">레인</th>';
                                }
                                ?>
                                <th style="background: none">등 번호</th>
                                <th style="background: none">이름</th>
                                <th style="background: none">국가</th>
                                <th style="background: none">경기 결과</th>
                                <th style="background: none">reaction time</th>
                                <th style="background: none">비고</th>
                                <th style="background: none">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table">
                            <?php
                            $num = 0;
                             $relm = 'record_' . $result_type . '_result,record_' . $result_type . '_record,record_pass,record_memo,record_new,record_reaction_time,athlete_name,athlete_bib, record_order,athlete_country';
                             if ($rows['record_state'] == 'y') {
                                 $order = 'record_' . $result_type . '_result';
                             } else if (in_array($rows['schedule_sports'], $longname)) {
                                 $order = 'athlete_bib';
                             } else {
                                 $order = 'record_order';
                             }
                             $sql = "SELECT " . $relm . " FROM list_record 
                             INNER JOIN list_athlete ON list_athlete.athlete_id = list_record.record_athlete_id 
                             and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'
                             ORDER BY " . $order . " ASC ";
                             $count = 0;
                            $result = $db->query($sql);
                            echo $sql;
                            while ($row = mysqli_fetch_array($result)) {
                                if (in_array($rows['schedule_sports'], $longname)) {
                                    echo '<tr id="id' . $row['athlete_bib'] .'"';
                                } else {
                                    echo '<tr id="rane' . $row['record_order'].'"';
                                }
                                if ($num % 2 == 1) echo ' class="Ranklist_Background">'; else echo ">";
                                echo '<td><input type="number" name="rank[]" id="rank" value="' . $row['record_' . $result_type . '_result'] . '" min="1" required="" /></td>';
                                if (in_array($rows['schedule_sports'], $longname)) {
                                    echo '<td hidden><input type="number" name="rain[]" value="' . $row['record_order'] . '" min="1" required="" readonly /></td>';
                                } else {
                                    echo '<td><input type="number" name="rain[]" value="' . $row['record_order'] . '" min="1" required="" readonly /></td>';
                                }
                                echo '<td><input placeholder="등번호" type="text" name="playerbib[]" 
                                value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly /></td>';
                                echo '<td><input placeholder="선수 이름" type="text" name="playername[]" 
                                value="' . $row['athlete_name'] . '" maxlength="30" required="" readonly /></td>';
                                echo '<td><input placeholder="국가" type="text" name="country" 
                                value="' . $row['athlete_country'] . '" maxlength="30" required="" readonly /></td>';
                                echo '<td><input placeholder="경기 결과를 입력해주세요" type="text" name="gameresult[]" id="result" value="' . $row['record_' . $result_type . '_record'] . '" maxlength="8"
                                required="" onkeyup="trackFinal(this)" style="float: left; width: auto; padding-right: 5px" />';
                                echo '<td><input placeholder="Reaction Time" type="text" name="reactiontime[]" id="reactiontime" value="' . $row['record_reaction_time'] . '" maxlength="8"
                                required="" onkeyup="trackFinal(this)" style="float: left; width: auto; padding-right: 5px" />';
                                echo '<input type="hidden" name="compresult[]" value="' . ($row['record_' . $result_type . '_record'] ?? null) . '"/></td>';
                                echo '<td><input placeholder="비고를 입력해주세요" type="text" name="bigo[]" value="' . ($row['record_memo'] ? $row['record_memo'] : null) . '" maxlength="100" /></td>';
                                if ($rows['schedule_name'] === 'Decathlon' || $rows['schedule_name'] === 'Heptathlon') {
                                    $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
                                } else {
                                    $sport_code = $rows['schedule_sports'];
                                }
                                if ($row['record_new'] == 'y') {
                                    if ($rows['record_state'] != 'y') {
                                        $time = $rows['record_start'];
                                    } else {
                                        $time = $rows['record_end'];
                                    }
                                    $athletics = check_my_record($row['athlete_name'], $sport_code, $time);
                                    if ((key($athletics) ?? null) === 'w') {
                                        echo '<td><input placeholder=""  type="text" name="newrecord[]" value="WR';
                                        echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'u') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" value="UWR';
                                        echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'a') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" value="AR';
                                        echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 's') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" value="UAR';
                                        echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    } else if ((key($athletics) ?? null) === 'c') {
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" value="CR';
                                        echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    } else {
                                        echo '<td><input placeholder="선택" type="text" name="newrecord[]" value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                    }
                                } else {
                                    echo '<td><input placeholder="선택" type="text" name="newrecord[]"  value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' record_round="' . $round . '" and record_gender ="' . $gender . '" and record_group="' . $group . '" record="' . $row['record_' . $result_type . '_record'] . '" readonly/></td>';
                                }
                                echo '<input placeholder="경기 통과 여부" type="hidden" name="gamepass[]" value="' . $row['record_pass'] . '" maxlength="50" required="" />';
                                $count++;
                            }
                            ?>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
            <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="note_text"
                value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />

            <div class="modify_Btn input_Btn result_Btn">
                <?php
                    if ($rows["record_state"] != "y") {
                      echo '<div class="signup_submit" style="width:100%;">
                                  <button type="submit" class="defaultBtn BTN_Blue full_width" name="addresult"
                                      formaction="../action/record/track_normal_result_insert.php">
                                      <span>확인</span>
                                  </button>
                              </div>';
                          }else{
                            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                <div class="modify_Btn input_Btn result_Btn">
                    <button type="submit" class="BTN_Blue full_width" name="addresult"
                        formaction="../action/record/track_normal_result_insert.php">
                        <span>확인</span>
                    </button>
                </div>
                <?php }
                          elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                <div class="modify_Btn input_Btn result_Btn">
                    <button type="submit" class="BTN_Blue full_width" name="addresult"
                        formaction="../action/record/track_normal_result_insert.php">
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
    </form>
    </div>
    </div>
    </div>
    <script src="assets/js/main.js?ver=7"></script>
</body>

</html>