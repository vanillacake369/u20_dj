<?php
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
    <!-- contents 본문 내용 -->
    <div class="container">
        <!-- class="contents something" -->
        <div class="something" style="padding: 100px 15px 60px 15px">
            <form action="../action/record/track_relay_result_insert.php" method="post" class="form">
                <h3 style="width:45%; display:inline-block; margin-right: 4.6%;">경기 이름</h3>
                <h3 style="width:48%; display:inline-block;">라운드</h3>
                <div class="input_row" style="width:45%; margin-right: 4.6%;">
                    <input placeholder="경기 이름" type="text" name="gamename" class="input_text"
                        value="<?=$rows['schedule_name']?>" maxlength="16" required="" readonly />
                </div>
                <div class="input_row" style="width:48%;">
                    <?php
                    echo '<input placeholder="라운드" type="text" name="round" class="input_text" value="'.$rows['schedule_round'].'"
                    maxlength="16" required="" readonly />';
                    ?>
                </div>
                <h3 style="width:45%; display:inline-block; margin-right: 4.6%;">심판 이름</h3>
                <h3 style="width:48%;  display:inline-block;">풍속</h3>
                <div class="input_row" style="width:45%; margin-right: 4.6%;">
                    <input placeholder="심판 이름" type="text" name="refereename" class="input_text"
                        value="<?=($judgerow[0]??$_SESSION['Judge_name'])?>" maxlength="30" required="" readonly />
                    <input type="hidden" name="schedule_id" value="<?=$id?>">
                </div>
                <div class=" input_row" style="width:48%;">
                    <?php
                    if($rows['schedule_status']==='y'){
                        
                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" class="input_text" value="'.$rows['record_wind'].'" maxlength="16"
                            required="" />';
                    }else{
                        
                        echo '<input placeholder="풍속을 입력해주세요." type="text" name="wind" class="input_text" value="" maxlength="16"
                            required="" />';
                        }
                    ?>
                </div>
                <div class="btn_base base_mar" style="display:inline-flex; align-items: baseline;">
                    <h2 style="margin-bottom: 10px; float:left; margin-right: 30px;">결과</h2>
                    <?php
                    if($rows['schedule_status']!='y'){
                        echo '<input type="button" onclick="openTextFile()" class="btn_add bold" value="자동 입력"
                        style="margin-right:15px;">';
                    }else{
                        echo' <div class="btn_base base_mar col_left">
                            <input type="button" onclick="execute_excel()" class="btn_excel bold" value="엑셀 출력" />
                        </div>
                        <button type="submit" class="btn_add bold" formaction="track_relay_result_pdf.php"><span>PDF 출력</span></button>
                        <button type="submit" class="btn_add bold" formaction="track_relay_result_word.php"><span>워드 출력</span></button>';
                        }
                    ?>
                </div>
                <table cellspacing="0" cellpadding="0" class="team_table" style="border-top: 0px">
                    <colgroup>
                        <col style="width: 7%" />
                        <col style="width: 7%" />
                        <col style="width: 26%" />
                        <col style="width: 7%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 20%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="background: none">등수</th>
                            <th style="background: none">레인</th>
                            <th style="background: none">이름</th>
                            <th style="background: none">국가</th>
                            <th style="background: none">통과 여부</th>
                            <th style="background: none">경기 결과</th>
                            <th style="background: none">비고</th>
                            <th style="background: none">신기록</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count=0;
                        $relm='athlete_country, athlete_bib, record_'.$result_type.'_result,record_'.$result_type.'_record,record_pass,record_memo,record_new,athlete_name,record_team_order, record_order';
                        if($rows['schedule_status']=='y'){
                            $order='record_'.$result_type.'_result';
                        }else{
                            $order='record_order';
                        }
                        $sql="SELECT  ".$relm." FROM list_record 
                        INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                        INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$id' 
                        ORDER BY ".$order." ASC,record_team_order ASC ";
                        $result=$db->query($sql); 
                        $count=0;
                        $athrecord=array();
                        while($row = mysqli_fetch_array($result)){
                            $athrecord[$count%4]=$row['record_'.$result_type.'_record'];
                            if($count%4==0){
                                echo '<tr id="rane'.$row['record_order'].'">';
                                echo '<td><input type="number" name="rank[]" id="rank" class="input_text" value="'.$row['record_'.$result_type.'_result'].'" min="1" max="12" required="" /></td>';
                                echo '<td><input type="number" name="rain[]" class="input_text" value="'.$row['record_order'].'" min="1" max="12" required="" readonly /></td>';
                                echo '<td>';
                            }
                            if($count%4==3){
                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                class="input_text" value="'.$row['athlete_name'].'" maxlength="30" required="" readonly/></td>';
                                // echo '<input type="hidden" placeholder="선수 번호" type="text" name="playernumber[]" value="'.$row['athlete_bib'].'">';
                                echo '<td><input placeholder="소속" type="text" name="division" class="input_text" value="'.$row['athlete_country'].'"maxlength="50" required="" readonly/></td>';
                                echo '<td><input placeholder="경기 통과 여부" type="text" name="gamepass[]" class="input_text" value="'.$row['record_pass'].'" maxlength="1" required="" /></td>';
                                echo '<td>
                            <input placeholder="경기 결과" type="text" name="gameresult[]" id="result" class="input_text"
                                value="'.($athrecord[3] ?$athrecord[3]:0 ).'" maxlength="8" required="" onkeyup="trackFinal(this)"
                                    style="float: left; width: 80px; padding-right: 5px" />
                                </div>
                                </div></td>';
                                echo '<input type="hidden" name="compresult[]" value="'.($athrecord[3] ?$athrecord[3]:0 ).'"/>';
                                echo '<td><input placeholder="비고" type="text" name="bigo[]" class="input_text" value="'.($row['record_memo'] ? $row['record_memo']:'&nbsp').'" maxlength="100" /></td>';
                                        $sport_code=$rows['schedule_sports'];
                                        if($rows['schedule_status'] !='y'){
                                            $time=$rows['schedule_start'];
                                        }else{
                                            $time=$rows['schedule_end'];
                                        }
                                        $athletics=check_my_record($row['athlete_country'],$sport_code,$time);
                                        if((key($athletics)??null)==='w'){
                                            echo '<td><input placeholder=""  type="text" name="newrecord[]" class="input_text" value="세계신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';
                                        }else if((key($athletics)??null)==='u'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="세계U20신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';   
                                        }else if((key($athletics)??null)==='a'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';                       
                                        }else if((key($athletics)??null)==='s'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="아시아U20신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';                        
                                        }else if((key($athletics)??null)==='c'){
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="대회신기록';
                                            echo '" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';
                                        }else{
                                            echo '<td><input placeholder="" type="text" name="newrecord[]" class="input_text" value="" maxlength="100" ath="'.$row['athlete_country'].'" sports='.$rows['schedule_sports'].' schedule_id="'.$id.'" record="'.$athrecord[3].'" readonly/></td>';                                      
                                        }
                            }else{
                                echo '<input placeholder="선수 이름" type="text" name="playername[]"
                                class="input_text" value="'.$row['athlete_name'].'" maxlength="30" required="" readonly style="margin-bottom: 10px;"/>';
                            }
                            $count++;
                        }
                    ?>
                    </tbody>
                </table>
                <h3>경기 비고</h3>
                <div class="input_row">
                    <input placeholder="비고를 입력해주세요." type="text" name="bibigo" class="input_text"
                        value="<?=($rows['schedule_memo']??null)?>" maxlength=" 100" />
                </div>
                <div class="signup_submit">
                    <button type="submit" class="btn_login" name="addresult">
                        <span>확인</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>