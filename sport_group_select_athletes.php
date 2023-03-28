<?php

// 로그 기능
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
        alert('잘못된 접근입니다.');
        history.back();
</script>");
}
require_once "includes/auth/config.php";
require_once "head.php";

function select_max_athlete_in_group(string $schedule_name)
{
    if (in_array($schedule_name, ["1500m", "3000mSC"])) {
        // 1500m, 3000m 장애물 -> 최대 인원 17명
        return 17;
    } else if (in_array($schedule_name, ["3000m", "5000m"])) {
        // 3000m, 5000m -> 최대 인원 34명
        return 34;
    } else if ($schedule_name === "10000m") {
        // 10000m -> 최대 인원 38명
        return 38;
    } else {
        // 그 이외 경기 -> 8명
        return 8;
    }
}

$sports = $_POST['sports']; //post로 받는 경기종목
$name = $sports;
$gender = $_POST['gender']; //post로 받는 성별
$round = $_POST['round']; //post로 받는 라운드
$mode = $_POST['method'] ?? NULL; // post로 받는자동이나 수동
$round_condition = ''; // 라운드 구분 조건
$gender_condition = ""; //릴레이를 위한 조건
$group = 1;
$join_condition="";
$sports_result = $db->query("SELECT sports_category from list_sports where sports_code='$sports'");
$sports_row = mysqli_fetch_assoc($sports_result);
$scheulde_result = $db->query("select * from list_record where record_sports='$sports' and record_round='$round' and record_gender='$gender'");
if (mysqli_num_rows($scheulde_result) != 0) {
    echo '<script>alert("해당 종목과 라운드에 대한 조가 이미 생성되어있습니다."); history.back();</script>';
    exit;
}
if ($sports == 'decathlon' || $sports == 'heptathlon') {
    if ($round != 'final') {
        $scheulde_result = $db->query("select * from list_record where record_sports='$sports' and record_round='final' and record_gender='$gender'");
        if (mysqli_num_rows($scheulde_result) == 0) {
            echo '<script>alert("final을 먼저 생성해주세요."); history.back();</script>';
            exit;
        }
        if ($round =='qualification' || $round == 'semi-final') {
            echo '<script>alert("예선 또는 준결승이 없는 경기입니다."); history.back();</script>';
            exit;
        }
        $sports_result = $db->query("SELECT sports_category from list_sports where sports_code='$round'");
        $sports_row = mysqli_fetch_assoc($sports_result);
        $round_condition="AND record_athlete_id=athlete_id AND record_round='final'";
        $join_condition='join list_record';
    }
}
if ($sports != '4x400mR(Mixed)') {
    $gender_condition = "and athlete_gender='$gender'";
}
if ($round == 'final') {
    if ($sports_row['sports_category'] == '트랙경기') {
        $check_round = $db->query("select schedule_round FROM list_schedule WHERE schedule_sports='$sports' and schedule_gender='$gender' ORDER BY FIELD(schedule_round,'semi-final','qualification')");
        if (mysqli_num_rows($check_round) != 0) {
            $check_round_row = mysqli_fetch_assoc($check_round);
            $check_status_result = $db->query("select distinct record_status from list_record where record_sports='$sports' and record_gender='$gender' and record_round='" . $check_round_row['schedule_round'] . "'");
            $check_status_row = mysqli_fetch_assoc($check_status_result);
            if ($check_status_row['record_status'] != 'o') {
                echo '<script>alert("이전 라운드의 결과가 Official Result일 때만 가능합니다."); history.back();</script>';
                exit;
            }
            $round_condition = "AND athlete_id = (SELECT record_athlete_id from list_record where record_sports = '$sports' and record_gender='$gender' AND (record_memo like '%Q%') or (record_memo like '%q%') AND record_round='" . $check_round_row['schedule_round'] . "')";
        }
    }
} else if ($round == 'semi-final') {
    $check_round = $db->query("select schedule_round FROM list_schedule WHERE schedule_sports='$sports' and schedule_gender='$gender' ORDER BY FIELD(schedule_round,'semi-final','qualification')");
    if (mysqli_num_rows($check_round) == 0) {
        echo '<script>alert("예선을 먼저 생성해주세요."); history.back();</script>';
        exit;
    }
    $check_round_row = mysqli_fetch_assoc($check_round);
    $check_status_result = $db->query("select distinct record_status from list_record where record_sports='$sports' and record_gender='$gender' and record_round='" . $check_round_row['schedule_round'] . "'");
    $check_status_row = mysqli_fetch_assoc($check_status_result);
    if ($check_status_row['record_status'] != 'o') {
        echo '<script>alert("이전 라운드의 결과가 Official Result일 때만 가능합니다."); history.back();</script>';
        exit;
    }
    $round_condition = "AND athlete_id = (SELECT record_athlete_id from list_record where record_sports = '$sports' and record_gender='$gender' AND (record_memo like '%Q%') or (record_memo like '%q%') AND record_round='" . $check_round_row['schedule_round'] . "')";
}
$sql = "select athlete_id,athlete_name,athlete_country,athlete_schedule from list_athlete ".$join_condition." where (athlete_schedule  like '%$sports%')" . $gender_condition . $round_condition;
$result = $db->query($sql);
$count=0;
while($row=mysqli_fetch_array($result)){
    $k= explode(',',$row['athlete_schedule']);
    if(in_array($sports,$k)){
        $count++;
    }
}

