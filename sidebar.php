<?php include_once(__DIR__ . "/backheader.php");
$id = $_SESSION['Id'];

$basename = basename($_SERVER["PHP_SELF"]);
echo $basename . "<br>";
$pieces = explode("_", $basename);

if (empty($pieces[1])) {
    $pieces[1] = 1;
}
?>

<div class="container">
    <div class="sidebar">
        <ul class="accordion">
            <?php if (authCheck($db, "authEntrysRead")) { ?>

            <?php
                if ($pieces[0] === "entry") {
                    echo '<li class="accordion_li on active"> <p class="menu_button">
                    <i class="fa-solid fa-angle-down"></i>
                    참가자 관리
                </p>';
                } else {
                    echo '<li class="accordion_li on"> <p class="menu_button">
                    <i class="fa-solid fa-angle-right"></i>
                    참가자 관리
                </p>';
                }
                ?>
            <div class="accordion_content">
                <ul>
                    <?php
                        if ($pieces[1] === "athlete.php") {
                            echo '<li class="active"><a href="entry_athlete.php">선수 목록</a></li>';
                        } else {
                            echo '<li><a href="entry_athlete.php">선수 목록</a></li>';
                        }
                        if ($pieces[1] === "coach.php") {
                            echo '<li class="active"><a href="entry_coach.php">코치 목록</a></li>';
                        } else {
                            echo '<li><a href="entry_coach.php">코치 목록</a></li>';
                        }
                        if ($pieces[1] === "judge.php") {
                            echo '<li class="active"><a href="entry_judge.php">심판 목록</a></li>';
                        } else {
                            echo '<li><a href="entry_judge.php">심판 목록</a></li>';
                        }
                        if ($pieces[1] === "director.php") {
                            echo '<li class="active"><a href="entry_director.php">임원 목록</a></li>';
                        } else {
                            echo '<li><a href="entry_director.php">임원 목록</a></li>';
                        }
                        ?>
                </ul>
            </div>
            </li>
            <?php } ?>
            <?php if (authCheck($db, "authSchedulesRead")) { ?>
            <?php
                if ($pieces[0] === "sport") {
                    echo '<li class="accordion_li on active"> <p class="menu_button">
                    <i class="fa-solid fa-angle-down"></i>
                    경기 관리
                </p>';
                } else {
                    echo '<li class="accordion_li on"> <p class="menu_button">
                    <i class="fa-solid fa-angle-right"></i>
                    경기 관리
                </p>';
                }
                ?>
            <div class="accordion_content">
                <ul>
                    <?php
                        if ($pieces[1] === "management.php") {
                            echo '<li class="active"><a href="sport_management.php">경기 목록</a></li>';
                        } else {
                            echo '<li><a href="sport_management.php">경기 목록</a></li>';
                        }
                        if ($pieces[1] === "countrymanagement.php") {
                            echo '<li class="active"><a href="sport_countrymanagement.php">국가 목록</a></li>';
                        } else {
                            echo '<li><a href="sport_countrymanagement.php">국가 목록</a></li>';
                        }
                        if ($pieces[1] === "schedulemanagement.php") {
                            echo '<li class="active"><a href="sport_schedulemanagement.php">일정 목록</a></li>';
                        } else {
                            echo '<li><a href="sport_schedulemanagement.php">일정 목록</a></li>';
                        }
                        ?>
                </ul>
            </div>
            </li>
            <?php } ?>

            <?php if (authCheck($db, "authRecordsRead")) { ?>

            <?php
                if ($pieces[0] === "record") {
                    echo '<li class="accordion_li on active"> <p class="menu_button">
                    <i class="fa-solid fa-angle-down"></i>
                    기록 관리
                </p>';
                } else {
                    echo '<li class="accordion_li on"> <p class="menu_button">
                    <i class="fa-solid fa-angle-right"></i>
                    기록 관리
                </p>';
                }
                ?>
            <div class="accordion_content">
                <ul>
                    <?php
                        if ($pieces[1] === "resultManagement.php") {
                            echo '<li class="active"><a href="record_resultManagement.php">경기결과 목록</a></li>';
                        } else {
                            echo '<li><a href="record_resultManagement.php">경기결과 목록</a></li>';
                        }
                        if ($pieces[1] === "recordHistory.php") {
                            echo '<li class="active"><a href="record_recordHistory.php">역대기록 목록</a></li>';
                        } else {
                            echo '<li><a href="record_recordHistory.php">역대기록 목록</a></li>';
                        }
                        ?>
                </ul>
            </div>
            </li>
            <?php } ?>

            <?php if (authCheck($db, "authStaticsRead")) { ?>
            <?php
                if ($pieces[0] === "statistics") {
                    echo '<li class="accordion_li on active"> <p class="menu_button">
                        <i class="fa-solid fa-angle-down"></i>
                        통계 관리
                    </p>';
                } else {
                    echo '<li class="accordion_li on"> <p class="menu_button">
                        <i class="fa-solid fa-angle-right"></i>
                        통계 관리
                    </p>';
                }
                ?>
            <div class="accordion_content">
                <ul>
                    <?php
                        if ($pieces[1] === "playerRanklisting.php") {
                            echo '<li class="active"><a href="statistics_playerRanklisting.php">선수별 순위보기</a></li>';
                        } else {
                            echo '<li><a href="statistics_playerRanklisting.php">선수별 순위보기</a></li>';
                        }
                        if ($pieces[1] === "newRecordListing.php") {
                            echo '<li class="active"><a href="statistics_newRecordListing.php">신기록 경기기록</a></li>';
                        } else {
                            echo '<li><a href="statistics_newRecordListing.php">신기록 경기기록</a></li>';
                        }
                        if ($pieces[1] === "statistics_scheduleRankListing.php") {
                            echo '<li class="active"><a href="#">경기별 순위보기</a></li>';
                        } else {
                            echo '<li><a href="statistics_scheduleRankListing.php">경기별 순위보기</a></li>';
                        }
                        if ($pieces[1] === "statistics") {
                            echo '<li class="active"><a href="statistics_schuduleListing.php">경기별 메달보기</a></li>';
                        } else {
                            echo '<li><a href="statistics_schuduleListing.php">경기별 메달보기</a></li>';
                        }
                        if ($pieces[1] === "countryListing.php") {
                            echo '<li class="active"><a href="statistics_countryListing.php">국가별 순위보기</a></li>';
                        } else {
                            echo '<li><a href="statistics_countryListing.php">국가별 순위보기</a></li>';
                        }
                        ?>
                </ul>
            </div>
            </li>
            <?php } ?>

            <?php
            if ($pieces[0] === "account") {
                echo '<li class="accordion_li on active">';
            } else {
                echo '<li class="accordion_li on">';
            }
            ?>
            <p class="menu_button">
                <i class="fa-solid fa-angle-right"></i>
                계정 관리
            </p>
            <div class="accordion_content">
                <ul>
                    <?php
                    if ($pieces[1] === "mypage.php") {
                        echo '<li class="active"><a href="account_mypage.php">계정 정보</a></li>';
                    } else {
                        echo '<li><a href="account_mypage.php">계정 정보</a></li>';
                    }
                    if ($pieces[1] === "change") {
                        echo '<li class="active"><a href="account_change_pw.php">비밀번호 변경</a></li>';
                    } else {
                        echo '<li><a href="account_change_pw.php">비밀번호 변경</a></li>';
                    }
                    ?>

                    <?php if (authCheck($db, "authAccountsCreate")) { ?>
                    <?php
                        if ($pieces[1] === "signup.php") {
                            echo '<li class="active"><a href="account_signup.php">계정 생성</a></li>';
                        } else {
                            echo '<li><a href="account_signup.php">계정 생성</a></li>';
                        }
                        ?>
                    <?php }
                    if (authCheck($db, "authAccountsRead")) { ?>
                    <?php
                        if ($pieces[1] === "user.php") {
                            echo '<li class="active"><a href="account_user.php">계정 목록</a></li>';
                        } else {
                            echo '<li><a href="account_user.php">계정 목록</a></li>';
                        }
                        if ($pieces[1] === "log.php") {
                            echo '<li class="active"><a href="account_log.php">로그 목록</a></li>';
                        } else {
                            echo '<li><a href="account_log.php">로그 목록</a></li>';
                        }
                        ?>
                    <?php } ?>
                </ul>
            </div>
            </li>
        </ul>
    </div>
</div>