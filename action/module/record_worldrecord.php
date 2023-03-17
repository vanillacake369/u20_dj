<?php
include_once(__DIR__ . "/../../includes/auth/config.php");

function changeresult($a){ //1분이상 경기 기록 변환
    if(strlen($a)>6){
        $a=explode(':',$a);
        $a=(float)$a[0]*60+(float)$a[1];
    }else{
        $a=(float)$a;
    }
    return $a;
}

//기록 원상태 변환
function restoreresult($a){
    if($a/60>=1){
        $b=(int)($a/60);
        $c=$a-$b*60;
        if($c<10){
            $d=(string)$b.":0".(string)$c;         
        }else if($c==(int)$c){
            $d=(string)$b.":".(string)$c.'.00';  
        }else{
            $d=(string)$b.":".(string)$c;     
        }
    }else{
        $d=$a;
    }
    return $d;
}

// 해당 스캐줄 정보 조회
function check_schedule($schedule_id,$check_round,$check_big = 'y'){
    global $db;
    if($check_round =='y' ){
        $schedule_result=$db->query("SELECT * from list_schedule join list_sports where schedule_id=$schedule_id and schedule_round=sports_code"); //해당 경기에 관련된 정보 조회
        $schedule_row=mysqli_fetch_array($schedule_result);
    }else{
        $schedule_result=$db->query("SELECT * from list_schedule join list_sports where schedule_id=$schedule_id and schedule_sports=sports_code"); //해당 경기에 관련된 정보 조회 
        $schedule_row=mysqli_fetch_array($schedule_result);    
        if($check_big=='y'){
            $schedule_result=$db->query("SELECT * from list_schedule join list_sports where schedule_sports='".$schedule_row['schedule_sports']."' and schedule_division='b' and '".$schedule_row['schedule_sports']."'=sports_code and schedule_gender= '".$schedule_row['schedule_gender']."'"); //해당 경기의 빅그룹에 관련된 정보 조회
            $schedule_row=mysqli_fetch_array($schedule_result);
        }
    }
    
    return $schedule_row;
}

