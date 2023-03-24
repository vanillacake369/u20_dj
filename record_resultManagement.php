<?php
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
$pageValue = getPageValue($_GET["page"] ?? null);
$categoryValue = getCategoryValue($_GET["order"] ?? null);
$orderValue = getOrderValue($_GET["sc"] ?? null);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? null);
$page_list_size = 10;
$link = "";
// 사용될 DB 테이블 변수 *****
$tableName = "list_schedule";
$columnStartsWith = "";
$id = $columnStartsWith . "id";
// 사용될 SQL 조건문 *****
$sql = "SELECT record_end,
            sports_category,
            schedule_name,
            schedule_round,
            athlete_name,
            judge_name,
            record_official_record,
            record_live_record,
            schedule_sports,
            record_status
            FROM list_schedule
            INNER JOIN list_record ON record_sports=schedule_sports and record_gender=schedule_gender and record_round=schedule_round and record_state='y'
            INNER JOIN list_athlete ON record_athlete_id = athlete_id
            INNER JOIN list_judge ON record_judge = judge_id
            INNER JOIN list_sports ON schedule_sports=sports_code ";
$sql_where = "";
$sql_order = "ORDER BY record_end desc";
$sql_like = "";

// page 내 row 에 따른 "page 번호";
$page_list_count = ($pageValue - 1) * $pagesizeValue;
//+) $page_size->$pagesizeValue

// pageSizeOption : 한 페이지 내의 행 수
$pageSizeOption = [];
    array_push($pageSizeOption, 10);
    array_push($pageSizeOption, 15);
    array_push($pageSizeOption, 20);
    array_push($pageSizeOption, 100);
    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? NULL);

// GET METHOD로 넘어온 값을 가져옴
$searchValue = [];
$searchValue["search"] = getSearchValue($_GET["search"] ?? null);
$searchValue["schedule_date"] = getSearchValue($_GET["schedule_date"] ?? null);
$searchValue["schedule_gender"] = getSearchValue($_GET["schedule_gender"] ?? null);
$searchValue["sports_category"] = getSearchValue($_GET["sports_category"] ?? null);
$searchValue["schedule_sports"] = getSearchValue($_GET["schedule_sports"] ?? null);

// 검색값이 있는지에 대한 검증
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
  // 검색 버튼 입력 여부
  $hasSearchedButton = hasSearchedValue($searchValue["search"]);
  // 기록
  $hasSearchedDate = hasSearchedValue($searchValue["schedule_date"]);
  // 성별
  $hasSearchedGender = hasSearchedValue($searchValue["schedule_gender"]);
  // 카테고리
  $hasSearchedCategory = hasSearchedValue($searchValue["sports_category"]);
  // 종목
  $hasSearchedSports = hasSearchedValue($searchValue["schedule_sports"]);
}
$athletics = ["WR", "UWR", "AR", "UAR", "CR"];
/**
 * 2. 검색값 입력 시,
 *      2.a bindarray,keyword array를 통해 WHERE절을 만든다.
 *
 * 조건 검색 컨트롤러
 * uri_array : URI String(key) => URI Value(value)
 * bindarray : 인덱스(key) => 검색 입력값(value)
 * keyword : 인덱스(key) => DB조건문(value)
 */
