<?php
session_start();
require_once __DIR__ . "/../../backheader.php";

$db->set_charset("utf8");
if (!$db) {
    die("데이터베이스 연결실패" . mysqli_connect_error());
}

if (!isset($_POST['id']) || $_POST['id'] == "") {
    mysqli_close($db);
    echo '<script>alert("아이디를 입력하세요");history.back();</script>';
    exit;
} else if (!isset($_POST['pw']) || $_POST['pw'] == "") {
    mysqli_close($db);
    echo '<script>alert("비밀번호를 입력하세요");history.back();</script>';
    exit;
} else {

    $id = trim($_POST['id']);
    $pw = trim($_POST['pw']);
    $session = md5(time() . $id);
    $_SERVER['REMOTE_ADDR'] == '::1' ? $ip_add = '127.0.0.1' : $ip_add = $_SERVER['REMOTE_ADDR'];
    $currentDate = date('Y-m-d H:i:s');
    $activity = "로그인";

    $adminsql = " SELECT * FROM list_admin WHERE admin_account = '" . $id . "';";
    $adminrow = $db->query($adminsql);

    $judgesql = " SELECT * FROM list_judge WHERE judge_account = '" . $id . "';";
    $judgerow = $db->query($judgesql);


    if (($admindata = mysqli_fetch_array($adminrow)) && (hash('sha256', $pw) == $admindata['admin_password'])) {

        $sql = "UPDATE list_admin SET admin_latest_datetime = ?, admin_latest_ip= ?, admin_session = ? WHERE admin_account = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $currentDate, $ip_add, $session, $id);
        $stmt->execute();

        // logInsert($db, $id, $activity, '');
        mysqli_close($db);
        $_SESSION['Id'] = $id;
        $_SESSION['Session'] = $session;

        echo "<script>alert('로그인되었습니다.'); location.href='../../index.php';</script>";
        exit;
    } else {
        mysqli_close($db);
        echo "<script>alert('아이디 혹은 비밀번호를 확인하세요.'); history.back();</script>";
        exit;
    }
}