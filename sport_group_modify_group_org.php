<?php

require_once "console_log.php";
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";
require_once "action/module/dictionary.php";

global $db, $categoryOfSports_dic;
$group_num = cleanInput($_GET["record_group"]) ?? null;         // ~ 조
$group_count = cleanInput($_GET["count_group"]) ?? null;        // 조 개수
$sport_code = cleanInput($_GET["record_sports"]) ?? null;       // 경기 이름(코드)
$round = cleanInput($_GET["record_round"]) ?? null;             // 경기 라운드
$gender = cleanInput($_GET["record_gender"]) ?? null;           // 경기 성별
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
    $each_group_athletes_id_lane[$result["record_group"]][$result["record_athlete_id"]][] = $result["record_order"];    // [1조][22232][2번 레인]
}

console_log($each_group_athletes_id_lane);


// m조에 대해
$first_index_each_group_athletes_id_lane = array_key_first($each_group_athletes_id_lane);
$last_index_each_group_athletes_id_lane = array_key_last($each_group_athletes_id_lane);
for ($i = $first_index_each_group_athletes_id_lane; $i <= $last_index_each_group_athletes_id_lane; $i++) {
    // each_group_athletes_id[][] :: m조에 편성되어있는 n명의 선수들 id값
    $each_group_athletes_id = array_keys($each_group_athletes_id_lane[$i]);
    // each_group_athletes_lane[][] :: m조에 편성되어있는 n명의 선수들 레인값
    $each_group_athletes_lane = array_values($each_group_athletes_id_lane[$i]);


    console_log($each_group_athletes_id);
    console_log($each_group_athletes_lane);



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
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script type="text/javascript" src="./assets/js/jquery-1.12.4.min.js"></script>
    <link rel="stylesheet" href="./assets/css/xeicon.min.css">
    <link rel="stylesheet" href="./assets/css/swiper.min.css">
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <script type="text/javascript" src="./assets/js/select2.min.js"></script>
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
            $first_index_each_group_athletes_data = array_key_first($each_group_athletes_data);
            $last_index_each_group_athletes_data = array_key_last($each_group_athletes_data);
            for ($i = $first_index_each_group_athletes_data; $i <= $last_index_each_group_athletes_data; $i++) {
            ?>
                <div class="schedule_filed filed_list_item filed2">
                    <div class="profile_logo">
                        <img src="/assets/images/logo.png">
                    </div>
                    <div class="schedule_filed_tit schedule_green">
                        <p class="tit_left_yellow"><?php echo $i ?>조</p>
                    </div>
                    <div class="filed2_form">
                        <table cellspacing="0" cellpadding="0" class="entry_table filed2_swap" id="" name="table_name[]">
                            <thead class="filed_list filed2_list result_table ">
                                <tr>
                                    <th>순서</th>
                                    <th>선수 이름</th>
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
                                            <input type="hidden" name="order[]" id="order[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_lane'] ?>">
                                            <!-- 순서 :: record_order -->
                                            <input type="text" class="number" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_lane'] ?>" name="lane[]" disabled>
                                        </td>
                                        <td>
                                            <!-- 선수 id :: record_athlete_id-->
                                            <input type="hidden" name="athlete_id[]" id="athlete_id[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_id'] ?>">
                                            <input type="text" name="name[]" value="<?php echo $each_group_athletes_data[$i][$j]['athlete_name'] ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            <?php
                            }
                            // END FOR (n명)
                            ?>
                        </table>
                        <div class="filed_BTN2">
                            <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn"><i class="xi-minus"></i></button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn"><i class="xi-plus"></i></button>
                        </div>
                    </div>
                </div>
            <?php
            }
            // END FOR (m조)
            ?>
            <!-- 추가버튼 시, 예제코드 -->
            <!-- 추가 버튼을 누르면 이와 같이 select2를 사용한 select > option 을 생성해내야 함 -->
            <!-- <div class="schedule_filed filed_list_item">
                <div class="profile_logo">
                    <img src="/assets/images/logo.png">
                </div>
                <div class="schedule_filed_tit schedule_green">
                    <p class="tit_left_yellow">1조</p>
                </div>
                <div class="filed2_form">
                    <table cellspacing="0" cellpadding="0" class="filed2_Table " id="" name="table_name[]">
                        <thead class="filed_list filed2_list">
                            <tr>
                                <th>순서</th>
                                <th>선수 이름</th>
                            </tr>
                            <tr class="filed2_bottom">
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody class="grouping_body">
                            <tr>
                                <td><input type="text" class="number" value="1" name="lane[]" disabled></td>
                                <td>
                                    <div id="copy-value">
                                        <select class='select-box' name="athlete" onchange="select_change_listener()" required class="select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                            <option value="" disabled="" selected="">선수 선택</option>
                                            <option value="84">
                                                Abdulrahman Ali (MYS)(WVY)
                                            </option>
                                            <option value="71">
                                                Aldrin Jeswin (NPL)(DVU)
                                            </option>
                                            <option value="81">
                                                Aneed Ameer Shakir (IRN)(HYK)
                                            </option>
                                            <option value="70">
                                                Chanda Km (MYS)(HGD)
                                            </option>
                                            <option value="85">
                                                Chen Kuei Ru (JPN)(KIK)
                                            </option>
                                            <option value="79">
                                                Cheung Wang Fung (CHN)(SUI)
                                            </option>
                                            <option value="3">
                                                choi KYUNG (MYS)(KUFF)
                                            </option>
                                            <option value="73">
                                                Chopra Neeraj (JPN)(SDFS)
                                            </option>
                                            <option value="75">
                                                Hong Kit Yeung (BTN)(SSY)
                                            </option>
                                            <option value="87">
                                                Hsieh Hsi En (LKA)(WER)
                                            </option>
                                            <option value="80">
                                                Kim Doyeon (KOR)(SGR)
                                            </option>
                                            <option value="13">
                                                kristina OVCHINNI (LBN)(BEFF)
                                            </option>
                                            <option value="14">
                                                krithana RAMASA (MMR)(NMN)
                                            </option>
                                            <option value="16">
                                                lai YIU (CHN)(CCC)
                                            </option>
                                            <option value="77">
                                                Li Tsz To (JPN)(KRF)
                                            </option>
                                            <option value="86">
                                                Li Yun Chen (BTN)(FGU)
                                            </option>
                                            <option value="76">
                                                Lin Mingfu (MMR)(DKU)
                                            </option>
                                            <option value="74">
                                                Lui Lai Yiu (NPL)(SSI)
                                            </option>
                                            <option value="23">
                                                Mudha Alshammary F (MYS)(KUFF)
                                            </option>
                                            <option value="91">
                                                Mudha Alshammary M (MYS)(KUFF)
                                            </option>
                                            <option value="83">
                                                Nayef Hussein (NPL)(SCH)
                                            </option>
                                            <option value="82">
                                                Obaid Mohamed Nahith (MDV)(CAK)
                                            </option>
                                            <option value="68">
                                                Paul Eldhose (PHL)(DCS)
                                            </option>
                                            <option value="66">
                                                Pha Aphay Silina (LBN)(XDO)
                                            </option>
                                            <option value="67">
                                                Phompakdy Sorsy (KGZ)(DSS)
                                            </option>
                                            <option value="69">
                                                Rani Annu (CHN)(TDV)
                                            </option>
                                            <option value="65">
                                                Rotundo Manuela (IRN)(DSA)
                                            </option>
                                            <option value="72">
                                                Yarraji Jyothi (KAZ)(DSC)
                                            </option>
                                            <option value="64">
                                                Zamora Cristhian (IDN)(GNF)
                                            </option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="playerid[]" value="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="filed_BTN2">
                        <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN delete-column-btn"><i class="xi-minus"></i></button>
                        <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN add-column-btn"><i class="xi-plus"></i></button>
                    </div>
                </div>
            </div> -->
            <input type="hidden" name="count" value="<?php echo $group_count ?>">
            <input type="hidden" name="round" value="<?php echo $round ?>">
            <input type="hidden" name="gender" value="<?php echo $gender ?>">
            <input type="hidden" name="sport_code" value="<?php echo $sport_code ?>">
            <input type="hidden" name="sport_category" value="<?php echo $category ?>">
            <button type="submit" class="changePwBtn defaultBtn">만들기</button>
        </div>
    </form>
</div>


<script>
    $("select[name=athlete]").select2();
</script>
<script src="assets/js/main.js?ver=13"></script>
</body>

</html>