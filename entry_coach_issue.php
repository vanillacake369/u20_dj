<?php
require_once "head.php";
if (!$_POST['coach_id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
} else {
    // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
    require_once "includes/auth/config.php";
    // 국가,종목,지역,직무에 대한 매핑구조
    require_once "action/module/dictionary.php";
    // isIssue 컬럼 UPDATE
    $update_issue_sql =
        "UPDATE list_coach SET coach_isIssued = 'Y' WHERE coach_id=" . $_POST['coach_id'];
    $db->query($update_issue_sql);
    // 참가자 정보 가져오기
    $sql =
        "SELECT 
                coach_id,
                coach_name,
                country_name,
                country_name_kr,
                country_code,
                coach_region,
                coach_division,
                coach_gender,
                coach_birth,
                coach_age, 
                coach_duty,
                coach_schedule,
                coach_profile,
                coach_attendance
                FROM list_coach
                INNER JOIN list_country  
                ON coach_country=country_code
                where coach_id=" . $_POST['coach_id'];
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
}
?>
<script>
  window.print();
</script>
</head>
<body>
    <div class=" AD_box a4">
        <div class="AD_front">
            <div class="AD_container">
                <div class="AD_card">
                    <img src="assets/images/pink_front.png" alt="">
                </div>
                <div class="AD_front_User">
                    <img src="<?php echo " ./assets/img/coach_img/" . $row["coach_profile"] ?>" alt="">
                </div>
                <div class="AD_form">
                    <div class="AD_front_name">
                        <!-- 이름 -->
                        <p><?php echo htmlspecialchars($row["coach_name"]) ?></p>
                    </div>
                </div>
                <!-- 직책 -->
                <div class="AD_front_desc">
                    <p>Technical Official</p>
                </div>
                <!-- 접근코드 -->
                <div class="AD_Venue">
                    <p><?php echo htmlspecialchars($row["coach_division"]) ?></p>
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
        </div>
    </div>
    <div class="AD_box a4">
        <div class="AD_back">
            <div class="AD_container">
                <div class="AD_card">
                    <img src="assets/images/pink_back.png" alt="">
                </div>
                <div class="AD_back_User">
                    <img src="<?php echo " ./assets/img/coach_img/" . $row["coach_profile"] ?>" alt="">
                </div>
                <div class="AD_back_name">
                    <!-- 이름 -->
                    <p><?php echo htmlspecialchars($row["coach_name"]) ?></p>
                </div>
                <div class="AD_back_nationality back_container">
                    <!-- 지역 -->
                    <p><?php echo htmlspecialchars($row["country_code"]) ?></p>
                    <!-- 생년월일 -->
                    <p><?php echo htmlspecialchars($row["coach_birth"]) ?></p>
                    <!-- 성별ㄴ -->
                    <p><?php echo htmlspecialchars($row["coach_gender"]) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>

<script>
    // AD 카드 발급 팝업 닫은 후 부모 페이지(참가자 페이지) 자동 리로드
    window.onunload = function() {
        window.opener.location.reload();
    };
</script>

</html>