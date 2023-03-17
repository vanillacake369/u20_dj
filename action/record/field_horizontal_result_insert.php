<?php
//멀리뛰기,삼단뛰기 경기용
include __DIR__ . "/../module/record_worldrecord.php";
include __DIR__ . "/../../includes/auth/config.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST["playername"];
$tempstore=$_POST['tempstore'];
$round = $_POST["round"];
$name = $_POST["gamename"];
$medal = 0;
$result = $_POST["rank"];
$record = $_POST["gameresult"];
$memo = $_POST["bigo"];
$rane = $_POST["rain"];
$comprecord=$_POST['compresult'];
$judge_id=$_POST['refereename'];
$s_id=$_POST['schedule_id'];
$judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
$judge=mysqli_fetch_array($judgeresult);
if($name==='Decathlon' || $name ==='Heptathlon'){
  $res1= $db->query("SELECT * FROM list_schedule INNER JOIN list_sports ON sports_code = schedule_round AND  schedule_id='$s_id'"); 
  $row1 = mysqli_fetch_array($res1);
  $sports_code=$row1['sports_code'];
  $totalresult=$db->query("select schedule_id from list_schedule where schedule_name='$name' and schedule_round='final' and schedule_division='s'");
  $totalrow=mysqli_fetch_array($totalresult);
  $trialcnt=4;
  $check_round='y';
}else{
  $res1= $db->query("SELECT * FROM list_schedule WHERE schedule_id='$s_id'"); 
  $row1 = mysqli_fetch_array($res1);
  $sports_code=$row1['schedule_sports'];
  $trialcnt=7;
  $check_round='n';
}

