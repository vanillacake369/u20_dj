<?php
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
include_once(__DIR__ . "/../../includes/auth/config.php");
// 국가,종목,지역,직무에 대한 매핑구조
include_once(__DIR__ . "/dictionary.php");
// 페이징 기능
include_once(__DIR__ . "/pagination.php");
// 외부 공격 방지 기능
include_once(__DIR__ . "/../../security/input_filtering.php");

/**
 * @vanillacake369
 * 1. DB 테이블 변수와 SQL문 변수를 자신의 조건과 테이블에 맞게 수정한다. (프리세팅)
 * 2. 검색값 입력 시, 
 *      2.a bindarray,keyword array를 통해 LIKE절을 만든다. bind:"
 *      2.b WHERE절에 LIKE절을 붙이고, URL 변수에 URI로 추가한다.
 * 3. 페이징 입력 시,
 *      3.a 입력한 페이지 쪽수를 URL 변수에 URI로 추가
 * 4. 정렬할 카테고리 & 정렬 순서 입력 시,
 *      4.a 선택한 카테고리에 따른 ORDER BY 절을 만든다.
 *      4.b 카테고리와 정렬순서값을 URL 변수에 URI로 추가한다.
 * 5. SQL문에 WHERE절을 붙인다.
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
 * 7. 이전 옵션값을 유지하기 위해 is~값을 toggle한다.
 */

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

// GET METHOD로 넘어온 값을 가져옴
$searchValue = [];
$searchValue["athlete_country"] = getSearchValue($_GET["athlete_country"] ?? NULL);
$searchValue["athlete_region"] = getSearchValue($_GET["athlete_region"] ?? NULL);
$searchValue["athlete_division"] = getSearchValue($_GET["athlete_division"] ?? NULL);
$searchValue["athlete_gender"] = getSearchValue($_GET["athlete_gender"] ?? NULL);
$searchValue["athlete_schedule"] = getSearchValue($_GET["athlete_schedule"] ?? NULL);
$searchValue["athlete_name"] = getSearchValue($_GET["athlete_name"] ?? NULL);

// 검색값이 있는지에 대한 검증
$hasSearched = hasSearchedValue($searchValue);
if ($hasSearched) {
    $hasSearchedCountry = hasSearchedValue($searchValue["athlete_country"]);
    $hasSearchedRegion = hasSearchedValue($searchValue["athlete_region"]);
    $hasSearchedDivision = hasSearchedValue($searchValue["athlete_division"]);
    $hasSearchedGender = hasSearchedValue($searchValue["athlete_gender"]);
    $hasSearchedSports = hasSearchedValue($searchValue["athlete_schedule"]);
    $hasSearchedName = hasSearchedValue($searchValue["athlete_name"]);
}
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
//+) pagesizeValue 추가

if ($hasSearched) {
    // 버퍼 오버플로우 방지
    $hasSearchedCountry = cleanHex($hasSearchedCountry);
    $hasSearchedRegion = cleanHex($hasSearchedRegion);
    $hasSearchedDivision = cleanHex($hasSearchedDivision);
    $hasSearchedGender = cleanHex($hasSearchedGender);
    $hasSearchedSports = cleanHex($hasSearchedSports);
    $hasSearchedName = cleanHex($hasSearchedName);

    // XSS 방지
    /**
     * @var string $hasSearchedCountry
     * @var string $hasSearchedRegion
     * @var string $hasSearchedDivision
     * @var string $hasSearchedGender
     * @var string $hasSearchedSports
     * @var string $hasSearchedName
     * @var string $pageValue
     * @var string $categoryValue
     * @var string $orderValue
     * @var string $pagesizeValue
     */
    $hasSearchedCountry = htmlspecialchars($hasSearchedCountry, ENT_QUOTES);
    $hasSearchedRegion = htmlspecialchars($hasSearchedRegion, ENT_QUOTES);
    $hasSearchedDivision = htmlspecialchars($hasSearchedDivision, ENT_QUOTES);
    $hasSearchedGender = htmlspecialchars($hasSearchedGender, ENT_QUOTES);
    $hasSearchedSports = htmlspecialchars($hasSearchedSports, ENT_QUOTES);
    $hasSearchedName = htmlspecialchars($hasSearchedName, ENT_QUOTES);
}

$page_list_size = 10;
$link = "";

$tableName = "list_athlete";
$columnStartsWith = "athlete_";
$id = $columnStartsWith . "id";

$sql_where = " WHERE $id > 0";
$sql_order = " ORDER BY $id ASC ";
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

$sql = "SELECT *
        FROM list_athlete
        INNER JOIN list_country  
        ON athlete_country=country_code";
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
if ($hasSearched) {
    if ($hasSearchedCountry) {
        $uri_array["athlete_country"] = $searchValue["athlete_country"];
        array_push($bindarray, $searchValue["athlete_country"]);
        array_push($keyword, "athlete_country=?");
    }
    if ($hasSearchedRegion) {
        $uri_array["athlete_region"] = $searchValue["athlete_region"];
        array_push($bindarray, $searchValue["athlete_region"]);
        array_push($keyword, "athlete_region=?");
    }
    if ($hasSearchedDivision) {
        $uri_array["athlete_division"] = $searchValue["athlete_division"];
        array_push($bindarray, $searchValue["athlete_division"]);
        array_push($keyword, "athlete_division=?");
    }
    if ($hasSearchedGender) {
        $uri_array["athlete_gender"] = $searchValue["athlete_gender"];
        array_push($bindarray, $searchValue["athlete_gender"]);
        array_push($keyword, "athlete_gender=?");
    }
    if ($hasSearchedSports) {
        $uri_array["athlete_schedule"] = $searchValue["athlete_schedule"];
        $isAttendingSports = "'\\\\b" . $searchValue["athlete_schedule"] . "\\\\b'";
        $isAttendingSports = "athlete_schedule REGEXP " . str_replace(' ', '', $isAttendingSports);
        array_push($keyword, $isAttendingSports);
    }
    if ($hasSearchedName) {
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
/**
 * 7. 이전 옵션값을 유지하기 위해 is~값을 toggle한다.
 * 
 * 이전 옵션 선택값을 유지
 * is~ : 이전 옵션 선택값 저장하는 array
 * maintainSelected : GET METHOD로 넘어온 이전 선택값(key) => ' selected'(value) 형태의 array 반환
 */
$isPageSizeChecked = maintainSelected($_GET["page_size"] ?? NULL);
$isCountrySelected = maintainSelected($_GET["athlete_country"] ?? NULL);
$isRegionSelected = maintainSelected($_GET["athlete_region"] ?? NULL);
$isDivisionSelected = maintainSelected($_GET["athlete_division"] ?? NULL);
$isGenderSelected = maintainSelected($_GET["athlete_gender"] ?? NULL);
$isSportsSelected = maintainSelected($_GET["athlete_schedule"] ?? NULL);
// $isAttendanceSelected = maintainSelected($_GET[""]??NULL);