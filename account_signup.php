<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "backheader.php";
// 읽기 권한 시에만 접근 가능
if (!authCheck($db, "authAccountsRead")) {
    exit("<script>
            alert('읽기 권한이 없습니다.');
            history.back();
        </script>");
}
// 생성 권한 시에만 접근 가능
if (!authCheck($db, "authAccountsCreate")) {
    exit("<script>
            alert('생성 권한이 없습니다.');
            history.back();
        </script>");
}
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
                    <h1>계정 생성<i class="xi-key key"></i></h1>
                </div>
                <div class="changePwNotice">
                    <h5>새로운 계정의 정보를 입력해 주세요.</h5>
                    <p>- 비밀번호는 8 ~ 20 자로 설정해 주세요.</p>
                </div>
                <form action="action/auth/auth_account_insert.php" method="post" class="form" id="authForm">
                    <!-- #2 PASSWORD VERIFICATION -->
                    <script>
                        const check = function() {
                            if (
                                document.getElementById("pw").value ==
                                document.getElementById("cpassword").value
                            ) {
                                document.getElementById(
                                    "message"
                                ).style.color = "green";
                                document.getElementById(
                                    "message"
                                ).innerHTML = "비밀번호가 같습니다.";
                            } else {
                                document.getElementById(
                                    "message"
                                ).style.color = "red";
                                document.getElementById(
                                    "message"
                                ).innerHTML = "비밀번호가 다릅니다.";
                            }
                        };
                    </script>
                    <div class="changePwFormArea">
                        <div class="changePwInputArea">
                            <input placeholder="아이디" type="text" name="id" value="" minlength="8" maxlength="16" required="" />
                        </div>
                        <div class="changePwInputArea">
                            <input placeholder="비밀번호" type="password" name="pw" id="pw" value="" minlength="8" maxlength="20" required="" onkeyup="check();" />
                        </div>
                        <div class="changePwInputArea">
                            <input placeholder="비밀번호 재확인" type="password" name="cpassword" id="cpassword" value="" minlength="8" maxlength="20" required="" onkeyup="check();" />
                        </div>
                        <div class="changePwInputArea">
                            <input placeholder="이름" type="text" id="name" name="name" value="" maxlength="50" required="" />
                        </div>
                        <div></div>
                    </div>
                    <div class="signupPageBox labelBox">
                        <h1>권한 설정</h1>
                        <div class="labelItem">
                            <label>참가자 관리</label>
                            <label>
                                <input type="checkbox" value="authEntrysRead" name="authEntrysRead" />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysUpdate" name="authEntrysUpdate" />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysDelete" name="authEntrysDelete" />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authEntrysCreate" name="authEntrysCreate" />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>경기 관리</label>
                            <label>
                                <input type="checkbox" value="authSchedulesRead" name="authSchedulesRead" />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesUpdate" name="authSchedulesUpdate" />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesDelete" name="authSchedulesDelete" />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authSchedulesCreate" name="authSchedulesCreate" />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>기록 관리</label>
                            <label>
                                <input type="checkbox" value="authRecordsRead" name="authRecordsRead" />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsUpdate" name="authRecordsUpdate" />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsDelete" name="authRecordsDelete" />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authRecordsCreate" name="authRecordsCreate" />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>통계 관리</label>
                            <label>
                                <input type="checkbox" value="authStaticsRead" name="authStaticsRead" />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsUpdate" name="authStaticsUpdate" />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsDelete" name="authStaticsDelete" />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authStaticsCreate" name="authStaticsCreate" />
                                <span>등록</span>
                            </label>
                        </div>
                        <div class="labelItem">
                            <label>계정 관리</label>
                            <label>
                                <input type="checkbox" value="authAccountsRead" name="authAccountsRead" />
                                <span>읽기</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsUpdate" name="authAccountsUpdate" />
                                <span>수정</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsDelete" name="authAccountsDelete" />
                                <span>삭제</span>
                            </label>
                            <label>
                                <input type="checkbox" value="authAccountsCreate" name="authAccountsCreate" />
                                <span>등록</span>
                            </label>
                        </div>
                </form>
            </div>
            <div class="changePwBtnArea">
                <button type="submit" class="changePwBtn defaultBtn" name="signup" onclick="readCheck()">확인</button>
            </div>
        </div>
    </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
</body>

</html>