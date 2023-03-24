<?php
require_once "head.php";
// 데이터베이스 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 페이징 기능
require_once "action/module/pagination.php";

$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
//+) pagesizeValue 추가
$page_list_size = 10;
$link = "";
// page 내 row 에 따른 "page 번호";
$page_list_count = ($pageValue - 1) * $pagesizeValue;
//+) $page_size->$pagesizeValue
// pageSizeOption : 한 페이지 내의 행 수

// SQL 조건문
$sql = "SELECT
        sports_name,
        sports_code,  
        schedule_sports,  
        schedule_gender,
        GROUP_CONCAT(CONCAT(IF(record_medal=10000,athlete_name,null))ORDER BY record_medal DESC)  AS gold_medal,
        GROUP_CONCAT(CONCAT(IF(record_medal=10000,record_official_result,NULL))ORDER BY record_medal DESC)  AS gold_record,
        GROUP_CONCAT(CONCAT(IF(record_medal=100,athlete_name,null))ORDER BY record_medal DESC)  AS silver_medal,
        GROUP_CONCAT(CONCAT(IF(record_medal=100,record_official_result,null))ORDER BY record_medal DESC)  AS silver_record,
        GROUP_CONCAT(CONCAT(IF(record_medal=1,athlete_name,null))ORDER BY record_medal DESC)  AS bronze_medal,
        GROUP_CONCAT(CONCAT(IF(record_medal=1,record_official_result,null))ORDER BY record_medal DESC)  AS bronze_record
    FROM list_athlete 
    INNER JOIN list_record  ON record_athlete_id = athlete_id AND record_medal >=1
    INNER JOIN list_schedule ON schedule_sports = record_sports
    INNER JOIN list_sports ON sports_code = schedule_sports";

$sql_where = " WHERE record_medal >=1 AND schedule_gender = record_gender AND record_round ='final' ";
$sql_order = " ORDER BY sports_code, schedule_gender";
$sql_like = "";
$sql_group = "GROUP BY schedule_sports";

// GET METHOD로 넘어온 값을 가져옴
$searchValue = [];
$searchValue["schedule_gender"] = getSearchValue($_GET["schedule_gender"] ?? NULL);
$searchValue["sports_code"] = getSearchValue($_GET["sports_code"] ?? NULL);
// 검색값이 있는지에 대한 검증
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
    $hasSearchedAthleteGender = hasSearchedValue($searchValue["schedule_gender"]);
    $hasSearchedScheduleName = hasSearchedValue($searchValue["sports_code"]);
}

$uri_array = array();
$bindarray = array();
$keyword = array();

// 검색이 되었다면
if ($hasSearched) {
    // 성별이 검색되었다면
    if ($hasSearchedAthleteGender) {
        $uri_array["schedule_gender"] = $searchValue["schedule_gender"];
        $isGenderSelected = "'\\\\b" . $searchValue["schedule_gender"] . "\\\\b'";
        $isGenderSelected = "schedule_gender REGEXP" . str_replace('', '', $isGenderSelected);
        array_push($keyword, $isGenderSelected);
    }
    // 참가경기가 검색되었다면
    if ($hasSearchedScheduleName) {
        $uri_array["sports_code"] = $searchValue["sports_code"];
        $isSportsSelected = "'\\\\b" . $searchValue["sports_code"] . "\\\\b'";
        $isSportsSelected = "sports_code REGEXP" . str_replace('', '', $isSportsSelected);
        array_push($keyword, $isSportsSelected);
    }
    for ($i = 0; $i < count($keyword); $i++) {
        $sql_like = $sql_like . " AND " . $keyword[$i];
    }
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    if (!empty($uri_array)) {
        $link = addToLink($link, array_keys($uri_array), $uri_array);
    }
}
/**
 * 3. 페이징 입력 시,
 *      3.a 입력한 페이지 쪽수를 URL 변수에 URI로 추가
 * 
 * 페이징 컨트롤러
 * 입력한 페이지 쪽수를 URI로 추가
 */
