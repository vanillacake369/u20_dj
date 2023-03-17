<?
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "security/security.php";

    $athlete = cleanInput($_GET['athlete']);
    $schedule = cleanInput($_GET['schedule']);
    $record = cleanInput($_GET['record']);
    $sport = cleanInput($_GET['sport']);


    $select = "record_order,athlete_name,athlete_country,record_id";

    //@Potatoeunbi
    //해당 경기마다 선수 및 국가 출력하는 방식이 다름
    if ($sport == 'field') {
        $sql = "SELECT $select FROM list_record 
        INNER JOIN list_athlete ON athlete_id = record_athlete_id 
        INNER JOIN list_schedule ON schedule_id= record_schedule_id 
        WHERE schedule_id = $schedule and record_trial=1 AND athlete_id!='" . $athlete . "' ORDER BY record_order ASC;";
    } else if ($sport == 'jump') {
        $sql = "SELECT $select FROM list_record 
        INNER JOIN list_athlete ON athlete_id = record_athlete_id 
        INNER JOIN list_schedule ON schedule_id= record_schedule_id 
        WHERE schedule_id = $schedule AND athlete_id!='" . $athlete . "' GROUP BY athlete_id ORDER BY record_order ASC;";
    } else {

        //트랙일 경우(릴레이 제외)
        $sql = "SELECT $select , s.schedule_group,s.schedule_sports from list_record AS r 
        JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
        JOIN list_athlete AS a ON r.record_athlete_id=a.athlete_id AND a.athlete_id!='" . $athlete . "' 
        WHERE record_schedule_id IN 
        (SELECT s1.schedule_id FROM list_schedule AS s1 right OUTER join list_schedule AS s2 ON 
        (s2.schedule_id=$schedule and s1.schedule_sports=s2.schedule_sports AND s1.schedule_name=s2.schedule_name AND s1.schedule_gender=s2.schedule_gender AND s1.schedule_round=s2.schedule_round ) 
        WHERE s1.schedule_division='s') 
        ORDER BY s.schedule_group ASC, r.record_order ASC;";
        $result = $db->query($sql);
        $row = mysqli_fetch_array($result);
        $sports_code = $row['schedule_sports'];
    }
    ?>
    <link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
    <script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/js/useDataTables.js"></script>
</head>

<body>
  <div class="container">
    <div class="schedule">
      <div class="schedule_filed">
        <div class="schedule_filed_tit">
          <p class="tit_left_blue">바꿀 대상의 선수</p>
        </div>
        <div class="schedule_change_members">
        <form action="action/sport/schedule_member_change.php" method="post">
            <input type="hidden" name="current_athlete" value="<?php echo $record ?>">
            <select class="d_select" name="change_athlete">
                <option value="non" hidden="">변경할 선수 선택</option>
                <?php
                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                            echo "<option value=" . $row['record_id'] . ">" . '[' . $row['athlete_country'] . '] ' . ($sport != 'field' && $sport != 'jump' ? $row['schedule_group'] . '조 ' : '') . $row['record_order'] . '레인 ' . $row['athlete_name'] . "</option>";
                        
                    } ?>
            </select>
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