<?php

/**
 * @vanillacake369
 * 영문(소문자), 12자 이내로 제한하는 함수
 *
 * @param [type] $input
 * @return void
 */
function cleanInput($input)
{
    $clean = strtolower($input);
    $clean = preg_replace("/[^a-z]/", "", $clean);
    $clean = substr($clean, 0, 12);
    return $clean;
}
/**
 * @vanillacake369
 * 오버플로우 방지 차원에서 정규식 사용
 * - 데이터 길이 제한
 * - 16진 문자열 제거
 *
 * @param [type] $input
 * @return void
 */
function cleanHex($input)
{
    $clean = preg_replace("![\][xX]([A-Fa-f0-9]{1,3})!", "", $input);
    return $clean;
}
