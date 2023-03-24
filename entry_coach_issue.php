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
}
?>
<script>
  window.print();
</script>
</head>
<body>
    <?php
         $coach_id = explode(",", $_POST['coach_id']);
         foreach ($coach_id as $id)
         {
             $update_issue_sql =
             "UPDATE list_coach SET coach_isIssued = 'Y' WHERE coach_id=" . $id;
             $db->query($update_issue_sql);

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
                     where coach_id=" . $id;
                $result = $db->query($sql);
                while($row = mysqli_fetch_array($result)){
    ?>
    <div class=" AD_box a4">
        <div class="AD_front">
            <div class="AD_container">
                <div class="AD_card">
                    <img src="assets/images/pink_front.png" alt="">
                </div>
                <div class="AD_front_User">
                    <img src="<?php echo " ./assets/img/coach_img/" . $row["coach_profile"] ?>" alt="">
                </div>
                <div class="AD_country">
                    <p><?php echo $row["country_code"] ?></p>
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
                <div class="AD_front_text">
                    <p>Yecheon Asian U20</p>
                    <p>Athletics Championships</p>
                </div>
                <!-- 접근코드 -->
                <div class="AD_Venue">
                    <p>HQ</p>
                </div>
                <!-- 접근 무한 -->
                <div class="All_Venue">
                    <p>∞</p>
                </div>
                    <!-- 시설 접근 코드 -->
                    <!-- 선수촌 거주 허용 코드 -->
                    <p class="village">AV</p>
                    <!-- 식사 가능 여부 없을 시 이미지 삭제 -->
                    <img class="eat" src="/assets/images/eat.png" alt="">
                    <!-- 교통권한 -->
                    <p class="transport">T</p>
                    <!-- 경기장 내 좌석 -->
                    <p class="Seats">RS</p>
                    <div class="Access_venue">
                    <p>1</p>
                    <p>2</p>
                    <p>3</p>
                    <p>4</p>
                </div>
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
    <?php
        }
    }
    ?>
    <script src="assets/js/main.js"></script>
</body>

<script>
    // AD 카드 발급 팝업 닫은 후 부모 페이지(참가자 페이지) 자동 리로드
    window.onunload = function() {
        window.opener.location.reload();
    };
</script>

</html>