<?
    require_once "head.php";
    if (!$_POST['athlete_id']) {
        echo "<script>alert('잘못된 유입경로입니다.')</script>";
        exit();
    } else {
        // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
        include_once(__DIR__ . "/includes/auth/config.php");
        // 국가,종목,지역,직무에 대한 매핑구조
        include_once(__DIR__ . "/action/module/dictionary.php");
        $sql =
            "SELECT 
                athlete_id,
                athlete_name,
                country_name,
                country_name_kr,
                country_code,
                athlete_region,
                athlete_division,
                athlete_gender,
                athlete_birth,
                athlete_age, 
                athlete_schedule,
                athlete_profile,
                athlete_attendance
                FROM list_athlete
                INNER JOIN list_country  
                ON athlete_country=country_code
            where athlete_id=" . $_POST['athlete_id'];
        $result = $db->query($sql);
        $row = mysqli_fetch_array($result);
    }
?>

<body>
    <div class="container AD_container">
        <div class="athlete AD_box">
            <div class="AD_front">
                <div class="AD_card">
                    <img src="assets/images/pink_front.png" alt="">
                </div>
                <div class="AD_front_User">
                    <img src="<? echo " ./assets/img/athlete_img/" . $row["athlete_profile"] ?>" alt="">
                </div>
                <div class="AD_form">
                    <div class="AD_front_name">
                        <!-- 이름 -->
                        <p><?php echo htmlspecialchars($row["athlete_name"]) ?></p>
                    </div>
                </div>
                <!-- 직책 -->
                <div class="AD_front_desc">
                    <p>Technical Official</p>
                </div>
                <!-- 접근코드 -->
                <div class="AD_Venue">
                    <p><?php echo htmlspecialchars($row["athlete_division"]) ?></p>
                </div>
                <!-- 시설 접근 코드 -->
                <!-- 선수촌 거주 허용 코드 -->
                <p class="village">AV</p>
                <!-- 식사 가능 여부 없을 시 이미지 삭제 -->
                <img class="eat" src="/assets/images/eat.png" alt="">
                <!-- 교통권한 -->
                <p class="transport">T1</p>
                <!-- 경기장 내 좌석 -->
                <p class="Seats">RS</p>
            </div>
            <div class="AD_back">
                <div class="AD_card">
                    <img src="assets/images/pink_back.png" alt="">
                </div>
                <div class="AD_back_User">
                    <img src="<? echo " ./assets/img/athlete_img/" . $row["athlete_profile"] ?>" alt="">
                </div>
                <div class="AD_back_name">
                    <!-- 이름 -->
                    <p><?php echo htmlspecialchars($row["athlete_name"]) ?></p>
                </div>
                <div class="AD_back_nationality back_container">
                    <!-- 지역 -->
                    <p><?php echo htmlspecialchars($row["country_code"]) ?></p>
                    <!-- 생년월일 -->
                    <p><?php echo htmlspecialchars($row["athlete_birth"]) ?></p>
                    <!-- 성별ㄴ -->
                    <p><?php echo htmlspecialchars($row["athlete_gender"]) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/main_dh.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>