<?php

include_once(__DIR__ . "/../../backheader.php");

    
    if(!isset($_POST['current_athlete']) || $_POST['current_athlete']==""){
        echo "<script>alert('잘못된 경로입니다.'); history.back();</script>";
        exit;
    }else if(!isset($_POST['change_athlete']) || $_POST['change_athlete']=="non"){
        echo "<script>alert('변경할 선수를 설정하지 않았습니다.'); history.back();</script>";
        exit;
    }else{
        $current_record = $_POST['current_athlete'];
        $change_record = $_POST['change_athlete'];

        //@Potatoeunbi
        //테이블에서 고른 선수의 record를 배열에 담음
        $sql="SELECT r1.record_id, r1.record_order, r1.record_schedule_id FROM list_record AS r1 join list_record AS r2 ON r2.record_id='".$current_record."' AND r1.record_athlete_id=r2.record_athlete_id AND r1.record_schedule_id=r2.record_schedule_id;";
        $result=$db->query($sql);
        while($current = mysqli_fetch_array($result)){
            $current_orders[]=$current['record_order'];
            $current_id[]=$current['record_id'];
            $current_schedule_id[]=$current['record_schedule_id'];
        }

        //바꾸려고 select에서 고른 선수의 record를 배열에 담음
        $Csql="SELECT r1.record_id, r1.record_order, r1.record_schedule_id FROM list_record AS r1 join list_record AS r2 ON r2.record_id='".$change_record."' AND r1.record_athlete_id=r2.record_athlete_id AND r1.record_schedule_id=r2.record_schedule_id;";
        $Cresult=$db->query($Csql);
        while($change = mysqli_fetch_array($Cresult)){
            $change_orders[]=$change['record_order'];
            $change_id[]=$change['record_id'];
            $change_schedule_id[]=$change['record_schedule_id'];
        }

        //서로 맞바꿔서 update해줌.
        for($i=0;$i<count($change_id);$i++){
        $sql = "UPDATE list_record SET record_schedule_id = ?, record_order=? WHERE record_id = ?";
        $stmt=$db->prepare($sql);
        $stmt->bind_param("sss",$change_schedule_id[$i],$change_orders[$i], $current_id[$i]);
        $stmt->execute();

        $sql = "UPDATE list_record SET record_schedule_id = ?, record_order=? WHERE record_id = ?";
        $stmt=$db->prepare($sql);
        $stmt->bind_param("sss",$current_schedule_id[$i],$current_orders[$i], $change_id[$i]);
        $stmt->execute();
        }
        echo "<script>alert('수정되었습니다.'); window.close(); </script>";
        exit;

    }

      ?>