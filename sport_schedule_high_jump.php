<?
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

  $s_id = $_GET['id'];
  $id = $_GET['id'];
  $idsql = "SELECT s1.schedule_id, s1.schedule_round, s1.schedule_sports FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON (s2.schedule_id= '" . $s_id . "' and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name 
    AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round=s2.schedule_round ) WHERE s1.schedule_division='s' ORDER BY s1.schedule_group ASC";
  $idresult = $db->query($idsql);
  $idrow = mysqli_fetch_array($idresult);
  $schedule_id = $idrow['schedule_id'];
 
  $sql =
    "SELECT DISTINCT * FROM list_record INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$schedule_id'";
  $result = $db->query($sql);
  $rows = mysqli_fetch_assoc($result);
  $schedule_sports = $rows['schedule_sports'];
  $schedule_round = $rows['schedule_round'];
  $schedule_result = $rows['schedule_result'];
  if ($rows['schedule_result'] == 'o') {
    $result_type = 'official';
  } else {
    $result_type = 'live';
  }
  $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$schedule_id'";
  $judgeresult = $db->query($judgesql);
  $judgerow = mysqli_fetch_array($judgeresult);

?>
  <!--Data Tables-->
  <link rel="stylesheet" type="text/css" href="../assets/DataTables/datatables.min.css" />
  <script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
  <script type="text/javascript" src="../assets/DataTables/datatables.min.js"></script>
  <script type="text/javascript" src="../assets/js/useDataTables.js"></script>
  <script type="text/javascript" src="../assets/js/onlynumber.js"></script>

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
                              $highresult = $db->query("SELECT DISTINCT record_" . $result_type . "_record FROM list_record INNER JOIN list_schedule 
                                                ON list_schedule.schedule_id= list_record.record_schedule_id AND list_schedule.schedule_id = '$schedule_id' and record_" . $result_type . "_record>0 limit 12");
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
                                $highresult = $db->query("SELECT DISTINCT record_" . $result_type . "_record FROM list_record INNER JOIN list_schedule 
                                                ON list_schedule.schedule_id= list_record.record_schedule_id AND list_schedule.schedule_id = '$schedule_id' and record_" . $result_type . "_record>0 limit 12,12");
                                while ($highrow = mysqli_fetch_array($highresult)) {
                                  echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
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
                        <tbody class="table_tbody entry_table">
                        <?php
            if ($rows["schedule_status"] === "y") {
              $order = "record_" . $result_type . "_result";
              $obj = "record_" . $result_type . "_result,record_memo,athlete_id,record_" . $result_type . "_record,";
              $jo = "WHERE record_" . $result_type . "_result>0";
            } else {
              $order = "record_order";
              $obj = "athlete_id,";
              $jo = "";
            }
            $result = $db->query(
              "SELECT DISTINCT " .
                $obj .
                "record_order,record_new,athlete_name,athlete_bib FROM list_record 
                            INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                            INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                            AND schedule_id = '$schedule_id'" . $jo . "
                            ORDER BY " . $order . " ASC , record_" . $result_type . "_record ASC"
            );
            $cnt = 1;
            $num = 0;
            while ($row = mysqli_fetch_array($result)) {
              $num++;
              echo '<tr id=col1 class="col1_' . $cnt . '"';
              if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
              echo '<td rowspan="2"><input type="number" name="rank[]" class="input_text" id="rank" value="' .
                ($row["record_" . $result_type . "_result"] ?? null) .
                '"min="1" required="" readonly/></td>';
              echo '<td rowspan="2"><input type="number" name="rain[]" class="input_text" value="' .
                $row["record_order"] .
                '" min="1" required="" readonly /></td>';
              echo '<td rowspan="2" ><input placeholder="bib" type="text" name="playerbib[]" id="bib" class="input_text"
                                 value="' .
                $row["athlete_bib"] .
                '" maxlength="30" required="" readonly/></td>';
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
                                                INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                                AND schedule_id = '$schedule_id' AND record_" . $result_type . "_record>0
                                                ORDER BY record_" . $result_type . "_record ASC limit 12"
              ); //선수별 기록 찾는 쿼리
              while ($recordrow = mysqli_fetch_array($record)) {
                echo "<td>";
                echo '<input placeholder="" type="text" name="gameresult' .
                  $cnt3 .
                  '[]" class="input_text" value="' .
                  $recordrow["record_trial"] .
                  '"
                              maxlength="3" onkeyup="highFormat(this)"
                              style="float: left; width: auto; padding-right: 5px" readonly/>';
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
                                      style="float: left; width: auto; padding-right: 5px" readonly/>';
                echo "</td>";
              }

              //
              echo '<td rowspan="2">';
              echo '<input placeholder="결과" id="result" type="text" name="gameresult[]" class="input_text"
                                    value="' .
                ($row["record_" . $result_type . "_record"] ?? null) .
                '" maxlength="5" required=""
                                    style="float: left; width: auto; padding-right: 5px" readonly/>';
              echo "</td>";
              echo '<input type="hidden" name="compresult[]" value="' . ($row["record_" . $result_type . "_record"] ?? null) . '"/>';
              echo '<td><input placeholder="비고" type="text" name="bigo[]" class="input_text" value="' .
                ($row["record_memo"] ?? null) .
                '" maxlength="100" readonly /></td>';
              //
              echo '<tr id=col2 class="col2_' . $cnt . '">';
              if ($cnt3 == 12) {
                //13번째 기록부터
                $record = $db->query(
                  "SELECT record_trial,record_athlete_id FROM list_record
                                                INNER JOIN list_athlete ON record_athlete_id=" .
                    $row["athlete_id"] .
                    " AND athlete_id= record_athlete_id
                                                INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                                AND schedule_id = '$schedule_id' AND record_" . $result_type . "_record>0
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
                              style="float: left; width: auto; padding-right: 5px" readonly/>';
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
                                        style="float: left; width: auto; padding-right: 5px" readonly/>';
                echo "</td>";
              }
              if ($rows['schedule_sports'] === 'decathlon' || $rows['schedule_sports'] === 'heptathlon') {
                $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
              } else {
                $sport_code = $rows['schedule_sports'];
              }
              if (($row['record_new'] && null) == 'y') {
                if ($rows['schedule_status'] != 'y') {
                  $time = $rows['schedule_start'];
                } else {
                  $time = $rows['schedule_end'];
                }
                $athletics = check_my_record($row['athlete_name'], $sport_code, $time);
                if ((key($athletics) ?? null) === 'w') {
                  echo '<td><input placeholder=""  type="text" name="newrecord[]" class="input_text" value="세계신기록';
                  echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                } else if ((key($athletics) ?? null) === 'u') {
                  echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="세계U20신기록';
                  echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                } else if ((key($athletics) ?? null) === 'a') {
                  echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아신기록';
                  echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                } else if ((key($athletics) ?? null) === 's') {
                  echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아U20신기록';
                  echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                } else if ((key($athletics) ?? null) === 'c') {
                  echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="대회신기록';
                  echo '" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                } else {
                  echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . $row["record_" . $result_type . "_record"] . '" readonly/></td>';
                }
              } else {
                echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="' . $row['athlete_name'] . '" sports=' . $sport_code . ' schedule_id="' . $schedule_id . '" record="' . ($row["record_" . $result_type . "_record"] ?? null) . '" readonly/></td>';
              }
              $cnt++;
            }
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
                              if ($rows['schedule_sports'] == "polevault" || $rows['schedule_sports'] == "highjump") {
                                echo "/record/field_vertical_result_view.php?id=" . $schedule_id;
                              } else if ($rows['schedule_sports'] == "longjump" || $rows['schedule_sports'] == "triplejump") {
                                echo "/record/field_horizontal_result_view.php?id=" . $schedule_id;
                              } else {
                                echo "/record/field_normal_result_view.php?id=" . $schedule_id;
                              }
                              echo '\')"기록 입력</button>';
                              if (authCheck($db, "authRecordsUpdate")) {
                                echo '<input type="button" onclick="if (window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
                                echo 'location.href =';
                                echo '\'./record_change_type.php?id='.$schedule_id.'\'';
                                echo '}" class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                              }
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
                      <th>선수 국가</th>
                    </tr>
                    <tr class="filed2_bottom">
                    </tr>
                </thead>
                <tbody class="table_tbody entry_table">
                <?php
                  $Subsql = "SELECT distinct record_id, athlete_country, athlete_id, record_order,schedule_result FROM list_record 
                            INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                            INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                            WHERE schedule_id = '" . $schedule_id . "' GROUP BY athlete_id ORDER BY record_order ASC;";

                  $Subresult = $db->query($Subsql);
                  $i = 1;
                  $num = 0;
                  while ($row = mysqli_fetch_array($Subresult)) {
                    $num++;
                    echo "<tr";
                    if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                    echo "<td>" . $i . "번</td>";
                  ?>
                    <td><a <?php if ($row['schedule_result'] == 'n') { ?> onclick="createPopupWin('sport_change_member.php?athlete=<?php echo $row['athlete_id'] ?>&record=<?php echo $row['record_id'] ?>&schedule=<?php echo $schedule_id ?>&sport=jump','창 이름',900,512)" <?php } ?>><?php echo htmlspecialchars($row['athlete_country']) ?></a>
                    </td>
                  </tr>
                  <?php
                    $i++;
                  } ?>
                </tbody>
            </table>
            <?php
                if (authCheck($db, "authSchedulesUpdate")) {  ?>
                  <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_vertical_result_insert.php">
                    <span>확인</span>
                  </button>
            <?php } elseif (authCheck($db, "authSchedulesDelete")) {  ?>
              <button type="submit" class="btn_login" name="addresult" formaction="../action/record/field_vertical_result_insert.php">
                <span>확인</span>
              </button>
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