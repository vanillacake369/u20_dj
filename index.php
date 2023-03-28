<?php

session_start();

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
require_once "backheader.php";

if (!isset($_SESSION['Id'])) {
    echo "<script>location.replace('login.php');</script>";
} else {
    echo "<script>location.replace('main.php');</script>";
}
