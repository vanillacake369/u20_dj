<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "action/module/dictionary.php";
require_once "security/security.php";
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
                    일정 등록
                </p>
                <form action="./action/sport/schedule_insert.php" method="post">
                    <div class="UserProfile_modify UserProfile_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row Desc_item">
                                    <span>종목</span>
                                    <select name="sports" id="sports" required>
                                        <option value="" disabled selected>종목</option>
                                        <?php
                                        // 경기 종목 코드
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
                                    <script>
                                    $(document).ready(function() {
                                        $("#sports").change(function() {
                                            var val = $(this).val();
                                            // 경기 선택 이벤트 발생 시 - 경기별 라운드 변경 이벤트
                                            if (val == "decathlon") {
                                                // 10종 선택에 따른 라운드 셀렉트 박스 변경
                                                $("#round").html(
                                                    '<option value="final">결승</option><option value="100m">100m</option><option value="longjump">longjump</option><option value="shotput">shotput</option><option value="highjump">highjump</option><option value="400m">400m</option><option value="110mH">110mH</option><option value="discusthrow">discusthrow</option><option value="polevault">polevault</option><option value="javelinthrow">javelinthrow</option><option value="1500m">1500m</option>'
                                                );
                                            } else if (val == "heptathlon") {
                                                // 7종 선택에 따른 라운드 셀렉트 박스 변경
                                                $("#round").html(
                                                    '<option value="final">결승</option><option value="100mh">100mh</option><option value="longjump">longjump</option><option value="shotput">shotput</option><option value="200m">200m</option><option value="highjump">highjump</option><option value="javelinthrow">javelinthrow</option><option value="800m">800m</option>'
                                                );
                                            } else {
                                                // 그 밖의 경기 시
                                                // $("#count").html('<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>');
                                                $("#round").html(
                                                    '<option value="" disabled selected>라운드</option><option value="preliminary-round">자격라운드</option><option value="qualification">예선</option><option value="semi-final">준결승</option><option value="final">결승</option>'
                                                );
                                            }

                                            // 경기 선택 이벤트 발생 시 - 경기별 성별 변경 이벤트
                                            if (val == "decathlon" || val == "110mh") {
                                                // 10종 경기: gender 남자 고정
                                                $("#gender").html('<option value="m">남자</option>');
                                            } else if (val == "heptathlon" || val == "100mh") {
                                                // 7종 경기: gender 여자 고정
                                                $("#gender").html('<option value="f">여자</option>');
                                            } else if (val == "4x400mR(Mixed)") {
                                                // 4 x 400m 릴레이 혼성 경기: gender 혼성 고정
                                                $("#gender").html('<option value="c">혼성</option>');
                                            } else {
                                                // 이외의 경기
                                                $("#gender").html(
                                                    '<option value="" disabled selected>성별 선택</option><option value="m">남자</option><option value="f">여자</option>'
                                                );
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

                                <li class="row input_row Desc_item">
                                    <span>경기 장소</span>
                                    <input placeholder="경기 장소" type="text" name="location" maxlength="50" required="" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 시간</span>
                                    <div>
                                        <input placeholder="경기 시작 시간(시)" type="number" name="start_hour" maxlength="2"
                                            oninput="maxLengthCheck(this)" required="" />
                                        <span>:</span>
                                        <input placeholder="경기 시작 시간(분)" type="number" name="start_minute" maxlength="2"
                                            oninput="maxLengthCheck(this)" required="" />
                                    </div>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기날짜</span>
                                    <div>
                                        <input placeholder="(YYYY)" type="number" name="date_year" maxlength="4"
                                            required="" oninput="maxLengthCheck(this)" value="" />
                                        <span>:</span>
                                        <input placeholder="(mm)" type="number" name="date_month" maxlength="2"
                                            oninput="maxLengthCheck(this)" value="" />
                                        <span>:</span>
                                        <input placeholder="(dd)" type="number" name="date_day" maxlength="2"
                                            oninput="maxLengthCheck(this)" value="" />
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