<?php
require_once "head.php";
// 데이터베이스 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 페이징 기능
require_once "action/module/pagination.php";
// 배열 안에 값이 존재 시에만 가져오는 함수
require_once "action/module/check_key_of_array.php";

// 페이지 관련 변수
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
        country_code,
        athlete_name,
        GROUP_CONCAT(concat(record_medal,',',record_sports,',',schedule_gender)ORDER BY record_medal)   AS result_medal,
            sum(record_medal) as medal,
            COUNT(IF(record_medal=10000,1,null)) AS gold, 
            COUNT(IF(record_medal=100,1,null)) AS silver, 
            COUNT(IF(record_medal=1,1,null)) AS bronze
            FROM list_country C  
        LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country) 
        LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id) 
        LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports) AND (S.schedule_round = R.record_round) AND (S.schedule_gender = R.record_gender)";
$sql_where = "WHERE record_medal >=1 AND schedule_sports IS NOT null AND schedule_gender = record_gender AND record_round ='final'";
$sql_order = "ORDER BY medal desc";
$sql_like = "";
$sql_group = "GROUP BY athlete_name";

$searchValue = [];
$searchValue["athlete_name"] = getSearchValue($_GET["athlete_name"] ?? NULL);
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
    $hasSearchedAthleteName = hasSearchedValue($searchValue["athlete_name"]);
}

$uri_array = array();
$bindarray = array();
$keyword = array();

// 검색이 되었다면
if ($hasSearched) {
    // 이름이 검색되었다면

    if ($hasSearchedAthleteName) {
        $uri_array["athlete_name"] = $searchValue["athlete_name"];
        $isAthleteNameSelected = "'\\\\b" . $searchValue["athlete_name"] . "\\\\b'";
        $isAthleteNameSelected = "athlete_name REGEXP" . str_replace('+', '', $isAthleteNameSelected);
        array_push($keyword, $isAthleteNameSelected);
    }
    for ($i = 0; $i < count($keyword); $i++) {
        $sql_like = $sql_like . " AND " . $keyword[$i];
    }
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    if (!empty($uri_array)) {
        $link = addToLink($link, array_keys($uri_array), $uri_array);
    }
}

//페이징
if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}

if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy("", $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

$sql = $sql . $sql_where . $sql_group;

if (isset($searchValue)) {
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $count = $stmt->get_result();

    $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
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
    $result = $db->query($sql_complete);
}
// 조회된 모든 row의 수 : total_count
$total_count = mysqli_num_rows($count);

$isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
$isAthleteNameSelected = maintainSelected($_GET["athlete_name"] ?? null);

//동순위 처리 로직
$rank_result = $db->query("SELECT  
            athlete_name,
            sum(record_medal) as medal
            FROM list_country C  
            LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country) 
            LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id) 
            LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports) AND (S.schedule_round = R.record_round) AND (S.schedule_gender = R.record_gender)
            WHERE record_medal >=1 AND schedule_sports IS NOT null AND schedule_gender = record_gender AND record_round ='final' 
            GROUP BY athlete_name
            ORDER BY medal desc ");
$array_rank = [];
$z = 1;
$prev_total = 0;
$same_rank = 0;
while ($rank_row = mysqli_fetch_array($rank_result)) {
    $all_total = $rank_row["medal"];
    if ($all_total == $prev_total) {
        $same_rank++;
    } else
        $same_rank = 0;
    $array_rank[$rank_row["athlete_name"]] = ($z - $same_rank);
    $z++;
    $prev_total = $all_total;
}
?>

<script src="assets/js/jquery-1.12.4.min.js"></script>
<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
</head>

