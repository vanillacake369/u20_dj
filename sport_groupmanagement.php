<?php
require_once "head.php";
// 세션 확인
require_once "includes/auth/config.php";
// 로그 기능
require_once "backheader.php";
//@Potatoeunbi
//읽기 권한이 없으면 history.back하도록 하는 함수
pageAuthCheck($db, 'authSchedulesRead');
// 페이징 기능
require_once "action/module/pagination.php";
// 딕셔너리 기능
require_once "action/module/dictionary.php";

// 검색조건값 가져오기
$searchValue = [];
$searchValue["record_sports"] = getSearchValue($_GET["record_sports"] ?? NULL);
$searchValue["record_round"] = getSearchValue($_GET["record_round"] ?? NULL);
$searchValue["record_gender"] = getSearchValue($_GET["record_gender"] ?? NULL);
$pageValue = getPageValue($_GET["page"] ?? NULL);               // n페이지
// $categoryValue = getCategoryValue($_GET["order"] ?? NULL);      // 정렬 카테고리
// $orderValue = getOrderValue($_GET["sc"] ?? NULL);               // 정렬 순서 (ASC/DESC)
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);  // m개씩 보기

// 테이블 행 수
$page_list_size = 10;
$link = "";

// 쿼리에 사용될 테이블
$tableName = "list_record";
$columnStartsWith = "record_";
$record_sports = $columnStartsWith . "sports";
$record_group = $columnStartsWith . "group";

// 검색조건에 따른 쿼리문 문자열
// $sql_where = " WHERE schedule_division='b'";    // 조건값에 따른 조 편성 쿼리문을 뭘로 수정하면 좋지?? @vanillacake369
$sql_where = " WHERE record_group > 0";    // 조건값에 따른 조 편성 쿼리문을 뭘로 수정하면 좋지?? @vanillacake369
$sql_order = " ORDER BY $record_sports ASC,$record_group";
$sql_like = "";
$page_list_count = ($pageValue - 1) * $pagesizeValue;

// 기본 쿼리문
$sql = "SELECT DISTINCT record_group,count_group,r1.record_sports,r1.record_round,r1.record_gender,r1.record_status 
        FROM list_record AS r1
        INNER JOIN
        (
            SELECT COUNT(record_group) count_group ,record_sports,record_round,record_gender,record_status 
            FROM 
            (SELECT 
            DISTINCT record_group,record_sports,record_round,record_gender,record_status 
            FROM list_record 
            LEFT JOIN list_schedule ON
            list_record.record_sports= list_schedule.schedule_sports AND
            list_record.record_round= list_schedule.schedule_round AND
            list_record.record_gender= list_schedule.schedule_gender) AS distinct_list_record
            GROUP BY distinct_list_record.record_sports
        ) r2
        ON r1.record_sports = r2.record_sports";
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
        array_push($keyword, "r1.record_sports=?");
    }
    if ((!is_null($searchValue["record_round"])) && ($searchValue["record_round"] != "non")) {
        $uri_array["record_round"] = $searchValue["record_round"];
        array_push($bindarray, $searchValue["record_round"]);
        array_push($keyword, "r1.record_round=?");
    }
    if ((!is_null($searchValue["record_gender"])) && ($searchValue["record_gender"] != "non")) {
        $uri_array["record_gender"] = $searchValue["record_gender"];
        array_push($bindarray, $searchValue["record_gender"]);
        array_push($keyword, "r1.record_gender=?");
    }
    for ($i = 0; $i < count($keyword); $i++) {
        $sql_like = $sql_like . " AND " . $keyword[$i];
    }
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    if (!empty($uri_array)) {
        $link = addToLink($link, array_keys($uri_array), $uri_array);
    }
}

// 행 수 크기(m개씩 보기) 선택 시 쿼리문 수정
if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}

// 정렬 카테고리 & 순서 선택 시 쿼리문 수정
if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

// 기본 SQL문 + 조건선택에 따른 WHERE절 추가
$sql = $sql . $sql_where;
$excel = $sql . $sql_order;

