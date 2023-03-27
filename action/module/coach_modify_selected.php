<?php
echo "<script>";
// DB 저장된 국가값 => select-box의 국가 : selected
echo "setSelectBoxByValue('coach_country', '" . strtoupper($row["coach_country"]) . "');";
echo "</script>";

// DB 저장된 성별 => select-box의 성별 : selected
echo "<script>";
if ($row["coach_gender"] == 'm')
    echo "document.getElementById('coach_gender').options[0].selected=true;";
else echo "document.getElementById('coach_gender').options[1].selected=true;";
echo "</script>";

// DB 저장된 직무 => select-box의 직무 : selected
echo '<script>';
$duty = $row["coach_duty"];
$duty_selected = "document.getElementById('$duty').selected=true;";
echo $duty_selected;
echo '</script>';

// DB 저장된 출입가능구역 => checkbox 의 출입가능구역 : checked
$coach_sectors = explode(',', $row["coach_sector"]); //체크 박스
foreach ($coach_sectors as $sec) {
    echo "<script>";
    $sc = "document.getElementById('" . $sec . "').checked = true;";
    echo $sc;
    echo "</script>";
}

echo "<script>";
echo 'if (document.querySelectorAll(\'.coach_sector>div>label>input[name="coach_sector[]"]\')) {

    const allow_access = document.querySelectorAll(\'.coach_sector>div>label>input[name="coach_sector[]"]\');
    let checkcnt =  document.querySelectorAll(\'.coach_sector>div>label>input[name="coach_sector[]"]:checked\').length;
  
    for (let i = 0; i < allow_access.length; i++) {
      if (allow_access[i].checked)
        checkcnt++;
    }
  
    for (let i = 0; i < allow_access.length; i++) {
        allow_access[i].addEventListener("click", () => {
            if (allow_access[i].checked)  {
                checkcnt++;
            } else{
                checkcnt--;
            }
            if (checkcnt > 4) {
                allow_access[i].checked = false;
                checkcnt--;
                alert("5개 이상 선택이 불가능합니다");
            }
        })
        }
  }';
echo "</script>";

// DB 저장된 참가예정경기 => checkbox 의 참가예정경기 : checked
// echo "<script>";
// $coach_schedules = explode(',', $row["coach_schedule"]); //체크 박스
// foreach ($coach_schedules as $s) {
//     echo "document.getElementById('sports_$s').checked = true;";
// }

// DB 저장된 참석확정경기 => checkbox 의 참석확정경기 : checked
// $coach_attendances = explode(',', $row["coach_attendance"]); //체크 박스
// foreach ($coach_attendances as $a) {
//     echo "document.getElementById('attendance_$a').checked = true;";
// }
// echo "</script>";