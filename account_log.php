<?
    require_once "backheader.php";
    // 읽기 권한 시에만 접근 가능
    if (!authCheck($db, "authAccountsRead")) {
        exit("<script>
            alert('읽기 권한이 없습니다.');
            history.back();
        </script>");
    }
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "action/module/pagination.php";

    //auth_Check($db,'authAccountsRead'); //권한 확인

    // uri 순서
    // ...?pagesize=#&page=#&search=#&order=#&sc=asc
    // statics_newRe..?pagesize=#&page=#&search=#&order=#&sc=asc
    /**
     * @param mixed $searchValue : 사용자가 검색한 값
     * @param mixed $pageValue : 사용자가 클릭한 페이지 쪽수
     * @param mixed $categoryValue : 사용자가 정렬하고자한 카테고리
     * @param mixed $orderValue : 사용자가 선택한 오름/내림 차순
     * 
     * @param mixed $page_size : 한 페이지 당 row 개수
     * @param mixed $page_list_size : 선택 가능한 페이징 쪽수
     * @param mixed $link : URL
     * 
     * @param mixed $tableName : 테이블 이름
     * @param mixed $columnStartsWith : 테이블 내 컬럼의 시작 문자열
     * @param mixed $id : 테이블 내 id컬럼명
     * 
     * @param mixed $sql_where : where 절
     * @param mixed $sql_order : where 절 다음 order 절
     * @param mixed $sql_like : 검색값이 있을 때 sql like문
     */

    $searchValue = getSearchValue($_GET["search_user"] ?? NULL);
    $pageValue = getPageValue($_GET["page"] ?? NULL);
    $categoryValue = getCategoryValue($_GET["order"] ?? NULL);
    $orderValue = getOrderValue($_GET["sc"] ?? NULL);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
    //+) pagesizeValue 추가

    $page_list_size = 10;
    $link = "";

    $tableName = "list_log";
    $columnStartsWith = "log_";
    $id = $columnStartsWith . "id";

    $sql_where = " WHERE $id > 0";
    $sql_order = " ORDER BY $id DESC ";
    $sql_like = " AND (log_account LIKE ? OR log_name LIKE ? OR log_activity LIKE ?) ";

    // page 내 row 에 따른 "page 번호";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;
    //+) $page_size->$pagesizeValue
    $param = null;

    // 검색 : search
    if (isset($searchValue)) {
        $param = trim("%{$searchValue}%");
        $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
        $link = addToLink($link, "&search_user=", $searchValue);
    }

    // +) 페이지 사이즈
    if (isset($pagesizeValue)) {
        $link = addToLink($link, "&page_size=", $pagesizeValue);
    }

    // 정렬 기능 선택한 경우
    // 선택한 카테고리에 따른 컬럼 생성,desc/asc 결정 -> sql문 작성
    if (isset($categoryValue) && isset($orderValue)) {
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }


    $sql = "SELECT log_id, log_account, log_name, log_division, log_activity, log_sub_activity, log_ip, log_datetime FROM $tableName $sql_where";
    $excel = $sql . $sql_order;

    // 검색 O : param_bind 
    // 검색 X : query
    if (isset($searchValue)) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $param, $param, $param);
        $stmt->execute();
        $count = $stmt->get_result();

        $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
        // +)$page_size->$pagesizeValue
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $param, $param, $param);
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
    ?>
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
</head>

<body>
    <!-- header -->
    <? require_once 'header.php'; ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>로그 목록<i class="xi-key key"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" enctype="multipart/form-data" class="searchForm pageArea" name="judge_searchForm"
                        method="get">
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
                                <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                                <input type="text" id="search_user" class="defaultSearchInput" name="search_user"
                                    placeholder="검색어를 입력해주세요" maxlength="30"
                                    value="<?php echo isset($searchValue) ? $searchValue : ''; ?>">
                                <button name="search" value="search" class="defaultSearchBth" type="submit"><i
                                        class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 30%" />
                        <col style="width: 15%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th>순번</th>
                            <th>아이디</th>
                            <th>이름</th>
                            <th>계정</th>
                            <th>활동내역</th>
                            <th>IP</th>
                            <th><a href="<?= Get_Sort_Link("datetime", $pageValue, $link, $orderValue) ?>">시간</a></th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $i = $total_count - $page_list_count;
                        $num = 0;
                        //$j = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            echo '<tr';
                            if ($num%2 == 0) echo ' class="Ranklist_Background">'; else echo ">";
                            echo "<td>" . $i . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_account"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_division"] == 'a' ? '관리자' : '심판') . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_activity"]) . ($row["log_sub_activity"] ? '(' . htmlspecialchars($row["log_sub_activity"]) . ')' : '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_ip"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_datetime"]) . "</td>";
                            echo '</tr>';
                            $i--;
                            //$j++;
                        }    ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <!-- SQL 쿼리문을 보내는 코드 -->
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                            <!-- bind_param 함수 사용 시 post 되는 코드 (bind_param 되는 값을 배열로 저장 시 사용하는 코드) -->
                            <? if ($param != null) {
                                $param = array_fill(0, 3, $param);
                                echo '<input type="text" name="keyword" value="' . implode(',', $param) . '" hidden />';
                            }
                            ?>
                            <!-- 엑셀 컬럼을 선택하는 코드 -->
                            <input type="text" name="role" value="account_log" hidden />
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