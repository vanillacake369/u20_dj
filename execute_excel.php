<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
</style>
<?php
// TODO 엑셀 실행 시 오류 메시지 없앨 방법 찾기
/**
 * 1. db 연결 (dbconnect.php), 특수 코드 한글 번역 (dictionary.php) php include
 * 2. 엑셀로 다운로드하기위한 header 추가
 * 3. 인자로 쿼리문, 페이지 유형(코치 페이지, 심판 페이지, ...), bind_param 인자 받기 (bind_param 은 쿼리문에 따라 존재 유/무)
 * 4. 주어진 쿼리문과 bind_param 인자를 합쳐 db에서 데이터 가져오기 (만약 bind_param 인자가 없으면 bind_param 작업 X)
 * 5. <table> 태그를 이용하여 출력준비 (만약, 쿼리문의 결과 값이 없으면, 경고문 출력 후, 뒤로가기)
 * 5-1. 페이지 유형에 따라 엑셀 컬럼 설정
 * 5-2. 데이터 추가 (추가시, 엑셀 변환에 불필요한 데이터를 제외 후 추가)
 * 5-3. 데이터 엑셀로 변환
 */
# 1번
require_once __DIR__ . "/includes/auth/config.php";
require_once __DIR__ . "/action/module/dictionary.php";

# 3번
global $db;
$query = $_POST['query'];
$role = $_POST['role'];
$keyword = $_POST['keyword'] ?? null;
$order = $_POST['get'] ?? null;
$ranking = 1;
$latest_data = "";
$same_data_count = 0;

if ($keyword !== null) {
    // bind_param()을 할 parameter가 들어왔을 경우
    $keyword = explode(',', $keyword);
} else if ($order !== null) {
    // 통계관리 - 오름차순/내림차순을 위한 paramater가 들어왔을 경우
    $order = explode(',', $order);
}

# 4번
$stmt = $db->prepare($query);
if ($keyword !== null) {
    $types = str_repeat('s', count($keyword));
    $stmt->bind_param($types, ...$keyword);
}
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

# 5번
if (!empty($data)) {
    # 2번
    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename = excel_test.xls");     //filename = 저장되는 파일명을 설정합니다.
    header("Content-Description: PHP4 Generated Data");
    print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
    $text = executeExcel($data, $role);
    # 5-3번
    echo $text;
    mysqli_close($db);
} else {
    mysqli_close($db);
    echo '<script>alert("찾고자 하는 데이터가 없습니다.");</script>';
    echo '<script>history.back();</script>';
}
function string_contains($haystack, $needle)
{
    return $needle !== '' && mb_strpos($haystack, $needle) !== false;
}


/**
 * HTML 테이블을 엑셀로 변환하는 함수
 *****
 * 1. 해더를 설정한다. (ex. 코치이름, 코치국적,...)
 * 2. DB에서 불러온 데이터를 한줄 씩 추가한다.
 * 3. 엑셀로 변환하여 내보낸다.
 *****
 * @param array $data 테이블 데이터
 * @param string $role 참가자 유형
 * @return string
 */

function executeExcel(array $data, string $role)
{
    $html = '<table style="width:100%">';
    # 5-1번
    set_header($html, $role);
    $data = set_marking($role, $data);
    $data = sort_order($role, $data);
    foreach ($data as $datum) {
        # 5-2번
        add_table_row_data($html, $datum);
    }
    $html .= "</table>";
    return $html;
}


/**
 * 메달과 관련된 엑셀 출력 시 메달로 내림차순하는 함수
 * @param string $role 엑셀 출력 헤더
 * @param array $data 엑셀 출력 데이터
 * @return array
 */