<body>

    <?php require_once 'header.php'; ?>

    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>선수별 순위보기<i class="xi-equalizer-thin chart"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize"
                                class="changePageSize">
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
                        <div class="selectArea float_r">
                            <div class="selectArea defaultSelectArea">
                                <div class="search">
                                    <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                    <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                                    <input type="text" id="search" class="defaultSearchInput" name="athlete_name"
                                        placeholder="  이름을 입력해주세요" maxlength="30"
                                        value="<?php echo isset($searchValue["athlete_name"]) ? $searchValue["athlete_name"] : ''; ?>">
                                    <button class="defaultSearchBth" name="search" type="submit" value=search
                                        title="검색"><i class="xi-search"></i></button>
                                </div>
                            </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width: 10%;">
                        <col style="width:15%;">
                        <col style="width:25%;">
                        <col style="width:10%;">
                        <col style="width:10%;">
                        <col style="width:10%;">
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th onclick="sortTable(0)"><a
                                    href="<?= Get_Sort_Link("medal", $pageValue, $link, $orderValue) ?>">순위</a>
                            </th>
                            <th onclick="sortTable(1)">이름</th>
                            <th onclick="sortTable(2)">국가</th>
                            <th onclick="sortTable(2)"><a
                                    href="<?= Get_Sort_Link("gold", $pageValue, $link, $orderValue) ?>">금</a>
                            </th>
                            <th onclick="sortTable(3)"><a
                                    href="<?= Get_Sort_Link("silver", $pageValue, $link, $orderValue) ?>">은</a>
                            </th>
                            <th onclick="sortTable(4)"><a
                                    href="<?= Get_Sort_Link("bronze", $pageValue, $link, $orderValue) ?>">동</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $sports = explode(',', $row["result_medal"]);
                            $gold = $row['gold'];
                            $silver = $row['silver'];
                            $bronze = $row['bronze'];

                            $goldWonMatch = array();
                            $silverWonMatch = array();
                            $bronzeWonMatch = array();

                            $goldWonMatch_gender = array();
                            $silverWonMatch_gender = array();
                            $bronzeWonMatch_gender = array();

                            if (count($sports) > 1) {
                                for ($i = 0; $i <= count($sports); $i = $i + 3) {
                                    $s = getValueWhenKeyExists($i, $sports);
                                    if ($s == 10000) {
                                        array_push($goldWonMatch, $sports[$i + 1]);
                                        array_push($goldWonMatch_gender, $sports[$i + 2]);
                                    }
                                    if ($s == 100) {
                                        array_push($silverWonMatch, $sports[$i + 1]);
                                        array_push($silverWonMatch_gender, $sports[$i + 2]);
                                    }
                                    if ($s == 1) {
                                        array_push($bronzeWonMatch, $sports[$i + 1]);
                                        array_push($bronzeWonMatch_gender, $sports[$i + 2]);
                                    }
                                }
                            }
                            $num++;
                            echo "<tr";
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            echo "<td scope='col'>" . ($array_rank[$row['athlete_name']]) . "</td>";
                            echo "<td>" . htmlspecialchars($row['athlete_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['country_code']) . "</td>";

                            echo '<td class="popup_BTN">';
                            echo $gold;
                            echo '<div class="item_popup" style="display: none;">';
                            if (empty($goldWonMatch)) {
                                echo "-";
                            } else {
                                $goldWonMatches = array();
                                for ($i = 0; $i < count($goldWonMatch); $i++) {
                                    $gender = "";
                                    if ($goldWonMatch_gender[$i] ==  "f") {
                                        $gender = "(여)";
                                    } else if ($goldWonMatch_gender[$i] ==  "m") {
                                        $gender = "(남)";
                                    } else if ($goldWonMatch_gender[$i] ==  "c") {
                                        $gender = "(혼성)";
                                    }


                                    array_push($goldWonMatches, $goldWonMatch[$i] . $gender);
                                }
                                $goldWonMatches_f = array_unique($goldWonMatches);
                                $last_key = end($goldWonMatches_f);
                                foreach ($goldWonMatches_f as $g) {
                                    if ($g == $last_key) {
                                        echo htmlspecialchars($g);
                                    } else {
                                        echo htmlspecialchars($g) . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                </td>';
                            echo '<td class="popup_BTN">';
                            echo $silver;
                            echo '<div class="item_popup" style="display: none;">';
                            if (empty($silverWonMatch)) {
                                echo "-";
                            } else {
                                $silverWonMatchs = array();
                                for ($i = 0; $i < count($silverWonMatch); $i++) {
                                    $gender = "";
                                    if ($silverWonMatch_gender[$i] ==  "f") {
                                        $gender = "(여)";
                                    } else if ($silverWonMatch_gender[$i] ==  "m") {
                                        $gender = "(남)";
                                    } else if ($silverWonMatch_gender[$i] ==  "c") {
                                        $gender = "(혼성)";
                                    }
                                    array_push($silverWonMatchs, $silverWonMatch[$i] . $gender);
                                }
                                $silverWonMatchs_f = array_unique($silverWonMatchs);
                                $last_key = end($silverWonMatchs_f);
                                foreach ($silverWonMatchs_f as $g) {
                                    if ($g == $last_key) {
                                        echo htmlspecialchars($g);
                                    } else {
                                        echo htmlspecialchars($g) . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                </td>';
                            echo '<td class="popup_BTN">';
                            echo $bronze;
                            echo '<div class="item_popup" style="display: none;">';
                            if (empty($bronzeWonMatch)) {
                                echo "-";
                            } else {
                                $bronzeWonMatchs = array();
                                for ($i = 0; $i < count($bronzeWonMatch); $i++) {
                                    $gender = "";
                                    if ($bronzeWonMatch_gender[$i] ==  "f") {
                                        $gender = "(여)";
                                    } else if ($bronzeWonMatch_gender[$i] ==  "m") {
                                        $gender = "(남)";
                                    } else if ($bronzeWonMatch_gender[$i] ==  "c") {
                                        $gender = "(혼성)";
                                    }
                                    array_push($bronzeWonMatchs, $bronzeWonMatch[$i] . $gender);
                                }
                                $bronzeWonMatchs_f = array_unique($bronzeWonMatchs);
                                $last_key = end($bronzeWonMatchs_f);
                                foreach ($bronzeWonMatchs_f as $g) {
                                    if ($g == $last_key) {
                                        echo htmlspecialchars($g);
                                    } else {
                                        echo htmlspecialchars($g) . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                </td>';
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
                            <input type="text" name="role" value="player_rank_listing" hidden />
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