<?php
function curlGetUnSsl($url)
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
$huya_url='http://api.huya.com/way/waylives?wayid=187&sign=c6f0081432992db5e7e28d607d575916';
$chushou_url='https://open.chushou.tv/open/online-room/list.htm?source=1041&gid=1269&pageSize=500';

$file_huya = "./test_huya.json";
$file_chushou = "./test_chushou.json";
$huya_json=curlGetUnSsl($huya_url);
$chushou_json=curlGetUnSsl($chushou_url);

file_put_contents($file_huya,$huya_json);
file_put_contents($file_chushou,$chushou_json);