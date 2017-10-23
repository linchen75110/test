<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$sql = <<<SQL
DROP TABLE IF EXISTS mn_fengmian_keyword;
SQL;
runquery($sql);
$finish = TRUE;
?>