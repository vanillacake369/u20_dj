<?
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "action/module/pagination.php";
    // 접근 권한 체크 함수
    require_once "backheader.php";
    // 읽기 권한 시에만 접근 가능
    if (!authCheck($db, "authAccountsRead")) {
        exit("<script>
            alert('읽기 권한이 없습니다.');
            history.back();
        </script>");
    }
    //auth_Check($db,'authAccountsRead');

    $searchValue = getSearchValue($_GET["search_user"] ?? NULL);
    $pageValue = getPageValue($_GET["page"] ?? NULL);
    $categoryValue = getCategoryValue($_GET["order"] ?? NULL);
    $orderValue = getOrderValue($_GET["sc"] ?? NULL);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

    $page_list_size = 10;
    $link = "";

    $tableName = "list_admin";
    $columnStartsWith = "admin_";
    $id = $columnStartsWith . "id";

    $sql_where = " WHERE $id > 0";
    $sql_order = " ORDER BY $id DESC ";
    $sql_like =  " AND (admin_account LIKE ? OR admin_name LIKE ?) ";

    $page_list_count = ($pageValue - 1) * $pagesizeValue;
    $param = null;

    if (isset($searchValue)) {
        $param = trim("%{$searchValue}%");
        $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
        $link = addToLink($link, "&search_user=", $searchValue);
    }

    if (isset($pagesizeValue)) {
        $link = addToLink($link, "&page_size=", $pagesizeValue);
    }

    if (isset($categoryValue) && isset($orderValue)) {
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }

    $sql = "SELECT admin_id,admin_account,admin_name,admin_level FROM $tableName $sql_where";
    $excel = $sql . $sql_order;

    if (isset($searchValue)) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $count = $stmt->get_result();

        $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $count = $db->query($sql);

        $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $result = $db->query($sql);
    }
    $total_count = mysqli_num_rows($count);
    ?>
<!--Data Tables-->
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
</head>

<body>
    <!-- header -->
    <? require_once 'header.php' ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>계정 목록<i class="xi-key key"></i></h1>
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
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="search">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <input type="text" id="search_user" class="defaultSearchInput" name="search_user"
                                    placeholder="검색어를 입력해주세요" maxlength="30"
                                    value="<?php echo isset($searchValue) ? $searchValue : ''; ?>">
                                <button name="search" value="search" class="defaultSearchBth" type="submit"
                                    title="검색"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="55%">
                        <?php if (authCheck($db, "authAccountsUpdate")) {  ?>
                        <col style="width: 10%" />
                        <?php }
                        if (authCheck($db, "authAccountsDelete")) { ?>
                        <col style="width: 10%" />
                        <?php } ?>
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th>순번</th>
                            <th>아이디</th>
                            <th>이름</th>
                            <th>권한</th>
                            <?php if (authCheck($db, "authAccountsUpdate")) {  ?>
                            <th>권한변경</th>
                            <?php }
                            if (authCheck($db, "authAccountsDelete")) { ?>
                            <th>삭제</th>
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
                            echo '<tr';
                            if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                            // 순번
                            echo "<td>" . $i . "</td>";
                            // 아이디
                            echo "<td>" . htmlspecialchars($row["admin_account"]) . "</td>";
                            // 이름
                            echo "<td>" . htmlspecialchars($row["admin_name"]) . "</td>";
                            // 권한
                            echo "<td>";
                            echo "<table class='table_UserID'>";
                            echo "<tr>";
                            echo    "<td>참가자관리</td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authEntrysRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ';
                            echo    " disabled><span>읽기</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authEntrysUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ';
                            echo    " disabled><span>수정</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authEntrysDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ';
                            echo    " disabled><span>삭제</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authEntrysCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>등록</span></td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo    "<td>경기관리</td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authSchedulesRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>읽기</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authSchedulesUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>수정</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authSchedulesDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>삭제</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authSchedulesCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>등록</span></td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo    "<td>기록관리</td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authRecordsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>읽기</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authRecordsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>수정</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authRecordsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>삭제</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authRecordsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>등록</span></td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo    "<td>통계관리</td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authStaticsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>읽기</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authStaticsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>수정</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authStaticsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>삭제</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authStaticsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>등록</span></td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo    "<td>계정관리</td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authAccountsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>읽기</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authAccountsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>수정</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authAccountsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>삭제</span></td>";
                            echo    "<td><input type='checkbox'";
                            echo    in_array('authAccountsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked';
                            echo    " disabled><span>등록</span></td>";
                            echo "</tr>";
                            echo "</table>";

                            echo "</td>";

                            if (authCheck($db, "authAccountsUpdate")) {
                                echo  "<td><input type='button' onclick=location.href='./account_change_auth.php?id=" . $row["admin_account"] . "' value='수정' class='BTN_Blue defaultBtn'></td>";
                            }
                            if (authCheck($db, "authAccountsDelete")) {
                                echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["admin_account"] . "','admin')" . " value='삭제' class='BTN_Red defaultBtn'></td>";
                            }
                            echo '</tr>';

                            $i--;
                            $j++;
                        }
                        ?>
                        <?php
                        if (authCheck($db, "authAccountsDelete")) {
                            if (isset($_POST["admin_delete"])) {
                                $sql = "DELETE FROM list_admin WHERE admin_account=?";
                                $stmt = $db->prepare($sql);
                                $stmt->bind_param("s", $_POST["admin_delete"]);
                                $stmt->execute();
                                logInsert($db, $_SESSION['Id'], '계정 삭제', $_POST["admin_delete"]);
                                echo "<script>location.href='./account_user.php';</script>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <!-- SQL 쿼리문을 보내는 코드 -->
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                            <!-- bind_param 함수 사용 시 post 되는 코드 (bind_param 되는 값을 배열로 저장 시 사용하는 코드) -->

                            <!-- 엑셀 컬럼을 선택하는 코드 -->
                            <?php
                                if ($param != null) {
                                    $param = array_fill(0, 2, $param);
                                    echo '<input type="text" name="keyword" value=' . implode(',', $param) . ' hidden />';
                                }
                            ?>
                            <input type="text" name="role" value="account_user" hidden />
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