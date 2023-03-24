<?php
require_once "head.php";
require_once "includes/auth/config.php";
require_once "action/module/dictionary.php";
?>
<script src="/assets/js/jquery-3.2.1.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="athlete">
            <div class="profile_logo">
                <img src="/assets/images/logo.png">
            </div>
            <div class="UserProfile">
                <p class="UserProfile_tit tit_left_blue">
                    신기록 등록
                </p>
                <form action="./action/record/worldrecord_insert.php" method="post">
                    <div class="UserProfile_modify UserProfile_input">
                        <div>
                            <ul class="UserDesc">
                                <li class="row input_row Desc_item">
                                    <span>종목</span>
                                    <select name="sports" style="width: 200px;" id='sports'>
                                        <option value="non" hidden="">경기 종목</option>
                                        <optgroup label="종합경기">
                                            <option value="decathlon">decathlon</option>
                                            <option value="decathlon(100m)">decathlon(100m)</option>
                                            <option value="decathlon(longjump)">decathlon(longjump)</option>
                                            <option value="decathlon(shotput)">decathlon(shotput)</option>
                                            <option value="decathlon(highjump)">decathlon(highjump)</option>
                                            <option value="decathlon(400m)">decathlon(400m)</option>
                                            <option value="decathlon(110mh)">decathlon(110mh)</option>
                                            <option value="decathlon(discusthrow)">decathlon(discusthrow)</option>
                                            <option value="decathlon(polevault)">decathlon(polevault)</option>
                                            <option value="decathlon(javelinthrow)">decathlon(javelinthrow)</option>
                                            <option value="decathlon(1500m)">decathlon(1500m)</option>
                                            <option value="heptathlon">heptathlon</option>
                                            <option value="heptathlon(100mh)">heptathlon(100mh)</option>
                                            <option value="heptathlon(highjump)">heptathlon(highjump)</option>
                                            <option value="heptathlon(shotput)">heptathlon(shotput)</option>
                                            <option value="heptathlon(200m)">heptathlon(200m)</option>
                                            <option value="heptathlon(longjump)">heptathlon(longjump)</option>
                                            <option value="heptathlon(javelinthrow)">heptathlon(javelinthrow)</option>
                                            <option value="heptathlon(800m)">heptathlon(800m)</option>
                                        </optgroup>
                                        <optgroup label="트랙경기">
                                            <option value="100m">100m</option>
                                            <option value="100mh">100mh</option>
                                            <option value="110mh">110mh</option>
                                            <option value="200m">200m</option>
                                            <option value="400m">400m</option>
                                            <option value="400mh">400mh</option>
                                            <option value="800m">800m</option>
                                            <option value="1500m">1500m</option>
                                            <option value="3000m">3000m</option>
                                            <option value="3000mSC">3000mSC</option>
                                            <option value="5000m">5000m</option>
                                            <option value="10000m">10000m</option>
                                            <option value="4x100mR">4x100mR</option>
                                            <option value="4x400mR">4x400mR</option>
                                            <option value="racewalk">racewalk</option>
                                        </optgroup>
                                        <optgroup label="필드경기">
                                            <option value="discusthrow">discusthrow</option>
                                            <option value="javelinthrow">javelinthrow</option>
                                            <option value="shotput">shotput</option>
                                            <option value="hammerthrow">hammerthrow</option>
                                            <option value="longjump">longjump</option>
                                            <option value="triplejump">triplejump</option>
                                            <option value="highjump">highjump</option>
                                            <option value="polevault">polevault</option>
                                        </optgroup>
                                    </select>
                                    <script>
                                    $(document).ready(function() {
                                        $("#sports").change(function() {
                                            var val = $(this).val();
                                            console.log("이벤트 발생")
                                            // 경기 선택 이벤트 발생 시 - 경기별 성별 변경 이벤트
                                            if (val.includes('decathlon') || val == "110mh" || val ==
                                                '10000m') {
                                                // 10종 경기: gender 남자 고정
                                                $("#gender").val("m").prop("selected", true);
                                            } else if (val.includes('heptathlon') || val == "100mh" ||
                                                val ==
                                                '3000m') {
                                                // 7종 경기: gender 여자 고정
                                                $("#gender").val("f").prop("selected", true);
                                            }
                                        });
                                    });
                                    </script>
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>성별</span>
                                    <select name="gender" style="width: 200px;" id='gender'>
                                        <option value="non" hidden="">경기 성별</option>
                                        <option value="m">남성</option>
                                        <option value="f">여성</option>
                                        <option value="c">혼성</option>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>기록 구분</span>
                                    <select name="athletics" style="width: 200px;">
                                        <option value="non" hidden="">기록 구분</option>
                                        <option value="w">세계신기록</option>
                                        <option value="u">세계U20신기록</option>
                                        <option value="a">아시아신기록</option>
                                        <option value="s">아시아U20신기록</option>
                                        <option value="c">대회신기록</option>
                                    </select>
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>선수 이름</span>
                                    <input placeholder="선수 이름" type="text" name="athletename" maxlength="50"
                                        required="" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>선수 국가</span>
                                    <input placeholder="선수 국가" type="text" name="athletecountry" maxlength="50"
                                        required="" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>기록</span>
                                    <input placeholder="기록" type="text" name="record" maxlength="50" required="" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>풍속/용기구 무게</span>
                                    <input placeholder="풍속/용기구 무게" type="text" name="wind" maxlength="50" required="" />
                                </li>
                                <li class="row input_row Desc_item">
                                    <span>장소</span>
                                    <input placeholder="장소" type="text" name="location" maxlength="50" required="" />
                                </li>
                                <li class="row input_row Desc_item input_width">
                                    <span>경기날짜</span>
                                    <input placeholder="(YYYY)" type="number" name="date_year" class="input_text_row"
                                        min="1" maxlength="4" oninput="maxLengthCheck(this)" required="" />
                                    <div style="float:left;"> -
                                    </div>
                                    <input placeholder="(mm)" type="number" name="date_month" class="input_text_row"
                                        min="1" max="12" maxlength=" 2" oninput="maxLengthCheck(this)" required="" />
                                    <div style="float:left;"> -
                                    </div>
                                    <input placeholder="(dd)" type="number" name="date_day" class="input_text_row"
                                        min="1" max="31" maxlength="2" oninput="maxLengthCheck(this)" required="" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modify_Btn input_Btn Participant_Btn">
                        <button type="submit" class="btn_login">등록</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/restrict.js"></script>
    <script language='javascript'>
    function checkNumber(event) {
        if (event.key === '.' ||
            event.key === '-' ||
            event.key >= 0 && event.key <= 9) {
            return true;
        }

        return false;
    }
    </script>
</body>

</html>