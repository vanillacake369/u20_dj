<?php
// 세션 확인
require_once __DIR__ . "/../../includes/auth/config.php";
// 로그 기능
require_once __DIR__ . "/../../backheader.php";
// 삭제 쿼리 WHERE 절을 위한 변수
$sport = trim($_GET["record_sports"] ?? NULL);
$group = trim($_GET["record_group"] ?? NULL);
$round = trim($_GET["record_round"] ?? NULL);
$gender = trim($_GET["record_gender"] ?? NULL);
// 삭제 쿼리 실행
$delete_query_state = 'DELETE FROM list_record WHERE record_sports=\'' . $sport . '\' AND record_group=' . $group . ' AND record_round=\'' . $round . '\' AND record_gender=\'' . $gender . '\';';
$db->query($delete_query_state);
// '조 편성 삭제' 로그 생성
logInsert($db, $_SESSION['Id'], '조 편성 삭제', $sport . '-' . $group . '-' . $round . '-' . $gender);
// 이전 페이지(조 편성 관리 페이지)로 돌아가기
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}