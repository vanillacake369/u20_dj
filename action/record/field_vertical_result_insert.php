<?php
//높이뛰기,장대높이뛰기 경기용
include __DIR__ . "/../module/record_worldrecord.php";
include __DIR__ . "/../../includes/auth/config.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST["playername"];
$round=$_POST['round'];
$gender = $_POST['gender'];
$name=$_POST['sports'];
$heat = $_POST['group'];
$medal = 0;
$result = $_POST["rank"];
$comprecord=$_POST['compresult'];
$record = $_POST["gameresult"];
$new = $_POST["newrecord"];
$s_memo=$_POST['bibigo'];
$tempstore=$_POST['tempstore'];
$memo = $_POST["bigo"];
$rane = $_POST["rain"];
$judge_id = $_POST["refereename"];
$judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
$judge=mysqli_fetch_array($judgeresult);
$res1 = $db->query("SELECT * FROM list_schedule 
join list_record
where record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round");
// echo "SELECT * FROM list_schedule join list_record where record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round";
$row1 = mysqli_fetch_array($res1);
if($name==='decathlon' || $name ==='heptathlon'){
  $sports_code=$row1['sports_code'];
  $totalrow='record_sports="'.$row1['schedule_sports'].'" and record_gender="$gender" and record_round="final"';
  $check_round='y';
}else{
  $sports_code=$row1['schedule_sports'];
  $check_round='n';
}
$starttime=$_POST['starttime'];
    // $db->query("update list_schedule set schedule_start ='".$starttime."' where schedule_sports='$name' and schedule_gender='$gender' and shcedule_round='$round'");
    $db->query("update list_record set record_start ='".$starttime."' where record_sports='$name' and record_gender='$gender' and record_round='$round' and record_group='$heat'");
if($row1['record_state'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체
  $result_type1='official';
  $result_type2='o';
}else{
  $result_type1='live';
  $result_type2='l';
}
$highcnt = 0; //높이 개수
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
  echo '<br>';
  $tempmemo='';
  $medal = 0;
  $best = 0; //선수별 최고 기록
  $re= $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' and athlete_name = '" . $athlete_name[$j] . "' and record_athlete_id=athlete_id");
  // echo "SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$j] . "' and record_athlete_id=athlete_id".'<br>';
  $row = mysqli_fetch_array($re);
  $highresult=$db->query("SELECT DISTINCT record_".$result_type1."_record 
      FROM list_record WHERE record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_athlete_id = ".$row['athlete_id']." AND record_".$result_type1."_record > 0 ORDER BY cast(record_".$result_type1."_record as int)");
  $checkhigh=$db->query("SELECT DISTINCT record_".$result_type1."_record 
      FROM list_record WHERE record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' and record_athlete_id='".$row['athlete_id']."'");
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
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record = '$checkrow[0]'";
      }else{
        // record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat'
        $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]')";
      }
      mysqli_query($db, $savequery);
      // echo 'ㅌㅌㅌ'.$savequery.'<br>';
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
        $zeroresult=$db->query("select record_id from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' and record_".$result_type1."_record='0'");
        $zerorow=mysqli_fetch_array($zeroresult);
      }
      if($row1['record_state']==='y'){ //경기가 끝났는 지 판단
        $newre=$db->query("select record_new,record_multi_record from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_result>0");
        $rerow=mysqli_fetch_array($newre);
        $new = $rerow[0];
      }
      //---------------------------- 신기록 시작
    //   if(strpos($memo[$j],'참고 기록')!==TRUE){   
    //    if($comprecord[$j] != $best){ //기존 기록과 변경된 기록이 같은 지 비교
    //        print_r($row);
    //      $memo[$j]=changePbSb($row[0],$best,$name, $gender, $round,$memo[$j],$check_round,'f');
    //      if($row1['record_state']==='y'){ //경기가 끝났는 지 판단
    //        if($rerow[0]==='y'){
    //          $arr=modify_worldrecord($athlete_name[$j],$row[1],$best,0,$name, $gender, $round,$check_round);
    //          $tempmemo=change_worldrecord_dec($athlete_name[$j],$row[1],$best,0,$name, $gender, $round,$check_round,$arr);
    //        }else{
    //          $arr2=insert_worldrecord_dec($athlete_name[$j],$row[1],$best,0,$name, $gender, $round,$check_round);
    //          $tempmemo=$arr2[0];
    //          $new=$arr2[1];
    //        }
    //      }else{
    //        $arr2=insert_worldrecord_dec($athlete_name[$j],$row['athlete_country'],$best,0,$name, $gender, $round,$check_round);
    //        $tempmemo=$arr2[0];
    //        $new=$arr2[1];
    //      }
    //    }
    //   if( $tempmemo!=''){
    //     if(strlen($memo[$j])>=1){
    //         $memo[$j]=$memo[$j].",".$tempmemo;
    //     }else{
    //         $memo[$j]=$tempmemo;
    //     }
    //   }
    // }
      //--------------------------- 신기록 끝
      if($row1['record_state']==='y'){
        $highrow=mysqli_fetch_array($highresult);
        if(empty($highrow)){
          $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]')";
        }else{
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."' ,record_trial='$ruf' 
          WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record = '$highrow[0]'";
        }
      }else{
        $checkrow=mysqli_fetch_array($checkhigh);
      if(($checkrow[0]??null)>0){
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
      WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record = '$checkrow[0]'";
        }else{
          $savequery =
            "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge)
                            VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]')";
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
              $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge,record_".$result_type1."_result,record_multi_record)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]','$result[$j]','$point')"; //최종기록에 등수 및 메달 업데이트
            }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                          WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record='$best'";
          }
          if($row1['record_state']!='y'){
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND $totalrow");
          }else{
            $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND $totalrow");
          }
        }else if($round ==='polevault'){
          $point= (int)(0.2797*pow(((float)$best*100-100),1.35)); //polevault
          if($best=='0'){
            if(($zerorow[0]??null) != null){
              $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                            WHERE record_id='$zerorow[0]'";
              }else{
                $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge,record_".$result_type1."_result,record_multi_record)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]','$result[$j]','$point')";
              }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_".$result_type1."_result='$result[$j]',record_multi_record='$point' 
                          WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record='$best'";
          }
          if($row1['record_state']!='y'){
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
              $updatequery="INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge,record_".$result_type1."_result)
                              VALUES ('$pass','$best','$memo[$j]','','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]','$result[$j]')"; //최종기록에 등수 및 메달 업데이트
            }
          }else{
            $updatequery="UPDATE list_record SET record_".$result_type1."_result='$result[$j]',record_medal='$medal' 
            WHERE record_athlete_id ='".$row["athlete_id"] ."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record='$best'"; //최종기록에 등수 및 메달 업데이트
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
    if($row1['record_state']==='y'){
      $highrow=mysqli_fetch_array($highresult);
      if(empty($highrow)){
        $savequery =
        "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge)
                        VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]')";
      }else{
        $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."' ,record_trial='$ruf' 
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record = '$highrow[0]'";
      }
    }else{
      //------- ↓각 높이에 해당하는 기록 삽입
      $checkrow=mysqli_fetch_array($checkhigh);
      if(($checkrow[0]??null)>0){
          $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
      WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_".$result_type1."_record = '$checkrow[0]'";
        }else if ($i == 0) {
        //처음은 오더때문에 생성 되어있기 때문에 업데이트로 넣음
        $savequery ="UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',record_".$result_type1."_record='$high[$i]', record_memo='".$memo[$j]."',record_trial='$ruf' 
        WHERE record_athlete_id ='".$row["athlete_id"]."' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat'";
        } else {
          //두번째부터는 높이가 정해져있지 않다고가정 후 작성 - 추후에 최초에 높이가 몇 번째까지 정해져있는지에 따라 바꿀 예정
          $savequery =
          "INSERT INTO list_record(record_pass, record_".$result_type1."_record,record_memo,record_trial,record_athlete_id,record_sports,record_round,record_gender,record_group,record_status,record_order,record_judge)
                          VALUES ('$pass','$high[$i]','$memo[$j]','$ruf','".$row["athlete_id"]."','$name','$round','$gender','$heat','l','$rane[$j]','$judge[0]')";
        }
    }
    // echo $savequery.'<br>';
      mysqli_query($db, $savequery);
    }
}
if($row1['record_status']!='o'  && $tempstore =='0'){
  $finishcnt=0;
  $db->query("UPDATE list_record set record_end='".date("Y-m-d H:i:s")."',record_state='y' where record_sports= '$name' AND record_round= '$round' AND record_group='$heat' AND record_gender='$gender'"); // 경기 종료 스케쥴에 반영
  $db->query("UPDATE list_schedule set schedule_memo='".$_POST['bibigo']."' where schedule_sports= '$name' AND schedule_round= '$round' AND schedule_gender='$gender'"); // 경기 종료 스케쥴에 반영
}
if($row1['record_state']!='y'){
    logInsert($db, $_SESSION['Id'], '기록 등록', $name . "-" . $row1['record_gender'] . "-" . $round. "-" .$row1['record_group']);
}else{
    logInsert($db, $_SESSION['Id'], '기록 수정', $name . "-" . $row1['record_gender'] . "-" . $round. "-" .$row1['record_group']);

}
 echo "<script>
   opener.parent.location.reload();
   window.close();
   </script>";
 echo "<script>
   location.replace(document.referrer)
 </script>";