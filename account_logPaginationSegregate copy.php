<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/assets/css/style.css" />
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css" />
    <script src="./assets/fontawesome/js/all.min.js"></script>
    <!--Data Tables-->
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css" />
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="js/useDataTables.js"></script>
    <script type="text/javascript" src="js/sorting.js"></script>
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script>
        $.Lithium = {};
        $.Lithium.tooltip = function() {
            $('.tooltip-click').on('click', function() {
                $(this).toggleClass('on')
            })

            $(document).on('click', function(event) {
                if ($(event.target).closest('.tooltip-click').length === 0) {
                    $('.tooltip-click').removeClass('on')
                }
            });
        };
        $(document).ready(function() {
            $.Lithium.tooltip();
        })
    </script>
    <title>U20</title>

    <?php
    // 데이터베이스 연결
    include_once(__DIR__ . "/database/dbconnect.php");
    // 국가,종목,지역,직무에 대한 매핑구조
    include_once(__DIR__ . "/action/module/dictionary.php");
    // 페이징 기능
    include_once(__DIR__ . "/action/module/pagination.php");

    $searchValue = [];
    $searchValue["athlete_name"] = getSearchValue($_GET["athlete_name"] ?? null);
    $pageValue = getPageValue($_GET["page"] ?? null);
    $categoryValue = getCategoryValue($_GET["order"] ?? null);
    $orderValue = getOrderValue($_GET["sc"] ?? null);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? null);

    $page_list_size = 10;
    $link = "";

    $sql_where = " WHERE  R.record_result <= 3 ";
    $sql_order = "GROUP BY athlete_id ORDER BY gold DESC, silver DESC, bronze DESC";
    $sql_like = "";

    // page 내 row 에 따른 "page 번호";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;
    //+) $page_size->$pagesizeValue

    // pageSizeOption : 한 페이지 내의 행 수
    $pageSizeOption = [];
    array_push($pageSizeOption, 5);
    array_push($pageSizeOption, 10);
    array_push($pageSizeOption, 15);
    array_push($pageSizeOption, 20);

    $sql = "SELECT
            C.country_code,
            C.country_name,
            A.athlete_name,
            GROUP_CONCAT(concat(R.record_medal,',',R.record_schedule_id)ORDER BY R.record_schedule_id)  AS result_medal,
            SUM( IF( schedule_sports LIKE '%relay', IF(record_medal=10000,2500,null), IF(record_medal=10000,10000,null) ) ) AS gold,
            SUM( IF( schedule_sports LIKE '%relay', IF(record_medal=100,25,null), IF(record_medal=100,100,null) ) ) AS silver,
            SUM( IF( schedule_sports LIKE '%relay', IF(record_medal=1,0.25,null), IF(record_medal=1,1,null) ) ) AS bronze
        FROM list_country C
        LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country)
        LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id)
        LEFT JOIN list_schedule S ON (S.schedule_id = R.record_schedule_id)";
    /**
     * 2. 검색값 입력 시,coach_id
     *      2.a bindarray,keyword array를 통해 WHERE절을 만든다.
     *
     * 조건 검색 컨트롤러
     * uri_array : URI String(key) => URI Value(value)
     * bindarray : 인덱스(key) => 검색 입력값(value)
     * keyword : 인덱스(key) => DB조건문(value)
     */
    $uri_array = array();
    $bindarray = array();
    $keyword = array();
    if (count($searchValue)) {
        if ((!is_null($searchValue["athlete_name"])) && ($searchValue["athlete_name"] != "non")) {
            $uri_array["athlete_name"] = $searchValue["athlete_name"];
            array_push($bindarray, trim("%{$searchValue["athlete_name"]}%"));
            array_push($keyword, "athlete_name LIKE ?");
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
    $sql = $sql . $sql_where;

    echo "sql where : ";
    echo '<br>';
    print_r($sql);
    echo '<br>';
    echo '<br>';

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

        $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";

        echo "sql where order limit : ";
        echo '<br>';
        print_r($sql);
        echo '<br>';
        echo '<br>';

        // +)$page_size->$pagesizeValue
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
        // +)$page_size->$pagesizeValue
        $result = $db->query($sql);
    }

    // 조회된 모든 row의 수 : total_count
    $total_count = mysqli_num_rows($count);

    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
    $isCountrySelected = maintainSelected($_GET["athlete_name"] ?? null);


    echo " page_list_size : ";
    print_r($page_list_size);
    echo "<br>";

    echo "pagesizeValue : ";
    print_r($pagesizeValue);
    echo "<br>";

    echo "pageValue : ";
    print_r($pageValue);
    echo "<br>";

    echo "total_count : ";
    print_r($total_count);
    echo "<br>";

    echo "link : ";
    print_r($link);
    echo "<br>";

    ?>
</head>

<body>
    <!-- header -->
    <?php // include 'header.php'
    ?>

    <!-- sidebar -->
    <?php // include 'sidebar.php'
    ?>

    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="something contents">
            <h2 class="country_h2">선수 순위보기</h2>
            <div class="page_size">
                <label class=>페이지당
                    <select name="page_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                        <?php
                        echo '<option value="5"' . ($pagesizeValue == 5 ? 'selected' : '') . '>5</option>';
                        echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10</option>';
                        echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15</option>';
                        echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20</option>';
                        ?>
                    </select> 개씩 보기
                </label>
            </div>
            <div class="table_wrap">
                <!-- 엑셀 출력 버튼 -->
                <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                    <input type="submit" name="query" id="execute_excel" value="<?php echo $sql ?>" hidden />
                    <?php if (count($bindarray) !== 0) {
                        echo '<input type="text" name="keyword" value=' . implode(',', $bindarray) . ' hidden />';
                    }
                    ?>
                    <input type="text" name="role" value="coach" hidden />
                    <label for="execute_excel" class="btn_excel label_for_excel_import bold float_l">엑셀 출력</label>
                </form>
                <!-- 엑셀 입력 버튼 -->
                <form action="./excel_to_db.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="upload_judge" onchange="this.form.submit()" accept=".csv" hidden />
                    <input type="text" name="role" value="coach" hidden />
                    <label for="upload_judge" class="btn_excel label_for_excel_import bold float_l">엑셀 입력</label>
                </form>
                <!-- 조건 검색 -->
                <form action="" enctype="multipart/form-data" class="searchForm" name="judge_searchForm" method="get" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end;">
                    <div class="selectArea float_r">
                        <!-- 이름 조건 검색 -->
                        <div class="search" style="width: 15em;">
                            <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                            <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                            <input type="text" id="search" class="word" name="athlete_name" placeholder="이름을 입력해주세요" maxlength="30">
                            <button name="search" value=search type="submit" class="btn_search" title="검색"></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tbl_area">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                    <colgroup>
                        <col style="width: 10%;">
                        <col style="width:15%;">
                        <col style="width:25%;">
                        <col style="width:10%;">
                        <col style="width:10%;">
                        <col style="width:10%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)"><a href="<?= Get_Sort_Link("datetime", $pageValue, $link, $orderValue) ?>">순위</a></th>
                            <th onclick="sortTable(1)">국가</th>
                            <th onclick="sortTable(2)">이름</th>
                            <th onclick="sortTable(3)">금</th>
                            <th onclick="sortTable(4)">은</th>
                            <th onclick="sortTable(5)">동</th>
                        </tr>
                    </thead>
                    <?php
                    $z = 1;
                    $same_rank = 0;
                    $prev_total = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        $sports = explode(',', $row["result_medal"]);

                        $all_total = $row['gold'] + $row['silver'] + $row['bronze'];
                        if ($all_total == $prev_total) {
                            $same_rank++;
                        } else {
                            $same_rank = 0;
                        }

                        $country_name = $row['country_name'];
                        $country_code = $row['country_code'];
                        $athlete_name = $row['athlete_name'];

                        $gold = $row['gold'] / 10000;
                        $silver = $row['silver'] / 100;
                        $bronze = $row['bronze'] / 1;

                        $sports = explode(',', $row["result_medal"]);
                        // print_r(end($sports));
                        // print_r(($sport_dic[$sports[0]]));
                        // print_r(($sport_dic[$sports[1]]));
                        // print_r(($sport_dic[$sports[2]]));
                        // print_r(($sport_dic[$sports[3]]));
                        // print_r(($sport_dic[$sports[4]]));
                        // print_r(($sport_dic[$sports[5]]));
                        // echo "<br>";
                        // echo "<br>";

                        $goldWonMatch = array();
                        $silverWonMatch = array();
                        $bronzeWonMatch = array();
                        for ($i = 0; $i + 1 <= end($sports); $i = $i + 2) {
                            if (isset($sports[$i])) {
                                if ($sports[$i] == 10000) {
                                    array_push($goldWonMatch, $sport_dic[$sports[$i + 1]]);
                                }
                                if ($sports[$i] == 100) {
                                    array_push($silverWonMatch, $sport_dic[$sports[$i + 1]]);
                                }
                                if ($sports[$i] == 1) {
                                    array_push($bronzeWonMatch, $sport_dic[$sports[$i + 1]]);
                                }
                            }
                        }

                        echo "<tr>";
                        echo "<td>" . $z - $same_rank . "</td>";
                        echo "<td>" . htmlspecialchars($country_name) . '(' . htmlspecialchars($country_code) . ')' . "</td>";
                        echo "<td>" . htmlspecialchars($athlete_name) . "</td>";

                        echo '<td class="tooltip-container tooltip-interactive tooltip-bottom tooltip-click center">';
                        echo '<i class="tooltip-click-trigger non-italic">' . htmlspecialchars((int) ($gold)) . '</i>';
                        echo '<div class="tooltip">';
                        echo '<div class="tooltip-description">';

                        foreach ($goldWonMatch as $match) {
                            if ($match == end($goldWonMatch)) {
                                echo htmlspecialchars($match);
                            } else {
                                echo htmlspecialchars($match) . ',';
                            }
                        }
                        if (empty($goldWonMatch)) {
                            echo "<br>";
                        }

                        echo '</div>
        </div>
        </td>';

                        echo '<td class="tooltip-container tooltip-interactive tooltip-bottom tooltip-click center">';
                        echo '<i class="tooltip-click-trigger non-italic">' . htmlspecialchars((int) ($silver)) . '</i>';
                        echo '<div class="tooltip">';
                        echo '<div class="tooltip-description">';
                        foreach ($silverWonMatch as $match) {
                            if ($match == end($silverWonMatch)) {
                                echo htmlspecialchars($match);
                            } else {
                                echo htmlspecialchars($match) . ',';
                            }
                        }
                        if (empty($silverWonMatch)) {
                            echo "<br>";
                        }

                        echo '</div>
         </div>
        </td>';
                        echo '<td class="tooltip-container tooltip-interactive tooltip-bottom tooltip-click center">';
                        echo '<i class="tooltip-click-trigger non-italic">' . htmlspecialchars((int) ($bronze)) . '</i>';
                        echo '<div class="tooltip">';
                        echo '<div class="tooltip-description">';
                        foreach ($bronzeWonMatch as $match) {
                            if ($match == end($bronzeWonMatch)) {
                                echo htmlspecialchars($match);
                            } else {
                                echo htmlspecialchars($match) . ',';
                            }
                        }
                        if (empty($bronzeWonMatch)) {
                            echo "<br>";
                        }

                        echo '</div>
        </div>
        </td>';
                        echo '</tr>';
                        $z++;
                        $prev_total = $all_total;
                    }
                    ?>

                </table>
            </div>
            <!-- 페이징 -->
            <div class="page_wrap">
                <div class="page_nation">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>

            <!-- footer -->
            <?php include __DIR__ . '/footer.php'; ?>

            <script src="js/main.js"></script>
</body>

</html>