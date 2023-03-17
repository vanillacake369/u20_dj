<?php
    //릴레이 경기 용
    include __DIR__ . "/../../includes/auth/config.php";
    include __DIR__ . "/../module/record_worldrecord.php";
    date_default_timezone_set('Asia/Seoul'); //timezone 설정
    global $db;   
    $check_round='n';
    $athlete_name=$_POST['playername'];
    $round=$_POST['round']; 
    $wind=$_POST['wind']??null;
    $pass=$_POST['gamepass'];
    $name=$_POST['gamename'];
    $medal=0;
    $result=$_POST['rank'];
    $comprecord=$_POST['compresult'];
    $reactiontime=$_POST['reactiontime'];
    $record=$_POST['gameresult'];
    $memo=$_POST['bigo'];
    $rane=$_POST['rain'];
    $judge_id=$_POST['refereename'];
    $s_id=$_POST['schedule_id'];
    $judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
    $judge=mysqli_fetch_array($judgeresult);
    $res1= $db->query("SELECT * FROM list_schedule WHERE schedule_id='$s_id'");
    $row1 = mysqli_fetch_array($res1);
    if($row1['schedule_result'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
    $result_type1='official';
    $result_type2='o';
}else{
    $result_type1='live';
    $result_type2='l';
}if($row1['schedule_result'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
    $result_type1='official';
    $result_type2='o';
}else{
    $result_type1='live';
    $result_type2='l';
}
        for($i=0;$i<count($athlete_name);$i++){
            $medal = 0;
            $in=(int)($i/4);
            
            //결승일 경우 메달 계산
            if($round == 'final'){
                switch($result[$in]){
                    case 1: $medal=10000; break;
                    case 2: $medal=100; break;
                    case 3: $medal=1; break;
                    default: $medal=0; break;
                }
            } 
            $re= $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_schedule_id = '$s_id' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");        
            $row = mysqli_fetch_array($re);
            
            if($i%4===0){
                    $tempmemo='';
                    $new='n';
                    // 신기록 계산
                    if($row1['schedule_status']==='y'){
                        $newre=$db->query("select record_new from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$s_id'");
                        $rerow=mysqli_fetch_array($newre);
                        $new = $rerow[0];
                    }
                    if($comprecord[$i/4] != $record[$i/4]){ //기존 기록과 변경된 기록이 같은 지 비교
                        $memo[$in]=changePbSb($athlete_name[$i],$record[$in],$s_id,$memo[$in],$check_round,'t');
                        if($row1['schedule_status']==='y'){ //경기가 끝났는 지 판단
                            if($rerow[0]==='y'){
                                $arr=modify_worldrecord($row[1],$row[1],$record[$in],$wind,$s_id,$check_round);
                                $tempmemo=change_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$s_id,$check_round,$arr);
                            }else{
                                $arr2=insert_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$s_id,$check_round);
                                $tempmemo=$arr2[0];
                                $new=$arr2[1];
                            }
                        }else{
                            $arr2=insert_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$s_id,$check_round);
                            $tempmemo=$arr2[0];
                            $new=$arr2[1];
                        }
                    }
                }else{
                    changePbSb($athlete_name[$i],$record[$in],$s_id,$memo[$in],$check_round,'t');
                }
                    //memo 합치는 과정
                    if( $tempmemo!=''){
                        if(strlen($memo[$in])>=1){
                            $memo[$in]=$memo[$in].",".$tempmemo;
                        }else{
                            $memo[$in]=$tempmemo;
                        }
                    }  
                    $savequery="UPDATE list_record SET record_pass='$pass[$in]', record_".$result_type1."_result='$result[$in]',record_judge='$judge[0]',
                    record_".$result_type1."_record='$record[$in]', record_new='$new',record_memo='".$memo[$in]."',record_medal=".$medal.",record_reaction_time='$reactiontime[$in]'
                    ,record_wind='$wind',record_status='".$result_type2."' WHERE record_athlete_id ='".$row['athlete_id']."' AND record_schedule_id='$s_id' AND record_judge='$judge[0]'" ;
                    // echo $savequery.'<br>';
                    mysqli_query($db,$savequery);
        }
        if($row1['schedule_status']!='y'){
            $finishcnt=0;
            $db->query("UPDATE list_schedule set schedule_end='".date("Y-m-d H:i:s")."',schedule_result='l',schedule_status='y',schedule_memo='".$_POST['bibigo']."' where schedule_id=".$s_id."");
            $schedule_result=$db->query("select schedule_status, schedule_id from list_schedule where schedule_name= '$name' and schedule_round= '$round' and schedule_division = 's' ORDER BY schedule_id ASC");
            while($schedule_row=mysqli_fetch_array($schedule_result)){
                if($schedule_row[0]==='n'){
                    $finishcnt++;
                }
            }
            if($finishcnt===0){
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
    location.replace('../../record/track_relay_result_view.php?id=".$s_id."') 
    </script>";

?>