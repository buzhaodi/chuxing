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

//获得微信的accesstoken
function getaccess_token(){
    $token=db("weixintoken")->find(1);
    if($token){
        if(time()-$token["time"]>7200||$token['token']==""){
            $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config("weixintoken")['appID'] . "&secret=" . config("weixintoken")['appsecret'];
            $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
            $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
            $access_token = $result['access_token'];

            $data["token"]=$access_token;
            $data["time"]=time();
            $data["id"]="1";

            db("weixintoken")->update($data);
        }
        else{

            $access_token=$token['token'];
        }
    }

    return $access_token;
}

//获得jsapi_ticket





//获取用户详情
function getweixinuser($openid){



    $token_access_url ="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".getaccess_token()."&openid=".$openid."&lang=zh_CN ";
    $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容

    $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量

    return $result;
}


//获得oppenid 的code //两个参数 第一个跳转处理页面  第二个跳转回当前页
function getcodeurl($url,$returnurl){
    $url= urlencode($url);
    $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".config("weixintoken")['appID']."&redirect_uri=".$url."&response_type=code&scope=snsapi_base&state=".$returnurl."#wechat_redirect";
    return $url;
}

//获得oppenid
function getoppenid($code){

    $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".config("weixintoken")['appID']."&secret=".config("weixintoken")['appsecret']."&code=".$code."&grant_type=authorization_code";
    return $url;
}

//创建短连接
function creatshorta($url){
    $url ="http://50r.cn/urls/add.json?url=".$url;


    $res = file_get_contents($url);
    //获取文件内容或获取网络请求的内容
    $result = json_decode($res, true);
    //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量




    return $result;
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


//发送post请求
function send_post($url, $post_data) {

    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}


//发送微信模板消息

function curl_post_send_information( $token,$vars,$second=120,$aHeader=array())
{
    $ch = curl_init();
    //超时时间
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    curl_setopt($ch,CURLOPT_URL,'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    if( count($aHeader) >= 1 ){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    $data = curl_exec($ch);
    if($data){
        curl_close($ch);
        return $data;
    }
    else {
        $error = curl_errno($ch);
        curl_close($ch);
        return $error;
    }
}



//发送模板消息简单版
function sendpost($data){

    $token=getaccess_token();

    $result =curl_post_send_information($token,json_encode($data));

   return $result;


}

//拆分省市县
function chai($str)
{
    $str=str_replace("-","",$str);
    $temp = explode("省", $str);
    if (!empty($temp[1])) {
        $province = $temp[0];
        $temp = explode("市", $temp[1]);

        $city = $temp[0];
        $county = $temp[1];
        return ['province' => $province, 'city' => $city, 'county' => $county];
    } else {

        $temp = explode("市", $temp[0]);

        $city = $temp[0];
        $county = $temp[1];
        return ['province' => "", 'city' => $city, 'county' => $county];
    }


}


