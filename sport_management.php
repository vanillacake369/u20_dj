 <?php
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "action/module/pagination.php";
    require_once "action/module/dictionary.php";

    // 로그 기능
    require_once "backheader.php";

    if (!authCheck($db, "authSchedulesRead")) {
        exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
    }
        
    $searchValue = [];
    $searchValue["sports_code"] = getSearchValue($_GET["sports_code"] ?? NULL);
    $searchValue["sports_name"] = getSearchValue($_GET["sports_name"] ?? NULL);
    // $searchValue["sports_name_kr"] = getSearchValue($_GET["sports_name_kr"] ?? NULL);
    $pageValue = getPageValue($_GET["page"] ?? NULL);
    $categoryValue = getCategoryValue($_GET["order"] ?? NULL);
    $orderValue = getOrderValue($_GET["sc"] ?? NULL);
    $pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

    $page_list_size = 10;
    $link = "";

    $tableName = "list_sports";
    $columnStartsWith = "sports_";
    $id = $columnStartsWith . "name";

    $sql_where = " WHERE ($id > 'a' or $id > '1')";
    $sql_order = " ORDER BY $id DESC ";
    $sql_like = "";
    $page_list_count = ($pageValue - 1) * $pagesizeValue;

    $sql = "SELECT sports_code,sports_name FROM list_sports";
    $uri_array = array();
    $bindarray = array();
    $keyword = array();

    if (count($searchValue)) {
        $keyword = array();
        $bindarray = array();

        if ((!is_null($searchValue["sports_code"])) && ($searchValue["sports_code"] != "non")) {
            $uri_array["sports_code"] = $searchValue["sports_code"];
            array_push($bindarray, $searchValue["sports_code"]);
            array_push($keyword, "sports_code=?");
        }
        if ((!is_null($searchValue["sports_name"])) && ($searchValue["sports_name"] != "non")) {
            $uri_array["sportse_name"] = $searchValue["sports_name"];
            array_push($bindarray, $searchValue["sports_name"]);
            array_push($keyword, "sports_name=?");
        }
        // if ((!is_null($searchValue["sports_name_kr"])) && ($searchValue["sports_name_kr"] != "non")) {
        //     $uri_array["sports_name_kr"] = $searchValue["sports_name_kr"];
        //     array_push($bindarray, $searchValue["sports_name_kr"]);
        //     array_push($keyword, "sports_name_kr=?");
        // }

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
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }

    $sql = $sql . $sql_where;
    $excel = $sql . $sql_order;

    if (isset($searchValue) && ($searchValue["sports_code"] != "non" || $searchValue["sports_name"] != "non")) {
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
     <? require_once 'header.php'; ?>
     <!-- contents 본문 내용 -->
     <div class="Area">
         <div class="Wrapper TopWrapper">
             <div class="MainRank matchList defaultList">
                 <div class="MainRank_tit">
                     <h1>경기 목록<i class="xi-calendar calendar"></i></h1>
                 </div>
                 <div class="searchArea">
                     <form action="" name="judge_searchForm" method="get" class="searchForm pageArea">
                         <div class="page_size">
                             <select name="entry_size" onchange="changeTableSize(this);" id="changePageSize"
                                 class="changePageSize">
                                 <option value="non" hidden="">페이지</option>
                                 <?php
                                    echo '<option value="10"' . ($pagesizeValue == 10 ? 'selected' : '') . '>10개씩</option>';
                                    echo '<option value="15"' . ($pagesizeValue == 15 ? 'selected' : '') . '>15개씩</option>';
                                    echo '<option value="20"' . ($pagesizeValue == 20 ? 'selected' : '') . '>20개씩</option>';
                                ?>
                             </select>
                         </div>
                         <div class="selectArea defaultSelectArea">
                             <div class="defaultSelectBox">
                                 <select title="경기종목 코드" name="sports_code">
                                     <option value="non" hidden="">경기종목 코드</option>
                                     <?
                                        $sSql = "SELECT distinct sports_code FROM list_sports ;";
                                        $sResult = $db->query($sSql);
                                        while ($sRow = mysqli_fetch_array($sResult)) {
                                            echo "<option value=" . $sRow['sports_code'] . ' ' . ($_GET["sports_code"] == $sRow['sports_code'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_code']) . "</option>";
                                        }
                                     ?>
                                 </select>
                             </div>
                             <div class="defaultSelectBox">
                                 <select title="경기종목 이름" name="sports_name">
                                     <option value="non" hidden="">경기종목 이름</option>
                                     <?
                                        $sSql = "SELECT distinct sports_name FROM list_sports;";
                                        $sResult = $db->query($sSql);
                                        while ($sRow = mysqli_fetch_array($sResult)) {
                                            echo "<option value='";
                                            echo $sRow['sports_name'];
                                            echo "'" . ($searchValue["sports_name"] == $sRow['sports_name'] ? 'selected' : '') . ">" . htmlspecialchars($sRow['sports_name']) . "</option>";
                                        }
                                        ?>
                                 </select>
                             </div>
                             <div class="search">
                                 <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                 <div class="search" style="width: 50px">
                                     <button class="SearchBtn" name="search" value="search" type="submit"><i
                                             class="xi-search" title="검색"></i></button>
                                 </div>
                             </div>
                     </form>
                 </div>
                 <table class="box_table">
                     <thead class="table_head entry_table">
                         <tr>
                             <th scope="col">구분</th>
                             <th scope="col"><a href="<?= Get_Sort_Link("code", $pageValue, $link, $orderValue) ?>">경기종목
                                     코드</a></th>
                             <th scope="col"><a href="<?= Get_Sort_Link("name", $pageValue, $link, $orderValue) ?>">경기종목
                                     이름</a></th>
                             <!-- <th scope="col"><a href=" // Get_Sort_Link("name_kr", $pageValue, $link, $orderValue)">경기종목
                                이름(한글)</th> -->
                             <?php
                        if (authCheck($db, "authSchedulesDelete")) {  ?>
                             <th scope="col">삭제</th>
                             <?php
                        } ?>
                         </tr>
                     </thead>
                     <tbody class="table_tbody entry_table">
                         <?php
                            $i = $total_count - $page_list_count;
                            $j = 0;
                            $num = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $num++;
                                echo '<tr';
                                if($num%2==0) echo ' class="Ranklist_Background">'; else echo '>';
                                echo "<td scope='col'>" . $i . "</td>";
                                echo "<td scope='col'>" . htmlspecialchars($row["sports_code"]) . "</td>";
                                echo "<td scope='col'>" . htmlspecialchars($row["sports_name"]) . "</td>";
                                // echo "<td scope='col'>" . htmlspecialchars($row["sports_name_kr"]) . "</td>";
                                if (authCheck($db, "authSchedulesDelete")) {
                                    echo "<td scope='col'><input type='button' onclick=" . "confirmDelete('" . $row["sports_code"] . "','sports')" . " value='삭제' class='BTN_Red defaultBtn'></td>";
                                }
                                echo "</tr>";
                                $i--;
                                $j++;
                            }
                            //경기 삭제 기능 구현
                            if (isset($_POST["sports_delete"])) {
                                $sql = "DELETE FROM list_sports WHERE sports_code=?";
                                $stmt = $db->prepare($sql);
                                $stmt->bind_param("s", $_POST["sports_delete"]);
                                $stmt->execute();
                                logInsert($db, $_SESSION['Id'], '경기 삭제', $_POST["sports_delete"]);
                                echo "<script>location.href='./sport_management.php';</script>";
                            }
                        ?>
                     </tbody>
                 </table>
                 <div class="playerRegistrationBtnArea">
                     <div class="ExcelBtn IDBtn">
                         <form action="./execute_excel.php" method="post" enctype="multipart/form-data">
                             <input type="submit" name="query" id="execute_excel" value="<?php echo $excel ?>" hidden />
                             <?php if (count($bindarray) !== 0) echo '<input type="text" name="keyword" value="' . implode(',', $bindarray) . '" hidden />' ?>
                             <input type="text" name="role" value="sport_management" hidden />

                             <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                 출력</label>
                         </form>
                     </div>
                     <div class="registrationBtn">
                         <?
                            if (authCheck($db, "authSchedulesCreate"))
                            {
                         ?>
                         <button class="defaultBtn BTN_Blue BIG_btn" type="button"
                             onclick="createPopupWin('sport_sport_input.php','창 이름',900,512)">등록</button>
                         <? } ?>
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