function sort_order(string $role, array $data)
{
    global $order;
    if (in_array($role, ["player_rank_listing", "country_listing"]) && $order !== null) {
        // 통계관리 - 선수 순위 보기 or 국가별 순위 보기
        // 정렬을 선택했을 경우
        if (in_array("desc", $order)) {
            // 내림차순 index 2 => order 종류
            usort($data, function ($datum1, $datum2) {
                global $order;
                if ($datum1[$order[2]] < $datum2[$order[2]]) {
                    return 1;
                } elseif ($datum1[$order[2]] > $datum2[$order[2]]) {
                    return -1;
                } else {
                    // 메달 갯수가 같을 경우 이름 순으로 정렬
                    global $role;
                    if ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) < ucwords($datum2["athlete_name"])) {
                        // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                        return -1;
                    } elseif ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) > ucwords($datum2["athlete_name"])) {
                        // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                        return 1;
                    } else if ($role === "country_listing" && $datum1["country_name"] < $datum2["country_name"]) {
                        return -1;
                    } elseif ($role === "country_listing" && $datum1["country_name"] > $datum2["country_name"]) {
                        return 1;
                    }
                    return 0;
                }
            });
        } else if (in_array("asc", $order)) {
            // 오름차순 index 2 => order 종류
            usort($data, function ($datum1, $datum2) {
                global $order;
                if ($datum1[$order[2]] > $datum2[$order[2]]) {
                    return 1;
                } elseif ($datum1[$order[2]] < $datum2[$order[2]]) {
                    return -1;
                } else {
                    // 메달 갯수가 같을 경우 이름 순으로 정렬
                    global $role;
                    if ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) < ucwords($datum2["athlete_name"])) {
                        // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                        return -1;
                    } elseif ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) > ucwords($datum2["athlete_name"])) {
                        // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                        return 1;
                    } else if ($role === "country_listing" && $datum1["country_name"] < $datum2["country_name"]) {
                        return -1;
                    } elseif ($role === "country_listing" && $datum1["country_name"] > $datum2["country_name"]) {
                        return 1;
                    }
                    return 0;
                }
            });
        }
    } else if ($role === "new_record_listing") {
        // 통계관리 - 신기록 경기기록 정렬
        usort($data, function ($data1, $data2) {
            // sport_name 순으로 오름차순
            if ($data1["sports_name"] < $data2["sports_name"]) {
                return -1;
            } else if ($data1["sports_name"] > $data2["sports_name"]) {
                return 1;
            } else {
                // 동점일 시 record 순으로 오름차순
                $data1_record = intval(str_replace([":", ".", ",", ";"], "", $data1["worldrecord_record"]));
                $data2_record = intval(str_replace([":", ".", ",", ";"], "", $data2["worldrecord_record"]));
                if ($data1_record < $data2_record) {
                    return -1;
                } else if ($data1_record > $data2_record) {
                    return 1;
                }
                return 0;
            }
        });
    }
    return $data;
}

/**
 * 랭킹과 구분을 매기는 함수
 * @param string $role
 * @param array $data
 * @return array
 */
function set_marking(string $role, array $data)
{
    if (in_array($role, ["player_rank_listing", "country_listing"])) {
        // 순위 편성 (선수별 순위보기 or 국가별 순위보기일 경우 해당)
        global $ranking, $same_data_count;
        // 메달 갯수로 정렬
        usort($data, function ($datum1, $datum2) {
            if ($datum1["medal"] < $datum2["medal"]) {
                return 1;
            } elseif ($datum1["medal"] > $datum2["medal"]) {
                return -1;
            } else {
                // 메달 갯수가 같을 경우 이름 순으로 정렬
                global $role;
                if ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) < ucwords($datum2["athlete_name"])) {
                    // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                    return -1;
                } elseif ($role === "player_rank_listing" && ucwords($datum1["athlete_name"]) > ucwords($datum2["athlete_name"])) {
                    // 선수별 순위보기 메달이 같은 경우 이름순으로 정렬
                    return 1;
                } else if ($role === "country_listing" && $datum1["country_name"] < $datum2["country_name"]) {
                    return -1;
                } elseif ($role === "country_listing" && $datum1["country_name"] > $datum2["country_name"]) {
                    return 1;
                }
                return 0;
            }
        });
        // 갯수로 정렬한 순서대로 순위 부여
        check_add_column($role, $data[0]);
        $data[0]["ranking"] = $ranking;
        for ($i = 1; $i < count($data); $i++) {
            check_add_column($role, $data[$i]);
            if ($ranking == 0 || $data[$i - 1]["medal"] !== $data[$i]["medal"]) {
                // 동점이 아닐경우
                $value = $ranking + $same_data_count + 1;
                $ranking = $value;
                $same_data_count = 0;
            } else {
                // 동점일 경우
                $same_data_count += 1;
                $value = $ranking;
            }
            $data[$i]['ranking'] = $value;
        }
    }
    if ($role == "sport_management") {
        // 구분자(No) 추가 (경기 관리 - 경기 목록일 때 해당)
        for ($i = 0; $i < count($data); $i++) {
            check_add_column($role, $data[$i]);
            $data[$i]["number"] = $i + 1; // 1부터 시작 (1)
            // $data[$i]["number"] = count($data) - 1; // 경기 갯수 만큼 시작 (25)
        }
    }
    return $data;
}

