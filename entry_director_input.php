<?
    require_once "head.php";
    // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
    require_once "includes/auth/config.php";
    // 국가,종목,지역,직무에 대한 매핑구조
    require_once "action/module/dictionary.php";
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
                    임원 정보
                </p>
                <form action="action/module/director_Insert.php" method="post" class="form"
                    enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input coachArea">
                        <div>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row input_row">
                                    <span>이름</span>
                                    <input type="text" name="director_second_name" id="director_name" value=""
                                        placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row">
                                    <span>성</span>
                                    <input type="text" name="director_first_name" id="director_name" value=""
                                        placeholder="성을 입력해 주세요" required />
                                </li>
                                <li class="row input_row input_width">
                                    <span>국가</span>
                                    <select name="director_country" required>
                                        <option value='' selected disabled hidden>국가 선택</option>
                                        <?php
                                            foreach ($country_code_dic as $key => $value)
                                                echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row">
                                    <span>소속</span>
                                    <input type="text" name="director_division" id="director_division" value=""
                                        placeholder="소속을 입력해 주세요" required />
                                </li>
                                <li class="row input_row input_width">
                                    <span>직무</span>
                                    <select name="director_duty" required>
                                        <option value="h">직무1</option>
                                        <option value="s">직무2</option>
                                    </select>
                                </li>
                                <li class="row input_row input_width">
                                    <span>성별</span>
                                    <select name="director_gender" required>
                                        <option value='' selected disabled hidden>성별 선택</option>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="text" name="director_birth_year" class="input_text_row_b"
                                        placeholder="연" required>
                                    <input type="text" name="director_birth_month" class="input_text_row_b"
                                        placeholder="월" required>
                                    <input type="text" name="director_birth_day" class="input_text_row_b"
                                        placeholder="일" required>
                                </li>
                                <li class="row input_row">
                                    <span>나이</span>
                                    <input type="text" name="director_age" id="director_age" value=""
                                        placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row">
                                    <span>이미지 변경</span>
                                    <input type="file" name="director_imgFile" />
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
                                    for ($value = 1; $value <= count($sport_dic); $value++) {
                                        echo "<li><label>";
                                        echo '<input type="checkbox" name="director_schedules[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . current($sport_dic) . '"/>';
                                        echo "<span>" . current($sport_dic) . "</span>";
                                        echo "</label></li>";
                                        next($sport_dic);
                                    }
                                    reset($sport_dic);
                                ?>
                                </ul>
                            </div>
                        </div>
                        <div class="modify_check">
                            <div class="modify_enter">
                                <p class="tit_left_green">참가 경기</p>
                                <ul class="modify_checkList">
                                    <?php
                                        for ($value = 1; $value <= count($sport_dic); $value++) {
                                            echo "<li><label>";
                                            echo '<input type="checkbox" name="attendance_sports[]"' . 'value="' . key($sport_dic) . '"' . 'id="' . current($sport_dic) . '"/>';
                                            echo "<span>" . current($sport_dic) . "</span>";
                                            echo "</label></li>";
                                            next($sport_dic);
                                        }
                                        reset($sport_dic);
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modify_Btn input_Btn Participant_Btn">
                    <button type="submit" class="BTN_blue2" name="director_edit">등록하기</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>