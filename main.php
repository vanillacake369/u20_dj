<?php

require_once "head.php";
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once "includes/auth/config.php";
// 페이징 기능
require_once "action/module/pagination.php";
// 검색 기능
require_once "action/module/entry_judge_search.php";

$pagesizeValue = getPageSizeValue($_GET["page_size"] ?? NULL);

?>
<script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" charset="utf8" src="/assets/DataTables/datatables.js"></script>
<script type="text/javascript" src="/assets/js/useDataTables.js?ver=3"></script>
</head>

<body>
    <!--header-->
    <?php  require_once "header.php"; ?>
    <!-- contents 본문 내용 -->
    <div class="Area">
        <div class="Wrapper TopWrapper">
            <div class="MainRank">
                <div class="MainRank_tit">
                    <h1>국가 랭킹<i class="xi-crown crown"></i></h1>
                </div>
                <table class="box_table" id="nation_rank">
                    <colgroup>
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">순위</th>
                            <th scope="col">국가</th>
                            <th scope="col">금</th>
                            <th scope="col">은</th>
                            <th scope="col">동</th>
                            <th scope="col">합계</th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                    <?php
                            $i = 1;
                            $same_rank = 0;
                            $prev_total = 0;

                            $sql = "SELECT C.*, 
                                    count(DISTINCT(IF(record_medal=10000,schedule_sports,NULL))) AS gold, 
                                    count(DISTINCT(IF(record_medal=100,schedule_sports,NULL))) as silver, 
                                    count(DISTINCT(IF(record_medal=1,schedule_sports,NULL))) AS bronze, 
                                    GROUP_CONCAT(concat(record_medal,',',record_id,',',schedule_gender) ORDER BY record_id)  AS result_medal,
                                    sum(record_medal) as medal FROM list_country C 
                                    LEFT JOIN list_athlete A ON (C.country_code = A.athlete_country) 
                                    LEFT JOIN list_record R ON (R.record_athlete_id = A.athlete_id) 
                                    LEFT JOIN list_schedule S ON (S.schedule_sports = R.record_sports) AND (S.schedule_round = R.record_round) AND (S.schedule_gender = R.record_gender)
                                    WHERE country_name IS NOT NULL GROUP BY country_code ORDER BY gold DESC, silver DESC, bronze DESC";

                            $result = $db->query($sql);
                            while ($rs_country = mysqli_fetch_array($result)) {

                                $all_total = $rs_country['gold'] + $rs_country['silver'] + $rs_country['bronze'];

                                if ($all_total == $prev_total) {
                                    $same_rank++;
                                } else
                                    $same_rank = 0;

                                $gold = $rs_country['gold'];
                                $silver = $rs_country['silver'];
                                $bronze = $rs_country['bronze'];
                                $total = $gold + $silver + $bronze;


                                echo "<tr>";
                                echo "<td scope='col'>" . ($i - $same_rank) . "</td>";
                                echo "<td scope='col'>" . $rs_country['country_name'] . "(" . $rs_country['country_code'] . ")" . "</td>";
                                echo "<td scope='col'>" . $gold . "</td>";
                                echo "<td scope='col'>" . $silver . "</td>";
                                echo "<td scope='col'>" . $bronze . "</td>";
                                echo "<td scope='col'>" . $total . "</td>";
                                echo "</tr>";
                                $i++;
                                $prev_total = $all_total;
                            }
                            ?>

                    </tbody>
                </table>
            </div>
            <!-- 선수 별 순위 -->
            <div class="MainRank">
                <div class="MainRank_tit">
                    <h1>선수 랭킹<i class="xi-crown crown"></i></h1>
                </div>
                <table class="box_table" id="player_rank">
                    <colgroup>
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                        <col width="auto" />
                    </colgroup>
                    <thead class="table_head entry_table">
                        <tr>
                            <th scope="col">순위</th>
                            <th scope="col">선수</th>
                            <th scope="col">금</th>
                            <th scope="col">은</th>
                            <th scope="col">동</th>
                            <th scope="col">합계</th>
                        </tr>
                    </thead>
                    <tbody class="table_tbody entry_table">
                        <?php
                            $i = 1;
                            $same_rank = 0;
                            $prev_total = 0;
                            $sql = "SELECT
                                    country_code,
                                    athlete_name,
                                    GROUP_CONCAT(concat(record_medal,',',record_id) ORDER BY record_id)  AS result_medal,
                                    sum(record_medal) as s_medal,
                                    COUNT(IF(record_medal=10000,1,null)) AS gold, 
                                    COUNT(IF(record_medal=100,1,null)) AS silver, 
                                    COUNT(IF(record_medal=1,1,null)) AS bronze
                                    FROM list_record R 
                                    INNER JOIN list_athlete A ON (R.record_athlete_id = A.athlete_id)
                                    INNER JOIN list_country C ON (C.country_code = A.athlete_country)
                                    INNER JOIN list_schedule S ON (S.schedule_sports = R.record_sports) AND (S.schedule_round = R.record_round) AND (S.schedule_gender = R.record_gender)
                                    WHERE record_medal >=1 AND schedule_sports IS NOT null
                                    GROUP BY athlete_name
                                    ORDER BY s_medal desc";

                        $result = $db->query($sql);
                        while ($rs = mysqli_fetch_array($result)) {
                            $total = $rs['gold'] + $rs['silver'] + $rs['bronze'];

                            if ($rs['s_medal'] == $prev_total) {
                                $same_rank++;
                            } else
                                $same_rank = 0;

                            echo '<tr>';
                            echo '<td scope="col">' . ($i - $same_rank) . '</td>';
                            echo '<td scope="col">' . $rs['athlete_name'] . "(" . $rs['country_code'] . ")" . '</td>';
                            echo '<td scope="col">' . $rs['gold'] . '</td>';
                            echo '<td scope="col">' . $rs['silver'] . '</td>';
                            echo '<td scope="col">' . $rs['bronze'] . '</td>';
                            echo '<td scope="col">' . $total . '</td>';
                            echo '</tr>';
                            $i++;
                            $prev_total = $rs['s_medal'];
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <script src="/assets/js/main.js?ver=9"></script>
</body>

</html>