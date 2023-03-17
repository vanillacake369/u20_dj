<?
    require_once "head.php";
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
        <form action="">
          <div class="UserProfile_modify UserProfile_input">
            <div>
              <ul class="UserDesc">
                <li class="row input_row Desc_item">
                  <span>경기 종목 코드</span>
                  <input placeholder="경기 종목 코드" type="text" name="sports" minlength="4"
                            maxlength="20" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)"
                            value="" />
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 종목 이름(한글)</span>
                  <input placeholder="경기 종목 이름(한글)" type="text" name="name" minlength="4"
                            maxlength="20" required="" value="" />
                </li>
                <li class="row input_row Desc_item input_width">
                  <span>경기성별</span>
                  <select name="gender">
                        <option value="non" hidden="">경기 성별</option>
                        <option value="m">남성</option>
                        <option value="f">여성</option>
                        <option value="c">혼성</option>
                  </select>
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 라운드</span>
                  <input class="input_text_row " type="text" name="direct_input" id="direct_input" style="width:200px;" disabled>
                  <div class="select_box">
                        <select name="round" id="round" style="width: 100px;">
                            <option value="" hidden="">라운드</option>
                            <option value="1">직접입력</option>
                            <option value="qualification">예선</option>
                            <option value="semi-final">준결승</option>
                            <option value="final">결승</option>
                        </select>
                    </div>
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 장소</span>
                  <input placeholder="경기 장소" type="text" name="location" maxlength="50" required="" />
                </li>
                <li class="row input_row Desc_item">
                  <span>경기 시간</span>
                  <div>
                  <input placeholder="경기 시작 시간(시)" type="number" name="start_hour" class="input_text_row" min="1" maxlength="5"
                            oninput="maxLengthCheck(this)" required="" />
                    <span>:</span>
                    <input placeholder="경기 시작 시간(분)" type="number" name="start_minute" maxlength="2"
                            oninput="maxLengthCheck(this)" required="" />
                  </div>
                </li>
                <li class="row input_row Desc_item input_width">
                  <span>경기진행상태</span>
                  <select name="status">
                        <option value="non" hidden="">경기 진행 상태</option>
                        <option value="n">준비</option>
                        <option value="c">취소됨</option>
                        <option value="o">경기중</option>
                        <option value="y">마감</option>
                    </select>
                </li>
                <li class="row input_row Desc_item input_width"">
                  <span>경기날짜</span>
                  <select name="date_year">
                        <option value="non" hidden="">경기 날짜(년)</option>
                        <option value="2023">2023</option>
                    </select>
                    <select name="date_month" >
                        <option value="non" hidden="">경기 날짜(월)</option>
                        <option value="06">06</option>
                    </select>
                    <select name="date_day">
                        <option value="non" hidden="">경기 날짜(일)</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                    </select>
                </li>
              </ul>
            </div>
          </div>
        </form>
        <div class="modify_Btn input_Btn Participant_Btn">
          <button type="button">등록</button>
        </div>
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