/**
 * 엑셀에 들어갈 해더를 추가하는 함수
 * @param string $html 내보낼 엑셀 파일
 * @param string $role 참가자 유형
 * @return void
 */
function set_header(string &$html, string $role)
{
    switch ($role) {
        case 'athlete': # 참가자 관리 - 선수목록 (entry_athlete)
            $header = array("No", "Name", "Country", "Region", "Duty", "Gender", "Birth", "Age", "Schedule", "Attendance");
            break;
        case 'coach': # 참가자 관리 - 코치 목록 (entry_coach)
            $header = array("No", "Name", "Country", "Region", "Duty", "Division", "Gender", "Birth", "Age", "Schedule", "Attendance");
            break;
        case 'judge': # 참가자 관리 - 심판 목록 (entry_judge):
        case 'director': # 참가자 관리 - 임원 목록 (entry_director)
            $header = array("No", "Name", "Country", "Division", "Gender", "Birth", "Age", "Duty", "Schedule", "Attendance");
            break;
        case 'sport_management': # 경기 관리 - 경기 목록 (sportmanagement)
            $header = array("No", "Sports Code", "Sports Name");
            break;
        case 'country_management': # 경기 관리 - 국가 목록 (countrymanagement)
            $header = array("Country", "Country(KOR)", "Country Code");
            break;
        case 'schedule_management': # 경기 관리 - 일정 목록 (schedulemanagement)
            $header = array("No", "Category", "Sports", "Gender", "Round", "Location", "Start Time", "Status", "Date", "Result");
            break;
        case 'result_management': # 기록 관리 - 경기결과 목록 (reusultManagement)
            $header = array("Date", "Category", "Name", "Round", "Athlete Name", "Coach Name", "Record", "Result");
            break;
        case 'record_history': # 기록 관리 - 역대기록 목록 (recordHistory)
            $header = array("Category", "Name", "Gender", "Location", "Sports", "Wind/Equip", "Record", "Date", "Country");
            break;
        case 'player_rank_listing': # 통계 관리 - 선수별 순위보기 (playerRankingListing)
            $header = array("Rank", "Country", "Name", "Gold", "Silver", "Bronze");
            break;
        case 'new_record_listing': # 통계 관리 - 신기록 경기기록 (newRecordListing)
            $header = array("Category", "Sports", "Name", "Gender", "Wind/Equip", "Record", "Date", "Country");
            break;
        case 'schedule_rank_listing': # 통계 관리 - 경기별 순위보기 (scheduleRankingListing)
            $header = array("Rank", "Sports", "이름", "Gender", "Country", "결과", "풍속/용기구", "비고");
            break;
        case 'schedule_listing': # 통계 관리 - 경기별 메달보기 (scheduleListing)
            $header = array("Sports", "Gender", "Gold", "Gold Record", "Silver", "Silver Record", "Bronze", "Bronze Record");
            break;
        case 'country_listing': # 통계 관리 - 국가별 메달보기 (countryListing)
            $header = array("Rank", "Country", "Gold", "Silver", "Bronze", "Total");
            break;
        case 'account_log': # 로그 목록 페이지
            $header = array("No", "ID", "Name", "Account", "Active", "IP", "Time");
            break;
        case 'account_user': # 계정 목록 페이지
            $header = array("No", "ID", "Name", "Level");
            break;
        default: # 절대 일어나면 안되는 상황
            return;
    }
    $html .= "<tr>";
    foreach ($header as $value) {
        $html .= "<th>$value</th>";
    }
    $html .= "</tr>";
}

