<?
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
    require_once "action/module/entry_judge_search.php";
    // 로그 기능
    require_once "backheader.php";

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

    $columnStartsWith = "judge_";
    if (isset($categoryValue) && isset($orderValue)) {
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $sql = $sql . $sql_order;
    }
    zend_version()
    
?>
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
</head>

<body>
    <!-- header -->
    <? require_once 'header.php'; ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank refereeList defaultList">
                <div class="MainRank_tit">
                    <h1>심판 목록<i class="xi-group group"></i></h1>
                </div>
                <div class="searchArea">
                    <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                        <div class="page_size">
                            <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize"
                                class="changePageSize">
                                <option value="non" hidden="">페이지</option>
                                <?php
                                $get_judge_count_sql = "SELECT COUNT(judge_id) AS judge_count FROM list_judge;";
                                $judge_count_result = $db->query($get_judge_count_sql);
                                $judge_count_row = mysqli_fetch_array($judge_count_result);
                                $size_of_all = $judge_count_row["judge_count"];
                                foreach ($pageSizeOption as $size) {
                                    echo '<option value="' . $size . '"' . ($isPageSizeChecked[$size] ?? NULL) . ">" . $size . "개씩</option>\"";
                                }
                                echo '<option value="' . $size_of_all . '"' . ($isPageSizeChecked[$size_of_all] ?? NULL) . ">모두</option>\"";
                                ?>
                            </select>
                        </div>

                        <div class="selectArea defaultSelectArea">
                            <div class="defaultSelectBox">
                                <select title="국가" name="judge_country">
                                    <option value='non' hidden="">국가</option>
                                    <option value="non">전체</option>
                                    <?
                                    foreach ($judge_country_dic as $key => $value) {
                                        echo "<option value=$value" . $isCountrySelected[$value] . ">$key</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="소속" name="judge_division">
                                    <option value="non" hidden="">소속</option>
                                    <option value="non">전체</option>
                                    <?
                                    foreach ($judge_division_dic as $key) {
                                        echo "<option value=$key" . $isDivisionSelected[$key] . ">$key</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="성별" name="judge_gender">
                                    <option value="non" hidden="">성별</option>
                                    <option value="non">전체</option>
                                    <?
                                    foreach ($judge_gender_dic as $key) {
                                        $gender = ($key == 'm') ? '남성' : '여성';
                                        echo "<option value=$key" . $isGenderSelected[$key] . ">$gender</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="defaultSelectBox">
                                <select title="예정경기" name="judge_schedule">
                                    <option value='non' hidden="">예정경기</option>
                                    <option value="non">전체</option>
                                    <?php
                                        $events = array_unique($categoryOfSports_dic);
                                        foreach ($events as $e) {
                                            echo "<optgroup label=\"$e\">";
                                            $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                            foreach ($sportsOfTheEvent as $s) {
                                                echo '<option value="' . $s . '"' . ($isSportsSelected[$s] ?? NULL) . ">" . $sport_dic[$s] . "</option>\"";
                                            }
                                            echo "</optgroup>";
                                        }
                                        ?>
                                </select>
                            </div>
                            <div class="search">
                                <input type="hidden" name="page_size" value="<?= $pagesizeValue; ?>">
                                <!-- +)검색할 때도 페이지 사이즈 유지하기 위해서 위에 추가해야 됨. -->
                                <input type="text" id="search" class="defaultSearchInput" name="judge_name"
                                    placeholder="이름을 입력해주세요" maxlength="30"
                                    value="<?php echo isset($searchValue["judge_name"]) ? $searchValue["judge_name"] : ''; ?>">

                                <button name="search" value=search class="defaultSearchBth" type="submit"><i
                                        class="xi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="box_table">
                    <colgroup>
                        <col width="auto">
                        <col width="auto">
                        <col width="auto">
                        <col width="15%">
                        <col width="auto">
                        <col width="auto">
                        <col width="auto">
                        <col width="auto">
                        <col width="auto">
                        <col width="auto">
                        <col width="8%">
                        <col width="8%">
                        <col width="5%">
                        <?if (authCheck($db, "authEntrysUpdate")) {?>
                        <col width="5%">
                        <?}?>
                        <?if (authCheck($db, "authEntrysDelete")) {?>
                        <col width="5%">
                        <?}?>
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">
                                <input type="checkbox" name="checkAll" id="checkAll" onclick="toggle(this)">
                            </th>
                            <th scope="col">AD카드</th>
                            <th scope="col"><a href="<?= Get_Sort_Link("id", $pageValue, $link, $orderValue) ?>">번호</a>
                            </th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("name", $pageValue, $link, $orderValue) ?>">이름</a></th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("country", $pageValue, $link, $orderValue) ?>">국가</a></th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("division", $pageValue, $link, $orderValue) ?>">소속</a></th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("gender", $pageValue, $link, $orderValue) ?>">성별</a></th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("birth", $pageValue, $link, $orderValue) ?>">생년월일</a></th>
                            <th scope="col"><a href="<?= Get_Sort_Link("age", $pageValue, $link, $orderValue) ?>">나이</a>
                            </th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("duty", $pageValue, $link, $orderValue) ?>">직무</a></th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("schedule", $pageValue, $link, $orderValue) ?>">예정경기</a>
                            </th>
                            <th scope="col"><a
                                    href="<?= Get_Sort_Link("attendance", $pageValue, $link, $orderValue) ?>">확정경기</a>
                            </th>
                            <th scope="col">보기</th>
                            <?if (authCheck($db, "authEntrysUpdate")) {?>
                            <th scope="col">수정</th>
                            <?}?>
                            <?if (authCheck($db, "authEntrysDelete")) {?>
                            <th scope="col">삭제</th>
                            <?}?>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                            // 행 번호
                            $num = 0;
                            // if ($result != false && $result->num_rows > 0)
                            //     $doesResultRowsExist = true;
                            // // 조회 결과가 없는 경우 
                            // if (!$doesResultRowsExist) {
                            //     echo '<tr>';
                            //     echo '<td align="center" colspan="15">찾고자 하는 참가자가 없습니다. </td>';
                            //     echo '</tr>';
                            // } else {
                            // 조회 결과가 있는 경우
                            while ($row = mysqli_fetch_array($result)) {
                                // 행 번호 auto increment
                                $num++;
                                // 체크박스
                                echo '<tr';
                                if ($num%2 == 1) echo ' class="Ranklist_Background">'; else echo ">";
                                $judge_id = $row["judge_id"];
                                $checkbox = <<<CHECKBOX
                                <td scope="col">
                                    <input class="entry_check" type="checkbox" value="$judge_id" id="check" name="checked[]">
                                </td>
                                CHECKBOX;
                                echo $checkbox;
                                // AD카드 발급 여부
                                echo htmlspecialchars($row["judge_isIssued"])=='Y' ? 
                                "<td><span class='AD_on'>" . htmlspecialchars($row["judge_isIssued"]) . "</span></td>" :
                                "<td><span class='AD_off'>" . htmlspecialchars($row["judge_isIssued"]) . "</span></td>";
                                // 행 번호
                                echo "<td>" . $num . "</td>";
                                // 참가자 이름
                                echo "<td>" . htmlspecialchars($row["judge_name"]) . "</td>";
                                // 참가자 국가코드
                                echo "<td>" . htmlspecialchars($row["country_code"]) . "</td>";
                                // 참가자 소속
                                echo "<td>" . htmlspecialchars($row["judge_division"]) . "</td>";
                                // 참가자 성별
                                echo "<td>";
                                echo htmlspecialchars($row["judge_gender"] == "m" ? "남" : "여");
                                // 참가자 생년월일
                                echo "</td>";
                                $date = explode('-', $row["judge_birth"]);
                                echo "<td>" . $date[0] . "." . $date[1] . "." . $date[2] . "</td>";
                                // 참가자 나이
                                echo "<td>" . htmlspecialchars($row["judge_age"]) . "</td>";
                                // 참가자 직무
                                echo "<td>" . htmlspecialchars($row["judge_duty"]) . "</td>";
                                // 참가자 참가 경기
                                echo '<td class="popup_BTN">';
                                $sports = explode(',', $row["judge_schedule"]);
                                if (count($sports) > 1) {
                                    echo htmlspecialchars($judge_sport_dic[$sports[0]]) . " 외 " . (count($sports) - 1) . "개";
                                } else {
                                    echo htmlspecialchars($judge_sport_dic[$sports[0]]);
                                }
                                echo '<div class="item_popup" style="display: none;">';
                                foreach ($sports as $id) {
                                    if ($id == end($sports)) {
                                        echo htmlspecialchars($judge_sport_dic[trim($id)]);
                                    } else {
                                        echo htmlspecialchars($judge_sport_dic[trim($id)]) . ',';
                                    }
                                }
                                echo '</div>';
                                echo "</td>";
                                // 참가자 참석 경기
                                // 클릭 시 모달창으로 보여줄 수 있게 하기
                                echo '<td class="popup_BTN">';
                                $attendingSports = explode(',', $row["judge_attendance"]);
                                if (hasSearchedValue($attendingSports)) {
                                    if (count($attendingSports) > 1) {
                                        echo htmlspecialchars($judge_sport_dic[$attendingSports[0]]) . " 외 " . (count($attendingSports) - 1) . "개";
                                    } else {
                                        echo htmlspecialchars($judge_sport_dic[$attendingSports[0]]);
                                    }
                                } else {
                                    echo htmlspecialchars(" - ");
                                }
                                echo '<div class="item_popup" style="display: none;">';
                                if (hasSearchedValue($attendingSports)) {
                                    foreach ($attendingSports as $attend) {
                                        if ($attend == end($attendingSports)) {
                                            echo htmlspecialchars($judge_sport_dic[trim($attend)]);
                                        } else {
                                            echo htmlspecialchars($judge_sport_dic[trim($attend)]) . ',';
                                        }
                                    }
                                } else {
                                    echo htmlspecialchars(" - ");
                                }
                                echo '</div>';
                                echo "</td>";
                                // 참가자 상세 보기
                                echo "<td>";
                                echo "<input type='button' onclick=" . "\"createPopupWin('entry_judge_info.php?id=" . $row["judge_id"] . "'" . ",'상세내용 보기',1100,900);\"" . "value='보기' class='BTN_DarkBlue defaultBtn'>";
                                echo "</td>";
                                // 참가자 수정
                                echo "<td>";
                                if (authCheck($db, "authEntrysUpdate")) {  
                                    echo "<input type='button' onclick=" . "updatePop(" . $row["judge_id"] . ",'judge_id',\"entry_judge_modify.php\")" . " value='수정' class='BTN_Blue defaultBtn'>";
                                }
                                echo "</td>";
                                // 참가자 삭제
                                if (authCheck($db, "authEntrysDelete")) {  
                                    echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["judge_id"] . "','judge')" . " value='삭제' class='BTN_Red defaultBtn'></td>";
                                }
                                echo '</tr>';
                            }
                            // }
                            // 삭제 버튼 입력 컨트롤러
                            if (isset($_POST["judge_delete"])) {
                                $sql = "SELECT * FROM list_judge WHERE judge_id=?";
                                $stmt = $db->prepare($sql);
                                $stmt->bind_param("s", $_POST["judge_delete"]);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = mysqli_fetch_array($result);
                                $id = $row["judge_id"];
                                $name = $row["judge_name"];
                                $country = $row["judge_country"];
                                // 로그 생성
                                logInsert($db, $_SESSION['Id'], '심판 삭제', $id . "-" . $name . "-" . $country);
                                $sql = "DELETE FROM list_judge WHERE judge_id=?";
                                $stmt = $db->prepare($sql);
                                $stmt->bind_param("s", $_POST["judge_delete"]);
                                $stmt->execute();
                                echo "<script>location.href='./entry_judge.php';</script>";
                            }
                            ?>
                    </tbody>
                </table>
                <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <input type="button" onclick="issueId('./entry_judge_issue.php','judge_id');" value="ID발급"
                            class="defaultBtn BIG_btn2 ID_Print">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $sql ?>" hidden />
                            <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                            <input type="text" name="role" value="judge" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                        <form action="./excel_to_db.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file" id="upload_judge" onchange="this.form.submit()" accept=".csv"
                                hidden />
                            <input type="text" name="role" value="judge" hidden />
                            <label for="upload_judge" class="defaultBtn BIG_btn2 excel_input">엑셀
                                입력</label>
                        </form>
                        <button type="button" class="defaultBtn excel_form BIG_btn2">엑셀 양식</button>
                    </div>
                    <div class="registrationBtn">
                        <input class="defaultBtn BIG_btn BTN_Blue" type="button"
                            onclick="createPopupWin('./entry_judge_input.php','등록', 1100, 900);" value="등록">
                    </div>
                </div>
                <div class="page">
                    <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link)?>
                </div>
            </div>
        </div>
    </div>


    <script src="/assets/js/main.js?ver=6"></script>
</body>



</html>