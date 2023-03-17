<?php

    include_once(__DIR__ . "/../../backheader.php");
    include_once(__DIR__ . "/../../security/security.php");

    if(!isset($_POST['contact']) || $_POST['contact']==""){
        echo "<script>alert('모두 선택하세요.'); history.back();</script>";
        exit;

        //@Potatoeunbi
        //국가를 선택했을 경우(국가 lane 순서를 바꿈)
    }else if($_POST['contact']=='국가'&&$_POST['contact']!='non'){
        $current_record = cleanInput($_POST['current_country']);
        $change_record = cleanInput($_POST['change_country']);

        //테이블에서 고른 선수의 record를 배열에 담음
        $sql="SELECT r1.record_id, r1.record_order, r1.record_schedule_id FROM list_record AS r1 join list_record AS r2 ON r2.record_id='".$current_record."' AND r1.record_order=r2.record_order AND r1.record_schedule_id=r2.record_schedule_id;";
        $result=$db->query($sql);
        while($current = mysqli_fetch_array($result)){
            $current_orders[]=$current['record_order'];
            $current_id[]=$current['record_id'];
            $current_schedule_id[]=$current['record_schedule_id'];
        }
        //@Potatoeunbi
        //바꾸려고 select에서 고른 선수의 record를 배열에 담음
        $Csql="SELECT r1.record_id, r1.record_order, r1.record_schedule_id FROM list_record AS r1 join list_record AS r2 ON r2.record_id='".$change_record."' AND r1.record_order=r2.record_order AND r1.record_schedule_id=r2.record_schedule_id;";
        $Cresult=$db->query($Csql);
        while($change = mysqli_fetch_array($Cresult)){
            $change_orders[]=$change['record_order'];
            $change_id[]=$change['record_id'];
            $change_schedule_id[]=$change['record_schedule_id'];
        }
        //@Potatoeunbi
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
        
        //@Potatoeunbi
        //선수를 선택했을 경우(해당 국가 주자를 바꿈)
    }else if($_POST['contact']=='선수'&&!in_array('non', $_POST['athlete'])){
        $athlete=array();
        $current_record = cleanInput($_POST['current_country']);
        $athlete = $_POST['athlete'];

        //@Potatoeunbi
        //select에서 고른 선수들의 record를 배열에 담음
        $sql="SELECT r1.record_id FROM list_record AS r1 join list_record AS r2 ON r2.record_id='".$current_record."' AND r1.record_order=r2.record_order AND r1.record_schedule_id=r2.record_schedule_id;";
        $result=$db->query($sql);
        while($current = mysqli_fetch_array($result)){
            $current_id[]=$current['record_id'];
        }

        //@Potatoeunbi
        //선택한 선수들이 중복일 경우 history.back
        function isOne($val){
            return $val!=1;
        }
        $ac = array_replace($athlete,array_fill_keys(array_keys($athlete, null),''));
        $arr = array_count_values($ac);
        $filter_result = array_filter ( $arr , "isOne" );

        if(!empty($filter_result)){
            echo "<script>alert('중복된 선수가 있습니다.');  history.back(); </script>";
            exit;
        }



        //중복이 아니면 update해줌
        for($i=0;$i<count($current_id);$i++){
            $sql = "UPDATE list_record SET record_athlete_id = ? WHERE record_id = ?";
            $stmt=$db->prepare($sql);
            $stmt->bind_param("ss",$athlete[$i], $current_id[$i]);
            $stmt->execute();
        }
        echo "<script>alert('수정되었습니다.'); window.close(); </script>";
        exit;

    }


      ?>