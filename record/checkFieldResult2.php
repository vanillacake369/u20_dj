<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../fontawesome/css/all.min.css" />
  <script src="..//fontawesome/js/all.min.js"></script>
  <!--Data Tables-->
  <link rel="stylesheet" type="text/css" href="../DataTables/datatables.min.css" />
  <script type="text/javascript" src="../DataTables/datatables.min.js"></script>
  <script type="text/javascript" src="../js/useDataTables.js"></script>
  <script type="text/javascript" src="../js/onlynumber.js"></script>
  <script type="text/javascript">
    function back() {
      window.history.back();
    }
  </script>
  <title>U20</title>
</head>

<body>
  <?php
  include_once(__DIR__ . "/../database/dbconnect.php"); //B:데이터베이스 연결 
  $sql =
    "SELECT DISTINCT judge_name,schedule_round,schedule_status,schedule_name,schedule_sports FROM list_judge JOIN list_record ON judge_id = record_judge INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '4'";
  $result = $db->query($sql);
  $rows = mysqli_fetch_assoc($result);
  ?>
  <!-- contents 본문 내용 -->
  <div class="container">
    <!-- class="contents something" -->
    <div class="something" style="padding: 100px 15px 60px 15px">
      <form method="post" class="form">
        <h3 style="width:45%; display:inline-block; margin-right: 4.6%;">경기 이름</h3>
        <h3 style="width:50%; display:inline-block;">라운드</h3>
        <div class="input_row" style="width:45%; margin-right: 4.6%;">
          <?php
          echo '<input placeholder="경기 이름" type="text" name="gamename" class="input_text" value="' . $rows['schedule_name'] . '"
                        maxlength="16" required="" readonly />';
          ?>
        </div>
        <div class="input_row" style="width:50%;">
          <?php echo '<input placeholder="라운드" type="text" name="round" class="input_text" value="' .
            $rows["schedule_round"] .
            '"
                    maxlength="16" required="" readonly />'; ?>
        </div>
        <h3>심판 이름</h3>
        <div class="input_row">
          <?php echo '<input placeholder="심판 이름" type="text" name="refereename" class="input_text" value="' .
            $rows["judge_name"] .
            '"
                    maxlength="30" required="" readonly />'; ?>
        </div>
        <div class="btn_base base_mar" style="display:inline">
          <h2 style="margin-bottom: 10px; float:left; margin-right: 30px;">결과</h2>
          <div class="btn_base base_mar col_left">
            <input type="button" onclick="" class="btn_excel bold" value="엑셀 출력" />
          </div>
          <button type="submit" class="btn_add bold" formaction="pdfout4.php"><span>PDF 출력</span></button>
        </div>
        <table cellspacing="0" cellpadding="0" class="team_table" style="border-top: 0px">
          <colgroup>
            <col style="width: 5%" />
            <col style="width: 5%" />
            <col style="width: 20%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 8%" />
            <col style="width: 16%" />

          </colgroup>
          <thead>
            <tr>
              <th style="background: none" rowspan="2">등수</th>
              <th style="background: none" rowspan="2">순서</th>
              <th style="background: none" rowspan="2">이름</th>
              <th style="background: none">1차 시기</th>
              <th style="background: none">2차 시기</th>
              <th style="background: none">3차 시기</th>
              <th style="background: none">4차 시기</th>
              <th style="background: none">5차 시기</th>
              <th style="background: none">6차 시기</th>
              <th style="background: none">기록</th>
              <th style="background: none">비고</th>

            </tr>
            <tr>
              <th style="background: none" colspan="7">풍속</th>
              <th style="background: none">신기록</th>
            </tr>
          </thead>
          <tbody id="body">
            <?php
            $i = 1;
            $count = 0; //신기록시 셀렉트 박스 찾는 용도
            $trial = 0;
            $order = "record_order";
            $obj = "record_live_result,record_new,record_memo,athlete_id,record_live_record,record_wind,";
            $order = "record_live_result";
            $check = 'record_live_result>0';
            $sql2 =
              "SELECT DISTINCT  " .
              $obj .
              "record_order,athlete_name FROM list_record 
                                    INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                                    INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                                    where $check AND schedule_id = '4'
                                    ORDER BY $order ASC";
            $result2 = $db->query($sql2);
            while ($id = mysqli_fetch_array($result2)) {
              echo "<tr>";
              echo '<td rowspan="2"><input type="number" name="rank[]" class="input_text" id="rank" value="' .
                ($id["record_live_result"] ?? null) .
                '" min="1" max="12" required=""  readonly/></td>';
              echo '<td rowspan="2"><input type="number" name="rain[]" class="input_text" value="' .
                $id["record_order"] .
                '" min="1" max="12" required="" readonly /></td>';
              echo '<td rowspan="2"><input placeholder="선수 이름" type="text" name="playername[]" class="input_text"
                                  value="' .
                $id["athlete_name"] .
                '" maxlength="30" required="" readonly /></td>';
              $answer = $db->query(
                "SELECT record_live_record,record_wind FROM list_record
                                INNER JOIN list_athlete ON record_athlete_id=" .
                  $id["athlete_id"] .
                  " AND athlete_id= record_athlete_id
                                INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                AND schedule_id = '4'
                                ORDER BY record_trial ASC"
              );
              while ($row = mysqli_fetch_array($answer)) {
                echo "<td>";
                echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                  $i .
                  '[]" class="input_text" value="' .
                  ($row["record_live_record"] ?? null) .
                  '"
                                  maxlength="5" onkeyup="field1Format(this)"
                                  style="float: left; width: auto; padding-right: 5px" readonly />';
                echo "</td>";
                $i++;
              }
              echo "<td>";
              echo '<input placeholder="경기 결과" id="result" type="text" name="gameresult[]" class="input_text"
                                    value="' .
                ($id["record_live_record"] ?? null) .
                '" maxlength="5" required="" onkeyup="field1Format(this)"
                                    style="float: left; width: auto; padding-right: 5px"  readonly/>';
              echo "</td>";
              echo '<td><input type="text" placeholder ="비고"name="bigo[]" class="input_text" value="' .
                ($id["record_memo"] ?? null) .
                '" maxlength="100"  readonly/></td>';
              echo "<tr>";
              for ($j = 0; $j <= 6; $j++) {
                $wind = $db->query("SELECT record_wind FROM list_record
                              INNER JOIN list_athlete ON record_athlete_id=" .
                  $id["athlete_id"] .
                  " AND athlete_id= record_athlete_id
                              INNER JOIN list_schedule ON schedule_id= record_schedule_id
                              AND schedule_id = '4'
                              ORDER BY record_live_record ASC limit 6 ");
                $windrow = mysqli_fetch_array($wind);
                if ($j % 7 == 6) {
                  echo "<td>";
                  echo '<input placeholder="풍속" type="text" name="lastwind[]" class="input_text" value="' .
                    ($id["record_wind"] ?? null) .
                    '"
                                            maxlength="5" required="" onkeyup="windFormat(this)"
                                            style="float: left; width: auto; padding-right: 5px"  readonly/>';
                  echo "</td>";
                } else {
                  echo "<td>";
                  echo '<input placeholder="풍속" type="text" name="wind' .
                    ($j + 1) .
                    '[]" class="input_text" value="' .
                    ($windrow["record_wind"] ?? null) .
                    '"
                                                maxlength="5" required="" onkeyup="windFormat(this)"
                                                style="float: left; width: auto; padding-right: 5px" readonly />';
                  echo "</td>";
                }
              }
              //  echo '<td>
              //              <select name="newrecord[]" onFocus="this.initialSelect = this.selectedIndex;" 
              //             onChange="this.selectedIndex = this.initialSelect;">
              //             <option value='.'n'.'>해당없음</option>
              //             <option value='.'c'.'>대회신기록</option>
              //             <option value='.'a'.'>아시아신기록</option>
              //             <option value='.'s'.'>아시아U20신기록</option>
              //             <option value='.'u'.'>세계U20신기록</option>
              //             <option value='.'w'.'>세계신기록</option>
              //             </select>
              //             </td>';
              if (($id['record_new'] ?? null) == 'y') {
                $newrecord = $db->query("SELECT worldrecord_athletics 
                                        FROM list_worldrecord 
                                        WHERE worldrecord_athlete_name ='" . $id['athlete_name'] . "' AND worldrecord_sports='" . $rows['schedule_sports'] . "'");
                //추후에 태블릿용 페이지를 만든 후 일정과 연결 시 스포츠이름 받아와야함
                // $athletics=mysqli_fetch_array($newrecord);
                // echo '<script>';
                // switch($athletics[0]){
                //     case 'w': echo "document.querySelectorAll('[value=\"w\"]')[$count].selected=true"; break;
                //     case 'u': echo "document.querySelectorAll('[value=\"u\"]')[$count].selected=true"; break; 
                //     case 's': echo "document.querySelectorAll('[value=\"s\"]')[$count].selected=true"; break;
                //     case 'a': echo "document.querySelectorAll('[value=\"a\"]')[$count].selected=true"; break;
                //     case 'c': echo "document.querySelectorAll('[value=\"c\"]')[$count].selected=true"; break;                                     
                // }
                // echo '</script>';
                while ($athletics = mysqli_fetch_array($newrecord)) {
                  $newathletics[] = $athletics[0];
                }
                switch (($newathletics[0] ?? null)) {
                  case 'w':
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="WR';
                    if (count($newathletics) > 1) {
                      echo ' 외 ' . (count($newathletics) - 1) . '개';
                    }
                    echo '" maxlength="100" readonly/></td>';
                    break;
                  case 'u':
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="UWR';
                    if (count($newathletics) > 1) {
                      echo ' 외 ' . (count($newathletics) - 1) . '개';
                    }
                    echo '" maxlength="100" readonly/></td>';
                    break;
                  case 's':
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="AR';
                    if (count($newathletics) > 1) {
                      echo ' 외 ' . (count($newathletics) - 1) . '개';
                    }
                    echo '" maxlength="100" readonly/></td>';
                    break;
                  case 'a':
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="UAR';
                    if (count($newathletics) > 1) {
                      echo ' 외 ' . (count($newathletics) - 1) . '개';
                    }
                    echo '" maxlength="100" readonly/></td>';
                    break;
                  case 'c':
                    echo '<td<input placeholder="" type="text" name="newrecord[]" class="input_text" value=">CR';
                    if (count($newathletics) > 1) {
                      echo ' 외 ' . (count($newathletics) - 1) . '개';
                    }
                    echo '" maxlength="100" readonly/></td>';
                    break;
                  default:
                    echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" readonly/></td>';
                    break;
                }
              } else {
                echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" readonly/></td>';
              }
              echo "</tr>";
              echo "</tr>";
              $count++;
              $i = 1;
            }
            ?>
            </tr>
            </tr>
          </tbody>
        </table>
        <h3>경기 비고</h3>
        <div class="input_row">
          <input placeholder="비고를 입력해주세요." type="text" name="gamepass" class="input_text" value="" maxlength="100" />
        </div>
      </form>
      <div class="signup_submit">
        <button class="btn_login" onClick="back()">
          <span>확인</span>
        </button>
      </div>
    </div>
  </div>
</body>

</html>