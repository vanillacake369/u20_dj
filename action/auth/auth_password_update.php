<?php
include_once __DIR__ . "/../../backheader.php";
if (!isset($_POST['pw']) || $_POST['pw'] == "" || !isset($_POST['cpassword']) || $_POST['cpassword'] == "") {
    echo "<script>alert('모두 입력하세요.'); history.back();</script>";
    exit;
} else if ($_POST['pw'] != $_POST['cpassword']) {
    echo "<script>alert('비밀번호와 비밀번호 확인이 다릅니다.'); history.back();</script>";
    exit;
} else {
    $id = $_SESSION['Id'];
    $pw_hash = hash("sha256", $_POST['pw']);
    $update_time = date("Y-m-d H:i:s");

    $sql = "UPDATE list_admin SET admin_password = ?, admin_password_datetime=? WHERE admin_account = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $pw_hash, $update_time, $id);
    $stmt->execute();
    logInsert($db, $id, '계정 비밀번호 변경', $id);
    echo "<script>alert('수정되었습니다.'); location.href='../../account_change_pw.php';</script>";
    exit;
}
