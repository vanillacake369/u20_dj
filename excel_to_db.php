<?php

/**
 * 1. 한글 변환을 위해 setlocale() 사용
 * 2. 파일 절대 경로, 파일 타입, 페이지 유형(코치 페이지, 관리자 페이지...)을 받아와 저장
 * 3. 파일 타입이 CSV 이면 DB 작업 실행. 아닐시, 경고문 출력 후 뒤로가기
 * 3-1. 페이지 유형에 따라 데이터 삽입. (삽입시, 글자 포맷을 utf-8로 변환)
 */
include_once(__DIR__ . "/includes/auth/config.php");
include_once(__DIR__ . "/action/module/dictionary.php");
# 1, 2번
setlocale(LC_CTYPE, 'ko_KR.utf8');
global $db;
$file_absolute_path = $_FILES['file']['tmp_name'];
$file_type = strtolower(substr($_FILES['file']['name'], -4));
$role = $_POST['role'];
$no_insert_data = [];

// 2번
// 파일이 csv형식이 아니면, 엑셀이라도 return only '.csv' 파일이어야 함
if ($file_type !== '.csv') {
    echo '<script>alert("엑셀을 .csv 파일로 변환하여 시도해 주세요")</script>';
} else {
    # 3번
    CSVtoMember($file_absolute_path, $role);
    if (count($no_insert_data) == 0) {
        echo '<script>alert("등록이 완료되었습니다")</script>';
    } else {
        echo '<script>alert("아래와 같은 데이터가 중복이거나 형식에 맞지 않아 저장되지 않았습니다.\n' . implode('', $no_insert_data) . '");</script>';
    }
}
echo '<script>history.go(-1); location.reload();</script>';

/**
 * CSV에서 추출한 data를 utf8로 변환하는 함수
 *****
 * for문을 돌려 data안에 있는 내용물을 utf8로 변환한다.
 *****
 * @param array $data csv에서 추출한 data
 * @return void
 */
function translate_format(array &$data)
{
    for ($i = 0; $i < count($data); $i++)
        $data[$i] = iconv("euc-kr", "utf-8", $data[$i]);
}

/**
 * CSV에 있는 데이터를 $db에 저장하는 함수
 ************
 * 1. 파일 경로와 참가자 유형을 매개변수로 받는다.
 * 2. 파일 경로를 토대로 파일을 읽는다.
 * 3. $db와 연결한다.
 * 4. 참가자 유형에 따라 QUERY 문이 달라지므로 switch 문으로 분리한다.
 * 5. 파일안의 데이터를 한 줄씩 읽어, $db 안의 데이터와 중복되지 않는지 확인하고 $db에 넣는다.
 ************
 * @param string $file_path csv 파일 경로
 * @param string $role 참가자 유형
 * @return void
 */