/**
 * 추가해야할 컬럼이 있는지 확인하는 함수
 * @param string $role 엑셀 출력 헤더
 * @param mixed $data 엑셀 출력 데이터
 * @return void
 */
function check_add_column(string $role, &$data)
{
    if ($role == "player_rank_listing") {
        $data = array_merge(["ranking" => ""], $data);
    }
    if ($role == "country_listing") {
        $data = array_merge(["ranking" => ""], $data, ["sum" => ""]);
    }
    if ($role == "sport_management") {
        $data = array_merge(["number" => ""], $data);
    }
}

/**
 * 엑셀에 들어갈 데이터를 추가하는 함수
 * @param string $html 내보낼 엑셀 파일
 * @param array $data DB에서 읽은 data
 * @return void
 */
function add_table_row_data(string &$html, array $data)
{
    $html .= "<tr>";
    foreach ($data as $key => $value) {
        if (!check_exclude_column($key)) {
            id_to_korea($key, $value, $data);
            $html .= '<td style=mso-number-format:"\@">' . $value . '</td>';
        }
    }
    $html .= "</tr>";
}

/**
 * SQL 쿼리문 중에서 엑셀로 변환이 불필요한 컬럼을 확인하는 함수
 * exclude_key_names 변수에 불필요한 컬럼 이름 저장
 * @param string $colum 컬럼 이름
 * @return bool true: 불필요한 컬럼 이름, false: 필요한 컬럼 이름
 */
function check_exclude_column(string $colum)
{
    global $role;
    switch ($role) {
        case "athlete":
            if (in_array($colum, ["athlete_profile", "athlete_bib", "athlete_isIssued", "athlete_sector", "athlete_sb", "athlete_pb", "country_code", "country_name", "country_name_kr"])) return true;
            return false;
        case "coach":
            if (in_array($colum, ["coach_profile", "coach_isIssued", "coach_sector", "country_code", "country_name", "country_name_kr"])) return true;
            return false;
        case "judge":
            if (in_array($colum, ["judge_profile", "judge_account", "judge_password", "judge_latest_datetime", "judge_latest_ip", "judge_latest_session", "judge_isIssued", "judge_sector", "country_code", "country_name", "country_name_kr"])) return true;
            return false;
        case "director":
            if (in_array($colum, ["director_profile", "director_isIssued", "director_sector", "country_code", "country_name", "country_name_kr"])) return true;
            return false;
        case "result_management":
            if (in_array($colum, ["record_live_record", "record_schedule_id", "schedule_sports"])) return true;
            return false;
        case "player_rank_listing":
            if (in_array($colum, ["result_medal", "medal"])) return true;
            return false;
        case "new_record_listing":
            if (in_array($colum, ["worldrecord_sports", "sports_code", "country_name_kr", "worldrecord_location"])) return true;
            return false;
        case "schedule_rank_listing":
            if (in_array($colum, ["country_name", "sports_code", "record", "record_weight"])) return true;
            return false;
        case "country_listing":
            if (in_array($colum, ["country_code", "result_medal", "medal"])) return true;
            return false;
        case "schedule_listing":
            if (in_array($colum, ["schedule_sports", "sports_code", "schedule_id"])) return true;
            return false;
        case "account_log":
            if (in_array($colum, ["log_sub_activity"])) return true;
            return false;
        default:
            return false;
    }
}

/**
 * id로 저장된 값을 한국어로 변경하는 함수
 * if-else 문으로 컬럼 별로 처리할 작업 분류
 * @param string $key
 * @param mixed $value
 * @param array $data
 * @return void
 */