$uri_array = [];
$bindarray = [];
$keyword = [];
if ($hasSearched) {
  //검색 버튼 클릭 시
  if ($hasSearchedDate) {
    // 날짜
    $uri_array["schedule_date"] = $searchValue["schedule_date"];
    array_push($bindarray, "%" . $searchValue["schedule_date"] . "%");
    array_push($keyword, "record_end like ?");
  }
  if ($hasSearchedGender) {
    // 성별
    $uri_array["schedule_gender"] = $searchValue["schedule_gender"];
    array_push($bindarray, $_GET["schedule_gender"]);
    array_push($keyword, "schedule_gender=?");
  }
  if ($hasSearchedCategory) {
    // 카테고리
    $uri_array["sports_category"] = $searchValue["sports_category"];
    array_push($bindarray, $_GET["sports_category"]);
    array_push($keyword, "sports_category=?");
  }
  if ($hasSearchedSports) {
    // 종목
    $uri_array["schedule_sports"] = $searchValue["schedule_sports"];
    array_push($bindarray, $_GET["schedule_sports"]);
    array_push($keyword, "schedule_sports=?");
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
 * 선택 카테고리,정렬방법을 URI로 추가
 * ORDER BY절 생성
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
    $types = str_repeat("s", count($bindarray));
    $stmt->bind_param($types, ...$bindarray);
  }
  $stmt->execute();
  $count = $stmt->get_result();

  $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
  // +)$page_size->$pagesizeValue
  $stmt = $db->prepare($sql_complete);
  if (count($bindarray) > 0) {
    $types = str_repeat("s", count($bindarray));
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
if ($count) {
  $total_count = mysqli_num_rows($count);
} else {
  $total_count = 0;
}
?>
<script type="text/javascript">
  function changetype() {
    if (window.confirm("30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?")) {
      location.href = './record_change_type.php'
    }
  }
</script>
</head>

<body>
  <?php require_once "header.php"; ?>
  <div class="Area">
    <div class="Wrapper TopWrapper">
      <div class="MainRank coachList defaultList">
        <div class="MainRank_tit">
          <h1>경기결과 목록<i class="xi-timer-o timer"></i></h1>
        </div>
        <div class="searchArea">
          <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
            <div class="page_size">
              <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                <option value="non" hidden="">페이지</option>
                <?php
                                    $get_schedule_count_sql = "SELECT COUNT(*) AS schedule_count FROM list_schedule;";
                                    $schedule_count_result = $db->query($get_schedule_count_sql);
                                    $schedule_count_row = mysqli_fetch_array($schedule_count_result);
                                    $size_of_all = $schedule_count_row["schedule_count"];
                                    foreach ($pageSizeOption as $size) {
                                        echo '<option value="' . $size . '"' . ($isPageSizeChecked[$size] ?? NULL) . ">" . $size . "개씩</option>\"";
                                    }
                                    echo '<option value="' . $size_of_all . '"' . ($isPageSizeChecked[$size_of_all] ?? NULL) . ">모두</option>\"";
                                ?>   
              </select>
            </div>
            <div class="selectArea defaultSelectArea">
              <div class="defaultSelectBox">
                <select title="날짜" name="schedule_date">
                  <option value="non">날짜</option>
                  <?php
                  $dSql = "SELECT DISTINCT (SUBSTRING_INDEX(record_end, ' ', 1)) AS a FROM list_record WHERE record_end is not null ORDER BY a ASC;";
                  $dResult = $db->query($dSql);
                  while ($dRow = mysqli_fetch_array($dResult)) {
                    echo "<option value=" . $dRow['a'] . ' ' . ($searchValue["schedule_date"] == $dRow['a'] ? 'selected' : '') . ">" . $dRow['a'] . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="defaultSelectBox">
                <select title="성별" name="schedule_gender">
                  <option value="non">성별</option>
                  <?php
                  $sSql = "SELECT distinct schedule_gender FROM list_schedule;";
                  $sResult = $db->query($sSql);
                  while ($sRow = mysqli_fetch_array($sResult)) {
                    echo "<option value=" . $sRow['schedule_gender'] . ' ' . ($searchValue["schedule_gender"] == $sRow['schedule_gender'] ? 'selected' : '') . ">" . ($sRow['schedule_gender'] == 'm' ? '남' : ($sRow['schedule_gender'] == 'f' ? '여' : '혼성')) . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="defaultSelectBox">
                <select title="경기 구분" name="sports_category">
                  <option value="non" hidden>경기 구분</option>
                  <?php
                  $events = array_unique($categoryOfSports_dic);
                  foreach ($events as $e) {
                    echo "<option value=$e " . ($e === $searchValue["sports_category"] ? 'selected' : '') . ">" . $e . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="defaultSelectBox">
                <select title="참가 경기" name="schedule_sports">
                  <option value='non' hidden="">참가 경기</option>
                  <option value="non">전체</option>
                  <?php
                  $events = array_unique($categoryOfSports_dic);
                  foreach ($events as $e) {
                    echo "<optgroup label=\"$e\">";
                    $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                    foreach ($sportsOfTheEvent as $s) {
                      $get_method_schedule_sports = ($_GET['schedule_sports'] ?? NULL);
                      echo "<option value=$s " . ($s === $get_method_schedule_sports ? 'selected' : '') . ">" . $sport_dic[$s] . "</option>";
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
          <colgroup>
            <col style="width:13%;">
            <col style="width:10%;">
            <col style="width:10%;">
            <col style="width:7%;">
            <col style="width:18%;">
            <col style="width:10%;">
            <col style="width:8%;">
            <col style="width:7%;">
            <col style="width:10%;">
            <!-- <col style="width: auto;"> -->
          </colgroup>
          <thead class="table_head entry_table">
            <tr>
              <th><a href="<?= Get_Sort_Link("schedule_date", $pageValue, $link, $orderValue) ?>">날짜</th>
              <th><a href="<?= Get_Sort_Link("sports_category", $pageValue, $link, $orderValue) ?>">구분</th>
              <th><a href="<?= Get_Sort_Link("schedule_name", $pageValue, $link, $orderValue) ?>">경기 이름</th>
              <th><a href="<?= Get_Sort_Link("schedule_round", $pageValue, $link, $orderValue) ?>">경기 라운드</th>
              <th><a href="<?= Get_Sort_Link("athlete_name", $pageValue, $link, $orderValue) ?>">선수 이름</th>
              <th><a href="<?= Get_Sort_Link("judge_name", $pageValue, $link, $orderValue) ?>">심판 이름</th>
              <th><a href="<?= Get_Sort_Link("record_live_record", $pageValue, $link, $orderValue) ?>">기록</th>
              <th><a href="<?= Get_Sort_Link("record_status", $pageValue, $link, $orderValue) ?>">기록 방식</th>
              <th><a href="<?= Get_Sort_Link("schedule_status", $pageValue, $link, $orderValue) ?>">기록 상태</th>
            </tr>
          </thead>
          <tbody class="table_tbody entry_table">
          <?php
            $num = 0;
            while ($result != null && ($row = mysqli_fetch_array($result))) {
              $num++;
              echo "<tr";
              if ($num % 2 == 0) echo ' class="Ranklist_Background">';
              else echo ">";
              // 날짜
              echo "<td>" . htmlspecialchars($row["record_end"]) . "</td>";
              // 구분
              echo "<td>" . htmlspecialchars($row["sports_category"]) . "</td>";
              // 경기 이름
              echo "<td>" . htmlspecialchars($row["schedule_name"]) . "</td>";
              // 경기 라운드
              echo "<td>" . htmlspecialchars($row["schedule_round"]) . "</td>";
              // 선수 이름
              echo "<td>" . htmlspecialchars($row["athlete_name"]) . "</td>";
              // 심판 이름
              echo "<td>" . htmlspecialchars($row["judge_name"]) . "</td>";
              if ($row["record_status"] === "l") {
                // 기록
                echo "<td>" . htmlspecialchars($row["record_live_record"]) . "</td>";
                // 기록 상태
                echo "<td>Live Result</td>";
                // 수정 버튼
                if (authCheck($db, "authRecordsUpdate")) {
                  if ($row["sports_category"] === "트랙경기") {
                    if ($row["schedule_sports"] === "4x100mR" || $row["schedule_sports"] === "4x400mR") {
                      echo "<td>";
                      echo '<input type="button" onclick="window.open';
                      echo "('./record/track_relay_result_view.php?id=" .
                        $row["record_schedule_id"] .
                        "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                      echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                    } else {
                      echo "<td>";
                      echo '<input type="button" onclick="window.open';
                      echo "('./record/track_normal_result_view.php?id=" .
                        $row["record_schedule_id"] .
                        "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                      echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                    }
                  } elseif ($row["sports_category"] === "필드경기") {
                    if ($row["schedule_sports"] === "polevault" || $row["schedule_sports"] === "highjump") {
                      echo "<td>";
                      echo '<input type="button" onclick="window.open';
                      echo "('./record/field_vertical_result_view.php?id=" .
                        $row["record_schedule_id"] .
                        "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                      echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                    } elseif ($row["schedule_sports"] === "longjump" || $row["schedule_sports"] === "triplejump") {
                      echo "<td>";
                      echo '<input type="button" onclick="window.open';
                      echo "('./record/field_horizontal_result_view.php?id=" .
                        $row["record_schedule_id"] .
                        "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                      echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                    } else {
                      echo "<td>";
                      echo '<input type="button" onclick="window.open';
                      echo "('./record/field_normal_result_view.php?id=" .
                        $row["record_schedule_id"] .
                        "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                      echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                    }
                  } else {
                    //혼합경기
                    switch ($row["schedule_round"]) {
                        //세부 종목별 분류
                      case "100m":
                      case "100mH":
                      case "110mH":
                      case "200m":
                      case "400m":
                      case "800m":
                      case "1500m":
                        echo "<td>";
                        echo '<input type="button" onclick="window.open';
                        echo "('./record/track_normal_result_view.php?id=" .
                          $row["record_schedule_id"] .
                          "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                        echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                        break;
                      case "discusthrow":
                      case "javelinthrow":
                      case "shotput":
                        echo "<td>";
                        echo '<input type="button" onclick="window.open';
                        echo "('./record/field_normal_result_view.php?id=" .
                          $row["record_schedule_id"] .
                          "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                        echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                        break;
                      case "polevault":
                      case "highjump":
                        echo "<td>";
                        echo '<input type="button" onclick="window.open';
                        echo "('./record/field_vertical_result_view.php?id=" .
                          $row["record_schedule_id"] .
                          "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                        echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                        break;
                      case "longjump":
                        echo "<td>";
                        echo '<input type="button" onclick="window.open';
                        echo "('./record/field_horizontal_result_view.php?id=" .
                          $row["record_schedule_id"] .
                          "','수정','width=1280,height=720,location=no,status=no,scrollbars=yes')";
                        echo '"value="수정" class="defaultBtn BTN_Blue"></td>';
                        break;
                    }
                  }
                }
              } elseif ($row["record_status"] === "o") {
                // 기록
                echo "<td>" . htmlspecialchars($row["record_official_record"]) . "</td>";
                // 기록 상태
                echo "<td>Official Result</td>";
                // 수정버튼
                echo "<td></td>";
              }
              echo "</tr>";
            } ?>
          </tbody>
        </table>
        <div class="playerRegistrationBtnArea">
          <div class="ExcelBtn IDBtn">
            <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
              <input type="submit" name="query" id="execute_excel" value="<?php echo $sql . $sql_order; ?>" hidden />
              <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
              <input type="text" name="role" value="result_management" hidden />
              <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀 출력</label>
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