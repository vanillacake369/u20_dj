<?
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "security/security.php";

    if (empty($_GET['id'])) {
        echo "<script>alert('잘못된 경로입니다.');  location.href='./sport_schedulemanagement.php';</script>";
    }

    $id = cleanInput($_GET['id']);
    $sql = "SELECT schedule_result, schedule_round FROM list_schedule WHERE schedule_id='" . $id . "'";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);

    if ($row['schedule_result'] != 'o' || $row['schedule_round'] == '결승') {
        echo "<script>alert('잘못된 경로입니다.');  location.href='./sport_schedulemanagement.php';</script>";
    }


?>
    <script src="/assets/js/jquery-3.2.1.min.js"></script>
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
            <form action="action/sport/sport_create_next_round_group.php" method="post" class="form">
            <input type="hidden" name="id" value="<?= $id ?>">
            <select class="d_select">
                <option value="" hidden="">라운드</option>
                <option value="1">직접입력</option>
                <?php if ($row['schedule_round'] != 'semi-final') { ?>
                <option value="semi-final">준결승</option>
                <?php } ?>
                <option value="final">결승</option>
            </select>
            <div class="schedule_filed_tit round_tit">
              <p class="tit_left_green">조 갯수</p>
            </div>
            <select class="d_select">
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
            <input placeholder="조원 수" type="number" name="count" value="8" min="1" maxlength="2" oninput="maxLengthCheck(this)" required="" />
          </form>
        </div>
        <div class="Participant_Btn">
          <button type="submit" class="changePwBtn defaultBtn">등록</button>
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