<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "security/security.php";

$id = cleanInput($_GET['id']);
$sql = "SELECT sports_category, sports_code FROM list_sports WHERE sports_code IN (SELECT schedule_sports FROM list_schedule WHERE schedule_id=$id);";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$category = $row['sports_category'];
$code = $row['sports_code'];
?>
<<<<<<< HEAD=======<script src="/assets/js/jquery-1.12.4.min.js">
    </script>
    <script src="/assets/js/restrict.js"></script>
    >>>>>>> dj_origin/main
    </head>


    <body>

        <!-- contents 본문 내용 -->
        <div class="container">
            <div class="schedule">
                <div class="schedule_filed">
                    <div class="schedule_filed_tit">
                        <p class="tit_left_blue">조 편성 하기</p>
                    </div>
                    <div class="schedule_change_members">
                        <form action="" method="get" class="form" id="form_action">
                            <input type="hidden" name="category" value="<?= $category ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <select class="d_select" name="round" id="round">
                                <option value="" hidden="">조편성 방식</option>
                                <option value="자동">자동</option>
                                <option value='수동'>수동</option>
                            </select>
                            <div class="schedule_filed_tit round_tit">
                                <p class="tit_left_green">조 갯수</p>
                            </div>
                            <select class="d_select" name="count" id="count">
                                <option value="non" hidden="">조 개수 선택</option>
                                <option value="1">1</option>
                                <?php
                                if (($category != '필드경기') && ($category != '종합경기') && ($code != '3000m') && ($code != '5000m') && ($code != '10000m')) {
                                    echo '<option value="2">2</option>';
                                    echo '<option value="3">3</option>';
                                    echo '<option value="4">4</option>';
                                    echo '<option value="5">5</option>';
                                    echo '<option value="6">6</option>';
                                    echo '<option value="7">7</option>';
                                    echo '<option value="8">8</option>';
                                    echo '<option value="9">9</option>';
                                    echo '<option value="10">10</option>';
                                    echo '<option value="11">11</option>';
                                    echo '<option value="12">12</option>';
                                } ?>
                            </select>
                            <div class="Participant_Btn">
                                <button type="submit" class="changePwBtn defaultBtn" id="btn_click">
                                    <span>확인</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script src="/assets/js/main.js?ver=4"></script>
            <script type="text/javascript">
                var $code = "<?php echo $code; ?>";
                $('#round').change(function() {
                    $("#round option:selected").each(function() {
                        if ($(this).val() == '수동' && $code != '4x100mR' && $code != '4x400mR') {
                            $("#form_action").attr("action", "sport_schedule_manual_group_org.php");
                            $("#form_action").attr("target", "_blank");
                        } else if ($code == '4x100mR' || $code == '4x400mR') {
                            $("#form_action").attr("action", "sport_schedule_manual_relay_org.php");
                            $("#form_action").attr("target", "_blank");
                        } else {
                            $("#form_action").attr("action", "sport_auto_create_group.php");
                            $("#form_action").attr("target", "_blank");
                        }
                    });
                });
                $("#btn_click").on("click", function() {
                    window.close();
                });
            </script>
    </body>


    </html>