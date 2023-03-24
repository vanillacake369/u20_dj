<?php
// 로그 기능
require_once "backheader.php";
// 읽기 권한 접근 제어
if (!authCheck($db, "authRecordsRead")) {
    exit("<script>
        alert('읽기 권한이 없습니다.');
        history.back();
    </script>");
}
require_once "head.php";
// DB 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 페이징 기능
require_once "action/module/pagination.php";
// 외부 공격 방지 기능
require_once "security/input_filtering.php";
/**
 * 1. DB 테이블 변수와 SQL문 변수를 자신의 조건과 테이블에 맞게 수정한다.
 * ***** @todo 사용자 입력 관련 변수 *****
 * @param mixed $searchValue : 사용자가 검색한 값
 * @param mixed $pageValue : 사용자가 클릭한 페이지 쪽수
 * @param mixed $categoryValue : 사용자가 정렬하고자한 카테고리
 * @param mixed $orderValue : 사용자가 선택한 오름/내림 차순
 * 
 * ***** @todo 페이징 관련 변수 *****
 * @param mixed $pagesizeValue : 한 페이지 당 row 개수
 * @param mixed $page_list_size : 선택 가능한 페이징 쪽수
 * @param mixed $link : URL
 * 
 * ***** @todo 사용될 DB 테이블 변수 *****
 * @param mixed $tableName : 테이블 이름
 * @param mixed $columnStartsWith : 테이블 내 컬럼의 시작 문자열
 * @param mixed $id : 테이블 내 id컬럼명
 * 
 * ***** @todo 검색 및 정렬에 사용될 SQL문 조건문 변수 *****
 * @param mixed $sql_where : where 절
 * @param mixed $sql_order : where 절 다음 order 절
 * @param mixed $sql_like : 검색값이 있을 때 sql like문
 */
// 페이징 관련 변수 *****
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
$page_list_size = 10;
$link = "";
// 사용될 DB 테이블 변수 *****
$tableName = "list_worldrecord";
$columnStartsWith = "worldrecord_";
$id = $columnStartsWith . "id";
// 사용될 SQL 조건문 *****
$sql = "SELECT 
                    worldrecord_athletics, 
                    worldrecord_athlete_name, 
                    worldrecord_gender, 
                    worldrecord_location, 
                    worldrecord_sports,
                    worldrecord_wind, 
                    worldrecord_record,
                    worldrecord_datetime, 
                    worldrecord_country_code
                    FROM list_worldrecord";
$sql_where = " where worldrecord_id>=1";
$sql_order = "";
$sql_like = "";

// page 내 row 에 따른 "page 번호";
$page_list_count = ($pageValue - 1) * $pagesizeValue;
//+) $page_size->$pagesizeValue

// GET METHOD로 넘어온 값을 가져옴
$searchValue = [];
$searchValue["search"] = getSearchValue($_GET["search"] ?? NULL);
$searchValue["worldrecord_athletics"] = getSearchValue($_GET["worldrecord_athletics"] ?? NULL);
$searchValue["worldrecord_gender"] = getSearchValue($_GET["worldrecord_gender"] ?? NULL);
$searchValue["worldrecord_sports"] = getSearchValue($_GET["worldrecord_sports"] ?? NULL);

// 검색값이 있는지에 대한 검증
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
    // 검색 버튼 입력 여부
    $hasSearchedButton = hasSearchedValue($searchValue["search"]);
    // 기록
    $hasSearchedAthletics = hasSearchedValue($searchValue["worldrecord_athletics"]);
    // 성별
    $hasSearchedGender = hasSearchedValue($searchValue["worldrecord_gender"]);
    // 종목
    $hasSearchedEvents = hasSearchedValue($searchValue["worldrecord_sports"]);
}
$athletics = array('세계신기록', '세계U20신기록', '아시아신기록', '아시아U20신기록', '대회신기록');
/**
 * 2. 검색값 입력 시, 
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
if ($hasSearched) { //검색 버튼 클릭 시
    if ($hasSearchedAthletics) { // 대회 선택
        $uri_array["worldrecord_athletics"] = $searchValue["worldrecord_athletics"];
        array_push($bindarray, $searchValue["worldrecord_athletics"]);
        array_push($keyword, "worldrecord_athletics=?");
    }
    if ($hasSearchedGender) { // 성ㅕㅂㄹ
        array_push($bindarray, $_GET["worldrecord_gender"]);
        array_push($keyword, "worldrecord_gender=?");
    }
    if ($hasSearchedEvents) { // 종목
        array_push($bindarray, $_GET["worldrecord_sports"]);
        array_push($keyword, "worldrecord_sports=?");
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
// order="country_id"
if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}
// 5. SQL문에 WHERE절을 붙인다.
$sql = $sql . $sql_where;

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
if ($hasSearched) {
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
?>
<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
</head>

<body>
    <?php require_once 'header.php'; ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>역대기록 목록<i class="xi-timer-o timer"></i></h1>
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
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="기록 구분" name="worldrecord_athletics">
                                    <option value="non">기록 선택</option>
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
                                        $get_method_worldrecord_athletics = ($_GET['worldrecord_athletics'] ?? NULL);
                                        echo "<option value=$s " . ($s === $get_method_worldrecord_athletics ? 'selected' : '') . ">$k</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="성별" name="worldrecord_gender">
                                    <option value="non">성별</option>
                                    <?php
                                    $sSql = "SELECT distinct schedule_gender FROM list_schedule;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['schedule_gender'] . ' ' . ($searchValue["worldrecord_gender"] == $sRow['schedule_gender'] ? 'selected' : '') . ">" . ($sRow['schedule_gender'] == 'm' ? '남' : ($sRow['schedule_gender'] == 'f' ? '여' : '혼성')) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="참가경기" name="worldrecord_sports">
                                    <option value='non' hidden="">참가경기</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $events = array_unique($categoryOfSports_dic);
                                    foreach ($events as $e) {
                                        echo "<optgroup label=\"$e\">";
                                        $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                        foreach ($sportsOfTheEvent as $s) {
                                            $get_method_worldrecord_sports = ($_GET['worldrecord_sports'] ?? NULL);
                                            echo "<option value=$s " . ($s === $get_method_worldrecord_sports ? 'selected' : '') . ">" . $sport_dic[$s] . "</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search">
                                <button class="SearchBtn" type="submit"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <!-- <colgroup>
                        <col width="10%">
                        <col width="20%">
                        <col width="10%">
                        <col width="15%">
                        <col width="12%">
                        <col width="8%">
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup> -->
                    <thead class="table_head entry_table">
                        <tr>
                            <th><a href="<?= Get_Sort_Link("athletics", $pageValue, $link, $orderValue) ?>">기록구분</th>
                            <th><a href="<?= Get_Sort_Link("athlete_name", $pageValue, $link, $orderValue) ?>">이름</th>
                            <th><a href="<?= Get_Sort_Link("gender", $pageValue, $link, $orderValue) ?>">성별</th>
                            <th><a href="<?= Get_Sort_Link("location", $pageValue, $link, $orderValue) ?>">장소</th>
                            <th><a href="<?= Get_Sort_Link("sports", $pageValue, $link, $orderValue) ?>">종목</th>
                            <th><a href="<?= Get_Sort_Link("wind", $pageValue, $link, $orderValue) ?>">풍속/용기구</th>
                            <th><a href="<?= Get_Sort_Link("record", $pageValue, $link, $orderValue) ?>">기록</th>
                            <th><a href="<?= Get_Sort_Link("datetime", $pageValue, $link, $orderValue) ?>">기록일자</th>
                            <th><a href="<?= Get_Sort_Link("country_code", $pageValue, $link, $orderValue) ?>">국가</th>
                            <?php
                            if (authCheck($db, "authRecordsUpdate")) {  ?>
                            <th colspan="2" scope="col">수정</th>
                            <?php }
                            if (authCheck($db, "authRecordsDelete")) {  ?>
                            <th colspan="2" scope="col">삭제</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        $num = 0;
                        while ($result != null && $row = mysqli_fetch_array($result)) {
                            $num++;
                            echo "<tr";
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo ">";
                            if ($row["worldrecord_athletics"] == "w") echo "<td>" . "세계신기록" . "</td>";
                            else if ($row["worldrecord_athletics"] == "u") echo "<td>" . "세계U20신기록" . "</td>";
                            else if ($row["worldrecord_athletics"] == "a") echo "<td>" . "아시아신기록" . "</td>";
                            else if ($row["worldrecord_athletics"] == "s") echo "<td>" . "아시아U20신기록" . "</td>";
                            else echo "<td>" . "대회신기록" . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_athlete_name"]) . "</td>";
                            if ($row['worldrecord_gender'] === 'c') {
                                echo "<td>혼성</td>";
                            } else if ($row['worldrecord_gender'] === 'm') {
                                echo "<td>남성</td>";
                            } else {
                                echo "<td>여성</td>";
                            }
                            echo "<td>" . htmlspecialchars($row["worldrecord_location"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_sports"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_wind"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_record"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_datetime"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["worldrecord_country_code"]) . "</td>";
                            echo "<td colspan='2' scope='col'>";
                            if (authCheck($db, "authRecordsUpdate")) {
                                echo '<button type=\'button\' onclick="createPopupWin(\'\',\'창 이름\',900,900)" class=\'BTN_Blue defaultBtn\'>수정</button>';
                            }
                            echo "</td>";
                            echo "<td colspan='2' scope='col'>";
                            if (authCheck($db, "authRecordsDelete")) {
                                echo "<button type='button' class='BTN_Red defaultBtn'>삭제</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel"
                                value="<?php echo $sql . $sql_order; ?>" hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="record_history" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                    </div>
                    <div class="registrationBtn">
                        <?php
                        if (authCheck($db, "authSchedulesCreate")) { ?>
                        <div class="btn_base base_mar col_right">
                            <button class="defaultBtn BIG_btn BTN_Blue" type="button"
                                onclick="createPopupWin('record_worldrecord_input.php','창 이름',900,900)">등록</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
    <script src="/assets/js/main_dh.js"></script>
</body>



</html>