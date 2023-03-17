<?php
//일반 트랙 경기용
include __DIR__ . "/../../includes/auth/config.php";
include __DIR__ . "/../module/record_worldrecord.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST['playername'];
$round = $_POST['round'];
$wind = $_POST['wind'] ?? null;
$pass = $_POST['gamepass'];
$name = $_POST['gamename'];
$medal = 0;
$result = $_POST['rank'];
$record = $_POST['gameresult'];
$comprecord=$_POST['compresult'];
$reactiontime=$_POST['reactiontime'];
$memo = $_POST['bigo'];
$judge_name = $_POST['refereename'];
$newrecord=$_POST['newrecord'];
$s_id=$_POST['schedule_id'];
$judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_name'"); //심판 아이디 쿼리
$judge=mysqli_fetch_array($judgeresult);
$new = 'n';
if($name==='Decathlon' || $name ==='Heptathlon'){
    $res1 = $db->query("SELECT * FROM list_schedule INNER JOIN list_sports ON sports_code = schedule_round AND  schedule_id='$s_id'");
    $row1 = mysqli_fetch_array($res1);
    $totalresult=$db->query("select schedule_id from list_schedule where schedule_name='$name' and schedule_round='final' and schedule_division='s'");
    $totalrow=mysqli_fetch_array($totalresult);
    $check_round='y';
}else{
    $res1 = $db->query("SELECT * FROM list_schedule where schedule_id='$s_id'");
    $row1 = mysqli_fetch_array($res1);
    $check_round='n';
}
if($row1['schedule_result'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
    $result_type1='official';
    $result_type2='o';
}else{
    $result_type1='live';
    $result_type2='l';
}

$schedule_id = $s_id;
for ($i = 0; $i < count($athlete_name); $i++) {
    $medal = 0;
    $re = $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");
    $row = mysqli_fetch_array($re);
    if ($round == 'final') {
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
    // 신기록 계산
    if($row1['schedule_status']==='y'){
        $newre=$db->query("select record_new,record_multi_record from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$schedule_id' AND record_".$result_type1."_result>0");
        $rerow=mysqli_fetch_array($newre);
        $new = $rerow[0];
    }
    if($comprecord[$i] != $record[$i]){ //기존 기록과 변경된 기록이 같은 지 비교
        if($row1['schedule_status']==='y'){ //경기가 끝났는 지 판단
            if($rerow[0]==='y'){
                $arr=modify_worldrecord($athlete_name[$i],$row[1],$record[$i],$wind,$s_id,$check_round);
                $memo[$i]=change_worldrecord_inc($athlete_name[$i],$row[1],$record[$i],$wind,$s_id,$check_round,$arr);
            }else{
                $arr2=insert_worldrecord_inc($athlete_name[$i],$row[1],$record[$i],$wind,$s_id,$check_round);
                $memo[$i]=$arr2[0];
                $new=$arr2[1];
            }
        }else{
            $arr2=insert_worldrecord_inc($athlete_name[$i],$row[1],$record[$i],$wind,$s_id,$check_round);
            $memo[$i]=$arr2[0];
            $new=$arr2[1];
        }
    }
    $plus=''; //10종,7종 경기시 사용할 쿼리 준비
        if($round ==='100m'){
            $point= (int)(25.4347*pow((18-(float)$record[$i]),1.81)); //100m
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if($round ==='100mh'){
            $point= (int)(9.23076*pow((26.7-(float)$record[$i]),1.835)); //100mH
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if($round ==='200m'){
            $point= (int)(4.99087*pow((42.5-(float)$record[$i]),1.81)); //200m
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if( $round ==='400m'){               
            $point= (int)(1.53775*pow((82-(float)$record[$i]),1.81)); //400m
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if( $round ==='800m'){               
            $temp=explode(":", $record[$i]);
            $point= (int)(0.11193*pow((254-((int)$temp[0])*60 -$temp[1]),1.88)); //1500m
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if($round ==='110mh'){
            $point= (int)(5.74352*pow((28.5-(float)$record[$i]),1.92)); //110mH
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }else if($round ==='1500m'){
            $temp=explode(":", $record[$i]);
            $point= (int)(0.03768*pow((480-((int)$temp[0])*60 -$temp[1]),1.85)); //1500m
            $plus=",record_multi_record='".$point."'";
            if($row1['schedule_status']!='y'){
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }else{
                $db->query("UPDATE list_record set record_".$result_type1."_record=record_".$result_type1."_record-$rerow[1]+$point where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='".$totalrow[0]."'");
            }
        }
            $savequery="UPDATE list_record SET record_pass='$pass[$i]', record_".$result_type1."_result='$result[$i]', record_judge='$judge[0]',
            record_".$result_type1."_record='$record[$i]', record_new='$new',record_memo=CONCAT(record_memo,'".$memo[$i]."'),record_medal=".$medal.",record_reaction_time='$reactiontime[$i]'
            ,record_wind='$wind',record_status='".$result_type2."'".$plus." WHERE record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$schedule_id'" ;    
            $db->query($savequery);
        }
        if($round ==='1500m' || $round ==='800m'){
            $dal=10000;
            $count=1;
            $total_memo='';
            $total_new='n';
            $rankresult=$db->query("SELECT record_id,record_".$result_type1."_record,record_new,athlete_name,athlete_country FROM list_record join list_athlete WHERE record_schedule_id=".$totalrow[0]." AND athlete_id=record_athlete_id ORDER BY record_".$result_type1."_record *1 DESC");
            while($rankrow=mysqli_fetch_array($rankresult)){
                if($row1['schedule_status']==='y'){ //신기록 등록
                    if($rankrow['record_new']==='y'){
                        $arr=modify_worldrecord($rankrow['athlete_name'],$rankrow['athlete_country'],$rankrow['record_".$result_type1."_record'],0,$totalrow[0],'n');
                        $total_memo=change_worldrecord_dec($rankrow['athlete_name'],$rankrow['athlete_country'],$rankrow['record_".$result_type1."_record'],0,$totalrow[0],'n',$arr);
                    }else{
                        $arr2=insert_worldrecord_dec($rankrow['athlete_name'],$rankrow['athlete_country'],$rankrow["record_".$result_type1."_record"],0,$totalrow[0],'n');
                        $total_memo=$arr2[0];
                        $total_new=$arr2[1];
                    }
                    }else{
                        $arr2=insert_worldrecord_dec($rankrow['athlete_name'],$rankrow['athlete_country'],$rankrow["record_".$result_type1."_record"],0,$totalrow[0],'n');
                        $total_memo=$arr2[0];
                        $total_new=$arr2[1];
                    }
                $db->query("update list_record SET record_medal='$dal',record_".$result_type1."_result='$count',record_memo='$total_memo',record_new='$total_new' where record_id ='$rankrow[0]'"); //등수 기록
                if($dal!=1) {
                    $dal=(int)$dal/100;
                }else if($dal<=1){
                    $dal=0;
                }
                $count++;
            }
            $db->query("UPDATE list_schedule set schedule_end='".date("Y-m-d H:i:s")."',schedule_result='l',schedule_status='y' where schedule_id=".$totalrow[0]."");
        }
    if($row1['schedule_status']!='y'){
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
    location.replace('../../record/track_normal_result_view.php?id=".$s_id."') 
    </script>";
        ?>