function CSVtoMember(string $file_path, string $role)
{
    # 3-1번
    global $db, $no_insert_data;
    $handle = fopen($file_path, "r");
    $db->set_charset('utf8mb4');
    fgetcsv($handle, 1000, ","); // 해더 제거 (ex. 코치 이름, 코치 국적,...)
    switch ($role) {
        case "athlete": // 참가자 관리 - 선수 목록 - 엑셀 출력
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                translate_format($data);
                translate_sports_word_to_code($data[7]); // 참가 예정 경기
                translate_sports_word_to_code($data[8]); // 참석 확정 경기
                $trans_result = translate_gender_to_code($data[4]);      // 성별
                //$data[2] 비밀번호, 헤쉬 암호화 필요
                $sql = "SELECT COUNT(athlete_id) as id_count FROM list_athlete WHERE athlete_name = ? AND athlete_country = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $data[0], $data[1]);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = mysqli_fetch_assoc($result);

                if ($count['id_count'] == 0 && $trans_result) {
                    $sql = "INSERT INTO `list_athlete` (`athlete_name`, `athlete_country`, `athlete_region`, `athlete_division`, `athlete_gender`, `athlete_birth`, `athlete_age`, `athlete_schedule`, `athlete_attendance`) VALUES (?,?,?,?,?,?,?,?,?)";
                    $stmt = $db->prepare($sql);
                    //$stmt -> bind_param("Foo","CHN","Tian","소속2","m","2021-09-29",23,"/img/profile1","4");
                    $bind_result = $stmt->bind_param("ssssssiss", ...$data);
                    if ($bind_result) {
                        $stmt->execute();
                    } else {
                        $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                    }
                    $stmt->close();
                } else {
                    $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                }
            }
            $db->close();
            fclose($handle);
            break;

        case "coach": // 참가자 관리 - 코치 목록 - 엑셀 입력
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                translate_format($data);
                translate_sports_word_to_code($data[8]);        // 참가 예정 경기
                translate_sports_word_to_code($data[9]);        // 참석 확정 경기
                $trans_result = translate_gender_to_code($data[5]);             // 성별
                $data[4] = $data[4] === "헤드 코치" ? 'h' : 's'; // 코치 종류

                $sql = "SELECT COUNT(coach_id) as id_count FROM list_coach WHERE coach_name = ? AND coach_country = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $data[0], $data[1]);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = mysqli_fetch_assoc($result);

                if ($count['id_count'] == 0 && $trans_result) {
                    $sql = "INSERT INTO `list_coach` (`coach_name`, `coach_country`, `coach_region`, `coach_division`, coach_duty, `coach_gender`, `coach_birth`, `coach_age`, `coach_schedule`,`coach_attendance`) VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $db->prepare($sql);
                    //$stmt -> bind_param("smith","IDN","Delhi","소속1","감독","m","1999-12-22","25","25");
                    $bind_result = $stmt->bind_param("sssssssiss", ...$data);
                    if ($bind_result) {
                        $stmt->execute();
                    } else {
                        $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                    }
                    $stmt->close();
                } else {
                    $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                }
            }
            $db->close();
            fclose($handle);
            break;

        case "judge": // 참가자 관리 - 임원 목록 - 엑셀 출력
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                translate_format($data);
                translate_sports_word_to_code($data[7]); // 참가 예정 경기
                translate_sports_word_to_code($data[8]); // 참석 확정 경기
                $trans_result = translate_gender_to_code($data[3]);      // 성별
                $data[10] = hash('sha256', $data[10]);   // 비밀번호

                $sql = "SELECT COUNT(judge_id) as id_count FROM list_judge WHERE judge_name = ? AND judge_country = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $data[0], $data[1]);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = mysqli_fetch_assoc($result);

                if ($count['id_count'] == 0 && $trans_result) {
                    $sql = "INSERT INTO `list_judge` (`judge_name`, `judge_country`, `judge_division`, `judge_gender`, `judge_birth`, `judge_age`, `judge_duty`, `judge_schedule`, `judge_attendance`, `judge_account`, `judge_password`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $db->prepare($sql);
                    //$stmt -> bind_param("James","KOR","Seoul","m","2021-09-29",23,"직무1","2");
                    $bind_result = $stmt->bind_param("sssssisssss", ...$data);
                    if ($bind_result) {
                        $stmt->execute();
                    } else {
                        $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                    }
                    $stmt->close();
                } else {
                    $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                }
            }
            $db->close();
            fclose($handle);
            break;

        case "director": // 참가자 관리 - 임원 목록 - 엑셀 출력
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                translate_format($data);
                translate_sports_word_to_code($data[7]); // 참가 예정 경기
                translate_sports_word_to_code($data[8]); // 참석 확정 경기
                $trans_result = translate_gender_to_code($data[3]);      // 성별

                $sql = "SELECT COUNT(director_id) as id_count FROM list_director WHERE director_name = ? AND director_country = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $data[0], $data[1]);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = mysqli_fetch_assoc($result);

                if ($count['id_count'] == 0 && $trans_result) {
                    $sql = "INSERT INTO `list_director` (`director_name`, `director_country`, `director_division`, `director_gender`, `director_birth`, `director_age`, `director_duty`, `director_schedule`, `director_attendance`) VALUES (?,?,?,?,?,?,?,?,?)";
                    $stmt = $db->prepare($sql);
                    //$stmt -> bind_param("smith","IDN","Delhi","m", "1999-12-22","25","임원1","1");
                    $bind_result = $stmt->bind_param("sssssisss", ...$data);
                    if ($bind_result) {
                        $stmt->execute();
                    } else {
                        $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                    }
                    $stmt->close();
                } else {
                    $no_insert_data[] = '(' . implode(', ', $data) . ')\n';
                }
            }
            $db->close();
            fclose($handle);
            break;
    }
}

/**
 * 스포츠 종목 리스트 -> 스포츠 id 리스트 변환 함수
 * @param string $sport_words 스포츠 종목 이름 리스트
 * @return void
 */
function translate_sports_word_to_code(&$sport_words)
{
    if (is_null($sport_words)) $sport_words = ' ';
    global $sport_dic;
    $sport_words_arr = explode(',', $sport_words);
    $sport_ids = array();

    foreach ($sport_words_arr as $word) {
        $word = ltrim(strtolower($word));
        $sport_ids[] = array_search($word, $sport_dic);
    }
    $sport_words = implode(', ', $sport_ids);
}

/**
 * 성별 -> 성별 코드 변환 함수
 * @param string $gender 성별
 * @return void
 */
function translate_gender_to_code(string &$gender)
{
    if ($gender == "남자" || $gender == "여자")
        $gender = $gender === '남자' ? 'm' : 'f';
    else if ($gender == "male" || $gender == "female") {
        $gender = $gender === 'male' ? 'm' : 'f';
    } else if ($gender == "남" || $gender == "여") {
        $gender = $gender === '남' ? 'm' : 'f';
    } else if ($gender == "m" || $gender == "f") {
        $gender = $gender === 'm' ? 'm' : 'f';
    } else {
        return false;
    }
    return true;
}
