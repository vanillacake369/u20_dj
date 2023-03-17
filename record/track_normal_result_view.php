<?php
    $id=$_GET['id'];
    require_once __DIR__. "/../head.php";
    require_once __DIR__ . "/../action/module/record_worldrecord.php";
    require_once __DIR__ . "/../includes/auth/config.php";//B:데이터베이스 연결 
    $sql= "SELECT DISTINCT * FROM list_record  INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id'";
    $result=$db->query($sql);
    $rows = mysqli_fetch_assoc($result);

    $judgesql="select distinct judge_name from list_judge  join list_record ON  record_judge = judge_id INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id'";
    $judgeresult=$db->query($judgesql);
    $judgerow=mysqli_fetch_array($judgeresult);
    $longname=['1500m','3000m','3000mSC','5000m','10000m','racewalk'];
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
<script type="text/javascript" src="../action/record/result_track_single_execute_excel.js"></script>
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
        let check = document.getElementsByTagName('th')[1].textContent;
        let val = ddd[0].split(" ")[1];
        wind.value = val;
        for (i = 1; i < ddd.length; i++) {
            let k = ddd[i].split(" ")
            let on;
            if (!document.querySelector("#id" + k[1])) {
                if (!k[1]) continue;
                on = document.querySelector("#" + k[1]).children
            } else {
                if (!k[1]) continue;
                on = document.querySelector("#id" + k[1]).children
            }
            if (k[2]) {
                on['gamepass[]'].value = 'p'
                on[4].firstElementChild.value = k[2]
            } else if (k[0] == 'DNS') {
                on['gamepass[]'].value = 'n'
                on[4].firstElementChild.value = 0
                on[6].firstElementChild.value = k[0]
            } else if (k[0] == 'DNF') {
                on['gamepass[]'].value = 'n'
                on[4].firstElementChild.value = 0
                on[6].firstElementChild.value = k[0]
            } else {
                on['gamepass[]'].value = 'd'
                on[4].firstElementChild.value = 0
                on[6].firstElementChild.value = 'DQ'
            }
            if (k[3]) {
                on[5].firstElementChild.value = k[3]
            } else {
                on[5].firstElementChild.value = '';
            }
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
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    <?=$rows['schedule_name']?>
                </p>
                <form action="">
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
                                    <span>풍속</span>
                                    <?php
                                    if($rows['schedule_status']==='y'){
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
                    <?/*
                    if($rows['schedule_status']!='y'){
                        echo '<input type="button" onclick="openTextFile()" class="btn_add bold" value="자동 입력"
                        style="margin-right:15px;">';
                }else{
                      echo' <div class="btn_base base_mar col_left">
                      
                        <input type="button" onclick="execute_excel()" class="btn_excel bold" value="엑셀 출력" />
                    </div>
                    <button type="submit" class="btn_add bold" formaction="track_normal_result_pdf.php"><span>PDF 출력</span></button>
                    <button type="submit" class="btn_add bold" formaction="track_normal_result_word.php"><span>워드 출력</span></button>';
                    }
                    */?>
                    <div class="Thorw_result">
                        <div class="relay_result ">
                            <h1 class="relay_tit tit_left_green">결과</h1>
                            <div class="BTNform BTNform2">
                                <?
                                if($rows['schedule_status']!='y'){
                                    echo '<input type="button" onclick="openTextFile()" class="defaultBtn BTN_green BIG_btn" value="자동 입력">';
                                }else{
                                    echo' <div class="btn_base base_mar col_left">
                                        <input type="button" onclick="execute_excel()" class="defaultBtn BIG_btn excel_Print filedBTN" value="엑셀 출력" />
                                    </div>
                                    <button type="submit" class="defaultBtn BIG_btn BTN_Red2 filedBTN" formaction="track_normal_result_pdf.php"><span>PDF 출력</span></button>
                                    <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="track_normal_result_word.php"><span>워드 출력</span></button>';
                                    }
                            ?>
                            </div>
                        </div>
                        <div class="relay_list">
                            <ul>
                                <li>
                                    <p>등수</p>
                                </li>
                                <?
                                if(in_array($rows['schedule_sports'],$longname)){                                            
                                }else{
                                    echo '<li><p>레인</p></li>';                       
                                }
                                ?>
                                <li>
                                    <p>등 번호</p>
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
                                </li>
                            </ul>
                        </div>
                        <?
                        $num = 0;
                        $relm='record_'.$result_type.'_result,record_'.$result_type.'_record,record_pass,record_memo,record_new,record_reaction_time,athlete_name,athlete_bib, record_order,athlete_country';
                        if($rows['schedule_status']=='y'){
                            $order='record_'.$result_type.'_result';
                        }else if(in_array($rows['schedule_sports'],$longname)){
                            $order='athlete_bib';
                        }else{
                            $order='record_order';                
                        }
                        $sql="SELECT ".$relm." FROM list_record 
                        INNER JOIN list_athlete ON list_athlete.athlete_id = list_record.record_athlete_id 
                        INNER JOIN list_schedule ON list_schedule.schedule_id= list_record.record_schedule_id 
                        AND list_schedule.schedule_id = '$id'
                        ORDER BY ".$order." ASC ";
                        $count=0;
                        $result=$db->query($sql); 
                            while($row = mysqli_fetch_array($result)){
                                $num++;
                                echo '<div class="relay_item"><ul';
                                if ($num%2 == 1) echo ' class="Ranklist_Background">'; else echo ">";
                                if(in_array($rows['schedule_sports'],$longname)){                      
                                    echo '<span id="id'.$row['athlete_bib'].'" style="display:none"></span>';                       
                                }else{
                                    echo '<li id="rane'.$row['record_order'].'" style="display:none"></li>';                       
                                }
                                echo '<li><input type="number" name="rank[]" id="rank" value="'.$row['record_'.$result_type.'_result'].'" min="1" required="" /></li>';
                                if(in_array($rows['schedule_sports'],$longname)){ 
                                }else{    
                                    echo '<li><input type="number" name="rain[]"  value="'.$row['record_order'].'" min="1" max="12" required="" readonly /></li>';
                                }
                                echo '<li><input placeholder="등번호" type="text" name="playerbib[]" 
                                 value="'.$row['athlete_bib'].'" maxlength="30" required="" readonly /></li>';
                                echo '<li><input placeholder="선수 이름" type="text" name="playername[]" 
                                value="'.$row['athlete_name'].'" maxlength="30" required="" readonly /></li>';
                                echo '<li><input placeholder="국가" type="text" name="country" 
                                 value="'.$row['athlete_country'].'" maxlength="30" required="" readonly /></li>';
                                echo '<li><input placeholder="경기 결과를 입력해주세요" type="text" name="gameresult[]" id="result" value="'.$row['record_'.$result_type.'_record'].'" maxlength="8"
                                required="" onkeyup="trackFinal(this)" />';
                                echo '<li><input placeholder="Reaction Time" type="text" name="reactiontime[]" id="reactiontime" value="'.$row['record_reaction_time'].'" maxlength="8"
                                required="" onkeyup="trackFinal(this)" />';
                                echo '<input type="hidden" name="compresult[]" value="'.($row['record_'.$result_type.'_record']??null).'"/></li>';
                                echo '<li><input placeholder="비고를 입력해주세요" type="text" name="bigo[]" value="'.($row['record_memo'] ? $row['record_memo']:null).'" maxlength="100" /></li>';
                                if($rows['schedule_name'] ==='Decathlon' || $rows['schedule_name'] ==='Heptathlon'){
                                    $sport_code=$rows['schedule_sports']."(".$rows['schedule_round'].")";                                  
                                }else{
                                    $sport_code=$rows['schedule_sports'];
                                }
                                if($row['record_new'] =='y'){
                                    if($rows['schedule_status'] !='y'){
                                        $time=$rows['schedule_start'];
                                    }else{
                                        $time=$rows['schedule_end'];
                                    }
                                    $athletics=check_my_record($row['athlete_name'],$sport_code,$time);
                                    if((key($athletics)??null)==='w'){
                                        echo '<li><input placeholder="선택"  type="text" name="newrecord[]" value="세계신기록';
                                        echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';
                                    }else if((key($athletics)??null)==='u'){
                                        echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="세계U20신기록';
                                        echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';   
                                    }else if((key($athletics)??null)==='a'){
                                        echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="아시아신기록';
                                        echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';                       
                                    }else if((key($athletics)??null)==='s'){
                                        echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="아시아U20신기록';
                                        echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';                        
                                    }else if((key($athletics)??null)==='c'){
                                        echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="대회신기록';
                                        echo '" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';
                                    }else{
                                        echo '<li><input placeholder="선택" type="text" name="newrecord[]" value="" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';                                      
                                    }
                                }else{
                                    echo '<li><input placeholder="선택" type="text" name="newrecord[]"  value="" maxlength="100" ath="'.$row['athlete_name'].'" sports='.$sport_code.' schedule_id="'.$id.'" record="'.$row['record_'.$result_type.'_record'].'" readonly/></li>';
                                }
                                echo '<input placeholder="경기 통과 여부" type="hidden" name="gamepass[]" value="'.$row['record_pass'].'" maxlength="50" required="" />';
                                $count++;
                                echo '</ul></div>';
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
    <script src="/assets/js/main.js?ver=7"></script>
</body>

</html>