<?php
include_once __DIR__ . "/../../backheader.php";
$id = trim($_POST['id']);
$str = "";
$form_data = array();

$sql = "SELECT admin_account from list_admin where admin_account='" . $id . "';";
$key = $db->query($sql);

if (mysqli_fetch_array($key)) {

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

    if (!empty($form_data)) {
        $form = implode(',', $form_data);
        $sql = "UPDATE list_admin SET admin_level=? WHERE admin_account=?;";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $form, $id);
        $stmt->execute();
        logInsert($db, $_SESSION['Id'], '계정 권한 수정', $id);
        echo "<script>alert('권한 수정되었습니다.'); location.href='../../account_user.php';</script>";
        exit;
    } else {
        echo "<script>alert('해당 계정의 권한이 설정되지 않았습니다.'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('올바르지 않은 경로입니다.'); history.back();</script>";
    exit;
}
