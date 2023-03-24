<?php
echo "<script>";

// DB 저장된 국가값 => select-box의 국가 : selected
echo "setSelectBoxByValue('director_country', '" . $row["director_country"] . "');";

// DB 저장된 성별 => select-box의 성별 : selected
if ($row["director_gender"] == 'm')
    echo "document.getElementById('director_gender').options[0].selected=true;";
else echo "document.getElementById('director_gender').options[1].selected=true;";


// DB 저장된 직무 => select-box의 직무 : selected
$duty = $row["director_duty"];
$duty_selected = "document.getElementById('$duty').selected=true;";
echo $duty_selected;

// DB 저장된 출입가능구역 => checkbox 의 출입가능구역 : checked
$athlete_sectors = explode(',', $row["director_sector"]); //체크 박스
foreach ($athlete_sectors as $sec) {
    $sc = "document.getElementById('" . $sec . "').checked = true;";
    echo $sc;
    echo "</script>";
}

// // DB 저장된 참가예정경기 => checkbox 의 참가예정경기 : checked
// echo "<script>";
// $director_schedules = explode(',', $row["director_schedule"]); //체크 박스
// foreach ($director_schedules as $s) {
//     echo "document.getElementById('sports_$s').checked = true;";
// }

// // DB 저장된 참석확정경기 => checkbox 의 참석확정경기 : checked
// $director_attendances = explode(',', $row["director_attendance"]); //체크 박스
// foreach ($director_attendances as $a) {
//     echo "document.getElementById('attendance_$a').checked = true;";
// }
// echo "</script>";