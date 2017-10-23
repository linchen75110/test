<?php
header('Content-type:text/html;charset=utf-8');
define('APPTYPEID', 20);
define('CURSCRIPT', 'video');
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
$url="http://www.dz.com";
$salt=$_G['salt'];            //加密盐
$login = new Login();
$login->loginAction($url,$salt);
class Login {
    public function loginAction($url,$salt){
        $data=$this->getUserData();

        if(!$data || empty($data)){
            header("Location: $url");
        }
        $friends=$this->getUserData($salt_url='&act=get_friend_list_info&json=1');
        $friends_names=array();
        foreach ($friends['data'] as $k => $v) {
            $friends_names[]=$v['NickName'];
        }
        session_start();

        $_SESSION['user']=$data['NickName'];
        $_SESSION['ava']=$data['head_id'];
        $_SESSION['uin']=$_GET['uin'];
        $_SESSION['friends_names']=$friends_names;
        $_SESSION['salt']=md5($salt.$_SESSION['user']);

        header("Location: $url/_login.php");
    }

    private function getUserData($salt_url='&act=getProfile&json=1') {



        $api_pub = 'http://120.24.64.132:8087/miniw/posting?';

        $cur_url = $_SERVER["QUERY_STRING"];

        $api_full = $api_pub.$cur_url.$salt_url;

        $data = json_decode($this->curlGetUnSsl($api_full),true);

        return $data;
    }

    //不验证ssl
    public function curlGetUnSsl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}
