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
$sql = "SELECT * FROM list_director
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
                            <?php if (!isset($row["director_profile"]) || $row["director_profile"] == "")
                            {
                            ?>
                            <img src=<?php echo "./assets/img/athlete_img/profile.png" ?> alt="avatar" />
                            <?php }else{?>
                            <img src=<?php echo "./assets/img/athlete_img/" . $row["director_profile"] ?> alt="avatar" />
                            <?php }?>
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
                                <li class="row">
                                    <span>식사 가능 여부</span>
                                    <p><?php echo htmlspecialchars($row["director_eat"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>대회접근시설</span>
                                    <p><?php echo htmlspecialchars($row["director_venue_access"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>경기장 내 좌석</span>
                                    <p><?php echo htmlspecialchars($row["director_seats"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>교통 권한</span>
                                    <p><?php echo htmlspecialchars($row["director_transport"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>선수촌</span>
                                    <p><?php echo htmlspecialchars($row["director_village"]) ?></p>
                                </li>
                                <li class="row">
                                <span>경기장 접근 허용</span>
                                    <div class="full_div">
                                        <?php
                                            $sector_row = explode(",",htmlspecialchars($row["director_sector"]));
                                            $i = 0;
                                            foreach ($sector_row as $sector)
                                            {
                                                echo "<p>" . $sector . "</p>";
                                            }
                                        ?>
                                    </div>
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
    <script src="assets/js/main.js?ver=11"></script>
</body>

</html>