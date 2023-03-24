<?php
require_once __DIR__ . "/../database/dbconnect.php";
    global $db;
	
	$id = $_GET['countryid'];
    $sql = "DELETE FROM list_country where country_id='".$id."';";
    $row = $db->query($sql);
    echo "<script>alert('국가 삭제되었습니다.'); location.href='../sport.php';</script>";
    exit;
