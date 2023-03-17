<?php
require_once (__DIR__) . "/../config/env.php";
// include dirname(__DIR__) . "/config/env.php";

$host = getenv('host');
$dbname = getenv('dbname');
$user = getenv('user');
$pw = getenv('pw');

$db = new mysqli($host, $user, $pw, $dbname);
$db->set_charset("utf8");
if (!$db) {
    die("데이터베이스 연결실패" . mysqli_connect_error());
}
