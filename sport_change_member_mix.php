<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";

$athlete = cleanInput($_GET['athlete']);
$code = cleanInput($_GET['code']);
$sport = cleanInput($_GET['sport']);
$round = cleanInput($_GET['round']);


//@Potatoeunbi
//바꿀 선수들 출력
$select = "distinct record_order,athlete_name,athlete_id,athlete_country";
$sql = "SELECT $select FROM list_record 
        INNER JOIN list_athlete ON athlete_id = record_athlete_id 
        INNER JOIN list_schedule ON schedule_id= record_schedule_id 
        WHERE schedule_sports = '" . $code . "' and schedule_round= '" . $round . "' and athlete_id!= '" . $athlete . "' ORDER BY record_order ASC;";
?>
<!--Data Tables-->
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
          <form action="action/sport/schedule_member_change_mix.php" method="post">
            <input type="hidden" name="schedule_sports" value="<?php echo $code ?>">
            <input type="hidden" name="schedule_round" value="<?php echo $round ?>">
            <input type="hidden" name="current_athlete" value="<?php echo $athlete ?>">
            <select class="d_select" name="change_athlete">
              <option value="non" hidden="">변경할 선수 선택</option>
              <?php
              $result = $db->query($sql);
              while ($row = mysqli_fetch_array($result)) {
                echo "<option value=" . $row['athlete_id'] . ">" . '[' . $row['athlete_country'] . '] '  . $row['record_order'] . '레인 ' . $row['athlete_name'] . "</option>";
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