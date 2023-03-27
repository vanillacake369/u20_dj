<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/assets/css/style.css" />
    <link rel="stylesheet" href="css/select2.min.css" />
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css" />
    <script src="/assets/fontawesome/js/all.min.js"></script>
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/select2.min.js"></script>
    <title>U20</title>
    <?php include_once(__DIR__ . "/../backheader.php"); ?>

</head>

<body>
    <div class="container" style="padding-bottom:0">
        <form action="test_copy.php" method="post" class="form">
            <div class="athleteInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>선수 선택</option>
                    <?php

                    $sql = "SELECT * FROM list_athlete;";
                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_name'] ?>"><?= $row['athlete_name'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="playername[]" value="" />

            </div>

            <div class="countryInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>국가 선택</option>
                    <?php

                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_country'] ?>"> <?= $row['athlete_country'] ?> </option>

                    <?php }
                    ?>
                    <option></option>
                </select>
                <input type="hidden" name="country[]" value="" />

            </div>


            <div class="athleteInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>소속 선택</option>
                    <?php

                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_division'] ?>"><?= $row['athlete_division'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="division[]" value="" />

            </div>


            <div class="athleteInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>선수 선택</option>
                    <?php

                    $sql = "SELECT * FROM list_athlete;";
                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_name'] ?>"><?= $row['athlete_name'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="playername[]" value="" />

            </div>

            <div class="countryInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>국가 선택</option>
                    <?php

                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_country'] ?>"> <?= $row['athlete_country'] ?> </option>

                    <?php }
                    ?>
                    <option></option>
                </select>
                <input type="hidden" name="country[]" value="" />

            </div>


            <div class="athleteInfo">
                <select id='selUser' name="athlete" required>
                    <option value="" disabled selected>소속 선택</option>
                    <?php

                    $result = $db->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <option value="<?= $row['athlete_division'] ?>"><?= $row['athlete_division'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="division[]" value="" />

            </div>




            <script>
                $("select[name=athlete]").select2();
                $("select[name=athlete]").change(function(idx) {
                    var index = $("select[name='athlete[]']").index(this);
                    var value = $(this).val();
                    var eqValue = $(this).next().next().val(value);
                });
            </script>
            <div class="signup_submit">
                <button type="submit" class="btn_login" name="addresult">
                    <span>확인</span>
                </button>
            </div>
        </form>
    </div>
</body>