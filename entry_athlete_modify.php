<?php
require_once "head.php";
if (!$_GET['id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
}
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
$sql = "SELECT * 
        FROM list_athlete
        INNER JOIN list_country  
        ON athlete_country=country_code
        where athlete_id=" . $_GET['id'];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$birth = explode('-', $row["athlete_birth"]); //생일 정보 나눔
// sb array
$athlete_sb_arr = json_decode($row['athlete_sb'], true);
// pb array
$athlete_pb_arr = json_decode($row['athlete_pb'], true);
// 10종,7종 선택 옵션
unset($sport_dic["decathlon"]);
unset($sport_dic["heptathlon"]);
$sport_dic["decathlon(100m)"] = "Decathlon(100mh)";
$sport_dic["decathlon(longjump)"] = "Decathlon(longjump)";
$sport_dic["decathlon(shotput)"] = "Decathlon(shotput)";
$sport_dic["decathlon(highjump)"] = "Decathlon(highjump)";
$sport_dic["decathlon(400m)"] = "Decathlon(400m)";
$sport_dic["decathlon(110mh)"] = "Decathlon(110mh)";
$sport_dic["decathlon(discusthrow)"] = "Decathlon(discusthrow)";
$sport_dic["decathlon(polevalut)"] = "Decathlon(polevalut)";
$sport_dic["decathlon(javelinthrow)"] = "Decathlon(javelinthrow)";
$sport_dic["decathlon(1500m)"] = "Decathlon(1500m)";
$sport_dic["heptathlon(100mh)"] = "Heptathlon(100mh)";
$sport_dic["heptathlon(highjump)"] = "Heptathlon(highjump)";
$sport_dic["heptathlon(shotput)"] = "Heptathlon(shotput)";
$sport_dic["heptathlon(longjump)"] = "Heptathlon(longjump)";
$sport_dic["heptathlon(javelinthrow)"] = "Heptathlon(javelinthrow)";
$sport_dic["heptathlon(800m)"] = "Heptathlon(800m)";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="./assets/js/main.js"></script>
</head>

<body>
    <div class="container">
        <form action="./action/module/athlete_update.php" method="post" class="form" enctype="multipart/form-data">
            <div class="athlete">
                <div class="profile_logo">
                    <img src="/assets/images/logo.png">
                </div>
                <div class="UserProfile">
                    <p class="UserProfile_tit tit_left_blue">
                        참가자 정보
                    </p>
                    <div class="UserProfile_modify Participant_img ptp_img">
                        <div>
                            <?php if (!isset($row["athlete_profile"]) && $row["athlete_profile"] == "")
                            {
                            ?>
                                <img src=<?php echo "./assets/img/athlete_img/profile.png" ?> alt="avatar" />
                            <?php }else{?>
                                <img src=<?php echo "./assets/img/athlete_img/" . $row["athlete_profile"] ?> alt="avatar" />
                            <?php }?>
                        </div>
                        <div>
                            <input type='hidden' name='athlete_id' value=<?= $_GET['id'] ?>>
                            <?php
                            $name = explode(" ", $row["athlete_name"]);
                            $secondName = isset($name[0]) ? $name[0] : NULL;
                            $firstName = isset($name[1]) ? $name[1] : NULL;
                            $fullName = $secondName . " " . $firstName;
                            ?>
                            <ul class="UserDesc Participant_list">
                                <li class="row">
                                    <span>이름</span>
                                    <?php
                                    echo '<input type="text" name="athlete_second_name" id="athlete_name"' . "value=\"" . $secondName . "\"" . ' />';
                                    ?>
                                </li>
                                <li class="row">
                                    <span>성</span>
                                    <?php
                                    echo '<input type="text" name="athlete_first_name" id="athlete_name"' . "value=\"" . $firstName . "\"" . ' />';
                                    ?>
                                </li>
                                <li class="row modify_input">
                                    <span>국가</span>
                                    <select class="d_select" name="athlete_country" id="athlete_country">
                                        <option value="non" hidden>국가 선택</option>
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo '<option value=' . '"' . $value  . '"' . '>' . $key . '</option>';
                                        ?>
                                        
                                    </select>
                                </li>
                                <li class="row">
                                    <span>지역</span>
                                    <input type="text" name="athlete_region" id="athlete_region" value=<?php echo htmlspecialchars($row["athlete_region"]) ?> />
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="athlete_division" id="athlete_division" value=<?php echo htmlspecialchars($row["athlete_division"]) ?> class="input_text_row" />
                                </li>
                                <li class="row modify_input">
                                    <span>성별</span>
                                    <select name="athlete_gender" id="athlete_gender" title="성별" required>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" value=<?php echo htmlspecialchars($birth[0]) ?> name="athlete_birth_year" class="input_text_row_b">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[1]) ?> name="athlete_birth_month" class="input_text_row_b">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[2]) ?> name="athlete_birth_day" class="input_text_row_b">
                                </li>
                                <li class="row input_row row_item">
                                    <span>나이</span>
                                    <input type="number" name="athlete_age" id="athlete_age" value="<?= htmlspecialchars($row["athlete_age"]) ?>" placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" />
                                </li>
                                <li class="row input_row row_item">
                                    <span>식사 가능 여부</span>
                                    <input type="checkbox" name="athlete_eat" value="식사" <?php echo $row["athlete_eat"] == 'y' ? "selected" : ""; ?>>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>대회접근시설</span>
                                    <select name="athlete_venue_access" required>
                                        <option value='' selected disabled hidden>접근시설선택</option>
                                        <option value="Y" <?php echo $row["athlete_venue_access"] == 'Y' ? "selected" : ""; ?>>전 구역</option>
                                        <option value="HQ" <?php echo $row["athlete_venue_access"] == 'HQ' ? "selected" : ""; ?>>본부호텔</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>경기장 내 좌석</span>
                                    <select name="athlete_seats" required>
                                        <option value='' selected disabled hidden>좌석선택</option>
                                        <option value="RS" <?php echo $row["athlete_seats"] == 'RS' ? "selected" : ""; ?>>VIP석</option>
                                        <option value="US" <?php echo $row["athlete_seats"] == 'US' ? "selected" : ""; ?>>자유석</option>
                                        <option value="AS" <?php echo $row["athlete_seats"] == 'AS' ? "selected" : ""; ?>>선수 임원석</option>
                                        <option value="MS" <?php echo $row["athlete_seats"] == 'MS' ? "selected" : ""; ?>>미디어석</option>

                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>교통 권한</span>
                                    <select name="athlete_transport">
                                        <option value='' selected disabled hidden>교통권한선택</option>
                                        <option value="T1" <?php echo $row["athlete_transport"] == 'T1' ? "selected" : ""; ?>>1인 1차량</option>
                                        <option value="T2" <?php echo $row["athlete_transport"] == 'T2' ? "selected" : ""; ?>>2인 1차량</option>
                                        <option value="TA" <?php echo $row["athlete_transport"] == 'TA' ? "selected" : ""; ?>>선수임원수송버스</option>
                                        <option value="TF" <?php echo $row["athlete_transport"] == 'TF' ? "selected" : ""; ?>>기술임원 수송버스</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>선수촌</span>
                                    <select name="athlete_village" required>
                                        <option value='' selected disabled hidden>접근시설선택</option>
                                        <option value="AV" <?php echo $row["athlete_village"] == 'AV' ? "selected" : ""; ?>>선수촌 거주 허용</option>
                                        <option value="VA" <?php echo $row["athlete_village"] == 'VA' ? "selected" : ""; ?>>선수촌 전구역(거주 불허)</option>
                                    </select>
                                </li>
                                <li class="row full_width athlete_sector">
                                    <span class="full_span">경기장 내 접근 허용</span>
                                    <div class="full_div">
                                        <?php
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="athlete_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . key($sector_dic) . '" autocomplete="off"/>';
                                            echo "<span>" . current($sector_dic) . "</span>";
                                            echo "</label>";
                                            next($sector_dic);
                                        }
                                        reset($sector_dic);
                                        ?>
                                    </div>
                                </li>
                                <li class="row input_row row_item">
                                    <span>등번호</span>
                                    <input type="number" name="athlete_bib" id="athlete_bib" value="<?php echo $row["athlete_bib"] ?>" placeholder="등번호를 입력해 주세요" required />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modifyform">
                    <div class="modify_enter">
                        <p class="tit_left_red">참석 예정 경기</p>
                        <ul class="modify_checkList">
                            <?php
                            for ($value = 1; $value <= count($sport_dic); $value++) {
                                echo "<li><label>";
                                echo '<input type="checkbox" name="athlete_schedules[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . "sports_" . key($sport_dic) . '"/>';
                                echo "<span>" . current($sport_dic) . "</span>";
                                echo "</label></li>";
                                next($sport_dic);
                            }
                            reset($sport_dic);
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="modifyform">
                    <div class="modify_enter">
                        <p class="tit_left_green">참석 확정 경기</p>
                        <ul class="modify_checkList">
                            <?php
                            for ($value = 1; $value <= count($sport_dic); $value++) {
                                echo "<li><label>";
                                echo '<input type="checkbox" name="attendance_sports[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . "attendance_" . key($sport_dic) . '"/>';
                                echo "<span>" . current($sport_dic) . "</span>";
                                echo "</label></li>";
                                next($sport_dic);
                            }
                            reset($sport_dic);
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="modifyform">

                    <!-- SB -->
                    <div class="modify_check">
                        <div class="modify_enter modify_tit_color" id="sb-section">
                            <p class="tit_left_red">SB</p>
                            <?php
                            // START FOR LOOP OF "SB"
                            // key($athlete_sb_arr) : sports_code
                            // current($athlete_sb_arr) : record
                            for ($i = 0; $i < count($athlete_sb_arr); $i++) {
                            ?>
                                <ul class="modify_checkList" id="sb-input">
                                    <select name="athlete_sb_sports[]" id="sb-sports-select">
                                        <?php
                                        // key($sport_dic) : sports_code
                                        // current($sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sport_dic); $value++) {
                                            $maintain_selected = "";
                                            if (key($athlete_sb_arr) === key($sport_dic)) {
                                                $maintain_selected = " selected";
                                            }
                                            echo '<option value="' . key($sport_dic) . '"' . $maintain_selected . '>' . current($sport_dic) . '</option>';
                                            next($sport_dic);
                                        }
                                        reset($sport_dic);
                                        ?>
                                    </select>
                                    <input type="text" name="athlete_sb[]" id="athlete_sb" value="<?php echo current($athlete_sb_arr) ?>" placeholder="SB를 입력해 주세요" onkeyup="heightFormat(this)" />
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-sb"><i class="xi-minus"></i></button>
                                </ul>

                            <?php
                                next($athlete_sb_arr);
                            }
                            reset($athlete_sb_arr);
                            // END FOR LOOP OF "SB"
                            ?>
                        </div>
                        <div class="filed_BTN2">
                            <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn" id="add-sb"><i class="xi-plus"></i></button>
                        </div>
                    </div>
                    <div class="modify_check">
                        <div class="modify_enter" id="pb-section">
                            <p class="tit_left_green">PB</p>
                            <?php
                            // START FOR LOOP OF "PB"
                            // key($athlete_pb_arr) : sports_code
                            // current($athlete_pb_arr) : record
                            for ($i = 0; $i < count($athlete_pb_arr); $i++) {
                            ?>
                                <ul class="modify_checkList" id="pb-input">
                                    <select name="athlete_pb_sports[]" id="pb-sports-select">
                                        <?php
                                        // key($sport_dic) : sports_code
                                        // current($sport_dic) : sports_name
                                        for ($value = 1; $value <= count($sport_dic); $value++) {
                                            $maintain_selected = "";
                                            if (key($athlete_pb_arr) === key($sport_dic)) {
                                                $maintain_selected = " selected";
                                            }
                                            echo '<option value="' . key($sport_dic) . '"' . $maintain_selected . '>' . current($sport_dic) . '</option>';
                                            next($sport_dic);
                                        }
                                        reset($sport_dic);
                                        ?>
                                    </select>
                                    <input type="text" name="athlete_pb[]" id="athlete_pb" value="<?php echo current($athlete_pb_arr) ?>" placeholder="PB를 입력해 주세요" />
                                    <button type=" button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-pb"><i class="xi-minus"></i></button>
                                </ul>
                            <?php
                                next($athlete_pb_arr);
                            }
                            reset($athlete_pb_arr);
                            // END FOR LOOP OF "PB"
                            ?>
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
                <div class="modify_Btn input_Btn Participant_Btn">
                    <button type="submit" class="BTN_blue2" type="button" name="athlete_edit">수정하기</button>
                </div>
            </div>
        </form>
    </div>
    <script src="/assets/js/main.js?v=8"></script>
    <?php
        require_once "action/module/athlete_modify_selected.php";
    ?>
    
</body>


</html>