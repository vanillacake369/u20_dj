<?php
include_once(__DIR__ . "/../../backheader.php");

$ID = false;
$Session = false;

if (isset($_SESSION['Id']) && $_SESSION['Id'])
	$ID = addslashes($_SESSION['Id']);
if (isset($_SESSION['Session']) && $_SESSION['Session'])
	$Session = addslashes($_SESSION['Session']);

if ($ID && $Session) {
	$sqlAdmin = "SELECT * FROM list_admin WHERE admin_account='$ID'";
	$resultAdmin = $db->query($sqlAdmin);
	$RsAuthAdmin = mysqli_fetch_array($resultAdmin);

	$sqlJudge = "SELECT * FROM list_judge WHERE judge_account='$ID'";
	$resultJudge = $db->query($sqlJudge);
	$RsAuthJudge = mysqli_fetch_array($resultJudge);


	if (!$RsAuthAdmin && !$RsAuthJudge) {
		mysqli_close($db);
		echo '<script>location.replace("../../login.php");</script>';
		exit;
	}

	if ($RsAuthAdmin && $RsAuthAdmin['admin_session'] != $Session) {
		mysqli_close($db);
		echo '<script>location.replace("action/auth/logout.php");</script>';
		exit;
	}

	if ($RsAuthJudge && $RsAuthJudge['judge_session'] != $Session) {
		mysqli_close($db);
		echo '<script>location.replace("action/auth/logout.php");</script>';
		exit;
	}
} else {
	mysqli_close($db);
	echo '<script>location.replace("../../login.php");</script>';
	exit;
}