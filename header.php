<!-- header -->
<?php $url_now = explode(".", basename($_SERVER["PHP_SELF"]))[0]; ?>
<div class="headerWrapper">
    <header class="headerBody">
        <div class="headerBodyLayout">
            <div class="headerBodyArea">
                <h1 class="logo"><a href="./index.php">예천U20</a></h1>
                <div class="gnbMask menuhide">
                    <div class="gnbsied">
                        <div class="gnb">
                            <ul class="gnbList">
                                <li class="gnbItem <?php if ($url_now == "entry_athlete" || $url_now == "entry_coach" || $url_now == "entry_judge" || $url_now == "entry_director") echo "gnb_active"; ?>">
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
                                            <li><a <?php echo $url_now == 'entry_athlete' ? 'class="menu_blue"' : ''; ?> href="/entry_athlete.php">선수
                                                    목록</a></li>
                                            <li><a <?php echo $url_now == 'entry_coach' ? 'class="menu_blue"' : ''; ?> href="/entry_coach.php">코치 목록</a></li>
                                            <li><a <?php echo $url_now == 'entry_judge' ? 'class="menu_blue"' : ''; ?> href="/entry_judge.php">심판 목록</a></li>
                                            <li><a <?php echo $url_now == 'entry_director' ? 'class="menu_blue"' : ''; ?> href="/entry_director.php">임원 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if ($url_now == "sport_management" || $url_now == "sport_countrymanagement" || $url_now == "sport_schedulemanagement" || $url_now == "sport_groupmanagement") echo "gnb_active"; ?>">
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
                                            <li><a <?php echo $url_now == 'sport_management' ? 'class="menu_blue"' : ''; ?> href="/sport_management.php">경기 목록</a></li>
                                            <li><a <?php echo $url_now == 'sport_countrymanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_countrymanagement.php">국가 목록</a></li>
                                            <li><a <?php echo $url_now == 'sport_schedulemanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_schedulemanagement.php">일정목록</a></li>
                                            <li><a <?php echo $url_now == 'sport_groupmanagement' ? 'class="menu_blue"' : ''; ?> href="/sport_groupmanagement.php">조편성 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if ($url_now == "record_resultManagement" || $url_now == "record_recordHistory") echo "gnb_active"; ?>">
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
                                            <li><a <?php echo $url_now == 'record_resultManagement' ? 'class="menu_blue"' : ''; ?> href="/record_resultManagement.php">경기결과 목록</a></li>
                                            <li><a <?php echo $url_now == 'record_recordHistory' ? 'class="menu_blue"' : ''; ?> href="/record_recordHistory.php">역대기록 목록</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if ($url_now == "statistics_playerRanklisting" || $url_now == "statistics_newRecordListing" || $url_now == "statistics_scheduleRankListing" || $url_now == "statistics_schuduleListing" || $url_now == "statistics_countryListing") echo "gnb_active"; ?>">
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
                                            <li><a <?php echo $url_now == 'statistics_playerRanklisting' ? 'class="menu_blue"' : ''; ?> href="/statistics_playerRanklisting.php">선수별 순위보기</a></li>
                                            <li><a <?php echo $url_now == 'statistics_newRecordListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_newRecordListing.php">신기록 경기기록</a></li>
                                            <li><a <?php echo $url_now == 'statistics_scheduleRankListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_scheduleRankListing.php">경기별 순위보기</a></li>
                                            <li><a <?php echo $url_now == 'statistics_schuduleListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_schuduleListing.php">경기별 메달보기</a></li>
                                            <li><a <?php echo $url_now == 'statistics_countryListing' ? 'class="menu_blue"' : ''; ?> href="/statistics_countryListing.php">국가별 순위보기</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="gnbItem <?php if ($url_now == "account_mypage" || $url_now == "account_change_pw" || $url_now == "account_signup" || $url_now == "account_list" || $url_now == "account_log") echo "gnb_active"; ?>">
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
                                            <li><a <?php echo $url_now == 'account_mypage' ? 'class="menu_blue"' : ''; ?> href="/account_mypage.php">계정 정보</a></li>
                                            <li><a <?php echo $url_now == 'account_change_pw' ? 'class="menu_blue"' : ''; ?> href="/account_change_pw.php">비빌번호 변경</a></li>
                                            <li><a <?php echo $url_now == 'account_signup' ? 'class="menu_blue"' : ''; ?> href="/account_signup.php">계정 생성</a></li>
                                            <li><a <?php echo $url_now == 'account_list' ? 'class="menu_blue"' : ''; ?> href="/account_list.php">계정 목록</a></li>
                                            <li><a <?php echo $url_now == 'account_log' ? 'class="menu_blue"' : ''; ?> href="/account_log.php">로그 목록</a></li>
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