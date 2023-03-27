<?php
require_once "head.php";
require_once "database/dbconnect.php"; //B:데이터베이스 연결
require_once "action/module/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조
// 로그 기능
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
$id = $_GET["id"];
$sql = "SELECT *
                    FROM list_judge
                    INNER JOIN list_country  
                    ON judge_country=country_code
                where judge_id=" . $_GET["id"];
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
?>
</head>

<body cz-shortcut-listen="true">
    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    심판 정보
                </p>
                <form action="">
                    <div class="UserProfile_modify Participant_img">
                        <div>
                        <?php if ((!isset($row["judge_profile"]) || $row["judge_profile"] == "")|| !file_exists("./assets/img/judge_img/" . $row["judge_profile"]))
                            {
                            ?>
                            <img src=<?php echo "./assets/img/profile.jpg" ?> alt="avatar" />
                            <?php }else{?>
                            <img src=<?php echo "./assets/img/judge_img/" . $row["judge_profile"] ?> alt="avatar" />
                            <?php }?>
                        </div>
                        <div>
                            <ul class="UserDesc infoDesc Participant_list">
                                <li class="row">
                                    <span>고유번호</span>
                                    <p><?php echo htmlspecialchars($row["judge_id"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>이름</span>
                                    <p><?php echo htmlspecialchars($row["judge_name"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>국가</span>
                                    <p><?php echo htmlspecialchars($row["country_name_kr"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <p><?php echo htmlspecialchars($row["judge_division"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>직무</span>
                                    <p><?php echo htmlspecialchars($row["judge_duty"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>성별</span>
                                    <p><?php echo htmlspecialchars($row["judge_gender"] == "m" ? "남" : "여") ?></p>
                                </li>
                                <li class="row">
                                    <span>생년월일</span>
                                    <p><?php
                                        $date = explode('-', $row["judge_birth"]);
                                        echo htmlspecialchars($date[0]) . "년 " . htmlspecialchars($date[1]) . "월 " . htmlspecialchars($date[2]) . "일";
                                        ?></p>
                                </li>
                                <li class="row">
                                    <span>나이</span>
                                    <p><?php echo htmlspecialchars($row["judge_age"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>식사 가능 여부</span>
                                    <p><?php echo htmlspecialchars($row["judge_eat"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>대회접근시설</span>
                                    <p><?php echo htmlspecialchars($row["judge_venue_access"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>경기장 내 좌석</span>
                                    <p><?php echo htmlspecialchars($row["judge_seats"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>교통 권한</span>
                                    <p><?php echo htmlspecialchars($row["judge_transport"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>선수촌</span>
                                    <p><?php echo htmlspecialchars($row["judge_village"]) ?></p>
                                </li>
                                <li class="row">
                                <span>경기장 접근 허용</span>
                                    <div class="full_div align">
                                        <?php
                                            $sector_row = explode(",",htmlspecialchars($row["judge_sector"]));
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

                    <div class="modify_check modify_container">
                        <div class="modify_enter">
                            <p class="tit_left_green">심판 이력조회</p>
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
                                            <th>참가예정경기</th>
                                            <th>참석확정경기</th>
                                            <th>직무</th>
                                            <th>ID</th>
                                            <th>비고</th>
                                        </tr>
                                        <tr class="filed2_bottom">
                                        </tr>
                                    </thead>
                                    <tbody class="table_tbody entry_table">
                                        <?php

                                        function str_begins($haystack, $needle)
                                        {
                                            return 0 === substr_compare($haystack, $needle, 0, strlen($needle));
                                        }

                                        if (str_begins($id, "heptathlon"));
                                        $category = "heptathlon";

                                        if (str_begins($id, "decathlon"));
                                        $category = "decathlon";

                                        $sports_id = explode(',', $row["judge_schedule"]);
                                        $attendingSports = explode(',', $row["judge_attendance"]);
                                        $num = 0;
                                        foreach ($sports_id as $id) {
                                            echo "<tr";
                                            if ($num % 2 == 1) echo ' class="Ranklist_Background">';
                                            else echo ">";
                                            // 대회 일자
                                            $hasKey = array_key_exists($id, $dateOfSports_dic);
                                            $date = $hasKey ? $dateOfSports_dic[$id] : "";
                                            echo "<td>" . htmlspecialchars($date) . ' ' . "</td>";
                                            // 대회명
                                            echo "<td>U20</td>";
                                            // 종목명
                                            $category = $id;
                                            if (str_begins($id, "heptathlon")) {
                                                $category = "heptathlon";
                                            }
                                            if (str_begins($id, "decathlon")) {
                                                $category = "decathlon";
                                            }
                                            echo "<td>" . htmlspecialchars($categoryOfSports_dic[$category]) . ' ' . "</td>";
                                            // 참가 경기
                                            echo "<td>" . htmlspecialchars($judge_sport_dic[trim($id)]) . ' ' . "</td>";
                                            // 참석 경기
                                            if (in_array($id, $attendingSports)) {
                                                echo "<td>" . htmlspecialchars("참가") . ' ' . "</td>";
                                            } else {
                                                echo "<td>" . htmlspecialchars("불참") . ' ' . "</td>";
                                            }
                                            // 직무
                                            echo "<td>" . htmlspecialchars($row["judge_duty"]) . "</td>";
                                            // ID
                                            echo "<td>" . htmlspecialchars($row["judge_account"]) . "</td>";
                                            // 비고
                                            echo "<td></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modify_check modify_container">
                            <div class="modify_enter modify_tit_color">
                                <p class="tit_left_red">참석 경기 & 직무</p>
                                <div class="attend">
                                    <table class="box_table">
                                        <colgroup>
                                            <col style="width: auto" />
                                        </colgroup>
                                        <thead class="result_table entry_table">
                                            <tr>
                                                <th>종목 이름</th>
                                                <th>경기 이름</th>
                                                <th>직무</th>
                                            </tr>
                                            <tr class="filed2_bottom">
                                            </tr>
                                        </thead>
                                        <tbody class="table_tbody entry_table">
                                            <?php
                                            $num = 0;
                                            $sports_id = explode(',', $row["judge_schedule"]);
                                            foreach ($sports_id as $id) {
                                                $category = $id;
                                                if (str_begins($id, "heptathlon")) {
                                                    $category = "heptathlon";
                                                }
                                                if (str_begins($id, "decathlon")) {
                                                    $category = "decathlon";
                                                }
                                                $num++;
                                                echo "<tr";
                                                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                                else echo ">";
                                                echo "<td>" . htmlspecialchars($categoryOfSports_dic[$category]) . ' ' . "</td>";
                                                echo "<td>" . htmlspecialchars($judge_sport_dic[$id]) . ' ' . "</td>";
                                                echo "<td>" . htmlspecialchars($row["judge_duty"]) . "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modify_Btn">
                <?php
                echo '<button
                    onclick="updatePop(' . $_GET["id"] . ', \'judge_id\' ,\'./entry_judge_issue.php\');" value="ID발급" class="BTN_blue2">ID카드 보기</button>';
                ?>
                <button class="BTN_green" onclick="window.close()">닫기</button>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js?ver=5"></script>
</body>

</html>