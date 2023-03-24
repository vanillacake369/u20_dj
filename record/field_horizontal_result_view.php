<?php
    $schedule_sports=$_POST['sports'];
    $schedule_round=$_POST['round'];
    $gender=$_POST['gender'];
    $group=$_POST['group'];
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../includes/auth/config.php"; //B:데이터베이스 연결 
    
    $sql = "SELECT DISTINCT * FROM list_record  join list_schedule where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' AND schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round";
    $result=$db->query($sql);
    $rows = mysqli_fetch_assoc($result);
    if ($rows['schedule_sports'] == 'decathlon' || $rows['schedule_sports'] == 'heptathlon') {
        $check_round = 'y';
    } else {
        $check_round = 'n';
    }
    if($rows['record_status']=='o'){
        $result_type='official';
    }else{
        $result_type='live';
    }
    $judgesql = "select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'";
    $judgeresult = $db->query($judgesql);
    $judgerow = mysqli_fetch_array($judgeresult);
    ?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/xeicon.min.css">
    <link rel="stylesheet" href="../assets/css/swiper.min.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=33">
    <title>u20 관리자 페이지</title>
    <script type="text/javascript" src="../assets/js/onlynumber.js"></script>
    <script type="text/javascript" src="../assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="../assets/js/change_athletics.js"></script>
    <script type="text/javascript" src="../action/record/result_field_horizontal_execute_excel(include_wind).js">
    </script>
    <script type="text/javascript">
    window.onload = function() {
        for (k = 0; k < document.querySelectorAll('#name').length; k++) {
            let a = document.querySelectorAll('#name')[k];
            fieldFinal2(a);
            console.log(k)
        }
        rankcal4()
    }
    $(document).on("click", "button[name='addtempresult']", function() {
        $(".signup_submit").append("<input type='hidden' name=tempstore value='1'>");
    });
    $(document).on("click", "button[name='addresult']", function() {
        $(".signup_submit").append("<input type='hidden' name=tempstore value='0'>")
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
    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="../assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    <?=$_POST['sports']?>
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
                                    <span>경기 시작 시간</span>
                                    <?php
                                    echo '<input placeholder="시작 시간" type="text" name="starttime" class="input_text" value="'. ($rows['record_start']) .'"
                                    maxlength="30" required="" />';
                                    ?>
                                    <input type="button" onclick="input_time()" class="btn_add bold" value="현재 시간" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="Thorw_result">
                        <div class="relay_result">
                            <h3 class="UserProfile_tit tit_left_green tit_padding">결과</h3>
                        </div>
                    </div>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 3%" />
                            <col style="width: 3%" />
                            <col style="width: 6%" />
                            <col style="width: 14%" />
                            <?php
                                if ($check_round == 'y') {
                                    echo '<col style="width: 15%" />';
                                    echo '<col style="width: 15%" />';
                                    echo '<col style="width: 15%" />';
                                    echo '<col style="width: 15%" />';
                                    echo '<col style="width: 14%" />';
                                }else{
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 9%" />';
                                    echo '<col style="width: 10%" />';
                                    echo '<col style="width: 10%" />';
                                }
                                ?>
                        </colgroup>
                        <thead class="result_table entry_table">
                            <tr>
                                <th style="background: none" rowspan="2">등수</th>
                                <th style="background: none" rowspan="2">순서</th>
                                <th style="background: none" rowspan="2">BIB</th>
                                <th style="background: none" rowspan="2">이름</th>
                                <th style="background: none">1차 시기</th>
                                <th style="background: none">2차 시기</th>
                                <th style="background: none">3차 시기</th>
                                <?php
                                if ($check_round == 'y') {
                                }else{
                                    echo '<th style="background: none">4차 시기</th>';
                                    echo '<th style="background: none">5차 시기</th>';
                                    echo '<th style="background: none">6차 시기</th>';
                                }
                                ?>
                                <th style="background: none">기록</th>
                                <th style="background: none">비고</th>

                            </tr>
                            <tr>
                                <?php
                                if ($check_round == 'y') {
                                  echo '<th style="background: none" colspan="4">풍속</th>';
                                }else{
                                  echo '<th style="background: none" colspan="7">풍속</th>';
                                }
                                ?>
                                <th style="background: none">신기록</th>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody entry_table">
                            <?php
                        $i=1;
                            $count=0; //신기록 위치 관련 변수
                            $trial=1;
                            $order = "record_order";
                        $obj = "record_".$result_type."_result,record_memo,record_".$result_type."_record,record_wind,";
                        if ($rows["record_state"] === "y") {
                            $order = "record_".$result_type."_result";
                            $check='record_'.$result_type.'_result>0';
                        } elseif ($_POST["check"] ?? null === "5") {
                            $trial = 6;
                            $check='record_trial ='.$trial.'';
                        } elseif ($_POST["check"] ?? null === "3") {
                            $trial = 4;
                            $check='record_trial ='.$trial.'';
                        } else {
                            $trial = 1;
                            $check='record_trial ='.$trial.'';
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
                        echo "<tr>";
                        echo '<td rowspan="2"><input type="number" name="rank[]" class="input_text" id="rank" value="' .
                            ($id["record_".$result_type."_result"] ?? null) .
                            '" min="1" required="" /></td>';
                            echo '<td rowspan="2"><input type="number" name="rain[]" class="input_text" value="';
                            if($id['record_order']>=9 && ($_POST["check"] ?? null>=3 || ($_POST["check"] ?? null) === null)){
                                echo '-';
                            }else{
                                echo $id['record_order'];
                            }
                            echo '" min="1" max="12" required="" readonly /></td>';
                            echo '<td rowspan="2"><input placeholder="등번호" id="bib" type="text" name="playerbib[]" class="input_text" value="'.$id["athlete_bib"].'" maxlength="30" required="" readonly /></td>';
                            echo '<td rowspan="2"><input placeholder="선수 이름" id="name" type="text" name="playername[]" class="input_text" value="'.$id["athlete_name"].'" maxlength="30" required="" readonly /></td>';
                            $answer = $db->query(
                            "SELECT record_".$result_type."_record,record_wind FROM list_record
                                INNER JOIN list_athlete ON record_athlete_id=" .
                                $id["a_id"] .
                                " AND athlete_id= record_athlete_id and
                                record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'
                                ORDER BY record_trial ASC"
                            );
                            while ($row = mysqli_fetch_array($answer)) {
                              echo "<td>";
                              echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                                $i .
                                '[]" class="input_text" value="' .
                                ($row["record_".$result_type."_record"] ?? null) .
                                '"
                                  maxlength="5" onkeyup="field2Format(this)"
                                  style="float: left; width: auto; padding-right: 5px" />';
                              echo "</td>";
                              $i++;
                            }
                          if($rows['schedule_name']==='Decathlon' || $rows['schedule_name']==='Heptathlon'){
                            $k=3;
                        }else{
                            $k=6;
                        }
                          for ($j = $i; $j <= $k; $j++) {
                            echo "<td>";
                            echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                              $j .
                              '[]" class="input_text" value=""
                                            maxlength="5" onkeyup="field2Format(this)"
                                            style="float: left; width: auto; padding-right: 5px" />';
                            echo "</td>";
                          }
                          echo "<td>";
                          echo '<input placeholder="경기 결과" id="result" type="text" name="gameresult[]" class="input_text"
                                    value="' .
                            ($id["record_".$result_type."_record"] ?? null) .
                            '" maxlength="5" required="" onkeyup="field2Format(this)"
                                    style="float: left; width: auto; padding-right: 5px" />';
                          echo "</td>";
                          echo '<input type="hidden" name="compresult[]" value="'.($id["record_".$result_type."_record"] ?? null).'"/>';
                          echo '<td><input type="text" placeholder ="비고"name="bigo[]" class="input_text" value="' .
                            ($id["record_memo"] ?? null) .
                            '" maxlength="100" /></td>';
                          echo "<tr>";
                              $wind=$db->query("SELECT record_wind FROM list_record
                              INNER JOIN list_athlete ON record_athlete_id=" .
                              $id["a_id"] .
                              " AND athlete_id= record_athlete_id
                              and record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group'
                              ORDER BY record_trial ASC limit 6 ");
                          for ($j = 0; $j <= $k; $j++) {
                                $windrow=mysqli_fetch_array($wind);
                            if ($j % 7 == $k) {
                              echo "<td>";
                              echo '<input placeholder="풍속" type="text" name="lastwind[]" class="input_text" value="' .
                                ($id["record_wind"] ?? null) .
                                '"
                                            maxlength="3" onkeyup="windFormat(this)"
                                            style="float: left; width: auto; padding-right: 5px" />';
                              echo "</td>";
                            } else {
                              echo "<td>";
                              echo '<input placeholder="풍속" type="text" name="wind' .
                                ($j + 1) .
                                '[]" class="input_text" value="'.($windrow["record_wind"] ?? null).'"
                                                maxlength="3" onkeyup="windFormat(this)"
                                                style="float: left; width: auto; padding-right: 5px"';
                              echo  '/>';
                              echo "</td>";
                            }
                          }
                          if($rows['schedule_name'] ==='Decathlon' || $rows['schedule_name'] ==='Heptathlon'){
                              $sport_code=$rows['schedule_sports']."(".$rows['schedule_round'].")";                                  
                          }else{
                              $sport_code=$rows['schedule_sports'];
                          }
                          if(($id['record_new']??null) =='y'){
                                        if($rows['record_state'] !='y'){
                                            $time=$rows['record_start'];
                                        }else{
                                            $time=$rows['record_end'];
                                        }
                                        $athletics=check_my_record($id['athlete_name'],$sport_code,$time);
                                        if((key($athletics)??null)==='w'){
                                            echo '<td><input placeholder=""  type="text" name="newrecord[]" class="input_text" value="세계신기록';
                                            echo '" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></td>';
                                        }else if((key($athletics)??null)==='u'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="세계U20신기록';
                                            echo '" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></td>';   
                                        }else if((key($athletics)??null)==='a'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아신기록';
                                            echo '" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></td>';                       
                                        }else if((key($athletics)??null)==='s'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아U20신기록';
                                            echo '" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></td>';                        
                                        }else if((key($athletics)??null)==='c'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="대회신기록';
                                            echo '" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></td>';
                                        }else{
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.($row['record_'.$result_type.'_record'] ?? null).'" readonly/></td>';
                                        }
  
                                    }else{
                                        echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="'.$id['athlete_name'].'" sports='.$sport_code.' record_round="'.$schedule_round.'" and record_gender ="'.$gender.'" and record_group="'.$group.'" record="'.($row['record_'.$result_type.'_record'] ?? null).'" readonly/></td>';
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
                    <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
                    <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="input_text"
                        value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                    <div class="modify_Btn input_Btn result_Btn">
                        <?php 
                        if ($rows["record_state"] != "y") {
                            if (($rows["schedule_name"] == 'Decathlon' || $rows["schedule_name"] == 'Heptathlon')) {
                            } else {
                                echo '<button class="BTN_Red" type="submit" formaction="/action/record/three_try_after_reverse.php">순서 재정렬</button>';
                            }
                        }
                        ?>
                        <?php
                        if ($rows["record_state"] != "y") {
                            echo '<div class="signup_submit" style="width:48%; margin-right:1%">
                            <button type="submit" class="btn_login" name="addtempresult"
                            formaction="../action/record/field_horizontal_result_insert.php">
                            <span>임시저장</span>
                            </button>
                            </div>';
                            echo '<div class="signup_submit" style="width:49%;">
                            <button type="submit" class="btn_login" name="addresult"
                            formaction="../action/record/field_horizontal_result_insert.php">
                            <span>확인</span>
                            </button>
                            </div>';
                        }else{
                            echo '<div class="signup_submit" style="width:100%;">
                            <button type="submit" class="btn_login" name="addresult"
                            formaction="../action/record/field_horizontal_result_insert.php">
                                <span>확인</span>
                                </button>
                                </div>';
                            }
                            ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="../assets/js/main.js?ver=7"></script>
</body>

</html>