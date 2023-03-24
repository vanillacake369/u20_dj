<?php
include __DIR__ . "/../database/dbconnect.php";

function secure(array $data)
{
    global $db;
    $filtered = [];
    foreach ($data as $key => $value) {
        array_push($filtered, mysqli_real_escape_string($db, $value));
    }

    return $filtered;
}


function cleanInput($input)
{
    $clean = preg_replace("![\][xX]([A-Fa-f0-9]{1,3})!", "", $input);
    $clean = substr($clean, 0, 32);
    return $clean;
}
