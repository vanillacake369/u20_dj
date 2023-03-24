<?php
            include_once $_SERVER["DOCUMENT_ROOT"].'/database/dbconnect.php';

            $schedule_sports = $_GET['schedule_sports'];
            $schedule_gender = $_GET['schedule_gender'];
            $schedule_round = $_GET['schedule_round'];
            $schedule_group = $_GET['schedule_group'];


            //schedule_id 
            $sql1 = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ?
                    AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ?";
            $stmt = $db -> prepare($sql1);
            $stmt -> bind_param('ssss', $schedule_sports,  $schedule_gender, $schedule_round, $schedule_group);
            $stmt -> execute();
            $result1 = $stmt -> get_result();
            $row2 = mysqli_fetch_array($result1);

            $schedule_id = $row2['schedule_id'];
            //schedule_id값 null일 때, 되돌아가기
            if(empty($schedule_id)){
                echo "<script>alert('등록되지 않은 경기입니다.');history.back();</script>";
            }

            switch($schedule_sports){
                //릴레이
                case ("4x400mR"):
                case ("4x100mR"):
                    echo "<script>location.href='relay_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id'</script>";
                    break;
                //종합경기
                case ("decathlon"):
                case ("heptathlon"):
                    if($schedule_round=="discusthrow"||$schedule_round=="shotput"||$schedule_round=="javelinthrow"){
                        echo "<script>location.href='throw_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&trial=1&page=1'</script>";
                    }else if($schedule_round=="highjump"||$schedule_round=="polevault"||$schedule_round=="longjump"){
                        echo "<script>location.href='jump_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&trial=1&page=1'</script>";
                    }else{
                        echo "<script>location.href='track_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&page=1'</script>";        
                    }
                    break;
                //던지기
                case ("discusthrow"):    
                case ("hammerthrow"):    
                case ("javelinthrow"):    
                case ("shotput"):    
                    echo "<script>location.href='throw_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&trial=1'</script>";
                    break;
                //높이뛰기
                case ("highjump"):
                case ("longjump");
                case ("polevault");
                case ("triplejump");
                    echo "<script>location.href='jump_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&trial=1'</script>";
                    break;
                default ://100m 100mh 110mh 200m 400m 400mh 경보 800m 1500m, 2000m, 3000m, 3000mSC, 5000m)
                    echo "<script>location.href='track_display.php?schedule_sports=$schedule_sports&schedule_gender=$schedule_gender&schedule_round=$schedule_round&schedule_group=$schedule_group&schedule_id=$schedule_id&page=1'</script>";
                    break;
            }
