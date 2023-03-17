    <?
    $s_id=$_GET['id'];
    require_once __DIR__ . "/../head.php";
    require_once __DIR__ . "/../backheader.php"; //B:데이터베이스 연결
    $sql =
      "SELECT DISTINCT schedule_round,schedule_status,schedule_name,schedule_sports,schedule_memo,schedule_result FROM list_record INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$s_id'";
    $result = $db->query($sql);
    $rows = mysqli_fetch_assoc($result);
    if($rows['schedule_result']=='o'){
      $result_type='official';
  }else{
      $result_type='live';
  }
    $judgesql="select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$s_id'";
    $judgeresult=$db->query($judgesql);
    $judgerow=mysqli_fetch_array($judgeresult);
?>
    <!--Data Tables-->

    <link rel="stylesheet" type="text/css" href="../assets/DataTables/datatables.min.css" />
    <script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="../assets/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="../assets/js/useDataTables.js"></script>
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
                    calcal < ul parseFloat(high[i - 1].value) ||
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
                        <?=$rows['schedule_name']?>
                    </p>
                    <form method="post" class="form">
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
                                        <? echo '<input placeholder="라운드" type="text" name="round" value="' .
                                          $rows["schedule_round"] . '" maxlength="16" required="" readonly />'; ?>
                                    </li>
                                    <li class="row input_row referee_row">
                                        <span>심판 이름</span>
                                        <? echo '<input placeholder="심판 이름" type="text" name="refereename"  
                                            value="'.($judgerow['judge_name']??$_SESSION['Judge_name']).'"
                                        maxlength="30" required="" readonly />'; ?>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="Thorw_result high_jump">
                            <div class="relay_result">
                                <h1 class="throw_tit tit_left_green">결과</h1>
                                <div class="BTNform BTNform2">
                                    <?php 
                                  if ($rows["schedule_status"] === "y") {
                                  echo' <div class="btn_base base_mar col_left">
                                  <input type="button" onclick="execute_excel()" class="defaultBtn BIG_btn excel_Print filedBTN" value="엑셀 출력" />
                              </div>
                              <button type="submit" class="defaultBtn BIG_btn BTN_Red2 filedBTN" formaction="field_vertical_result_pdf.php"><span>PDF 출력</span></button>
                              <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="field_vertical_result_word.php"><span>워드 출력</span></button>';
                                  }
                                ?>
                                </div>
                            </div>
                            <div class="high_jump_tit">
                                <ul>
                                    <li>
                                        <p>등수</p>
                                    </li>
                                    <li>
                                        <p>순서</p>
                                    </li>
                                    <li>
                                        <p>이름</p>
                                    </li>
                                    <li>
                                        <p>높이</p>
                                        <p>높이</p>
                                        <?
                                          // 높이 찾는 쿼리
                                          $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record FROM list_record INNER JOIN list_schedule 
                                              ON list_schedule.schedule_id= list_record.record_schedule_id AND list_schedule.schedule_id = '$s_id' and record_".$result_type."_record>0 limit 12");
                                          $cnt1 = 0;
                                          while ($highrow = mysqli_fetch_array($highresult)) {
                                            echo '<p><input placeholder="높이" type="text" name="trial[]" id="trial" value="' .
                                              $highrow["record_".$result_type."_record"] . '" maxlength="4" onkeyup="heightFormat(this)"></p>';
                                            $cnt1++;
                                          }
                                          for ($j = 0; $j < 12 - $cnt1; $j++) {
                                            echo '<p><input placeholder="높이" type="text" name="trial[]" id="trial" value="" maxlength="4" 
                                                  onkeyup="heightFormat(this)"></p>';
                                          }
                                        ?>
                                    </li>
                                    <li>
                                        <p>기록</p>
                                    </li>
                                    <li>
                                        <p>비고</p>
                                        <p>신기록</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="high_jump_list high_jump_item">
                                <ul>
                                    <li>
                                        <p>등수</p>
                                    </li>
                                    <li>
                                        <p>1</p>
                                    </li>
                                    <li>
                                        <p>Nadezhda Dubo</p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <input type="text">
                                        </p>
                                        <p>
                                            <input type="text">
                                        </p>
                                    </li>
                                    <li>
                                        <p>기록</p>
                                    </li>
                                    <li>
                                        <p>비고</p>
                                        <select title="신기록" name="record">
                                            <option>해당없음</option>
                                            <option>대회신기록</option>
                                            <option>아시아신기록</option>
                                            <option>아시아U20신기록</option>
                                            <option>세계U20신기록</option>
                                            <option>세계신기록</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <?
                        if ($rows["schedule_status"] === "y") {
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
                            INNER JOIN list_schedule ON schedule_id= record_schedule_id 
                            AND schedule_id = '$s_id'".$jo."
                            ORDER BY ".$order." ASC , record_".$result_type."_record ASC"
                        );
                        $cnt = 1;
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                          $num++;
                          echo '<div class="high_jump_list high_jump_item';
                          if ($num%2 == 1) echo ' Ranklist_Background">'; else echo ">";
                          echo '<ul>';
                          echo '<li><p><input type="number" name="rank[]" id="rank" value="' .
                            ($row["record_".$result_type."_result"] ?? null) .
                            '"min="1" required="" /></p></li>';
                          echo '<li><p><input type="number" name="rain[]" value="' .
                            $row["record_order"] .
                            '" min="1" max="12" required="" readonly /></p></li>';
                          echo '<li><p><input placeholder="선수 이름" type="text" name="playername[]" id="name"
                                value="' .
                            $row["athlete_name"] .
                            '" maxlength="30" required="" readonly/></p></li>';
                            echo "<li><ul>";
                          $cnt3 = 1;
                          $record = $db->query(
                              "SELECT record_trial FROM list_record
                               INNER JOIN list_athlete ON record_athlete_id=" .
                               $row["athlete_id"] .
                              " AND athlete_id= record_athlete_id
                               INNER JOIN list_schedule ON schedule_id= record_schedule_id
                               AND schedule_id = '$s_id' AND record_".$result_type."_record>0
                               ORDER BY record_".$result_type."_record ASC limit 12"); //선수별 기록 찾는 쿼리
                          
                          while ($recordrow = mysqli_fetch_array($record)) {
                            echo "<li><p>";
                            echo '<input placeholder="" type="text" name="gameresult' . $cnt3 .
                                '[]" value="' . $recordrow["record_trial"] . '" maxlength="3" onkeyup="highFormat(this)"/>';
                            echo "</p></li>";
                            $cnt3++;
                          }
                          for ($a = $cnt3; $a <= 12; $a++) {
                            //기록을 제외한 빈칸으로 생성
                            echo "<li><p>";
                            echo '<input placeholder="" type="text" name="gameresult' . $a . '[]" value="" maxlength="3" onkeyup="highFormat(this)"/>';
                            echo "</p></li>";
                          }
                          echo "</ul><ul>";
                          if ($cnt3 == 12) {
                          //13번째 기록부터
                            $record = $db->query(
                                "SELECT record_trial,record_athlete_id FROM list_record
                                 INNER JOIN list_athlete ON record_athlete_id=" .
                                 $row["athlete_id"] .
                                 " AND athlete_id= record_athlete_id
                                 INNER JOIN list_schedule ON schedule_id= record_schedule_id
                                 AND schedule_id = '$s_id' AND record_".$result_type."_record>0
                                 ORDER BY record_".$result_type."_record ASC limit 12,12"); //선수별 기록 찾는 쿼리
                              while ($recordrow = mysqli_fetch_array($record)) {
                                echo "<li><p>";
                                echo '<input placeholder="" type="text" name="gameresult' . $cnt3 . '[]" value="' .  $recordrow["record_trial"] . '" maxlength="3" onkeyup="highFormat(this)"/>';
                                echo "</p></li>";
                                $cnt3++;
                              }
                            } else {
                              $cnt3 = 13;
                            }
                            for ($a = $cnt3; $a <= 24; $a++) {
                              //기록을 제외한 빈칸으로 생성
                              echo "<li><p>";
                              echo '<input placeholder="" type="text" name="gameresult' . $a . '[]" value=""
                                          maxlength="3" onkeyup="highFormat(this)" />';
                              echo "</p></li>";
                            }
                            echo "</ul></li>";
                          
                          //
                          echo '<li><p>';
                          echo '<input placeholder="결과" id="result" type="text" name="gameresult[]" value="' .
                            ($row["record_".$result_type."_record"] ?? null) . '" maxlength="5" required="" />';
                          echo "</p></li>";
                          echo '<span><input type="hidden" name="compresult[]" value="'.($row["record_".$result_type."_record"] ?? null) .'"/></span>';
                          echo '<li><p><input placeholder="비고" type="text" name="bigo[]" value="' .
                            ($row["record_memo"] ?? null) .
                            '" maxlength="100" /></p></li>';
                          
                          
                          if(($row['record_new']&&null) =='y'){
                            $newrecord=$db->query("SELECT worldrecord_athletics FROM list_worldrecord WHERE worldrecord_athlete_name ='".$row['athlete_name']."' AND worldrecord_sports='".$rows['schedule_sports']."'and worldrecord_record='".($row["record_".$result_type."_record"] ?? null)."'");
                            echo "SELECT worldrecord_athletics FROM list_worldrecord WHERE worldrecord_athlete_name ='".$row['athlete_name']."' AND worldrecord_sports='".$rows['schedule_sports']."'".'<br>';
                            //추후에 태블릿용 페이지를 만든 후 일정과 연결 시 스포츠이름 받아와야함
                            $newathletics= array();
                            while($athletics=mysqli_fetch_array($newrecord)){
                              $newathletics[]=$athletics[0];
                            }
                            if(($newathletics[0]??null)==='w'){
                              echo '<p><input placeholder=""  type="text" name="newrecord[]" value="세계신기록';
                              if(count($newathletics)>1){
                                echo ' 외 '.(count($newathletics)-1).'개';
                                }
                                echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';
                            }else if(($newathletics[0]??null)==='u'){
                              echo '<p><input placeholder="" type="text" name="newrecord[]" value="세계U20신기록';
                              if(count($newathletics)>1){
                                echo ' 외 '.(count($newathletics)-1).'개';
                              }
                                            echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';   
                                        }else if(($newathletics[0]??null)==='a'){
                                            echo '<p><input placeholder="" type="text" name="newrecord[]" value="아시아신기록';
                                            if(count($newathletics)>1){
                                                echo ' 외 '.(count($newathletics)-1).'개';
                                            }
                                            echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';                       
                                        }else if(($newathletics[0]??null)==='s'){
                                            echo '<p><input placeholder="" type="text" name="newrecord[]" value="아시아U20신기록';
                                            if(count($newathletics)>1){
                                                echo ' 외 '.(count($newathletics)-1).'개';
                                            }
                                            echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';                        
                                        }else if(($newathletics[0]??null)==='c'){
                                            echo '<p><input placeholder="" type="text" name="newrecord[]" value="대회신기록';
                                            if(count($newathletics)>1){
                                                echo ' 외 '.(count($newathletics)-1).'개';
                                            }
                                            echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';
                                        }else{
                                            echo '<p><input placeholder="" type="text" name="newrecord[]" value="" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';                                      
                                        }
  
                                    }else{
                                        echo '<p><input placeholder="" type="text" name="newrecord[]" value="" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$s_id.'" record="'.$row["record_".$result_type."_record"].'" readonly/></p>';
                                    }
                          $cnt++;
                          echo "</li></li></ul></div>";
                        }
                        ?>
                        </div>

                        <div class="modify_Btn input_Btn result_Btn">
                            <button class=" BTN_Red" type="submit">순서 재 정렬</button>
                            <button class=" BTN_Blue" type="submit">등록하기</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="/assets/js/main.js"></script>
    </body>

    <body>
        <!-- contents 본문 내용 -->
        <div class="container">
            <!-- class="contents something" -->
            <div class="something" style="padding: 100px 15px 60px 15px">
                <form method="post" class="form">
                    <h3 style="width:45%; display:inline-block; margin-right: 4.6%;">경기 이름</h3>
                    <h3 style="width:50%; display:inline-block;">라운드</h3>
                    <div class="input_row" style="width:45%; margin-right: 4.6%;">

                    </div>
                    <div class="input_row" style="width:50%;">

                    </div>
                    <h3>심판 이름</h3>
                    <input type="hidden" name="schedule_id" value="<?=$s_id?>">
                    <div class="input_row">

                    </div>
                    <div class="btn_base base_mar" style="display:inline">
                        <h2 style="margin-bottom: 10px; float:left; margin-right: 30px;">결과</h2>

                    </div>
                    <table cellspacing="0" cellpadding="0" class="team_table" style="border-top: 0px">
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
                        <thead>

                            <tr id="col1">
                                <th style="background: none" rowspan="2">등수</th>
                                <th style="background: none" rowspan="2">순서</th>
                                <th style="background: none" rowspan="2">이름</th>

                                <th style="background: none" rowspan="2">기록</th>
                                <th style="background: none">비고</th>

                            </tr>
                            <tr id="col2">
                                <?php if ($cnt1 == 12) {
                              $cnt2 = 0;
                              $highresult = $db->query("SELECT DISTINCT record_".$result_type."_record FROM list_record INNER JOIN list_schedule 
                                ON list_schedule.schedule_id= list_record.record_schedule_id AND list_schedule.schedule_id = '$s_id' and record_".$result_type."_record>0 limit 12,12");
                              while ($highrow = mysqli_fetch_array($highresult)) {
                                echo '<th style="background: none"><input placeholder="높이" type="text" name="trial[]"
                                    class="input_trial" id="trial" value="' .
                                  $highrow["record_".$result_type."_record"] .
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
                        </thead>
                        <tbody id="body">

                        </tbody>
                    </table>
                    <h3>경기 비고</h3>
                    <div class="input_row">
                        <input placeholder="비고를 입력해주세요." type="text" name="bibigo"
                            value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                    </div>
                    <div style='display:flex; width:100%;'>
                        <?php
                        if ($rows["schedule_status"] != "y") {
                        echo '<div class="signup_submit" style="width:49%; margin-right:1%">
                              <button type="submit" class="btn_login" name="addtempresult"
                                  formaction="../action/record/field_vertical_result_insert.php">
                                  <span>임시저장</span>
                              </button>
                          </div>';
                          echo '<div class="signup_submit" style="width:49%;">
                            <button type="submit" class="btn_login" name="addresult"
                                formaction="../action/record/field_vertical_result_insert.php">
                                <span>확인</span>
                            </button>
                        </div>';
                    }else{
                      echo '<div class="signup_submit" style="width:100%;">
                            <button type="submit" class="btn_login" name="addresult"
                                formaction="../action/record/field_vertical_result_insert.php">
                                <span>확인</span>
                            </button>
                        </div>';
                    }
                    ?>
                    </div>
                </form>
            </div>
        </div>
    </body>

    </html>