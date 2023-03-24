<!-- header -->
<div class="headerWrapper">
    <header class="headerBody">
        <div class="headerBodyLayout">
            <div class="headerBodyArea">
                <h1 class="logo"><a href="/index.php">예천U20</a></h1>
                <div class="gnbMask menuhide">
                    <div class="gnbsied">
                        <div class="gnb">
                            <ul class="gnbList">
                                <li class="gnbItem <?php if (explode(".", basename($_SERVER["PHP_SELF"]))[0] == "entry_athlete" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "entry_coach" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "entry_judge" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "entry_director") echo "gnb_active"; ?>">
                                    <div>
                                        <span>
                                            <i class="xi-user menu_red"></i>
                                            <i class="xi-group"></i>
                                            참가자 관리
                                            <i class="xi-angle-up"></i>
                                        </span>
                                        <i class="xi-man"></i>
                                        <i class="xi-run"></i>
                                    </div>
                                    <div class="gnbDetailMenuList">
                                        <ul>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'entry_athlete' ? 'class="menu_blue"' : ''; ?> href="/entry_athlete.php">선수
                                                    목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'entry_coach' ? 'class="menu_blue"' : ''; ?> href="/entry_coach.php">코치 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'entry_judge' ? 'class="menu_blue"' : ''; ?> href="/entry_judge.php">심판 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'entry_director' ? 'class="menu_blue"' : ''; ?> href="/entry_director.php">임원 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if (explode(".", basename($_SERVER["PHP_SELF"]))[0] == "sport_management" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "sport_countrymanagement" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "sport_schedulemanagement" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "sport_groupmanagement") echo "gnb_active"; ?>">
                                    <div>
                                        <span>
                                            <i class="xi-run menu_Orange"></i>
                                            <i class="xi-bicycle"></i>
                                            경기 관리
                                            <i class="xi-angle-up"></i>
                                        </span>
                                        <i class="xi-man"></i>
                                        <i class="xi-run"></i>
                                    </div>
                                    <div class="gnbDetailMenuList">
                                        <ul>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'sport_management' ? 'class="menu_blue"' : ''; ?> href="/sport_management.php">경기 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'sport_countrymanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_countrymanagement.php">국가 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'sport_schedulemanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_schedulemanagement.php">일정목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'sport_groupmanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_groupmanagement.php">조편성 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if (explode(".", basename($_SERVER["PHP_SELF"]))[0] == "record_resultManagement" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "record_recordHistory") echo "gnb_active"; ?>">
                                    <div>
                                        <span>
                                            <i class="xi-timer-o menu_yellow"></i>
                                            <i class="xi-alarm-clock-o"></i>
                                            기록 관리
                                            <i class="xi-angle-up"></i>
                                        </span>
                                        <i class="xi-man"></i>
                                        <i class="xi-run"></i>
                                    </div>
                                    <div class="gnbDetailMenuList">
                                        <ul>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'record_resultManagement' ? 'class="menu_blue"' : ''; ?> href="/record_resultManagement.php">경기결과 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'record_recordHistory' ? 'class="menu_blue"' : ''; ?> href="/record_recordHistory.php">역대기록 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if (explode(".", basename($_SERVER["PHP_SELF"]))[0] == "statistics_playerRanklisting" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "statistics_newRecordListing" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "statistics_scheduleRankListing" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "statistics_schuduleListing" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "statistics_countryListing") echo "gnb_active"; ?>">
                                    <div>
                                        <span>
                                            <i class="xi-paper-o menu_green"></i>
                                            <i class="xi-equalizer-thin"></i>
                                            통계 관리
                                            <i class="xi-angle-up"></i>
                                        </span>
                                        <i class="xi-man"></i>
                                        <i class="xi-run"></i>
                                    </div>
                                    <div class="gnbDetailMenuList">
                                        <ul>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'statistics_playerRanklisting' ? 'class="menu_blue"' : ''; ?> href="/statistics_playerRanklisting.php">선수별 순위보기</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'statistics_newRecordListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_newRecordListing.php">신기록 경기기록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'statistics_scheduleRankListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_scheduleRankListing.php">경기별 순위보기</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'statistics_schuduleListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_schuduleListing.php">경기별 메달보기</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'statistics_countryListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_countryListing.php">국가별 순위보기</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if (explode(".", basename($_SERVER["PHP_SELF"]))[0] == "account_mypage" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "account_change_pw" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "account_signup" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "account_list" || explode(".", basename($_SERVER["PHP_SELF"]))[0] == "account_log") echo "gnb_active"; ?>">
                                    <div>
                                        <span>
                                            <i class="xi-key menu_blue"></i>
                                            <i class="xi-security"></i>
                                            계정 관리
                                            <i class="xi-angle-up"></i>
                                        </span>
                                        <i class="xi-man"></i>
                                        <i class="xi-run"></i>
                                    </div>
                                    <div class="gnbDetailMenuList">
                                        <ul>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'account_mypage' ? 'class="menu_blue"' : ''; ?> href="/account_mypage.php">계정 정보</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'account_change_pw' ? 'class="menu_blue"' : ''; ?> href="/account_change_pw.php">비빌번호 변경</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'account_signup' ? 'class="menu_blue"' : ''; ?> href="/account_signup.php">계정 생성</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'account_list' ? 'class="menu_blue"' : ''; ?> href="/account_list.php">계정 목록</a></li>
                                            <li><a <?php explode(".", basename($_SERVER["PHP_SELF"]))[0] == 'account_log' ? 'class="menu_blue"' : ''; ?> href="/account_log.php">로그 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="gnbMask_right">

                    </div>
                </div>
                <div class="UserPage">
                    <a href="/account_mypage.php"><i class="xi-profile xi-1x"></i>
                        <p>마이페이지</p>
                    </a>
                    <a href="/action/auth/logout.php"><i class="xi-power-off xi-1x"></i>
                        <p>로그아웃</p>
                    </a>
                    <button type="button" class="menuBtn" type="button"><i class="xi-bars"></i></button>
                </div>
            </div>
        </div>
    </header>
</div>

<div class="top">
    <div id="js-scroll"><i class="xi-arrow-up topBtn"></i></div>
</div>