function id_to_korea(string $key, &$value, array $data)
{
    global $role;
    if (string_contains($key, "gender")) {
        # m, f, c -> 남자, 여자, 혼성 변환
        if ($value == 'm')
            $value = "Man";
        else if ($value == 'f')
            $value = "Woman";
        else if ($value == 'c')
            $value = "Mixed";
        else
            $value = "";
    } else if (string_contains($key, "log_division")) {
        # a, j -> 관리자, 심판 변환
        $value = $value === 'a' ? "Admin" : "Judge";
    } else if (string_contains($key, "admin_level")) {
        # 계정 권한 ID -> 영어 변환
        global $level_dic_en;
        $level_ids = explode(',', $value);
        $level_kor = array();
        foreach ($level_ids as $level_id) {
            if ($level_id != "")
                $level_kor[] = $level_dic_en[trim($level_id)];
        }
        $value = implode(', ', $level_kor);
    } else if (string_contains($key, "log_activity")) {
        # log_activity 와 log_sub_activity 결합
        $sub_activity = $data['log_sub_activity'] ?? null;
        if ($sub_activity !== null && $sub_activity !== "") {
            $value = $value . '(' . $sub_activity . ')';
        }
    } else if ((string_contains($key, "_schedule") || string_contains($key, "_attendance")) && $key != "record_schedule_id") {
        # 참가예정경기, 참석확정경기 -> 스포츠 종목 명(영어) 변환
        global $sport_dic, $judge_sport_dic;
        $sports_ids = explode(',', $value);
        $sports_kor = array();
        foreach ($sports_ids as $sport_id) {
            if ($sport_id != '' && in_array(trim($sport_id), array_keys($sport_dic))) {
                $sports_kor[] = $sport_dic[trim($sport_id)];
            } else if (in_array(trim($sport_id), array_keys($sport_dic))) {
                $sports_kor[] = $judge_sport_dic[trim($sport_id)];
            }
        }
        $value = implode(', ', $sports_kor);
    } else if (string_contains($key, "schedule_status")) {
        // 일정 목록의 경기 상태 코드 -> 영어 변환
        if ($value == 'n')
            $value = "Not Start";
        else if ($value == 'o')
            $value = "In Game";
        else if ($value == 'y')
            $value = "End";
        else if ($value == 'c')
            $value = "Cancel";
        else
            $value = "";
    } else if (string_contains($key, "schedule_result")) {
        // 일정 목록 - 경기 결과 상태 코드 -> 영어 변환
        if ($value == 'o')
            $value = "Official Result";
        else if ($value == 'l')
            $value = "Live Result";
        else if ($value == 'n')
            $value = "Not Start";
        else
            $value = "";
    } else if (string_contains($key, "schedule_start")) {
        // 일정 목록 - 시작 시간 datetime -> time 형식으로 변경
        $value = explode(' ', $value)[1];
    } else if (string_contains($key, "schedule_date")) {
        // 일정 목록 - 날짜 datetime -> date 형식으로 변경
        $value = explode(' ', $value)[0];
    } else if (string_contains($key, "worldrecord_athletics")) {
        // 신기록 영어 변경
        if ($value == "w") $value = "WR";
        else if ($value == "u") $value = "UWR";
        else if ($value == "a") $value = "AR";
        else if ($value == "s") $value = "UAR";
        else $value = "CR";
    } else if ($key == "record_wind") {
        // 풍속 / 용기구 병합
        $value = $data[$key] == "" ? $data["record_weight"] : $data["record_wind"];
    } else if (in_array($key, ["bronze", "silver", "gold"]) && $role === "player_rank_listing") {
        // 선수별 순위보기 - 메달 편성(금, 은, 동)
        $value = $value == "" ? 0 : $value;
    } else if ($key == "sum" && $role === "player_rank_listing") {
        // 선수별 순위보기 - 메달 총 개수
        $gold = intval($data["gold"] / 10000);
        $silver = intval($data["silver"] / 100);
        $bronze = intval($data["bronze"]);
        $value = $gold + $silver + $bronze;
    } else if ($key == "sum" && $role === "country_listing") {
        // 국가별 순위보기 - 메달 총 개수
        $value = intval($data["gold_total"]) + intval($data["silver_total"]) + intval($data["bronze_total"]);
    } else if ($key == "record_official_record") {
        // live, official record 병합
        $value = $data["record_official_record"] == "" ? $data["record_live_record"] ?? "" : $data["record_official_record"] ?? "";
    } else if (in_array($key, ["gold_record", "silver_record", "bronze_record"])) {
        // 경기별 릴레이 경기 정보 한개만 출력
        $value = explode(",", $value)[0];
    } else if ($key === "country_name" && $role === "country_listing") {
        // 국가별 순위보기 - 국가 -> 국가(국가코드) 병합
        $value = $value . '(' . $data['country_code'] . ')';
    }
}