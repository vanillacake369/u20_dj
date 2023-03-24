<?php
require_once "head.php";
?>
<script src="/assets/js/restrict.js"></script>

</head>

<body>
    <!-- contents 본문 내용 -->
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    경기 등록
                </p>
                <form action="action/sport/sports_insert.php" method="post" class="form">
                    <div class="UserProfile_modify UserProfile_input country_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row">
                                    <span>경기종목 코드</span>
                                    <input placeholder="경기종목 코드를 입력해 주세요" type="text" name="code" id="wr_1" value="" minlength="4" maxlength="64" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" oninput="handleOnInput(this)" />
                                </li>
                                <li class="row input_row">
                                    <span>경기종목 이름(영문)</span>
                                    <input placeholder="경기종목 이름을 입력해 주세요" type="text" name="name" id="wr_1" value="" minlength="4" maxlength="64" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" oninput="handleOnInput(this)" />
                                </li>
                                <li class="row input_row">
                                    <span>경기종목 이름(한글)</span>
                                    <input placeholder="경기종목 이름(한글)을 입력해 주세요" type="text" name="name_kr" id="wr_1" value="" minlength="2" maxlength="64" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" onkeypress="if(!(event.keyCode < 47 && event.keyCode > 58)) event.returnValue=false;" />
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" name="signup" class="BTN_blue2">등록</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="/assets/js/main.js?v=4"></script>

</html>