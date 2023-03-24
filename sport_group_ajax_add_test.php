<?php
function addNewRow($name, $age, $gender)
{
    // add new row to the table with the given data
    echo "<tr><td>$name</td><td>$age</td><td>$gender</td></tr>";
}

if (isset($_POST['functionName']) && $_POST['functionName'] == 'addNewRow') {
    // get the data passed from Ajax call
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    // call the function with the data
    addNewRow($name, $age, $gender);
    exit(); // stop executing the script after the function call
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add New Row to Table Using Ajax</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#addBtn").click(function() {
                var name = $("#name").val();
                var age = $("#age").val();
                var gender = $("#gender").val();
                $.ajax({
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>", // current page URL
                    type: "POST",
                    data: {
                        functionName: "addNewRow",
                        name: name,
                        age: age,
                        gender: gender
                    },
                    success: function(result) {
                        $("#dataTable").append(result); // add new row to the table
                        $("#name").val('');
                        $("#age").val('');
                        $("#gender").val('');
                    }
                });
            });
        });
    </script>
</head>

<body>
    <label>Name: <input type="text" id="name"></label>
    <label>Age: <input type="text" id="age"></label>
    <label>Gender:
        <select id="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </label>
    <button id="addBtn">Add to Table</button>
    <table id="dataTable">
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
        </tr>
        <!-- existing table rows here -->
    </table>
</body>

</html>