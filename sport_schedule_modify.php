<?
    require_once "head.php";
    require_once "includes/auth/config.php";

    $sql = "SELECT schedule_sports,schedule_name,schedule_gender,schedule_round,schedule_location,schedule_start,schedule_status,schedule_date, schedule_result FROM list_schedule where schedule_id = '" . $_GET['id'] . "'";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    ?>

<script src="assets/js/restrict.js"></script>
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
                    일정 수정
                </p>
                <form action="action/sport/schedule_modify.php" method="post" class="form">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                    <div class="UserProfile_modify UserProfile_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row Desc_item">
                                    <span>경기 종목 코드</span>
                                    <input placeholder="경기 종목 코드" type="text" name="sports" minlength="4" maxlength="20"
                                        required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)"
                                        value="<?=$row['schedule_sports']?>" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 이름</span>
                                    <input placeholder="경기 종목 이름(한글)" type="text" name="name" minlength="4"
                                        maxlength="20" required="" value="<?php echo $row['schedule_name'] ?>" />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>성별</span>
                                    <select title="성별" name="gender">
                                        <option value="non" hidden="">경기 성별</option>
                                        <?
                                            echo '<option value="m"' . ($row['schedule_gender'] == 'm' ? 'selected' : '') . '>남성</option>';
                                            echo '<option value="f"' . ($row['schedule_gender'] == 'f' ? 'selected' : '') . '>여성</option>';
                                            echo '<option value="c"' . ($row['schedule_gender'] == 'c' ? 'selected' : '') . '>혼성</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 라운드</span>
                                    <input placeholder="경기 라운드" type="text" name="round" maxlength="50" required=""
                                        onkeyup="characterCheck(this)" onkeydown="characterCheck(this)"
                                        value="<?php echo $row['schedule_round'] ?>" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 장소</span>
                                    <input placeholder="경기 장소" type="text" name="location" maxlength="50" required=""
                                        value="<?php echo $row['schedule_location'] ?>" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 시간</span>
                                    <div>
                                        <input placeholder="시를 입력하세요" type="number" name="start_hour"
                                            style="float:left; width: 200px; padding-right: 5px;" maxlength="2"
                                            required="" oninput="maxLengthCheck(this)"
                                            value="<?=date("H", strtotime($row["schedule_start"]))?>" />
                                        <span>:</span>
                                        <input placeholder="분을 입력하세요" type="number" name="start_minute"
                                            style="width: auto;padding-left: 5px;float: left; margin-right: 5px;"
                                            maxlength="2" oninput="maxLengthCheck(this)"
                                            value="<?=date("i", strtotime($row["schedule_start"])) ?>" />
                                    </div>
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>경지 진행 상태</span>
                                    <select title="경기 진행 상태" name="result">
                                        <option value="non" hidden>경기 진행 상태</option>
                                        <?
                                            echo '<option value="n"' . ($row['schedule_result'] == 'n' ? 'selected' : '') . '>준비</option>';
                                            echo '<option value="c"' . ($row['schedule_result'] == 'c' ? 'selected' : '') . '>취소됨</option>';
                                            echo '<option value="l"' . ($row['schedule_result'] == 'l' ? 'selected' : '') . '>경기중</option>';
                                            echo '<option value="o"' . ($row['schedule_result'] == 'o' ? 'selected' : '') . '>마감</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기날짜</span>
                                    <div class="select_box">
                                        <select name="date_year">
                                            <option value="2023">2023</option>
                                        </select>
                                    </div>
                                    <div style="float:left;">-
                                    </div>
                                    <div class="select_box">
                                        <select name="date_month">
                                            <option value="06">06</option>
                                        </select>
                                    </div>
                                    <div style="float:left;">-
                                    </div>
                                    <div class="select_box">
                                        <select name="date_day">
                                            <option value="non" hidden="">경기 날짜(일)</option>
                                            <?php
                                                echo '<option value="04"' . (date("d", strtotime($row["schedule_date"])) == '4' ? 'selected' : '') . '>04</option>';
                                                echo '<option value="05"' . (date("d", strtotime($row["schedule_date"])) == '5' ? 'selected' : '') . '>05</option>';
                                                echo '<option value="06"' . (date("d", strtotime($row["schedule_date"])) == '6' ? 'selected' : '') . '>06</option>';
                                                echo '<option value="07"' . (date("d", strtotime($row["schedule_date"])) == '7' ? 'selected' : '') . '>07</option>';
                                            ?>
                                        </select>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" name="signup" class="changePwBtn defaultBtn">수정</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=6"></script>
</body>

</html>