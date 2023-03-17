<?
    require_once "head.php";
?>
</head>

<body>
    <!-- contents 본문 내용 -->
    <div class="loginBox">
        <div class="login_Logo">
            <img src="assets/images/logo.png">
        </div>
        <div class="loginform">
            <form method="post" action="action/auth/login.php">
                <div class="login_pannel">
                    <div class="login_pannel_inner">
                        <div class="id_pw_wrap">
                            <div class="login_row">
                                <input type="text" id="id" class="input_text" name="id" placeholder="아이디" required />
                            </div>
                            <div class="login_row">
                                <input type="password" id="pw" class="input_text" name="pw" placeholder="비밀번호"
                                    required />
                            </div>
                        </div>
                        <div class="keep"></div>
                        <button type="submit" class="btn_login" name="login">
                            <span class="btn_text">로그인</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
</body>


</html>