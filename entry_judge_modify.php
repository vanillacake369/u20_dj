<?php
require_once "head.php";

// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";

require_once "backheader.php";

if (!$_POST['judge_id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
}
if (!authCheck($db, "authEntrysRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}



$sql = "SELECT 
            judge_id,
            judge_name,
            judge_country,
            country_name_kr,
            country_code,
            judge_division,
            judge_gender,
            judge_birth,
            judge_age, 
            judge_duty,
            judge_sector,
            judge_schedule,
            judge_profile,
            judge_attendance,
            judge_account,
            judge_password
            FROM list_judge
            INNER JOIN list_country  
            ON judge_country=country_code
            where judge_id=" . $_POST['judge_id'];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$birth = explode('-', $row["judge_birth"]); //생일 정보 나눔

?>
<script src="/assets/js/restrict.js?v=1"></script>
</head>

<body>
    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    심판 정보
                </p>
                <form action="./action/module/judge_update.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify coachArea Participant_img ptp_img">
                        <div>
                            <img src=<?php echo "./assets/img/judge_img/" . $row["judge_profile"] ?> alt="avatar" />

                        </div>
                        <div>
                            <span id="message" style="padding-left: 10px;" style="display:none"></span>
                            <input type='hidden' name='judge_id' value=<?php echo $_POST['judge_id'] ?>>
                            <?php
                            $name = explode(" ", $row["judge_name"]);
                            $secondName = isset($name[0]) ? $name[0] : NULL;
                            $firstName = isset($name[1]) ? $name[1] : NULL;
                            $fullName = $secondName . " " . $firstName;
                            ?>
                            <ul class="UserDesc Participant_list">
                                <li class="row check">
                                    <span>태블릿(사용여부)</span>
                                    <input type="checkbox" name="is_using_tablet" id="is_using_tablet" onclick="isUsingTablet()" />
                                </li>
                                <li class="row" id="use_pw" style="display:none">
                                    <span>비밀번호</span>
                                    <input type="password" name="judge_password" id="judge_password" value="" minlength="4" maxlength="20" placeholder="새로운 비밀번호를 입력해 주세요" />
                                </li>
                                <li class="row" id="use_pw_check" style="display:none">
                                    <span>비밀번호 재확인</span>
                                    <input type="password" name="cpassword" id="cpassword" value="" minlength="4" maxlength="20" placeholder="비밀번호를 다시 입력해 주세요" onkeyup="check();" />
                                </li>
                                <li class="row">
                                    <span>이름</span>
                                    <?php
                                    echo '<input type="text" name="judge_second_name" id="judge_second_name"' . "value=\"" . $secondName . "\"" . ' />';
                                    ?>
                                </li>
                                <li class="row">
                                    <span>성</span>
                                    <?php
                                    echo '<input type="text" name="judge_first_name" id="judge_first_name"' . "value=\"" . $firstName . "\"" . '/>';
                                    ?>
                                </li>
                                <li class="row modify_input">
                                    <span>국가</span>
                                    <select name="judge_country" id="judge_country">
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo '<option value=' . '"' . $value . '"' . '">' . $key . '</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="judge_division" id="judge_division" value=<?php echo htmlspecialchars($row["judge_division"]) ?> />
                                </li>
                                <li class="row modify_input">
                                    <span>직무</span>
                                    <select title="직무" name="judge_duty" id="judge_duty">
                                        <option value="" disabled selected>직무</option>
                                        <?php
                                        foreach ($judge_duty_dic as $duty) {
                                            echo '<option value=' . '"' . $duty . '"' . 'id="' . $duty . '">' . $duty . '</option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                <li class="row modify_input">
                                    <span>성별</span>
                                    <select name="judge_gender" id="judge_gender">
                                        <?php
                                        echo '<option value="m"' . ($row['judge_gender'] == 'm' ? 'selected' : '') . '>남자</option>';
                                        echo '<option value="f"' . ($row['judge_gender'] == 'f' ? 'selected' : '') . '>여자</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" value=<?php echo htmlspecialchars($birth[0]) ?> name="judge_birth_year" class="input_text_row_b" placeholder="연">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[1]) ?> name="judge_birth_month" class="input_text_row_b" placeholder="월">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[2]) ?> name="judge_birth_day" class="input_text_row_b" placeholder="일">
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <input type="number" name="judge_age" id="judge_age" value=<?php echo htmlspecialchars($row["judge_age"]) ?> />
                                </li>
                                <li class="row">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" class="form-control" />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">출입가능구역</span>
                                    <div class="full_div">
                                        <?php
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="judge_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . current($sector_dic) . '"/>';
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
                                <p class="tit_left_red">참석 경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                    for ($value = 1; $value <= count($judge_sport_dic); $value++) {
                                        echo '<li><label>';
                                        echo '<input type="checkbox" name="judge_schedules[]"' . 'value="' . key($judge_sport_dic) . '"' . 'id="' . "sports_" . key($judge_sport_dic) . '"/>';
                                        echo "<span>" . current($judge_sport_dic) . "</span>";
                                        echo "</label></li>";
                                        next($judge_sport_dic);
                                    }
                                    reset($judge_sport_dic);
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="modify_check">
                            <div class="modify_enter">
                                <p class="tit_left_green">참가 경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                    for ($value = 1; $value <= count($judge_sport_dic); $value++) {
                                        echo "<li><label>";
                                        echo '<input type="checkbox" name="attendance_sports[]"' . 'value="' . key($judge_sport_dic) . '"' . 'id="' . "attendance_" . key($judge_sport_dic) . '"/>';
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
                        <button type="submit" class="BTN_blue2" name="judge_edit">수정하기</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script src="/assets/js/main.js?ver=7"></script>
    <?php require_once "action/module/judge_modify_selected.php"; ?>
</body>

</html>