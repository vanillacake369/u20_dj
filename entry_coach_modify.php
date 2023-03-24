<?php
require_once "head.php";
if (!$_POST['coach_id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
}
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

$sql = "SELECT 
            coach_id,
            coach_name,
            coach_country,
            country_name_kr,
            country_code,
            coach_region,
            coach_division,
            coach_gender,
            coach_birth,
            coach_age, 
            coach_duty,
            coach_sector,
            coach_schedule,
            coach_profile,
            coach_attendance
            FROM list_coach
            INNER JOIN list_country  
            ON coach_country=country_code
            where coach_id=" . $_POST['coach_id'];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$birth = explode('-', $row["coach_birth"]); //생일 정보 나눔
?>

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
                    코치 정보
                </p>
                <form action="">
                    <div class="UserProfile_modify coachArea Participant_img ptp_img">
                        <div>
                            <img src="<?php echo "./assets/img/coach_img/" . $row["coach_profile"] ?>" alt="avatar">
                        </div>
                        <div>
                            <ul class="UserDesc Participant_list">
                                <input type='hidden' name='coach_id' value=<?= $_POST['coach_id'] ?>>
                                <?php
                                $name = explode(" ", $row["coach_name"]);
                                $secondName = isset($name[0]) ? $name[0] : NULL;
                                $firstName = isset($name[1]) ? $name[1] : NULL;
                                $fullName = $secondName . " " . $firstName;
                                ?>
                                <li class="row">
                                    <span>이름</span>
                                    <input type="text" name="coach_second_name" id="coach_name" value="<?= $secondName ?>" />
                                </li>
                                <li class="row">
                                    <span>성</span>
                                    <input type="text" name="coach_second_name" id="coach_name" value="<?= $firstName ?>" />
                                </li>
                                <li class="row modify_input">
                                    <span>국가</span>
                                    <select title="국가" name="coach_country" id="coach_country">
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row">
                                    <span>지역</span>
                                    <input type="text" name="coach_region" id="coach_region" value=<?= htmlspecialchars($row["coach_region"]) ?> />
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="coach_division" id="coach_division" value=<?php echo htmlspecialchars($row["coach_division"]) ?> />
                                </li>
                                <li class="row modify_input">
                                    <span>직무</span>
                                    <select title="직무" id="coach_duty" name="coach_duty" required>
                                        <?php
                                        foreach ($coach_duty_dic as $duty) {
                                            echo '<option value=' . '"' . $duty . '"' . 'id="' . $duty . '">' . $duty . '</option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                <li class="row modify_input">
                                    <span>성별</span>
                                    <select id="coach_gender" name="coach_gender">
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="number" value=<?= htmlspecialchars($birth[0]) ?> name="coach_birth_year" placeholder="연">
                                    <input type="number" value=<?= htmlspecialchars($birth[1]) ?> name="coach_birth_month" placeholder="월">
                                    <input type="number" value=<?= htmlspecialchars($birth[2]) ?> name="coach_birth_day" placeholder="일">
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <input type="number" name="coach_age" id="coach_age" value=<?php echo htmlspecialchars($row["coach_age"]) ?> />
                                </li>
                                <li class="row">
                                    <span>이미지 변경</span>
                                    <input type="file" name="coach_imgFile" />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">출입가능구역</span>
                                    <div class="full_div">
                                        <?php
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="coach_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . current($sector_dic) . '"/>';
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
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" class="BTN_blue2" name="coach_edit">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<?php
require_once "action/module/coach_modify_selected.php";
?>

</html>