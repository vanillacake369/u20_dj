<?
    require_once "head.php";
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
?>
<!-- 태블릿 심판 유무 스크립트 -->
<script src="/assets/js/restrict.js?ver=1"></script>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    심판 정보
                </p>
                <form action="action/module/judge_insert.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify UserProfile_input coachArea">
                        <div>
                            <span id="message" style="padding-left: 10px;" style="display:none"></span>
                            <ul class="UserDesc Participant_list Participant_padding">
                                <li class="row check">
                                    <span>태블릿 사용 심판이신가요?(맞다면 체크)</span>
                                    <input type="checkbox" name="is_using_tablet" id="is_using_tablet"
                                        onclick="isUsingTablet()" />
                                </li>
                                <li class="row input_row Desc_item" id="userid" style="display:none">
                                    <span>아이디</span>
                                    <input type="text" size="35" name="userid" id="user_id"
                                        placeholder="아이디를 입력해 주세요" />
                                </li>
                                <li class="row input_row Desc_item" id="use_pw" style="display:none">
                                    <span>비밀번호</span>
                                    <input type="password" name="judge_password" id="judge_password" value=""
                                        minlength="8" maxlength="20" placeholder="비밀번호를 입력해 주세요" />
                                </li>
                                <li class="row input_row Desc_item" id="use_pw_check" style="display:none">
                                    <span class="input_guide">비밀번호 재확인</span>
                                    <input type="password" name="cpassword" id="cpassword" value="" minlength="8"
                                        maxlength="20" class="input_text_row" placeholder="비밀번호를 다시 입력해 주세요"
                                        onkeyup="check();" />
                                </li>

                                <li class="row input_row Desc_item">
                                    <span>이름</span>
                                    <input type="text" name="judge_second_name" id="judge_name" value=""
                                        placeholder="이름을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>성</span>
                                    <input type="text" name="judge_first_name" id="judge_name" value=""
                                        placeholder="성을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>국가</span>
                                    <select name="judge_country" required>
                                        <option value='' selected disabled hidden>국가 선택</option>
                                        <?
                                        foreach ($country_code_dic as $key => $value)
                                            echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>소속</span>
                                    <input type="text" name="judge_division" id="judge_division" value=""
                                        placeholder="소속을 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>직무</span>
                                    <input type="text" name="judge_duty" id="judge_duty" value=""
                                        placeholder="직무를 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>성별</span>
                                    <select name="judge_gender" required>
                                        <option value='' selected disabled hidden>성별 선택</option>
                                        <option value="m">남자</option>
                                        <option value="f">여자</option>
                                    </select>
                                </li>
                                <li class="row input_row row_item row_date">
                                    <span>생년월일</span>
                                    <input type="text" name="judge_birth_year" class="input_text_row_b" placeholder="연"
                                        required>
                                    <input type="text" name="judge_birth_month" class="input_text_row_b" placeholder="월"
                                        required>
                                    <input type="text" name="judge_birth_day" class="input_text_row_b" placeholder="일"
                                        required>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>나이</span>
                                    <input type="text" name="judge_age" id="judge_age" value=""
                                        placeholder="나이를 입력해 주세요" required />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>이미지 변경</span>
                                    <input type="file" name="judge_imgFile" />
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
                        <div class="modify_check full_width">
                            <div class="modify_enter modify_tit_color">
                                <p class="tit_left_red">참가예정경기</p>
                                <ul class="modify_checkList">
                                    <?
                                        for ($value = 1; $value <= count($judge_sport_dic); $value++) {

                                            echo "<li><label>";
                                            echo '<input type="checkbox" name="judge_schedules[]"' . 'value="' . key($judge_sport_dic) . '"' . 'id="' . current($judge_sport_dic) . '"/>';
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
                        <button type="submit" class="BTN_blue2" name="judge_edit">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>