<?php
require_once "head.php";
if (!$_POST['judge_id']) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
} else {
    // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
    require_once "includes/auth/config.php";
    // 국가,종목,지역,직무에 대한 매핑구조
    require_once "action/module/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조
}
?>
<script>
  window.print();
</script>
</head>
<body>
    <?php
         $director_id = explode(",", $_POST['judge_id']);
         foreach ($director_id as $id)
         {
             $update_issue_sql =
             "UPDATE list_judge SET judge_isIssued = 'Y' WHERE judge_id=" . $id;
             $db->query($update_issue_sql);
             $sql =
                    "SELECT *
                                FROM list_judge
                                INNER JOIN list_country  
                                ON judge_country=country_code
                            where judge_id=" . $id;
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
                <img src="<?php echo " ./assets/img/judge_img/" . $row["judge_profile"] ?>" alt="">
                </div>
                <div class="AD_country">
                    <p><?php echo $row["country_code"] ?></p>
                </div>
                <div class="AD_form">
                    <div class="AD_front_name">
                        <!-- 이름 -->
                        <p><?php echo htmlspecialchars($row["judge_name"]) ?></p>
                    </div>
                </div>
                <!-- 직책 -->
                <div class="AD_front_desc">
                    <p>Judge</p>
                </div>
                <div class="AD_front_text">
                    <p>Yecheon Asian U20</p>
                    <p>Athletics Championships</p>
                </div>
                <!-- 접근코드 -->
                <div class="AD_Venue">
                    <p><?php if (htmlspecialchars($row["judge_venue_access"]) == 'HQ') echo 'HQ';?></p>
                </div>
                <!-- 접근 무한 -->
                <div class="All_Venue">
                    <p><?php if (htmlspecialchars($row["judge_venue_access"]) == 'Y') echo '∞';?></p>
                </div>
                    <!-- 시설 접근 코드 -->
                    <!-- 선수촌 거주 허용 코드 -->
                    <p class="village"><?php echo htmlspecialchars($row["judge_village"]) ?></p>
                    <!-- 식사 가능 여부 없을 시 이미지 삭제 -->
                    <?php if (htmlspecialchars($row["judge_eat"]) == 'y'){ ?>
                        <img class="eat" src="/assets/images/eat.png" alt="">
                    <?php } ?>
                    <!-- 교통권한 -->
                    <p class="transport"><?php if (htmlspecialchars($row["judge_transport"]) != "") echo "T"; ?></p>
                    <!-- 경기장 내 좌석 -->
                    <p class="Seats"><?php echo htmlspecialchars($row["judge_seats"]) ?></p>
                    <div class="Access_venue">
                    <?php
                        $sector_row = explode(",", htmlspecialchars($row["judge_sector"]));
                        foreach ($sector_row as $sector) {
                            echo "<p>" . $sector . "</p>";
                        }
                    ?>
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
                    <img src="<?php echo " ./assets/img/judge_img/" . $row["judge_profile"] ?>" alt="">
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