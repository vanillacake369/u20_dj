<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";
require_once 'action/module/schedule_worldrecord.php';

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
$num = 0;
$count = 0;
//@Potatoeunbi
//대분류의 소분류들 record, athlete, schedule 정보들을 모두 가져오도록 하는 sql문
$sql = "SELECT *,if(r.record_status='o',r.record_official_result,record_live_result) as result,if(r.record_status='o',r.record_official_record,record_live_record) as record from list_record AS r
JOIN list_schedule AS s on r.record_sports=s.schedule_sports AND r.record_gender=s.schedule_gender AND r.record_round=s.schedule_round AND if(r.record_status ='n', r.record_trial='1',if(r.record_status='o',r.record_official_result>0,record_live_result>0))
JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id AND r.record_sports='$sports' AND r.record_gender='$gender' AND r.record_round='$round'
ORDER BY if(r.record_status='n',record_order,result);";

$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$schedule_sports = $row['schedule_sports'];
$schedule_result = $row['record_status'];
$schedule_round = $row['schedule_round'];
$schedule_name = $row['schedule_name'];
$total_count = mysqli_num_rows($result);
if (empty($total_count)) {
    echo "<script>alert('세부 경기 일정이 없습니다.'); location.href='./sport_schedulemanagement.php';</script>";
}
?>

<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
</head>

