<?php

include_once(__DIR__ . "/../../backheader.php");

    
    if(!isset($_POST['current_athlete']) || $_POST['current_athlete']==""||!isset($_POST['schedule_round']) || $_POST['schedule_round']==""||!isset($_POST['schedule_sports']) || $_POST['schedule_sports']==""){
        echo "<script>alert('잘못된 경로입니다.'); history.back();</script>";
        exit;
    }else if(!isset($_POST['change_athlete']) || $_POST['change_athlete']=="non"){
        echo "<script>alert('변경할 선수를 설정하지 않았습니다.'); history.back();</script>";
        exit;
    }else{
        $current_record = $_POST['current_athlete'];
        $change_record = $_POST['change_athlete'];

        $schedule_sports = $_POST['schedule_sports'];
        $schedule_round = $_POST['schedule_round'];

        //@Potatoeunbi
        //테이블에서 고른 선수의 record를 배열에 담음
        $sql="SELECT r.record_id, r.record_order  FROM list_record AS r inner join list_schedule AS s on schedule_sports= '".$schedule_sports."' AND schedule_round= '".$schedule_round."' and r.record_schedule_id=s.schedule_id WHERE record_athlete_id='".$current_record."';";
        $result=$db->query($sql);

        while($current = mysqli_fetch_array($result)){
            $current_orders[]=$current['record_order'];
            $current_id[]=$current['record_id'];
        }

        //바꾸려고 select에서 고른 선수의 record를 배열에 담음
        $Csql="SELECT r.record_id, r.record_order  FROM list_record AS r inner join list_schedule AS s on schedule_sports= '".$schedule_sports."' AND schedule_round= '".$schedule_round."' and r.record_schedule_id=s.schedule_id WHERE record_athlete_id='".$change_record."';";
        $Cresult=$db->query($Csql);

        while($change = mysqli_fetch_array($Cresult)){
            $change_orders[]=$change['record_order'];
            $change_id[]=$change['record_id'];
        }

        //서로 맞바꿔서 update해줌.
        for($i=0;$i<count($change_id);$i++){
            $usql = "UPDATE list_record SET record_order=? WHERE record_id = ?";
            $stmt=$db->prepare($usql);
            $stmt->bind_param("ss",$change_orders[$i], $current_id[$i]);
            $stmt->execute();
            $usql = "UPDATE list_record SET record_order=? WHERE record_id = ? ";
            $stmt=$db->prepare($usql);
            $stmt->bind_param("ss",$current_orders[$i], $change_id[$i]);
            $stmt->execute();
        }
                
            

        
        echo "<script>alert('수정되었습니다.'); window.close();</script>";
        exit;
    }




      ?>