<?php
require_once "head.php";
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
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

if (!isset($_GET["id"])) {
    echo "<script>alert('잘못된 유입경로입니다.')</script>";
    exit();
}
$sql = "SELECT 
                    director_id,
                    director_name,
                    country_name_kr,
                    country_code,
                    director_division,
                    director_gender,
                    director_birth,
                    director_age, 
                    director_duty,
                    director_schedule,
                    director_profile,
                    director_attendance
                    FROM list_director
                    INNER JOIN list_country  
                    ON director_country=country_code
                where director_id=" . $_GET["id"];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
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
                    임원 정보
                </p>
                <form action="">
                    <div class="UserProfile_modify Participant_img">
                        <div>
                            <img src=<?php echo "./assets/img/director_img/" . $row["director_profile"] ?> alt="avatar" />
                        </div>
                        <div>
                            <ul class="UserDesc infoDesc Participant_list">
                                <li class="row Desc_item">
                                    <span>고유번호</span>
                                    <p><?php echo htmlspecialchars($row["director_id"]) ?></p>
                                </li>
                                <li class="row Desc_item">

                                    <span>이름</span>
                                    <p><?php echo htmlspecialchars($row["director_name"]) ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>국가</span>
                                    <p><?php echo htmlspecialchars($row["country_name_kr"]) ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>소속</span>
                                    <p><?php echo htmlspecialchars($row["director_division"]) ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>직무</span>
                                    <p><?php echo htmlspecialchars($row["director_duty"] == "h" ? "직무1" : "직무2") ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>성별</span>
                                    <p><?php echo htmlspecialchars($row["director_gender"] == "m" ? "남" : "여") ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>생년월일</span>
                                    <p><?php
                                        $date = explode('-', $row["director_birth"]);
                                        echo htmlspecialchars($date[0]) . "년 " . htmlspecialchars($date[1]) . "월 " . htmlspecialchars($date[2]) . "일";
                                        ?></p>
                                </li>
                                <li class="row Desc_item">
                                    <span>나이</span>
                                    <p><?php echo htmlspecialchars($row["director_age"]) ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modify_Btn">
                <?php
                echo '<button
                    onclick="updatePop(' . $_GET["id"] . ', \'director_id\' ,\'./entry_director_issue.php\');" value="ID발급" class="BTN_blue2">ID카드 보기</button>';
                ?>
                <button class="BTN_green" onclick="window.close()">닫기</button>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js?ver=5"></script>
</body>

</html>