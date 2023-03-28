<?php
require_once "head.php";
require_once "action/module/record_worldrecord.php";
require_once "includes/auth/config.php";
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
  exit("<script>
          alert('잘못된 접근입니다.');
          history.back();
      </script>");
}

$sports=$_GET['sports'];
$gender=$_GET['gender'];
$round=$_GET['round'];
$sql = "SELECT DISTINCT * FROM list_record join list_schedule where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and schedule_sports=record_sports and schedule_round=record_round and schedule_gender=record_gender and if(record_state='y',record_live_result>0,'1')";
$result = $db->query($sql);
$rows = mysqli_fetch_assoc($result);
$schedule_sports = $rows['schedule_sports'];
$schedule_round = $rows['schedule_round'];
$schedule_group= $rows['record_group'];
$schedule_result = $rows['record_status'];
$group=$rows['record_group'];
$check_round='n';
if($schedule_sports == 'decathlon' || $schedule_sports == 'heptathlon'){
  $check_round='y';
}
if ($rows['record_status'] == 'o') {
  $result_type = 'official';
} else {
  $result_type = 'live';
}
    $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
    $judgeresult = $db->query($judgesql);
    $judgerow = mysqli_fetch_array($judgeresult);

?>
<script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="../assets/js/onlynumber.js"></script>

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
        <div class="schedule schedule_flex">
            <form action="" method="post" class="form schedule_filed filed_list_item">
                <div>
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">1조</p>
                        <?php echo '<span class="defaultBtn';
            echo $schedule_result == 'o' ? ' BTN_green">Official Result</span>' : ($schedule_result == 'l' ? ' BTN_yellow">Live Result</span>' : ' BTN_green">Start List</span>');
            ?>
                    </div>
                    <input name="round" value="<?=$round?>" hidden>
                    <input name="sports" value="<?=$sports?>" hidden>
                    <input name="gender" value="<?=$gender?>" hidden>
                    <input name="group" value="<?=$schedule_group?>" hidden>
                    <!--<input name="name" value="--><?php //=$schedule_name?>
                    <!--" hidden>-->
                    <input name="result" value="<?=$schedule_result?>" hidden>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 3%" />
                            <col style="width: 3%" />
                            <col style="width: 5%" />
                            <col style="width: 15%" />
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
                            <col style="width: 9%" />
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th style="background: none" rowspan="2">등수</th>
                                <th style="background: none" rowspan="2">순서</th>
                                <th style="background: none" rowspan="2">BIB</th>
                                <th style="background: none" rowspan="2">이름</th>
                                <?php
                // 높이 찾는 쿼리
                $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record FROM list_record where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_".$result_type."_record>0 limit 12");
                $cnt1 = 0;
                while ($highrow = mysqli_fetch_array($highresult)) {
                  echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                    class="input_trial" id="trial" value="' .
                    $highrow["record_" . $result_type . "_record"] .
                    '" maxlength="4" 
                                                    onkeyup="heightFormat(this) readonly"></th>';
                  $cnt1++;
                }
                for ($j = 0; $j < 12 - $cnt1; $j++) {
                  echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                    class="input_trial" id="trial" value="" maxlength="4" 
                                                    onkeyup="heightFormat(this)" readonly></th>';
                }
                ?>
                                <th style="background: none" rowspan="2">기록</th>
                                <th style="background: none">비고</th>
                            </tr>
                            <tr id="col2">
                                <?php if ($cnt1 == 12) {
                  $cnt2 = 0;
                  $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record 
                  FROM list_record 
                  where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_".$result_type."_record>0 
                  limit 12,12");
                  while ($highrow = mysqli_fetch_array($highresult)) {
                    echo '<th><input placeholder="높이" type="text" name="trial[]"
                                                    class="input_trial" id="trial" value="' .
                      $highrow["record_" . $result_type . "_record"] .
                      '" maxlength="4" 
                                                    onkeyup="heightFormat(this)" readonly></th>';
                    $cnt2++;
                  }
                  for ($j = 0; $j < 12 - $cnt2; $j++) {
                    echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                    class="input_trial" id="trial" value="" maxlength="4" 
                                                    onkeyup="heightFormat(this)" readonly></th>';
                  }
                } else {
                  for ($j = 0; $j < 12; $j++) {
                    echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                                        class="input_trial" id="trial" value="" maxlength="4" 
                                                        onkeyup="heightFormat(this)" readonly></th>';
                  }
                } ?>
                                <th style="background: none">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table">
                            <?php
              if ($rows["record_state"] === "y") {
                $order = "record_" . $result_type . "_result";
                $obj = "record_" . $result_type . "_result,record_memo,athlete_id,record_" . $result_type . "_record,";
                $jo = "WHERE record_" . $result_type . "_result>0";
              } else {
                $order = "record_order";
                $obj = "athlete_id,";
                $jo = "";
              }
              $result = $db->query("SELECT DISTINCT ".$obj."record_order,record_new,athlete_name,athlete_bib FROM list_record 
                            INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                            and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'".$jo."
                            ORDER BY ".$order." ASC , record_".$result_type."_record ASC"
              );
              $cnt = 1;
              $num = 0;
              while ($row = mysqli_fetch_array($result)) {
                
                $num++;
                echo '<tr id=col1 "';
                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                else echo ">";
                echo '<td rowspan="2"><input type="number" name="rank[]"  id="rank" value="' .
                  ($row["record_" . $result_type . "_result"] ?? null) .
                  '"min="1" required="" readonly/></td>';
                echo '<td rowspan="2"><input type="number" name="rain[]"  value="' .
                  $row["record_order"] .
                  '" min="1" required="" readonly /></td>';
                echo '<td rowspan="2" ><input placeholder="bib" type="text" name="playerbib[]" id="bib" 
                                 value="';
                if (isset($row["athlete_bib"])) echo $row["athlete_bib"]; else echo "";
                echo '" maxlength="30" required="" readonly/></td>';
                echo '<td rowspan="2" ><input placeholder="선수 이름" type="text" name="playername[]" id="name" 
                                 value="';
                if (isset($row["athlete_name"])) echo $row["athlete_name"]; else echo "";
                echo '" maxlength="30" required="" readonly/></td>';
                $cnt3 = 1;
                $record = $db->query(
                    "SELECT record_trial FROM list_record
                      INNER JOIN list_athlete ON record_athlete_id=" .
                      $row["athlete_id"] .
                      " AND athlete_id= record_athlete_id
                      and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'AND record_".$result_type."_record>0
                      ORDER BY cast(record_".$result_type."_record as float) ASC limit 12"
                ); //선수별 기록 찾는 쿼리
                while ($recordrow = mysqli_fetch_array($record)) {
                  echo "<td>";
                  echo '<input placeholder="" type="text" name="gameresult' .
                    $cnt3 .
                    '[]"  value="' .
                    $recordrow["record_trial"] .
                    '" maxlength="3" onkeyup="highFormat(this)" readonly/>';
                  echo "</td>";
                  $cnt3++;
                }
                for ($a = $cnt3; $a <= 12; $a++) {
                  //기록을 제외한 빈칸으로 생성
                  echo "<td>";
                  echo '<input placeholder="" type="text" name="gameresult' .
                    $a . '[]"  value="" maxlength="3" onkeyup="highFormat(this)" readonly/>';
                  echo "</td>";
                }
                //
                echo '<td rowspan="2">';
                echo '<input placeholder="결과" id="result" type="text" name="gameresult[]" value="' .
                  ($row["record_" . $result_type . "_record"] ?? null) . '" maxlength="5" required="" readonly/>';
                echo "</td>";
                echo '<input type="hidden" name="compresult[]" value="' . ($row["record_" . $result_type . "_record"] ?? null) . '"/>';
                echo '<td><input placeholder="비고" type="text" name="bigo[]"  value="' .($row["record_memo"] ?? null) .
                  '" maxlength="100" readonly /></td>';
                //
                echo '<tr id=col2';
                  if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                  else echo ">";
                if ($cnt3 == 13) {
                  //13번째 기록부터
                  $record = $db->query(
                      "SELECT record_trial,record_athlete_id FROM list_record
                      INNER JOIN list_athlete ON record_athlete_id=" .
                      $row["athlete_id"] .
                      " AND athlete_id= record_athlete_id
                      and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' AND record_" . $result_type . "_record>0
                      ORDER BY cast(record_" . $result_type . "_record as float) ASC limit 12,12"
                    );//선수별 기록 찾는 쿼리
                  while ($recordrow = mysqli_fetch_array($record)) {
                    echo "<td>";
                    echo '<input placeholder="" type="text" name="gameresult' .
                    $cnt3 . '[]"  value="' . $recordrow["record_trial"] . '" maxlength="3" onkeyup="highFormat(this)" readonly/>';
                    echo "</td>";
                    $cnt3++;
                  }
                } else {
                  $cnt3 = 13;
                }
                for ($a = $cnt3; $a <= 24; $a++) {
                  //기록을 제외한 빈칸으로 생성
                  echo "<td>";
                  echo '<input placeholder="" type="text" name="gameresult' . $a . '[]"  value="" maxlength="3" onkeyup="highFormat(this)" readonly/>';
                  echo "</td>";
                }
                if ($rows['schedule_sports'] === 'decathlon' || $rows['schedule_sports'] === 'heptathlon') {
                  $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
                } else {
                  $sport_code = $rows['schedule_sports'];
                }
                if (($row['record_new'] && null) == 'y') {
                  if ($rows['record_state'] != 'y') {
                    $time = $rows['schedule_start'];
                  } else {
                    $time = $rows['record_end'];
                  }
                  $athletics = check_my_record($row['athlete_name'], $sport_code, $time);
                  if ((key($athletics) ?? null) === 'w') {
                      echo '<td><input placeholder=""  type="text" name="newrecord[]"  value="세계신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'u') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]"  value="세계U20신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'a') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]"  value="아시아신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 's') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]"  value="아시아U20신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else if ((key($athletics) ?? null) === 'c') {
                      echo '<td><input placeholder="" type="text" name="newrecord[]"  value="대회신기록';
                      echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    } else {
                      echo '<td><input placeholder="" type="text" name="newrecord[]"  value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                    }
                  } else {
                    echo '<td><input placeholder="" type="text" name="newrecord[]"  value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="' . ($row["record_" . $result_type . "_record"] ?? null) . '" readonly/></td>';
                  }
                $cnt++;
              }
              ?>
                        </tbody>
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
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_vertical_result_pdf.php">PDF(한)
                                출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_vertical_result_eng_pdf.php">PDF(영)
                                출력</button>
                            <button type="submit" formaction="/action/record/result_execute_vertical_excel.php"
                                class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/field_vertical_result_word.php">워드
                                출력</button>

                        </div>

                        <div>
                            <?php
              // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                       if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                         echo '<button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="';
                        if ($schedule_sports == "polevault" || $schedule_sports == "highjump" || $check_round=='y') {
                            echo "/record/field_vertical_result_view.php";
                        } else if ($schedule_sports == "longjump" || $schedule_sports == "triplejump") {
                            echo "/record/field_horizontal_result_view.php";
                        } else {
                            echo "/record/field_normal_result_view.php";
                        }
                        echo '">기록 입력</button>';
                        if (authCheck($db, "authRecordsUpdate")) {
                            echo '<input type="submit" formaction="';
                                echo './record_change_type.php"';
                                echo 'class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                        }
                      }
              ?>
                        </div>
                    </div>
                </div>
                <button type="button" class="changePwBtn defaultBtn">확인</button>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/restrict.js"></script>
</body>


</html>