<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
require_once "action/module/pagination.php";
require_once "action/module/dictionary.php";

pageAuthCheck($db, 'authSchedulesRead');

$searchValue = [];
$searchValue["schedule_sports"] = getSearchValue($_GET["schedule_sports"] ?? NULL);
$searchValue["schedule_name"] = getSearchValue($_GET["schedule_name"] ?? NULL);
$searchValue["schedule_location"] = getSearchValue($_GET["schedule_location"] ?? NULL);
$searchValue["schedule_gender"] = getSearchValue($_GET["schedule_gender"] ?? NULL);
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

$page_list_size = 10;
$link = "";

$tableName = "list_schedule";
$columnStartsWith = "schedule_";
$id = $columnStartsWith . "name";

$sql_where = "";
// $sql_where = " WHERE schedule_division='b'";
$sql_order = " ORDER BY $id DESC ";
$sql_like = "";
$page_list_count = ($pageValue - 1) * $pagesizeValue;

$sql = "SELECT distinct
        (SELECT sports_category FROM list_sports WHERE schedule_sports=sports_code) AS category, 
        schedule_name,schedule_sports,schedule_gender,schedule_round,record_state,record_status,
        schedule_location,schedule_start,schedule_date
        FROM list_schedule JOIN list_record ON schedule_sports=record_sports AND schedule_gender=record_gender AND schedule_round=record_round";

$uri_array = array();
$bindarray = array();
$keyword = array();

if (count($searchValue)) {
    $keyword = array();
    $bindarray = array();

    if ((!is_null($searchValue["schedule_name"])) && ($searchValue["schedule_name"] != "non")) {
        $uri_array["schedule_name"] = $searchValue["schedule_name"];
        array_push($bindarray, $searchValue["schedule_name"]);
        array_push($keyword, "schedule_sports=?");
    }
    if ((!is_null($searchValue["schedule_location"])) && ($searchValue["schedule_location"] != "non")) {
        $uri_array["schedule_location"] = $searchValue["schedule_location"];
        array_push($bindarray, $searchValue["schedule_location"]);
        array_push($keyword, "schedule_location=?");
    }
    if ((!is_null($searchValue["schedule_gender"])) && ($searchValue["schedule_gender"] != "non")) {
        $uri_array["schedule_gender"] = $searchValue["schedule_gender"];
        array_push($bindarray, $searchValue["schedule_gender"]);
        array_push($keyword, "schedule_gender=?");
    }
    if ((!is_null($searchValue["schedule_sports"])) && ($searchValue["schedule_sports"] != "non")) {
        $uri_array["schedule_sports"] = $searchValue["schedule_sports"];
        array_push($bindarray, $searchValue["schedule_sports"]);
        array_push($keyword, "having category=?");
    }
    for ($i = 0; $i < count($keyword); $i++) {
        if ($keyword[$i] != "having category=?")
            $sql_like = $sql_like . " AND " . $keyword[$i];
        else
            $sql_like = $sql_like . ' ' . $keyword[$i];
    }
    $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
    if (!empty($uri_array)) {
        $link = addToLink($link, array_keys($uri_array), $uri_array);
    }
}

if (isset($pagesizeValue)) {
    $link = addToLink($link, "&page_size=", $pagesizeValue);
}

if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $link = addToLink($link, "&order=", $categoryValue);
    $link = addToLink($link, "&sc=", $orderValue);
}

$sql = $sql . $sql_where;
$excel = $sql . $sql_order;


