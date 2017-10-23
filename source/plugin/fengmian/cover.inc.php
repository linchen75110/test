<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
loadcache('plugin');
$open_upload=intval($_G['cache']['plugin']['fengmian']['open_upload']);
$groups=unserialize($_G['cache']['plugin']['fengmian']['groups']);
if(!$open_upload||!in_array($_G['groupid'],$groups)) showmessage(lang('plugin/fengmian','noright'));
$tid=intval($_GET['tid']);
if($tid){
	if(submitcheck('cover_upload_submit')){//上传图片
		$tmp=$_FILES['newcover']['tmp_name'];
		$pinfo=pathinfo($_FILES['newcover']['name']);
		$ftype=strtolower($pinfo['extension']);
		if(!in_array($ftype,array('jpg','jpeg','gif','png','bmp'))) showmessage(lang('plugin/fengmian','errortype'));
		$ftype='jpg';//cover 强制命名成jpg格式
		$coverdir = DISCUZ_ROOT.'./data/attachment/forum/threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/';
		@mkdir($coverdir,0777,true);
		$coverdir .= $tid.'.'.$ftype;
		$r=move_uploaded_file($tmp,$coverdir);
		if($r&&file_exists($coverdir)){
			DB::update('forum_thread',array('cover'=>1),array('tid'=>$tid));
			//可用 coverpath
			DB::delete('forum_threadimage',array('tid'=>$tid));
			DB::insert('forum_threadimage',array('tid'=>$tid,'attachment'=>'threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/'.$tid.'.'.$ftype));
			$thumbpath=DB::result_first("select thumbpath from ".DB::table('common_block_item')." where id='$tid' and makethumb=1 order by itemid desc");
			if($thumbpath){
				@unlink(DISCUZ_ROOT.'./data/attachment/'.$thumbpath);
				DB::update('common_block_item',array('makethumb'=>0,'thumbpath'=>'','pic'=>'forum/threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/'.$tid.'.'.$ftype),array('id'=>$tid));
			}
			showmessage(lang('plugin/fengmian','upload_ok'),'forum.php?mod=viewthread&tid='.$tid,array(),array('locationtime'=>true,'refreshtime'=>3, 'showdialog'=>1, 'showmsg' => true));
		}else{
			showmessage(lang('plugin/fengmian','upload_error'));
		}
	}elseif(submitcheck('cover_get_submit')){//获取网络图片
		$url=trim($_POST['newcover_url']);
		/*if (!$url||!preg_match("/^(http:|https:)\/\/(.+\.(jpg|jpeg|gif|bmp|png))$/",strtolower($url))){
			showmessage(lang('plugin/fengmian','errorurl'));//链接有误
		}*/
		$ftype='jpg';//cover 强制命名成jpg格式
		ob_start();
		readfile($url);
		$content = ob_get_contents();
		ob_end_clean();
		/*var_dump($img);
		$content=@file_get_contents($url);
		var_dump($content);die;*/

		if(!$content){
			showmessage(lang('plugin/fengmian','get_error'));
		}else{
			$coverdir = DISCUZ_ROOT.'./data/attachment/forum/threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/';
			@mkdir($coverdir,0777,true);
			$coverdir .= $tid.'.'.$ftype;
			$r=file_put_contents($coverdir,$content);
			if($r&&file_exists($coverdir)){
				DB::update('forum_thread',array('cover'=>1),array('tid'=>$tid));
				DB::delete('forum_threadimage',array('tid'=>$tid));
				DB::insert('forum_threadimage',array('tid'=>$tid,'attachment'=>'threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/'.$tid.'.'.$ftype));
				$thumbpath=DB::result_first("select thumbpath from ".DB::table('common_block_item')." where id='$tid' and makethumb=1 order by itemid desc");
				if($thumbpath){
					@unlink(DISCUZ_ROOT.'./data/attachment/'.$thumbpath);
					DB::update('common_block_item',array('makethumb'=>0,'thumbpath'=>'','pic'=>'forum/threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/'.$tid.'.'.$ftype),array('id'=>$tid));
				}
				showmessage(lang('plugin/fengmian','upload_ok'),'forum.php?mod=viewthread&tid='.$tid,array(),array('locationtime'=>true,'refreshtime'=>3, 'showdialog'=>1, 'showmsg' => true));
			}else{
				showmessage(lang('plugin/fengmian','upload_error'));
			}
		}
	}else{//弹出框内容加载
		$img_diy=$img_cover='';
		$log=DB::fetch_first("select * from ".DB::table('forum_threadimage')." where tid='$tid' and remote=0");
		if($log){
			if(file_exists(DISCUZ_ROOT.'./data/attachment/forum/'.$log['attachment'])){
				$img_diy=$_G['siteurl'].'data/attachment/forum/'.$log['attachment'];
			}
		}
		$coverdir = 'data/attachment/forum/threadcover/'.substr(md5($tid),0,2).'/'.substr(md5($tid),2,2).'/'.$tid.'.jpg';
		if(file_exists(DISCUZ_ROOT.'./'.$coverdir)){
			$img_cover=$_G['siteurl'].$coverdir;
		}
	}
}else{
	showmessage(lang('plugin/fengmian','notid'));
}

include template('fengmian:cover');
?>