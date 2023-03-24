<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "action/module/pagination.php";
require_once "action/module/dictionary.php";

// 로그 기능
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}

pageAuthCheck($db, "authSchedulesRead");
$searchValue = getSearchValue($_GET["search_country"] ?? NULL);
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

$pageSizeOption = [];
    array_push($pageSizeOption, 10);
    array_push($pageSizeOption, 15);
    array_push($pageSizeOption, 20);
    array_push($pageSizeOption, 100);
    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? NULL);

$page_list_size = 10;
$link = "";

$tableName = "list_country";
$columnStartsWith = "country_";
$id = $columnStartsWith . "name";


$sql_where = " WHERE $id > 'a'";
$sql_order = " ORDER BY $id DESC ";
$sql_like = " AND (country_code LIKE ? OR country_name LIKE ? OR country_name_kr LIKE ?)";

$page_list_count = ($pageValue - 1) * $pagesizeValue;
$param = null;

if (isset($searchValue)) {
    $param = "%" . trim($_GET['search_country']) . "%";
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    $link = addToLink($link, "&search_country=", $searchValue);
}

if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}

if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}


$sql = "SELECT country_name, country_name_kr, country_code FROM  $tableName $sql_where";
$excel = $sql . $sql_order;

if (isset($searchValue)) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $count = $stmt->get_result();

    $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $param, $param, $param);
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

    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank countryList defaultList">
                <div class="MainRank_tit">
                    <h1>국가 목록<i class="xi-calendar calendar"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="director_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                    $get_country_count_sql = "SELECT COUNT(*) AS country_count FROM list_country;";
                                    $country_count_result = $db->query($get_country_count_sql);
                                    $country_count_row = mysqli_fetch_array($country_count_result);
                                    $size_of_all = $country_count_row["country_count"];
                                    foreach ($pageSizeOption as $size) {
                                        echo '<option value="' . $size . '"' . ($isPageSizeChecked[$size] ?? NULL) . ">" . $size . "개씩</option>\"";
                                    }
                                    echo '<option value="' . $size_of_all . '"' . ($isPageSizeChecked[$size_of_all] ?? NULL) . ">모두</option>\"";
                                ?>   
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="search">
                                <input type="text" id="search_country" class="defaultSearchInput" name="search_country" placeholder="검색어를 입력해주세요" maxlength="30" value="<?php echo isset($searchValue) ? $searchValue : ''; ?>">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <button name="search" value="search" class="defaultSearchBth" type="submit"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">구분</th>
                            <th colspan="5" scope="col"><a href="<?= Get_Sort_Link("name", $pageValue, $link, $orderValue) ?>">국가
                                    이름(ENG)</a></th>
                            <th colspan="4" scope="col"><a href="<?= Get_Sort_Link("name_kr", $pageValue, $link, $orderValue) ?>">국가
                                    이름(KOR)</a></th>
                            <th colspan="4" scope="col"><a href="<?= Get_Sort_Link("code", $pageValue, $link, $orderValue) ?>">국가 코드</a>
                            </th>
                            <?php if (authCheck($db, "authSchedulesDelete")) { ?>
                                <th colspan="1" scope="col">삭제</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $i = $total_count - $page_list_count;
                        $j = 0;
                        $num = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            $where = '';
                            echo '<tr';
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo '>';
                            echo "<td>" . $i . "</td>";
                            echo "<td colspan='5' scope='col'>" . htmlspecialchars($row["country_name"]) . "</td>";
                            echo "<td colspan='4' scope='col'>" . htmlspecialchars($row["country_name_kr"]) . "</td>";
                            echo "<td colspan='4' scope='col'>" . htmlspecialchars($row["country_code"]) . "</td>";

                            echo "</td>";
                            if (authCheck($db, "authSchedulesDelete")) {
                                echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["country_code"] . "','country');" . " value='삭제' class='BTN_Red defaultBtn'></td>";
                            }

                            echo "</tr>";
                            $i--;
                            $j++;
                        }    ?>
                        <?php
                        if (isset($_POST["country_delete"])) {
                            $sql = "DELETE FROM list_country WHERE country_code=?";
                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("s", $_POST["country_delete"]);
                            $stmt->execute();
                            logInsert($db, $_SESSION['Id'], '국가 삭제', $_POST["country_delete"]);
                            echo "<script>location.href='./sport_countrymanagement.php';</script>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                            <?php if ($param != null) {
                                $param = array_fill(0, 3, $param);
                                echo '<input type="text" name="keyword" value="' . implode(',', $param) . '" hidden />';
                            }
                            ?>
                            <input type="text" name="role" value="country_management" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>

                    </div>
                    <div class="registrationBtn">
                        <?php
                        if (authCheck($db, "authSchedulesCreate")) { ?>
                            <button class="defaultBtn BIG_btn BTN_Blue" type="button" onclick="createPopupWin('sport_country_input.php','창 이름',900,900)">등록</button>
                        <?php } ?>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/main_dh.js"></script>
</body>

</html>