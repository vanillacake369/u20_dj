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
    $page_list_size = 10;
    $link = "";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;

    $pageSizeOption = [];
    array_push($pageSizeOption, 10);
    array_push($pageSizeOption, 15);
    array_push($pageSizeOption, 20);
    array_push($pageSizeOption, 100);
    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? NULL);
    
    // SQL 조건문
    $sql = "SELECT
                country_name,    
                country_code,
                count(DISTINCT(IF(record_medal=10000,schedule_sports,NULL))) as gold_total,
                count(DISTINCT(IF(record_medal=100,schedule_sports,NULL))) as silver_total,
                count(DISTINCT(IF(record_medal=1,schedule_sports,NULL))) as bronze_total,                   
                GROUP_CONCAT(concat(record_medal,',',schedule_sports,',',schedule_gender)ORDER BY record_medal)  AS result_medal,
                sum(record_medal) as medal
            FROM
                list_country C
            LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country)
            LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id)
                AND record_medal > 0
            LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports)";
    $sql_where = "WHERE country_name IS NOT NULL AND schedule_gender = record_gender AND record_round ='final'";
    $sql_order = "ORDER BY gold_total desc, silver_total desc, bronze_total desc";
    $sql_like = "";
    $sql_group = "GROUP BY country_name";
    /**
     * 검색값 X
     * - sql + sql_where + sql_group + sql_order
     * 검색값 O
     * - sql + sql_where + sql_like + sql_group + sql_order
     */
    // GET METHOD로 넘어온 값을 가져옴
    $searchValue = [];
    $searchValue["country_name"] = getSearchValue($_GET["country_name"] ?? NULL);
    $hasSearched = hasSearchedValue($searchValue);
    $hasSearchedCode = 0;
    if ($hasSearched) {
        $hasSearchedCode = hasSearchedValue($searchValue["country_name"]);
    }
    
    $uri_array = array();
    $bindarray = array();
    $keyword = array();
    
    
    // 검색이 되었다면
    if (isset($searchValue)) {
        // 국가이름가 검색되었다면
        if ($hasSearchedCode) {
            $uri_array["country_name"] = $searchValue["country_name"];
            $isAthleteNameCodeSelected = "'\\\\b" . $searchValue["country_name"] . "\\\\b'";
            $isAthleteNameCodeSelected = "country_name REGEXP" . str_replace('+', '', $isAthleteNameCodeSelected);
            array_push($keyword, $isAthleteNameCodeSelected);
        }
        for ($i = 0; $i < count($keyword); $i++) {
            $sql_like = $sql_like . " AND " . $keyword[$i];
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
        $sql_order = makeOrderBy("", $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }
    
    $sql = $sql . $sql_where . $sql_group;
    
    if (isset($searchValue)) {
        $stmt = $db->prepare($sql);
    
        $stmt->execute();
        $count = $stmt->get_result();
    
        $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $stmt = $db->prepare($sql_complete);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $count = $db->query($sql);
    
        $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $result = $db->query($sql_complete);
    }
    $total_count = mysqli_num_rows($count);
    
    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
    $isAthleteNameCodeSelected = maintainSelected($_GET["country_code"] ?? null);
    
    //동순위 처리 로직
    $rank_result = $db->query("SELECT  
            country_code, 
            count(DISTINCT(IF(record_medal=10000,schedule_sports,NULL))) as gold_total, 
            count(DISTINCT(IF(record_medal=100,schedule_sports,NULL))) as silver_total, 
            count(DISTINCT(IF(record_medal=1,schedule_sports,NULL))) as bronze_total 
            FROM list_country C 
            LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country) 
            LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id) AND record_medal > 0 
            LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports)
            WHERE country_name IS NOT NULL AND schedule_gender = record_gender AND record_round ='final'
            GROUP BY country_name 
            ORDER BY gold_total desc , silver_total desc, bronze_total desc");
    $array_rank = [];
    $z = 1;
    $prev_total = 0;
    $same_rank = 0;
    while ($rank_row = mysqli_fetch_array($rank_result)) {
        $all_total = $rank_row[1] * 100 + $rank_row[2] * 10 + $rank_row[3];
        if ($all_total == $prev_total) {
            $same_rank++;
        } else
            $same_rank = 0;
        $array_rank[$rank_row["country_code"]] = ($z - $same_rank);
        $z++;
        $prev_total = $all_total;
    }
    ?>

<script src="/assets/js/jquery-1.12.4.min.js"></script>
<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
</head>

