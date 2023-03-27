<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "action/module/dictionary.php";
require_once  "backheader.php";

if (!authCheck($db, "authEntrysRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
?>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    코치 정보
                </p>
                <form action="./action/module/coach_Insert.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input coachArea">
                        <div>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row input_row row_item">
                                    <span>이름</span>
                                    <input type="text" name="coach_second_name" id="coach_name" value="" placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>성</span>
                                    <input type="text" name="coach_first_name" id="coach_name" value="" placeholder="성을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>국가</span>
                                    <select aria-placeholder="국가 선택" name="coach_country" required>
                                        <option value='' selected disabled hidden>국가 선택</option>
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row row_item">
                                    <span>지역</span>
                                    <input type="text" name="coach_region" id="coach_region" value="" placeholder="지역을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>소속</span>
                                    <input type="text" name="coach_division" id="coach_division" value="" placeholder="소속을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>직무</span>
                                    <select title="직무" name="coach_duty" required>
                                        <?php
                                        foreach ($coach_duty_dic as $duty) {
                                            echo '<option value=' . '"' . $duty . '"' . 'id="' . $duty . '"/>' . $duty . '</option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>성별</span>
                                    <select name="coach_gender" style="width: 100px;" title="성별" required>
                                        <option value='' selected disabled hidden>성별 선택</option>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" name="coach_birth_year" placeholder="연" required value="" maxlength="4" oninput="maxLengthCheck(this)" required>
                                    <input type="number" name="coach_birth_month" placeholder="월" required value="" maxlength="2" oninput="maxLengthCheck(this)" required>
                                    <input type="number" name="coach_birth_day" placeholder="일" required value="" maxlength="2" oninput="maxLengthCheck(this)" required>
                                </li>
                                <li class="row input_row row_item">
                                    <span>나이</span>
                                    <input type="number" name="coach_age" id="coach_age" value="" maxlength="2" oninput="maxLengthCheck(this)" placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" />
                                </li>
                                <li class="row input_row row_item">
                                    <span>식사 가능 여부</span>
                                    <input type="checkbox" name="coach_eat" value="식사">
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>대회접근시설</span>
                                        <select name="coach_venue_access" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="Y">전 구역</option>
                                            <option value="HQ">본부호텔</option>
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>경기장 내 좌석</span>
                                        <select name="coach_seats" required>
                                            <option value='' selected disabled hidden>좌석선택</option>
                                            <option value="RS">VIP석</option>
                                            <option value="US">자유석</option>
                                            <option value="AS">선수 임원석</option>
                                            <option value="MS">미디어석</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>교통 권한</span>
                                        <select name="coach_transport">
                                            <option value='' selected disabled hidden>교통권한선택</option>
                                            <option value="T1">1인 1차량</option>
                                            <option value="T2">2인 1차량</option>
                                            <option value="TA">선수임원수송버스</option>
                                            <option value="TF">기술임원 수송버스</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width full_width">
                                    <span class="full_span">선수촌</span>
                                        <select class="full_width2" name="coach_village" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="AV">선수촌 거주 허용</option>
                                            <option value="VA">선수촌 전구역(거주 불허)</option>
                                        </select>
                                </li>
                                <li class="row full_width coach_sector">
                                <span class="full_span">경기장 내 접근 허용</span>
                                    <div class="full_div">
                                        <label><input type="checkbox" name="coach_sector[]" value="0" id="All Area"><span>경기장 내 전
                                            구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="1"
                                            id="Competition Area(FOP)"><span>경기구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="2"
                                            id="Warm-up Area"><span>선수준비구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="3"
                                            id="Administration & Operation zone"><span>경기운영구역
                                            zone</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="4"
                                            id="International Officials' Zone"><span>국제임원 업무구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="5"
                                            id="VIP Area"><span>VIP구역(3F)</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="6"
                                            id="Mixed Zone"><span>공동취재구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="7"
                                            id="PEA(Post Event Area)"><span>경기 후 구역</span></label>
                                        <label><input type="checkbox" name="coach_sector[]" value="8"
                                            id="TIC(Technical Information Center)"><span>기술정보센터(2F)</span></label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" class="BTN_blue2" name="coach_edit">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/js/main.js?ver=10"></script>
    <script>
    if (document.querySelectorAll('.coach_sector>div>label>input[name="coach_sector[]"]')) {

        const allow_access = document.querySelectorAll('.coach_sector>div>label>input[name="coach_sector[]"]');
        let checkcnt = 0;

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