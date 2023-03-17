<?
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
                    coach_id,
                    coach_name,
                    country_name_kr,
                    country_code,
                    coach_region,
                    coach_division,
                    coach_gender,
                    coach_birth,
                    coach_age, 
                    coach_duty,
                    coach_profile
                    FROM list_coach
                    INNER JOIN list_country  
                    ON coach_country=country_code
                where coach_id=" . $_GET["id"];
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
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
                    코치 정보
                </p>
                <form action="">
                    <div class="UserProfile_modify Participant_img">
                        <div>
                            <img src="<? echo " ./assets/img/coach_img/" . $row["coach_profile"]; ?>">
                        </div>
                        <div>
                            <ul class="UserDesc infoDesc Participant_list">
                                <li class="row">
                                    <span>고유번호</span>
                                    <p><?=htmlspecialchars($row["coach_id"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>이름</span>
                                    <p><?=htmlspecialchars($row["coach_name"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>국가</span>
                                    <p><?=htmlspecialchars($row["country_name_kr"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <p><?=htmlspecialchars($row["coach_division"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>직무</span>
                                    <p><?=htmlspecialchars($row["coach_duty"] == "h" ? "헤드 코치" : "서브 코치") ?></p>
                                </li>
                                <li class="row">
                                    <span>성별</span>
                                    <p><?=htmlspecialchars($row["coach_gender"] == "m" ? "남" : "여") ?></p>
                                </li>
                                <li class="row">
                                    <span>생년월일</span>
                                    <p>
                                        <?
                                        $date = explode('-', $row["coach_birth"]);
                                        echo htmlspecialchars($date[0]) . "년 " . htmlspecialchars($date[1]) . "월 " . htmlspecialchars($date[2]) . "일";
                                        ?>
                                    </p>
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <p><?=htmlspecialchars($row["coach_age"]) ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modify_Btn">
                <?
                    echo '<button class="BTN_blue2" 
                    onclick="updatePop(' . $_GET["id"] . ', \'coach_id\' ,\'./entry_coach_issue.php\');" value="ID발급" class="btn_view">ID카드 보기</button>';
                ?>
                <button class="BTN_green" type="button" onClick="window.close()">닫기</button>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js?ver=5"></script>
</body>

</html>