<?php
require_once "head.php";
// 세션 확인
include_once(__DIR__ . "/includes/auth/config.php");
// 로그 기능
include_once(__DIR__ . "/backheader.php");
//@Potatoeunbi
//읽기 권한이 없으면 history.back하도록 하는 함수
pageAuthCheck($db, 'authSchedulesRead');
// 페이징 기능
include_once(__DIR__ . "/action/module/pagination.php");
// 딕셔너리 기능
include_once(__DIR__ . "/action/module/dictionary.php");

// 검색조건값 가져오기
$searchValue = [];
$searchValue["record_sports"] = getSearchValue($_GET["record_sports"] ?? NULL);
$searchValue["record_round"] = getSearchValue($_GET["record_round"] ?? NULL);
$searchValue["record_gender"] = getSearchValue($_GET["record_gender"] ?? NULL);
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

// 테이블 행 수
$page_list_size = 10;
$link = "";

// 쿼리에 사용될 테이블
$tableName = "list_record";
$columnStartsWith = "record_";
$id = $columnStartsWith . "group";

// 검색조건에 따른 쿼리문 문자열
// $sql_where = " WHERE schedule_division='b'";    // 조건값에 따른 조 편성 쿼리문을 뭘로 수정하면 좋지?? @vanillacake369
$sql_where = "";    // 조건값에 따른 조 편성 쿼리문을 뭘로 수정하면 좋지?? @vanillacake369
$sql_order = " ORDER BY $id ASC";
$sql_like = "";
$page_list_count = ($pageValue - 1) * $pagesizeValue;

// 기본 쿼리문
$sql = "SELECT * FROM list_record LEFT JOIN list_schedule ON
        list_record.record_sports= list_schedule.schedule_sports AND
        list_record.record_round= list_schedule.schedule_round AND
        list_record.record_gender= list_schedule.schedule_gender
        LEFT JOIN list_athlete ON
        list_record.record_athlete_id = list_athlete.athlete_id";
$uri_array = array();
$bindarray = array();
$keyword = array();

// 검색조건 값이 있다면 쿼리문 수정
if (count($searchValue)) {
    $keyword = array();
    $bindarray = array();

    if ((!is_null($searchValue["record_sports"])) && ($searchValue["record_sports"] != "non")) {
        $uri_array["record_sports"] = $searchValue["record_sports"];
        array_push($bindarray, $searchValue["record_sports"]);
        array_push($keyword, "record_sports=?");
    }
    if ((!is_null($searchValue["record_round"])) && ($searchValue["record_round"] != "non")) {
        $uri_array["record_round"] = $searchValue["record_round"];
        array_push($bindarray, $searchValue["record_round"]);
        array_push($keyword, "record_round=?");
    }
    if ((!is_null($searchValue["record_gender"])) && ($searchValue["record_gender"] != "non")) {
        $uri_array["record_gender"] = $searchValue["record_gender"];
        array_push($bindarray, $searchValue["record_gender"]);
        array_push($keyword, "record_gender=?");
    }
    for ($i = 0; $i < count($keyword); $i++) {
        if ($keyword[$i] != "record_gender=?")
            $sql_like = $sql_like . " AND " . $keyword[$i];
        else
            $sql_like = $sql_like . ' ' . $keyword[$i];
    }
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    if (!empty($uri_array)) {
        $link = addToLink($link, array_keys($uri_array), $uri_array);
    }
}

if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}

if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

$sql = $sql . $sql_where;
$excel = $sql . $sql_order;

if (isset($searchValue) && ($searchValue["record_sports"] != "non" || $searchValue["record_round"] != "non" || $searchValue["record_gender"] != "non")) {
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $count = $stmt->get_result();

    $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();


    $result = $stmt->get_result();
} else {
    $count = $db->query($sql);

    $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
    $result = $db->query($sql);
}
$total_count = mysqli_num_rows($count);

?>

</head>

