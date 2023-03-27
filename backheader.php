<?php
require_once "database/dbconnect.php";

//include "security/security.php";
//include_once "auth/config.php";

if (!isset($_SESSION)) {
    session_start();
}

global $db;

/** @Potatoeunbi
 * 로그 남기는 함수
 *
 * @param [type] $db
 * @param [type] $id
 * @param [type] $activity
 * @param [type] $sub_activity
 * @return void
 */
function logInsert($db, $id, $activity, $sub_activity)
{

    $currentDate = date('Y-m-d H:i:s');
    // SQL INJECTION 방지(지훈 작성)
    $id = mysqli_real_escape_string($db, $id);
    $adminsql = " SELECT * FROM list_admin WHERE admin_account = '" . $id . "';";
    $adminrow = $db->query($adminsql);

    // $judgesql = " SELECT * FROM list_judge WHERE judge_account = '".$id."';";
    // $judgerow = $db->query($judgesql);

    if (($admindata = mysqli_fetch_array($adminrow))) {
        $division = "a";
        $sql = "INSERT into list_log (log_account, log_name, log_ip, log_division, log_activity,log_sub_activity,log_datetime) values (?,?,?,?,?,?,?);";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssssss", $admindata['admin_account'], $admindata['admin_name'], $admindata['admin_latest_ip'], $division, $activity, $sub_activity, $currentDate);
        $stmt->execute();
    }
    // else if($judgedata = mysqli_fetch_array($judgerow)){

    //     $division="j";
    //     $sql = "INSERT into list_log (log_account, log_name, log_ip, log_division,log_activity, log_sub_activity, log_datetime) values (?,?,?,?,?,?,?);";
    //     $stmt=$db->prepare($sql);
    //     $stmt->bind_param("sssssss",$judgedata['admin_account'], $judgedata['admin_name'],$judgedata['judge_latest_ip'],$division,$activity,$sub_activity, $currentDate);
    //     $stmt->execute();

    // }

}

/** @Potatoeunbi
 * 권한 체크 함수(수정, 삭제 권한에서 주로 쓰임)
 *
 * @param [type] $db
 * @param [type] $activity
 * @return true|false
 */
function authCheck($db, $activity)
{
    if (isset($_SESSION['Id']) &&  $_SESSION['Id'] != "")
    {
        $sql = " SELECT admin_level FROM list_admin WHERE admin_account = '" . $_SESSION['Id'] . "';";
        $row = $db->query($sql);
        $result = mysqli_fetch_array($row);

        if (!in_array($activity, explode(',', $result['admin_level']))) {
            return false;
            exit;
        } else {
            return true;
            exit;
    }
    }
    else
    {
        return false;
        exit;
    }
    
}

/** @Potatoeunbi
 * 권한 체크 함수(읽기 권한에서 주로 쓰임)
 *
 * @param [type] $db
 * @param [type] $activity
 * @return void
 */
function pageAuthCheck($db, $action)
{
    $sql = " SELECT admin_level FROM list_admin WHERE admin_account = '" . $_SESSION['Id'] . "';";
    $row = $db->query($sql);
    $result = mysqli_fetch_array($row);

    if (!in_array($action, explode(',', $result['admin_level']))) {
        echo "<script>alert('해당 권한이 없습니다.'); history.back();</script>";
        exit;
    }
}

/**
 * @author 정진호 사장님
 * 첫번째 인자로 alert문 실행 후, 두번째 인자에 따른 js 액션 실행
 *
 * @param [type] $StrMsg
 * @param [type] $StrAction
 * @param string $StrRefresh
 * @return void
 */
function AlertBox($StrMsg, $StrAction, $StrRefresh = '')
{
    global $connect;
    mysqli_close($connect);

    print "<script language='javascript'>\n";
    print "alert ('$StrMsg');\n";

    if ($StrAction == 'close') {
        print "self.close();\n";
    } elseif ($StrAction == 'reload') {
        print "location.reload();\n";
    } elseif ($StrAction == 'back') {
        print "history.back();\n";
    }

    if ($StrRefresh) {
        print "self.location.href='$StrRefresh'";
    }

    print "</script>";
}