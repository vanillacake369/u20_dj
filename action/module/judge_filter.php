<?php
// month 0 이하 12 초과 필터링
function mq($sql)
{
global $db;
return $db->query($sql);
}