<body>
    <!-- header -->
    <?php require_once "header.php"; ?>

    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank scheduleList defaultList">
                <div class="MainRank_tit">
                    <h1>조편성 목록<i class="xi-users users"></i></h1>
                </div>
                <div class="searchArea">
                    <!-- 조건 검색 -->
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10개씩</option>';
                                echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15개씩</option>';
                                echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20개씩</option>';
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="경기종목 이름" name="sports_name">
                                    <option value="non" hidden="">경기종목 이름</option>
                                    <?php
                                    $sSql = "SELECT distinct sports_name FROM list_sports;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        $search_sports_name = $searchValue["sports_name"] ?? NULL;
                                        echo "<option value='";
                                        echo $sRow['sports_name'];
                                        echo "'" . ($search_sports_name == $sRow['sports_name'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="경기 라운드" name="sports_code">
                                    <option value="non" hidden="">경기 라운드</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $sSql = "SELECT distinct record_round FROM list_record;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['record_round'] . ' ' . ($searchValue["record_round"] == $sRow['record_round'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['record_round']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="경기 성별" name="sports_gender">
                                    <option value="non" hidden="">경기 성별</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $sSql = "SELECT distinct record_gender FROM list_record;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['record_gender'] . ' ' . ($searchValue["record_gender"] == $sRow['record_gender'] ? 'selected' : '') . ">" . ($sRow['record_gender'] == 'm' ? '남' : ($sRow['record_gender'] == 'f' ? '여' : '혼성')) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <div class="search" style="width: 50px">
                                    <button class="SearchBtn" name="search" value="search" type="submit"><i class="xi-search" title="검색"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">경기 조</th>
                            <th scope="col">경기 종목</th>
                            <th scope="col">경기 라운드</th>
                            <th scope="col">경기 성별</th>
                            <th scope="col">선수 이름</th>
                            <?php
                            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                                <th scope="col">수정</th>
                            <?php }
                            if (authCheck($db, "authSchedulesDelete")) {  ?>
                                <th scope="col">삭제</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $i = $total_count - $page_list_count;
                        $j = 0;

                        while ($row = mysqli_fetch_array($result)) {
                            $where = '';
                            echo '<tr>';
                            // 경기 조
                            echo "<td>" . $row["record_group"] . "</td>";
                            // 경기 종목
                            echo "<td>" . $row["record_sports"] . "</td>";
                            // 경기 라운드
                            echo "<td>" . $row["record_round"] . "</td>";
                            // 경기 성별
                            echo "<td>" . $row["record_gender"] . "</td>";
                            // 선수 이름
                            echo "<td>" . $row["athlete_name"] . "</td>";
                            // 수정
                            echo "<td>";
                            if (authCheck($db, "authSchedulesUpdate") && ($row['record_status'] != 'o')) {
                                if ($row['record_status'] != 'o') {
                                    echo '<input type=\'button\' onclick="createPopupWin(\'sport_group_modify_athletes.php?id=' . $row['schedule_id'] . '\',\'창 이름\',900,900)" value=\'수정\' class=\'btn_modify\'>';
                                }
                                echo "</td>";
                            }
                            // 삭제
                            echo "<td>";
                            if (authCheck($db, "authSchedulesDelete")) {
                                echo "<input type='button' onclick=" . "confirmDelete('" . $row["schedule_id"] . "','schedule')" . " value='삭제' class='btn_delete'>";
                            }
                            echo "</td>";
                            echo "</tr>";
                            $i--;
                            $j++;
                        }

                        //@Potatoeunbi
                        //삭제 버튼 클릭 시 발생하는 이벤트
                        if (isset($_POST["schedule_delete"])) {

                            //@Potatoeunbi
                            //로그 이력을 위해서 select sql
                            $S_sql = "SELECT (SELECT sports_category FROM list_sports 
                                WHERE schedule_sports=sports_code)AS category,schedule_sports,schedule_name,schedule_gender,schedule_round, schedule_location 
                                FROM list_schedule 
                                WHERE schedule_id='" . $_POST['schedule_delete'] . "';";

                            $S_result = $db->query($S_sql);
                            $S_row = mysqli_fetch_array($S_result);

                            $condition = '';
                            $whereD = "s2.schedule_id= ?
                                and s1.schedule_sports=s2.schedule_sports 
                                AND s1.schedule_name=s2.schedule_name 
                                AND s1.schedule_gender=s2.schedule_gender";

                            //@Potatoeunbi
                            //트랙경기일 경우에만 sql문에 round 추가해줌.
                            if ($S_row['category'] == '트랙경기') $condition = "AND s1.schedule_round=s2.schedule_round ";

                            //@Potatoeunbi
                            //해당 경기의 신기록도 삭제하기 위해서 경기의 끝나는 시간을 다 불러오는 sql문.(대분류, 소분류 모두)
                            $endsql = "SELECT s1.schedule_end FROM list_schedule AS s1 
                                join list_schedule AS s2 
                                where (  s2.schedule_id= '" . $_POST['schedule_delete'] . "'
                                and s1.schedule_sports=s2.schedule_sports 
                                AND s1.schedule_name=s2.schedule_name 
                                AND s1.schedule_gender=s2.schedule_gender  $condition  );";

                            $endresult = $db->query($endsql);
                            while ($endrow = mysqli_fetch_array($endresult)) {
                                //@Potatoeunbi
                                //삭제하려는 경기의 신기록 삭제
                                $worldsql = "DELETE FROM list_worldrecord 
                                            WHERE worldrecord_sports=? 
                                            AND worldrecord_location=? 
                                            AND worldrecord_gender=? 
                                            AND worldrecord_datetime=DATE( ? );";
                                $worldstmt = $db->prepare($worldsql);
                                $worldstmt->bind_param("ssss", $S_row['schedule_sports'],  $S_row['schedule_location'], $S_row['schedule_gender'], $endrow['schedule_end']);
                                $worldstmt->execute();
                            }

                            //@Potatoeunbi
                            //삭제하려는 경기의 record 삭제
                            $subsql = "DELETE r from list_record AS r 
                                JOIN list_schedule AS s on r.record_schedule_id=s.schedule_id 
                                WHERE record_schedule_id 
                                IN (SELECT s1.schedule_id FROM list_schedule AS s1 
                                right OUTER join list_schedule AS s2 
                                ON ( $whereD $condition ));";

                            $substmt = $db->prepare($subsql);
                            $substmt->bind_param("s", $_POST["schedule_delete"]);
                            $substmt->execute();

                            //@Potatoeunbi
                            //경기 삭제(생성된 대분류, 소분류 모두)
                            $sql = "DELETE s1 FROM list_schedule AS s1 
                                join list_schedule AS s2 
                                where ( $whereD $condition );";

                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("s", $_POST["schedule_delete"]);
                            $stmt->execute();


                            logInsert($db, $_SESSION['Id'], '일정 삭제', $S_row['category'] . '-' . $S_row['schedule_name'] . '-' . $S_row['schedule_gender'] . '-' . $S_row['schedule_round']);
                            echo "<script>location.href='./sport_schedulemanagement.php';</script>";
                        }

                        ?>

                    </tbody>
                </table>

                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="sport_management" hidden />

                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                    </div>
                    <div class="registrationBtn">
                        <?php
                        if (authCheck($db, "authSchedulesCreate")) {
                            echo '<div class="btn_base base_mar col_right">';
                            echo '<input class="btn_add btn_txt bold" type="button" onclick="createPopupWin(\'sport_group_input.php\',\'창 이름\',900,900)" value="새로운 조 편성" class="btn_view">';
                            echo '</div>';
                        } ?>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                console.log("Browser tab is hidden")
            } else {
                console.log("Browser tab is visible")
                location.reload();
            }
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>