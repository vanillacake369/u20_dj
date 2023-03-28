<?php
// AJAX에서 던진 순서와 선수이름 인자값을 AJAX로 받아 새로운 row 생성
// @author 임지훈 @vanillacake369
function addNewRow($group, $lane, $id, $name)
{
    // add new row to the table with the given data
    echo '<tbody class="grouping_body entry_table">';
    echo '<tr>';
    echo '<td>';
    // <!-- 조 :: record_group -->
    echo '<input type="hidden" name="group[]" id="group[]" value="' . $group . '">';
    // <!-- 순서 :: record_order -->
    echo '<input type="hidden" name="order[]" id="order[]" value="' . $lane . '">';
    echo '<input type="text" class="number" value="' . $lane . '" name="lane[]">';
    echo '</td>';
    echo '<td>';
    // <!-- 선수 id :: record_athlete_id-->
    echo '<input type="hidden" name="athlete_id[]" id="athlete_id[]" value="' . $id . '">';
    // <!-- 선수 이름 -->
    echo '<input type="text" name="name[]" value="' . $name . '">';
    echo '</td>';
    // 삭제버튼
    echo '<td>';
    echo '<div class="filed_BTN2">';
    echo '<button type="button" name="delete_each_row" class="defaultBtn BIG_btn BTN_Blue filedBTN"><i class="xi-minus"></i></button>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
    echo '</tbody>';
}
if (isset($_POST['functionName']) && $_POST['functionName'] == 'addNewRow') {
    // get the data passed from Ajax call
    $group = $_POST['group'];
    $lane = $_POST['lane'];
    $id = $_POST['id'];
    $name = $_POST['name'];
    // call the function with the data
    addNewRow($group, $lane, $id,  $name);
    exit(); // stop executing the script after the function call
}
require_once "console_log.php";
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";
require_once "action/module/dictionary.php";


global $db, $categoryOfSports_dic;
$group_num = $_GET["record_group"] ?? null;         // ~ 조
$group_count = $_GET["count_group"] ?? null;        // 조 개수
$sport_code = $_GET["record_sports"] ?? null;       // 경기 이름(코드)
$round = $_GET["record_round"] ?? null;             // 경기 라운드
$gender = $_GET["record_gender"] ?? null;           // 경기 성별
$category = $categoryOfSports_dic[$sport_code] ?? null;         // 경기 종목 (필드, 트랙)
$each_group_athletes_id_lane = array();                         // each_group_athletes_id_lane[m][n][p] :: [m조][선수n의 id][선수 n의 레인p]
$each_group_athletes_data = array();                            // each_group_athletes_data[m][n][] :: [m조][0~n][선수 n의 모든 정보]

// m조,선수id,레인p 가져오기
$select_athlete_ids_sql = 'SELECT record_group,record_athlete_id,record_order FROM list_record'
    . ' WHERE record_sports = \'' . $sport_code . '\''
    . ' AND record_round = \'' . $round . '\''
    . ' AND record_gender =  \'' . $gender . '\''
    . ' ORDER BY record_group ASC, record_athlete_id';
$get_all_athlete_of_group = $db->query($select_athlete_ids_sql);

