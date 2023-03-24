<?php
header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";
require_once "action/module/dictionary.php";
global $db, $categoryOfSports_dic;

$create_type = cleanInput($_POST["mode"]) ?? null; // 자동 or 수동
$group_count = cleanInput($_POST["groupcount"]) ?? null; // 조 개수
$number      = cleanInput($_POST["groupnumber"]) ?? null; // 최대 조 인원
$sport_code  = cleanInput($_POST["sports"]) ?? null; // 경기 이름(코드)
$round       = cleanInput($_POST["round"]) ?? null; // 경기 라운드
$gender      = cleanInput($_POST["gender"]) ?? null; // 경기 성별
$athlete_ids = $_POST["athlete_id"] ?? null; // 경기에 참가하는 선수 id들
$category    = $categoryOfSports_dic[$sport_code] ?? null; // 경기 종목 (필드, 트랙)

// if (!isset($create_type, $group_count, $sport_code, $round, $gender, $athlete_ids, $category)) {
//     // 필수 적인 값들이 안들어오면 창 종료
//     mysqli_close($db);
//     exit("<script>alert('잘못된 경로입니다.');  window.close();</script>");
// }
// 참가하는 athlete_id의 data 가저오는 query
$query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_schedule FROM list_athlete WHERE athlete_gender = ? AND INSTR(athlete_schedule, ?) AND (athlete_id = ";
$query = $query . implode(" OR athlete_id = ", $athlete_ids) . ") ORDER BY athlete_name asc";
$stmt  = $db->prepare($query);
$stmt->bind_param("ss", $gender, $sport_code);
$stmt->execute();
$athletes_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 해당 종목인지 한번 더 필더링 100m 문자열 포함시, 100mh도 같이 끌고 와짐
$athletes_data = array_filter($athletes_data, function ($athlete) use ($sport_code) {
    // athlete_schedule 문자열을 배열로 변환 후, sport_name이 배열 안에 있으면 다시 배열에 집어 넣고, 아니면 집어 넣지 않음
    $schedule_sports = explode(',', $athlete["athlete_schedule"]);
    if (in_array($sport_code, $schedule_sports)) {
        return true;
    } else {
        return false;
    }
});

// select box 중복 제거를 위한 선수 dictionary
$athlete_labels = ["선수 선택" => ''];
foreach ($athletes_data as $athlete) {
    $key                    = trim($athlete["athlete_name"]) . '(' . trim($athlete["athlete_country"]) . ')' . '(' . trim($athlete["athlete_division"]) . ')';
    $athlete_labels[$key] = $athlete["athlete_id"];
}
// select box onclick listener를 사용하기 때문에  dictionary를 script로 보냄
echo "<script type='text/javascript'>const ORIGIN_LABEL_JSON = '" . json_encode($athlete_labels) . "';</script>";


define("MAX_ATHLETE", count($athlete_ids));
$athlete_count = 0;
// 자동 생성이면 action/sport/sport_auto_create_group.php를 실행시켜 그룹을 배열로 생성하여 가져옴
if (($sport_code === "decathlon" || $sport_code === "heptathlon") && $round === "final") {
    $number   = [];
    $groups[] = $athletes_data;
    $number[] = count($athletes_data);
} else if ($create_type === "자동") {
    require_once __DIR__ . "/action/sport/sport_auto_create_group.php";
    if ($sport_code === "decathlon" || $sport_code === "heptathlon") {
        // 종합경기 round => 세부 sport_code, sport_code => 종합경기 code
        $category = $categoryOfSports_dic[$round];
        $groups   = get_group($round, $group_count, $number, $gender, $category, $athlete_ids, $sport_code);
    } else {
        $groups = get_group($sport_code, $group_count, $number, $gender, $category, $athlete_ids);
    }
    $group_count        = count($groups);
    $number             = [];
    $select_athlete_ids = [];
    for ($i = 0; $i < count($groups); $i++) {
        $number[] = count($groups[$i]);
        for ($j = 0; $j < count($groups[$i]); $j++) {
            // select-box 선택을 위한  athlete_id 추가
            $select_athlete_ids[] = $groups[$i][$j]["athlete_id"];
        }
        // record_order순으로 정렬 (오름차순)
        usort($groups[$i], function ($data1, $data2) {
            if (intval($data1["record_order"]) < intval($data2["record_order"])) {
                return -1;
            } elseif (intval($data1["record_order"]) > intval($data2["record_order"])) {
                return 1;
            } else {
                // order가 같으면 이름순으로 정렬
                if ($data1["athlete_name"] < $data2["athlete_name"]) {
                    return -1;
                } elseif ($data1["athlete_name"] > $data2["athlete_name"]) {
                    return 1;
                }
                return 0;
            }
        });
    }
} else {
    $temp_number = $number;
    $number      = [];
    for ($i = 0; $i < $group_count; $i++) {
        $number[] = $temp_number;
    }
}
?>

<link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />
<link rel="stylesheet" href="/assets/css/select2.min.css" />
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/assets/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js"></script>
<script type="text/javascript" src="/assets/js/plus_table_column.js"></script>
<script type="text/javascript" src="/assets/js/select2.min.js"></script>
<script src="/assets/js/restrict.js"></script>
</head>

<body>
    <!-- contents 본문 내용 -->
    <div class="schedule_container">
        <form action="action/sport/schedule_manual_insert.php" method="post" class="form">
            <div class="schedule schedule_flex">
                <?php for ($i = 1; $i <= $group_count; $i++) { ?>
                    <div class="schedule_filed filed_list_item">
                        <div class="profile_logo">
                            <img src="/assets/images/logo.png">
                        </div>
                        <div class="schedule_filed_tit schedule_green">
                            <p class="tit_left_yellow">
                                <?php echo $i ?>조 편성
                            </p>
                        </div>
                        <div class="filed2_form">
                            <table cellspacing="0" cellpadding="0" class="filed2_Table " id="" name="table_name[]">
                                <thead class="filed_list filed2_list ">
                                    <tr>
                                        <th><?php echo $category == '트랙경기' ? '레인' : '순서' ?></th>
                                        <th>선수 이름</th>
                                    </tr>
                                    <tr class="filed2_bottom">
                                        <th colspan="2"></th>
                                    </tr>
                                </thead>
                                <tbody class="filed_item filed2_item">
                                    <!-- 레인넘버 & 선수 선택 for문 -->
                                    <?php for ($j = 1; $j <= $number[$i - 1] && $athlete_count < MAX_ATHLETE; $j++, $athlete_count++) { ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="group[]" id="group[]" value=" <?php echo $i ?>">
                                                <?php
                                                if ($create_type === "수동") {
                                                    echo '<input type="text" class="input_text" value="' . $j . '" name="lane[]">';
                                                } else if ($create_type === "자동") {
                                                    $record_order = $groups[$i - 1][$j - 1]["record_order"] ?? ' ';
                                                    echo '<input type="text" class="input_text" value="' . $record_order . '" name="lane[]">';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div id="copy-value">
                                                    <select class='select-box' name="athlete" onchange="select_change_listener()" required class="select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                                        <?php
                                                        if ($create_type === "수동") {
                                                            echo '<option value="" disabled selected>선수 선택</option>';
                                                            foreach ($athletes_data as $athlete) {
                                                                echo '<option value="' . $athlete["athlete_id"] . '">';
                                                                echo $athlete["athlete_name"] . '(' . $athlete["athlete_country"] . ')(' . $athlete["athlete_division"] . ')';
                                                                echo '</option>';
                                                            }
                                                        } elseif ($create_type === "자동" && isset($groups)) {
                                                            $athlete = $groups[$i - 1][$j - 1];
                                                            echo '<option value="' . $athlete["athlete_id"] . '" selected>';
                                                            echo $athlete["athlete_name"] . '(' . $athlete["athlete_country"] . ')(' . $athlete["athlete_division"] . ')';
                                                            echo '</option>';
                                                            echo '<option value="">선수 선택</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php
                                                // 선택된 선수들의 id값을 배열로서 파라미터 넘김 => action/sport/sport_manual_insert.php
                                                // player 선택 => player_id값이 변경되도록 js 구현되어있음
                                                if ($create_type === "수동") {
                                                    echo '<input type="hidden" class="hidden-input" id="player_id" name="player_id[]" value=""/>';
                                                } else if ($create_type === "자동") {
                                                    echo '<input type="hidden" class="hidden-input" id="player_id" name="player_id[]" value="' . $groups[$i - 1][$j - 1]["athlete_id"] . '"/>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                </tbody>
                            
                            </table>

                            <div class="filed_BTN2">
                                <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn"><i class="xi-minus"></i></button>
                                <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn"><i class="xi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <input type="hidden" name="count" value="<?php echo $group_count ?>">
                <input type="hidden" name="round" value="<?php echo $round ?>">
                <input type="hidden" name="gender" value="<?php echo $gender ?>">
                <input type="hidden" name="sport_code" value="<?php echo $sport_code ?>">
                <input type="hidden" name="sport_category" value="<?php echo $category ?>">
                <button type="submit" class="changePwBtn defaultBtn" name="addresult">확인</button>
            </div>
        </form>
    </div>

    <script src="./assets/js/main.js"></script>
    <script>
        //@Potatoeunbi
        //history.back이 될 때, select된 값이 남아있지만 select가 선수 선택으로만 남겨져 있어서 이 부분 해결해야 함.

        // 컬럼을 추가하는 버튼을 누를 때, span이 펼쳐지게 하는 select2()를 사용
        // $("select[name=athlete]").select2();
        $(".select-box").select2();

        // $("select[name=athlete]").on('change', function(idx) {
        // var index = $("select[name='athlete[]']").index(this);
        // var value = $(this).val();
        // var eqValue = $(this).parent().next().val(value);
        // });

        // $(document).ready(function() {
        // $('.select-box').each(function(index, element) {
        // $(element).on('select2:select', function(e) {
        // var sb = $('.select-box');
        // console.log(sb);
        // // var selectedValue = e.params.data.id;
        // // $(element).parent().next('.hidden-input').val(selectedValue);
        // // // var el = $(element).parent().next('.hidden-input');
        // // var el = $(element).parent().next('.hidden-input');
        // // console.log(el);
        // });
        // });
        // });

        // $(document).on('click', function() {
        // $('.select-box').each(function(index, element) {
        // $(element).on('select2:select', function(e) {
        // var selectedValue = e.params.data.id;
        // var hiddenInput = $(element).parent().next('.hidden-input');
        // hiddenInput.val(selectedValue);
        // console.log(hiddenInput.val());
        // });
        // });
        // });
        //
        // $("select").each(function() {
        // $(this).val($(this).find('option[selected]').val()).prop("selected", true);
        // });
    </script>

</body>

</html>