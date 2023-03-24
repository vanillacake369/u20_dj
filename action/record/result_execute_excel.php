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
require_once __DIR__ . '/../../includes/auth/config.php';
require_once __DIR__ . '/../module/dictionary.php';
global $db, $categoryOfSports_dic;

$sports = $_POST['schedule_sports'];
$round = $_POST['schedule_round'];
$gender = $_POST['schedule_gender'];
$group = $_POST['schedule_group'];
$schedule_result = $_POST['schedule_result'];
$category = $categoryOfSports_dic[$sports];
switch ($schedule_result) {
    case 'l':
        $schedule_result = "Live Result";
        break;
    case 'o':
        $schedule_result = "Official Result";
        break;
    case 'n':
        $schedule_result = "Not Start";
        break;
}

function get_data_merge(array $merge_data, int $length)
{
    $record_result_data = [];
    for ($i = 0; $i < $length; $i++) {
        $insert_data = [];
        foreach (array_keys($merge_data) as $insert_type) {
            if (in_array($insert_type, ["relay_bib", "relay_player"])) {
                $merge_name = '';
                for ($name_idx = $i * 4; $name_idx < ($i * 4) + 4; $name_idx++) {
                    $merge_name .= $merge_data[$insert_type][$name_idx] . '<br>';
                }
                $insert_data[] = $merge_name;
            } else if ($insert_type == "attempt") {
                for ($attempt_idx = $i * 6; $attempt_idx < ($i * 6) + 6; $attempt_idx++) {
                    $insert_data[] = $merge_data[$insert_type][$attempt_idx];
                }
            } else {
                $insert_data[] = $merge_data[$insert_type][$i];
            }
        }
        $record_result_data[] = $insert_data;
    }
    return $record_result_data;
}

function pre_work(string &$html, string $category, string $sports)
{
    $html .= "<tr>";
    switch ($category) {
        case '트랙경기':
            $rain = $_POST["rain"];
            $rank = $_POST["rank"];
            $player_bib = $_POST["playerbib"];
            $player_name = $_POST["playername"];
            $record_result = $_POST["gameresult"];
            $reaction_time = $_POST["reactiontime"];
            $memo = $_POST["bigo"];
            $new_record = $_POST["newrecord"];
            $country = $_POST["division"] ?? null;  // 릴레이 경기 한정
            if (in_array($sports, ["4x400mR", "4x100mR", "4x400m Relay"])) {
                $header = ["레인", "등수", "등번호", "이름", "국가", "기록", "Reaction<br>Time", "비고", "신기록"];
                $merge_data = ["rain" => $rain, "rank" => $rank, "relay_bib" => $player_bib, "relay_player" => $player_name,
                    "country" => $country, "result" => $record_result, "reaction" => $reaction_time, "memo" => $memo, "new_record" => $new_record];
            } else {
                $header = ["레인", "등수", "등번호", "이름", "기록", "Reaction<br>Time", "비고", "신기록"];
                $merge_data = ["rain" => $rain, "rank" => $rank, "bib" => $player_bib, "player_name" => $player_name,
                    "result" => $record_result, "reaction" => $reaction_time, "memo" => $memo, "new_record" => $new_record];
            }
            $record_result_data = get_data_merge($merge_data, count($rain));
            break;
        case '필드경기':
            $order = $_POST["order"];
            $rank = $_POST["rank"];
            $player_bib = $_POST["playerbib"];
            $player_name = $_POST["playername"];
            $record_result = $_POST["gameresult"];
            $memo = $_POST["bigo"];
            $new_record = $_POST["newrecord"];

            if (!in_array($sports, ["Pole Vault", "High Jump"])) {
                $attempt = [];
                $result_idx = 1;
                while (($_POST["gameresult" . $result_idx] ?? null) != null) {
                    $attempt[] = $_POST["gameresult" . $result_idx][0];
                    $result_idx += 1;
                }
            }
            $merge_data = ["order" => $order, "rank" => $rank, "bib" => $player_bib, "player_name" => $player_name,
                           "attempt" => $attempt, "result" => $record_result, "memo" => $memo, "new_record" => $new_record];
            $header = ["순번", "등수", "BIB", "이름", "1차 시기", "2차 시기", "3차 시기", "4차 시기", "5차 시기", "6차 시기", "기록", "비고", "신기록"];
            $record_result_data = get_data_merge($merge_data, count($order));
            break;
        default:
            return;
    }
    foreach ($header as $value) {
        $html .= "<th>$value</th>";
    }
    $html .= "</tr>";
    return $record_result_data;
}

function add_table_row_data(&$html, $data)
{
    $html .= "<tr>";
    foreach ($data as $datum) {
        $html .= '<td style=mso-number-format:"\@">' . $datum . '</td>';
    }
    $html .= "</tr>";
}

$FILE_NAME = $sports . '_' . $gender . '_' . $round . '_' . $group . 'group(' . $schedule_result . ').xls';
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename = " . $FILE_NAME);     //filename = 저장되는 파일명을 설정합니다.
header("Content-Description: PHP4 Generated Data");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
$html = '<table style="width:100%">';
$insert_data = pre_work($html, $category, $sports);
foreach ($insert_data as $data) {
    add_table_row_data($html, $data);
}
$html .= "</table>";
echo $html;