if (isset($searchValue) && ($searchValue["schedule_sports"] != "non" || $searchValue["schedule_name"] != "non" || $searchValue["schedule_location"] != "non" || $searchValue["schedule_gender"] != "non")) {
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $count = $stmt->get_result();

    $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
    $stmt = $db->prepare($sql);
    if (count($bindarray) > 0) {
        $types = str_repeat('s', count($bindarray));
        $stmt->bind_param($types, ...$bindarray);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $count = $db->query($sql);

    $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
    $result = $db->query($sql);
}

$total_count = mysqli_num_rows($count);
?>

</head>

<body>
    <!-- header -->
    <?php require_once "header.php"; ?>

    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank scheduleList defaultList">
                <div class="MainRank_tit">
                    <h1>일정 목록<i class="xi-calendar calendar"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                    echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10개씩</option>';
                                    echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15개씩</option>';
                                    echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20개씩</option>';
                                    echo '<option value="100"' . ($pagesizeValue == 100 ? 'selected' : '') . '>100개씩</option>';
                                    if ($total_count != 0){
                                        echo '<option value="' . $total_count . "\">모두</option>\"";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="구분" name="schedule_sports">
                                    <option value="non" hidden="">구분</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $sSql = "SELECT distinct sports_category FROM list_sports;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['sports_category'] . ' ' . ($searchValue["schedule_sports"] == $sRow['sports_category'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_category']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="경기종목" name="schedule_name">
                                    <option value="non" hidden="">
                                        경기 종목</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $events = array_unique($categoryOfSports_dic);
                                    foreach ($events as $e) {
                                        echo "<optgroup label=\"$e\">";
                                        $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                        foreach ($sportsOfTheEvent as $s) {
                                            $get_schedule_name = $_GET['schedule_name'] ?? NULL;
                                            echo "<option value= '$s' " . ($s === $get_schedule_name ? 'selected' : '') . ">" . $sport_dic[$s] . "</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="장소" name="schedule_location">
                                    <option value="non" hidden="">경기 장소</option>
                                    <?php
                                    $sSql = "SELECT distinct schedule_location FROM list_schedule;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value='";
                                        echo $sRow['schedule_location'];
                                        echo "' " . ($searchValue["schedule_location"] == $sRow['schedule_location'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['schedule_location']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="성별" name="schedule_gender">
                                    <option value="non" hidden="">성별</option>
                                    <?php
                                    $sSql = "SELECT distinct schedule_gender FROM list_schedule;";
                                    $sResult = $db->query($sSql);
                                    while ($sRow = mysqli_fetch_array($sResult)) {
                                        echo "<option value=" . $sRow['schedule_gender'] . ' ' . ($searchValue["schedule_gender"] == $sRow['schedule_gender'] ? 'selected' : '') . ">" . ($sRow['schedule_gender'] == 'm' ? '남' : ($sRow['schedule_gender'] == 'f' ? '여' : '혼성')) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <button class="SearchBtn" type="submit" name="search" title="검색"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <thead class="table_head entry_table">
                    <tr>
                            <th scope="col">구분</th>
                            <th colspan="3" scope="col">구분</th>
                            <th colspan="3" scope="col"><a
                                    href="<?= Get_Sort_Link("name", $pageValue, $link, $orderValue) ?>">경기 종목</a>
                            </th>
                            <th colspan="2" scope="col">성별</th>
                            <th colspan="2" scope="col">라운드</th>
                            <th colspan="4" scope="col">경기 장소</th>
                            <th colspan="3" scope="col">
                                시작 시간
                            </th>

                            <th colspan="4" scope="col">
                                진행 상태
                            </th>
                            <th colspan="3" scope="col">날짜</th>
                            <th colspan="3" scope="col">경기 정보</th>
                            <?php
                            if (authCheck($db, "authSchedulesUpdate")) {  ?>
                            <th colspan="2" scope="col">수정</th>
                            <?php }
                            if (authCheck($db, "authSchedulesDelete")) {  ?>
                            <th colspan="2" scope="col">삭제</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                    <?php
                            $i = 1;
                            $j = 0;
                            $num = 0;
                            $can=array();
                            while ($row = mysqli_fetch_array($result)) {
                            $num++;
                            $where = '';
                            $tmp= $row["schedule_name"].$row["schedule_round"].$row['schedule_gender'];
                            if(in_array($tmp,$can)){
                                $i-=1;
                                continue;
                            }else{
                                $can[]=$tmp;
                            }
                            echo '<tr';
                            if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                            else echo '>';
                            echo "<td>" . $i . "</td>";
                            //@Potatoeunbi
                            //경기에 따른 버튼 색 구분
                            echo "<td colspan='3' scope='col'>";
                            echo "<a class=";
                            echo $row['category'] == '필드경기' ? 'btn_field' : ($row['category'] == '트랙경기' ? 'btn_track' : 'btn_multi');
                            echo ">" . htmlspecialchars($row["category"]) . "</a>";
                            echo "</td>";

                            echo "<td colspan='3' scope='col'>" . htmlspecialchars($row["schedule_name"]) . "</td>";
                            echo "<td colspan='2' scope='col'>";

                            //@Potatoeunbi
                            //경기 성별에 따른 색 구분
                            echo "<a class=";
                            echo $row['schedule_gender'] == 'm' ? 'btn_man' : ($row['schedule_gender'] == 'f' ? 'btn_girl' : 'btn_hon');
                            echo ">" . htmlspecialchars($row['schedule_gender'] == 'm' ? '남' : ($row['schedule_gender'] == 'f' ? '여' : '혼성')) . "</a>";


                            //@Potatoeunbi
                            //경기 라운드, 경기 상태의 enum 형태에 따라 올바르게 출력
                            echo "</td>";
                            echo "<td colspan='2' scope='col'>" . htmlspecialchars($row["schedule_round"] == 'final' ? '결승' : ($row["schedule_round"] == 'semi-final' ? '준결승' : ($row["schedule_round"] == 'qualification' ? '예선' : ($row["schedule_round"] == 'quarterfinal' ? '준준결승' : $row["schedule_round"])))) . "</td>";
                            echo "<td colspan='4' scope='col'>" . htmlspecialchars($row["schedule_location"]) . "</td>";
                            echo "<td colspan='3' scope='col'>" . htmlspecialchars(date("H:i", strtotime($row["schedule_start"]))) . "</td>";
                            echo "<td colspan='4' scope='col'>" . ($row["record_state"] == 'c' ? 'cancel' : ($row["record_status"] == 'n' ? 'start list' : ($row["record_status"] == 'o' ? 'official result' : 'live result'))) . "</td>";
                            echo "<td colspan='3' scope='col'>" . htmlspecialchars(date("Y-m-d", strtotime($row["schedule_date"]))) . "</td>";
                            echo "<td colspan='3' scope='col'>";

                            //@Potatoeunbi
                            //310~340 line의 if ~ else if : 해당 경기에 list_record가 생성이 안 되어 있을 경우를 고려.
                            //생성되어 있으면 상세 정보 버튼, 생성 안 되어 있으면 조편성하기 버튼
                                //@Potatoeunbi
                                //상세 정보 버튼 - 해당 경기에 맞게 url 연결
                                // 모든 트랙 경기 : sport_schedule_track.php
                                // 높이뛰기, 장대높이뛰기를 제외한 필드 경기 : sport_schedule_field.php
                                // 높이뛰기, 장대높이뛰기 : sport_schedule_high_jump.php
                                // 10종 경기 : sport_schedule_mixed10.php
                                // 7종 경기 : sport_schedule_mixed7.php
                                echo "<button type='button' class='BTN_DarkBlue defaultBtn' onclick=\"window.open('";
                                echo ($row['category'] == '필드경기' && $row['schedule_sports'] != 'highjump' && $row['schedule_sports'] != 'polevault' ? 'sport_schedule_field.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].'' : ($row['category'] == '트랙경기' ? 'sport_schedule_track.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].'' : ($row['schedule_sports'] == 'decathlon' ? 'sport_schedule_mixed10.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].'' : ($row['schedule_sports'] == 'heptathlon' ? 'sport_schedule_mixed7.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].'' : 'sport_schedule_high_jump.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].''))));
                                echo '\')";>상세 정보</button>';
                            // else if ($row["schedule_status"] != 'c') {
                            //     //조편성 버튼 
                            //     if (authCheck($db, "authSchedulesCreate")) {
                            //         echo '<button type=\'button\' onclick="createPopupWin(\'sport_schedule_group_org.php?id=' . $row['schedule_id'] . '\',\'창 이름\',900,500)\" class=\'makeGroupBtn defaultBtn\'>조편성하기</button>';
                            //     }
                            // }
                            echo "</td>";
                            echo "<td colspan='2' scope='col'>";

                            //@Potatoeunbi
                            //수정 권한이 있고 official 경기가 아닐 경우에만 수정 버튼이 보이도록 함
                            if (authCheck($db, "authSchedulesUpdate") && ($row['record_status'] != 'o')) {
                            if ($row['record_state'] != 'o') {
                                echo '<button type=\'button\' onclick="createPopupWin(\'sport_schedule_modify.php?sports='.$row['schedule_sports'].'&gender='.$row['schedule_gender'].'&round='.$row['schedule_round'].'\',\'창 이름\',900,900)" class=\'BTN_Blue defaultBtn\'>수정</button>';
                            }
                            echo "</td>";
                            }
                            echo "<td colspan='2' scope='col'>";

                            //@Potatoeunbi
                            //삭제 권한이 있을 경우에만 삭제 버튼이 보이도록 함
                            if (authCheck($db, "authSchedulesDelete")) {
                                echo "<button type='button' onclick=" . "schedule_Delete('".$row['schedule_sports']."','".$row['schedule_gender']."','".$row['schedule_round']."')" . " class='BTN_Red defaultBtn'>삭제</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                            $i++;
                            $j++;
                        }

                        //@Potatoeunbi
                        //삭제 버튼 클릭 시 발생하는 이벤트
                        if (isset($_POST["sports"])&&isset($_POST["gender"])&&isset($_POST["round"])) {

                            //@Potatoeunbi
                            //로그 이력을 위해서 select sql
                            $S_sql = "SELECT (SELECT sports_category FROM list_sports 
                                WHERE schedule_sports=sports_code)AS category,schedule_sports,schedule_name,schedule_gender,schedule_round, schedule_location 
                                FROM list_schedule 
                                WHERE schedule_sports='".$_POST["sports"]."' and schedule_gender='".$_POST["gender"]."' and schedule_round='".$_POST["round"]."';";

                            $S_result = $db->query($S_sql);
                            $S_row = mysqli_fetch_array($S_result);;

                            $condition = '';
                            $whereD = "
                                and s1.schedule_sports=s2.schedule_sports 
                                AND s1.schedule_name=s2.schedule_name 
                                AND s1.schedule_gender=s2.schedule_gender";

                            //@Potatoeunbi
                            //해당 경기의 신기록도 삭제하기 위해서 경기의 끝나는 시간을 다 불러오는 sql문.(대분류, 소분류 모두)
                            $endsql = "select distinct record_end from list_record where record_sports='".$_POST["sports"]."' and record_gender='".$_POST["gender"]."' and record_round='".$_POST["round"]."'";
                            $endresult = $db->query($endsql);
                            while ($endrow = mysqli_fetch_array($endresult)) {
                                //@Potatoeunbi
                                //삭제하려는 경기의 신기록 삭제
                                $worldsql = "DELETE FROM list_worldrecord 
                                            WHERE worldrecord_sports=? 
                                            AND worldrecord_location=? 
                                            AND worldrecord_gender=? 
                                            AND worldrecord_datetime=?;";
                                $worldstmt = $db->prepare($worldsql);
                                $worldstmt->bind_param("ssss", $S_row['schedule_sports'],  $S_row['schedule_location'], $S_row['schedule_gender'], $endrow['record_end']);
                                $worldstmt->execute();
                            }

                            //@Potatoeunbi
                            //삭제하려는 경기의 record 삭제
                            $subsql = "UPDATE list_record SET record_pass ='n',record_official_result=null,record_live_result=null,record_official_record=NULL,record_live_record=NULL,
                            record_multi_record=NULL,record_new='n',record_memo=NULL,record_medal=0,record_wind=NULL,record_weight=NULL,record_status='n',record_reaction_time=NULL,
                            record_start=NULL,record_end=NULL,record_state='n' WHERE record_sports=? AND record_round=? AND record_gender=?";

                            $substmt = $db->prepare($subsql);
                            $substmt->bind_param("sss", $S_row['schedule_sports'],$S_row['schedule_round'], $S_row['schedule_gender']);
                            $substmt->execute();


                            //높이뛰기와 장대높이뛰기일 경우 선수별로 하나의 record투플를 제외한 나머지를 삭제
                            if($S_row['schedule_sports'] =='highjump' || $S_row['schedule_sports'] =='polevault' || $S_row['schedule_round']  =='polevault' || $S_row['schedule_round']  =='highjump'){
                                $distinctsql="DELETE FROM list_record 
                                WHERE record_id IN (
                                  SELECT record_id 
                                  FROM (
                                    SELECT record_id,
                                           ROW_NUMBER() OVER (
                                             PARTITION BY record_sports, record_round, record_gender,record_athlete_id
                                             ORDER BY record_sports
                                           ) AS cnt 
                                 FROM list_record WHERE record_sports=? and record_round=? and record_gender=?
                                  ) AS subquery 
                                  WHERE cnt > 1
                                );";
                                $substmt = $db->prepare($subsql);
                                $substmt->bind_param("sss", $S_row['schedule_sports'],$S_row['schedule_round'], $S_row['schedule_gender']);
                                $substmt->execute();
                            }

                            //@Potatoeunbi
                            //경기 삭제(생성된 대분류, 소분류 모두)
                            $sql = "DELETE FROM list_schedule WHERE schedule_sports=? AND schedule_round=? AND schedule_gender=?;";

                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("sss", $S_row['schedule_sports'],$S_row['schedule_round'], $S_row['schedule_gender']);
                            $stmt->execute();


                            logInsert($db, $_SESSION['Id'], '일정 삭제', $S_row['category'] . '-' . $S_row['schedule_name'] . '-' . $S_row['schedule_gender'] . '-' . $S_row['schedule_round']);
                            echo "<script>location.href='./sport_schedulemanagement.php';</script>";
                        }

                        ?>
                    </tbody>
                </table>

                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="schedule_management" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                        
                    </div>
                    <div class="registrationBtn">
                    <?php
                        if (authCheck($db, "authSchedulesCreate")) { ?>
                        <div class="btn_base base_mar col_right">
                            <button class="defaultBtn BIG_btn BTN_Blue" type="button"
                                onclick="createPopupWin('sport_schedule_input.php','창 이름',900,900)">등록</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
    <script>
    // 일정 생성, 수정 후 부모 페이지(일정 목록 페이지) 자동 리로드
    window.onunload = function() {
        window.opener.location.reload();
    };
    </script>
</body>

</html>