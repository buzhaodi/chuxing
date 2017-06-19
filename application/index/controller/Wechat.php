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


    //发送验证码方法  第一个参数 电话号 第二个参数 oppenid
    public function sendsms($tel,$oppenid){

        $datas[]=createRandomStr(4);
       $res= sendTemplateSMS($tel,$datas,"29760");
       if($res){
           return "验证码发送成功";
       }else{
           return "验证码发送失败";
       }
    }





}



