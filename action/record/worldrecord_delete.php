<?php
    require_once __DIR__ . "/../../includes/auth/config.php";
    //기존 정보
    $sports=$_POST['sports'];
    $record=$_POST['record'];
    $datetime=$_POST['datetime'];
    $country_code=$_POST['country_code'];
    $athletics=$_POST['athletics'];
    $athlete_name=$_POST['athlete_name'];
    $location=$_POST['location'];
    $gender=$_POST['gender'];
    $wind=$_POST['wind'];
    $savesql="DELETE FROM list_worldrecord
    where 
    worldrecord_sports='$sports' and
    worldrecord_location='$location' and
    worldrecord_gender='$gender' and
    worldrecord_athlete_name='$athlete_name' and
    worldrecord_athletics='$athletics' and
    worldrecord_wind='$wind' and
    worldrecord_datetime='$datetime' and
    worldrecord_country_code='$country_code' and
    worldrecord_record='$record'
    ";
    $db->query($savesql);
    // alert('삭제되었습니다.');
echo "<script>
location.replace(document.referrer);</script>";
?>