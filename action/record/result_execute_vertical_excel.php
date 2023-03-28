<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
</style>
<table class="box_table">
    <colgroup>
        <col style="width: 3%"/>
        <col style="width: 3%"/>
        <col style="width: 5%"/>
        <col style="width: 15%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 5%"/>
        <col style="width: 9%"/>
    </colgroup>
    <thead class="result_table entry_table">
    <tr>
        <th style="background: none" rowspan="2">등수</th>
        <th style="background: none" rowspan="2">순서</th>
        <th style="background: none" rowspan="2">BIB</th>
        <th style="background: none" rowspan="2">이름</th>
        <?php
        require_once __DIR__ . '/../../database/dbconnect.php';
        global $db;

        $sports = $_POST['sports'];
        $gender = $_POST['gender'];
        $round = $_POST['round'];
        $group = $_POST['group'];

        $sql = "SELECT DISTINCT * FROM list_record join list_schedule where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and schedule_sports=record_sports and schedule_round=record_round and schedule_gender=record_gender and if(record_state='y',record_live_result>0,'1') and record_group='$group'";
        $result = $db->query($sql);
        $rows = mysqli_fetch_assoc($result);
        $schedule_sports = $rows['schedule_sports'];
        $schedule_round = $rows['schedule_round'];
        $schedule_group = $rows['record_group'];
        $schedule_result = $rows['record_status'];
        $group = $rows['record_group'];
        if ($rows['record_status'] == 'o') {
            $result_type = 'official';
        } else {
            $result_type = 'live';
        }
        switch ($schedule_result) {
            case 'l':
                $schedule_result = "Live Result";
                break;
            case 'o':
                $schedule_result = "Official Result";
                break;
            case 'n':
                $schedule_result = "Not Start";
                break;
        }

        $FILE_NAME = $sports . '_' . $gender . '_' . $round . '_' . $group . 'group(' . $schedule_result . ').xls';
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename = " . $FILE_NAME);     //filename = 저장되는 파일명을 설정합니다.
        header("Content-Description: PHP4 Generated Data");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

        // 높이 찾는 쿼리
        $highresult = $db->query("SELECT DISTINCT record_" . $result_type . "_record FROM list_record where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_" . $result_type . "_record>0 limit 12");
        $cnt1 = 0;
        while ($highrow = mysqli_fetch_array($highresult)) {
            echo '<th>' . $highrow["record_" . $result_type . "_record"] . '</th>';
            $cnt1++;
        }
        for ($j = 0; $j < 12 - $cnt1; $j++) {
            echo '<th></th>';
        }
        ?>
        <th rowspan="2">기록</th>
        <th>비고</th>
    </tr>
    <tr id="col2">
        <?php if ($cnt1 == 12) {
            $cnt2 = 0;
            $highresult = $db->query("SELECT DISTINCT record_" . $result_type . "_record 
                  FROM list_record 
                  where record_sports='$schedule_sports' and record_round='$schedule_round' and record_gender ='$gender' and record_group='$group' and record_" . $result_type . "_record>0 
                  limit 12,12");
            while ($highrow = mysqli_fetch_array($highresult)) {
                echo '<th>' . $highrow["record_" . $result_type . "_record"] . '</th>';
                $cnt2++;
            }
            for ($j = 0; $j < 12 - $cnt2; $j++) {
                echo '<th>&nbsp</th>';
            }
        } else {
            for ($j = 0; $j < 12; $j++) {
                echo '<th>&nbsp</th>';
            }
        } ?>
        <th>신기록</th>
    </tr>
    </thead>
    <tbody class="table_tbody De_tbody entry_table">
    <?php
    if ($rows["record_state"] === "y") {
        $order = "record_" . $result_type . "_result";
        $obj = "record_" . $result_type . "_result,record_memo,athlete_id,record_" . $result_type . "_record,";
        $jo = "WHERE record_" . $result_type . "_result>0";
    } else {
        $order = "record_order";
        $obj = "athlete_id,";
        $jo = "";
    }
    $result = $db->query("SELECT DISTINCT " . $obj . "record_order,record_new,athlete_name,athlete_bib FROM list_record 
                                INNER JOIN list_athlete ON athlete_id = record_athlete_id 
                                and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'" . $jo . "
                                ORDER BY " . $order . " ASC , record_" . $result_type . "_record ASC"
    );
    $cnt = 1;
    $num = 0;
    while ($row = mysqli_fetch_array($result)) {

        $num++;
        echo '<tr id=col1 "';
        if ($num % 2 == 0) echo ' class="Ranklist_Background">';
        else echo ">";
        echo '<td rowspan="2">' . ($row["record_" . $result_type . "_result"] ?? null) . '</td>';
        echo '<td rowspan="2">' . $row["record_order"] . '</td>';
        echo '<td rowspan="2" >';
        if (isset($row["athlete_bib"])) echo $row["athlete_bib"]; else echo "";
        '</td>';
        echo '<td rowspan="2" >';
        if (isset($row["athlete_name"])) echo $row["athlete_name"]; else echo "";
        '</td>';
        $cnt3 = 1;
        $record = $db->query(
            "SELECT record_trial FROM list_record
                          INNER JOIN list_athlete ON record_athlete_id=" .
            $row["athlete_id"] .
            " AND athlete_id= record_athlete_id
                          and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group'AND record_" . $result_type . "_record>0
                          ORDER BY cast(record_" . $result_type . "_record as float) ASC limit 12"
        ); //선수별 기록 찾는 쿼리
        while ($recordrow = mysqli_fetch_array($record)) {
            echo "<td>" . $recordrow["record_trial"] . "</td>";
            $cnt3++;
        }
        for ($a = $cnt3; $a <= 12; $a++) {
            //기록을 제외한 빈칸으로 생성
            echo "<td>&nbsp</td>";
        }
        //
        echo '<td rowspan="2">' . ($row["record_" . $result_type . "_record"] ?? null) . "</td>";
        echo '<td>' . ($row["record_memo"] ?? null) . '</td>';
        //
        echo '<tr id=col2';
        if ($num % 2 == 0) echo ' class="Ranklist_Background">';
        else echo ">";
        if ($cnt3 == 13) {
            //13번째 기록부터
            $record = $db->query(
                "SELECT record_trial,record_athlete_id FROM list_record
                          INNER JOIN list_athlete ON record_athlete_id=" .
                $row["athlete_id"] .
                " AND athlete_id= record_athlete_id
                          and record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' AND record_" . $result_type . "_record>0
                          ORDER BY cast(record_" . $result_type . "_record as float) ASC limit 12,12"
            );//선수별 기록 찾는 쿼리
            while ($recordrow = mysqli_fetch_array($record)) {
                echo "<td>" . $recordrow["record_trial"] . "</td>";
                $cnt3++;
            }
        } else {
            $cnt3 = 13;
        }
        for ($a = $cnt3; $a <= 24; $a++) {
            //기록을 제외한 빈칸으로 생성
            echo "<td>&nbsp</td>";
        }
        if ($rows['schedule_sports'] === 'decathlon' || $rows['schedule_sports'] === 'heptathlon') {
            $sport_code = $rows['schedule_sports'] . "(" . $rows['schedule_round'] . ")";
        } else {
            $sport_code = $rows['schedule_sports'];
        }
        if (($row['record_new'] && null) == 'y') {
            if ($rows['record_state'] != 'y') {
                $time = $rows['schedule_start'];
            } else {
                $time = $rows['record_end'];
            }
            $athletics = check_my_record($row['athlete_name'], $sport_code, $time);
            if ((key($athletics) ?? null) === 'w') {
                echo '<td>세계신기록</td>';
            } else if ((key($athletics) ?? null) === 'u') {
                echo '<td>세계U20신기록</td>';
            } else if ((key($athletics) ?? null) === 'a') {
                echo '<td>아시아신기록</td>';
            } else if ((key($athletics) ?? null) === 's') {
                echo '<td>아시아U20신기록</td>';
            } else if ((key($athletics) ?? null) === 'c') {
                echo '<td>대회신기록</td>';
            } else {
                echo '<td>&nbsp</td>';
            }
        } else {
            echo '<td>&nbsp</td>';
        }
        $cnt++;
    }
    ?>
    </tbody>
</table>

