<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>U20</title>
</head>

<body>
    <div class="page">
        <div class="top">
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                <tr>
                    <td style="width: 80%; text-align: right">제20회 예천아시아 U20 육상경기선수권대회</td>
                    <td style="text-align: right"><img style="width: 80px; height: 40px"
                            src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/assets/img/print_logo.png" ?>"
                            alt="Logo" class="logo_img" /></td>
                </tr>
            </table>
        </div>

        <div class="middle" style="display:inline-block">
            <p style="margin:10px 0px 0px 0px; text-align:center;">RESULT[경기결과]</p>
            <?php
        include_once(__DIR__ . "/../database/dbconnect.php"); //B:데이터베이스 연결
        /* word 다운을 위한 해더 */
        header("Content-type: application/vnd.ms-word;charset=UTF-8");
        header("Content-Disposition: attachment; filename=word_download_test.doc");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-word; charset=utf-8\">");

        $sql="select 
                        schedule_location,
                        schedule_sports,
                        schedule_gender, 
                        schedule_date,
                        athlete_birth,
                        athlete_bib 
                        from list_schedule S 
                        INNER JOIN list_athlete A ON (S.schedule_sports = A.athlete_schedule) 
                        where schedule_name= '".$_POST['gamename']."' and schedule_status='y' and schedule_round='".$_POST['round']."'";
        $result=$db->query($sql);
        $row = mysqli_fetch_assoc($result);
        ?>
            <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table">
                <tr>
                    <?php
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">종목명: ' . $_POST['gamename'] . '</p></td>';
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">위치: ' . $row['schedule_location'] . '</p></td>';
                ?>
                </tr>
                <tr>
                    <?php
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">성별: ' . $row['schedule_gender'] . '</p></td>';
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">일자: ' . $row['schedule_date'] . '</p></td>';
                ?>
                </tr>
                <tr>
                    <?php
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">라운드: ' . $_POST['round'] . '</p></td>';
                echo '<td><p style="font-size:12px; width:330px; margin-bottom: 1em">풍속: ' . $_POST['wind'] . '</p></td>';
                ?>
                </tr>
            </table>
            <div class="table_area" style="margin-bottom: 50px;">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab2"
                    style="border-top: 2px solid black">
                    <colgroup>
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 30%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid gray">순위</th>
                            <th style="border-bottom: 1px solid gray">등번호</th>
                            <th style="border-bottom: 1px solid gray">성명</th>
                            <th style="border-bottom: 1px solid gray">국가</th>
                            <th style="border-bottom: 1px solid gray">생년월일</th>
                            <th style="border-bottom: 1px solid gray">레인</th>
                            <th style="border-bottom: 1px solid gray">기록</th>
                            <th style="border-bottom: 1px solid gray">비고</th>
                            <th style="border-bottom: 1px solid gray">신기록</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                $j=0;

                for($i=0;$i<count($_POST['rain']);$i++){
                    echo '<tr>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$_POST['rank'][$i].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$row['athlete_bib'].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">';
                    $k=$j+4;
                    for($j;$j<$k;$j++){
                        echo $_POST['playername'][$j].'<br>';
                    }
                    $country=$db->query("select athlete_country from list_athlete where athlete_name ='".$_POST['playername'][$j-1]."'");
                    // echo "select athlete_country from list_athlete where athlete_name =".$_POST['playername'][$i]."";
                    $row1=mysqli_fetch_array($country);
                    echo '</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$row1[0].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray; font-size: 10px">'.$row['athlete_birth'].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$_POST['rain'][$i].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$_POST['gameresult'][$i].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray">'.$_POST['bigo'][$i].'</td>';
                    echo '<td style="padding-bottom: 1.5em; border-bottom: 1px solid gray; font-size: 10px">'.$_POST['newrecord'][$i].'</td>';
                    echo '</tr>';
                }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="total">
            <p>종합신기록</p>
            <div class="table_area">
                <table width="100%" cellspacing="0" cellpadding="0" class="table table-hover team_table tab2"
                    style="border-top: 2px solid black">
                    <colgroup>
                        <col style="width: 20%" />
                        <col style="width: 15%" />
                        <col style="width: 10%" />
                        <col style="width: 30%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid gray">구분</th>
                            <th style="border-bottom: 1px solid gray">기록</th>
                            <th style="border-bottom: 1px solid gray">풍속</th>
                            <th style="border-bottom: 1px solid gray">성명</th>
                            <th style="border-bottom: 1px solid gray">소속</th>
                            <th style="border-bottom: 1px solid gray">일자</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                $world=$db->query("SELECT distinct worldrecord_record,worldrecord_athletics, worldrecord_wind, worldrecord_athlete_name, worldrecord_country_code, worldrecord_datetime 
                                                FROM list_worldrecord a
                                                inner JOIN (SELECT Min(ROUND(cast(worldrecord_record as float),2)) AS MAX_record
                                                FROM list_worldrecord 
                                                WHERE worldrecord_sports ='".$row['schedule_sports']."' AND worldrecord_gender='".$row['schedule_gender']."' 
                                                GROUP BY worldrecord_athletics) b
                                                ON a.worldrecord_record =b.Max_record AND worldrecord_sports ='".$row['schedule_sports']."' AND worldrecord_gender='".$row['schedule_gender']."'  
                                                order by FIELD(worldrecord_athletics, 'w','u','a','s','c')");
                while($row2=mysqli_fetch_array($world)){
                    echo '<tr>';
                    switch($row2['worldrecord_athletics']){
                        case 'w': echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">세계신기록</td>'; break;
                        case 'u': echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">세계U20신기록</td>'; break;
                        case 's': echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">아시아신기록</td>'; break;
                        case 'a': echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">아시아U20신기록</td>'; break;
                        case 'c': echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">대회신기록</td>'; break;
                        default:  echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray"></td>'; break;
                    }
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">'.$row2['worldrecord_record'].'</td>';
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">'.$row2['worldrecord_wind'].'</td>';
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">'.$row2['worldrecord_athlete_name'].'</td>';
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">'.$row2['worldrecord_country_code'].'</td>';
                    echo '<td style="margin-bottom: 1em; border-bottom: 1px solid gray">'.$row2['worldrecord_datetime'].'</td>';
                    echo '</tr>';
                }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>