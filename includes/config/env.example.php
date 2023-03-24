<?php
$variables = [
    'host' => 'localhost:3306', // DB 호스트
    'dbname' => '', // 데이터베이스 이름
    'user' => '', // 데이터베이스  유저
    'pw' => '' // 데이터베이스 유저 비밀번호  ㅎㅎ
];

foreach ($variables as $key => $value) {
    putenv("$key=$value");
}
