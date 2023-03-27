<?php
require_once "head.php";
if (!$_GET['id']) {
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

$sql = "SELECT *
            FROM list_director
            INNER JOIN list_country  
            ON director_country=country_code
            where director_id=" . $_GET['id'];
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
                <form action="./action/module/director_update.php" method="post" class="form" enctype="multipart/form-data">
                    <div class="UserProfile_modify coachArea Participant_img ptp_img">
                        <div>
                            <?php if ((!isset($row["director_profile"]) || $row["director_profile"] == "")|| !file_exists("./assets/img/director_img/" . $row["director_profile"]))
                            {
                            ?>
                            <img src=<?php echo "./assets/img/profile.jpg" ?> alt="avatar" />
                            <?php }else{?>
                            <img src=<?php echo "./assets/img/director_img/" . $row["director_profile"] ?> alt="avatar" />
                            <?php }?>
                        </div>
                        <div>
                            <ul class="UserDesc Participant_list">
                                <input type='hidden' name='director_id' value=<?php echo$_GET['id'] ?>>
                                <?php
                                $name = explode(" ", $row["director_name"]);
                                $secondName = isset($name[0]) ? $name[0] : NULL;
                                $firstName = isset($name[1]) ? $name[1] : NULL;
                                $fullName = $secondName . " " . $firstName;
                                ?>
                                <li class="row">
                                    <span>이름</span>
                                    <?php
                                    echo '<input type="text" name="director_second_name" id="director_name"' . "value=\"" . $secondName . "\"" . '/>';
                                    ?>
                                </li>
                                <li class="row">
                                    <span>성</span>
                                    <?php
                                    echo '<input type="text" name="director_first_name" id="director_name"' . "value=\"" . $firstName . "\"" . ' />';
                                    ?>
                                </li>
                                <li class="row modify_input">
                                    <span>국가</span>
                                    <select name="director_country" id="director_country">
                                        <?php
                                        foreach ($country_code_dic as $key => $value)
                                            echo "<option value=" . $value . ">" . $key . "</option>";
                                        ?>
                                    </select>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <input type="text" name="director_division" id="director_division" value=<?php echo htmlspecialchars($row["director_division"]) ?> />
                                </li>
                                <li class="row modify_input">
                                    <span>직무</span>
                                    <select name="director_duty" required>
                                        <option value="" disabled selected>직무</option>
                                        <?php
                                        foreach ($director_duty_dic as $duty) {
                                            echo '<option value=' . '"' . $duty . '"' . 'id="' . $duty . '">' . $duty . '</option>';
                                        }
                                        ?>
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
                                    <input type="number" value=<?php echo htmlspecialchars($birth[0]) ?> name="director_birth_year" class="input_text_row_b" placeholder="연" required maxlength="4" oninput="maxLengthCheck(this)">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[1]) ?> name="director_birth_month" class="input_text_row_b" placeholder="월" required maxlength="2" oninput="maxLengthCheck(this)">
                                    <input type="number" value=<?php echo htmlspecialchars($birth[2]) ?> name="director_birth_day" class="input_text_row_b" placeholder="일" required maxlength="2" oninput="maxLengthCheck(this)">
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <input type="number" name="director_age" id="director_age" value=<?php echo htmlspecialchars($row["director_age"]) ?> required maxlength="2" oninput="maxLengthCheck(this)"/>
                                </li>
                                <li class="row">
                                    <span>이미지 변경</span>
                                    <input type="file" name="main_photo" />
                                </li>
                                <li class="row input_row row_item">
                                    <span>식사 가능 여부</span>
                                    <input type="checkbox" name="director_eat" value="식사" <?php echo $row["director_eat"] == 'y' ? "selected" : "";?>>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>대회접근시설</span>
                                        <select name="director_venue_access" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="Y" <?php echo $row["director_venue_access"] == 'Y' ? "selected" : "";?>>전 구역</option>
                                            <option value="HQ" <?php echo $row["director_venue_access"] == 'HQ' ? "selected" : "";?>>본부호텔</option>
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>경기장 내 좌석</span>
                                        <select name="director_seats" required>
                                            <option value='' selected disabled hidden>좌석선택</option>
                                            <option value="RS" <?php echo $row["director_seats"] == 'RS' ? "selected" : "";?>>VIP석</option>
                                            <option value="US" <?php echo $row["director_seats"] == 'US' ? "selected" : "";?>>자유석</option>
                                            <option value="AS" <?php echo $row["director_seats"] == 'AS' ? "selected" : "";?>>선수 임원석</option>
                                            <option value="MS" <?php echo $row["director_seats"] == 'MS' ? "selected" : "";?>>미디어석</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>교통 권한</span>
                                        <select name="director_transport">
                                            <option value='' selected disabled hidden>교통권한선택</option>
                                            <option value="T1" <?php echo $row["director_transport"] == 'T1' ? "selected" : "";?>>1인 1차량</option>
                                            <option value="T2" <?php echo $row["director_transport"] == 'T2' ? "selected" : "";?>>2인 1차량</option>
                                            <option value="TA" <?php echo $row["director_transport"] == 'TA' ? "selected" : "";?>>선수임원수송버스</option>
                                            <option value="TF" <?php echo $row["director_transport"] == 'TF' ? "selected" : "";?>>기술임원 수송버스</option>
                                            
                                        </select>
                                </li>
                                <li class="row input_row row_item input_width">
                                    <span>선수촌</span>
                                        <select name="director_village" required>
                                            <option value='' selected disabled hidden>접근시설선택</option>
                                            <option value="AV" <?php echo $row["director_village"] == 'AV' ? "selected" : "";?>>선수촌 거주 허용</option>
                                            <option value="VA" <?php echo $row["director_village"] == 'VA' ? "selected" : "";?>>선수촌 전구역(거주 불허)</option>
                                        </select>
                                </li>
                                <li class="row full_width director_sector">
                                <span class="full_span">경기장 내 접근 허용</span>
                                    <div class="full_div">
                                        <?php
                                        for ($value = 1; $value <= count($sector_dic); $value++) {
                                            echo "<label>";
                                            echo '<input type="checkbox" name="director_sector[]"' . 'value="' . key($sector_dic) . '"' . 'id="' . key($sector_dic) . '" autocomplete="off"/>';
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
    <script type="text/javascript" src="/assets/js/main.js?ver=12"></script>
    <?php
    require_once "action/module/director_modify_selected.php";
    ?>
    <script type="text/javascript">
    function maxLengthCheck(object){
      if (object.value.length > object.maxLength){
        object.value = object.value.slice(0, object.maxLength);
      }    
    }
  </script>
</body>


</html>