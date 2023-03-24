<?php
require_once __DIR__ . "/../module/record_worldrecord.php";
$schedule= check_schedule(33,'n');
$wr=check_worldrecord(33,'n',$schedule['schedule_start']);
print_r($wr);
