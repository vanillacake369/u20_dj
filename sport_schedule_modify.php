<?php
require_once "head.php";
require_once "includes/auth/config.php";

$sql = "SELECT schedule_sports,schedule_name,schedule_gender,schedule_round,schedule_location,schedule_start,record_state,schedule_date,record_state FROM list_schedule join list_record on record_sports=schedule_sports and record_gender=schedule_gender and record_round=schedule_round where schedule_sports='".$_GET['sports']."' and schedule_gender='".$_GET['gender']."' and schedule_round='".$_GET['round']."'";
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
                    <input type="hidden" name="search_sports" value="<?php echo $row['schedule_sports'] ?>">
                    <input type="hidden" name="search_name" value="<?php echo $row['schedule_name'] ?>">
                    <input type="hidden" name="search_gender" value="<?php echo $_GET['gender'] ?>">
                    <input type="hidden" name="search_round" value="<?php echo $_GET['round'] ?>">
                    <div class="UserProfile_modify UserProfile_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row Desc_item">
                                    <span>경기 종목 코드</span>
                                    <input placeholder="경기 종목 코드" type="text" name="sports" minlength="4" maxlength="20"
                                        required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)"
                                        value="<?= $row['schedule_sports'] ?>" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 이름</span>
                                    <input placeholder="경기 종목 이름(한글)" type="text" name="name" minlength="4"
                                        maxlength="20" required="" value="<?php echo $row['schedule_name'] ?>" />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>성별</span>
                                    <select title="성별" name="gender" style="width: 200px;">
                                        <option value="non" hidden="">경기 성별</option>
                                        <?php
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
                                        value="<?php echo $row['schedule_location']; ?>" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기 시간</span>
                                    <div>
                                        <input placeholder="시를 입력하세요" type="number" name="start_hour" maxlength="2" required="" oninput="maxLengthCheck(this)" value="<?= date("H", strtotime($row["schedule_start"])) ?>" />
                                        <span>:</span>
                                        <input placeholder="분을 입력하세요" type="number" name="start_minute" maxlength="2" oninput="maxLengthCheck(this)" value="<?= date("i", strtotime($row["schedule_start"])) ?>" />
                                    </div>
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>경지 진행 상태</span>
                                    <select title="경기 진행 상태" name="result" style="width: 200px;">
                                        <option value="non" hidden>경기 진행 상태</option>
                                        <?php
                                        echo '<option value="n"' . ($row['record_state'] == 'n' ? 'selected' : '') . '>준비</option>';
                                        echo '<option value="c"' . ($row['record_state'] == 'c' ? 'selected' : '') . '>취소됨</option>';
                                        echo '<option value="l"' . ($row['record_state'] == 'l' ? 'selected' : '') . '>경기중</option>';
                                        echo '<option value="o"' . ($row['record_state'] == 'o' ? 'selected' : '') . '>마감</option>';
                                        ?>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>경기날짜</span>
                                    <div>
                                        <input placeholder="(YYYY)" type="number" name="date_year" maxlength="4"
                                            required="" oninput="maxLengthCheck(this)"
                                            value="<?= date("Y", strtotime($row["schedule_date"])) ?>" />
                                        <span>:</span>
                                        <input placeholder="(mm)" type="number" name="date_month"
                                            maxlength="2" oninput="maxLengthCheck(this)"
                                            value="<?= date("M", strtotime($row["schedule_date"])) ?>" />
                                        <span>:</span>
                                        <input placeholder="(dd)" type="number" name="date_day"
                                            maxlength="2" oninput="maxLengthCheck(this)"
                                            value="<?= date("D", strtotime($row["schedule_date"])) ?>" />
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" name="signup" class="changePwBtn">수정</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=6"></script>
</body>

</html>