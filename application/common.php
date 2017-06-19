<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件



//微信 回复手机号  发送验证码

function sendTemplateSMS($to,$datas,$tempId)
{


    // 初始化REST SDK
    $rest = new \rongyun\phpsdk\REST();
    // 发送模板短信
    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    if($result == NULL ) {
//        echo "result error!";

        return false;

    }
    if($result->statusCode!=0) {
        return "error msg :" . $result->statusMsg;

    }else{
        return true;
    }
}



//创建随机数字
function createRandomStr($length){
    $str = '0123456789';//62个字符
    $strlen = 62;
    while($length > $strlen){
        $str .= $str;
        $strlen += 62;
    }
    $str = str_shuffle($str);
    return substr($str,0,$length);
}