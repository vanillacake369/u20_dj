<?php
    require_once __DIR__ . "/../../includes/auth/config.php";
    //기존 정보
$old_sports=$_POST['old_sports'];
$old_datetime=$_POST['old_datetime'];
$old_country_code=$_POST['old_country_code'];
$old_record=$_POST['old_record'];
$old_athletics=$_POST['old_athletics'];
$old_athlete_name=$_POST['old_athlete_name'];
$old_location=$_POST['old_location'];
$old_gender=$_POST['old_gender'];
$old_wind=$_POST['old_wind'];
 //새로운 정보 
    $sports=$_POST['sports']; 
    $gender=$_POST['gender']; 
    $athletics=$_POST['athletics'];
    $athletename=$_POST['athletename']; 
    $athletecountry=$_POST['athletecountry']; 
    $record=$_POST['record'];
    $location=$_POST['location']; 
    $wind=$_POST['wind']; 
    $date_year=$_POST['date_year'];
    $date_month=$_POST['date_month']; 
    $date_day=$_POST['date_day']; print_r($_POST);
    $date=$date_year.'-'.$date_month.'-'.$date_day; 
    $savesql="UPDATE list_worldrecord set 
    worldrecord_sports='$sports',
    worldrecord_location='$location',
    worldrecord_gender='$gender',
    worldrecord_athlete_name='$athletename',
    worldrecord_athletics='$athletics',
    worldrecord_wind='$wind',
    worldrecord_datetime='$date',
    worldrecord_country_code='$athletecountry',
    worldrecord_record='$record'
    where 
    worldrecord_sports='$old_sports' and
    worldrecord_location='$old_location' and
    worldrecord_gender='$old_gender' and
    worldrecord_athlete_name='$old_athlete_name' and
    worldrecord_athletics='$old_athletics' and
    worldrecord_wind='$old_wind' and
    worldrecord_datetime='$old_datetime' and
    worldrecord_country_code='$old_country_code' and
    worldrecord_record='$old_record'
    ";
    $db->query($savesql);
echo "<script>alert('수정되었습니다.');
opener.parent.location.reload();
window.close();</script>";
?>