if($row1['schedule_result'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
  $result_type1='official';
  $result_type2='o';
}else{
  $result_type1='live';
  $result_type2='l';
}
$schedule_id = $row1["schedule_id"];
$last_wind = $_POST["lastwind"];
$windrecord = [$_POST["wind1"], $_POST["wind2"], $_POST["wind3"], ($_POST["wind4"]??null), ($_POST["wind5"]??null), ($_POST["wind6"]??null)];
$fieldrecord = [$_POST["gameresult1"], $_POST["gameresult2"], $_POST["gameresult3"], ($_POST["gameresult4"]??null), ($_POST["gameresult5"]??null),($_POST["gameresult6"]??null)];
for ($i = 0; $i < count($athlete_name); $i++) {
    $highrecord=0;
    $medal = 0;
    $highwin=0;
    for ($j = 0; $j < $trialcnt; $j++) {
    $plus='';  
    $new = 'n';
    $re = $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");
    $row = mysqli_fetch_array($re);
    if ($j < $trialcnt-1) {
      if ($fieldrecord[$j][$i] == "X") {
        $pass = "d";
      } elseif ($fieldrecord[$j][$i] == "-") {
        $pass = "w";
      } else {
        $pass = "p";
      }
    }
    if ($j+1 == $trialcnt && $tempstore =='0') {
      if ($round == "final") {
        switch ($result[$i]) {
          case 1:
            $medal = 10000;
            break;
          case 2:
            $medal = 100;
            break;
          case 3:
            $medal = 1;
            break;
          default:
            $medal = 0;
            break;
        }
      }
      if($row1['schedule_status']==='y'){
        $newre=$db->query("select record_new,record_multi_record from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$s_id' AND record_".$result_type1."_result>0");
        $rerow=mysqli_fetch_array($newre);
        $new = $rerow[0];
    }
      if($comprecord[$i] != $highrecord ){ //기존 기록과 변경된 기록이 같은 지 비교
        if($row1['schedule_status']==='y'){ //경기가 끝났는 지 판단
            if($rerow[0]==='y'){
                $arr=modify_worldrecord($athlete_name[$i],$row[1],$highrecord,$highwin,$s_id,$check_round);
                $memo[$i]=change_worldrecord_dec($athlete_name[$i],$row[1],$highrecord,$highwin,$s_id,$check_round,$arr);
            }else{
                $arr2=insert_worldrecord_dec($athlete_name[$i],$row[1],$highrecord,$highwin,$s_id,$check_round); 
                $memo[$i]=$arr2[0];
                $new=$arr2[1];
            }
        }else{
            $arr2=insert_worldrecord_dec($athlete_name[$i],$row[1],$highrecord,$highwin,$s_id,$check_round);
            $memo[$i]=$arr2[0];
            $new=$arr2[1];
        }
      } 
      if($round ==='longjump'){
        if($row1['schedule_gender']==='m'){  
        $point= (int)(0.14354*pow(((float)$highrecord*100-220),1.4)); //longjump
        }else{
          $point= (int)(0.188807*pow(((float)$highrecord*100-210),1.41)); //longjump
        }
        $plus=',record_multi_record="'.$point.'"';
        if($row1['schedule_status']!='y'){
          $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
      }else{
          $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
      }
      }
      if($highrecord == '0' && $ruf=="X"){
        $highrecord ='X';
      }
        $savequery="UPDATE list_record SET record_".$result_type1."_result='$result[$i]',record_judge='$judge[0]',record_new='$new',record_medal=".$medal.",record_status='".$result_type2."'".$plus." 
                    WHERE record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record='$highrecord'";
    } else {
      $k = $j + 1;
      $ruf = ($fieldrecord[$j][$i]??0);
      $win = ($windrecord[$j][$i]??NULL);
      if($win==''){
        $win='NULL';
      }
      $savequery =
        "UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',
                record_".$result_type1."_record='$ruf', record_new='$new',record_medal=" .
        $medal .
        ",record_memo=CONCAT(record_memo,' ".$memo[$i]."') ,record_wind=$win
                WHERE record_athlete_id ='" .
        $row["athlete_id"] .
        "' AND record_schedule_id='$schedule_id' AND record_trial='$k'";
        if($highrecord<$ruf && $ruf!='X'){
          $highrecord= $ruf;
          $hightrial=$k;
          $highwin=$win;
      }
    }
    mysqli_query($db, $savequery);
  }
}
if($row1['schedule_status']!='y' && $tempstore =='0'){
  $finishcnt=0;
  $db->query("UPDATE list_schedule set schedule_end='".date("Y-m-d H:i:s")."',schedule_result='l',schedule_status='y',schedule_memo='".$_POST['bibigo']."' where schedule_id=".$row1['schedule_id'].""); // 경기 종료 스케쥴에 반영
  if($name==='Decathlon' || $name ==='Heptathlon'){
      $schedule_result=$db->query("select schedule_status, schedule_id from list_schedule where schedule_name= '$name' and schedule_division = 's' ORDER BY schedule_id ASC"); //10종,7종 소그룹 경기 종료 여부 찾는 쿼리
  }else{
      $schedule_result=$db->query("select schedule_status, schedule_id from list_schedule where schedule_name= '$name' and schedule_round= '$round' and schedule_division = 's' ORDER BY schedule_id ASC"); //소그룹 경기 종료 여부 찾는 쿼리
  }
  while($schedule_row=mysqli_fetch_array($schedule_result)){
      if($schedule_row[0]==='n' || $schedule_row[0] ==='o'){
          $finishcnt++;
      }
    }
    if($finishcnt===0){ //모두 종료시 빅그룹 경기 일정 종료
        $db->query("UPDATE list_schedule set schedule_end='".date("Y-m-d H:i:s")."',schedule_result='l',schedule_status='y' where schedule_name= '$name' and schedule_round= '$round' and schedule_gender ='".$row1['schedule_gender']."'and schedule_division = 'b'");
} 
} 
if($row1['schedule_status']!='y'){
    logInsert($db, $_SESSION['Id'], '기록 등록', $name . "-" . $row1['schedule_gender'] . "-" . $round. "-" .$row1['schedule_group']);
}else{
    logInsert($db, $_SESSION['Id'], '기록 수정', $name . "-" . $row1['schedule_gender'] . "-" . $round. "-" .$row1['schedule_group']);
    
}   
echo "<script>
        opener.parent.location.reload();
        window.close(); 
        </script>";
        echo "<script>
    location.replace('../../record/field_horizontal_result_view.php?id=".$s_id."') 
    </script>";
?>