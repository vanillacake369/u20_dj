<?php
require_once __DIR__ . "/head.php";
require_once __DIR__ . "/includes/auth/config.php";
require_once __DIR__ . "/security/security.php";
require_once __DIR__ . "/action/module/dictionary.php";
global $db, $categoryOfSports_dic;

if (!isset($_GET['sports'], $_GET['gender'], $_GET['round'])) {
  // GET 유효성 검사
  mysqli_close($db);
  exit("<script>alert('잘못된 경로입니다.');  window.close();</script>");
} else if (strtolower($_GET['round']) === 'final') {
  // 라운드가 결승이면 블로킹
  mysqli_close($db);
  exit("<script>alert('Final 경기는 다음 라운드 조 편성을 할 수 없습니다.');  window.close();</script>");
} else if (!isset($categoryOfSports_dic[$_GET['sports']]) || $categoryOfSports_dic[$_GET['sports']] != "트랙경기") {
  // sports_code가 list_sports에 없거나, 트랙경기가 아니면 블로킹
  mysqli_close($db);
  exit("<script>alert('유효하지 않은 Sports 이거나 트랙경기가 아닌 Sports입니다.');  window.close();</script>");
}

$sports = $_GET['sports'];
$round = $_GET['round'];
$gender = cleanInput($_GET['gender']);
// 경기 상태 확인하는 query
$sql = "SELECT record_status, record_state FROM list_record WHERE record_sports= ? AND record_gender=? AND record_round=? GROUP BY record_status, record_state";
$stmt = $db->prepare($sql);
$stmt->bind_param("sss", $sports, $gender, $round);
$stmt->execute();
$row = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
if (count($row) !== 1) {
    // 경기 중일 때 (sports가 record_status, record_state가 같지 않을 때)
    mysqli_close($db);
    exit("<script>alert('경기 중에는 다음 라운드 조 편성을 할 수 없습니다.');  window.close();</script>");
} else if ((($row[0]["record_status"] != 'o') || $row[0]["record_state"] != 'y')) {
    // 경기가 official reocrd가 아니거나 마감(y)이 안되어 있을 때
    mysqli_close($db);
     exit("<script>alert('경기가 시작이 되지 않았거나, 경기 중에는 다음 라운드 조 편성을 할 수 없습니다.');  window.close();</script>");
}
?>
<script src="/assets/js/jquery-1.12.4.min.js"></script>
<script src="/assets/js/restrict.js"></script>
</head>

<body>
  <div class="container">
    <div class="schedule">
      <div class="schedule_filed">
        <div class="schedule_filed_tit">
          <p class="tit_left_blue">조 편성 하기</p>
        </div>
        <div class="schedule_change_members">
        <form action="/action/sport/sport_create_next_round_group.php" method="post" class="form">
                    <input type="hidden" name="sports" value="<?php echo $sports ?>">
                    <input type="hidden" name="current_round" value="<?php echo $round ?>">
                    <input type="hidden" name="gender" value="<?php echo $gender ?>">
                    <select name="next_round" class="d_select">
                        <option value="" hidden="">라운드</option>
                        <?php
                        // break문을 걸지 않아 해당 라운드에 나와야하는 라운드가 나옴
                        switch ($round) {
                            case 'preliminary-round':
                                echo '<option value="qualification">예선</option>';
                            case 'qualification':
                                echo '<option value="semi-final">준결승</option>';
                            case 'semi-final':
                                echo '<option value="final">결승</option>';
                        }
                        ?>
                    </select>
                    <div class="schedule_filed_tit round_tit">
                        <p class="tit_left_green">조 갯수</p>
                    </div>
                    <select name="group_count" class="d_select">
                        <option value="" hidden="">조 갯수</option>
                        <option value="non" hidden="">조 개수 선택</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    </select>
                    <div class="schedule_filed_tit round_tit">
                        <p class="tit_left_red">조원 수</p>
                    </div>
                    <input placeholder="조원 수" type="number" name="count" value="8" min="1" maxlength="2"
                           oninput="maxLengthCheck(this)" required=""/>

                    <div class="Participant_Btn">
                        <button type="submit" class="changePwBtn defaultBtn">등록</button>
                    </div>
                </form>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      //이메일 입력방식 선택
      $('#round').change(function() {
        $("#round option:selected").each(function() {
          if ($(this).val() == '1') { //직접입력일 경우
            $("#direct_input").val(''); //값 초기화
            $("#direct_input").attr("disabled", false); //활성화
            $("#direct_input").attr("placeholder", " 라운드를 입력하세요.");
            $("#form_action").css("color", "var(--color-blue)");
          } else { //직접입력이 아닐경우
            $("#direct_input").val($(this).text()); //선택값 입력
            $("#direct_input").attr("disabled", true); //비활성화
          }
        });
      });
    </script>
</body>

</html>