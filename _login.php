<?php

define('NOROBOT', FALSE);
define('ADMINSCRIPT', basename(__FILE__));
define('CURSCRIPT', 'admin');
define('HOOKTYPE', 'hookscript');
define('APPTYPEID', 0);

require './source/class/class_core.php';
session_start();
$newusername=$_SESSION['user'];
$salt=$_SESSION['salt'];
$uin=$_SESSION['uin'];
unset($_SESSION['user']);
unset($_SESSION['salt']);
unset($_SESSION['uin']);

if (md5($_G['salt'].$newusername) !=$salt) {
   exit('非法操作！');
}
$discuz = C::app();
$discuz->init();

require libfile('function/member');
require libfile('class/member');
runhooks();

$newpassword = $_G['salt'];//trim($_GET['newpassword']);
$newemail = $uin.'@mini.com';

if(!$newusername || !$newemail) {
    showmessage('您目前未登录迷你世界，暂时以游客身份只读访问论坛');
}
//var_dump($_SESSION['ava']);die;
// 以下几句防止第3方伪造
$time= (int)(time());
$curdate= time();
$seckey=$time.$newusername.$newpassword;
$seckey=  md5($seckey);

$_G['uid']='';
$userid=C::t('common_member')->fetch_uid_by_username($newusername);

    $_SERVER['REQUEST_METHOD'] = 'POST';//注册需要模拟POST防止2次校验不通过
    $_GET['formhash'] = formhash();// 防止 2次校验不通过
    $_G['group']['seccode']='';// 防止 2次校验不通过

if(!$userid){// 没有找到对应用户则调用注册

    $_GET['regsubmit']='yes';
    $_GET['infloat']='yes';
    $_GET['lssubmit']='yes';
    $ctl_obj = new register_ctl();//785
    $ctl_obj->setting = $_G['setting'];
    $ctl_obj->template = 'member/register';

    $_GET[''.$ctl_obj->setting['reginput']['username']]=$newusername;
    $_GET[''.$ctl_obj->setting['reginput']['password']]= $newpassword;
    $_GET[''.$ctl_obj->setting['reginput']['password2']]= $newpassword;
    $_GET[''.$ctl_obj->setting['reginput']['email']] =$newemail;
    $ctl_obj->on_register();
}
//uc_user_synlogout();
$_G['groupid'] = $_G['member']['groupid'] = 7;
$_G['uid'] = $_G['member']['uid'] = 0;
$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';

// 登陆
$_GET['loginsubmit']='yes';
$_GET['lssubmit']='';
$_GET['username']=$newusername;
$_GET['password']= $newpassword;
$ctl_obj = new logging_ctl();
$ctl_obj->setting = $_G['setting'];
$ctl_obj->template = 'member/login';
$ctl_obj->on_login();

