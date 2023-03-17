<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include_once $_SERVER["DOCUMENT_ROOT"].'/database/dbconnect.php';
            include_once "../action/module/dictionary.php";?>

</head>

<body>
    <form action="startlist.php" method="get" name="form">
        <div>
            <label>전광판에 띄울 경기</label>
            <select onchange="categoryChange(this)" name="schedule_sports" id="schedule_sports">
                <option value='non' hidden="">참가 경기</option>
                <option value="non">전체</option>
                <?php
            $events = array_unique($categoryOfSports_dic);
            foreach ($events as $e) {
                echo "<optgroup label=\"$e\">";
                $sportsOfTheEvent = array_keys($categoryOfSports_dic, $e);
                foreach ($sportsOfTheEvent as $s) {
                    echo "<option value=$s >". $sport_dic[$s] . "</option>";
                }
                echo "</optgroup>";
            }
            ?>
            </select>

            <select name="schedule_round" id="good">
                <option>라운드</option>
            </select>
            <select title="성별" name="schedule_gender" style="width:5em;">
                <option value="non">성별</option>
                <?php
            $sSql = "SELECT distinct schedule_gender FROM list_schedule;";
            $sResult = $db->query($sSql);
            while ($sRow = mysqli_fetch_array($sResult)) {
                echo "<option value=" . $sRow['schedule_gender'] . ' ' . ($searchValue["worldrecord_gender"] == $sRow['schedule_gender'] ? 'selected' : '') . ">" . ($sRow['schedule_gender'] == 'm' ? '남' : ($sRow['schedule_gender'] == 'f' ? '여' : '혼성')) . "</option>";
            }
            ?>
            </select>
            <select title="schedule group" name="schedule_group" style="width:5em;">
                <option value="non" hidden="">그룹</option>
                <?php
                $Sql="SELECT distinct schedule_group FROM list_schedule WHERE schedule_division = 's';";
                $Result=$db->query($Sql);
                while ($Row = mysqli_fetch_array($Result)){
                    echo "<option value=".$Row['schedule_group'].' '.($_GET["schedule_group"]==$Row['schedule_group']?'selected':'').">".htmlspecialchars($Row['schedule_group'])."</option>";
                }
            ?>
            </select>

            <script>
            function categoryChange(e) {
                var good_a = ["qualification", "semi-final", "final"];
                var good_b = ["final"];
                var good_c = ["100m", "400m", "1500m", "110mh", "discusthrow", "highjump", "javelinthrow", "longjump",
                    "polevault", "shotput"
                ];
                var good_d = ["200m", "800m", "100mh", "highjump", "javelinthrow", "longjump", "shotput"];
                var target = document.getElementById("good");

                if (e.value == "100m" || e.value == "100mh" || e.value == "110mh" || e.value == "200m" || e.value ==
                    "400m" || e.value == "400mh" || e.value == "800m" || e.value == "4x100mR" || e.value == "4x400mR" ||
                    e.value == "1500m" || e.value == "3000m" || e.value == "3000mSC" || e.value == "5000m" || e.value ==
                    "10000m") var z = good_a;
                else if (e.value == "discusthrow" || e.value == "javelinthrow" || e.value == "shotput" || e.value ==
                    "hammerthrow" || e.value == "longjump" || e.value == "triplejump" || e.value == "highjump" || e
                    .value == "polevault" || e.value == "racewalk") var z = good_b;
                else if (e.value == "decathlon") var z = good_c;
                else if (e.value == "heptathlon") var z = good_d;

                target.options.length = 0;

                for (x in z) {
                    var opt = document.createElement("option");
                    opt.value = z[x];
                    opt.innerHTML = z[x];
                    target.appendChild(opt);
                }
            }
            </script>
        </div>
        <input type="hidden" name="page" value="1">
        <input type="submit" value="Start List" style="width:100px; height:30px; margin-bottom: 30px;">
    </form>
</body>

</html>