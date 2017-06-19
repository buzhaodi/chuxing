<?php
/**
 * Created by PhpStorm.
 * User: buzha
 * Date: 2017-06-19
 * Time: 9:42
 */

namespace app\index\controller;

class Wechat extends \think\Controller
{

    public function index()
    {



        $wechatObj = new \wechat\phpsdk\wechatCallbackapi();
        if (!isset($_GET['echostr'])) {
            $wechatObj->responseMsg();
        }else{
            $wechatObj->valid();
        }

    }





}



