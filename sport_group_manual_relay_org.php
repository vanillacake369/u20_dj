<?php
require_once __DIR__ . "/head.php";
require_once __DIR__ . "/includes/auth/config.php";
require_once __DIR__ . "/security/security.php";
require_once __DIR__ . "/action/sport/sport_auto_create_group.php";
global $db;
// print_r($_POST);
$create_type = $_POST["mode"] ?? null;                  // string: 자동 or 수동
$sport_code = $_POST["sports"] ?? null;                 // string: 경기 이름
$country_code = $_POST["athlete_id"] ?? null;           // array: 참가하는 국가 배열
$group_count = cleanInput($_POST["groupcount"]);        // string: 조 개수
$number = cleanInput($_POST["groupnumber"]) ?? null;    // string: 최대 조 인원
$category = "트랙경기";                                   // string: 릴레이 종목은 트랙경기 밖에 없음
$round = cleanInput($_POST["round"]);                   // string: 경기 라운드(영어)
$gender = cleanInput($_POST["gender"]);                 // string: 경기 성별(enum 코드)
// $id = cleanInput($_GET["id"]);                       //schedule_id
// $number = 8;

if (!isset($create_type, $sport_code, $country_code, $group_count, $category, $round, $number)) {
    // 필수 적인 값들이 안들어오면 창 종료
    mysqli_close($db);
    exit("<script>alert('잘못된 경로입니다.');  window.close();</script>");
}

// 자동 생성이면 action/sport/sport_auto_create_group.php를 실행시켜 그룹을 배열로 생성하여 가져옴
if ($create_type === "자동") {
    $groups = get_group_relay($sport_code, $gender, $country_code);
    $group_count = count($groups);
    for ($i = 0; $i < $group_count; $i++) {
        // order 순으로 정렬
        usort($groups[$i], function ($data1, $data2) {
            $data1_order = intval($data1["record_order"]);
            $data2_order = intval($data2["record_order"]);
            if ($data1_order < $data2_order) {
                return -1;
            } else if ($data1_order > $data2_order) {
                return 1;
            } else {
                return 0;
            }
        });
    }
} else if ($create_type === "수동") {
    $groups = [];
    for ($i = 0; $i < $group_count; $i++) {
        $groups[$i] = [];
        for ($j = 0; $j < $number; $j++) {
            $groups[$i][] = [];
        }
    }
    $athletes = get_athlete_season_best_relay($sport_code, $gender, $country_code);
}
// $sportssql = "SELECT schedule_sports FROM list_schedule WHERE schedule_id='" . $id . "'";
// $sportresult = $db->query($sportssql);
// $sport = mysqli_fetch_array($sportresult);
// $sql = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, FIND_IN_SET( '" . $sport['schedule_sports'] . "' ,athlete_schedule) AS checking FROM list_athlete having checking>0 order by athlete_country asc, athlete_name asc;";
// $result = $db->query($sql);
// while ($row = mysqli_fetch_array($result)) {
//    $resultArr[] = $row;
// }

?>
<!--Data Tables-->
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
                            <p class="tit_left_yellow"><?php echo $i ?>조 편성</p>
                        </div>
                        <div class="filed_list filed2_list ">
                            <ul>
                                <li>
                                    <p><?php echo $category == '트랙경기' ? '레인' : '순서' ?></p>
                                    <p>선수 이름</p>
                                </li>
                            </ul>
                        </div>
                        <div class="filed_item filed2_item">
                            <?php for ($j = 1; $j <= count($groups[$i - 1]); $j++) { ?>
                                <ul>
                                    <?php for ($order = 1; $order <= 4; $order++) { ?>
                                        <li>
                                            <input type="hidden" name="group[]" id="group[]" value=" <?php echo $i ?>">
                                            <input type="hidden" name="order[]" value="<?php echo $order ?>">
                                            <?php if ($create_type === "수동") { ?>
                                                <input type="text" class="input_text" value="<?= $order % 4 == 1 ? $j : '' ?>" name="lane[]" <?php echo $order % 4 == 1 ? '' : ' readonly' ?>>
                                            <?php } else if ($create_type === "자동") { ?>
                                                <input type="text" class="input_text" value="<?= $order % 4 == 1 ? $groups[$i - 1][$j - 1]["record_order"] : '' ?>" name="lane[]" <?php echo $order % 4 == 1 ? '' : ' readonly' ?>>
                                            <?php } ?>
                                        </li>
                                        <li>
                                            <select id='athlete' name="athlete" required>
                                                <?php if ($create_type === "수동") { ?>
                                                    <option value="" disabled selected>선수 선택</option>
                                                    <?php
                                                    for ($k = 0; $k < count($athletes); $k++) { ?>
                                                        <option value="<?php echo $athletes[$k]['athlete_id'] ?>">
                                                            <?php echo $athletes[$k]['athlete_name'] ?>
                                                            (<?php echo $athletes[$k]['athlete_country'] ?>
                                                            )(<?php echo $athletes[$k]['athlete_division'] ?>)
                                                        </option>
                                                    <?php } ?>
                                                <?php } else if ($create_type === "자동") { ?>
                                                    <option value="<?php echo $groups[$i - 1][$j - 1][$order - 1]['athlete_id'] ?>" selected>
                                                        <?php echo $groups[$i - 1][$j - 1][$order - 1]['athlete_name'] ?>
                                                        (<?php echo $groups[$i - 1][$j - 1][$order - 1]['athlete_country'] ?>
                                                        )(<?php echo $groups[$i - 1][$j - 1][$order - 1]['athlete_division'] ?>
                                                        )
                                                    </option>
                                                    <?php for ($k = 4; $k < count($groups[$i - 1][$j - 1]) - 1; $k++) { ?>
                                                        <option value="<?php echo $groups[$i - 1][$j - 1][$k]['athlete_id'] ?>">
                                                            <?php echo $groups[$i - 1][$j - 1][$k]['athlete_name'] ?>
                                                            (<?php echo $groups[$i - 1][$j - 1][$k]['athlete_country'] ?>
                                                            )(<?php echo $groups[$i - 1][$j - 1][$k]['athlete_division'] ?>)
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <?php if ($create_type === "수동") {
                                                echo '<input type="hidden" name="player_id[]" value=""/>';
                                            } else if ($create_type === "자동") {
                                                echo '<input type="hidden" name="player_id[]" value="' . $groups[$i - 1][$j - 1][$order - 1]['athlete_id'] . '"/>';
                                            }
                                            ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>

                            <div class="filed_BTN2">
                                <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN" onclick="addColumn()"><i class="xi-plus"></i></button>
                                <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN" onclick="deleteColumn()"><i class="xi-minus"></i></button>
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
    <script>
        $("select[name=athlete]").select2();
        $("select[name=athlete]").change(function(idx) {
            var index = $("select[name='athlete[]']").index(this);
            var value = $(this).val();
            var eqValue = $(this).next().next().val(value);
        });
    </script>

</body>

</html>