$result = $db->query($sql);
// $count = mysqli_num_rows($result);
if ($sports == 'decathlon' || $sports == 'heptathlon') {
    if ($sports_row['sports_category'] == '필드경기') {
        $groupcount = 2;
        $group=ceil($count/2);
    } else {
        $group = select_max_athlete_in_group($name);
        $groupcount = ceil($count / $group);
    }
    if($round =='final') {
        $groupcount = 1;
        $group = $count;
    }
} else {
    if ($round != 'final') {
    if ($sports_row['sports_category'] == '필드경기') {
        echo '<script>alert("해당 종목은 결승만 가능합니다."); history.back();</script>';
        exit;
    }
    if (($name == '800m' && $count <= 10)) {
        echo '<script>alert("해당 종목은 10명이하일 경우 결승만 가능합니다."); history.back();</script>';
        exit;
    }
    $group = select_max_athlete_in_group($name);
    if ($name == '4x100mR' || $name == '4x400mR' || $name == '4x400mR(Mixed)') {
        $sql = "select distinct athlete_country,athlete_schedule from list_athlete where (athlete_schedule  like '%$sports%')" . $gender_condition . $round_condition;
        $result = $db->query($sql);
        $count=0;
        while($row=mysqli_fetch_array($result)){
            $k= explode(',',$row['athlete_schedule']);
            if(in_array($sports,$k)){
                $count++;
            }
        }
        $result = $db->query($sql);
    }
    if ($count <= $group && $sports != 'decathlon' && $sports != 'heptathlon') {
        echo '<script>alert("해당 종목은 결승만 가능합니다."); history.back();</script>';
        exit;
    }
    }else{
        $group=$count;
    }
    if ($count <= 0)
    {
        echo '<script>alert("참가 선수 목록이 비었습니다."); history.back();</script>';
        exit;
    }
    else{
        $groupcount = ceil($count / $group);
    }
    
}
?>
    <script src="/assets/js/restrict.js"></script>
    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('input[name="athlete_id[]"]').change(
                function() {
                    let a = $('input[name="athlete_id[]"]:checked').length
                    $('input[name="playercount"]').attr("value", a)
                    $('input[name="groupcount"]').attr("value", Math.ceil(a / $('input[name="groupnumber"]').val()))
                }
            )
            $('input[name="groupnumber"]').keyup(
                function() {
                    let a = $('input[name="athlete_id[]"]:checked').length
                    $('input[name="playercount"]').attr("value", a)
                    $('input[name="groupcount"]').attr("value", Math.ceil(a / $('input[name="groupnumber"]').val()))
                }
            )
        })
    </script>
</head>

<body>
  <!-- contents 본문 내용 -->
  <div class="container pbottom--0">
    <!-- <div class="contents something"> -->
    <div class="something ptop--40 athlete">
      <div class="groupSelect">
        <div class="result_list2">
          <p class="tit_left_blue ">참가 선수 목록</p>
        </div>
          <form action="<?php if ($name == '4x100mR' || $name == '4x400mR' || $name == '4x400mR(Mixed)') {
              // 릴레이 경기 조 편성 페이지
              echo './sport_group_manual_relay_org.php';
          } else {
              // 릴레이 경기 이외의 조 편성 페이지
              echo './sport_group_manual_group_org.php';
          } ?>" method="post" class="form">
          <div class="groupSelect_tit">
            <ul class="headerBody">
              <li>
                <p>총 인원 </p>
              </li>
              <li>
                <p>편성 가능한 조 </p>
              </li>
              <li>
                <p>조 당 인원 </p>
              </li>
            </ul>
            <ul>
              <li>
              <input type="number" name="playercount" value="<?php echo $count; ?>" readonly>
              </li>
              <li>
              <input type="number" name="groupcount" value="<?php echo $groupcount; ?>"readonly>

              </li>
              <li>
              <input type="number" name="groupnumber" value="<?php echo $group; ?>">

              </li>
            </ul>
          </div>
          <div class="grouptSelectList">
            <?php
                        while ($row = mysqli_fetch_array($result)) {
                            if(in_array($sports,explode(',',$row['athlete_schedule']))){
                                echo '<div>';
                                if ($name == '4x100mR' || $name == '4x400mR' || $name == '4x400mR(Mixed)') {
                                    echo '<label><input type="checkbox" name=athlete_id[] value="' . $row['athlete_country'] . '"checked/>';
                                    echo '국가: ' . $row['athlete_country'] . '</label></div>';
                                } else {
                                    echo '<label><input type="checkbox" name=athlete_id[] value="' . $row['athlete_id'] . '"checked/>';
                                    echo '이름: ' . $row[1] . '</label></div>';
                                }                     
                            }
                        }
                        ?>
                        <input type="hidden" name='mode' value='<?php echo $mode ?>'>
                        <input type="hidden" name='sports' value='<?php echo $sports ?>'>
                        <input type="hidden" name='round' value='<?php echo $round ?>'>
                        <input type="hidden" name='gender' value='<?php echo $gender ?>'>
          </div>
          <div class=" signup_submit">
            <button type="submit" class="btn_login" name="signup">
              <span>확인</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="assets/js/main.js"></script>
</body>

</html>