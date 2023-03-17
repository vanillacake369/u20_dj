<?php
include __DIR__ . "/database/dbconnect.php";
global $db;
?>


<body>

    <?php

    $search = $_GET['search'];
    ?>
    <h1>검색결과</h1>
    <table>
        <thead>
            <tr>
                <th width="70">국가 코드</th>
                <th width="70">국가 이름</th>
                <th width="70">국가 한글 이름</th>
            </tr>
        </thead>
        <?php
        $sql = "select * from list_country where country_name_kr like '%$search%' order by country_id desc;";
        $row = $db->query($sql);

        while ($data = mysqli_fetch_array($row)) {

        ?>
            <tbody>
                <tr>

                    <!--- 추가부분 18.08.01 END -->
                    <td width="70"><?php echo $data['country_code'] ?></td>
                    <td width="70"><?php echo $data['country_name'] ?></td>
                    <td width="70"><?php echo $data['country_name_kr']; ?></td>

                </tr>
            </tbody>

        <?php } ?>
    </table>
    <form action="country_search.php" method="get">
        <input type="text" name="search" size="40" required="required" /> <button>검색</button>
    </form>

    </div>
</body>

</html>