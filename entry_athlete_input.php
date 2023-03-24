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
                                    <input type="text" name="athlete_first_name" id="athlete_name" value="" placeholder="성을 입력해 주세요" required />
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
                                    <input type="number" name="athlete_birth_year" class="input_text_row_b" placeholder="연" required>
                                    <input type="number" name="athlete_birth_month" class="input_text_row_b" placeholder="월" required>
                                    <input type="number" name="athlete_birth_day" class="input_text_row_b" placeholder="일" required>
                                </li>
                                <li class="row input_row row_item">
                                    <span>나이</span>
                                    <input type="number" name="athlete_age" id="athlete_age" value="" placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" class="form-control" />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">출입가능구역</span>
                                    <div class="full_div">
                                        <?php
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="athlete_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . current($sector_dic) . '"/>';
                                            echo "<span>" . current($sector_dic) . "</span>";
                                            echo "</label>";
                                            next($sector_dic);
                                        }
                                        reset($sector_dic);
                                        ?>
                                    </div>
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
                                    <select name="athlete_sb_sports" id="sb-sports-select">
                                        <?php
                                        // key($sport_dic) : sports_code
                                        // current($sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sport_dic); $value++) {
                                            echo '<option value=\'\' selected disabled hidden>종목 선택</option>';
                                            echo '<option value="' . key($sport_dic) . '">' . current($sport_dic) . '</option>';
                                            next($sport_dic);
                                        }
                                        reset($sport_dic);
                                        ?>
                                    </select>
                                    <input type="number" name="athlete_sb" id="athlete_sb" value="" placeholder="SB를 입력해 주세요" />
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-sb"><i class="xi-minus"></i></button>
                                </ul>
                            </div>
                            <div class="filed_BTN2">
                                <!-- <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-sb"><i class="xi-minus"></i></button> -->
                                <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn" id="add-sb"><i class="xi-plus"></i></button>
                            </div>
                        </div>
                        <div class="modify_check">
                            <div class="modify_enter" id="pb-section">
                                <p class="tit_left_green">PB</p>
                                <ul class="modify_checkList" id="pb-input">
                                    <select name="athlete_pb_sports" id="pb-sports-select">
                                        <?php
                                        // key($sport_dic) : sports_code
                                        // current($sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sport_dic); $value++) {
                                            echo '<option value=\'\' selected disabled hidden>종목 선택</option>';
                                            echo '<option value="' . key($sport_dic) . '">' . current($sport_dic) . '</option>';
                                            next($sport_dic);
                                        }
                                        reset($sport_dic);
                                        ?>
                                    </select>
                                    <input type="number" name="athlete_pb" id="athlete_pb" value="" placeholder="PB를 입력해 주세요" />
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-pb"><i class="xi-minus"></i></button>
                                </ul>
                            </div>
                            <div class="filed_BTN2">
                                <!-- <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-pb"><i class="xi-minus"></i></button> -->
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
                        </script>
                    </div>
                    <div class=" modify_Btn input_Btn Participant_Btn">
                        <button class="BTN_blue2" type="button">등록하기</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>