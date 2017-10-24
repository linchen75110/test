<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
@set_time_limit(0);
$doing=trim($_GET['doing']);
$wid=intval($_GET['wid']);
if($doing=='view'&&$wid&&$_GET['formhash']==formhash()){
	$info=DB::fetch_first("select * from ".DB::table('fengmian_keyword')." where id='$wid'");
	if(!$info) cpmsg(lang('plugin/fengmian','view_error'));
	showtableheader(lang('plugin/fengmian','view_title',array('keyword'=>$info['keyword'],'dir'=>'/data/fengmian/'.$wid,'url'=>ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=fengmian&pmod=keyword')));
	showsubtitle(array('',lang('plugin/fengmian','view_m_1'),lang('plugin/fengmian','view_m_2')));
	$worddir = DISCUZ_ROOT.'./data/fengmian/'.$wid.'/';
	$list=list_dir($worddir);
	if(!$list||count($list)==0) echo '<tr><td colspan="3">'.lang('plugin/fengmian','view_nopic').'</td></tr>';
	foreach($list as $k=>$pic){
		showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
			'',
			'<img src="data/fengmian/'.$wid.'/'.$pic.'" width="220px" height="150px;"/>',
			'data/fengmian/'.$wid.'/'.$pic,
		));
	}
	showtablefooter();
}elseif($doing=='delete'&&$wid&&$_GET['formhash']==formhash()){
	DB::delete('fengmian_keyword',array('id'=>$wid));
	$worddir = DISCUZ_ROOT.'./data/fengmian/'.$wid.'/';
	d_rmdir($worddir);
	createKeywordCache();
	cpmsg(lang('plugin/fengmian','update_ok'),'action=plugins&operation=config&identifier=fengmian&pmod=keyword', 'succeed');
}elseif(submitcheck('submit')){
	$addnum=0;
	foreach($_POST['newkeyword'] as $k=>$w){
		$word=addslashes(trim($_POST['newkeyword'][$k]));
		if($word){
			$old=DB::result_first("SELECT `id` FROM ".DB::table('fengmian_keyword')." WHERE `keyword` like '$word'");
			if(!$old){//ÐÂÌí¼Ó
				$wid=DB::insert('fengmian_keyword',array('keyword' => $word),TRUE);
				$worddir = DISCUZ_ROOT.'./data/fengmian/'.$wid.'/';
				@mkdir($worddir,0777,true);
				$addnum++;
			}
		}
	}
	if($addnum) createKeywordCache();
	cpmsg(lang('plugin/fengmian','update_ok'),'action=plugins&operation=config&identifier=fengmian&pmod=keyword', 'succeed');
}else{
	showtableheader(lang('plugin/fengmian','list_tips_title'));
	showtablerow('',array('colspan="9" class="tipsblock"'), array(lang('plugin/fengmian','list_tips_info')));
	showtablefooter();
	showformheader('plugins&operation=config&do='.$pluginid.'&identifier=fengmian&pmod=keyword');
	showtableheader(lang('plugin/fengmian','list_title'));
	showsubtitle(array('',lang('plugin/fengmian','list_m_1'),lang('plugin/fengmian','list_m_2'),lang('plugin/fengmian','list_m_3'),lang('plugin/fengmian','list_m_4')));
	$pagenum=30;
	$page=max(1,intval($_GET['page']));
	$count=DB::result_first("SELECT COUNT(*) FROM ".DB::table('fengmian_keyword')." ");
	$query=DB::query("SELECT * FROM ".DB::table('fengmian_keyword')." ORDER BY id DESC LIMIT ".(($page-1)*$pagenum).",$pagenum");
	$pages=multi($count,$pagenum, $page, ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=fengmian&pmod=keyword');
	while($row = DB::fetch($query)){
		showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
			'',
			'<input type="hidden" class="txt" name="keyword['.$row['id'].']" value="'.$row['keyword'].'"/>'.$row['keyword'],
			'/data/fengmian/'.$row['id'].'/',
			'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=fengmian&pmod=keyword&doing=view&wid='.$row['id'].'&formhash='.FORMHASH.'">'.lang('plugin/fengmian','list_op_view').'</a>',
			'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=fengmian&pmod=keyword&doing=delete&wid='.$row['id'].'&formhash='.FORMHASH.'">'.lang('plugin/fengmian','list_op_delete').'</a>',
		));
	}
	echo '<tr><td></td><td colspan="4"><div><a href="javascript:;" onclick="addrow(this, 0)" class="addtr">'.lang('plugin/fengmian','list_op_add').'</a></div></td></tr>';
	if($pages) echo '<tr><td></td><td colspan="4">'.$pages.'</td></tr>';
	echo '<script type="text/JavaScript">
	var rowtypedata = new Array();
	rowtypedata[0] = [
				[1,"<input type=\"hidden\" class=\"txt\" name=\"newdel[]\" value=\"\">"],
				[1,\'<input type="text" class="txt" name="newkeyword[]" />\', "td_k"],
				[1,\'\', "td_k"],
			];
	</script>';
	showsubmit('submit',lang('plugin/fengmian','list_op_submit'),lang('plugin/fengmian','list_op_tips'));
	showtablefooter();
	showformfooter();
}

function d_rmdir($dirname){   //É¾³ý·Ç¿ÕÄ¿Â¼
	if(!is_dir($dirname)) {
		return false;
	}
	$handle = @opendir($dirname);
	while(($file = @readdir($handle))!==false){
		if($file != '.' && $file != '..'){
			$dir = $dirname.'/'.$file;
			is_dir($dir) ? d_rmdir($dir) : unlink($dir);
		}
	}
	closedir($handle);
	return rmdir($dirname);
}

function list_dir($dirname){   //É¾³ý·Ç¿ÕÄ¿Â¼
	if(!is_dir($dirname)) {
		return false;
	}
	$handle = @opendir($dirname);
	$list=array();
	while(($file = @readdir($handle))!==false){
		if($file != '.' && $file != '..'){
			$list[]=$file;
		}
	}
	closedir($handle);
	return $list;
}

function createKeywordCache(){
	$data=DB::fetch_all("SELECT `id`,`keyword` FROM ".DB::table('fengmian_keyword'));
	@require_once libfile('function/cache');
	$cacheArray = "\$keywords=".arrayeval($data).";\n";
	writetocache('fengmian_keywords',$cacheArray);
}
?>