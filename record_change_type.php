<?php
include_once(__DIR__ . "/database/dbconnect.php");
date_default_timezone_set('Asia/Seoul'); //timezone 설정
function gap_time($start_date, $end_date)
{
    //시간 차이 구하는 함수
    $start_time = strtotime($start_date);
    $end_time = strtotime($end_date);
    $diff = $end_time - $start_time;
    $hours = floor($diff / 3600);
    $diff = $diff - ($hours * 3600);
    $min = floor($diff / 60);
    $sec = $diff - ($min * 60);
    return array($diff, $min, $sec); //시연용 바로 통과
    // return array($hours, $min, $sec); //원래 꺼
}
$changedsports = array();
$changedgender =array();
$changedround = array();
$changedgroup = array();
$s_cnt=0; //그룹 s인거 바꾼 개수
$s_id=0; //같은 스케쥴 아이디 체크용
$result1 = $db->query("SELECT schedule_end,schedule_id,record_live_result,record_live_record,record_id,schedule_name,schedule_round,schedule_gender,schedule_group
                    from list_schedule 
                    inner join list_record on schedule_id = record_schedule_id 
                    where schedule_result = 'l'"); //live_result인 모든 스케쥴을 찾는 쿼리
while ($row = mysqli_fetch_array($result1)) {
    $check = gap_time(($row[0] ?? date("Y-m-d H:i:s")), date("Y-m-d H:i:s")); 
    // echo $row[5].': '.date("Y-m-d H:i:s").'에 끝남 '.$check[0].'시간 '.$check[1].'분 '.$check[2].'초 경과<br>';
    if ($check[0] >= 1 || ($check[1] >= 30 && $check[2] >= 0)) { //시간차이가 30분이상인지 판단
        if ($row[1]!= $s_id) {
            $s_id=$row[1];
            array_push($changedsports, $row[5]);
            array_push($changedround, $row[6]); //해당하는 것을 배열에 삽입
            array_push($changedgender, $row[7]); //해당하는 것을 배열에 삽입
            array_push($changedgroup, $row[8]); //해당하는 것을 배열에 삽입
            $s_cnt++;
        }

        $db->query("UPDATE list_schedule set schedule_result ='o' where schedule_id =".$row['schedule_id'].""); // 스케쥴 official로 전화
        // echo 'UPDATE list_record set record_official_result ="' . ($row[2]??null) . '",record_official_record=\'' . $row[3] . '\',record_status=\'o\' where record_id =' . $row[4].''.'<br>';
        $db->query('UPDATE list_record set record_official_result =\'' . ($row[2]??null) . '\',record_official_record=\'' . $row[3] . '\',record_status=\'o\' where record_id ="' . $row[4].'"'); //스케쥴에 맞는 기록을 official result로 변환
    }
    unset($check); //배열 초기화
}
$result2=$db->query("SELECT schedule_end,schedule_name,schedule_round,schedule_gender,schedule_id from list_schedule where schedule_result='l' and schedule_division='b'");
while($row1=mysqli_fetch_array($result2)){
    $result3=$db->query("SELECT * from list_schedule 
    where schedule_name='".$row1['schedule_name']."' and schedule_round='".$row1['schedule_round']."' and schedule_gender='".$row1['schedule_gender']."'
     AND (schedule_result='l' OR schedule_result='n') AND schedule_division='s'");
    //  echo "SELECT * from list_schedule 
    //  where schedule_name='".$row1['schedule_name']."' and schedule_round='".$row1['schedule_round']."' and schedule_gender='".$row1['schedule_gender']."'
    //   AND (schedule_result='l' OR schedule_result='n') AND schedule_division='s'"."<br>";
    if(mysqli_num_rows($result3) === 0){
        $check = gap_time(($row1[0] ?? date("Y-m-d H:i:s")), date("Y-m-d H:i:s")); 
        if ($check[0] >= 1 || $check[1] >= 30 && $check[2] >= 0) {
            array_push($changedsports,$row1['schedule_name']);
            array_push($changedround,$row1['schedule_round']);
            array_push($changedgender,$row1['schedule_gender']);
            $db->query("UPDATE list_schedule set schedule_result ='o' where schedule_id =".$row1['schedule_id'].""); // 스케쥴 official로 전화
        }
        unset($chcek);
    }

}
echo "<script>";
echo "alert('";
if (count($changedsports) > 0) { //변경된 종목을 출력
    for ($i = 0; $i < count($changedsports); $i++) {
        echo $changedsports[$i];
        if($changedgender[$i] ==='m'){
            echo "(남) ";
        }else if($changedgender[$i] ==='f'){
            echo "(여) ";
        }else{
            echo "(혼성) ";
        }
        echo $changedround[$i];
        if(($s_cnt-1)>=$i){
            echo ' '.$changedgroup[$i].'조';
        }else{
            echo "의 모든 조";
        }
        if ($i + 1 === count($changedsports)) {
            echo "(이)가 변경되었습니다.";
        }else{
            echo ", ";
        }
    }
} else {
    echo "변경사항이 없습니다.";
}
echo "')
location.replace(document.referrer) 
</script>";