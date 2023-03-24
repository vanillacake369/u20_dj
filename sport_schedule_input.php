<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "action/module/dictionary.php";
?>
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
          일정 등록
        </p>
        <form action="./action/sport/schedule_insert.php" method="post">
          <div class="UserProfile_modify UserProfile_input">
            <div>
              <ul class="UserDesc">
                <li class="row input_row Desc_item">
                  <span>경기 종목</span>
                    <select name="sports" style="width: 200px;">
                      <option value="non" hidden="">경기 종목</option>
                      <?php
                        $events = array_unique($categoryOfSports_dic);
                        foreach ($events as $e) {
                          echo "<optgroup label=\"$e\">";
                          $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                          foreach ($sportsOfTheEvent as $a) {
                            echo '<option value="'.$a.'">' . $a . '</option>';
                          }
                          echo "</optgroup>";
                        }
                      ?>
                    </select>
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 라운드</span>
                  <!-- 직접 입력기능(input 태그)는 왜 있는 거야?? @author 임지훈 @vanillacake369 -->
                  <!-- <input class="input_text_row" type="text" name="direct_input" id="direct_input" style="width:200px;" disabled> -->
                    <select name="round" id="round" style="width: 100px;">
                      <option value="" hidden="">라운드</option>
                      <option value="preliminary-round">자격라운드</option>
                                        <option value="qualification">예선</option>
                                        <option value="semi-final">준결승</option>
                                        <option value="final">결승</option>
                    </select>
                </li>
                <li class="row input_row Desc_item input_width">
                  <span>경기성별</span>
                  <select name="gender" style="width: 200px;">
                    <option value="non" hidden="">경기 성별</option>
                    <option value="m">남성</option>
                    <option value="f">여성</option>
                    <option value="c">혼성</option>
                  </select>
                </li>
                
                <li class="row input_row Desc_item">
                  <span>경기 장소</span>
                  <input placeholder="경기 장소" type="text" name="location" maxlength="50" required="" />
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 시간</span>
                  <div>
                  <input placeholder="경기 시작 시간(시)" type="number" name="start_hour" maxlength="2" oninput="maxLengthCheck(this)" required="" />
                    <span>:</span>
                    <input placeholder="경기 시작 시간(분)" type="number" name="start_minute" maxlength="2" oninput="maxLengthCheck(this)" required="" />
                  </div>
                </li>
                <li class="row input_row Desc_item">
                                    <span>경기날짜</span>
                                    <div>
                                        <input placeholder="(YYYY)" type="number" name="date_year" maxlength="4"
                                            required="" oninput="maxLengthCheck(this)"
                                            value="" />
                                        <span>:</span>
                                        <input placeholder="(mm)" type="number" name="date_month"
                                            maxlength="2" oninput="maxLengthCheck(this)"
                                            value="" />
                                        <span>:</span>
                                        <input placeholder="(dd)" type="number" name="date_day"
                                            maxlength="2" oninput="maxLengthCheck(this)"
                                            value="" />
                                    </div>
                                </li>
              </ul>
            </div>
          </div>
          <div class="modify_Btn input_Btn Participant_Btn">
            <button type="submit" class="btn_login" name="signup">등록</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/restrict.js"></script>
  <script language='javascript'>
    function checkNumber(event) {
      if (event.key === '.' ||
        event.key === '-' ||
        event.key >= 0 && event.key <= 9) {
        return true;
      }

      return false;
    }
  </script>
</body>

</html>