<?php
//높이뛰기,장대높이뛰기 경기용
include __DIR__ . "/../module/record_worldrecord.php";
include __DIR__ . "/../../includes/auth/config.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST["playername"];
$round = $_POST["round"];
$name = $_POST["gamename"];
$medal = 0;
$result = $_POST["rank"];
$comprecord=$_POST['compresult'];
$record = $_POST["gameresult"];
$s_id=$_POST['schedule_id'];
$new = $_POST["newrecord"];
$s_memo=$_POST['bibigo'];
$tempstore=$_POST['tempstore'];
$memo = $_POST["bigo"]; 
$rane = $_POST["rain"];
$judge_id = $_POST["refereename"];
$judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
$judge=mysqli_fetch_array($judgeresult);
if($name==='Decathlon' || $name ==='Heptathlon'){
  $res1= $db->query("SELECT * FROM list_schedule INNER JOIN list_sports ON sports_code = schedule_round AND  schedule_id='$s_id'"); 
  $row1 = mysqli_fetch_array($res1);
  $sports_code=$row1['sports_code'];
  $totalresult=$db->query("select schedule_id from list_schedule where schedule_name='$name' and schedule_round='final' and schedule_division='s'");
  $totalrow=mysqli_fetch_array($totalresult);
  $check_round='y';
}else{
  $res1= $db->query("SELECT * FROM list_schedule WHERE schedule_id='$s_id'"); 
  $row1 = mysqli_fetch_array($res1);
  $sports_code=$row1['schedule_sports'];
  $check_round='n';
}
$starttime=$_POST['starttime'];
$db->query("update list_schedule set schedule_start ='".$starttime."' where schedule_id=$s_id");
if($row1['schedule_result'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
  $result_type1='official';
  $result_type2='o';
}else{
  $result_type1='live';
  $result_type2='l';
}
$highcnt = 0; //높이 개수
$schedule_id = $row1["schedule_id"];
$high = $_POST["trial"];
$fieldrecord = [
  $_POST["gameresult1"],
  $_POST["gameresult2"],
  $_POST["gameresult3"],
  $_POST["gameresult4"],
  $_POST["gameresult5"],
  $_POST["gameresult6"],
  $_POST["gameresult7"],
  $_POST["gameresult8"],
  $_POST["gameresult9"],
  $_POST["gameresult10"],
  $_POST["gameresult11"],
  $_POST["gameresult12"],
  $_POST["gameresult13"],
  $_POST["gameresult14"],
  $_POST["gameresult15"],
  $_POST["gameresult16"],
  $_POST["gameresult17"],
  $_POST["gameresult18"],
  $_POST["gameresult19"],
  $_POST["gameresult20"],
  $_POST["gameresult21"],
  $_POST["gameresult22"],
  $_POST["gameresult23"],
  $_POST["gameresult24"],
];
for ($i = 0; $i < count($high); $i++) {
  if ($high[$i] != "") {
    $highcnt++;
  }
}

for ($j = 0; $j < count($athlete_name); $j++) {
  $tempmemo='';
  $medal = 0;
  $best = 0; //선수별 최고 기록
    $re = $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$j] . "' and record_athlete_id=athlete_id");
    // echo "SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$j] . "' and record_athlete_id=athlete_id".'<br>';
    $row = mysqli_fetch_assoc($re);
  $highresult=$db->query("SELECT DISTINCT record_".$result_type1."_record 
      FROM list_record INNER JOIN list_schedule ON list_schedule.schedule_id= list_record.record_schedule_id AND list_schedule.schedule_id = '$s_id' AND record_athlete_id = ".$row['athlete_id']." AND record_".$result_type1."_record > 0 ORDER BY record_".$result_type1."_record ");
  $checkhigh=$db->query("SELECT DISTINCT record_".$result_type1."_record 
      FROM list_record INNER JOIN list_schedule ON schedule_id= record_schedule_id AND schedule_id = '$s_id' and record_athlete_id='".$row['athlete_id']."'");
  for ($i = 0; $i < $highcnt; $i++) {
    $k = $j + 1;
    $ruf = $fieldrecord[$i][$j];
    if (strpos($ruf, "O") !== false) { //성공여부 판별
      $pass = "p";
      $best = $high[$i]; //최고기록 저장
    } else if(strpos($ruf, "-") !== false){
      $pass = "w";
    }else{
      $pass = "d";
      if($tempstore =='1'){
        $checkrow=mysqli_fetch_array($checkhigh);
        if(($checkrow[0]??null)>0){
            $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record = '$checkrow[0]'";        
      }else{
        $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]')";
      }
      mysqli_query($db, $savequery);
      // echo $savequery.'<br>';
      break;
        }
      if ($round == "final") {
        switch ($result[$j]) {
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
      if($best=='0'){
        $zeroresult=$db->query("select record_id from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$s_id' and record_".$result_type1."_record='0'");
        $zerorow=mysqli_fetch_array($zeroresult);
      }
      if($row1['schedule_status']==='y'){ //경기가 끝났는 지 판단
        $newre=$db->query("select record_new,record_multi_record from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$s_id' AND record_".$result_type1."_result>0");
        $rerow=mysqli_fetch_array($newre);
        $new = $rerow[0];
      }
      //---------------------------- 신기록 시작
      if($comprecord[$j] != $best){ //기존 기록과 변경된 기록이 같은 지 비교
        $memo[$j]=changePbSb($athlete_name[$j],$highrecord,$s_id,$memo[$j],$check_round,'f');
        if($row1['schedule_status']==='y'){ //경기가 끝났는 지 판단
          if($rerow[0]==='y'){
            $arr=modify_worldrecord($athlete_name[$j],$row[1],$best,0,$s_id,$check_round);
            $tempmemo=change_worldrecord_dec($athlete_name[$j],$row[1],$best,0,$s_id,$check_round,$arr);
          }else{
            $arr2=insert_worldrecord_dec($athlete_name[$j],$row[1],$best,0,$s_id,$check_round); 
            $tempmemo=$arr2[0];
            $new=$arr2[1];
          }
        }else{
          $arr2=insert_worldrecord_dec($athlete_name[$j],$row['athlete_country'],$best,0,$s_id,$check_round);
          $tempmemo=$arr2[0];
          $new=$arr2[1];
        }
      }
      if( $tempmemo!=''){
        if(strlen($memo[$j])>=1){
            $memo[$j]=$memo[$j].",".$tempmemo;
        }else{
            $memo[$j]=$tempmemo;
        }
      }
      //--------------------------- 신기록 끝
      if($row1['schedule_status']==='y'){
        $highrow=mysqli_fetch_array($highresult);
        if(empty($highrow)){
          $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]')";
        }else{
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."' ,record_trial='$ruf' 
          WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record = '$highrow[0]'";   
        }
      }else{
        $checkrow=mysqli_fetch_array($checkhigh);
      if(($checkrow[0]??null)>0){
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
      WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record = '$checkrow[0]'";        
        }else{
          $savequery =
            "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status ,record_order,record_judge)
                            VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]')";
        }
      }
        if($round ==='highjump'){
          if($row1['schedule_gender'] ==='m'){
            $point= (int)(0.8465*pow(((float)$best*100-75),1.42)); //highjump
          }else{
            $point= (int)(1.84523*pow(((float)$best*100-75),1.348)); //highjump
          }
          if($best=='0'){
            if(($zerorow[0]??null) != null){
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                          WHERE record_id='$zerorow[0]'"; 
            }else{
              $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge,record_".$result_type1."_result,record_multi_record)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]','$result[$j]','$point')"; //최종기록에 등수 및 메달 업데이트                         
            }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                          WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record='$best'"; 
          }
          if($row1['schedule_status']!='y'){
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
          }else{
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
          }              
        }else if($round ==='polevault'){
          $point= (int)(0.2797*pow(((float)$best*100-100),1.35)); //polevault
          if($best=='0'){
            if(($zerorow[0]??null) != null){
              $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                            WHERE record_id='$zerorow[0]'"; 
              }else{
                $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge,record_".$result_type1."_result,record_multi_record)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]','$result[$j]','$point')";
              }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                          WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record='$best'"; 
          }
          if($row1['schedule_status']!='y'){
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
          }else{
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
          }
        }else{
          //일반 경기
          if($best=='0'){
            if(($zerorow[0]??null) != null){
              $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_medal='$medal' 
              WHERE record_id='$zerorow[0]'"; //최종기록에 등수 및 메달 업데이트       
            }else{
              $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge,record_".$result_type1."_result)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]','$result[$j]')"; //최종기록에 등수 및 메달 업데이트                               
            }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_medal='$medal' 
            WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record='$best'"; //최종기록에 등수 및 메달 업데이트                
          }
        }
        // echo $savequery.'<br>';
        // echo $updatequery.'<br>';
      mysqli_query($db, $savequery);
      mysqli_query($db, $updatequery);
      break;
    }
    //------- ↑최종기록에 등수 및 메달등 삽입
    //------- ↓기록 수정시 로직
    if($row1['schedule_status']==='y'){
      $highrow=mysqli_fetch_array($highresult);
      if(empty($highrow)){
        $savequery =
        "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge)
                        VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]')";
      }else{
        $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."' ,record_trial='$ruf' 
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record = '$highrow[0]'";   
      }
    }else{
      //------- ↓각 높이에 해당하는 기록 삽입
      $checkrow=mysqli_fetch_array($checkhigh);
      if(($checkrow[0]??null)>0){
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
      WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_record = '$checkrow[0]'";        
        }else if ($i == 0) {
        //처음은 오더때문에 생성 되어있기 때문에 업데이트로 넣음
        $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_schedule_id='$schedule_id'";
        } else {
          //두번째부터는 높이가 정해져있지 않다고가정 후 작성 - 추후에 최초에 높이가 몇 번째까지 정해져있는지에 따라 바꿀 예정
          $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_schedule_id,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$schedule_id','l','$rane[$j]','$judge[0]')";
        }
    }
    // echo $savequery.'<br>';
      mysqli_query($db, $savequery);
    }
}
if($row1['schedule_status']!='y'  && $tempstore =='0'){
  $finishcnt=0;
  $db->query("UPDATE list_schedule set schedule_end='".date("Y-m-d H:i:s")."',schedule_result='l',schedule_status='y',schedule_memo='".$s_memo."' where schedule_id=".$row1['schedule_id'].""); // 경기 종료 스케쥴에 반영
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
  location.replace('../../record/field_vertical_result_view.php?id=".$s_id."') 
</script>";
