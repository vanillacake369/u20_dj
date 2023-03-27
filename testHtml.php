<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8" />
<title>Page Title</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="" />
<style></style>
<script src=""></script>

<body>

  <div class="">
    <h1>This is a Heading</h1>
    <?php

    // $str_contains = function ($haystack, $needle) {
    //     return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    // };

    function string_contains($haystack, $needle)
    {
      return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }

    $answer =  string_contains("hah", "ha");
    // $answer =  str_contains("hah", "ha");
    print_r($answer);

    ?>
    <h1>asddsa</h1>
    <p>This is a paragraph.</p>
    <p>This is another paragraph.</p>
  </div>
</body>

</html>