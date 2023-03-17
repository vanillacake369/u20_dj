<?
    require_once "head.php";
    require_once  "includes/auth/config.php";

    $id=$_GET['id'];
     //B:데이터베이스 연결
    $sql= "SELECT DISTINCT * FROM list_record  INNER JOIN list_schedule ON schedule_id= record_schedule_id INNER JOIN list_athlete ON record_athlete_id = athlete_id AND schedule_id = '$id' ORDER BY record_official_result ASC LIMIT 3";
    $result=$db->query($sql);
    $country = array();
    while ($row = mysqli_fetch_assoc($result))
    {
        $country[]=$row["athlete_country"];
    }
?>
<link rel="stylesheet" href="/assets/css/animate.css">
<link rel="stylesheet" href="/assets/css/award_reset.css">
<link rel="stylesheet" href="/assets/css/award_ceremony.css?ver=1">
<script src="/assets/js/jquery-1.12.4.min.js"></script>
</head>

<body>
    <div class="awardCeremony">
        <div class="flag-container first">
            <canvas id="flag1" class="flag"></canvas>
        </div>

        <div class="flag-container second">
            <canvas id="flag2" class="flag"></canvas>
        </div>

        <div class="flag-container third">
            <canvas id="flag3" class="flag"></canvas>
        </div>

        <div class="banner-container">
            <div class="banner-element"></div>
            <div class="bannerLogoContainer">
                <div class="bannerLogo"></div>
            </div>
        </div>

        <video loop="" autoplay="" muted="">
            <source src="/assets/images/background_1.mp4" type="video/ogg">
        </video>
    </div>
    <script>
    var h = new Image();
    h.src = "/assets/images/u20_national_flag/<?=$country[0]?>.png";
    var h2 = new Image();
    h2.src = "/assets/images/u20_national_flag/<?=$country[1]?>.png";
    var h3 = new Image();
    h3.src = "/assets/images/u20_national_flag/<?=$country[2]?>.png";
    </script>
    <script src="/assets/js/award.js?ver=6"></script>
</body>

</html>