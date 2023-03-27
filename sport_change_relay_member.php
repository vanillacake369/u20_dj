<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";

$country = cleanInput($_GET['country']);
$schedule = cleanInput($_GET['schedule']);
$record = cleanInput($_GET['record']);
$sport = $_GET['sport'];


$select = "record_id,record_order,athlete_country";
$athleteArr = array();

//@Potatoeunbi
//바꿀 국가들 출력
$sql = "SELECT distinct $select , s.schedule_group,s.schedule_sports from list_record AS r 
        JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
        JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id AND a.athlete_country!='" . $country . "' 
        WHERE record_schedule_id IN 
        (SELECT s1.schedule_id FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON 
        (s2.schedule_id=$schedule and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round=s2.schedule_round ) 
        WHERE s1.schedule_division='s') 
        group by record_order, athlete_country ORDER BY s.schedule_group ASC, r.record_order ASC;";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$sports_code = $row['schedule_sports'];


//@Potatoeunbi
//바꿀 선수들 출력
$athlete_sql = "SELECT athlete_id, athlete_name, FIND_IN_SET( '" . $sport . "' ,athlete_schedule) AS checking FROM list_athlete WHERE athlete_country='" . $country . "'  having checking>0;";
$athlete_result = $db->query($athlete_sql);
while ($athlete_row = mysqli_fetch_array($athlete_result)) {
    $athleteArr[] = $athlete_row;
}

?>
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
<script src="/assets/js/radion_btn.js"></script>
</head>

<body>
    <div class="container">
        <div class="schedule">
            <div class="schedule_filed">
                <div class="schedule_filed_tit">
                    <p class="tit_left_blue">바꿀 대상의 국가</p>
                </div>
                <div class="schedule_change_members">
                    <form action="action/sport/schedule_member_change_relay.php" method="post">
                        <h3>선택</h3>
                        <input class="checkbox1" type="radio" name="contact" checked value="국가"><label for="">국가</label>
                        <input class="checkbox1" type="radio" name="contact" value="선수"><label for="">선수</label>

                        <input type="hidden" name="current_country" value="<?php echo $record ?>">
                        <input type="hidden" name="country" value="<?php echo $country ?>">
                        <input type="hidden" name="schedule" value="<?php echo $schedule ?>">
                        <select class="d_select" name="change_athlete">
                            <option value="non" hidden="">변경할 국가 선택</option>
                            <?php
                            $result = $db->query($sql);
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<option value=" . $row['record_id'] . ">" . '[' . $row['athlete_country'] . '] ' . ($sport != 'field' ? $row['schedule_group'] . '조 ' : '') . $row['record_order'] . '레인 ' . "</option>";
                            }
                            ?>
                        </select>
                        <div id="house">
                            <div class="someting" style="padding: 60px 60px 60px 60px; height: 150px;">
                                <h3>1번 주자</h3>
                                <select class="input_text" name="athlete[]" style="   background-color: var(   --color-sky)">
                                    <option value="non" hidden="">1번 주자 선택</option>
                                    <?php
                                    for ($k = 0; $k < count($athleteArr); $k++) { ?>
                                        <option value="<?= $athleteArr[$k]['athlete_id'] ?>">
                                            [<?= $country ?>]<?= $athleteArr[$k]['athlete_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="someting" style="padding: 60px 60px 60px 60px; height: 150px;">
                                <h3>2번 주자</h3>
                                <select class="input_text" name="athlete[]" style="   background-color: var(   --color-sky)">
                                    <option value="non" hidden="">2번 주자 선택</option>
                                    <?php
                                    for ($k = 0; $k < count($athleteArr); $k++) { ?>
                                        <option value="<?= $athleteArr[$k]['athlete_id'] ?>">
                                            [<?= $country ?>]<?= $athleteArr[$k]['athlete_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="someting" style="padding: 60px 60px 60px 60px; height: 150px;">
                                <h3>3번 주자</h3>
                                <select class="input_text" name="athlete[]" style="   background-color: var(   --color-sky)">
                                    <option value="non" hidden="">3번 주자 선택</option>
                                    <?php
                                    for ($k = 0; $k < count($athleteArr); $k++) { ?>
                                        <option value="<?= $athleteArr[$k]['athlete_id'] ?>">
                                            [<?= $country ?>]<?= $athleteArr[$k]['athlete_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="someting" style="padding: 60px 60px 60px 60px; height: 150px;">
                                <h3>4번 주자</h3>
                                <select class="input_text" name="athlete[]" style="   background-color: var(   --color-sky)">
                                    <option value="non" hidden="">4번 주자 선택</option>
                                    <?php
                                    for ($k = 0; $k < count($athleteArr); $k++) { ?>
                                        <option value="<?= $athleteArr[$k]['athlete_id'] ?>">
                                            [<?= $country ?>]<?= $athleteArr[$k]['athlete_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="Participant_Btn">
                            <button type="submit" class="changePwBtn defaultBtn">확인</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <script src="assets/js/main.js"></script>
</body>

</html>