if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}
/**
 * 4. 정렬할 카테고리 & 정렬 순서 입력 시,
 *      4.a 선택한 카테고리에 따른 ORDER BY 절을 만든다.
 *      4.b 카테고리와 정렬순서값을 URL 변수에 URI로 추가한다.
 * 
 * 정렬 기능 컨틀롤러
 * ORDER BY절 생성
 * 선택 카테고리,정렬방법을 URI로 추가
 */
if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy("", $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

// 5. SQL문에 WHERE절을 붙인다.
$sql = $sql . $sql_where . $sql_group;
/**
 * 6. 검색 O / X?
 *      6.a 검색 O
 *          6.a.i 행 수를 제한하지 않은 순수 조건문을 bind_param을 하여 실행 
 *          6.a.ii 순수 조건문에 대한 num_rows를 저장  => Get_Pagination에 쓰임
 *          6.a.iii 순수 조건문에 ORDER BY절과 LIMIT 조건문을 붙여 행 수를 제한한 sql문 생성
 *          6.a.iv 다시 bind_param을 하여 실행
 *      6.b 검색 X
 *          6.b.i 행 수를 제한하지 않은 순수 조건문을 실행
 *          6.b.ii 순수 조건문에 대한 num_rows를 저장  => Get_Pagination에 쓰임
 *          6.a.iii 순수 조건문에 ORDER BY절과 LIMIT 조건문을 붙여 행 수를 제한한 sql문 생성
 *          6.b.iv 다시 실행
 */
if (isset($searchValue)) {
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $count = $stmt->get_result();

    $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
    // +)$page_size->$pagesizeValue
    $stmt = $db->prepare($sql_complete);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $count = $db->query($sql);

    $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
    // +)$page_size->$pagesizeValue
    $result = $db->query($sql_complete);
}

// 조회된 모든 row의 수 : total_count
$total_count = mysqli_num_rows($count);

$isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
$isGenderSelected = maintainSelected($_GET["schedule_gender"] ?? null);
$isSportsSelected = maintainSelected($_GET["sports_code"] ?? NULL);

?>

<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
</head>

<body>
    <!-- header -->
    <?php require_once 'header.php'; ?>

    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>경기별 메달보기<i class="xi-equalizer-thin chart"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                    echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10개씩</option>';
                                    echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15개씩</option>';
                                    echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20개씩</option>';
                                    echo '<option value="100"' . ($pagesizeValue == 100 ? 'selected' : '') . '>100개씩</option>';
                                    if ($total_count != 0){
                                        echo '<option value="' . $total_count . "\">모두</option>\"";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="성별" name="schedule_gender">
                                    <option value="non" hidden="">성별</option>
                                    <option value="non">전체</option>
                                    <?php
                                    foreach ($schedule_gender_dic as $key) {
                                        if ($key == 'm') {
                                            $gender = '남성';
                                        } else if ($key == 'f') {
                                            $gender = '여성';
                                        } else {
                                            $gender = '혼성';
                                        }
                                        echo "<option value=$key" . ($isGenderSelected[$key] ?? NULL) . ">$gender</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="종목" name="sports_code">
                                    <option value='non' hidden="">종목</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $events = array_unique($categoryOfSports_dic);
                                    foreach ($events as $e) {
                                        echo "<optgroup label=\"$e\">";
                                        $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                        foreach ($sportsOfTheEvent as $a) {
                                            echo "<option value=$a" . ($isSportsSelected[$a] ?? NULL) . ">" . $sport_dic[$a] . "</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search">
                                <button name="search" value=search class="SearchBtn" type="submit"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width: 8%" />
                        <col style="width: auto" />
                        <col style="width: 15%" />
                        <col style="width: auto" />
                        <col style="width: 15%" />
                        <col style="width: auto" />
                        <col style="width: 15%" />
                        <col style="width: auto" />
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th onclick="sortTable(0)">경기</th>
                            <th>성별</th>
                            <th>금</th>
                            <th>기록</th>
                            <th>은</th>
                            <th>기록</th>
                            <th>동</th>
                            <th>기록</th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                    <?php
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            $sports_name = $row["sports_name"];
                            // $sports_name_kr = $row["sports_name_kr"];
                            $schedule_gender = $row["schedule_gender"];
                            $Gold_record = explode(',', $row["gold_record"]);
                            $Silver_record = explode(',', $row["silver_record"]);
                            $Bronze_record = explode(',', $row["bronze_record"]);

                            echo '<tr';
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            echo "<td>" . ($row["sports_name"]) . "</td>";
                            if ($row["schedule_gender"] == "f") {
                                echo "<td>" . "여성" . "</td>";
                            } else if ($row["schedule_gender"] == "m") {
                                echo "<td>" . "남성" . "</td>";
                            } else if ($row["schedule_gender"] == "c") {
                                echo "<td>" . "혼성" . "</td>";
                            }
                            // 클릭 시 이름 모달창
                            echo '<td class="popup_BTN">';
                            $Goldname = explode(',', trim($row["gold_medal"]));
                            if (hasSearchedValue($Goldname)) {
                                if (count($Goldname) > 1) {
                                    echo htmlspecialchars($Goldname[0]) . " 외 " . (count($Goldname) - 1) . "명";
                                } else {
                                    echo htmlspecialchars($Goldname[0]);
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '<div class="item_popup" style="display: none;">';
                            if (hasSearchedValue($Goldname)) {
                                foreach ($Goldname as $gold) {
                                    if ($gold == end($Goldname)) {
                                        echo htmlspecialchars(trim($gold));
                                    } else {
                                        echo htmlspecialchars(trim($gold)) . ',' . "<br>";
                                    }
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '</div>';
                            echo "</td>";
                            echo "<td>" . $Gold_record[0] . "</td>";
                            // 클릭 시 이름 모달창
                            echo '<td class="popup_BTN">';
                            $Silvername = explode(',', trim($row["silver_medal"]));
                            if (hasSearchedValue($Silvername)) {
                                if (count($Silvername) > 1) {
                                    echo htmlspecialchars($Silvername[0]) . " 외 " . (count($Silvername) - 1) . "명";
                                } else {
                                    echo htmlspecialchars($Silvername[0]);
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '<div class="item_popup" style="display: none;">';
                            if (hasSearchedValue($Silvername)) {
                                foreach ($Silvername as $silver) {
                                    if ($silver == end($Silvername)) {
                                        echo htmlspecialchars(trim($silver));
                                    } else {
                                        echo htmlspecialchars(trim($silver)) . ',' . "<br>";
                                    }
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }

                            echo '</div>';
                            echo "</td>";
                            echo "<td>" . $Silver_record[0] . "</td>";
                            // 클릭 시 이름 모달창
                            echo '<td class="popup_BTN">';
                            $Bronzename = explode(',', trim($row["bronze_medal"]));
                            if (hasSearchedValue($Bronzename)) {
                                if (count($Bronzename) > 1) {
                                    echo htmlspecialchars($Bronzename[0]) . " 외 " . (count($Bronzename) - 1) . "명";
                                } else {
                                    echo htmlspecialchars($Bronzename[0]);
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '<div class="item_popup" style="display: none;">';
                            if (hasSearchedValue($Bronzename)) {
                                foreach ($Bronzename as $Bronze) {
                                    if ($Bronze == end($Bronzename)) {
                                        echo htmlspecialchars(trim($Bronze));
                                    } else {
                                        echo htmlspecialchars(trim($Bronze)) . ',' . "<br>";
                                    }
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '</div>';
                            echo "</td>";
                            echo "<td>" . $Bronze_record[0] . "</td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $sql ?>" hidden />
                            <?php if (count($bindarray) !== 0) {
                                echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />';
                            }
                            ?>
                            <input type="text" name="role" value="schedule_listing" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
</body>

</html>