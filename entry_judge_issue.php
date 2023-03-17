<?
  require_once "head.php";
  if (!$_POST['judge_id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
  } else {
    // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
    include_once(__DIR__ . "/includes/auth/config.php");
    // 국가,종목,지역,직무에 대한 매핑구조
    include_once(__DIR__ . "/action/module/dictionary.php");//B:서치 select 태크 사용하기 위한 자료구조
    $sql =
      "SELECT 
                    judge_id,
                    judge_name,
                    country_name,
                    country_name_kr,
                    country_code,
                    judge_division,
                    judge_gender,
                    judge_birth,
                    judge_age, 
                    judge_duty,
                    judge_schedule,
                    judge_profile,
                    judge_attendance
                    FROM list_judge
                    INNER JOIN list_country  
                    ON judge_country=country_code
                where judge_id=" . $_POST['judge_id'];
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
  }
?>
</head>

<body>
    <div class="container AD_container">
        <div class="athlete AD_box">
            <div class="AD_front">
                <div class="AD_card">
                    <img src="assets/images/pink_front.png" alt="">
                </div>
                <div class="AD_front_User">
                    <img src="<? echo " ./assets/img/judge_img/" . $row["judge_profile"] ?>" alt="">
                </div>
                <div class="AD_form">
                    <div class="AD_front_name">
                        <!-- 이름 -->
                        <p><?php echo htmlspecialchars($row["judge_name"]) ?></p>
                    </div>
                </div>
                <!-- 직책 -->
                <div class="AD_front_desc">
                    <p>Technical Official</p>
                </div>
                <!-- 접근코드 -->
                <div class="AD_Venue">
                    <p>Judge</p>
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
                    <img src="<? echo " ./assets/img/judge_img/" . $row["judge_profile"] ?>" alt="">
                </div>
                <div class="AD_back_name">
                    <!-- 이름 -->
                    <p><?php echo htmlspecialchars($row["judge_name"]) ?></p>
                </div>
                <div class="AD_back_nationality back_container">
                    <!-- 지역 -->
                    <p><?php echo htmlspecialchars($row["country_code"]) ?></p>
                    <!-- 생년월일 -->
                    <p><?php echo htmlspecialchars($row["judge_birth"]) ?></p>
                    <!-- 성별ㄴ -->
                    <p><?php echo htmlspecialchars($row["judge_gender"]) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/main_dh.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>