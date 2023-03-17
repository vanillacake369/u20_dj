<?php
    require_once "head.php";
    require_once __DIR__ . "auth/config.php";
    require_once __DIR__ . "action/module/pagination.php";

    $searchValue = [];
    $searchValue["sports_code"] = getSearchValue($_GET["sports_code"] ?? NULL);
    $searchValue["sports_name"] = getSearchValue($_GET["sports_name"] ?? NULL);
    $searchValue["sports_name_kr"] = getSearchValue($_GET["sports_name_kr"] ?? NULL);
    $pageValue = getPageValue($_GET["page"] ?? NULL);
    $categoryValue = getCategoryValue($_GET["order"] ?? NULL);
    $orderValue = getOrderValue($_GET["sc"] ?? NULL);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

    $page_list_size = 10;
    $link = "";

    $tableName = "list_sports";
    $columnStartsWith = "sports_";
    $id = $columnStartsWith . "code";

    $sql_where = " WHERE ($id > 'a' or $id > '1')";
    $sql_order = " ORDER BY $id DESC ";
    $sql_like = "";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;

    $sql = "SELECT sports_code,sports_name,sports_name_kr FROM list_sports";
    $uri_array = array();
    $bindarray = array();
    $keyword = array();

    if (count($searchValue)) {
        $keyword = array();
        $bindarray = array();

        if ((!is_null($searchValue["sports_code"])) && ($searchValue["sports_code"] != "non")) {
            $uri_array["sports_code"] = $searchValue["sports_code"];
            array_push($bindarray, $searchValue["sports_code"]);
            array_push($keyword, "sports_code=?");
        }
        if ((!is_null($searchValue["sports_name"])) && ($searchValue["sports_name"] != "non")) {
            $uri_array["sportse_name"] = $searchValue["sports_name"];
            array_push($bindarray, $searchValue["sports_name"]);
            array_push($keyword, "sports_name=?");
        }
        if ((!is_null($searchValue["sports_name_kr"])) && ($searchValue["sports_name_kr"] != "non")) {
            $uri_array["sports_name_kr"] = $searchValue["sports_name_kr"];
            array_push($bindarray, $searchValue["sports_name_kr"]);
            array_push($keyword, "sports_name_kr=?");
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
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }

    $sql = $sql . $sql_where;
    $excel = $sql . $sql_order;

    if (isset($searchValue) && ($searchValue["sports_code"] != "non" || $searchValue["sports_name"] != "non" || $searchValue["sports_name_kr"] != "non")) {
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
    <?php include __DIR__ . '/header.php' ?>

    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="contents main-table">

            <div class="country_table space">
                <div class="team_tabs-content tab">
                    <table class="table table-hover team_table">
                        <h2 class="country_h2">경기 목록</h2>
                        <div class="page_size">
                            <label class=>페이지당
                                <select name="page_size" onchange="changeTableSize(this);" id="changePageSize"
                                    class="changePageSize">
                                    <?php
                                    echo '<option value="5"' . ($pagesizeValue == 5 ? 'selected' : '') . '>5</option>';
                                    echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10</option>';
                                    echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15</option>';
                                    echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20</option>';
                                    ?>
                                </select> 개씩 보기
                            </label>
                        </div>

                        <div class="selectArea float_l">
                            <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                                <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>"
                                    hidden />
                                <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value=' . implode(',', $bindarray) . ' hidden />' ?>
                                <input type="text" name="role" value="sport_management" hidden />

                                <label for="execute_excel"
                                    class="btn_excel label_for_excel_import bold float_l">엑셀출력</label>
                            </form>
                        </div>

                        <form action="" enctype="multipart/form-data" class="searchForm" name="judge_searchForm"
                            method="get"
                            style="display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end;">

                            <div class="selectArea float_r">
                                <div class="select_box mr10">
                                    <select class="d_select" title="경기종목 코드" style="width: 160px;" name="sports_code">
                                        <option value="non" hidden="">경기종목 코드</option>
                                        <?php
                                        $sSql = "SELECT distinct sports_code FROM list_sports ;";
                                        $sResult = $db->query($sSql);
                                        while ($sRow = mysqli_fetch_array($sResult)) {
                                            echo "<option value=" . $sRow['sports_code'] . ' ' . ($_GET["sports_code"] == $sRow['sports_code'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_code']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="select_box mr10">
                                    <select class="d_select" title="경기종목 이름" style="width: 140px;" name="sports_name">
                                        <option value="non" hidden="">경기종목 이름</option>
                                        <?php
                                        $sSql = "SELECT distinct sports_name FROM list_sports;";
                                        $sResult = $db->query($sSql);
                                        while ($sRow = mysqli_fetch_array($sResult)) {
                                            echo "<option value=" . $sRow['sports_name'] . ' ' . ($_GET["sports_name"] == $sRow['sports_name'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="select_box mr10">
                                    <select class="d_select" title="구분" style="width: 172.667px;" name="sports_name_kr">
                                        <option value="non" hidden="">경기종목 이름(한글)</option>
                                        <?php
                                        $sSql = "SELECT distinct sports_name_kr FROM list_sports;";
                                        $sResult = $db->query($sSql);
                                        while ($sRow = mysqli_fetch_array($sResult)) {
                                            echo "<option value=" . $sRow['sports_name_kr'] . ' ' . ($_GET["sports_name_kr"] == $sRow['sports_name_kr'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_name_kr']) . "</option>";
                                        }
                                        ?>

                                    </select>

                                </div>
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <div class="search" style="width: 50px">
                                    <button name="search" value="search" type="submit" class="btn_search"
                                        title="검색"></button>
                                </div>

                        </form>
                </div>
                <thead>
                    <tr>
                        <th scope="col">구분</th>
                        <th scope="col">경기종목 코드</th>
                        <th scope="col">경기종목 이름</th>
                        <th scope="col">경기종목 이름(한글)</th>
                        <?php
                        if (authCheck($db, "authSchedulesDelete")) {  ?>
                        <th scope="col">삭제</th>
                        <?php
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = $total_count - $page_list_count;
                    $j = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<tr>';
                        echo "<td scope='col'>" . $i . "</td>";
                        echo "<td scope='col'>" . htmlspecialchars($row["sports_code"]) . "</td>";
                        echo "<td scope='col'>" . htmlspecialchars($row["sports_name"]) . "</td>";
                        echo "<td scope='col'>" . htmlspecialchars($row["sports_name_kr"]) . "</td>";
                        if (authCheck($db, "authSchedulesDelete")) {
                            echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["sports_code"] . "','sports')" . " value='삭제' class='btn_delete'></td>";
                        }
                        echo "</tr>";
                        $i--;
                        $j++;

                        if (isset($_POST["sports_delete"])) {
                            $sql = "DELETE FROM list_sports WHERE sports_code=?";
                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("s", $_POST["sports_delete"]);
                            $stmt->execute();
                            logInsert($db, $_SESSION['Id'], '경기 삭제', $_POST["sports_delete"]);
                            echo "<script>location.href='./sportmanagement.php';</script>";
                        }
                    }    ?>


                </tbody>
                </table>
                <div class="selectArea float_r">
                    <?php
                    if (authCheck($db, "authSchedulesCreate")) { ?>
                    <div class="btn_base base_mar col_right">
                        <input class="btn_add btn_txt bold" type="button"
                            onclick="createPopupWin('sport_sport_input.php','창 이름',900,512)" value="등록"
                            class="btn_view">
                    </div>
                    <?php } ?>
                    <colgroup>
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                    </colgroup>
                </div>
            </div>
        </div>

        <div class="page_wrap">
            <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>

        </div>
    </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>