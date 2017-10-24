<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_fengmian {
	function list_dir($dirname){//获取目录中的图片列表
		if(!is_dir($dirname)){
			return false;
		}
		$handle=@opendir($dirname);
		$list=array();
		while(($file = @readdir($handle))!==false){
			if($file != '.'&&$file !='..'){
				$list[]=$file;
			}
		}
		closedir($handle);
		return $list;
	}
}

class plugin_fengmian_forum extends plugin_fengmian{
	function viewthread_top_output(){
		//免费版 无自动封面设置功能
		return '<!-- run for free-->';
	}

	function viewthread_useraction(){
		global $_G;
		loadcache('plugin');
		$open_upload=intval($_G['cache']['plugin']['fengmian']['open_upload']);
		if(!$open_upload) return '';
		$groups=unserialize($_G['cache']['plugin']['fengmian']['groups']);
		if($_G['groupid']&&in_array($_G['groupid'],$groups)){
			return '<a href="plugin.php?id=fengmian:cover&tid='.$_G['tid'].'" id="fengmian" onclick="showWindow(this.id, this.href)"><i><img src="static/image/filetype/image_s.gif" alt="'.lang('plugin/fengmian','button').'">'.lang('plugin/fengmian','button').'</i></a>';
		}else{
			return '';
		}
	}
}

?>