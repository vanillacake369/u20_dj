<?php 
 function jump($schedule_sports){
    echo ($schedule_sports == 'longjump' || $schedule_sports == 'triplejump') ? '1' : '2';
}

function overall($schedule_sports){
//     echo ($schedule_sports == 'decathlon' || $schedule_sports == 'heptathlon') ? '2' : '1';
}
 
 function world($db, $athlete, $new, $schedule_sports,$record)
 {
    if ($new == 'y') {
        $newrecord = $db->query("SELECT worldrecord_athletics FROM list_worldrecord WHERE worldrecord_athlete_name ='" . $athlete . "' AND worldrecord_sports='".$schedule_sports."' and worldrecord_record='".$record."'");
        while ($athletics = mysqli_fetch_array($newrecord)) {
            $newathletics[] = $athletics[0];
        }
        if(($newathletics[0]??null)==='w'){
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="WR';
                echo '" maxlength="100" readonly/></td>';
        }else if(($newathletics[0]??null)==='u'){
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="UWR';
                echo '" maxlength="100" readonly/></td>';
        }else if(($newathletics[0]??null)==='a'){
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="AR';
                echo '" maxlength="100" readonly/></td>';
        }else if(($newathletics[0]??null)==='s'){
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="UAR';
                echo '" maxlength="100" readonly/></td>';
        }else if(($newathletics[0]??null)==='c'){
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="CR';
                echo '" maxlength="100" readonly/></td>';
        }else{
                echo '<td rowspan=';
                overall($schedule_sports);
                echo '><input placeholder="" type="text" name="newrecord[]" value="" maxlength="100" readonly/></td>';
        }
    } else {
        echo '<td rowspan=';
        echo overall($schedule_sports);
        echo ' ><input placeholder="" type="text" name="newrecord[]" value="" maxlength="100" readonly/></td>';
    }
}
