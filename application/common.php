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
function sendtelsms(){



    return 111;
}




//发送验证码

function sendTemplateSMS($to,$datas,$tempId)
{
    // 初始化REST SDK

    $rest = new \rongyun\phpsdk\REST();


    // 发送模板短信
    echo "Sending TemplateSMS to $to <br/>";
    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    if($result == NULL ) {
        echo "result error!";
        exit();
    }
    if($result->statusCode!=0) {
        echo "error code :" . $result->statusCode . "<br>";
        echo "error msg :" . $result->statusMsg . "<br>";
        //TODO 添加错误处理逻辑
    }else{
        echo "Sendind TemplateSMS success!<br/>";
        // 获取返回信息
        $smsmessage = $result->TemplateSMS;
        echo "dateCreated:".$smsmessage->dateCreated."<br/>";
        echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
        //TODO 添加成功处理逻辑
    }
}