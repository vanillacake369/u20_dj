<?php
require_once "head.php";
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once "includes/auth/config.php";
// 국가,종목,지역,직무에 대한 매핑구조
require_once "action/module/dictionary.php";
// 페이징 기능
require_once "action/module/pagination.php";
// 외부 공격 방지 기능
require_once "security/input_filtering.php";
// 검색 기능
require_once "action/module/entry_athlete_search.php";
// 로그 기능
require_once "backheader.php";
require_once "console_log.php";

if (!authCheck($db, "authEntrysRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
$pageValue = getPageValue($_GET["page"] ?? NULL);
$categoryValue = getCategoryValue($_GET["order"] ?? NULL);
$orderValue = getOrderValue($_GET["sc"] ?? NULL);
$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

$columnStartsWith = "athlete_";
if (isset($categoryValue) && isset($orderValue)) {
    $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
    $sql = $sql . $sql_order;
}
?>
<script type="text/javascript" src="./assets/js/jquery-1.12.4.min.js"></script>
</head>

<body>
    <!-- header -->
    <?php require_once "header.php"; ?>

    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank athleteList defaultList">
                <div class="MainRank_tit">
                    <h1>선수 목록<i class="xi-group group"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize" class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                $get_athlete_count_sql = "SELECT COUNT(athlete_id) AS athlete_count FROM list_athlete;";
                                $athlete_count_result = $db->query($get_athlete_count_sql);
                                $athlete_count_row = mysqli_fetch_array($athlete_count_result);
                                $size_of_all = $athlete_count_row["athlete_count"];
                                foreach ($pageSizeOption as $size) {
                                    echo '<option value="' . $size . '"' . ($isPageSizeChecked[$size] ?? NULL) . ">" . $size . "개씩</option>\"";
                                }
                                echo '<option value="' . $size_of_all . '"' . ($isPageSizeChecked[$size_of_all] ?? NULL) . ">모두</option>\"";
                                ?>
                            </select>
                        </div>
                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="국가" name="athlete_country">
                                    <option value='non' hidden="">국가</option>
                                    <option value="non">전체</option>
                                    <?php
                                    foreach ($athlete_country_dic as $key => $value) {
                                        echo '<option value="' . $value . '" ' . ($isCountrySelected[$value] ?? NULL) . '>' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select class="d_select" title="지역" name="athlete_region" style="width: 8em;">
                                    <option value="non" hidden="">지역</option>
                                    <option value="non">전체</option>
                                    <?php
                                    foreach ($athlete_region_dic as $key) {
                                        echo '<option value="' . $key . '" ' . ($isRegionSelected[$key] ?? NULL) . '>' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select class="d_select" title="소속" name="athlete_division" style="width: 8em;">
                                    <option value="non" hidden="">소속</option>
                                    <option value="non">전체</option>
                                    <?php
                                    foreach ($athlete_division_dic as $key) {
                                        echo '<option value="' . $key . '" ' . ($isDivisionSelected[$key] ?? NULL) . '>' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select class="d_select" title="성별" name="athlete_gender" style="width:5em;">
                                    <option value="non" hidden="">성별</option>
                                    <option value="non">전체</option>
                                    <?php
                                    foreach ($athlete_gender_dic as $key) {
                                        $gender = ($key == 'm') ? '남성' : '여성';
                                        echo '<option value="' . $key . '" ' . ($isGenderSelected[$key] ?? NULL) . '>' . $gender . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select class="d_select" title="예정경기" name="athlete_schedule" style="width: 10em;">
                                    <option value='non' hidden="">예정경기</option>
                                    <option value="non">전체</option>
                                    <?php
                                    $events = array_unique($categoryOfSports_dic);
                                    foreach ($events as $e) {
                                        echo "<optgroup label=\"$e\">";
                                        $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                        foreach ($sportsOfTheEvent as $s) {
                                            echo '<option value="' . $s . '" ' . ($isSportsSelected[$s] ?? NULL) . '>' . $sport_dic[$s] . '</option>';
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                                <input type="text" id="search" class="defaultSearchInput" name="athlete_name" placeholder="이름을 입력해주세요" maxlength="30" value="<?php echo isset($searchValue["athlete_name"]) ? $searchValue["athlete_name"] : ''; ?>">
                                <button type="submit" class="defaultSearchBth" title="검색"><i class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col width="4%">
                        <col width="4%">
                        <col width="4%">
                        <col width="auto">
                        <col width="5%">
                        <col width="6%">
                        <col width="6%">
                        <col width="6%">
                        <col width="10%">
                        <col width="4%">
                        <col width="12%">
                        <col width="12%">
                        <col width="5%">
                        <?php
                        if (authCheck($db, "authEntrysUpdate")) { ?>
                            <col width="5%">
                        <?php } ?>
                        <?php if (authCheck($db, "authEntrysDelete")) { ?>
                            <col width="5%">
                        <?php } ?>


                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">
                                <input type="checkbox" name="checkAll" id="checkAll" onclick="toggle(this)">
                            </th>
                            <th scope="col">AD카드</th>
                            <th scope="col"><a href="<?= Get_Sort_Link("id", $pageValue, $link, $orderValue) ?>">번호</a>
                            </th>
                            <th scope="col"><a href="<?= Get_Sort_Link("name", $pageValue, $link, $orderValue) ?>">이름</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("country", $pageValue, $link, $orderValue) ?>">국가</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("region", $pageValue, $link, $orderValue) ?>">지역</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("division", $pageValue, $link, $orderValue) ?>">소속</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("gender", $pageValue, $link, $orderValue) ?>">성별</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("birth", $pageValue, $link, $orderValue) ?>">생년월일</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("age", $pageValue, $link, $orderValue) ?>">나이</a>
                            </th>
                            <th scope="col"><a href="<?= Get_Sort_Link("schedule", $pageValue, $link, $orderValue) ?>">예정경기</a>
                            </th>
                            <th scope="col"><a href="<?= Get_Sort_Link("attendance", $pageValue, $link, $orderValue) ?>">확정경기</a>
                            </th>
                            <th scope="col">보기</th>
                            <?php if (authCheck($db, "authEntrysUpdate")) { ?>
                                <th scope="col">수정</th>
                            <?php } ?>
                            <?php if (authCheck($db, "authEntrysDelete")) { ?>
                                <th scope="col">삭제</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                        // 행 번호
                        $num = 0;
                        // 조회 결과가 있는 경우
                        while ($row = mysqli_fetch_array($result)) {
                            // 행 번호 auto increment
                            $num++;
                            // 체크박스
                            echo '<tr';
                            if ($num % 2 == 1) echo ' class="Ranklist_Background">';
                            else echo ">";
                            $athlete_id = $row["athlete_id"];
                            $checkbox = <<<CHECKBOX
                                <td scope="col">
                                    <input class="entry_check" type="checkbox" value="$athlete_id" id="check" name="checked[]">
                                </td>
                                CHECKBOX;
                            echo $checkbox;
                            // AD카드 발급 여부
                            echo htmlspecialchars($row["athlete_isIssued"]) == 'Y' ?
                                "<td><span class='AD_on'>" . htmlspecialchars($row["athlete_isIssued"]) . "</span></td>" :
                                "<td><span class='AD_off'>" . htmlspecialchars($row["athlete_isIssued"]) . "</span></td>";
                            // 행 번호
                            echo "<td>" . $athlete_id . "</td>";
                            // 참가자 이름
                            echo "<td>" . htmlspecialchars($row["athlete_name"]) . "</td>";
                            // 참가자 국가코드
                            echo "<td>" . htmlspecialchars($row["country_code"]) . "</td>";
                            // 참가자 지역
                            echo "<td>" . htmlspecialchars($row["athlete_region"]) . "</td>";
                            // 참가자 소속
                            echo "<td>" . htmlspecialchars($row["athlete_division"]) . "</td>";
                            // 참가자 성별
                            echo "<td>";
                            echo htmlspecialchars($row["athlete_gender"] == "m" ? "남" : "여");
                            // 참가자 생년월일
                            echo "</td>";
                            $date = explode('-', $row["athlete_birth"]);
                            echo "<td>" . $date[0] . "." . $date[1] . "." . $date[2] . "</td>";
                            // 참가자 나이
                            echo "<td>" . htmlspecialchars($row["athlete_age"]) . "</td>";
                            // 참가자 참가 경기
                            echo '<td class="popup_BTN">';
                            $sports = explode(',', $row["athlete_schedule"]);
                            if (count($sports) > 1) {
                                echo htmlspecialchars($sport_dic[$sports[0]]) . " 외 " . (count($sports) - 1) . "개";
                            } else {
                                echo htmlspecialchars($sport_dic[$sports[0]]);
                            }
                            echo '<div class="item_popup" style="display: none;">';
                            foreach ($sports as $id) {
                                if ($id == end($sports)) {
                                    echo htmlspecialchars($sport_dic[trim($id)]);
                                } else {
                                    echo htmlspecialchars($sport_dic[trim($id)]) . '<br>';
                                }
                            }
                            echo '</div>';
                            echo "</>";
                            // 참가자 참석 경기
                            // 클릭 시 모달창으로 보여줄 수 있게 하기
                            echo '<td class="popup_BTN">';
                            $attendingSports = explode(',', $row["athlete_attendance"]);
                            if (hasSearchedValue($attendingSports)) {
                                if (count($attendingSports) > 1) {
                                    echo htmlspecialchars($sport_dic[$attendingSports[0]]) . " 외 " . (count($attendingSports) - 1) . "개";
                                } else {
                                    echo htmlspecialchars($sport_dic[$attendingSports[0]]);
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }
                            echo '<div class="item_popup" style="display: none;">';
                            if (hasSearchedValue($attendingSports)) {
                                foreach ($attendingSports as $attend) {
                                    if ($attend == end($attendingSports)) {
                                        echo htmlspecialchars($sport_dic[trim($attend)]);
                                    } else {
                                        echo htmlspecialchars($sport_dic[trim($attend)]) . '<br>';
                                    }
                                }
                            } else {
                                echo htmlspecialchars(" - ");
                            }

                            echo '</div>';
                            echo "</td>";
                            // 참가자 상세 보기
                            echo "<td>";
                            echo "<input type='button' onclick=" . "\"createPopupWin('entry_athlete_info.php?id=" . $row["athlete_id"] . "'" . ",'상세내용 보기',1100,1000);\"" . "value='보기' class='BTN_DarkBlue defaultBtn'>";
                            echo "</td>";
                            // 참가자 수정
                            echo "<td>";
                            if (authCheck($db, "authEntrysUpdate")) {
                                echo "<input type='button' onclick=" . "\"createPopupWin('entry_athlete_modify.php?id=" . $row["athlete_id"] . "'" . ",'수정',1100,900);\"" . " value='수정' class='BTN_Blue defaultBtn'>";
                            }
                            echo "</td>";
                            // 참가자 삭제
                            if (authCheck($db, "authEntrysDelete")) {
                                echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["athlete_id"] . "','athlete')" . " value='삭제' class='BTN_Red defaultBtn'></td>";
                            }
                            echo '</tr>';
                        }
                        // 삭제 버튼 입력 컨트롤러
                        if (isset($_POST["athlete_delete"])) {
                            $sql = "SELECT * FROM list_athlete WHERE athlete_id=?";
                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("s", $_POST["athlete_delete"]);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = mysqli_fetch_array($result);
                            $id = $row["athlete_id"];
                            $name = $row["athlete_name"];
                            $country = $row["athlete_country"];
                            // 로그 생성
                            logInsert($db, $_SESSION['Id'], '선수 삭제', $id . "-" . $name . "-" . $country);
                            $sql = "DELETE FROM list_athlete WHERE athlete_id=?";
                            $stmt = $db->prepare($sql);
                            $stmt->bind_param("s", $_POST["athlete_delete"]);
                            $stmt->execute();

                            echo "<script>location.href='./entry_athlete.php';</script>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <button type="button" class="defaultBtn ID_Print BIG_btn2" onclick="issueId('./entry_athlete_issue.php','athlete_id');">ID발급</button>
                        <form action="execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?= $sql ?>" hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="athlete" hidden />
                            <label for="execute_excel" class="defaultBtn excel_Print BIG_btn2">엑셀 출력</label>
                        </form>
                        <form action="./excel_to_db.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file" id="upload_judge" onchange="this.form.submit()" accept=".csv" hidden />
                            <input type="text" name="role" value="athlete" hidden />
                            <label for="upload_judge" class="defaultBtn excel_input BIG_btn2">엑셀 입력</label>
                        </form>
                        <button type="button" onclick="window.location.href='./assets/excel/athlete_example.CSV'" class="defaultBtn excel_form BIG_btn2">엑셀 양식</button>
                    </div>
                    <div class="registrationBtn">
                        <button type="button" class="defaultBtn BIG_btn BTN_Blue" onclick="createPopupWin('./entry_athlete_input.php','등록', 1100, 900);">등록</button>
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/main.js?v=7"></script>
    <script>
        // active browser에 대한 auto refresh 함수
        reloadWhenVisibilityChange();
    </script>
</body>

</html>