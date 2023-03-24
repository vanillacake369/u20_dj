<?php
    require_once "head.php";
?>
</head>

<body>
    <div class="page2">
        <div class="pdf_img">
            <img src="/assets/images/logo2.png" alt="Logo" class="logo_img" /></a>
        </div>
        <div class="paf_tit">
            <p>Player Record Certificate</p>
        </div>
        <div class="pdf_Number">
            <p>No: </p>
        </div>
        <div class="pdf_left">
            <div>
                <p>Name: <?php echo htmlspecialchars($_GET['athlete_name'] ?? NULL) ?></p>
                <!-- 선수 이름 -->
                <p>D.O.B: <?php echo htmlspecialchars($_GET['athlete_birth'] ?? NULL) ?></p>
                <!-- 생일 -->
                <p>Nationality: <?php echo htmlspecialchars($_GET['athlete_country'] ?? NULL) ?></p>
                <!-- 국적 -->
            </div>
        </div>
        <div class="pdf_middle">
            <div>
                <span class="middle_tit">Match</span>
                <p><?php echo htmlspecialchars($_GET['record_sports'] ?? NULL) ?></p>
                <!-- 경기 종목 -->
            </div>
            <div>
                <span class="middle_tit">Ranking </span>
                <p><?php echo htmlspecialchars($_GET['result'] ?? NULL) ?></p>
                <!-- 등수 -->
            </div>
            <div>
                <span class="middle_tit">Record</span>
                <p><?php echo htmlspecialchars($_GET['record'] ?? NULL) ?></p>
                <!-- 풍속 용기구 -->
            </div>
            <div>
                <span class="middle_tit">Wind speed/Apparatus</span>
                <?php
                $wind = $_GET['wind'] ?? NULL;
                $weight = $_GET['weight'] ?? NULL;
                if ($wind === "")
                    $wind = 'NONE';
                if ($weight === "")
                    $weight = 'NONE';
                ?>
                <p><?php echo $wind ?>/<?php echo $weight ?></p>
                <!-- 풍속 용기구 -->
            </div>
        </div>
        <div class="pdf_certi">
            <p>Yecheon U20 certifies the above.</p>
        </div>
        <div class="pdf_date">
            <!-- <p>2023.03.21</p> -->
            <p><?php echo htmlspecialchars(date("y.m.d")) ?></p>
        </div>
        <div class="pdf_stamp">
            <p>Asia Federation President</p>
        </div>
    </div>
</body>