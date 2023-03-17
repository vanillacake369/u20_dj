<?

// $str_contains = function ($haystack, $needle) {
//     return $needle !== '' && mb_strpos($haystack, $needle) !== false;
// };

function string_contains($haystack, $needle)
{
    return $needle !== '' && mb_strpos($haystack, $needle) !== false;
}

$answer =  string_contains("hah", "oqwo");
if (string_contains("hah", "hah")) {
}
echo "<h1> hi ";
