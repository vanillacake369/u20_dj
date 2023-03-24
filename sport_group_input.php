<?php
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "security/security.php";
    require_once "action/module/dictionary.php";
    // 로그 기능
    require_once "backheader.php";
    if (!authCheck($db, "authSchedulesRead")) {
        exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
    }
    if (!authCheck($db, "authSchedulesCreate")) {
        exit("<script>
                alert('잘못된 접근입니다.');
                history.back();
            </script>");
    }

    // $id = cleanInput($_GET['id']); // schedule_id
    // $sql = "SELECT sports_category, sports_code FROM list_sports WHERE sports_code IN (SELECT schedule_sports FROM list_schedule WHERE schedule_id=$id);";
    // $result = $db->query($sql);
    // $row = mysqli_fetch_array($result);
    // $category = $row['sports_category'];
    // $code = $row['sports_code'];
    ?>
    <script src="/assets/js/jquery-1.12.4.min.js"></script>
    <script src="/assets/js/restrict.js"></script>
</head>

<body>
  <div class="container">
    <div class="athlete">
      <div class="profile_logo">
        <img src="/assets/images/logo.png">
      </div>
      <div class="UserProfile">
        <p class="UserProfile_tit tit_left_blue">
            새로운 조 편성 하기
        </p>
        <form action="./sport_group_select_athletes.php" method="post">
          <div class="UserProfile_modify UserProfile_input">
            <div>
              <ul class="UserDesc">
                <li class="row input_row Desc_item">
                    <span>조편성 방식</span>
                        <select name="method" id="method" required>
                        <option value="" disabled selected>조편성 방식</option>
                        <option value="자동">자동</option>
                        <option value='수동'>수동</option>
                    </select>
                </li>
                <li class="row input_row Desc_item">
                    <span>종목</span>
                    <select name="sports" id="sports" required>
                        <option value="" disabled selected>종목</option>
                        <?php
                        // 경기 종목 코드
                        foreach (array_keys($sport_dic) as $s) {
                            echo '<option value="' . $s . '">' . $s . '</option>';
                        }
                        ?>
                    </select>
                    <script>
                        $(document).ready(function() {
                            $("#sports").change(function() {
                                var val = $(this).val();
                                // 경기 선택 이벤트 발생 시 - 경기별 라운드 변경 이벤트
                                if (val == "heptathlon") {
                                    // 10종 선택에 따른 라운드 셀렉트 박스 변경
                                    $("#round").html('<option value="final">final</option><option value="100m">100m</option><option value="longjump">longjump</option><option value="shotput">shotput</option><option value="highjump">highjump</option><option value="400m">400m</option><option value="110mH">110mH</option><option value="discusthrow">discusthrow</option><option value="polevault">polevault</option><option value="javelinthrow">javelinthrow</option><option value="1500m">1500m</option>');
                                } else if (val == "decathlon") {
                                    // 7종 선택에 따른 라운드 셀렉트 박스 변경
                                    $("#round").html('<option value="final">final</option><option value="100mh">100mh</option><option value="longjump">longjump</option><option value="shotput">shotput</option><option value="200m">200m</option><option value="highjump">highjump</option><option value="discusthrow">discusthrow</option><option value="800m">800m</option>');
                                } else {
                                    // 그 밖의 경기 시
                                    // $("#count").html('<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>');
                                    $("#round").html('<option value="" disabled selected>라운드</option><option value="preliminary-round">자격라운드</option><option value="qualification">예선</option><option value="semi-final">준결승</option><option value="final">결승</option>');
                                }

                                // 경기 선택 이벤트 발생 시 - 경기별 성별 변경 이벤트
                                if (val == "heptathlon" || val == "110mh") {
                                    // 10종 경기: gender 남자 고정
                                    $("#gender").html('<option value="m">남자</option>');
                                } else if (val == "decathlon" || val == "100mh") {
                                    // 7종 경기: gender 여자 고정
                                    $("#gender").html('<option value="f">여자</option>');
                                } else if (val == "4x400mR(Mixed)") {
                                    // 4 x 400m 릴레이 혼성 경기: gender 혼성 고정
                                    $("#gender").html('<option value="c">혼성</option>');
                                } else {
                                    // 이외의 경기
                                    $("#gender").html('<option value="" disabled selected>성별 선택</option><option value="m">남자</option><option value="f">여자</option>');
                                }
                            });
                        });
                    </script>
                </li>
                <li class="row input_row Desc_item">
                    <span>라운드</span>
                    <select name="round" id="round" required>
                        <option value="" disabled selected>라운드</option>
                        <option value="preliminary-round">자격라운드</option>
                        <option value="qualification">예선</option>
                        <option value="semi-final">준결승</option>
                        <option value="final">결승</option>
                    </select>
                </li>
                <li class="row input_row Desc_item input_width">
                    <span>성별</span>
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>성별 선택</option>
                        <option value="m">남성</option>
                        <option value="f">여성</option>
                        <option value="c">혼성</option>
                    </select>
                </li>
              </ul>
            </div>
          </div>
          <div class="modify_Btn input_Btn Participant_Btn">
            <button type="submit" class="btn_login" name="signup">확인</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="assets/js/main.js?ver=10"></script>
 </body>
 
</html>