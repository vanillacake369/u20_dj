<?php
require_once "head.php";
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once "includes/auth/config.php";
require_once "action/module/dictionary.php";

require_once "backheader.php";

if (!authCheck($db, "authEntrysRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="./assets/js/main.js"></script>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    참가자 정보
                </p>
                <form action="./action/module/athlete_Insert.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input">
                        <div>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row input_row row_item">
                                    <span>이름</span>
                                    <input type="text" name="athlete_second_name" id="athlete_name" value="" placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>성</span>
                                    <input type="text" name="athlete_first_name" id="athlete_name" value="" placeholder="성을 입력해 주세요" />
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>국가</span>
                                    <select aria-placeholder="국가 선택" name="athlete_country">
                                        <option value="non" selected disabled hidden>국가 선택</option>
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo '<option value="' . $value . '">' . $key . '</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row row_item">
                                    <span>지역</span>
                                    <input type="text" name="athlete_region" id="athlete_region" value="" placeholder="지역을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>소속</span>
                                    <input type="text" name="athlete_division" id="athlete_division" value="" placeholder="소속을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>성별</span>
                                    <select name="athlete_gender" required>
                                        <option value='' selected disabled hidden>성별 선택</option>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" name="athlete_birth_year" class="input_text_row_b" placeholder="연" maxlength="4" oninput="maxLengthCheck(this)" required>
                                    <input type="number" name="athlete_birth_month" class="input_text_row_b" placeholder="월" maxlength="2" oninput="maxLengthCheck(this)" required>
                                    <input type="number" name="athlete_birth_day" class="input_text_row_b" placeholder="일" maxlength="2" oninput="maxLengthCheck(this)" required>
                                </li>
                                <li class="row input_row row_item">
                                    <span>나이</span>
                                    <input type="number" name="athlete_age" id="athlete_age" value="" maxlength="2" oninput="maxLengthCheck(this)" placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" class="form-control" />
                                </li>
                                <li class="row input_row row_item">
                                    <span>식사 가능 여부</span>
                                    <input type="checkbox" name="athlete_eat" value="식사">
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>대회접근시설</span>
                                    <select name="athlete_venue_access">
                                        <option value='' selected disabled hidden>접근시설선택</option>
                                        <option value="Y">전 구역</option>
                                        <option value="HQ">본부호텔</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>경기장 내 좌석</span>
                                    <select name="athlete_seats">
                                        <option value='' selected disabled hidden>좌석선택</option>
                                        <option value="RS">VIP석</option>
                                        <option value="US">자유석</option>
                                        <option value="AS">선수 임원석</option>
                                        <option value="MS">미디어석</option>

                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>교통 권한</span>
                                    <select name="athlete_transport">
                                        <option value='' selected disabled hidden>교통권한선택</option>
                                        <option value="T1">1인 1차량</option>
                                        <option value="T2">2인 1차량</option>
                                        <option value="TA">선수임원수송버스</option>
                                        <option value="TF">기술임원 수송버스</option>

                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>선수촌</span>
                                    <select name="athlete_village">
                                        <option value='' selected disabled hidden>접근시설선택</option>
                                        <option value="AV">선수촌 거주 허용</option>
                                        <option value="VA">선수촌 전구역(거주 불허)</option>
                                    </select>
                                </li>
                                <li class="row full_width athlete_sector">
                                    <span class="full_span">경기장 내 접근 허용</span>
                                    <div class="full_div">
                                        <label><input type="checkbox" name="athlete_sector[]" value="0" id="All Area"><span>경기장 내 전
                                                구역</span></label>
                                        <label><input type="checkbox" name="athlete_sector[]" value="1" id="Competition Area(FOP)"><span>경기구역</span></label>
                                        <label><input type="checkbox" name="athlete_sector[]" value="2" id="Warm-up Area"><span>선수준비구역</span></label>
                                        <label><input type="checkbox" name="athlete_sector[]" value="3" id="Administration & Operation zone"><span>경기운영구역
                                                zone</span></label>
                                        <label><input type="checkbox" name="athlete_sector[]" value="4" id="International Officials' Zone"><span>국제임원 업무구역</span>
                                            <label><input type="checkbox" name="athlete_sector[]" value="5" id="VIP Area"><span>VIP구역(3F)</span></label>
                                            <label><input type="checkbox" name="athlete_sector[]" value="6" id="Mixed Zone"><span>공동취재구역</span></label>
                                            <label><input type="checkbox" name="athlete_sector[]" value="7" id="PEA(Post Event Area)"><span>경기 후 구역</span></label>
                                            <label><input type="checkbox" name="athlete_sector[]" value="8" id="TIC(Technical Information Center)"><span>기술정보센터(2F)</span></label>
                                    </div>
                                </li>
                                <li class="row input_row row_item full_width">
                                    <span class="full_span">등번호</span>
                                    <input type="number" class="full_width2" name="athlete_bib" id="athlete_bib" value="" placeholder="등번호를 입력해 주세요" maxlength="5" oninput="maxLengthCheck(this)" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modifyform">
                        <div class="modify_check">
                            <div class="modify_enter modify_tit_color">
                                <p class="tit_left_red">참석예정경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                    for ($value = 1; $value <= count($sport_dic); $value++) {

                                        echo '<li>';
                                        echo "<label>";
                                        echo '<input type="checkbox" name="athlete_schedules[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . current($sport_dic) . '"/>';
                                        echo "<span>" . current($sport_dic) . "</span>";
                                        echo "</label>";
                                        echo '</li>';

                                        next($sport_dic);
                                    }
                                    reset($sport_dic);
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="modify_check">
                            <div class="modify_enter">
                                <p class="tit_left_green">참가확정경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                    for ($value = 1; $value <= count($sport_dic); $value++) {

                                        echo '<li>';
                                        echo "<label>";
                                        echo '<input type="checkbox" name="attendance_sports[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . current($sport_dic) . '"/>';
                                        echo "<span>" . current($sport_dic) . "</span>";
                                        echo "</label>";
                                        echo '</li>';

                                        next($sport_dic);
                                    }
                                    reset($sport_dic);
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modifyform">
                        <div class="modify_check">
                            <div class="modify_enter modify_tit_color" id="sb-section">
                                <p class="tit_left_red">SB</p>
                                <ul class="modify_checkList" id="sb-input">
                                    <select name="athlete_sb_sports[]" id="sb-sports-select">
                                        <?php
                                        // sb,pb에는 아래와 같이 "종합(상세종목)" 으로 기입되어야 함
                                        // 10종,7종 선택 옵션
                                        $sbpb_sport_dic = $sport_dic;
                                        unset($sbpb_sport_dic["decathlon"]);
                                        unset($sbpb_sport_dic["heptathlon"]);
                                        $sbpb_sport_dic["decathlon(100m)"] = "Decathlon(100mh)";
                                        $sbpb_sport_dic["decathlon(longjump)"] = "Decathlon(longjump)";
                                        $sbpb_sport_dic["decathlon(shotput)"] = "Decathlon(shotput)";
                                        $sbpb_sport_dic["decathlon(highjump)"] = "Decathlon(highjump)";
                                        $sbpb_sport_dic["decathlon(400m)"] = "Decathlon(400m)";
                                        $sbpb_sport_dic["decathlon(110mh)"] = "Decathlon(110mh)";
                                        $sbpb_sport_dic["decathlon(discusthrow)"] = "Decathlon(discusthrow)";
                                        $sbpb_sport_dic["decathlon(polevalut)"] = "Decathlon(polevalut)";
                                        $sbpb_sport_dic["decathlon(javelinthrow)"] = "Decathlon(javelinthrow)";
                                        $sbpb_sport_dic["decathlon(1500m)"] = "Decathlon(1500m)";
                                        $sbpb_sport_dic["heptathlon(100mh)"] = "Heptathlon(100mh)";
                                        $sbpb_sport_dic["heptathlon(highjump)"] = "Heptathlon(highjump)";
                                        $sbpb_sport_dic["heptathlon(shotput)"] = "Heptathlon(shotput)";
                                        $sbpb_sport_dic["heptathlon(longjump)"] = "Heptathlon(longjump)";
                                        $sbpb_sport_dic["heptathlon(javelinthrow)"] = "Heptathlon(javelinthrow)";
                                        $sbpb_sport_dic["heptathlon(800m)"] = "Heptathlon(800m)";
                                        // key($sbpb_sport_dic) : sports_code
                                        // current($sbpb_sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sbpb_sport_dic); $value++) {
                                            echo '<option value=\'\' selected disabled hidden>종목 선택</option>';
                                            echo '<option value="' . key($sbpb_sport_dic) . '">' . current($sbpb_sport_dic) . '</option>';
                                            next($sbpb_sport_dic);
                                        }
                                        reset($sbpb_sport_dic);
                                        ?>
                                    </select>
                                    <input type="text" name="athlete_sb[]" id="athlete_sb" value="" placeholder="SB를 입력해 주세요" onkeyup="heightFormat(this)" />
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-sb"><i class="xi-minus"></i></button>
                                </ul>
                            </div>
                            <div class="filed_BTN2">
                                <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn" id="add-sb"><i class="xi-plus"></i></button>
                            </div>
                        </div>
                        <div class="modify_check">
                            <div class="modify_enter" id="pb-section">
                                <p class="tit_left_green">PB</p>
                                <ul class="modify_checkList" id="pb-input">
                                    <select name="athlete_pb_sports[]" id="pb-sports-select">
                                        <?php
                                        // key($sbpb_sport_dic) : sports_code
                                        // current($sbpb_sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sbpb_sport_dic); $value++) {
                                            echo '<option value=\'\' selected disabled hidden>종목 선택</option>';
                                            echo '<option value="' . key($sbpb_sport_dic) . '">' . current($sbpb_sport_dic) . '</option>';
                                            next($sbpb_sport_dic);
                                        }
                                        reset($sbpb_sport_dic);
                                        ?>
                                    </select>
                                    <input type="text" name="athlete_pb[]" id="athlete_pb" value="" placeholder="PB를 입력해 주세요" onkeyup="heightFormat(this)" />
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-pb"><i class="xi-minus"></i></button>
                                </ul>
                            </div>
                            <div class="filed_BTN2">
                                <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn" id="add-pb"><i class="xi-plus"></i></button>
                            </div>
                        </div>
                        <script>
                            // sb 추가 버튼 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $('#add-sb').click(function() {
                                    var list = $('#sb-input').clone(); // Make a copy of the <ul> element
                                    $('#sb-section').append(list); // Append the copy to the body of the document
                                });
                            });
                            // pb 추가 버튼 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $('#add-pb').click(function() {
                                    var list = $('#pb-input').clone(); // Make a copy of the <ul> element
                                    $('#pb-section').append(list); // Append the copy to the body of the document
                                });
                            });
                            // sb 삭제 버튼 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $(document).on("click", '#delete-sb', function() {
                                    $(this).parent().remove();
                                });
                            });
                            // pb 삭제 버튼 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $(document).on("click", '#delete-pb', function() {
                                    $(this).parent().remove();
                                });
                            });
                            // sb 7종, 10종 선택 시, 입력값 형식강제 풀어버림 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $(document).on("change", '#sb-sports-select', function() {
                                    if ($(this).val().indexOf('heptathlon') !== -1) {
                                        $(this).siblings('#athlete_sb').removeAttr('onkeyup');
                                    } else if ($(this).val().indexOf('decathlon') !== -1) {
                                        $(this).siblings('#athlete_sb').removeAttr('onkeyup');
                                    } else {
                                        $(this).siblings('#athlete_sb').attr('onkeyup', 'heightFormat(this)');
                                    }
                                });
                            });
                            // pb 7종, 10종 선택 시, 입력값 형식강제 풀어버림 @author 임지훈 @vanillacake369
                            $(document).ready(function() {
                                $(document).on("change", '#pb-sports-select', function() {
                                    if ($(this).val().indexOf('heptathlon') !== -1) {
                                        $(this).siblings('#athlete_pb').removeAttr('onkeyup');
                                    } else if ($(this).val().indexOf('decathlon') !== -1) {
                                        $(this).siblings('#athlete_pb').removeAttr('onkeyup');
                                    } else {
                                        $(this).siblings('#athlete_pb').attr('onkeyup', 'heightFormat(this)');
                                    }
                                });
                            });
                        </script>
                    </div>
                    <div class=" modify_Btn input_Btn Participant_Btn">
                        <button class="BTN_blue2" type="submit">등록하기</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="/assets/js/main.js?v=8"></script>
    <script>
        if (document.querySelectorAll('.athlete_sector>div>label>input[name="coach_sector[]"]')) {

            const allow_access = document.querySelectorAll('.athlete_sector>div>label>input[name="coach_sector[]"]');
            let checkcnt = 0;

            for (let i = 0; i < allow_access.length; i++) {
                allow_access[i].addEventListener("click", () => {
                    if (allow_access[i].checked) {
                        checkcnt++;
                    } else {
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
        function maxLengthCheck(object) {
            if (object.value.length > object.maxLength) {
                object.value = object.value.slice(0, object.maxLength);
            }
        }
    </script>
</body>

</html>