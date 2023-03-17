<?
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
                <form action="./action/module/coach_Insert.php" method="post" class="form"
                    enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input coachArea">
                        <div>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row input_row row_item">
                                    <span>이름</span>
                                    <input type="text" name="coach_second_name" id="coach_name" value=""
                                        placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>성</span>
                                    <input type="text" name="coach_first_name" id="coach_name" value=""
                                        placeholder="성을 입력해 주세요" required />
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
                                    <input type="text" name="coach_region" id="coach_region" value=""
                                        placeholder="지역을 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>소속</span>
                                    <input type="text" name="coach_division" id="coach_division" value=""
                                        placeholder="소속을 입력해 주세요" required />
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
                                    <input type="text" name="coach_birth_year" placeholder="연" required value="">
                                    <input type="text" name="coach_birth_month" placeholder="월" required value="">
                                    <input type="text" name="coach_birth_day" placeholder="일" required value="">
                                </li>
                                <li class="row input_row row_item">
                                    <span>나이</span>
                                    <input type="text" name="coach_age" id="coach_age" value=""
                                        placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row row_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="coach_imgFile" />
                                </li>
                                <li class="row full_width">
                                    <span class="full_span">출입가능구역</span>
                                    <div class="full_div">
                                        <?
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
                </form>
                <div class="modify_Btn input_Btn Participant_Btn">
                    <button type="submit" class="BTN_blue2" name="coach_edit">확인</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>