//해당 경기 종목에 대한 신기록 조회
function check_worldrecord($schedule_id,$check_round,$time){
    global $db;
    $i=0;
    $worldrecord=array(); //해당 종목의 하나의 athletics에 여러 기록이 있을 경우 최고 기록을 찾기 저장하기 위한 배열
    $schedule= check_schedule($schedule_id,$check_round);
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    $wr_result = $db->query("SELECT worldrecord_athletics, worldrecord_record ,worldrecord_wind, worldrecord_athlete_name, worldrecord_country_code, worldrecord_datetime
    FROM list_worldrecord 
    WHERE worldrecord_sports ='$sport_code' AND worldrecord_gender='" . $schedule['schedule_gender'] . "' and worldrecord_datetime <= '".$time."'
    order BY FIELD(worldrecord_athletics, 'w', 'u', 'a','s','c') "); 
    //신기록 key->value로 저장 key: ahtletics, value: record
    if($schedule['sports_category'] == '트랙경기'){
        while($wr_row=mysqli_fetch_assoc($wr_result)){ 
            if(empty($worldrecord[$wr_row['worldrecord_athletics']])){
                $worldrecord[$wr_row['worldrecord_athletics']]['athlete_name'] = $wr_row['worldrecord_athlete_name'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['country_code'] = $wr_row['worldrecord_country_code'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['record'] = changeresult($wr_row['worldrecord_record']);       
                $worldrecord[$wr_row['worldrecord_athletics']]['wind'] = $wr_row['worldrecord_wind'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['datetime'] = $wr_row['worldrecord_datetime'];      
                $worldrecord[$wr_row['worldrecord_athletics']]['athletics'] = $wr_row['worldrecord_athletics'];       
            }else if($worldrecord[$wr_row['worldrecord_athletics']]['record']>=changeresult($wr_row['worldrecord_record'])){
                if($worldrecord[$wr_row['worldrecord_athletics']]['record']==changeresult($wr_row['worldrecord_record'])){
                    $worldrecord[$wr_row['worldrecord_athletics']][$i] = $wr_row;
                    $i++;   
                    continue;
                }
                $worldrecord[$wr_row['worldrecord_athletics']]['athlete_name'] = $wr_row['worldrecord_athlete_name'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['country_code'] = $wr_row['worldrecord_country_code'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['record'] = changeresult($wr_row['worldrecord_record']);       
                $worldrecord[$wr_row['worldrecord_athletics']]['wind'] = $wr_row['worldrecord_wind'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['datetime'] = $wr_row['worldrecord_datetime'];     
                $worldrecord[$wr_row['worldrecord_athletics']]['athletics'] = $wr_row['worldrecord_athletics'];
                $i=0;
            }
        }
    }else{
        while($wr_row=mysqli_fetch_assoc($wr_result)){ 
            if(empty($worldrecord[$wr_row['worldrecord_athletics']])){
                $worldrecord[$wr_row['worldrecord_athletics']]['athlete_name'] = $wr_row['worldrecord_athlete_name'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['country_code'] = $wr_row['worldrecord_country_code'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['record'] = changeresult($wr_row['worldrecord_record']);       
                $worldrecord[$wr_row['worldrecord_athletics']]['wind'] = $wr_row['worldrecord_wind'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['datetime'] = $wr_row['worldrecord_datetime'];   
                $worldrecord[$wr_row['worldrecord_athletics']]['athletics'] = $wr_row['worldrecord_athletics'];        
            }else if($worldrecord[$wr_row['worldrecord_athletics']]['record']<=changeresult($wr_row['worldrecord_record'])){
                if($worldrecord[$wr_row['worldrecord_athletics']]['record']==changeresult($wr_row['worldrecord_record'])){
                    $worldrecord[$wr_row['worldrecord_athletics']][$i] = $wr_row;
                    $i++;   
                    continue;
                }
                $worldrecord[$wr_row['worldrecord_athletics']]['athlete_name'] = $wr_row['worldrecord_athlete_name'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['country_code'] = $wr_row['worldrecord_country_code'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['record'] = changeresult($wr_row['worldrecord_record']);       
                $worldrecord[$wr_row['worldrecord_athletics']]['wind'] = $wr_row['worldrecord_wind'];       
                $worldrecord[$wr_row['worldrecord_athletics']]['datetime'] = $wr_row['worldrecord_datetime'];  
                $worldrecord[$wr_row['worldrecord_athletics']]['athletics'] = $wr_row['worldrecord_athletics'];
                $i=0;     
            }
        }
    }
    return $worldrecord;
}

//선수의 신기록 조회
function check_my_record($athlete_name,$sport_code,$time){
    global $db;
    $myrecord=array();
    $giresult=$db->query("select * from list_worldrecord where worldrecord_athlete_name='$athlete_name' and worldrecord_sports='$sport_code' and worldrecord_datetime  between (SELECT min(schedule_date) FROM list_schedule) and '$time'"); //between 날짜 스케쥴상 처음 날짜부터 검색
    while($row=mysqli_fetch_assoc($giresult)){
        $myrecord[$row['worldrecord_athletics']]=$row;
    }
    return $myrecord;
}

//더 낮은 신기록 입력(트랙)
function insert_worldrecord_inc($athlete_name,$athlete_country,$record,$wind,$schedule_id,$check_round){
    global $db;
    $memo='';
    $new='n';
    $schedule= check_schedule($schedule_id,$check_round);
    $wr=check_worldrecord($schedule_id,$check_round,$schedule['schedule_start']);
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    foreach($wr as $k){
        if(changeresult($record)<=$k['record']){
            if(changeresult($record)==$k['record']){
                $memo='tie record';
            }
            $new='y';
            $savesql="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
            worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
            values('".$sport_code."','".$schedule['schedule_location']."','".$schedule['schedule_gender']."','".$athlete_name."','".$k['athletics']."','$wind','".date("Y-m-d H:i:s")."','".$athlete_country."','$record')";
            $db->query($savesql);
        }
    }
    $arr=[$memo,$new];
    return $arr;
}

//더 높은 신기록 입력(필드,10종&7종 최종 점수)
function insert_worldrecord_dec($athlete_name,$athlete_country,$record,$wind,$schedule_id,$check_round){
    global $db;
    $memo='';
    $new='n';
    $schedule= check_schedule($schedule_id,$check_round);
    $wr=check_worldrecord($schedule_id,$check_round,$schedule['schedule_start']);
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    foreach($wr as $k){
        if(changeresult($record)>=$k['record']){
            if(changeresult($record)==$k['record']){
                $memo='tie record';
            }
            $new='y';
            $savesql="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
            worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
            values('".$sport_code."','".$schedule['schedule_location']."','".$schedule['schedule_gender']."','".$athlete_name."','".$k['athletics']."','$wind','".date("Y-m-d H:i:s")."','".$athlete_country."','$record')";
            $db->query($savesql);
        }
    }
    $arr=[$memo,$new];
    return $arr;
}

//신기록 수정
function modify_worldrecord($athlete_name,$athlete_country,$record,$wind,$schedule_id,$check_round){
    global $db;
    $schedule= check_schedule($schedule_id,$check_round);
    $ath=array();
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    $check=check_my_record($athlete_name,$sport_code,$schedule['$schedule_end']); // 선수가 가지고 있는 신기록 아이디
    foreach($check as $cnt){            
        $db->query("update list_worldrecord set worldrecord_record='$record', worldrecord_wind='$wind' where worldrecord_id='".$cnt['worldrecord_athletics']."'");//업데이트믄     
        // echo "update list_worldrecord set worldrecord_record='$record[$i]', worldrecord_wind='$wind' where worldrecord_id='$girow[0]'".'<br>';
        array_push($ath,$cnt['worldrecord_athletics']);
    }
    return $ath;
}

function change_worldrecord_inc($athlete_name,$athlete_country,$record,$wind,$schedule_id,$check_round,&$ath){
    global $db;
    $memo='';
    $schedule= check_schedule($schedule_id,$check_round);
    $wr=check_worldrecord($schedule_id,$check_round,$schedule['schedule_start']);
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    $ccc = array_diff(array_keys($wr), $ath);
    foreach($wr as $k){
        foreach($ccc as $c){
            echo $k['athletics'].'  '.$c.'<br>';
            if($k['athletics']==$c){
                if(changeresult($record)<=$k['record']){
                    if(changeresult($record)==$k['record']){
                        $memo='tie record';
                    }
                    $savesql="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                    worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                    values('".$sport_code."','".$schedule['schedule_location']."','".$schedule['schedule_gender']."','".$athlete_name."','".$k['athletics']."','$wind','".date("Y-m-d H:i:s")."','".$athlete_country."','$record')";
                    $db->query($savesql);
                }
            }
        }     
        return $memo;
    }
}

function change_worldrecord_dec($athlete_name,$athlete_country,$record,$wind,$schedule_id,$check_round,&$ath){
    global $db;
    $memo='';
    $schedule= check_schedule($schedule_id,$check_round);
    $wr=check_worldrecord($schedule_id,$check_round,$schedule['schedule_start']);
    if($check_round =='y' ){
        $sport_code=$schedule['schedule_sports']."(".$schedule['schedule_round'].")"; //10종 및 7종 경기의 경우 worldrecord_sports형식 : 경기_스포츠(경기라운드) 경기_스포츠-10종 or 7종, 경기라운드-세부 종목
    }else{
        $sport_code=$schedule['schedule_sports'];
    }
    $ccc = array_diff(array_keys($wr), $ath);
    foreach($wr as $k){
        foreach($ccc as $c){
            echo $k['athletics'].'  '.$c.'<br>';
            if($k['athletics']==$c){
                if(changeresult($record)<=$k['record']){
                    if(changeresult($record)==$k['record']){
                        $memo='tie record';
                    }
                    $savesql="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                    worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                    values('".$sport_code."','".$schedule['schedule_location']."','".$schedule['schedule_gender']."','".$athlete_name."','".$k['athletics']."','$wind','".date("Y-m-d H:i:s")."','".$athlete_country."','$record')";
                    $db->query($savesql);
                }
            }
        }     
        return $memo;
    }
}