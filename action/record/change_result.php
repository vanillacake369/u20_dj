<?php
include __DIR__ . "/../../database/dbconnect.php";
// include_once(__DIR__ . "auth/config.php";
include_once(__DIR__ . "/../../security/security.php");
$name=$_POST['name'];
$sports=$_POST['sports'];
$gizone=$_POST['gizone'];
$newrecord=$_POST['newrecord'];
$id=$_POST['schedule_id'];
$count=0; // 신기록 모두 삭제 시 필요한 카운트
$sql="";
$check=false;
for($i=0;$i<count($newrecord);$i++){
    if($gizone[$i]==='n'){
        if($sports==='4x400mR'||$sports === '4x100mR'){
            $result=$db->query("SELECT record_id,schedule_location,schedule_gender,athlete_country,record_live_record,schedule_end,record_wind,record_weight 
                            from list_schedule 
                            inner JOIN list_record ON record_schedule_id = schedule_id
                            INNER JOIN list_athlete ON athlete_country = '$name'  AND record_athlete_id = athlete_id
                            WHERE schedule_id = '$id'");
            while($row1=mysqli_fetch_array($result)){
                $sql.="update list_record set record_new='y' where record_id =".$row1['record_id'].";";
            if($check===false){
                $sql.="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                    worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                    values('$sports',
                    '".$row1['schedule_location']."'
                    ,'".$row1['schedule_gender']."','$name','$newrecord[$i]','"
                    .($row1['record_wind']??$row1['record_weight'])."','".$row1['schedule_end']."','".$row1['athlete_country']."','".$row1['record_live_record']."');";
                    $check=true;
                } 
            }       
        }else{
            $result=$db->query("SELECT record_id,schedule_location,schedule_gender,athlete_country,record_live_record,schedule_end,record_wind,record_weight 
            from list_schedule 
            inner JOIN list_record ON record_schedule_id = schedule_id
            INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
            WHERE schedule_id = '$id' and record_live_record NOT IN('X','-')
            ORDER BY record_live_record desc");   
            $row1=mysqli_fetch_array($result); 
            $sql.="update list_record set record_new='y' where record_id =".$row1['record_id'].";";       
            $sql.="insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
                        worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
                        values('$sports',
                        '".$row1['schedule_location']."'
                        ,'".$row1['schedule_gender']."','$name','$newrecord[$i]','"
                        .($row1['record_wind']??$row1['record_weight'])."','".$row1['schedule_end']."','".$row1['athlete_country']."','".$row1['record_live_record']."');";
        }
        // echo "SELECT athlete_name,schedule_location,schedule_gender,athlete_country,record_live_record,schedule_end,record_wind,record_weight 
        //                     from list_schedule 
        //                     inner JOIN list_record ON record_schedule_id = schedule_id
        //                     INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
        //                     WHERE schedule_sports = '$sports'".'<br>';    
    }else if($newrecord[$i]==='n'){
        $count++;
        if($count == count($newrecord)){
            if($sports==='4x400mR'||$sports === '4x100mR'){
            $result=$db->query("SELECT record_id
                            from list_schedule 
                            inner JOIN list_record ON record_schedule_id = schedule_id
                            INNER JOIN list_athlete ON athlete_country = '$name'  AND record_athlete_id = athlete_id
                            WHERE schedule_id = '$id'");
            while($row1=mysqli_fetch_array($result)){
                $sql.="update list_record set record_new='n' where record_id =".$row1['record_id'].";";
                }
            }else{
                $result=$db->query("SELECT record_id
                                from list_schedule 
                                inner JOIN list_record ON record_schedule_id = schedule_id
                                INNER JOIN list_athlete ON athlete_name = '$name'  AND record_athlete_id = athlete_id
                                WHERE schedule_id = '$id'");
                $row1=mysqli_fetch_array($result);
                $sql.="update list_record set record_new='n' where record_id =".$row1['record_id'].";";           
            }
        }
        $sql.="DELETE from list_worldrecord where worldrecord_athlete_name ='$name' AND worldrecord_sports='$sports' and worldrecord_athletics ='$gizone[$i]';";    
    }else{
        $sql.="update list_worldrecord set worldrecord_athletics='$newrecord[$i]' where worldrecord_athlete_name='$name' and worldrecord_sports ='$sports' and worldrecord_athletics='$gizone[$i]';";    
    }
}
echo $sql;
//  execute multi quer
    $db->multi_query($sql);
    echo "<script>
    opener.parent.location.reload();
window.close();
    </script>";  
?>