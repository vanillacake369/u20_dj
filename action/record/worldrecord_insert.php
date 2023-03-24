<?php
    require_once __DIR__ . "/../../includes/auth/config.php";
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
    $date_day=$_POST['date_day'];
    $date=$date_year.'-'.$date_month.'-'.$date_day;
    $savesql = "insert into list_worldrecord(worldrecord_sports, worldrecord_location, worldrecord_gender,worldrecord_athlete_name,
            worldrecord_athletics,worldrecord_wind,worldrecord_datetime,worldrecord_country_code,worldrecord_record) 
            values('$sports','$location','$gender','$athletename','$athletics','$wind','$date','$athletecountry','$record')";
            $db->query($savesql);
    echo "<script>alert('등록되었습니다.');window.close()</script>";
?>