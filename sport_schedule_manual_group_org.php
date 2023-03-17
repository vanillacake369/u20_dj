<?
    require_once "head.php";
    require_once "includes/auth/config.php";
    require_once "security/security.php";

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
    if ($category == '필드경기')
        $number = 14;

    // schedule_id 경기 종목을 가져오기
    // => 프론트 상에서 직접 입력하게 바꾸기
    $sportssql = "SELECT schedule_sports FROM list_schedule WHERE schedule_id='" . $id . "'";
    $sportresult = $db->query($sportssql);
    $sport = mysqli_fetch_array($sportresult);

    //@Potatoeunbi
    //해당 경기 참여하는 선수들만 출력하기 위해서 find_in_set을 사용.
    //해당 경기 참여하는 선수들을 모두 배열로 저장하여 한 번만 while을 사용하도록 함.
    $sql = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, FIND_IN_SET( '" . $sport['schedule_sports'] . "' ,athlete_schedule) AS checking FROM list_athlete having checking>0 order by athlete_name asc, athlete_country asc;";
    $result = $db->query($sql);
    while ($row = mysqli_fetch_array($result)) {
        $resultArr[] = $row;
    }
    ?>
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
                                <input type="text" class="input_text" value="<?= $j ?>" name="lane[]">
                            </li>
                            <li>
                            <select class='select-box' name="athlete" required class="select2-hidden-accessible"
                                    tabindex="-1" aria-hidden="true">
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
                            <button type="button" class="defaultBtn BIG_btn BTN_Blue filedBTN"
                                onclick="deleteColumn()"><i class="xi-minus"></i></button>
                            <button type="button" class="defaultBtn BIG_btn BTN_Orange2 filedBTN"
                                onclick="addColumn()"><i class="xi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <? } ?>

                <button type="submit" class="changePwBtn defaultBtn" name="addresult">확인</button>
            </div>
        </form>
    </div>

    <script>
        //@Potatoeunbi
        //history.back이 될 때, select된 값이 남아있지만 select가 선수 선택으로만 남겨져 있어서 이 부분 해결해야 함.

        // 컬럼을 추가하는 버튼을 누를 때, span이 펼쳐지게 하는 select2()를 사용
        // $("select[name=athlete]").select2();
        $(".select-box").select2();

        // $("select[name=athlete]").on('change', function(idx) {
        //     var index = $("select[name='athlete[]']").index(this);
        //     var value = $(this).val();
        //     var eqValue = $(this).parent().next().val(value);
        // });

        // $(document).ready(function() {
        //     $('.select-box').each(function(index, element) {
        //         $(element).on('select2:select', function(e) {
        //             var sb = $('.select-box');
        //             console.log(sb);
        //             // var selectedValue = e.params.data.id;
        //             // $(element).parent().next('.hidden-input').val(selectedValue);
        //             // // var el = $(element).parent().next('.hidden-input');
        //             // var el = $(element).parent().next('.hidden-input');
        //             // console.log(el);
        //         });
        //     });
        // });
        $(document).on('click', function() {
            $('.select-box').each(function(index, element) {
                $(element).on('select2:select', function(e) {
                    var selectedValue = e.params.data.id;
                    var hiddenInput = $(element).parent().next('.hidden-input');
                    hiddenInput.val(selectedValue);
                    console.log(hiddenInput.val());
                });
            });
        });

        $("select").each(function() {
            $(this).val($(this).find('option[selected]').val()).prop("selected", true);
        });
</body>

</html>