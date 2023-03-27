<?php
//일반 필드 경기용
require_once __DIR__ . "/../module/record_worldrecord.php";
require_once __DIR__ . "/../../includes/auth/config.php";
date_default_timezone_set('Asia/Seoul'); //timezone 설정
global $db;
$athlete_name = $_POST['playername'];
$tempstore = $_POST['tempstore'];
$round = $_POST['round'];
$gender = $_POST['gender'];
$name = $_POST['sports'];
$heat = $_POST['group'];
$weight = $_POST['weight'];
$result = $_POST['rank'];
$record = $_POST['gameresult'];
$memo = $_POST['bigo'];
$rane = $_POST['rain'];
$comprecord = $_POST['compresult'];
// echo $gender." ".$round." ".$heat." ".$name.'<br>';
$judge_name = $_POST['refereename'];
$judgeresult = $db->query("select judge_id from list_judge where judge_name='$judge_name'"); //심판 아이디 쿼리
$judge = mysqli_fetch_array($judgeresult);
$new = 'n';
$res1 = $db->query("SELECT * FROM list_schedule 
    join list_record
    where record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_sports=record_sports AND schedule_gender=record_gender AND schedule_round =record_round");
$row1 = mysqli_fetch_array($res1);
if ($name === 'decathlon' || $name === 'heptathlon') {
    $check_round = 'y';
    $totalrow = 'record_sports="' . $row1['schedule_sports'] . '" and record_gender="$gender" and record_round="final"';
    $trialcnt = 4;
} else {
    $check_round = 'n';
    $trialcnt = 7;
}
$starttime = $_POST['starttime'];
// $db->query("update list_schedule set schedule_start ='".$starttime."' where schedule_sports='$name' and schedule_gender='$gender' and shcedule_round='$round'");
$db->query("update list_record set record_start ='" . $starttime . "' where record_sports='$name' and record_gender='$gender' and record_round='$round' and record_group='$heat'");
if ($row1['record_state'] === 'o') { //schedule_result에 따른 수정 및 저장 주체
    $result_type1 = 'official';
    $result_type2 = 'o';
} else {
    $result_type1 = 'live';
    $result_type2 = 'l';
}
$fieldrecord = [$_POST["gameresult1"], $_POST["gameresult2"], $_POST["gameresult3"], ($_POST["gameresult4"] ?? null), ($_POST["gameresult5"] ?? null), ($_POST["gameresult6"] ?? null)];
for ($i = 0; $i < count($athlete_name); $i++) {
    $tempmemo = '';
    $highrecord = 0;
    $hightrial = 0;
    $medal = 0;
    $re = $db->query("SELECT athlete_id,athlete_country FROM list_athlete join list_record on record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' and athlete_name = '" . $athlete_name[$i] . "' and record_athlete_id=athlete_id");
    $row = mysqli_fetch_array($re);
    $newre = $db->query("select record_new,record_multi_record from list_record where record_athlete_id ='" . $row['athlete_id'] . "' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_" . $result_type1 . "_result>0");
    $rerow = mysqli_fetch_array($newre);
    for ($j = 0; $j < $trialcnt; $j++) {
        $new = 'n';
        $plus = '';
        if ($j < $trialcnt - 1) {
            if ($fieldrecord[$j][$i] == "X") {
                $pass = 'd';
            } else if ($fieldrecord[$j][$i] == "-") {
                $pass = 'w';
            } else {
                $pass = 'p';
            }
        }
        if ($j + 1 == $trialcnt && $tempstore == '0') {
            if ($round == 'final') {
                switch ($result[$i]) {
                    case 1:
                        $medal = 10000;
                        break;
                    case 2:
                        $medal = 100;
                        break;
                    case 3:
                        $medal = 1;
                        break;
                    default:
                        $medal = 0;
                        break;
                }
            }
            if ($row1['record_state'] === 'y') {
                $new = $rerow[0];
            }
            if(strpos($memo[$i],'참고 기록')!==TRUE){   
                if ($comprecord[$i] != $highrecord) { //기존 기록과 변경된 기록이 같은 지 비교
                    $memo[$i] = changePbSb($row[0], $highrecord, $name, $gender, $round, $memo[$i], $check_round, 'f');
                    if ($row1['record_state'] === 'y') { //경기가 끝났는 지 판단
                        if ($rerow[0] === 'y') {
                            $arr = modify_worldrecord($athlete_name[$i], $row[1], $highrecord, $weight, $name, $gender, $round, $check_round);
                            $tempmemo = change_worldrecord_dec($athlete_name[$i], $row[1], $highrecord, $weight, $name, $gender, $round, $check_round, $arr);
                        } else {
                            $arr2 = insert_worldrecord_dec($athlete_name[$i], $row[1], $highrecord, $weight, $name, $gender, $round, $check_round);
                            $tempmemo = $arr2[0];
                            $new = $arr2[1];
                        }
                    } else {
                        $arr2 = insert_worldrecord_dec($athlete_name[$i], $row[1], $highrecord, $weight, $name, $gender, $round, $check_round);
                        $tempmemo = $arr2[0];
                        $new = $arr2[1];
                    }
                }
            }
            if ($tempmemo != '') {
                if (strlen($memo[$i]) >= 1) {
                    $memo[$i] = $memo[$i] . "," . $tempmemo;
                } else {
                    $memo[$i] = $tempmemo;
                }
            }
            if ($name === 'decathlon' || $name === 'heptathlon') {
                if ($round === 'discusthrow') {
                    $point = (int)(12.91 * pow(((float)$highrecord - 4), 1.1)); //discusthrow
                } else if ($round === 'javelinthrow') {
                    if ($row1['schedule_gender'] === 'm') {
                        $point = (int)(10.14 * pow(((float)$highrecord - 7), 1.08)); //javelinthrow
                    } else {
                        $point = (int)(15.9803 * pow(((float)$highrecord - 3.8), 1.04)); //javelinthrow
                    }
                } else if ($round === 'shotput') {
                    if ($row1['schedule_gender'] === 'm') {
                        $point = (int)(51.39 * pow(((float)$highrecord - 1.5), 1.05)); //shotput
                    } else {
                        $point = (int)(56.0211 * pow(((float)$highrecord - 1.5), 1.05)); //shotput
                    }
                }
                if ($row1['record_state'] != 'y') {
                    $db->query("UPDATE list_record set record_" . $result_type1 . "_record=record_" . $result_type1 . "_record+$point where record_athlete_id ='" . $row['athlete_id'] . "' AND $totalrow");
                } else {
                    $db->query("UPDATE list_record set record_" . $result_type1 . "_record=record_" . $result_type1 . "_record-$rerow[1]+$point where record_athlete_id ='" . $row['athlete_id'] . "' AND $totalrow");
                }
                $plus = ",record_multi_record='" . $point . "'";
            }
            $savequery = "UPDATE list_record SET record_" . $result_type1 . "_result='$result[$i]',record_judge='$judge[0]',
                record_new='$new',record_medal=" . $medal . ",record_status='" . $result_type2 . "'" . $plus . ",record_memo='" . $memo[$i] . "'
                WHERE record_athlete_id ='" . $row['athlete_id'] . "' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_" . $result_type1 . "_record='$highrecord' and record_trial='$hightrial'";
        } else {
            $k = $j + 1;
            $ruf = ($fieldrecord[$j][$i] ?? 0);
            $savequery = "UPDATE list_record SET record_pass='$pass',record_judge='$judge[0]',
                record_" . $result_type1 . "_record='$ruf', record_new='$new',record_memo='" . $memo[$i] . "' ,record_medal=" . $medal . "
                ,record_weight='$weight',record_status='" . $result_type2 . "' WHERE record_athlete_id ='" . $row['athlete_id'] . "' AND record_sports= '$name' AND record_round= '$round' AND record_gender='$gender' AND record_group = '$heat' AND record_trial='$k'";
            if ($highrecord < $ruf && $ruf != 'X') {
                $highrecord = $ruf;
                $hightrial = $k;
            }
        }
        // echo $savequery.'<br>';
        mysqli_query($db, $savequery);
    }
}
if ($row1['record_status'] != 'o' && $tempstore == '0') {
    $finishcnt = 0;
    $db->query("UPDATE list_record set record_end='" . date("Y-m-d H:i:s") . "',record_state='y' where record_sports= '$name' AND record_round= '$round' AND record_group='$heat' AND record_gender='$gender'"); // 경기 종료 스케쥴에 반영
    $db->query("UPDATE list_schedule set schedule_memo='" . $_POST['bibigo'] . "' where schedule_sports= '$name' AND schedule_round= '$round' AND schedule_gender='$gender'"); // 경기 종료 스케쥴에 반영
}
if ($row1['record_state'] != 'y') {
    logInsert($db, $_SESSION['Id'], '기록 등록', $name . "-" . $row1['record_gender'] . "-" . $round . "-" . $row1['record_group']);
} else {
    logInsert($db, $_SESSION['Id'], '기록 수정', $name . "-" . $row1['record_gender'] . "-" . $round . "-" . $row1['record_group']);

}
// echo "<script>
// opener.parent.location.reload();
// window.close(); 
//     location.replace('../../record/field_normal_result_view.php?id=".$s_id."') 
//     </script>";