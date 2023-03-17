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
include_once(__DIR__ . "/includes/auth/config.php");
include_once(__DIR__ . "/action/module/dictionary.php");

# 3번
global $db;
$query = $_POST['query'];
$role = $_POST['role'];
$keyword = $_POST['keyword'] ?? null;
$ranking = 0;
$latest_data = "";
$same_data_count = 0;

if ($keyword !== null) {
    $keyword = explode(',', $keyword);
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
    $data = sort_before_rank($role, $data);
    foreach ($data as $datum) {
        check_add_column($role, $datum);
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
 * @return void
 */
function sort_before_rank(string $role, array $data)
{
    if ($role == "player_rank_listing") {
        usort($data, function ($datum1, $datum2) {
            if ($datum1["medal"] < $datum2["medal"]) {
                return 1;
            } elseif ($datum1["medal"] > $datum2["medal"]) {
                return -1;
            } else {
                return 0;
            }
        });
        return $data;
    } else if ($role == "country_listing") {
        usort($data, function ($datum1, $datum2) {
            $datum1_total_medal = intval($datum1["gold_total"] * 10000) + intval($datum1["silver_total"] * 100) + intval($datum1["bronze_total"]);
            $datum2_total_medal = intval($datum2["gold_total"] * 10000) + intval($datum2["silver_total"] * 100) + intval($datum2["bronze_total"]);
            if ($datum1_total_medal < $datum2_total_medal) {
                return 1;
            } elseif ($datum1_total_medal > $datum2_total_medal) {
                return -1;
            } else {
                return 0;
            }
        });
        return $data;
    } else if ($role == "schedule_rank_listing") {
        usort($data, function ($datum1, $datum2) {
            if ($datum1["record_live_result"] > $datum2["record_live_result"]) {
                return 1;
            } elseif ($datum1["record_live_result"] < $datum2["record_live_result"]) {
                return -1;
            } else {
                return 0;
            }
        });
        return $data;
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
            $header = array("번호", "이름", "국가", "지역", "소속", "성별", "생년월일", "나이", "예정 경기", "참석 확정 경기");
            break;
        case 'coach': # 참가자 관리 - 코치 목록 (entry_coach)
            $header = array("번호", "이름", "국가", "지역", "소속", "성별", "생년월일", "나이", "직무", "참가 예정 경기", "참석 확정 경기");
            break;
        case 'judge': # 참가자 관리 - 심판 목록 (entry_judge):
        case 'director': # 참가자 관리 - 임원 목록 (entry_director)
            $header = array("번호", "이름", "국가", "소속", "성별", "생년월일", "나이", "직무", "참가 예정 경기", "참석 확정 경기");
            break;
        case 'sport_management': # 경기 관리 - 경기 목록 (sportmanagement)
            $header = array("경기종목 코드", "경기종목 이름", "경기종목 이름(한글)");
            break;
        case 'country_management': # 경기 관리 - 국가 목록 (countrymanagement)
            $header = array("국가 이름", "국가 이름(한글)", "국가코드");
            break;
        case 'schedule_management': # 경기 관리 - 일정 목록 (schedulemanagement)
            $header = array("구분", "경기 종목 구분", "경기 이름", "경기 성별", "경기 라운드", "경기 장소", "경기 시작 시간", "경기진행 상태", "경기 날짜", "경기 결과");
            break;
        case 'result_management': # 기록 관리 - 경기결과 목록 (reusultManagement)
            $header = array("날짜", "구분", "경기 이름", "경기 라운드", "선수 이름", "심판 이름", "기록", "기록 상태");
            break;
        case 'record_history': # 기록 관리 - 역대기록 목록 (recordHistory)
            $header = array("기록 구분", "이름", "성별", "장소", "종목", "풍속/용기구", "기록", "일자", "국가");
            break;
        case 'player_rank_listing': # 통계 관리 - 선수별 순위보기 (playerRankingListing)
            $header = array("순위", "국가", "이름", "금", "은", "동");
            break;
        case 'new_record_listing': # 통계 관리 - 신기록 경기기록 (newRecordListing)
            $header = array("기록구분", "종목", "이름", "성별", "풍속/용기구", "기록", "기록일자", "국가");
            break;
        case 'schedule_rank_listing': # 통계 관리 - 경기별 순위보기 (scheduleRankingListing)
            $header = array("등수", "종목", "이름", "성별", "국가", "결과", "풍속/용기구", "비고");
            break;
        case 'schedule_listing': # 통계 관리 - 경기별 메달보기 (scheduleListing)
            $header = array("경기", "성별", "금", "기록", "은", "기록", "동", "기록");
            break;
        case 'country_listing': # 통계 관리 - 국가별 메달보기 (countryListing)
            $header = array("순위", "국가", "금", "은", "동", "합계");
            break;
        case 'account_log': # 로그 목록 페이지
            $header = array("순번", "아이디", "이름", "계정", "활동내역", "IP", "시간");
            break;
        case 'account_user': # 계정 목록 페이지
            $header = array("순번", "아이디", "이름", "권한");
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
            if (in_array($colum, ["athlete_profile"])) return true;
            return false;
        case "coach":
            if (in_array($colum, ["coach_profile"])) return true;
            return false;
        case "judge":
            if (in_array($colum, ["judge_profile"])) return true;
            return false;
        case "director":
            if (in_array($colum, ["director_profile"])) return true;
            return false;
        case "result_management":
            if (in_array($colum, ["record_live_record", "record_schedule_id", "schedule_sports"])) return true;
            return false;
        case "player_rank_listing":
            if (in_array($colum, ["result_medal", "medal"])) return true;
            return false;
        case "new_record_listing":
            if (in_array($colum, ["sports_code", "country_name_kr", "worldrecord_location"])) return true;
            return false;
        case "schedule_rank_listing":
            if (in_array($colum, ["country_name", "sports_code", "record", "record_weight"])) return true;
            return false;
        case "country_listing":
            if (in_array($colum, ["country_code", "result_medal", "medal"])) return true;
            return false;
        case "schedule_listing":
            if (in_array($colum, ["sports_code", "schedule_id"])) return true;
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
            $value = "남자";
        else if ($value == 'f')
            $value = "여자";
        else if ($value == 'c')
            $value = "혼성";
        else
            $value = "";
    } else if (string_contains($key, "coach_duty")) {
        # h, s -> 해드 코치, 서브코치 변환
        $value = $value === 'h' ? "헤드 코치" : "서브 코치";
    } else if (string_contains($key, "log_division")) {
        # a, j -> 관리자, 심판 변환
        $value = $value === 'a' ? "관리자" : "심판";
    } else if (string_contains($key, "admin_level")) {
        # 계정 권한 ID -> 한국어 변환
        global $level_dic;
        $level_ids = explode(',', $value);
        $level_kor = array();
        foreach ($level_ids as $level_id) {
            if ($level_id != "")
                $level_kor[] = $level_dic[trim($level_id)];
        }
        $value = implode(', ', $level_kor);
    } else if (string_contains($key, "log_activity")) {
        # log_activity 와 log_sub_activity 결합
        $sub_activity = $data['log_sub_activity'] ?? null;
        if ($sub_activity !== null && $sub_activity !== "") {
            $value = $value . '(' . $sub_activity . ')';
        }
    } else if ((string_contains($key, "_schedule") || string_contains($key, "_attendance")) && $key != "record_schedule_id") {
        # 참가예정경기, 참석확정경기 -> 스포츠 종목 명(한국어) 변환
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
        // 일정 목록의 경기 상태 코드 -> 한글 변환
        if ($value == 'n')
            $value = "시작안함";
        else if ($value == 'o')
            $value = "경기중";
        else if ($value == 'y')
            $value = "마감";
        else if ($value == 'c')
            $value = "취소";
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
        if ($value == "w") $value = "세계신기록";
        else if ($value == "u") $value = "세계U20신기록";
        else if ($value == "a") $value = "아시아신기록";
        else if ($value == "s") $value = "아시아U20신기록";
        else $value = "대회신기록";
    } else if ($key == "record_wind") {
        // 풍속 / 용기구 병합
        $value = $data[$key] == "" ? $data["record_weight"] : $data["record_wind"];
    } else if ($key == "ranking") {
        // 순위 편성
        global $latest_data, $ranking, $same_data_count;
        if ($ranking == 0 || $latest_data != $data["medal"]) {
            $value = $ranking + $same_data_count + 1;
            $ranking = $value;
            $same_data_count = 0;
        } else {
            $same_data_count += 1;
            $value = $ranking;
        }
        $latest_data = $data["medal"];
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