// 조건검색이 되었다면 bindparam 실행, 아니라면 기본 SQL문 실행
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
                    <h1>조편성 목록<i class="xi-calendar calendar"></i></h1>
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
                                echo '<option value="100"' . ($pagesizeValue == 100 ? 'selected' : '') . '>100개씩</option>';
                                echo '<option value="' . $total_count . "\">모두</option>\"";
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="경기종목 이름" name="record_sports">
                                    <option value="non" hidden="">경기종목 이름</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $sSql = "SELECT distinct record_sports FROM list_record;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['record_sports'] . ' ' . ($searchValue["record_sports"] == $sRow['record_sports'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['record_sports']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="경기 라운드" name="record_round">
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
                                <select title="경기 성별" name="record_gender">
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
                            <th scope="col">경기 종목</th>
                            <th scope="col">경기 조</th>
                            <th scope="col">경기 라운드</th>
                            <th scope="col">경기 성별</th>
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
                        // 검색조건 있는 경우에만 table 보여주기
                        // if ($searchValue["record_sports"] !== null || $searchValue["record_round"] !== null || $searchValue["record_gender"] !== null) {
                        $i = $total_count - $page_list_count;
                        $j = 0;
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            $where = '';
                            echo '<tr';
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            $sport = $row["record_sports"];
                            $group = $row["record_group"];
                            $round = $row["record_round"];
                            $gender = $row["record_gender"];
                            // 경기 종목
                            echo "<td>" . $sport . "</td>";
                            // 경기 조
                            echo "<td>" . $group . "</td>";
                            // 경기 라운드
                            echo "<td>" . $round . "</td>";
                            // 경기 성별
                            echo "<td>";
                            echo $gender == 'm' ? "MEN" : ($gender == 'f' ? "WOMEN" : "MIXED");
                            echo "</td>";
                            // 수정
                            echo "<td>";
                            if (authCheck($db, "authSchedulesUpdate") && ($row['record_status'] != 'o')) {
                                if ($row['record_status'] != 'o') {
                                    if ($sport == '4x100mR' || $sport == '4x400mR' || $sport == '4x400mR(Mixed)') {
                                        $url = './sport_group_modify_relay_org.php';
                                    } else {
                                        $url = './sport_group_modify_group_org.php';
                                    }
                                    $url = $url . '?record_group=' . $row['record_group']
                                        . '&count_group=' . $row['count_group']
                                        . '&record_sports=' . $row['record_sports']
                                        . '&record_round=' . $row['record_round']
                                        . '&record_gender=' . $row['record_gender']
                                        . '&count_group=' . $row['count_group']
                                        . '&sports_category=' . ($categoryOfSports_dic[$row['record_sports']] ?? null);
                                    echo '<input type=\'button\' onclick="createPopupWin(\'' . $url . '\',\'창 이름\',900,900)" value=\'수정\' class=\'BTN_Blue defaultBtn\'>';
                                }
                                echo "</td>";
                            }
                            // 삭제
                            echo "<td>";
                            if (authCheck($db, "authSchedulesDelete")) {
                                echo '<form method="GET"'
                                    . "action=\"action/record/delete_sport_group.php\""
                                    . 'onsubmit="return confirmDeleteGroup()">';
                                echo '<input type="hidden" name="record_sports" value=' . $sport . '>';
                                echo '<input type="hidden" name="record_group" value=' . $group . '>';
                                echo '<input type="hidden" name="record_round" value=' . $round . '>';
                                echo '<input type="hidden" name="record_gender" value=' . $gender . '>';
                                echo '<button type="submit" class="BTN_Red defaultBtn">삭제</button>';
                                echo '</form>';
                            }
                            echo "</td>";
                            echo "</tr>";
                            $i--;
                            $j++;
                        }
                        // }
                        ?>
                    </tbody>
                </table>


                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <!-- <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php // echo $excel 
                                                                                        ?>" hidden />
                            <?php // if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' 
                            ?>
                            <input type="text" name="role" value="sport_management" hidden />

                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form> -->
                    </div>
                    <div class="registrationBtn">
                        <?php
                        if (authCheck($db, "authSchedulesCreate")) {
                            echo '<div class="btn_base base_mar col_right">';
                            echo '<input class="defaultBtn BIG_btn BTN_blue2" type="button" onclick="createPopupWin(\'sport_group_input.php\',\'창 이름\',900,900)" value="새로운 조 편성" class="btn_view">';
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
    <script src="assets/js/main.js"></script>
    <script>
        // active browser에 대한 auto refresh 함수
        reloadWhenVisibilityChange();
    </script>
</body>

</html>