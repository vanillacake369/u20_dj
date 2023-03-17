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
                    athlete_id,
                    athlete_name,
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
                where athlete_id=" . $_GET["id"];
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
                    참가자 정보
                </p>
                <form action="">
                    <div class="UserProfile_modify info_img Participant_img">
                        <div>
                            <img src="<? echo "/assets/img/athlete_img/" . $row["athlete_profile"] ?>" alt="avatar">
                        </div>
                        <div>
                            <ul class=" UserDesc infoDesc Participant_list">
                                <li class="row">
                                    <span>고유번호</span>
                                    <p><?= htmlspecialchars($row["athlete_id"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>이름</span>
                                    <p><?= htmlspecialchars($row["athlete_name"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>국가</span>
                                    <p><?= htmlspecialchars($row["country_name_kr"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <p><?= htmlspecialchars($row["athlete_division"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>성별</span>
                                    <p><?= htmlspecialchars($row["athlete_gender"] == "m" ? "남" : "여") ?></p>
                                </li>
                                <li class="row">
                                    <span>생년월일</span>
                                    <p>
                                        <?
                                        $date = explode('-', $row["athlete_birth"]);
                                        echo htmlspecialchars($date[0]) . "년 " . htmlspecialchars($date[1]) . "월 " . htmlspecialchars($date[2]) . "일";
                                        ?>
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_check modify_container">
                        <div class="modify_enter">
                            <p class="tit_left_green">참가자 이력조회</p>
                            <div class="enter">
                                <table class="box_table">
                                    <colgroup>
                                        <col style="width: auto" />
                                    </colgroup>
                                    <thead class="result_table entry_table">
                                        <tr>
                                            <th>대회 일자</th>
                                            <th>대회명 이름</th>
                                            <th>종목</th>
                                            <th>참가 경기</th>
                                            <th>참석 여부</th>
                                            <th>비고</th>
                                        </tr>
                                        <tr class="filed2_bottom">
                                        </tr>
                                    </thead>
                                    <tbody class="table_tbody entry_table">
                                        <?php
                                            $sports_id = explode(',', $row["athlete_schedule"]);
                                            $attendingSports = explode(',', $row["athlete_attendance"]);
                                            $num = 0;
                                            foreach ($sports_id as $id) {
                                                $num++;
                                                echo "<tr";
                                                if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                                                // 대회 일자
                                                $hasKey = array_key_exists($id, $dateOfSports_dic);
                                                $date = $hasKey ? $dateOfSports_dic[$id] : "";
                                                echo "<td>" . htmlspecialchars($date) . ' ' . "</td>";
                                                // 대회명
                                                echo "<td>U20</td>";
                                                // 종목명
                                                echo "<td>" . htmlspecialchars($categoryOfSports_dic[trim($id)]) . ' ' . "</td>";
                                                // 참가 경기
                                                echo "<td>" . htmlspecialchars($sport_dic[trim($id)]) . ' ' . "</td>";
                                                // 참석 경기
                                                if (in_array(trim($id), $attendingSports)) {
                                                    echo "<td>" . htmlspecialchars("참가") . ' ' . "</td>";
                                                } else {
                                                    echo "<td>" . htmlspecialchars("불참") . ' ' . "</td>";
                                                }
                                                // 비고
                                                echo "<td></td>";
                                                echo "</tr>";
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modify_check modify_container">
                        <div class="modify_check modify_container">
                            <div class="modify_enter modify_tit_color">
                                <p class="tit_left_red">참석 경기</p>
                                <div class="attend">
                                    <table class="box_table">
                                        <colgroup>
                                            <col style="width: auto" />
                                        </colgroup>
                                        <thead class="result_table entry_table">
                                            <tr>
                                                <th>종목 이름</th>
                                                <th>경기 이름</th>
                                            </tr>
                                            <tr class="filed2_bottom">
                                            </tr>
                                        </thead>
                                        <tbody class="table_tbody entry_table">
                                            <?php
                                                foreach ($attendingSports as $eachSport) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($categoryOfSports_dic[trim($eachSport)]) . ' ' . "</td>";
                                                    echo "<td>" . htmlspecialchars($sport_dic[trim($eachSport)]) . ' ' . "</td>";
                                                    echo "</tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modify_Btn">
                <?
                    echo '<button onclick="updatePop(' . $_GET["id"] . ', \'athlete_id\' ,\'./entry_athlete_issue.php\');" value="ID발급" class="BTN_blue2">ID카드 보기</button>';
                ?>
                <button class="BTN_green" type="button" onClick="window.close()">닫기</button>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js?ver=5"></script>
</body>

</html>