while ($result = mysqli_fetch_array($get_all_athlete_of_group)) {
    if ($result["record_order"] != null) {
        $each_group_athletes_id_lane[$result["record_group"]][$result["record_athlete_id"]][] = $result["record_order"];    // [1조][22232][2번 레인]
    }
}
// m조에 대해
$first_index_each_group_athletes_id_lane = array_key_first($each_group_athletes_id_lane);
$last_index_each_group_athletes_id_lane = array_key_last($each_group_athletes_id_lane);
for ($i = $first_index_each_group_athletes_id_lane; $i <= $last_index_each_group_athletes_id_lane; $i++) {
    // each_group_athletes_id[][] :: m조에 편성되어있는 n명의 선수들 id값
    $each_group_athletes_id = array_keys($each_group_athletes_id_lane[$i]);
    // each_group_athletes_lane[][] :: m조에 편성되어있는 n명의 선수들 레인값
    $each_group_athletes_lane = array_values($each_group_athletes_id_lane[$i]);
    // n명의 선수들 정보 가저오는 query
    $query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_attendance FROM list_athlete WHERE athlete_gender = ? AND INSTR(athlete_attendance, ?) AND (athlete_id = ";
    $query = $query . implode(" OR athlete_id = ", $each_group_athletes_id) . ") ORDER BY athlete_id asc";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $gender, $sport_code);
    $stmt->execute();
    $each_group_athletes_data[$i] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // 해당 종목인지 한번 더 필더링 :: 100m 문자열 포함시, 100mh도 같이 끌고 와짐 :: @author 변상원 @1st-award
    $each_group_athletes_data[$i] = array_filter(
        $each_group_athletes_data[$i],
        function ($athlete) use ($sport_code) {
            // athlete_attendance 문자열을 배열로 변환 후, sport_name이 배열 안에 있으면 다시 배열에 집어 넣고, 아니면 집어 넣지 않음
            $attendance_sports = explode(',', $athlete["athlete_attendance"]);
            if (in_array($sport_code, $attendance_sports)) {
                return true;
            } else {
                return false;
            }
        }
    );
    // n명에 대해
    for ($j = 0; $j < count($each_group_athletes_data[$i]); $j++) {
        // 선수이름 => 선수이름(국가)(소속)
        $each_group_athletes_data[$i][$j]['athlete_name'] = $each_group_athletes_data[$i][$j]['athlete_name']
            . '(' . $each_group_athletes_data[$i][$j]['athlete_country'] . ')'
            . '(' . $each_group_athletes_data[$i][$j]['athlete_division'] . ')';
        // 선수정보배열 <= 선수레인
        $each_group_athletes_data[$i][$j]['athlete_lane'] = $each_group_athletes_lane[$j][0];
    }
    // 선수 id 순으로 each_group_athletes_data 배열을 생성하였으므로 선수 레인 순서 별로 재정렬 (PHP7 기준에 맞춤)
    usort($each_group_athletes_data[$i], function ($a, $b) {
        return $a['athlete_lane'] <=> $b['athlete_lane'];
    });
}
// 선수 추가 시, 경기 참가하는 선수 쿼리
$query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_schedule FROM list_athlete WHERE athlete_gender = ? AND INSTR(athlete_schedule, ?)";
$query = $query . "ORDER BY athlete_name asc";
$stmt  = $db->prepare($query);
$stmt->bind_param("ss", $gender, $sport_code);
$stmt->execute();
$athletes_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// select box 중복 제거를 위한 선수 dictionary
$athlete_labels = ["선수 선택" => ''];
foreach ($athletes_data as $athlete) {
    $key                    = trim($athlete["athlete_name"]) . '(' . trim($athlete["athlete_country"]) . ')' . '(' . trim($athlete["athlete_division"]) . ')';
    $athlete_labels[$key] = $athlete["athlete_id"];
}
// select box onclick listener를 사용하기 때문에  dictionary를 script로 보냄
echo "<script type='text/javascript'>const ORIGIN_LABEL_JSON = '" . json_encode($athlete_labels) . "';</script>";
?>

<script type="text/javascript" src="./assets/js/jquery-1.12.4.min.js"></script>
<link rel="stylesheet" href="./assets/css/xeicon.min.css">
<link rel="stylesheet" href="./assets/css/swiper.min.css">
<link rel="stylesheet" href="./assets/css/reset.css">
<link rel="stylesheet" href="./assets/css/select2.min.css">
<script type="text/javascript" src="./assets/js/select2.min.js"></script>
<script>
    $("select[name=athlete]").select2();
</script>
<script src="assets/js/main.js?ver=15"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" href="./assets/css/style.css">
<link rel="stylesheet" href="./assets/css/style_dh.css">
<title>조 편성</title>
</head>


