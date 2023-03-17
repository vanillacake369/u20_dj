<?php

include_once(__DIR__ . "/../../backheader.php");


if (!isset($_POST['code']) || $_POST['code'] == '' || !isset($_POST['name']) || $_POST['name'] == '') {
    echo "<script>alert('모두 입력하세요.'); history.back();</script>";
} else {
    $code = trim($_POST['code']); //trim 공백 및 문자열 제거
    $name = trim($_POST['name']);

    $sql = "SELECT * FROM list_sports WHERE sports_code = ? or sports_name = ?"; //Select
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $code, $name);
    $stmt->execute();
    $result = $stmt->get_result();



    if (!mysqli_fetch_array($result)) {
        $sql2 = "INSERT INTO list_sports (sports_code, sports_name) VALUES(?,?)"; //Insert
        $stmt = $db->prepare($sql2);
        $stmt->bind_param('ss', $code, $name); //sql 문장의 ? 부분을 파러미터로 바인드
        $stmt->execute();
        echo "<script>alert('등록되었습니다.'); location.href='../../sport_sport_input.php';</script>";
    } else {
        echo "<script>alert('이미 등록된 경기입니다.'); history.back();</script>";
    }
}