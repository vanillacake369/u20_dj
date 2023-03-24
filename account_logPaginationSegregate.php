<?php
// 접근 권한 체크 함수
require_once "backheader.php";
// 읽기 권한 시에만 접근 가능
if (!authCheck($db, "authAccountsRead")) {
    exit("<script>
            alert('읽기 권한이 없습니다.');
            history.back();
        </script>");
}

require_once "auth/config.php";
require_once "action/module/pagination.php";

//auth_Check($db,'authAccountsRead'); //권한 확인

// uri 순서
// ...?pagesize=#&page=#&search=#&order=#&sc=asc
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

$page_size = 10;
$page_list_size = 10;
$link = "";

$tableName = "list_log";
$columnStartsWith = "log_";
$id = $columnStartsWith . "id";

$sql_where = " WHERE $id > 0";
$sql_order = " ORDER BY $id DESC ";
$sql_like = " AND (log_account LIKE ? OR log_name LIKE ? OR log_activity LIKE ?) ";

// page 내 row 에 따른 "page 번호";
$page_list_count = ($pageValue - 1) * $page_size;

// 검색 : search
if (isset($searchValue)) {
    $param = trim("%{$searchValue}%");
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    $link = addToLink($link, "&search_user=", $searchValue);
}

// 정렬 기능 선택한 경우
// 선택한 카테고리에 따른 컬럼 생성,desc/asc 결정 -> sql문 작성
if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

$sql = "SELECT * FROM $tableName $sql_where";

// 검색 O : param_bind
// 검색 X : query
if (isset($searchValue)) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $count = $stmt->get_result();
    print_r($sql);
    echo "<br>";

    $sql .= " $sql_order LIMIT $page_list_count, $page_size";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
    print_r($sql);
    echo "<br>";
} else {
    $count = $db->query($sql);

    $sql .= " $sql_order LIMIT $page_list_count, $page_size";
    $result = $db->query($sql);
}
print_r($sql);
echo "<br>";

// 조회된 모든 row의 수 : total_count
$total_count = mysqli_num_rows($count);
print_r($total_count);
echo "<br>";
?>
<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css" />
</head>

<body>
    <!-- header -->
    <?php include __DIR__ . '/header.php' ?>

    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="something contents ptop--40">
            <div class="mypage_100">
                <h3>로그 목록</h3>
                <div class="mypage_notice">
                </div>
            </div>

            <div class="table_wrap">
                <div class="btn_base base_mar">
                    <input type="button" onclick="" class="btn_excel bold" value="엑셀 출력">
                </div>
                <form action="" enctype="multipart/form-data" class="searchForm" name="judge_searchForm" method="get" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end;">
                    <div class="selectArea float_r">

                        <div class="search" style="width: 260px; ">
                            <input type="text" id="search_user" class="word" name="search_user" placeholder="검색어를 입력해주세요" maxlength="30" style="width: 260px;
                            height: 40px;
                            padding-left: 20px;
                            font-size: var(--font-small);">
                            <button name="search" value="search" type="submit" class="btn_search" title="검색"></a>
                        </div>
                    </div>
                </form>
            </div>

            <div>
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                    <colgroup>
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 10%" />
                        <col style="width: 30%" />
                        <col style="width: 15%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead>
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
                    <tbody>
                        <?php
                        $i = $total_count - $page_list_count;
                        // $j = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo "<td>" . $i . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_account"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_division"] == 'a' ? '관리자' : '심판') . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_activity"]) . ($row["log_sub_activity"] ? '(' . htmlspecialchars($row["log_sub_activity"]) . ')' : '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_ip"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["log_datetime"]) . "</td>";
                            echo '</tr>';
                            $i--;
                            // $j++;
                        }    ?>

                    </tbody>
                </table>
            </div>

            <div class="page_wrap">
                <div class="page_nation">
                    <?= Get_Pagenation($page_list_size, $page_size, $pageValue, $total_count, $link) ?>
                    <?php
                    // Get_Pagenation($page_list_size, $page_size, $page, $total_count, '')
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include __DIR__ . '/footer.php'; ?>

    <script src="js/main.js"></script>
</body>

</html>