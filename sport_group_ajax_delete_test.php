<!DOCTYPE html>
<html>

<head>
    <title>Delete Last Row of Table Using Ajax</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".deleteBtn").click(function() {
                var tableId = $(this).closest("table").attr("id");
                $.ajax({
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>", // current page URL
                    type: "POST",
                    data: {
                        tableId: tableId
                    },
                    success: function(result) {
                        var lastRow = '#' + tableId + ' tr:last-child';
                        $(lastRow).find('input').each(function() {
                            $(this).val('');
                        });
                    }
                });
            });
        });
    </script>
</head>

<body>
    <table id="dataTable">
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Delete</th>
        </tr>
        <tr>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><select>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select></td>
            <td><button class="deleteBtn">Delete</button></td>
        </tr>
        <tr>
            <td><input type="text" value="안녕"></td>
            <td><input type="text" value="아프지마 ㅠ"></td>
            <td><select>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select></td>
            <td><button class="deleteBtn">Delete</button></td>
        </tr>
        <!-- existing table rows here -->
    </table>
</body>

</html>