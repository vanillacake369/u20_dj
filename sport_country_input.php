<?php
require_once "head.php";
// 로그 기능
require_once "backheader.php";

if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
?>

<script src="/assets/js/restrict.js"></script>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    국가 등록
                </p>
                <form action="action/sport/country_insert.php" method="post" class="form">
                    <div class="UserProfile_modify UserProfile_input country_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row Desc_item">
                                    <span>국가 코드</span>
                                    <input placeholder="국가 코드를 입력해 주세요" type="text" id="wr_1" name="code" value="" minlength="3" maxlength="3" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" oninput="handleOnInput(this)" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>국가 이름</span>
                                    <input placeholder="국가 이름(영문)을 입력해 주세요" type="text" name="name" id="wr_1" value="" minlength="2" maxlength="64" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" oninput="handleOnInput(this)" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>국가 이름</span>
                                    <input placeholder="국가 이름(한글)을 입력해 주세요" type="text" name="name_kr" id="wr_1" value="" minlength="1" maxlength="32" required="" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" onkeypress="if(!(event.keyCode < 47 && event.keyCode > 58)) event.returnValue=false;" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" class="BTN_blue2">등록</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="/assets/js/main.js?ver=4"></script>

</html>