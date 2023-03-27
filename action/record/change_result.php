<?php
include __DIR__ . "/../../database/dbconnect.php";
// include_once(__DIR__ . "auth/config.php";
include_once(__DIR__ . "/../../security/security.php");
$name=$_POST['name'];
$sports=$_POST['sports'];
$gender=$_POST['gender'];
$round=$_POST['round'];
$group=$_POST['group'];
$gizone=$_POST['gizone'];
echo $sports;
$newrecord=$_POST['newrecord'];
$sports_code=explode('(',$sports)[0];
echo $sports_code.'<br>';
$count=0; // 신기록 모두 삭제 시 필요한 카운트
$sql="";
$check=false;
for($i=0;$i<count($newrecord);$i++){
    if($gizone[$i]==='n'){
        if($sports_code==='4x400mR'||$sports_code === '4x100mR'){
            $result=$db->query("SELECT record_id,schedule_location,schedule_gender,athlete_country,if(record_status='o',record_official_record,record_live_record) as record,record_end,record_wind,record_weight 
                            from list_schedule 
                            inner JOIN list_record ON schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round=record_round
                            INNER JOIN list_athlete ON athlete_country = '$name'  AND record_athlete_id = athlete_id
                            WHERE record_sports='$sports_code' AND record_gender='$gender' AND record_round='$round' AND record_group='$group'");
            while($row1=mysqli_fetch_array($result)){
                $sql.="update list_record set record_new='y' where record_id =".$row1['record_id'].";";
            if($check===false){
                $sql.="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                    worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                    values('$sports',
                    '".$row1['schedule_location']."'
                    ,'".$row1['schedule_gender']."','$name','$newrecord[$i]','"
                    .($row1['record_wind']??$row1['record_weight'])."','".$row1['record_end']."','".$row1['athlete_country']."','".$row1['record']."');";
                    $check=true;
                } 
            }       
        }else{
            $result=$db->query("SELECT record_id,
                            schedule_location,
                            schedule_gender,
                            athlete_country
                            , if(record_status='o',record_official_record,record_live_record) as record,
                            record_end,
                            record_wind,
                            record_weight 
            from list_schedule 
            inner JOIN list_record ON schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round=record_round
            INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
            WHERE record_sports='$sports' AND record_gender='$gender' AND record_round='$round' AND record_group='$group' and if(record_status='o',record_official_record,record_live_record) NOT IN('X','-')
            ORDER BY if(record_status='o',record_official_record,record_live_record) desc"); 
            $row1=mysqli_fetch_array($result); 
            $sql.="update list_record set record_new='y' where record_id =".$row1['record_id'].";";       
            $sql.="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                        worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                        values('$sports',
                        '".$row1['schedule_location']."'
                        ,'".$row1['schedule_gender']."','$name','$newrecord[$i]','"
                        .($row1['record_wind']??$row1['record_weight'])."','".$row1['record_end']."','".$row1['athlete_country']."','".$row1['record']."');";
        }
        // echo "SELECT athlete_name,schedule_location,schedule_gender,athlete_country,record_live_record,schedule_end,record_wind,record_weight 
        //                     from list_schedule 
        //                     inner JOIN list_record ON record_schedule_id = schedule_id
        //                     INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
        //                     WHERE schedule_sports = '$sports'".'<br>';    
    }else if($newrecord[$i]==='n'){
        $count++;
        if($count == count($newrecord)){
            if($sports_code==='4x400mR'||$sports_code === '4x100mR'){
            $result=$db->query("SELECT record_id
                            from list_schedule 
                            inner JOIN list_record ON schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round=record_round
                            INNER JOIN list_athlete ON athlete_country = '$name'  AND record_athlete_id = athlete_id
                            WHERE record_sports='$sports_code' AND record_gender='$gender' AND record_round='$round' AND record_group='$group'");
            while($row1=mysqli_fetch_array($result)){
                $sql.="update list_record set record_new='n' where record_id =".$row1['record_id'].";";
                }
            }else{
                $result=$db->query("SELECT record_id
                                from list_schedule 
                                inner JOIN list_record ON schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round=record_round
                                INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
                                WHERE record_sports='$sports_code' AND record_gender='$gender' AND record_round='$round' AND record_group='$group' AND if(record_status='o',record_official_result>0,record_live_result>0)");
                $row1=mysqli_fetch_array($result);
                $sql.="update list_record set record_new='n' where record_id =".$row1['record_id'].";";           
            }
        }
        $sql.="DELETE from list_worldrecord where worldrecord_athlete_name ='$name' AND worldrecord_sports='$sports' and worldrecord_athletics ='$gizone[$i]';";    
    }else{
        $sql.="update list_worldrecord set worldrecord_athletics='$newrecord[$i]' where worldrecord_athlete_name='$name' and worldrecord_sports ='$sports' and worldrecord_athletics='$gizone[$i]';";    
    }
}
echo $sql.'<br>';
//  execute multi quer
    $db->multi_query($sql);
    echo "<script>
    opener.parent.location.reload();
window.close();
    </script>";