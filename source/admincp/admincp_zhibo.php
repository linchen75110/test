<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}


$tuijian=DB::fetch_all("SELECT * FROM mn_zhibo_tuijian");
//var_dump($tuijian);die;
$huya_json=file_get_contents('./test_huya.json');
$chushou_json=file_get_contents('./test_chushou.json');
$huya_result=json_decode($huya_json,true)['data'];
$chushou_result=json_decode($chushou_json,true)['data']['items'];

$huya_len=count($huya_result);
$chushou_len=count($chushou_result);
$all_len=$huya_len+$chushou_len;
$len=$huya_len>$chushou_len ? $huya_len : $chushou_len;

    $all_result=array();
    for ($i=0; $i <$len ; $i++) {
        if(isset($huya_result[$i])){
            $all_result[]=$huya_result[$i];
        }
        if(isset($chushou_result[$i])){
            $all_result[]=$chushou_result[$i];
        }
    }
    $pagenum=$_GET['page'];
    if(!$pagenum){$pagenum=1;}
    $page_start=($pagenum-1)*30;
    $pintai=$_GET['pintai'];
    $id=$_GET['id'];
    $uid=$page_start+$_GET['uid'];
    if($pintai=='')$pintai='index';
    if($pintai=='index'){
        $result=array_slice($all_result,$page_start,30);

        $page=multi($all_len, 30, $pagenum, 'admin.php?mod=zhibo&action=zhibo&operation=list&do=tuijian&pintai='.$pintai);
        if($id){
            if(!empty($all_result[$uid]['nick'])){
                $name=$all_result[$uid]['nick'];
                $title=$all_result[$uid]['livetitle'];
                $url=$all_result[$uid]['outweburl'];
                $image=$all_result[$uid]['avatarurl'];
                $pingtai='虎牙TV';
            }else{
                $name=$all_result[$uid]['anchorNickname'];
                $title=$all_result[$uid]['name'];
                $url=$all_result[$uid]['swf'];
                $image=$all_result[$uid]['cover'];
                $pingtai='触手TV';
            }
            DB::update('zhibo_tuijian', array('name'=>$name,'title'=>$title,'url'=>$url,'image'=>$image,'pingtai'=>$pingtai),array('id'=>$id));
            echo "第".$id."个直播推荐位，修改成功！<a href='admin.php?action=zhibo&operation=list&do=tuijian'>点击刷新页面</a>";
        }
    }elseif ($pintai=='hy') {
        $result=array_slice($huya_result,$page_start,30);
        $page=multi($huya_len, 30, $pagenum, 'admin.php?mod=zhibo&action=zhibo&operation=list&do=tuijian&pintai='.$pintai);
        if($id){
            $name=$all_result[$uid]['nick'];
            $title=$all_result[$uid]['livetitle'];
            $url=$all_result[$uid]['outweburl'];
            $image=$all_result[$uid]['avatarurl'];
            $pingtai='虎牙TV';
            DB::update('zhibo_tuijian', array('name'=>$name,'title'=>$title,'url'=>$url,'image'=>$image,'pingtai'=>$pingtai),array('id'=>$id));
            echo "第".$id."个直播推荐位，修改成功！<a href='admin.php?action=zhibo&operation=list&do=tuijian'>点击刷新页面</a>";
        }
    }
    elseif ($pintai=='cs') {
        $result=array_slice($chushou_result,$page_start,30);
        $page=multi($chushou_len, 30, $pagenum, 'admin.php?mod=zhibo&action=zhibo&operation=list&do=tuijian&pintai='.$pintai);
        if($id){$name=$all_result[$uid]['anchorNickname'];
                $title=$all_result[$uid]['name'];
                $url=$all_result[$uid]['swf'];
                $image=$all_result[$uid]['cover'];
                $pingtai='触手TV';
            }
            DB::update('zhibo_tuijian', array('name'=>$name,'title'=>$title,'url'=>$url,'image'=>$image,'pingtai'=>$pingtai),array('id'=>$id));
            echo "第".$id."个直播推荐位，修改成功！<a href='admin.php?action=zhibo&operation=list&do=tuijian'>点击刷新页面</a>";

    }
include template('diy:video/zhibo_tj');


