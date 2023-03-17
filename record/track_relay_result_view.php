<?
    require_once __DIR__ . "/../head.php";
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../includes/auth/config.php";

    $id=$_GET['id'];
    //B:데이터베이스 연결
   $sql= "SELECT DISTINCT * FROM list_record  INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id'";
   $judgesql="select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id'";
   $judgeresult=$db->query($judgesql);
   $judgerow=mysqli_fetch_array($judgeresult);
   $result=$db->query($sql);
   $rows = mysqli_fetch_assoc($result);
   if($rows['schedule_result']=='o'){
       $result_type='official';
   }else{
       $result_type='live';
   }
?>
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
        let val = ddd[0].split(" ")[1];
        wind.value = val;
        let count = -1;
        for (i = 1; i < ddd.length; i++) {
            let k = ddd[i].split(" ")
            if (k[0].indexOf('rane') != 0) {
                count++

            } else {
                let on = document.querySelector("#" + k[0]).children
                let total = on[5].firstElementChild
                if (k[1]) {
                    on[4].firstElementChild.value = 'p'
                } else if (k[0] == 'DNS') {
                    on[4].firstElementChild.value = 'n'
                    total.value = 0
                    on[6].firstElementChild.value = k[0]
                } else if (k[0] == 'DNF') {
                    on[4].firstElementChild.value = 'n'
                    total.value = 0
                    on[6].firstElementChild.value = k[0]
                } else {
                    on[4].firstElementChild.value = 'd'
                    total.value = 0
                    on[6].firstElementChild.value = 'DQ'
                }
                let temp = k[1].split(":")
                if (temp.length == 2) {
                    total.value = parseFloat(total.value) + parseFloat(temp[0] * 60) + parseFloat(temp[1])
                } else {
                    total.value = parseFloat(total.value) + parseFloat(temp[0])
                }
                if (count == 3) {
                    if (parseInt(parseInt(total.value) / 60) >= 1) {
                        if (total.value % 60 < 10) {
                            total.value = parseInt(parseInt(total.value) / 60) + ":0" + (total.value % 60)
                                .toFixed(2)
                        } else {
                            total.value = parseInt(parseInt(total.value) / 60) + ":" + (total.value % 60)
                                .toFixed(2)
                        }
                    }
                }
            }
        }
        rankcal1()
    };
    reader.readAsText(file, /* optional */ "utf-8");
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
                    <?=$rows['schedule_name']?>
                </p>
                <form action="">
                    <input name="id" hidden value="<?=$_GET['id'];?>" />
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
                                        echo '<input placeholder="라운드" type="text" name="round" value="'.$rows['schedule_round'].'"
                                        maxlength="16" required="" readonly />';
                                    ?>
                                </li>
                                <li class="row input_row throw_row">
                                    <span>심판 이름</span>
                                    <input placeholder="심판 이름" type="text" name="refereename"
                                        value="<?=($judgerow[0]??$_SESSION['Judge_name'])?>" maxlength="30" required=""
                                        readonly />
                                    <input type="hidden" name="schedule_id" value="<?=$id?>">
                                </li>
                                <li class="row input_row throw_row">
                                    <span>풍속</span>
                                    <?php
                                        if($rows['schedule_status']==='y'){
                                            
                                            echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" class="input_text" value="'.$rows['record_wind'].'" maxlength="16"
                                                required="" />';
                                        }else{
                                            
                                            echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" class="input_text" value="" maxlength="16"
                                                required="" />';
                                            }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="Thorw_result">
                        <div class="relay_result">
                            <h1 class="relay_tit tit_left_green">결과</h1>
                            <div class="BTNform BTNform2">
                                <?php
                                if($rows['schedule_status']!='y'){
                                    echo '<input type="button" onclick="openTextFile()" class="defaultBtn BTN_green BIG_btn" value="자동 입력"
                                    style="margin-right:15px;">';
                                }else{
                                    echo' <div class="btn_base base_mar col_left">
                                        <input type="button" onclick="execute_excel()" class="defaultBtn BIG_btn excel_Print filedBTN" value="엑셀 출력" />
                                    </div>
                                    <button type="submit" class="defaultBtn BIG_btn BTN_Red2 filedBTN" formaction="track_relay_result_pdf.php"><span>PDF 출력</span></button>
                                    <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"" formaction="track_relay_result_word.php"><span>워드 출력</span></button>';
                                    }
                            ?>
                            </div>
                        </div>
                        <div class="relay_list">
                            <ul>
                                <li>
                                    <p>등수</p>
                                </li>
                                <li>
                                    <p>레인</p>
                                </li>
                                <li>
                                    <p>등번호</p>
                                </li>
                                <li>
                                    <p>이름</p>
                                </li>
                                <li>
                                    <p>국가</p>
                                </li>
                                <li>
                                    <p>경기 결과</p>
                                </li>
                                <li>
                                    <p>Reaction Time</p>
                                </li>
                                <li>
                                    <p>비고</p>
                                </li>
                                <li>
                                    <p>신기록</p>
                                </li>
                            </ul>
                        </div>
                        <?
                        $count=0;
                        $num = 0;
                        
                        $relm = 'athlete_country, athlete_bib, record_' . $result_type . '_result,record_' . $result_type . '_record,record_pass,record_memo,record_new,athlete_name,record_team_order,record_reaction_time,record_order';
                        if ($rows['schedule_status'] == 'y') {
                            $order = 'record_' . $result_type . '_result';
                        } else {
                            $order = 'record_order';
                        }
                        $sql = "SELECT  " . $relm . " FROM list_record 
                        INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                        INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id' 
                        ORDER BY " . $order . " ASC,record_team_order ASC ";
                        $result = $db->query($sql);
                        $count = 0;
                        $athrecord = array();
                        $athname = array();
                        while ($row = mysqli_fetch_array($result)) {
                            $athname[$count] = $row['athlete_name'];
                            $athrecord[$count%4]=$row['record_'.$result_type.'_record'];
                            
                            if($count%4==0){
                                $num++;
                                echo '<div class="relay_item"><ul';
                                if ($num%2 == 1) echo ' class="Ranklist_Background">'; else echo ">";
                                echo '<li><input type="number" name="rank[]" id="rank" value="'.$row['record_'.$result_type.'_result'].'" min="1" max="12" required="" /></li>';
                                echo '<li><input type="number" name="rain[]" value="'.$row['record_order'].'" min="1" max="12" required="" readonly /></li>';
                                echo '<li>';
                            }
                            if($count%4==3){
                                echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                class="input_text" value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly/>';
                                for ($k = $count - 3; $k <= $count; $k++) {
                                    if ($k == $count - 3) {
                                        echo '<li>';
                                    }
                                    if ($k == $count) {
                                        echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                        class="input_text" value="' . $athname[$k] . '" maxlength="30" required="" readonly/></li>';
                                    } else {
                                        echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                        class="input_text" value="' . $athname[$k] . '" maxlength="30" required="" readonly/>';
                                    }
                                }
                                echo '<li><input placeholder="국가" type="text" name="country" 
                                value="'.$row['athlete_country'].'" maxlength="30" required="" readonly /></li>';
                                echo '<li><input placeholder="경기 통과 여부" type="text" name="gamepass[]" value="'.$row['record_pass'].'" maxlength="1" required="" /></li>';
                                echo '<li>
                                    <input placeholder="경기 결과" type="text" name="gameresult[]" id="result" value="'.($athrecord[3] ?$athrecord[3]:0 ).'" maxlength="8" required="" onkeyup="trackFinal(this)"
                                   /><input type="hidden" name="compresult[]" value="'.($athrecord[3] ?$athrecord[3]:0 ).'"/></li></li>';
                                echo '<li>
                                <input placeholder="reactiontime" type="text" name="reactiontime[]" id="reactiontime" class="input_text"
                                value="' . ($row['record_reaction_time'] ?? null) . '" maxlength="8" required="" onkeyup="trackFinal(this)" /></li>';
                                echo '<li><input placeholder="비고" type="text" name="bigo[]" value="'.($row['record_memo'] ? $row['record_memo']:'').'" maxlength="100" /></li>';
                                        $sport_code=$rows['schedule_sports'];
                                        if($rows['schedule_status'] !='y'){
                                            $time=$rows['schedule_start'];
                                        }else{
                                            $time=$rows['schedule_end'];
                                        }
                                        $athletics=check_my_record($row['athlete_country'],$sport_code,$time);
                                        if((key($athletics)??null)==='w'){
                                            echo '<li><input placeholder=""  type="text" name="newrecord[]" value="세계신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';
                                        }else if((key($athletics)??null)==='u'){
                                            echo '<li><input placeholder="" type="text" name="newrecord[]" value="세계U20신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';   
                                        }else if((key($athletics)??null)==='a'){
                                            echo '<li><input placeholder="" type="text" name="newrecord[]" value="아시아신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';                       
                                        }else if((key($athletics)??null)==='s'){
                                            echo '<li><input placeholder="" type="text" name="newrecord[]" value="아시아U20신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';                        
                                        }else if((key($athletics)??null)==='c'){
                                            echo '<li><input placeholder="" type="text" name="newrecord[]" value="대회신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';
                                        }else{
                                            echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></li>';                                      
                                        }
                                        echo '</ul></div>';
                            }else{
                                echo '<input placeholder="등번호" type="text" name="playerbib[]"
                                class="input_text" value="' . $row['athlete_bib'] . '" maxlength="30" required="" readonly />';
                            }
                            $count++;
                        }
                    ?>
                    </div>
                    <h3 class="UserProfile_tit tit_left_red tit_padding">경기 비고</h3>
                    <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="note_text"
                        value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                    <div class="modify_Btn input_Btn result_Btn">
                        <button class="BTN_Red" type="submit">순서 재 정렬</button>
                        <button type="submit" class="BTN_Blue" name="addresult">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/js/main.js?ver=6"></script>
</body>

</html>