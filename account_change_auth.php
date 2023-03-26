<?php
// 접근 권한 체크 함수
require_once "backheader.php";
// 읽기 권한 시에만 접근 가능
if (!authCheck($db, "authAccountsRead")) {
    exit("<script>
            alert('읽기 권한이 없습니다.');
            history.back();
        </script>");
}
// 특정 계정에 대한 정보 수정 접근 시, 수정 권한에 따른 접근 제한
if ($_GET && !authCheck($db, "authAccountsUpdate")) {
    exit("<script>
            alert('수정 권한이 없습니다.');
            history.back();
        </script>");
}
require_once "head.php";
require_once "includes/auth/config.php";

$account = $_GET['id'];
$sql = " SELECT admin_level, admin_name, admin_account FROM list_admin WHERE admin_account = '$account';";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
?>
</head>

<body>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper">
            <div class="result_tit">
                <div class="result_list2">
                    <p class=" tit_left_blue"><?= htmlspecialchars($row["admin_name"]); ?>(<?= htmlspecialchars($row["admin_account"]); ?>)</p>
                </div>
            </div>
            <div class="MainRank coachList defaultList">
                <form action="action/auth/auth_account_update.php" method="post" class="form" id="authForm">
                    <input name="id" value="<?php echo $account; ?>" hidden />
                    <div class="signupPageBox labelBox">
                        <div class="pms_tit">
                            <h1 class="tit_left_green">권한 설정</h1>
                        </div>
                        <div class="labelItem">
                            <label>참가자 관리</label>
                            <label>
                                <input type="checkbox" value="authEntrysRead" name="authEntrysRead" class="" <?php echo in_array('authEntrysRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysUpdate" name="authEntrysUpdate" class="" <?php echo in_array('authEntrysUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysDelete" name="authEntrysDelete" class="" <?php echo in_array('authEntrysDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysCreate" name="authEntrysCreate" class="" <?php echo in_array('authEntrysCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>경기 관리</label>
                            <label>
                                <input type="checkbox" value="authSchedulesRead" name="authSchedulesRead" class="" <?php echo in_array('authSchedulesRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesUpdate" name="authSchedulesUpdate" class="" <?php echo in_array('authSchedulesUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesDelete" name="authSchedulesDelete" class="" <?php echo in_array('authSchedulesDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesCreate" name="authSchedulesCreate" class="" <?php echo in_array('authSchedulesCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>기록 관리</label>
                            <label>
                                <input type="checkbox" value="authRecordsRead" name="authRecordsRead" class="" <?php echo in_array('authRecordsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsUpdate" name="authRecordsUpdate" class="" <?php echo in_array('authRecordsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsDelete" name="authRecordsDelete" class="" <?php echo in_array('authRecordsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsCreate" name="authRecordsCreate" class="" <?php echo in_array('authRecordsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>통계 관리</label>
                            <label>
                                <input type="checkbox" value="authStaticsRead" name="authStaticsRead" class="" <?php echo in_array('authStaticsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsUpdate" name="authStaticsUpdate" class="" <?php echo in_array('authStaticsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsDelete" name="authStaticsDelete" class="" <?php echo in_array('authStaticsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsCreate" name="authStaticsCreate" class="" <?php echo in_array('authStaticsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>계정 관리</label>
                            <label>
                                <input type="checkbox" value="authAccountsRead" name="authAccountsRead" class="" <?php echo in_array('authAccountsRead', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsUpdate" name="authAccountsUpdate" class="" <?php echo in_array('authAccountsUpdate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsDelete" name="authAccountsDelete" class="" <?php echo in_array('authAccountsDelete', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsCreate" name="authAccountsCreate" class="" <?php echo in_array('authAccountsCreate', explode(',', $row['admin_level'])) ? ' checked' : ' unchecked' ?> />
                                <span>등록</span>
                            </label>
                        </div>
                    </div>
                    <div class="changePwBtnArea">
                        <button type="submit" class="changePwBtn defaultBtn" name="signup" onclick="readCheck()">변경하기</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
</body>

</html>