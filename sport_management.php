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
    $searchValue = getSearchValue($_GET["search_sports"] ?? NULL);
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
    $sql_like = " AND (sports_name LIKE ? OR sports_code LIKE ? )";
    
    $page_list_count = ($pageValue - 1) * $pagesizeValue;
    $param = null;

    if (isset($searchValue)) {
        $param = "%" . trim($_GET['search_sports']) . "%";
        $sql_where = addLikeToWhereStmt($sql_where, $sql_like);
        $link = addToLink($link, "&search_sports=", $searchValue);
    }

    if (isset($pagesizeValue)) {
        $link = addToLink($link, "&page_size=", $pagesizeValue);
    }

    if (isset($categoryValue) && isset($orderValue)) {
        $sql_order = makeOrderBy($columnStartsWith, $categoryValue, $orderValue);
        $link = addToLink($link, "&order=", $categoryValue);
        $link = addToLink($link, "&sc=", $orderValue);
    }

    $sql = "SELECT sports_code,sports_name FROM list_sports $sql_where" ;
    $excel = $sql . $sql_order;

    if (isset($searchValue)) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $count = $stmt->get_result();
    
        $sql .= " $sql_order LIMIT $page_list_count, $pagesizeValue";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
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
     <?php require_once 'header.php'; ?>
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
                            <div class="search">
                                <input type="text" id="search_sports" class="defaultSearchInput" name="search_sports" placeholder="검색어를 입력해주세요" maxlength="30" value="<?php echo isset($searchValue) ? $searchValue : ''; ?>">
                                <input type="hidden" name="page_size" value="<?php echo $pagesizeValue; ?>">
                                <button name="search" value="search" class="defaultSearchBth" type="submit"><i class="xi-search"></i></button>
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
                                if ($num % 2 == 0) echo ' class="Ranklist_Background">';
                                else echo '>';
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
                             <?php if ($param != null) {
                                $param = array_fill(0, 2, $param);
                                echo '<input type="text" name="keyword" value="' . implode(',', $param) . '" hidden />'; 
                             }?>
                             <input type="text" name="role" value="sport_management" hidden />

                             <label for="execute_excel" class="defaultBtn BIG_btn2 excel_Print">엑셀
                                 출력</label>
                         </form>
                     </div>
                     <div class="registrationBtn">
                         <?php
                            if (authCheck($db, "authSchedulesCreate")) {
                            ?>
                             <button class="defaultBtn BTN_Blue BIG_btn" type="button" onclick="createPopupWin('sport_sport_input.php','창 이름',900,512)">등록</button>
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
 </body>

 </html>