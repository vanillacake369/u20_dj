<?php
    //릴레이 경기 용
    require_once __DIR__ . "/../../includes/auth/config.php";
    require_once __DIR__ . "/../module/record_worldrecord.php";
    date_default_timezone_set('Asia/Seoul'); //timezone 설정
    global $db;   
    $check_round='n';
    $athlete_name=$_POST['playername'];
    $round=$_POST['round']; 
    $wind=$_POST['wind']??null;
    $pass=$_POST['gamepass'];
    $name=$_POST['gamename'];
    $sports=$_POST['sports'];
    $medal=0;
    $result=$_POST['rank'];
    $heat = $_POST['group'];
    $gender = $_POST['gender'];
    $comprecord=$_POST['compresult'];
    $reactiontime=$_POST['reactiontime'];
    $record=$_POST['gameresult'];
    $memo=$_POST['bigo'];
    $rane=$_POST['rain'];
    $judge_id=$_POST['refereename'];
    $judgeresult=$db->query("select judge_id from list_judge where judge_name='$judge_id'"); //심판 아이디 쿼리
    $judge=mysqli_fetch_array($judgeresult);
    $starttime = $_POST['starttime'];
$db->query("update list_record set record_start ='" . $starttime . "' where record_sports='$name' and record_gender='$gender' and record_round='$round' and record_group='$heat'");
    $res1 = $db->query("SELECT * FROM list_schedule 
    join list_record
    where record_sports= '$sports' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round");
    $row1 = mysqli_fetch_array($res1);
    if($row1['record_state'] ==='o'){ //schedule_result에 따른 수정 및 저장 주체 
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
            $re= $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_sports= '$sports' AND record_round= '$round' AND record_group='$heat' AND record_gender='$gender' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");        
            $row = mysqli_fetch_array($re);
            
            if($i%4===0){
                    $tempmemo='';
                    $new='n';
                    // 신기록 계산
                    if($row1['record_state']==='y'){
                        $newre=$db->query("select record_new from list_record where record_athlete_id ='".$row['athlete_id']."' AND record_sports= '$sports' AND record_round= '$round' AND record_group='$heat' and record_gender='$gender'");
                        $rerow=mysqli_fetch_array($newre);
                        $new = $rerow[0];
                    }
                    if(strpos($memo[$in],'참고 기록') !== TRUE){
                    if($comprecord[$i/4] != $record[$i/4]){ //기존 기록과 변경된 기록이 같은 지 비교
                        $memo[$in]=changePbSb($row['athlete_id'],$record[$in],$sports,$gender,$round,$memo[$in],$check_round,'t');
                        if($row1['record_state']==='y'){ //경기가 끝났는 지 판단
                            if($rerow[0]==='y'){
                                $arr=modify_worldrecord($row[1],$row[1],$record[$in],$wind,$sports,$gender,$round,$check_round);
                                $tempmemo=change_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$sports,$gender,$round,$check_round,$arr);
                            }else{
                                $arr2=insert_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$sports,$gender,$round,$check_round);
                                $tempmemo=$arr2[0];
                                $new=$arr2[1];
                            }
                        }else{
                            $arr2=insert_worldrecord_inc($row[1],$row[1],$record[$in],$wind,$sports,$gender,$round,$check_round);
                            $tempmemo=$arr2[0];
                            $new=$arr2[1];
                        }
                    }
                }else{
                    changePbSb($row['athlete_id'],$record[$in],$sports,$gender,$round,$memo[$in],$check_round,'t');
                }
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
                    ,record_wind='$wind',record_status='".$result_type2."' WHERE record_athlete_id ='".$row['athlete_id']."' and record_sports= '$sports' AND record_round= '$round' AND record_group='$heat' AND record_gender='$gender' AND record_judge='$judge[0]'" ;
                    // echo $savequery.'<br>';
                    mysqli_query($db,$savequery);
        }
        if($row1['record_status']!='o'){
            $finishcnt=0;
        $db->query("UPDATE list_record set record_end='".date("Y-m-d H:i:s")."',record_state='y' where record_sports= '$sports' AND record_round= '$round' AND record_group='$heat' AND record_gender='$gender'"); // 경기 종료 스케쥴에 반영
        $db->query("UPDATE list_schedule set schedule_memo='".$_POST['bibigo']."' where schedule_sports= '$sports' AND schedule_round= '$round' AND schedule_gender='$gender'"); // 경기 종료 스케쥴에 반영
        }
if($row1['record_state']!='y'){
    logInsert($db, $_SESSION['Id'], '기록 등록', $sports . "-" . $row1['schedule_gender'] . "-" . $round. "-" .$row1['record_group']);
}else{
    logInsert($db, $_SESSION['Id'], '기록 수정', $sports . "-" . $row1['schedule_gender'] . "-" . $round. "-" .$row1['record_group']);
    
}   
echo "<script>
        opener.parent.location.reload();
        window.close(); 
        </script>";
        echo "<script>
    location.replace(document.referrer) 
    </script>";