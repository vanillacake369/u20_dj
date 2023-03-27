<?php
$checkedCoaches = $_POST['checked'];
if (empty($checkedCoaches)) {
    echo ("You didn't select");
} else {
    echo ("You did select.");
    echo var_dump($checkedCoaches);
}
