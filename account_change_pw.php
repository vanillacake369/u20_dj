<?
    require_once "head.php";
    require_once "includes/auth/config.php";
?>
</head>

<body>
    <!-- header -->
    <? require_once 'header.php' ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank coachList defaultList">
                <div class="MainRank_tit">
                    <h1>비밀번호 변경<i class="xi-key key"></i></h1>
                </div>
                <div class="changePwNotice">
                    <h5>새로운 비밀번호를 입력해 주세요.</h5>
                    <p>- 비밀번호는 8 ~ 32 자의 영문 대소문자, 숫자, 특수문자를 조합하여 설정해 주세요.</p>
                </div>
                <form action="action/auth/auth_password_update.php" method="post" class="form">
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
                            <input placeholder="비밀번호" type="password" name="pw" id="pw" value="" minlength="8"
                                maxlength="32" required="" onkeyup="check();" />
                        </div>
                        <div class="changePwInputArea">
                            <input placeholder="비밀번호 재확인" type="password" name="cpassword" id="cpassword" value=""
                                minlength="8" maxlength="32" required="" onkeyup="check();" />
                        </div>
                        <div></div>
                    </div>
                    <div class="changePwBtnArea PwBtnArea">
                        <button type="submit" class="changePwBtn defaultBtn" name="signup">변경하기</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?ver=4"></script>
</body>


</html>