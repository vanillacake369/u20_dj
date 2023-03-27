<?php
require_once __DIR__ . "/../../backheader.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
if (!isset($_POST['sports']) || $_POST['sports'] == "" || 
    !isset($_POST['round']) || $_POST['round'] == "" || 
    !isset($_POST['gender']) || $_POST['gender'] == "non" ||  
    !isset($_POST['location']) || $_POST['location'] == "" || 
    !isset($_POST['start_hour']) || $_POST['start_hour'] == "non" || 
    !isset($_POST['start_minute']) || $_POST['start_minute'] == "non" || 
    !isset($_POST['date_year']) || $_POST['date_year'] == "non" || 
    !isset($_POST['date_month']) || $_POST['date_month'] == "non" || 
    !isset($_POST['date_day']) || $_POST['date_day'] == "non")
 {
    mysqli_close($db);
    echo '<script>alert("모두 입력하세요.");history.back();</script>';
    exit;
} else {
    $sports = trim($_POST['sports']);

    $sql_search = "SELECT sports_name FROM list_sports WHERE sports_code = '$sports'";
    $result_search = $db->query($sql_search);
    $row_search = mysqli_fetch_array($result_search);

    $sql = "SELECT * FROM list_record";

// -- and sports_code='" . $sports . "'"


// $sql="SELECT * FROM list_sports WHERE sports_code='".$sports."' AND  sports_name_kr='".$name."'";
$key = $db->query($sql);

if (mysqli_fetch_array($key)) {
    
        $name = trim($row_search['sports_name']);
        $gender = trim($_POST['gender']);
        $round = trim($_POST['round']);
        $location = trim($_POST['location']);
        $start_hour = trim($_POST['start_hour']);
        $start_minute = trim($_POST['start_minute']);
        $date_year = trim($_POST['date_year']);
        $date_month = trim($_POST['date_month']);
        $date_day = trim($_POST['date_day']);

        $db->query("update list_record set record_judge=1");
        $checkresult=$db->query("SELECT * FROM list_record WHERE record_sports='$sports' AND record_round='$round' AND record_gender='$gender'");
        if(mysqli_num_rows($checkresult)==0){
            echo "<script>alert('해당 경기에 대한 조가 없습니다.'); history.back();</script>";     
            exit;
        }
        // $start = date("Y-m-d H:i:s");
        $date = $date_year . "-" . $date_month . "-" . $date_day;
        // var_dump($date);
        $start = $date_year . "-" . $date_month . "-" . $date_day . " " . $start_hour . ":" . $start_minute . ":00";
        //$start = DateTime::createFromFormat('Y-m-d H:i:s', $start)->format('Y-m-d h:i:s');
        $sql = "SELECT COUNT(*) as cnt from list_schedule where schedule_sports='" . $sports . "'  and schedule_gender='" . $gender . "' and schedule_round='" . $round . "';";
        $key = $db->query($sql);
        $row_key = mysqli_fetch_array($key);
        //echo ($sql);
        if ($row_key['cnt'] == 0) {
            $sql = " INSERT into list_schedule (schedule_sports, schedule_name, schedule_gender, schedule_round, schedule_location, schedule_start, schedule_date) values (?,?,?,?,?,?,?);";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sssssss", $sports, $name, $gender, $round, $location, $start, $date);
            $stmt->execute();
            logInsert($db, $_SESSION['Id'], '일정 생성', $sports . "-" . $name . "-" . $round);
            echo "<script>alert('일정 생성되었습니다.'); opener.parent.location.reload(); window.close(); </script>";
            exit;
        } else {
            echo "<script>alert('해당 일정은 이미 존재합니다.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('조편성을 해주십시오.'); history.back();</script>";
        exit;
    }
}