<body>
    <!-- header -->
    <?php require_once 'header.php'; ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>국가별 순위보기<i class="xi-equalizer-thin chart"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                    $get_count_sql = "SELECT COUNT(*) AS record_count FROM list_country C
                                    LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country)
                                    LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id)
                                        AND record_medal > 0
                                    LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports) WHERE country_name IS NOT NULL AND schedule_gender = record_gender AND record_round ='final'";
                                    $count_result = $db->query($get_count_sql);
                                    $count_row = mysqli_fetch_array($count_result);
                                    $size_of_all = $count_row["record_count"];
                                    foreach ($pageSizeOption as $size) {
                                        echo '<option value="' . $size . '"' . ($isPageSizeChecked[$size] ?? NULL) . ">" . $size . "개씩</option>\"";
                                    }
                                    if ($size_of_all != 0){
                                        echo '<option value="' . $size_of_all . '"' . ($isPageSizeChecked[$size_of_all] ?? NULL) . ">모두</option>\"";
                                        }
                                ?>
                            </select>
                        </div>
                        <div class="selectArea float_r">
                            <div class="selectArea defaultSelectArea">
                                <div class="search">
                                    <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                    <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                                    <input type="text" id="search" class="defaultSearchInput" name="country_code"
                                        placeholder="국가를 입력해주세요" maxlength="30"
                                        value="<?php echo isset($searchValue["country_code"]) ? $searchValue["country_code"] : ''; ?>">
                                    <button name="search" value=search class="defaultSearchBth" type="submit"><i
                                            class="xi-search"></i></button>
                                </div>
                            </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width: 10%" />
                        <col style="width: 40%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th onclick="sortTable(0)"><a href="<?= Get_Sort_Link("medal", $pageValue, $link, $orderValue) ?>">순위</a></th>
                            <th onclick="sortTable(1)">국가</th>
                            <th onclick="sortTable(2)">금
                            </th>
                            <th onclick="sortTable(3)">은
                            </th>
                            <th onclick="sortTable(4)">동
                            </th>
                            <th onclick="sortTable(5)"><a href="<?= Get_Sort_Link("medal", $pageValue, $link, $orderValue) ?>">합계</a></th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                    <?php
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {

                            $country_code = $row['country_code'];
                            $country_name = $row['country_name'];

                            $total_medal = $row["gold_total"] + $row["silver_total"] + $row["bronze_total"];

                            $sports = explode(',', $row["result_medal"]);

                            $goldWonMatch = array();
                            $silverWonMatch = array();
                            $bronzeWonMatch = array();
                            $totalMatch = array();

                            $goldWonMatch_gender = array();
                            $silverWonMatch_gender = array();
                            $bronzeWonMatch_gender = array();
                            $totalMatch_gender = array();

                            $Match = array();
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
                                    if ($s == 10000  || $s == 100 || $s == 1) {
                                        array_push($totalMatch, $sports[$i + 1]);
                                        array_push($totalMatch_gender, $sports[$i + 2]);
                                    }
                                }
                            }
                            $num++;
                            echo "<tr";
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            echo "<td scope='col'>" . ($array_rank[$country_code]) . "</td>";
                            echo "<td>" . htmlspecialchars($country_name) . '(' . ($country_code) . ')' . "</td>";
                            echo '<td class="popup_BTN">';
                            echo $row["gold_total"];
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
                                        echo htmlspecialchars($g) . ',' . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                    </td>';
                            echo '<td class="popup_BTN">';
                            echo $row["silver_total"];
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
                                        echo htmlspecialchars($g) . ',' . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                    </td>';
                            echo '<td class="popup_BTN">';
                            echo $row["bronze_total"];
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
                                        echo htmlspecialchars($g) . ',' . "<br>";
                                    }
                                }
                            }
                            echo '</div>
                                    </td>';
                            echo '<td class="popup_BTN">';
                            echo $total_medal;
                            echo '<div class="item_popup" style="display: none;">';
                            if (empty($totalMatch)) {
                                echo "-";
                            } else {
                                $totalMatchs = array();
                                for ($i = 0; $i < count($totalMatch); $i++) {
                                    $gender = "";
                                    if ($totalMatch_gender[$i] ==  "f") {
                                        $gender = "(여)";
                                    } else if ($totalMatch_gender[$i] ==  "m") {
                                        $gender = "(남)";
                                    } else if ($totalMatch_gender[$i] ==  "c") {
                                        $gender = "(혼성)";
                                    }
                                    array_push($totalMatchs, $totalMatch[$i] . $gender);
                                }
                                $totalMatchs_f = array_unique($totalMatchs);
                                $last_key = end($totalMatchs_f);
                                foreach ($totalMatchs_f as $g) {
                                    if ($g == $last_key) {
                                        echo htmlspecialchars($g);
                                    } else {
                                        echo htmlspecialchars($g) . ',' . "<br>";
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
                            <input type="text" name="role" value="country_listing" hidden />
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