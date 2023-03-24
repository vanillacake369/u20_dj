
<?php
echo "<script>";

// DB 저장된 국가값 => select-box의 국가 : selected
echo "setSelectBoxByValue('athlete_country', '" . $row["athlete_country"] . "');";

// DB 저장된 성별 => select-box의 성별 : selected
if ($row["athlete_gender"] == 'm')
    echo "document.getElementById('athlete_gender').options[0].selected=true;";
else echo "document.getElementById('athlete_gender').options[1].selected=true;";
echo "</script>";


// DB 저장된 출입가능구역 => checkbox 의 출입가능구역 : checked
$athlete_sectors = explode(',', $row["athlete_sector"]); //체크 박스
foreach ($athlete_sectors as $sec) {
    echo "<script>";
    $sc = "document.getElementById('" . $sec . "').checked = true;";
    echo $sc;
    echo "</script>";
}

echo "<script>";

// DB 저장된 참가예정경기 => checkbox 의 참가예정경기 : checked
$athlete_schedules = explode(',', $row["athlete_schedule"]); //체크 박스
foreach ($athlete_schedules as $s) {
    echo "document.getElementById('sports_$s').checked = true;";
}

// DB 저장된 참석확정경기 => checkbox 의 참석확정경기 : checked
$athlete_attendances = explode(',', $row["athlete_attendance"]); //체크 박스
foreach ($athlete_attendances as $a) {
    echo "document.getElementById('attendance_$a').checked = true;";
}
echo "</script>";
?>