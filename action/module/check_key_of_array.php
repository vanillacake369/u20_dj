<?php
/**
 * @author 지훈
 * 배열 안에 키 값이 존재하면 키가 가르키는 value를 가져오는 함수
 */
function getValueWhenKeyExists($key,$array){
    $hasKey = array_key_exists($key, $array);
    return $hasKey ? $array[$key] : "";
}