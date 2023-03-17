<?
    require_once "head.php";
    if (!$_POST['director_id']) {
        echo "<script>alert('잘못된 유입경로입니다.')</script>";
        exit();
    }
    require_once "includes/auth/config.php";
    // 국가,종목,지역,직무에 대한 매핑구조
    require_once "action/module/dictionary.php";

    require_once "backheader.php";

    if (!authCheck($db, "authEntrysRead")) {
        exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
    }

    $sql = "SELECT 
            director_id,
            director_name,
            director_country,
            country_name_kr,
            country_code,
            director_division,
            director_gender,
            director_birth,
            director_age, 
            director_duty,
            director_sector,
            director_schedule,
            director_profile,
            director_attendance
            FROM list_director
            INNER JOIN list_country  
            ON director_country=country_code
            where director_id=" . $_POST['director_id'];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$birth = explode('-', $row["director_birth"]); //생일 정보 나눔
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
                    임원 정보
                </p>
                <form action="./action/module/director_update.php" method="post" class="form"
                    enctype="multipart/form-data">
                    <div class="UserProfile_modify coachArea Participant_img ptp_img">
                        <div>
                            <img src=<?php echo "./assets/img/director_img/" . $row["director_profile"] ?>
                                alt="avatar" />
                        </div>
                        <div>
                            <ul class="UserDesc Participant_list">
                                <input type='hidden' name='director_id' value=<?php echo $_POST['director_id'] ?>>
                                <?php
                                    $name = explode(" ", $row["director_name"]);
                                    $secondName = isset($name[0]) ? $name[0] : NULL;
                                    $firstName = isset($name[1]) ? $name[1] : NULL;
                                    $fullName = $secondName . " " . $firstName;
                                ?>
                                <li class="row">
                                    <span>이름</span>
                                    <?
                                        echo '<input type="text" name="director_second_name" id="director_name"' . "value=\"" . $secondName . "\"" . '/>';
                                    ?>
                                </li>
                                <li class="row">
                                    <span>성</span>
                                    <?
                                        echo '<input type="text" name="director_first_name" id="director_name"' . "value=\"" . $firstName . "\"" . ' />';
                                    ?>
                                </li>
                                <li class="row modify_input">
                                    <span>국가</span>
                                    <select name="director_country" id="director_country">
                                        <?
                                            foreach ($country_code_dic as $key => $value)
                                                echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="director_division" id="director_division" value=<? echo
                                        htmlspecialchars($row["director_division"]) ?> />
                                </li>
                                <li class="row modify_input">
                                    <span>직무</span>
                                    <select name="director_duty" required>
                                        <option value="h">직무1</option>
                                        <option value="s">직무2</option>
                                    </select>
                                </li>
                                <li class="row modify_input">
                                    <span>성별</span>
                                    <select class="d_select" name="director_gender" id="director_gender">
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="text" value=<?php echo htmlspecialchars($birth[0]) ?>
                                        name="director_birth_year" class="input_text_row_b" placeholder="연">
                                    <input type="text" value=<?php echo htmlspecialchars($birth[1]) ?>
                                        name="director_birth_month" class="input_text_row_b" placeholder="월">
                                    <input type="text" value=<?php echo htmlspecialchars($birth[2]) ?>
                                        name="director_birth_day" class="input_text_row_b" placeholder="일">
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <input type="text" name="director_age" id="director_age"
                                        value=<?php echo htmlspecialchars($row["director_age"]) ?> />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">이미지 변경</span>
                                    <input type="file" name="director_imgFile" />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">출입가능구역</span>
                                    <div class="full_div">
                                        <?
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="director_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . current($sector_dic) . '"/>';
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
                        <button type="submit" class="BTN_blue2" name="director_edit">확인</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script type="text/javascript" src="/assets/js/main.js?ver=8"></script>
</body>
<?php
    require_once "action/module/director_modify_selected.php";
    ?>

</html>