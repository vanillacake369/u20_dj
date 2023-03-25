<?php
require_once "head.php";
if (!$_POST['athlete_id']) {
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
        where athlete_id=" . $_POST['athlete_id'];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$birth = explode('-', $row["athlete_birth"]); //생일 정보 나눔
// sb array
$athlete_sb_arr = json_decode($row['athlete_sb'], true);
// pb array
$athlete_pb_arr = json_decode($row['athlete_pb'], true);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
                            <img src=<?php echo "./assets/img/athlete_img/" . $row["athlete_profile"] ?> alt="avatar" />
                        </div>
                        <div>
                            <input type='hidden' name='athlete_id' value=<?= $_POST['athlete_id'] ?>>
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
                                            echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row">
                                    <span>지역</span>
                                    <input type="text" name="athlete_region" id="athlete_region" value=<?php echo htmlspecialchars($row["athlete_region"]) ?> />
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="athlete_division" id="athlete_division" value=<?= htmlspecialchars($row["athlete_division"]) ?> class="input_text_row" />
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
                                    <input type="text" name="athlete_sb[]" id="athlete_sb" value="<?php echo current($athlete_sb_arr) ?>" placeholder="SB를 입력해 주세요" />
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
                                    <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn" id="delete-pb"><i class="xi-minus"></i></button>
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
                    </script>
                </div>
                <div class="modify_Btn input_Btn Participant_Btn">
                    <button type="submit" class="BTN_blue2" type="button" name="athlete_edit">수정하기</button>
                </div>
            </div>
        </form>
    </div>
    <script src="/assets/js/main.js?v=6"></script>
</body>
<?php
require_once "action/module/athlete_modify_selected.php";
?>

</html>