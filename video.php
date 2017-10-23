<?php
define('APPTYPEID', 10);
define('CURSCRIPT', 'video');


require './source/class/class_core.php';
$discuz = C::app();
$cachelist=array();
$discuz->cachelist=$cachelist;
$discuz->init();
runhooks();

$navtitle ='迷你世界';
$metakeywords ='实况直播';
$metadescription ='迷你世界实况直播';

$mod=$_GET['mod'];
$pagenum=$_GET['page'];
if(!$pagenum){$pagenum=1;}
if(!$mod){$mod='index';}
$tuijian=DB::fetch_all("SELECT * FROM %t",array('zhibo_tuijian'));
if($mod=='index'){

//var_dump($tuijian);die;
    include template('diy:video/index');
}elseif($mod=='list'){
    include template('diy:video/list');
}elseif($mod=='zhibo'){

    $action=$_GET['action'];
    if(!$action){$action='index';}
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
    $page_start=($pagenum-1)*20;
    if($action=='index'){
        $result=array_slice($all_result,$page_start,20);

        $page=multi($all_len, 20, $pagenum, 'video.php?mod=zhibo&action='.$action);
    }elseif ($action=='hy') {
        $result=array_slice($huya_result,$page_start,20);
        $page=multi($huya_len, 20, $pagenum, 'video.php?mod=zhibo&action='.$action);
    }elseif ($action=='cs') {

        $result=array_slice($chushou_result,$page_start,20);
        $page=multi($chushou_len, 20, $pagenum, 'video.php?mod=zhibo&action='.$action);
    }

    include template('diy:video/zhibo');
}elseif ($mod=='tj') {
        showmessage('成功！','video.php');
    }elseif ($mod=='target') {
        $target=$_GET['url'];
        include template('diy:video/target');
    }








