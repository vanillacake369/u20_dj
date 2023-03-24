<?php
echo "<script>";
// DB 저장된 국가값 => select-box의 국가 : selected
echo "setSelectBoxByValue('judge_country', '" . $row["judge_country"] . "');";
echo "</script>";

echo "<script>";
// DB 저장된 성별 => select-box의 성별 : selected
if ($row["judge_gender"] == 'm')
    echo "document.getElementById('judge_gender').options[0].selected=true;";
else echo "document.getElementById('judge_gender').options[1].selected=true;";
echo "</script>";

echo "<script>";
// DB 저장된 직무 => select-box의 직무 : selected
$duty = $row["judge_duty"];
$duty_selected = "document.getElementById('$duty').selected=true;";
echo $duty_selected;
echo "</script>";

// DB 저장된 출입가능구역 => checkbox 의 출입가능구역 : checked
$athlete_sectors = explode(',', $row["judge_sector"]); //체크 박스
foreach ($athlete_sectors as $sec) {
    echo "<script>";
    $sc = "document.getElementById('" . $sec . "').checked = true;";
    echo $sc;
    echo "</script>";
}

// DB 저장된 참가예정경기 => checkbox 의 참가예정경기 : checked
echo "<script>";
$judge_schedules = explode(',', $row["judge_schedule"]); //체크 박스
foreach ($judge_schedules as $s) {
    echo "document.getElementById('sports_$s').checked = true;";
}
echo "</script>";

echo "<script>";
// DB 저장된 참석확정경기 => checkbox 의 참석확정경기 : checked
$judge_attendances = explode(',', $row["judge_attendance"]); //체크 박스
foreach ($judge_attendances as $a) {
    echo "document.getElementById('attendance_$a').checked = true;";
}
echo "</script>";
