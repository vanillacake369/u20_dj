<?php
require_once "database/dbconnect.php";
require_once "module/judge_filter.php";
if ($_POST['userid'] != NULL) {
	$id_check = mq("select * from list_judge where judge_account='{$_POST['userid']}'");
	$id_check = $id_check->fetch_array();

	if ($id_check >= 1) {
		echo "존재하는 아이디입니다.";
	} else {
		echo "존재하지 않는 아이디입니다.";
	}
}
