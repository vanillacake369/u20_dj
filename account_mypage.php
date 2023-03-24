<?php
require_once "head.php";
require_once "backheader.php";
// 읽기 권한 시에만 접근 가능
if (!authCheck($db, "authAccountsRead")) {
        exit("<script>
                alert('읽기 권한이 없습니다.');
                history.back();
        </script>");
}

require_once "includes/auth/config.php";
$sql = "SELECT admin_account, admin_name, admin_level,admin_password_datetime from list_admin where admin_account='" . $_SESSION['Id'] . "'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
?>
</head>

<body>
        <!-- header -->
        <?php require_once 'header.php' ?>
        <!-- contents 본문 내용 -->
        <div class="Area">
                <div class="Wrapper TopWrapper">
                        <div class="MainRank coachList defaultList">
                                <div class="MainRank_tit">
                                        <h1>계정 정보<i class="xi-key key"></i></h1>
                                </div>
                                <div class="mypage_account">
                                        <div class="myPageBox">
                                                <p>아이디</p>
                                                <p><?= htmlspecialchars($row["admin_account"]); ?></p>
                                        </div>
                                        <div class="myPageBox">
                                                <p>이름</p>
                                                <p><?= htmlspecialchars($row["admin_name"]); ?></p>
                                        </div>
                                        <div class="myPageBox">
                                                <p>비밀번호 변경일 : </p>

                                                <p>
                                                        <a class="changePwLink" href="account_change_pw.php">
                                                                비밀번호 변경
                                                                <i class="xi-lock"></i>
                                                        </a>
                                                </p>
                                        </div>
                                </div>
                                <div class="myPageBox labelBox">
                                        <h1>권한</h1>
                                        <div class="labelItem">
                                                <label>참가자 관리</label>
                                                <label><input type="checkbox" <?php echo in_array('authEntrysRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>읽기</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authEntrysUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>수정</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authEntrysDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>삭제</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authEntrysCreate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>등록</span></label>
                                        </div>
                                        <div class="labelItem">
                                                <label>경기 관리</label>
                                                <label><input type="checkbox" <?php echo in_array('authSchedulesRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>읽기</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authSchedulesUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>수정</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authSchedulesDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>삭제</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authSchedulesCreate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>등록</span></label>
                                        </div>
                                        <div class="labelItem">
                                                <label>기록 관리</label>
                                                <label><input type="checkbox" <?php echo in_array('authRecordsRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>읽기</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authRecordsUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>수정</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authRecordsDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>삭제</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authRecordsCreate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>등록</span></label>
                                        </div>
                                        <div class="labelItem">
                                                <label>통계 관리</label>
                                                <label><input type="checkbox" <?php echo in_array('authStaticsRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>읽기</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authStaticsUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>수정</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authStaticsDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>삭제</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authStaticsCreate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>등록</span></label>
                                        </div>
                                        <div class="labelItem">
                                                <label>계정 관리</label>
                                                <label><input type="checkbox" <?php echo in_array('authAccountsRead', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>읽기</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authAccountsUpdate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>수정</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authAccountsDelete', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>삭제</span></label>
                                                <label><input type="checkbox" <?php echo in_array('authAccountsCreate', explode(',', $row['admin_level'])) ? ' checked ' : ' unchecked ' ?> disabled /><span>등록</span></label>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <script src="/assets/js/main.js?ver=4"></script>
</body>

</html>