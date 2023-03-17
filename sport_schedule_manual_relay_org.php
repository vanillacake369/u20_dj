<?
    require_once "head.php";
    require_once "/includes/auth/config.php";
    require_once "/security/security.php";

    if ($_GET["round"] != '수동' || $_GET["round"] == '' || !isset($_GET["round"]) || $_GET["count"] == '' || !isset($_GET["count"]) || $_GET["id"] == '' || !isset($_GET["id"]) || $_GET["category"] == '' || !isset($_GET["category"]) || ($_GET["category"] != '트랙경기' && $_GET["count"] != '1') || ($_GET["category"] == '트랙경기' && $_GET["count"] > 12)) {
        echo "<script>alert('잘못된 경로입니다.');  location.href='./sport_schedulemanagement.php';</script>";
    }
    if ($_GET["count"] == 'non') {
        echo "<script>alert('조 개수를 선택하세요.');  window.close();</script>";
    }

    $group_count = cleanInput($_GET["count"]); //조 개수
    $id = cleanInput($_GET["id"]); //schedule_id
    $category = cleanInput($_GET["category"]);

    $number = 8;

    $sportssql = "SELECT schedule_sports FROM list_schedule WHERE schedule_id='" . $id . "'";
    $sportresult = $db->query($sportssql);
    $sport = mysqli_fetch_array($sportresult);

    $sql = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, FIND_IN_SET( '" . $sport['schedule_sports'] . "' ,athlete_schedule) AS checking FROM list_athlete having checking>0 order by athlete_country asc, athlete_name asc;";
    $result = $db->query($sql);
    while ($row = mysqli_fetch_array($result)) {
        $resultArr[] = $row;
    }
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
                <? for ($i = 1; $i <= $group_count; $i++) { ?>
                <div class="schedule_filed filed_list_item">
                    <div class="profile_logo">
                        <img src="/assets/images/logo.png">
                    </div>
                    <div class="schedule_filed_tit schedule_green">
                        <p class="tit_left_yellow"><?= $i ?>조 편성</p>
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="count" value="<?= $group_count ?>">
                    </div>
                    <div class="filed_list filed2_list ">
                        <ul>
                            <li>
                                <p><?= $category == '트랙경기' ? '레인' : '순서' ?></p>
                                <p>선수 이름</p>
                            </li>
                        </ul>
                    </div>
                    <div class="filed_item filed2_item">
                        <? for ($j = 1; $j <= $number; $j++) { ?>
                        <ul>
                            <li>
                                <input type="hidden" name="group[]" id="group[]" value=" <?= $i ?>">
                                <input type="hidden" name="order[]" value="<?= $order ?>">
                                <input type="text" class="input_text" value="<?= $order % 4 == 1 ? $j : '' ?>" name="lane[]" <?php echo $order % 4 == 1 ? '' : ' readonly' ?>>
                            </li>
                            <li>
                            <select id='athlete' name="athlete" required>
                                                <option value="" disabled selected>선수 선택</option>
                                                <?php
                                                for ($k = 0; $k < count($resultArr); $k++) { ?>
                                                    <option value="<?= $resultArr[$k]['athlete_id'] ?>">
                                                        <?= $resultArr[$k]['athlete_name'] ?>
                                                        (<?= $resultArr[$k]['athlete_country'] ?>)(<?= $resultArr[$k]['athlete_division'] ?>)
                                                    </option>
                                                <?php } ?>
                                            </select>
                                <input type="hidden" name="playerid[]" value="" />
                            </li>
                        </ul>
                        <?}?>

                        <div class="filed_BTN2">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN"
                                onclick="addColumn()"><i class="xi-plus"></i></button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                onclick="deleteColumn()"><i class="xi-minus"></i></button>
                            
                        </div>
                    </div>
                </div>
                <? } ?>

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