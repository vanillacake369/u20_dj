<?php
include __DIR__ . "/../database/dbconnect.php";
    global $db;
	
	$id = $_GET['scheduleid'];
    $sql = "DELETE FROM list_schedule where schedule_id='".$id."';";
    $row = $db->query($sql);
    echo "<script>alert('경기 삭제되었습니다.'); location.href='../sport.php';</script>";
    exit;
?>