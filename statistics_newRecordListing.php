<?php
require_once "head.php";
// 데이터베이스 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 페이징 기능
require_once "action/module/pagination.php";

// 페이지 관련 변수
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
$page_list_size = 10;
$link = "";
$page_list_count = ($pageValue - 1) * $pagesizeValue;

// SQL 조건문
$sql = "SELECT
            worldrecord_athletics,
            worldrecord_sports,
            sports_code,
            sports_name,
            worldrecord_athlete_name,
            worldrecord_gender,
            worldrecord_location,
            worldrecord_wind,
            worldrecord_record,
            worldrecord_datetime,
            worldrecord_country_code,
            country_name_kr
        FROM
            list_worldrecord
        INNER JOIN
            list_sports ON worldrecord_sports =sports_code
        INNER JOIN
            list_country ON worldrecord_country_code=country_code AND worldrecord_datetime >= '2015-01-01' "; //기록일자는 임시로 지정
$sql_order = "ORDER BY sports_code, worldrecord_athletics, worldrecord_gender ";
$sql_like = "";
$sql_where = "";

// GET METHOD로 넘어온 값을 가져옴
$searchValue = [];
$searchValue["worldrecord_athletics"] = getSearchValue($_GET["worldrecord_athletics"] ?? NULL);
$searchValue["worldrecord_gender"] = getSearchValue($_GET["worldrecord_gender"] ?? NULL);
$searchValue["sports_code"] = getSearchValue($_GET["sports_code"] ?? NULL);
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
    $hasSearchedAthletics = hasSearchedValue($searchValue["worldrecord_athletics"]);
    $hasSearchedGender = hasSearchedValue($searchValue["worldrecord_gender"]);
    $hasSearchedSports_name = hasSearchedValue($searchValue["sports_code"]);
}

$uri_array = array();
$bindarray = array();
$keyword = array();
// 검색이 되었다면
if ($hasSearched) {
    // 기록구분이 검색되었다면
    if ($hasSearchedAthletics) {
        $uri_array["worldrecord_athletics"] = $searchValue["worldrecord_athletics"];
        array_push($bindarray, $searchValue["worldrecord_athletics"]);
        array_push($keyword, "worldrecord_athletics=?");
    }
    // 성별이 검색되었을 때
    if ($hasSearchedGender) {
        $uri_array["worldrecord_gender"] = $searchValue["worldrecord_gender"];
        array_push($bindarray, $searchValue["worldrecord_gender"]);
        array_push($keyword, "worldrecord_gender=?");
    }

    //종목이 검색 되었을 때
    if ($hasSearchedSports_name) {
        $uri_array["sports_code"] = $searchValue["sports_code"];
        array_push($bindarray, $searchValue["sports_code"]);
        array_push($keyword, "sports_code=?");
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

$sql = $sql . $sql_where;

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
$total_count = mysqli_num_rows($count);

$isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
$isAthleticsSelected = maintainSelected($_GET["worldrecord_athletics"] ?? NULL);
$isGenderSelected = maintainSelected($_GET["worldrecord_gender"] ?? null);
$isSportsSelected = maintainSelected($_GET["sports_code"] ?? NULL);
?>

<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />

</head>

<body>
    <!-- header -->
    <?php require_once 'header.php' ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>신기록 경기기록<i class="xi-equalizer-thin chart"></i></h1>
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
                        <div class="selectArea float_r">
                            <div class="selectArea defaultSelectArea">
                                <div class="defaultSelectBox">
                                    <select title="기록선택" name="worldrecord_athletics">
                                        <option value="non" hidden="">기록선택</option>
                                        <option value="non">전체</option>
                                        <?php
                                        foreach ($worldrecord_athletics_dic as $s) {
                                            if ($s == 'w') {
                                                $k = '세계신기록';
                                            } else if ($s == 'u') {
                                                $k = '세계U20신기록';
                                            } else if ($s == 'a') {
                                                $k = '아시아신기록';
                                            } else if ($s == 's') {
                                                $k = '아시아U20신기록';
                                            } else {
                                                $k = '대회신기록';
                                            }
                                            echo "<option value=$s" . ($isAthleticsSelected[$s] ?? NULL) . ">$k</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="defaultSelectBox">
                                    <select title="성별" name="worldrecord_gender">
                                        <option value="non" hidden="">성별</option>
                                        <option value="non">전체</option>
                                        <?php
                                        foreach ($worldrecord_gender_dic as $key) {
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
                                    <button class="SearchBtn" type="submit" name="search" value=search><i class="xi-search"></i></button>
                                </div>
                            </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width:15%;">
                        <col style="width:13%;">
                        <col style="width:20%;">
                        <col style="width:8%;">
                        <col style="width:8%;">
                        <col style="width:13%;">
                        <col style="width:10%;">
                        <col style="width:8%;">
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th onclick="sortTable(0)">기록구분</th>
                            <th onclick="sortTable(1)">종목</th>
                            <th onclick="sortTable(2)">이름</th>
                            <th onclick="sortTable(3)">성별</th>
                            <th onclick="sortTable(4)">풍속/용기구</th>
                            <th onclick="sortTable(5)">기록</th>
                            <th onclick="sortTable(6)">기록일자</th>
                            <th onclick="sortTable(7)">국가</th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            echo "<tr";
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            if ($row["worldrecord_athletics"] == "w") {
                                echo "<td>" . "세계신기록" . "</td>";
                            } else if ($row["worldrecord_athletics"] == "u") {
                                echo "<td>" . "세계U20신기록" . "</td>";
                            } else if ($row["worldrecord_athletics"] == "a") {
                                echo "<td>" . "아시아신기록" . "</td>";
                            } else if ($row["worldrecord_athletics"] == "s") {
                                echo "<td>" . "아시아U20신기록" . "</td>";
                            } else {
                                echo "<td>" . "대회신기록" . "</td>";
                            }
                            echo "<td>" . htmlspecialchars($row["worldrecord_sports"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_athlete_name"]) . "</td>";
                            if ($row["worldrecord_gender"] == "f") {
                                echo "<td>" . "여성" . "</td>";
                            } else if ($row["worldrecord_gender"] == "m") {
                                echo "<td>" . "남성" . "</td>";
                            } else if ($row["worldrecord_gender"] == "c") {
                                echo "<td>" . "혼성" . "</td>";
                            }

                            echo "<td>" . htmlspecialchars($row["worldrecord_wind"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_record"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_datetime"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_country_code"]) . "</td>";
                            echo "</tr>";
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
                            <input type="text" name="role" value="new_record_listing" hidden />
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