<div class="schedule_container">
    <form action="action/sport/schedule_manual_update.php" method="post" class="form">
        <div class="schedule schedule_flex">
            <!-- n조 -->
            <?php
            // START FOR (1~m조)
            $num = 0;
            $first_index_each_group_athletes_data = array_key_first($each_group_athletes_data);
            $last_index_each_group_athletes_data = array_key_last($each_group_athletes_data);
            for ($i = $first_index_each_group_athletes_data; $i <= $last_index_each_group_athletes_data; $i++) {
                $num++;
            ?>
                <div class="schedule_filed filed_list_item filed2">
                    <div class="profile_logo">
                        <img src="/assets/images/logo.png">
                    </div>
                    <div class="schedule_filed_tit schedule_green">
                        <p class="tit_left_yellow"><?php echo $i ?>조</p>
                    </div>
                    <div class="filed2_form">
                        <table cellspacing="0" cellpadding="0" class="entry_table filed2_swap" id="dataTable<?php echo $i ?>" name="table_name[]">
                            <thead class="filed_list filed2_list result_table ">
                                <tr>
                                    <th>순서</th>
                                    <th>선수 이름</th>
                                    <th>삭제</th>
                                </tr>
                                <tr class="filed2_bottom">
                                </tr>
                            </thead>
                            <!-- m명 -->
                            <?php
                            // START FOR (1~n명)
                            $first_index_each_group_athletes_data_i = array_key_first($each_group_athletes_data[$i]);
                            $last_index_each_group_athletes_data_i = array_key_last($each_group_athletes_data[$i]);
                            for ($j = $first_index_each_group_athletes_data_i; $j <= $last_index_each_group_athletes_data_i; $j++) {
                            ?>
                                <tbody class="grouping_body entry_table">
                                    <tr>
                                        <td>
                                            <!-- 조 :: record_group -->
                                            <input type="hidden" name="group[]" id="group[]" value="<?php echo $i ?>">
                                            <!-- 순서 :: record_order -->
                                            <input type="hidden" name="order[]" id="order[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_lane'] ?? NULL ?>">
                                            <input type="text" class="number" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_lane']  ?? NULL ?>" name="lane[]">
                                        </td>
                                        <td>
                                            <!-- 선수 id :: record_athlete_id-->
                                            <input type="hidden" name="athlete_id[]" id="athlete_id[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_id']  ?? NULL ?>">
                                            <!-- 선수 이름 -->
                                            <input type="text" name="name[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_name']  ?? NULL ?>">
                                        </td>
                                        <td>
                                            <!-- 삭제 버튼 -->
                                            <div class="filed_BTN2">
                                                <button type="button" name="delete_each_row" class="defaultBtn BIG_btn BTN_Blue filedBTN" id="deleteBtn<?php echo $i ?>"><i class="xi-minus"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php
                            }
                            // END FOR (n명)
                            ?>
                        </table>
                        <table class="entry_table changeMemberList">
                            <colgroup>
                                <col width="20%">
                                <col width="20%">
                                <col width="60%">
                            </colgroup>
                            <thead class="  result_table ">
                                <tr>
                                    <th>조</th>
                                    <th>순서</th>
                                    <th>선수이름</th>
                                </tr>
                                <tr class="filed2_bottom">
                                </tr>
                            </thead>
                            <tbody class="grouping_body entry_table">
                                <td> <input type="text" class="number" id="group<?php echo $i ?>" value="<?php echo $i ?>" readonly></td>
                                <td> <input type="text" class="number" id="lane<?php echo $i ?>"></td>
                                <td> <select class='select-box' name="athlete" id="name<?php echo $i ?>" onchange="select_change_listener()" class="select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                        <?php
                                        echo '<option value="" disabled selected>선수 선택</option>';
                                        foreach ($athletes_data as $athlete) {
                                            echo '<option value="' . $athlete["athlete_id"] . '">';
                                            echo $athlete["athlete_name"] . '(' . $athlete["athlete_country"] . ')(' . $athlete["athlete_division"] . ')';
                                            echo '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tbody>
                        </table>
                        <div class="filed_BTN2">
                            <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN" id="addBtn<?php echo $i ?>"><i class=" xi-plus"></i></button>
                        </div>

                    </div>
                </div>
                <script>
                    // @author 임지훈 @vanillacake369
                    // 순서와 선수이름 선택 후 +버튼을 누르게 되면 AJAX를 통해 PHP 행 추가 함수를 호출
                    // 각 조 별로 테이블,input,select의 id값이 달라야 서로 달리 적용가능
                    $(document).ready(function() {
                        // addBtn1,addBtn2,addBtn3 ...
                        $("#addBtn<?php echo $i ?>").click(function() {
                            // group1,group2,group3 ...
                            var group = $("#group<?php echo $i ?>").val();
                            // lane1,lane2,lane3 ...
                            var lane = $("#lane<?php echo $i ?>").val();
                            // name1,name2,name3 ...
                            var id = $("#name<?php echo $i ?>").val();
                            // name1,name2,name3 ...
                            var name = $("#name<?php echo $i ?> option:selected").text();
                            $.ajax({
                                url: "<?php echo $_SERVER['PHP_SELF']; ?>", // current page URL
                                type: "POST",
                                data: {
                                    functionName: "addNewRow",
                                    group: group,
                                    lane: lane,
                                    id: id,
                                    name: name
                                },
                                success: function(result) {
                                    // dataTable1,dataTable2,dataTable3 ...
                                    $("#dataTable<?php echo $i ?>").append(result); // add new row to the table
                                    $("#lane<?php echo $i ?>").val('');
                                    $("#name<?php echo $i ?>").val('');
                                }
                            });
                        });
                    });
                    // @author 임지훈 @vanillacake369
                    // 각 행의 -버튼을 누르게 되면 AJAX를 통해 테이블의 마지막 행을 "가상"삭제
                    $(document).ready(function() {
                        $("button[name='delete_each_row']").click(function() {
                            // SQL NULL값 INSERT : 해당 선수 record의 group과 order => NULL
                            $(this).closest("tr").find("input[name='group[]']").val('');
                            $(this).closest("tr").find("input[name='order[]']").val('');
                            // 가상 삭제(뷰)
                            $(this).closest("tr").find("input[name='lane[]']").val('');
                            $(this).closest("tr").find("input[name='name[]']").val('');
                        });
                    });
                </script>
            <?php
            }
            // END FOR (m조)
            ?>
            <input type="hidden" name="count" value="<?php echo $group_count ?>">
            <input type="hidden" name="round" value="<?php echo $round ?>">
            <input type="hidden" name="gender" value="<?php echo $gender ?>">
            <input type="hidden" name="sport_code" value="<?php echo $sport_code ?>">
            <input type="hidden" name="sport_category" value="<?php echo $category ?>">
            <button type="submit" class="changePwBtn defaultBtn">만들기</button>
        </div>
    </form>
</div>
</body>

</html>