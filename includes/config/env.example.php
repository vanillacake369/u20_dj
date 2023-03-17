<?php
$variables = [
    'host' => 'localhost', // DB 호스트
    'dbname'=> 'u20_db', // 데이터베이스 이름
    'user'=> 'orange', // 데이터베이스  유저
    'pw'=>'cndnj1029' // 데이터베이스 유저 비밀번호  ㅎㅎ
];

foreach ($variables as $key => $value) {
    putenv("$key=$value");
}
?>