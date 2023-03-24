<?php
//멀리뛰기,삼단뛰기 경기용
require_once __DIR__ . "/../module/record_worldrecord.php";
require_once __DIR__ . "/../../includes/auth/config.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST["playername"];
// $tempstore = $_POST['tempstore'];
$round=$_POST['round'];
$gender = $_POST['gender'];
$name=$_POST['sports'];
$heat = $_POST['group'];
$medal = 0;
$result = $_POST["rank"];
$record = $_POST["gameresult"];
$memo = $_POST["bigo"];
$rane = $_POST["rain"];
$comprecord = $_POST['compresult'];
$judge_id = $_POST['refereename'];
$judgeresult = $db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
$judge = mysqli_fetch_array($judgeresult);
$res1 = $db->query("SELECT * FROM list_schedule 
    join list_record
    where record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round");
$row1 = mysqli_fetch_array($res1);
$athletics = [];
$worldrecord = [];
$trial_count = $_POST["count"];

$windrecord = [$_POST["wind1"] ?? null,
  $_POST["wind2"] ?? null,
  $_POST["wind3"] ?? null,
  $_POST["wind4"] ?? null,
  $_POST["wind5"] ?? null,
  $_POST["wind6"] ?? null,];
$fieldrecord = [$_POST["gameresult1"], $_POST["gameresult2"], $_POST["gameresult3"], $_POST["gameresult4"], $_POST["gameresult5"], $_POST["gameresult6"]];
for ($j = 0; $j < $trial_count; $j++) {
    for ($i = 0; $i < count($athlete_name); $i++) {
        $re = $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");
    $row = mysqli_fetch_array($re);
    if ($j < 6) {
      if ($fieldrecord[$j][$i] == "X") {
        $pass = "d";
      } elseif ($fieldrecord[$j][$i] == "-") {
        $pass = "w";
      } else {
        $pass = "p";
      }
    }
      $k = $j + 1;
      $ruf = $fieldrecord[$j][$i];
      $win = $windrecord[$j][$i] ?? null;
      $savequery =
        "UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',
                record_live_record='$ruf', record_medal=" .
        $medal .
        ",record_memo='$memo[$i]',record_wind='$win'
                WHERE record_athlete_id ='" .
        $row["athlete_id"] .
        "' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_trial='$k'";
    mysqli_query($db, $savequery);
  }
}