<body>
    <div class="schedule_container">
        <div class="result_tit">
            <div class="result_list2">
                <p class="tit_left_blue"><?php echo $schedule_sports?>
                    <?php echo $schedule_round=='final'?'결승전':($schedule_round=='semi-final'?'준결승전':'예선전')?>
                </p>
            </div>
            <div class="result_list">
                <?php echo '<p class="defaultBtn';
            echo $schedule_result=='o'?' BTN_DarkBlue">마감중</p>':($schedule_result=='l'?'
                BTN_Blue">진행중</p>':' BTN_yellow ">대기중</p>');?>
            </div>
        </div>
        <form action="" method="post">
            <input name="round" value="<?php echo $schedule_round?>" hidden>
            <input name="sports" value="<?php echo $schedule_sports?>" hidden>
            <input name="gender" value="<?php echo $gender?>" hidden>
            <input name="group" value="<?php echo $row["record_group"] ?>" hidden>
            <input name="name" value="<?php echo $schedule_name?>" hidden>
            <input name="result" value="<?php echo $schedule_result?>" hidden>
            <div class="schedule schedule_flex">
                <div class="schedule_filed filed_list_item">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow">1조</p>
                        <?php echo '<span class="defaultBtn';
                    echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>');
                    ?>
                    </div>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 15%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 10%" />
                        </colgroup>
                        <thead class="result_table De_tbody entry_table">
                            <tr>
                                <?php
                        echo "<th rowspan='2'>순서</th>";
                        echo "<th rowspan='2'>등수</th>";
                        echo "<th rowspan='2'>BIB</th>";
                        echo "<th rowspan='2'>이름</th>";
                        echo "<th rowspan=";
                        //@Potatoeunbi
                        //jump() 함수는 풍속이 있는 경기, 없는 경기에 따른 rowspan을 주기 위해서이다.
                        jump($schedule_sports);
                        echo ">1차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">2차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">3차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">4차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">5차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">6차 시기</th>";
                        echo "<th rowspan=";
                        jump($schedule_sports);
                        echo ">기록</th>";
                        echo "<th>비고</th>";
                        echo "</tr>";
                        if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                            echo "<tr>";
                            echo  "<th colspan='7'>풍속</th>";
                        }
                        echo "<th> 신기록</th>";
                        echo "</tr>";
                        ?>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table">
                            <?php

                    $i = 1;
                    $k = 1;
                    $j = 0;
                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    if ($row['record_group'] != $k) {
                    ?>
                        </tbody>
                    </table>
                    <div class="filed_BTN">
                        <div>
                            <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                formaction="electronic_display<? echo $schedule_result=='o' ? '_official' : '';?>.php">전광판
                                보기</button>
                            <? if ($schedule_result=='o'){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <?}?>
                            <? if ($sports=='longjump' || $sports=='triplejump'){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_horizontal_result_pdf.php">PDF(한) 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_horizontal_result_eng_pdf.php">PDF(영) 출력</button>
                            <button type="submit" formaction="/action/record/result_execute_track_field_excel.php"
                                class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/field_horizontal_result_word.php">워드 출력</button>
                            <?}else if ($sports=='longjump' || $sports=='triplejump'){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_normal_result_pdf.php">PDF(한) 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_normal_result_eng_pdf.php">PDF(영) 출력</button>
                            <button type="submit" formaction="/action/record/result_execute_track_field_excel.php"
                                class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/field_normal_result_word.php">워드 출력</button>
                            <?}?>
                        </div>
                    </div>
                    <div>
                        <?php
                        // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                        if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                            echo '<button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="';
                            if ($schedule_sports == "polevault" || $schedule_sports == "highjump") {
                                echo "/record/field_vertical_result_view.php";
                            } else if ($schedule_sports == "longjump" || $schedule_sports == "triplejump") {
                                echo "/record/field_horizontal_result_view.php";
                            } else {
                                echo "/record/field_normal_result_view.php";
                            }
                            echo '">기록 입력</button>';
                            echo '<input type="submit" formaction="';
                                echo './record_change_type.php"';
                                echo 'class="defaultBtn BIG_btn BTN_green filedBTN" value="기록 전환">';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </form>
        <?php
    $k++;
    if ($total_count != $j){
    $count=0;
    ?>
        <form action="" method="post">
            <input name="round" value="<?php echo $schedule_round?>" hidden>
            <input name="sports" value="<?php echo $schedule_sports?>" hidden>
            <input name="gender" value="<?php echo $gender?>" hidden>
            <input name="group" value="<?php echo $row['record_group']?>" hidden>
            <input name="name" value="<?php echo $schedule_name?>" hidden>
            <input name="result" value="<?php echo $schedule_result?>" hidden>
            <!-- <input name="weight" value="<?php echo $record_weight?>" hidden> -->
            <div class="schedule schedule_flex">
                <div class="schedule_filed filed_list_item">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_yellow"><?php echo $k?>조</p>
                        <?php echo '<span class="defaultBtn';
                        echo $schedule_result=='o'?' BTN_green">Official Result</span>':($schedule_result=='l'?' BTN_yellow">Live Result</span>':' BTN_green">Start List</span>'); ?>
                    </div>
                    <table class="box_table">
                        <colgroup>
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 4%" />
                            <col style="width: 15%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 7%" />
                            <col style="width: 10%" />
                        </colgroup>
                        <thead class="result_table De_tbody entry_table">
                            <tr>
                                <?php
                    echo "<th rowspan='2'>순서</th>";
                    echo "<th rowspan='2'>등수</th>";
                    echo "<th rowspan='2'>BIB</th>";
                    echo "<th rowspan='2'>이름</th>";
                    echo "<th rowspan=";
                    //@Potatoeunbi
                    //jump() 함수는 풍속이 있는 경기, 없는 경기에 따른 rowspan을 주기 위해서이다.
                    jump($schedule_sports);
                    echo ">1차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">2차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">3차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">4차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">5차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">6차 시기</th>";
                    echo "<th rowspan=";
                    jump($schedule_sports);
                    echo ">기록</th>";
                    echo "<th>비고</th>";
                    echo "</tr>";
                    if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                        echo "<tr>";
                        echo  "<th colspan='7'>풍속</th>";
                    }
                    echo "<th> 신기록</th>";
                    echo "</tr>";
                    ?>
                            </tr>
                            <tr class="filed2_bottom">
                            </tr>
                        </thead>
                        <tbody class="table_tbody De_tbody entry_table">
                            <?php }
                }
                if ($row['record_group'] == $k) {
                $num++;
                echo "<tr";
                if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                echo "<td rowspan='2'><input type='number' name='rain[]' value='" .
                    ($row['record_order'] ?? null) .
                    "' min='1' max='12' required='' readonly /></td>";
                echo "<td rowspan='2'><input placeholder='등수' type='number' name='rank[]' id='rank' value='" .
                    ($row['result'] ?? null) .
                    "' min='1' max='12' required=''  readonly/></td>";

                echo "<td rowspan='2'><input placeholder='BIB' type='text' name='playerbib[]' value='" .
                    $row["athlete_bib"] .
                    "' maxlength='30' required='' readonly /></td>";
                echo "<td rowspan='2'><input placeholder='선수 이름' type='text' name='playername[]' value='" .
                    $row["athlete_name"] .
                    "' maxlength='30' required='' readonly /></td>";
                $answer = $db->query(
                    "SELECT record_live_record,record_wind,record_new FROM list_record 
                                        INNER JOIN list_athlete ON record_athlete_id='".$row['athlete_id']."' 
                                        AND athlete_id= record_athlete_id and record_sports='$sports' 
                                        and record_round='$round' and record_gender='$gender' and record_group=".$row['record_group']." ORDER BY record_trial ASC"
                );
                while ($id = mysqli_fetch_array($answer)) {
                    echo "<td rowspan=";
                    jump($schedule_sports);
                    echo ">";
                    echo '<input placeholder="경기 결과" type="text" name="gameresult' .
                        $i . '[]" value="' . ($id["record_live_record"] ?? null) .  '"
                                    maxlength="5" onkeyup="field1Format(this)" readonly />';
                    echo "</td>";
                    if ($i == 6)
                        $i = 1;
                    else
                        $i++;
                }
                echo "<td rowspan=";
                jump($schedule_sports);
                echo ">";
                echo '<input placeholder="경기 결과" id="result" type="text" name="gameresult[]"
                                value="' . ($row["record"] ?? null) . '" maxlength="5" required="" onkeyup="field1Format(this)" readonly/>';
                echo "</td>";
                echo '<td><input type="text" placeholder ="비고"name="bigo[]" value="' .
                    ($row["memo"] ?? null) .
                    '" maxlength="100"  readonly/></td>';
                echo "<tr";
                if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";

                if ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') {
                    for ($t = 0; $t <= 6; $t++) {
                        $wind = $db->query("SELECT record_wind FROM list_record
                                    INNER JOIN list_athlete ON record_athlete_id=" .
                            $row["athlete_id"] .
                            " AND athlete_id= record_athlete_id
                                    and record_sports='$sports' 
                                        and record_round='$round' and record_gender='$gender' and record_group=".$row['record_group']."
                                    ORDER BY record_live_record ASC limit 6 ");
                        $windrow = mysqli_fetch_array($wind);
                        if ($t % 7 == 6) {
                            echo "<td>";
                            echo '<input placeholder="풍속" type="text" name="lastwind[]" class="input_text" value="' .
                                ($row["wind"] ?? null) .
                                '"
                                                maxlength="5" required="" onkeyup="windFormat(this)" readonly/>';
                            echo "</td>";
                        } else {
                            echo "<td>";
                            echo '<input placeholder="풍속" type="text" name="wind' .
                                ($t + 1) .
                                '[]" value="' .
                                ($windrow["record_wind"] ?? null) .
                                '"
                                    maxlength="5" required="" onkeyup="windFormat(this)" readonly />';
                            echo "</td>";
                        }
                    }
                }

                //@Potatoeunbi
                //include_once(__DIR__ . '/action/module/schedule_worldrecord.php');에 들어있는 함수.
                //신기록 출력하는 함수, @gwonsan 학생 신기록 출력 방식 그대로임.
                world($db, $row['athlete_name'], $row['record_new'], $schedule_sports, ($row["record"] ?? null));

                echo "</tr>";
                echo "</tr>";
                $count++;
                $j++;
                if ($j == $total_count) { ?>
                        </tbody>
                    </table>
                    <div class="filed_BTN">
                        <div>
                            <button type="submit" class="defaultBtn BIG_btn BTN_DarkBlue filedBTN"
                                formaction="electronic_display<? echo $schedule_result=='o' ? '_official' : '';?>.php">전광판
                                보기</button>
                            <? if ($schedule_result=='o'){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_purple filedBTN"
                                formaction="award_ceremony.php">시상식 보기</button>
                            <?}?>
                            <? if ($sports=='longjump' || $sports=='triplejump'){?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_horizontal_result_pdf.php">PDF(한) 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_horizontal_result_eng_pdf.php">PDF(영) 출력</button>
                            <button type="submit" formaction="/action/record/result_execute_track_field_excel.php"
                                class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/field_horizontal_result_word.php">워드 출력</button>
                            <?}else{?>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_normal_result_pdf.php">PDF(한) 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Red filedBTN"
                                formaction="/record/field_normal_result_eng_pdf.php">PDF(영) 출력</button>
                            <button type="submit" formaction="/action/record/result_execute_track_field_excel.php"
                                class="defaultBtn BIG_btn excel_Print filedBTN">엑셀 출력</button>
                            <button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                formaction="/record/field_normal_result_word.php">워드 출력</button>
                            <?}?>
                        </div>
                        <div>
                            <?php
                    // 수정 권한, 생성 권한 둘 다 있는 경우에만 접근 가능
                    if (authCheck($db, "authSchedulesUpdate") && authCheck($db, "authSchedulesCreate")) {
                        echo '<button type="submit" class="defaultBtn BIG_btn BTN_Blue filedBTN" formaction="';
                        if ($schedule_sports == "polevault" || $schedule_sports == "highjump") {
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
                    }?>
                            <?php      }
                    }
                    }?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <button type="button" class="changePwBtn defaultBtn">확인</button>
</body>

<script src="assets/js/main.js?ver=10"></script>

</html>