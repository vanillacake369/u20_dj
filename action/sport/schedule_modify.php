<?php
require_once __DIR__ . "/../../backheader.php";


if (!isset($_POST['sports']) || $_POST['sports'] == "" || !isset($_POST['name']) || $_POST['name'] == "" || !isset($_POST['gender']) || $_POST['gender'] == "non" || !isset($_POST['round']) || $_POST['round'] == "" || !isset($_POST['location']) || $_POST['location'] == "" || !isset($_POST['start_hour']) || $_POST['start_hour'] == "" || !isset($_POST['start_minute']) || $_POST['start_minute'] == "" || !isset($_POST['result']) || $_POST['result'] == "non" || !isset($_POST['date_year']) || $_POST['date_year'] == "non" || !isset($_POST['date_month']) || $_POST['date_month'] == "non" || !isset($_POST['date_day']) || $_POST['date_day'] == "non") {
    mysqli_close($db);
    echo '<script>alert("모두 입력하세요.");history.back();</script>';
    exit;
} else {
    $id = trim($_POST['id']);
    $sports = trim($_POST['sports']);
    $name = trim($_POST['name']);

    $sql = "SELECT * FROM list_sports WHERE sports_code='" . $sports . "'";
    // $sql="SELECT * FROM list_sports WHERE sports_code='".$sports."' AND  sports_name_kr='".$name."'";
    $key = $db->query($sql);

    if (mysqli_fetch_array($key)) {

        $gender = trim($_POST['gender']);
        $round = trim($_POST['round']);
        $location = trim($_POST['location']);
        $start_hour = trim($_POST['start_hour']);
        $start_minute = trim($_POST['start_minute']);
        $result = trim($_POST['result']);
        $date_year = trim($_POST['date_year']);
        $date_month = trim($_POST['date_month']);
        $date_day = trim($_POST['date_day']);


        $date = $date_year . "-" . $date_month . "-" . $date_day . " " . $start_hour . ":" . $start_minute . ":00";
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d h:i:s');

        $start = $date_year . "-" . $date_month . "-" . $date_day . " " . $start_hour . ":" . $start_minute . ":00";
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $start)->format('Y-m-d h:i:s');
        $sql = "SELECT schedule_id from list_schedule where schedule_sports='" . $sports . "' and schedule_name='" . $name . "' and schedule_gender='" . $gender . "' and schedule_round='" . $round . "';";
        $key = $db->query($sql);

        if (mysqli_fetch_array($key)) {
            $sql = "UPDATE list_schedule SET schedule_sports = ?, schedule_name=?, schedule_gender=?, schedule_round=?, schedule_location=?, schedule_start=?, schedule_result=?,schedule_date=?  WHERE schedule_id = ?;";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sssssssss", $sports, $name, $gender, $round, $location, $start, $result, $date, $id);
            $stmt->execute();
            logInsert($db, $_SESSION['Id'], '일정 수정', $sports . "-" . $name . "-" . $gender . "-" . $round);
            echo "<script>alert('일정 수정되었습니다.'); window.close();</script>";
            exit;
        } else {
            echo "<script>alert('잘못된 경로입니다.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('해당 경기 종목은 존재하지 않습니다.'); history.back();</script>";
        exit;
    }
}
