    <!-- 국가등록 -->
    <?php
    require_once __DIR__ . "/../../backheader.php";

    if (!isset($_POST['code']) || $_POST['code'] == '' || !isset($_POST['name']) || $_POST['name'] == '' || !isset($_POST['name_kr']) || $_POST['name_kr'] == '') {
        echo "<script>alert('모두 입력하세요.'); history.back();</script>";
    } else {
        $code = trim($_POST['code']);
        $name = trim($_POST['name']);
        $name_kr = trim($_POST['name_kr']);
        $sql = "SELECT * FROM list_country WHERE country_code = ? or country_name = ? or country_name_kr = ?"; //국가 중복검사
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $code, $name, $name_kr);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!mysqli_fetch_array($result)) {
            $sql2 = "INSERT INTO list_country (country_code, country_name, country_name_kr) VALUES(?,?,?)";
            $stmt = $db->prepare($sql2);
            $stmt->bind_param('sss', $code, $name, $name_kr);
            $stmt->execute();
            echo "<script>alert('등록되었습니다.'); location.href='../../sport_country_input.php';</script>";
        } else {
            echo "<script>alert('이미 등록된 국가입니다.'); history.back();</script>";
        }
    }


    ?>