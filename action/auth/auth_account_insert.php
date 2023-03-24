<?php
include_once __DIR__ . "/../../backheader.php";

if (!isset($_POST['id']) || $_POST['id'] == "" || !isset($_POST['pw']) || $_POST['pw'] == "" || !isset($_POST['cpassword']) || $_POST['cpassword'] == "" || !isset($_POST['name']) || $_POST['name'] == "") {
    mysqli_close($db);
    echo '<script>alert("모두 입력하세요.");history.back();</script>';
    exit;
} else if ($_POST['pw'] != $_POST['cpassword']) {
    echo '<script>alert("비밀번호와 비밀번호 재확인이 다릅니다.");history.back();</script>';
    exit;
} else {

    $id = trim($_POST['id']);
    $pw = trim($_POST['pw']);
    $pw_hash = hash("sha256", $pw);
    $name = trim($_POST['name']);
    $create_time = date("Y-m-d H:i:s");
    $str = "";
    $form_data = array();

    $sql = "SELECT * from list_admin where admin_account='" . $id . "';";
    $akey = $db->query($sql);
    $sql = "SELECT * from list_judge where judge_account='" . $id . "';";
    $jkey = $db->query($sql);

    if (!mysqli_fetch_array($akey) && !mysqli_fetch_array($jkey)) {

        if (!empty($_POST['authEntrysRead']))
            array_push($form_data, $_POST['authEntrysRead']);
        if (!empty($_POST['authEntrysUpdate']))
            array_push($form_data, $_POST['authEntrysUpdate']);
        if (!empty($_POST['authEntrysDelete']))
            array_push($form_data, $_POST['authEntrysDelete']);
        if (!empty($_POST['authEntrysCreate']))
            array_push($form_data, $_POST['authEntrysCreate']);

        if (!empty($_POST['authSchedulesRead']))
            array_push($form_data, $_POST['authSchedulesRead']);
        if (!empty($_POST['authSchedulesUpdate']))
            array_push($form_data, $_POST['authSchedulesUpdate']);
        if (!empty($_POST['authSchedulesDelete']))
            array_push($form_data, $_POST['authSchedulesDelete']);
        if (!empty($_POST['authSchedulesCreate']))
            array_push($form_data, $_POST['authSchedulesCreate']);

        if (!empty($_POST['authRecordsRead']))
            array_push($form_data, $_POST['authRecordsRead']);
        if (!empty($_POST['authRecordsUpdate']))
            array_push($form_data, $_POST['authRecordsUpdate']);
        if (!empty($_POST['authRecordsDelete']))
            array_push($form_data, $_POST['authRecordsDelete']);
        if (!empty($_POST['authRecordsCreate']))
            array_push($form_data, $_POST['authRecordsCreate']);

        if (!empty($_POST['authStaticsRead']))
            array_push($form_data, $_POST['authStaticsRead']);
        if (!empty($_POST['authStaticsUpdate']))
            array_push($form_data, $_POST['authStaticsUpdate']);
        if (!empty($_POST['authStaticsDelete']))
            array_push($form_data, $_POST['authStaticsDelete']);
        if (!empty($_POST['authStaticsCreate']))
            array_push($form_data, $_POST['authStaticsCreate']);

        if (!empty($_POST['authAccountsRead']))
            array_push($form_data, $_POST['authAccountsRead']);
        if (!empty($_POST['authAccountsUpdate']))
            array_push($form_data, $_POST['authAccountsUpdate']);
        if (!empty($_POST['authAccountsDelete']))
            array_push($form_data, $_POST['authAccountsDelete']);
        if (!empty($_POST['authAccountsCreate']))
            array_push($form_data, $_POST['authAccountsCreate']);

        logInsert($db, $_SESSION['Id'], '계정 생성', $id);

        if (!empty($form_data)) {
            $form = implode(',', $form_data);

            $sql = " INSERT into list_admin (admin_account, admin_password, admin_name, admin_level, admin_datetime)  values (?,?,?,?,?);";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sssss", $id, $pw_hash, $name, $form, $create_time);
            $stmt->execute();
            echo "<script>alert('계정 생성되었습니다.'); location.href='../../account_signup.php';</script>";
            exit;
        } else {
            echo "<script>alert('해당 계정의 권한이 설정되지 않았습니다.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('해당 아이디는 이미 존재합니다.'); history.back();</script>";
        exit;
    }
}
