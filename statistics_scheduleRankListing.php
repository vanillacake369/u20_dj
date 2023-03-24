 <?php
    require_once "head.php";

    // 데이터베이스 연결
    require_once "includes/auth/config.php";
    // 국가,종목,지역,직무에 대한 매핑구조
    require_once "action/module/dictionary.php";
    // 페이징 기능
    require_once "action/module/pagination.php";

    // 페이지 관련 변수
    $pageValue = getPageValue($_GET["page"] ?? NULL);
    $categoryValue = getCategoryValue($_GET["order"] ?? NULL);
    $orderValue = getOrderValue($_GET["sc"] ?? NULL);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);
    $page_list_size = 10;
    $link = "";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;

    // SQL 조건문
    $sql = "SELECT DISTINCT
                record_official_result,
                sports_code,
                sports_name,
                GROUP_CONCAT(CONCAT(athlete_name))  AS athlete_name,
        		GROUP_CONCAT(CONCAT(record_official_record))  AS record,
                athlete_country,
                country_name,
                schedule_gender,
                record_official_record,                
                record_wind,                
                record_weight,
                record_memo
            FROM 
                list_record     
            INNER JOIN list_athlete  ON record_athlete_id = athlete_id
            INNER JOIN list_schedule ON schedule_sports = record_sports
            INNER JOIN list_country ON country_code = athlete_country
            INNER JOIN list_sports ON sports_code = schedule_sports";

    $sql_where = " WHERE record_official_result>0 AND schedule_gender = record_gender and schedule_round = 'final'";
    $sql_order = " ORDER BY record_official_result, sports_code, schedule_gender ";
    $sql_like = "";
    $sql_group = "GROUP BY record_official_result, sports_code";

    // GET METHOD로 넘어온 값을 가져옴
    $searchValue = [];
    $searchValue["schedule_gender"] = getSearchValue($_GET["schedule_gender"] ?? NULL);
    $searchValue["sports_code"] = getSearchValue($_GET["sports_code"] ?? NULL);
    $hasSearched = hasSearchedValue($searchValue);
    if ($hasSearched) {
        $hasSearchedAthleteGender = hasSearchedValue($searchValue["schedule_gender"]);
        $hasSearchedScheduleName = hasSearchedValue($searchValue["sports_code"]);
    }

    $uri_array = array();
    $bindarray = array();
    $keyword = array();

    // 검색이 되었다면
    if ($hasSearched) {
        // 성별이 검색되었다면
        if ($hasSearchedAthleteGender) {
            $uri_array["schedule_gender"] = $searchValue["schedule_gender"];
            $isGenderSelected = "'\\\\b" . $searchValue["schedule_gender"] . "\\\\b'";
            $isGenderSelected = "schedule_gender REGEXP" . str_replace('+', '', $isGenderSelected);
            array_push($keyword, $isGenderSelected);
        }
        // 참가예정경기가 검색되었다면
        if ($hasSearchedScheduleName) {
            $uri_array["sports_code"] = $searchValue["sports_code"];
            $isSportsSelected = "'\\\\b" . $searchValue["sports_code"] . "\\\\b'";
            $isSportsSelected = "sports_code REGEXP" . str_replace('+', '', $isSportsSelected);
            array_push($keyword, $isSportsSelected);
        }
        for ($i = 0; $i < count($keyword); $i++) {
            $sql_like = $sql_like . " AND " . $keyword[$i];
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
        $sql_order = makeOrderBy("", $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }

    $sql = $sql . $sql_where . $sql_group;

    if (isset($searchValue)) {
        $stmt = $db->prepare($sql);
        if (count($bindarray) > 0) {
            $types = str_repeat('s', count($bindarray));
            $stmt->bind_param($types, ...$bindarray);
        }
        $stmt->execute();
        $count = $stmt->get_result();

        $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $stmt = $db->prepare($sql_complete);
        if (count($bindarray) > 0) {
            $types = str_repeat('s', count($bindarray));
            $stmt->bind_param($types, ...$bindarray);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $count = $db->query($sql);

        $sql_complete = $sql . " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $result = $db->query($sql_complete);
    }

    // 조회된 모든 row의 수 : total_count
    $total_count = mysqli_num_rows($count);

    $isPageSizeChecked = maintainSelected($_GET["page_size"] ?? null);
    $isGenderSelected = maintainSelected($_GET["schedule_gender"] ?? null);
    $isSportsSelected = maintainSelected($_GET["sports_code"] ?? NULL);

    // SQL문에 ORDER BY 삽입
    if (isset($categoryValue) && isset($orderValue)) {
        $sql_order = makeOrderBy('', $categoryValue, $orderValue);
        $sql = $sql . $sql_order;
    }
    ?>
 <!--Data Tables-->
 <script src="assets/js/jquery-1.12.4.min.js"></script>
 <link rel="stylesheet" type="text/css" href="/assets/DataTables/datatables.min.css" />

 </head>

 <body>
     <!-- header -->
     <?php require_once 'header.php' ?>

     <div class="Area">
         <div class="Wrapper TopWrapper">
             <div class="MainRank coachList defaultList">
                 <div class="MainRank_tit">
                     <h1>경기별 순위보기<i class="xi-equalizer-thin chart"></i></h1>
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
                         <div class="selectArea float_r">
                             <div class="selectArea defaultSelectArea">
                                 <div class="defaultSelectBox">
                                     <select title="성별" name="schedule_gender">
                                         <option value="non" hidden="">성별</option>
                                         <option value="non">전체</option>
                                         <?php
                                            foreach ($schedule_gender_dic as $key) {
                                                if ($key == 'm') {
                                                    $gender = '남성';
                                                } else if ($key == 'f') {
                                                    $gender = '여성';
                                                } else {
                                                    $gender = '혼성';
                                                }
                                                echo '<option value="' . $key . '"' . ($isGenderSelected[$key] ?? NULL) . ">$gender</option>\"";
                                            }
                                            ?>
                                     </select>
                                 </div>
                                 <div class="defaultSelectBox">
                                     <select title="종목" name="sports_code">
                                         <option value='non' hidden="">종목</option>
                                         <option value="non">전체</option>
                                         <?php
                                            $events = array_unique($categoryOfSports_dic);
                                            foreach ($events as $e) {
                                                echo "<optgroup label=\"$e\">";
                                                $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                                                foreach ($sportsOfTheEvent as $a) {
                                                    echo '<option value="' . $a . '"' . ($isSportsSelected[$a] ?? NULL) . ">" . $sport_dic[$a] . "</option>\"";
                                                }
                                                echo "</optgroup>";
                                            }
                                            ?>
                                     </select>
                                 </div>
                                 <div class="search">
                                     <button name="search" value=search class="SearchBtn" type="submit" title="검색"><i class="xi-search"></i></button>
                                 </div>
                             </div>
                     </form>
                 </div>
                 <table class="box_table">
                     <colgroup>
                         <col style="width: auto" />
                         <col style="width: 15%" />
                         <col style="width: 15%" />
                         <col style="width: auto" />
                         <col style="width: auto" />
                         <col style="width: 10%" />
                         <col style="width: 8%" />
                         <col style="width: auto" />
                     </colgroup>
                     <thead class="table_head entry_table">
                         <tr>
                             <th onclick="sortTable(0)"><a href="<?= Get_Sort_Link("record_live_result", $pageValue, $link, $orderValue) ?>">등수</a>
                             </th>
                             <th onclick="sortTable(1)">종목</th>
                             <th onclick="sortTable(2)">이름</th>
                             <th onclick="sortTable(3)">성별</th>
                             <th onclick="sortTable(4)">국가</th>
                             <th onclick="sortTable(5)"><a href="<?= Get_Sort_Link("record_live_record", $pageValue, $link, $orderValue) ?>">결과</a>
                             </th>
                             <th onclick="sortTable(6)">풍속/용기구</th>
                             <th onclick="sortTable(7)">비고</th>
                         </tr>
                     </thead>
                     <tbody class="table_tbody entry_table">
                     <?php
                            $num = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $num++;
                                $record_result = $row["record_official_result"];
                                $athlete_country = $row["athlete_country"];
                                $record_record = $row["record_official_record"];
                                $record_wind = $row["record_wind"];
                                $record_weight = $row["record_weight"];
                                $record_memo = $row["record_memo"];

                                echo '<tr';
                                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                else echo ">";
                                echo "<td>" . htmlspecialchars($record_result) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sports_name']) . "</td>";
                                echo '<td class="popup_BTN">';
                                $athlete_name = explode(',', trim($row["athlete_name"]));
                                if (hasSearchedValue($athlete_name)) {
                                    if (count($athlete_name) > 1) {
                                        echo htmlspecialchars($athlete_name[0]) . " 외 " . (count($athlete_name) - 1) . "명";
                                    } else {
                                        echo htmlspecialchars($athlete_name[0]);
                                    }
                                } else {
                                    echo htmlspecialchars(" - ");
                                }
                                echo '<div class="item_popup" style="display: none;">';
                                if (hasSearchedValue($athlete_name)) {
                                    foreach ($athlete_name as $name) {
                                        if ($name == end($athlete_name)) {
                                            echo htmlspecialchars(trim($name));
                                        } else {
                                            echo htmlspecialchars(trim($name)) . ',' . "<br>";
                                        }
                                    }
                                } else {
                                    echo htmlspecialchars(" - ");
                                }

                                echo '</div>';
                                echo "</td>";
                                if ($row["schedule_gender"] == "f") {
                                    echo "<td>" . "여성" . "</td>";
                                } else if ($row["schedule_gender"] == "m") {
                                    echo "<td>" . "남성" . "</td>";
                                } else if ($row["schedule_gender"] == "c") {
                                    echo "<td>" . "혼성" . "</td>";
                                }
                                echo "<td>" . htmlspecialchars($athlete_country) . "</td>";
                                echo "<td>" . htmlspecialchars($record_record) . "</td>";
                                echo "<td>" . htmlspecialchars($record_wind . $record_weight) . "</td>";
                                echo "<td>" . htmlspecialchars($record_memo) . "</td>";
                                echo '</tr>';
                            }
                            ?>
                     </tbody>
                 </table>
                 <div class="playerRegistrationBtnArea">
                    <div class="ExcelBtn IDBtn">
                        <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                            <input type="submit" name="query" id="execute_excel" value="<?php echo $sql ?>" hidden />
                            <?php if (count($bindarray) !== 0) {
                                    echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />';
                                }
                                ?>
                            <input type="text" name="role" value="schedule_rank_listing" hidden />
                            <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                출력</label>
                        </form>
                    </div>
                </div>
                 <div class="page">
                     <?= Get_Pagenation($page_list_size, $pagesizeValue, $pageValue, $total_count, $link) ?>
                 </div>
             </div>
         </div>
     </div>

     <script src="/assets/js/main.js?ver=4"></script>
 </body>

 </html>