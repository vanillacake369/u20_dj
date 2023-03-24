<?php
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
    $sql = "SELECT DISTINCT * FROM list_record join list_schedule where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and schedule_sports=record_sports and schedule_round=record_round and schedule_gender=record_gender";
    $result = $db->query($sql);
    $rows = mysqli_fetch_assoc($result);
    if ($rows['schedule_result'] == 'o') {
      $result_type = 'official';
    } else {
      $result_type = 'live';
    }
    $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
    $judgeresult = $db->query($judgesql);
    $judgerow = mysqli_fetch_array($judgeresult);
    ?>
    <!DOCTYPE html>
    <html lang="ko">

    <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="stylesheet" href="../assets/css/style.css" />
      <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css" />
      <script src="../assets/fontawesome/js/all.min.js"></script>
      <!--Data Tables-->
      <script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
      <script type="text/javascript" src="../assets/js/onlynumber.js"></script>
      <script type="text/javascript" src="../assets/js/change_athletics.js"></script>
      <script type="text/javascript" src="../action/record/result_field_vertical_execute_excel.js"></script>
      <script type="text/javascript">
        window.onload = function() {
            for (k = 0; k < document.querySelectorAll('#name').length; k++) {
                let a = document.querySelectorAll('#name')[k];
                const rain = a.parentElement.parentElement.className.split("_")[1];
                //성공시 처리 부분
                let high = document.querySelectorAll('[name="trial[]"]'); // 높이 배열 가져오기
                let index = document.querySelectorAll("#result");
                let calcal = 0.0;
                for (i = 1; i <= 24; i++) {
                    let k = '[name="gameresult' + i + '[]"]';
                    let temp = document.querySelectorAll(k)[rain - 1].value;
                    if (temp.search("O") != -1) {
                        if (
                            calcal < parseFloat(high[i - 1].value) ||
                            isNaN(parseFloat(index[rain - 1].value))
                        ) {
                            calcal = parseFloat(high[i - 1].value);
                        }
                    }
                }
                index[rain - 1].value = calcal; // 기존 값과 비교 후 성공 시 기록이 크면 바꾸기
            }
            rankcal2()
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
      <title>U20</title>
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
                                  ?>
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
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 5%" />
                              <col style="width: 11%" />
                      </colgroup>
                        <thead class="result_table entry_table">
                            
                        <tr id="col1">
                          <th rowspan="2">등수</th>
                          <th rowspan="2">순서</th>
                          <th rowspan="2">이름</th>
                          <?php
                            // 높이 찾는 쿼리
                            $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record FROM list_record where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_".$result_type."_record>0 limit 12");
                            $cnt1 = 0;
                            while ($highrow = mysqli_fetch_array($highresult)) {
                              echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                    class="input_trial" id="trial" value="' .
                                $highrow["record_" . $result_type . "_record"] .
                                '" maxlength="4" 
                                              onkeyup="heightFormat(this)"></th>';
                              $cnt1++;
                            }
                            for ($j = 0; $j < 12 - $cnt1; $j++) {
                              echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                              class="input_trial" id="trial" value="" maxlength="4" 
                                              onkeyup="heightFormat(this)"></th>';
                            }
                            ?>
                              <th rowspan="2">기록</th>
                              <th>비고</th>

                            </tr>
                            <tr id="col2">
                            <?php if ($cnt1 == 12) {
                              $cnt2 = 0;
                              $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record FROM list_record where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_".$result_type."_record>0 limit 12,12");
                              while ($highrow = mysqli_fetch_array($highresult)) {
                                echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                    class="input_trial" id="trial" value="' .
                                    $highrow["record_" . $result_type . "_record"] .
                                    '" maxlength="4" 
                                                onkeyup="heightFormat(this)"></th>';
                                  $cnt2++;
                                }
                                for ($j = 0; $j < 12 - $cnt2; $j++) {
                                  echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                class="input_trial" id="trial" value="" maxlength="4" 
                                                onkeyup="heightFormat(this)"></th>';
                                }
                              } else {
                                for ($j = 0; $j < 12; $j++) {
                                  echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                    class="input_trial" id="trial" value="" maxlength="4" 
                                                    onkeyup="heightFormat(this)"></th>';
                                          }
                                        } ?>
                                <th style="background: none">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                        </tr>
                        </thead>
                        <tbody class="table_tbody entry_table">
                        <?php
                        if ($rows["record_state"] === "y") {
                          $order = "record_".$result_type."_result";
                          $obj = "record_".$result_type."_result,record_memo,athlete_id,record_".$result_type."_record,";
                          $jo = "WHERE record_".$result_type."_result>0";
                        } else {
                          $order = "record_order";
                          $obj = "athlete_id,";
                          $jo = "";
                        }
                        $result = $db->query(
                          "SELECT DISTINCT " .
                            $obj .
                            "record_order,record_new,athlete_name FROM list_record 
                            INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                            and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'
                            ORDER BY ".$order." ASC , record_".$result_type."_record ASC"
                        );
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($result)) {
                          echo '<tr id=col1 class="col1_' . $cnt . '">';
                          echo '<td rowspan="2"><input type="number" name="rank[]" class="input_text" id="rank" value="' .
                            ($row["record_".$result_type."_result"] ?? null) .
                            '"min="1" required="" /></td>';
                          echo '<td rowspan="2"><input type="number" name="rain[]" class="input_text" value="' .
                            $row["record_order"] .
                            '" min="1" required="" readonly /></td>';
                          echo '<td rowspan="2" ><input placeholder="선수 이름" type="text" name="playername[]" id="name" class="input_text"
                                value="' .
                    $row["athlete_name"] .
                    '" maxlength="30" required="" readonly/></td>';
                  $cnt3 = 1;
                  $record = $db->query(
                    "SELECT record_trial FROM list_record
                      INNER JOIN list_athlete ON record_athlete_id=" .
                      $row["athlete_id"] .
                      " AND athlete_id= record_athlete_id
                      and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'AND record_".$result_type."_record>0
                      ORDER BY record_".$result_type."_record ASC limit 12"
                            ); //선수별 기록 찾는 쿼리
                            while ($recordrow = mysqli_fetch_array($record)) {
                              echo "<td>";
                              echo '<input placeholder="" type="text" name="gameresult' .
                                $cnt3 .
                                '[]" class="input_text" value="' .
                                $recordrow["record_trial"] .
                                '"
                              maxlength="3" onkeyup="highFormat(this)"
                              style="float: left; width: auto; padding-right: 5px" />';
                    echo "</td>";
                    $cnt3++;
                  }
                  for ($a = $cnt3; $a <= 12; $a++) {
                    //기록을 제외한 빈칸으로 생성
                    echo "<td>";
                    echo '<input placeholder="" type="text" name="gameresult' .
                      $a .
                      '[]" class="input_text" value=""
                                      maxlength="3" onkeyup="highFormat(this)"
                                      style="float: left; width: auto; padding-right: 5px" />';
                    echo "</td>";
                  }

                  //
                  echo '<td rowspan="2">';
                  echo '<input placeholder="결과" id="result" type="text" name="gameresult[]" class="input_text"
                                    value="' .
                    ($row["record_" . $result_type . "_record"] ?? null) .
                    '" maxlength="5" required=""
                                    style="float: left; width: auto; padding-right: 5px" />';
                  echo "</td>";
                  echo '<input type="hidden" name="compresult[]" value="' . ($row["record_" . $result_type . "_record"] ?? null) . '"/>';
                  echo '<td><input placeholder="비고" type="text" name="bigo[]" class="input_text" value="' .
                    ($row["record_memo"] ?? null) .
                    '" maxlength="100" /></td>';
                  //
                  echo '<tr id=col2 class="col2_' . $cnt . '">';
                  if ($cnt3 == 12) {
                    //13번째 기록부터
                    $record = $db->query(
                      "SELECT record_trial,record_athlete_id FROM list_record
                      INNER JOIN list_athlete ON record_athlete_id=" .
                      $row["athlete_id"] .
                      " AND athlete_id= record_athlete_id
                      and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' AND record_" . $result_type . "_record>0
                      ORDER BY record_" . $result_type . "_record ASC limit 12,12"
                    ); //선수별 기록 찾는 쿼리
                    while ($recordrow = mysqli_fetch_array($record)) {
                      echo "<td>";
                      echo '<input placeholder="" type="text" name="gameresult' .
                        $cnt3 .
                        '[]" class="input_text" value="' .
                        $recordrow["record_trial"] .
                        '"
                              maxlength="3" onkeyup="highFormat(this)"
                              style="float: left; width: auto; padding-right: 5px" />';
                      echo "</td>";
                      $cnt3++;
                    }
                  } else {
                    $cnt3 = 13;
                  }
                  for ($a = $cnt3; $a <= 24; $a++) {
                    //기록을 제외한 빈칸으로 생성
                    echo "<td>";
                    echo '<input placeholder="" type="text" name="gameresult' .
                      $a .
                      '[]" class="input_text" value=""
                                        maxlength="3" onkeyup="highFormat(this)"
                                        style="float: left; width: auto; padding-right: 5px" />';
                    echo "</td>";
                  }
                  if ($rows['record_sports'] === 'decathlon' || $rows['record_sports'] === 'heptathlon') {
                    $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
                  } else {
                    $sport_code = $rows['schedule_sports'];
                  }
                  if (($row['record_new'] && null) == 'y') {
                    if ($rows['record_state'] != 'y') {
                      $time = $rows['record_start'];
                    } else {
                      $time = $rows['record_end'];
                    }
                    $athletics = check_my_record($row['athlete_name'], $sport_code, $time);
                    if ((key($athletics) ?? null) === 'w') {
                      echo '<td><input placeholder=""  type="text" name="newrecord[]" class="input_text" value="세계신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'u') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="세계U20신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'a') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 's') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아U20신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'c') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="대회신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else {
                      echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    }
                  } else {
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . ($row["record_" . $result_type . "_record"] ?? null) . '" readonly/></td>';
                  }
                  $cnt++;
                }
                ?>
                </tr>
                </tr>
                        </tbody>
                    </table>
                    </div>
                    <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
                    <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="note_text"
                        value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                      <div class="modify_Btn input_Btn result_Btn">
                    <?php
                    if ($rows["schedule_status"] != "y") {
                      echo '<div class="signup_submit" style="width:49%;>
                                    <button type="submit" class="BTN_Red full_width" name="addtempresult"
                                        formaction="../action/record/field_vertical_result_insert.php">
                                        <span>임시저장</span>
                                    </button>
                                </div>';
                      echo '<div class="signup_submit" style="width:49%;">
                                  <button type="submit" class="BTN_Blue full_width" name="addresult"
                                      formaction="../action/record/field_vertical_result_insert.php">
                                      <span>확인</span>
                                  </button>
                              </div>';
                          }else{
                            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                              <div class="modify_Btn input_Btn result_Btn">
                                <button type="submit" class="BTN_Blue full_width" name="addresult"
                                    formaction="../action/record/field_vertical_result_insert.php">
                                    <span>확인</span>
                                </button>
                            </div>
                          <?php }
                          elseif (authCheck($db, "authSchedulesDelete")) {  ?>
                              <div class="modify_Btn input_Btn result_Btn">
                              <button type="submit" class="BTN_Blue full_width" name="addresult"
                                  formaction="../action/record/field_vertical_result_insert.php">
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
    <script src="../assets/js/main.js?ver=7"></script>
</body>
    
</html>