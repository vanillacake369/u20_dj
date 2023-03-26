<?php
require_once "head.php";
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 로그 기능
require_once "backheader.php";

if (!authCheck($db, "authEntrysRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
?>
<!-- 태블릿 심판 유무 스크립트 -->
<script src="/assets/js/restrict.js?ver=1"></script>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    심판 정보
                </p>
                <form action="action/module/judge_insert.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input coachArea">
                        <div>
                            <span id="message" style="padding-left: 10px;" style="display:none"></span>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row check">
                                    <span>태블릿 사용 심판이신가요?(맞다면 체크)</span>
                                    <input type="checkbox" name="is_using_tablet" id="is_using_tablet" onclick="isUsingTablet()" />
                                </li>
                                <li class="row input_row Desc_item" id="userid" style="display:none">
                                    <span>아이디</span>
                                    <input type="text" size="35" name="userid" id="user_id" placeholder="아이디를 입력해 주세요" />
                                </li>
                                <li class="row input_row Desc_item" id="use_pw" style="display:none">
                                    <span>비밀번호</span>
                                    <input type="password" name="judge_password" id="judge_password" value="" minlength="8" maxlength="20" placeholder="비밀번호를 입력해 주세요" />
                                </li>
                                <li class="row input_row Desc_item" id="use_pw_check" style="display:none">
                                    <span class="input_guide">비밀번호 재확인</span>
                                    <input type="password" name="cpassword" id="cpassword" value="" minlength="8" maxlength="20" class="input_text_row" placeholder="비밀번호를 다시 입력해 주세요" onkeyup="check();" />
                                </li>

                                <li class="row input_row Desc_item">
                                    <span>이름</span>
                                    <input type="text" name="judge_second_name" id="judge_name" value="" placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>성</span>
                                    <input type="text" name="judge_first_name" id="judge_name" value="" placeholder="성을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>국가</span>
                                    <select name="judge_country" required>
                                        <option value='' selected disabled hidden>국가 선택</option>
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo '<option value="' . $value . '">' . $key . '</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>소속</span>
                                    <input type="text" name="judge_division" id="judge_division" value="" placeholder="소속을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>직무</span>
                                    <select name="judge_duty" id="judge_duty" required>
                                        <option value="" disabled selected>직무</option>
                                        <?php
                                        foreach ($judge_duty_dic as $key) {
                                            echo '<option value="' . $key . '">' . $key . '</option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                <li class=" row input_row Desc_item input_width">
                                    <span>성별</span>
                                    <select name="judge_gender" required>
                                        <option value='' selected disabled hidden>성별 선택</option>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" name="judge_birth_year" class="input_text_row_b" placeholder="연" required maxlength="4" oninput="maxLengthCheck(this)">
                                    <input type="number" name="judge_birth_month" class="input_text_row_b" placeholder="월" required maxlength="2" oninput="maxLengthCheck(this)">
                                    <input type="number" name="judge_birth_day" class="input_text_row_b" placeholder="일" required maxlength="2" oninput="maxLengthCheck(this)">
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>나이</span>
                                    <input type="number" name="judge_age" id="judge_age" value="" placeholder="나이를 입력해 주세요" required maxlength="2" oninput="maxLengthCheck(this)" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" />
                                </li>
                                <li class="row input_row row_item">
                                    <span>식사 가능 여부</span>
                                    <input type="checkbox" name="judge_eat" value="식사">
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>대회접근시설</span>
                                        <select name="judge_venue_access" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="Y">전 구역</option>
                                            <option value="HQ">본부호텔</option>
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>경기장 내 좌석</span>
                                        <select name="judge_seats" required>
                                            <option value='' selected disabled hidden>좌석선택</option>
                                            <option value="RS">VIP석</option>
                                            <option value="US">자유석</option>
                                            <option value="AS">선수 임원석</option>
                                            <option value="MS">미디어석</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>교통 권한</span>
                                        <select name="judge_transport">
                                            <option value='' selected disabled hidden>교통권한선택</option>
                                            <option value="T1">1인 1차량</option>
                                            <option value="T2">2인 1차량</option>
                                            <option value="TA">선수임원수송버스</option>
                                            <option value="TF">기술임원 수송버스</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>선수촌</span>
                                        <select name="judge_village" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="AV">선수촌 거주 허용</option>
                                            <option value="VA">선수촌 전구역(거주 불허)</option>
                                        </select>
                                </li>
                                <li class="row full_width judge_sector">
                                <span class="full_span">경기장 내 접근 허용</span>
                                    <div class="full_div">
                                        <label><input type="checkbox" name="judge_sector[]" value="0" id="All Area"><span>경기장 내 전
                                            구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="1"
                                            id="Competition Area(FOP)"><span>경기구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="2"
                                            id="Warm-up Area"><span>선수준비구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="3"
                                            id="Administration & Operation zone"><span>경기운영구역
                                            zone</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="4"
                                            id="International Officials' Zone"><span>국제임원 업무구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="5"
                                            id="VIP Area"><span>VIP구역(3F)</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="6"
                                            id="Mixed Zone"><span>공동취재구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="7"
                                            id="PEA(Post Event Area)"><span>경기 후 구역</span></label>
                                        <label><input type="checkbox" name="judge_sector[]" value="8"
                                            id="TIC(Technical Information Center)"><span>기술정보센터(2F)</span></label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modifyform">
                        <div class="modify_check full_width">
                            <div class="modify_enter modify_tit_color">
                                <p class="tit_left_red">참가예정경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                    for ($value = 1; $value <= count($judge_sport_dic); $value++) {

                                        echo "<li><label>";
                                        echo '<input type="checkbox" name="judge_schedules[]"' . 'value="' . key($judge_sport_dic) . '"' . 'id="' . current($judge_sport_dic) . '"/>';
                                        echo "<span>" . current($judge_sport_dic) . "</span>";
                                        echo "</label></li>";
                                        next($judge_sport_dic);
                                    }
                                    reset($judge_sport_dic);
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" class="BTN_blue2" name="judge_edit">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/js/main.js?ver=9"></script>
    <script>
        if (document.querySelectorAll('.judge_sector>div>label>input[name="judge_sector[]"]')) {

        const allow_access = document.querySelectorAll('.judge_sector>div>label>input[name="judge_sector[]"]');
        let checkcnt = document.querySelectorAll('.judge_sector>div>label>input[name="judge_sector[]"]:checked').length;

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
                alert('5개 이상 선택이 불가능합니다');
            }
        })
        }
        }
    </script>
    <script type="text/javascript">
    function maxLengthCheck(object){
      if (object.value.length > object.maxLength){
        object.value = object.value.slice(0, object.maxLength);
      }    
    }
  </script>
</body>

</html>