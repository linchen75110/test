<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$sql = <<<EOF
DROP TABLE IF EXISTS `mn_fengmian_keyword`;
CREATE TABLE IF NOT EXISTS `mn_fengmian_keyword` (
  `id` int unsigned NOT NULL auto_increment,
  `keyword` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
)ENGINE=MyISAM;
EOF;

runquery($sql);

$finish = TRUE;

?>