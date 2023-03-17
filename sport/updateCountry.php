<?php

include __DIR__ . "/../database/dbconnect.php";
    global $db;
	$id = $_GET['countryid'];
    $code = $_POST['countryCode'];
    $name = $_POST['countryName'];
    $namekr = $_POST['countryNameKr'];

    
    $sql = " UPDATE list_country SET country_code='".$code."', country_name='".$name."', country_name_kr='".$namekr."'  WHERE country_id= '".$id."';";
    $row = $db->query($sql);
                echo "<script>alert('국가 수정되었습니다.'); location.href='../sport.php';</script>";
                exit;