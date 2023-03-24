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
        WHERE athlete_id=" . $_GET["id"];
$result = $db->query($sql);
$athlete_personal = mysqli_fetch_array($result);

// 선수 이력 조회
$sql =
    "SELECT 
    *
    FROM list_athlete AS a
    INNER JOIN list_country
    ON athlete_country = country_code
    INNER JOIN list_record
    ON athlete_id = record_athlete_id
    INNER JOIN list_schedule
    ON schedule_sports = record_sports AND
    schedule_round = record_round AND 
    schedule_gender = record_gender
    WHERE athlete_id = " . $_GET["id"];
$result = $db->query($sql);
$athlete_info_arr = array();
while ($row = mysqli_fetch_array($result)) {
    array_push($athlete_info_arr, $row);
}
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
                            <img src="<?php echo "/assets/img/athlete_img/" . $athlete_personal["athlete_profile"] ?>" alt="avatar">
                        </div>
                        <div>
                            <ul class=" UserDesc infoDesc Participant_list">
                                <li class="row">
                                    <span>고유번호</span>
                                    <p><?= htmlspecialchars($athlete_personal["athlete_id"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>이름</span>
                                    <p><?= htmlspecialchars($athlete_personal["athlete_name"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>국가</span>
                                    <p><?= htmlspecialchars($athlete_personal["country_name_kr"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>소속</span>
                                    <p><?= htmlspecialchars($athlete_personal["athlete_division"]) ?></p>
                                </li>
                                <li class="row">
                                    <span>성별</span>
                                    <p><?= htmlspecialchars($athlete_personal["athlete_gender"] == "m" ? "남" : "여") ?></p>
                                </li>
                                <li class="row">
                                    <span>생년월일</span>
                                    <p>
                                        <?php
                                        $date = explode('-', $athlete_personal["athlete_birth"]);
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
                                            <th>라운드</th>
                                            <th>참석 여부</th>
                                            <th>기록</th>
                                            <th>증명서</th>
                                            <th>비고</th>
                                        </tr>
                                        <tr class="filed2_bottom">
                                        </tr>
                                    </thead>
                                    <tbody class="table_tbody entry_table">
                                        <?php
                                        // function str_contains($haystack, $needle)
                                        // {
                                        //     if (is_string($haystack) && is_string($needle)) {
                                        //         return '' === $needle || false !== strpos($haystack, $needle);
                                        //     } else {
                                        //         return false;
                                        //     }
                                        // }
                                        // 선수 경기 이력이 있다면 기록과 증명서 출력, 없다면 기록만 출력
                                        if ($athlete_info_arr) {
                                            $schedule_sports = $athlete_info_arr[0]["athlete_schedule"];
                                            $attending_sports = $athlete_info_arr[0]["athlete_attendance"];
                                            for ($i = 0; $i < count($athlete_info_arr); $i++) {
                                                echo "<tr";
                                                if ($i % 2 == 0) echo ' class="Ranklist_Background">';
                                                else echo ">";
                                                // 대회 일자
                                                $date = $athlete_info_arr[$i]['schedule_date'];
                                                // $hasKey = array_key_exists($id, $dateOfSports_dic);
                                                // $date = $hasKey ? $dateOfSports_dic[$id] : "";
                                                echo "<td>" . htmlspecialchars($date) . ' ' . "</td>";
                                                // 대회명
                                                echo "<td>U20</td>";
                                                // 종목명
                                                echo "<td>" . htmlspecialchars($categoryOfSports_dic[$athlete_info_arr[$i]['record_sports']]) . ' ' . "</td>";
                                                // 참가 경기
                                                echo "<td>" . htmlspecialchars($athlete_info_arr[$i]['record_sports']) . ' ' . "</td>";
                                                // 라운드
                                                echo "<td>" . htmlspecialchars($athlete_info_arr[$i]['record_round']) . ' ' . "</td>";
                                                // 참석 여부
                                                if (str_contains($attending_sports, $athlete_info_arr[$i]['record_sports'])) {
                                                    echo "<td>" . htmlspecialchars("참가") . ' ' . "</td>";
                                                } else {
                                                    echo "<td>" . htmlspecialchars("불참") . ' ' . "</td>";
                                                }
                                                // 기록
                                                $result = '';
                                                $record = '';
                                                if ($athlete_info_arr[$i]['record_status'] = 'l') {
                                                    // live
                                                    $result = $athlete_info_arr[$i]['record_live_result'];
                                                    $record = $athlete_info_arr[$i]['record_live_record'];
                                                    echo "<td>" . htmlspecialchars($record) . ' ' . "</td>";
                                                } else if ($athlete_info_arr[$i]['record_status'] = 'o') {
                                                    // official
                                                    $result = $athlete_info_arr[$i]['record_official_result'];
                                                    $record = $athlete_info_arr[$i]['record_official_record'];
                                                    echo "<td>" . htmlspecialchars($record) . ' ' . "</td>";
                                                } else {
                                                    // not started
                                                    echo "<td>" . htmlspecialchars("NOT STARTED") . ' ' . "</td>";
                                                }
                                                // 증명서
                                                $url = 'entry_athlete_certificate.php?';
                                                // <!-- 선수 이름 -->
                                                $url .= 'athlete_name=' . $athlete_personal["athlete_name"];
                                                // <!-- 생일 -->
                                                $url .= '&athlete_birth=' . $athlete_personal["athlete_birth"];
                                                // <!-- 국적 -->
                                                $url .= '&athlete_country=' . $athlete_personal["country_name_kr"];
                                                // <!-- 참가 경기명 -->
                                                $url .= '&record_sports=' . $athlete_info_arr[$i]['record_sports'];
                                                // <!-- 등수 -->
                                                $url .= '&result=' . $result;
                                                // <!-- 기록 -->
                                                $url .= '&record=' . $record;
                                                // <!-- 풍속 용기구 -->
                                                $url .= '&wind=' . $athlete_info_arr[$i]['record_wind'] . '&weight=' . $athlete_info_arr[$i]['record_weight'];
                                                echo '<td>'
                                                    . '<button onclick="' . "window.open('$url','_blank')" . '">증명서 출력</button>'
                                                    . ' ' . '</td>';
                                                // 비고
                                                echo "<td></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            $sports_id = explode(',', $athlete_personal["athlete_schedule"]);
                                            $attendingSports = explode(',', $athlete_personal["athlete_attendance"]);
                                            $num = 0;
                                            foreach ($sports_id as $id) {
                                                $num++;
                                                echo "<tr";
                                                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                                else echo ">";
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
                                                // 라운드
                                                echo "<td></td>";
                                                // 참석 여부
                                                if (in_array(trim($id), $attendingSports)) {
                                                    echo "<td>" . htmlspecialchars("참가") . ' ' . "</td>";
                                                } else {
                                                    echo "<td>" . htmlspecialchars("불참") . ' ' . "</td>";
                                                }
                                                // 기록
                                                echo "<td></td>";
                                                // 증명서
                                                echo "<td></td>";
                                                // 비고
                                                echo "<td></td>";
                                                echo "</tr>";
                                            }
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
                                            foreach ((explode(',', $athlete_personal["athlete_attendance"])) as $eachSport) {
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
                <?php
                echo '<button onclick="updatePop(' . $_GET["id"] . ', \'athlete_id\' ,\'./entry_athlete_issue.php\');" value="ID발급" class="BTN_blue2">ID카드 보기</button>';
                ?>
                <button class="BTN_green" type="button" onclick="window.close()">닫기</button>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js?ver=5"></script>
</body>

</html>