<?php
/**
 * Created by PhpStorm.
 * User: buzha
 * Date: 2017-06-19
 * Time: 9:42
 */

namespace app\index\controller;
use think\Session;

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

    //注册页面
    public function reg(){

        return $this->fetch();
    }


    //订阅事件
    public function subscribe($openid){

        return "欢迎关注".config("appname")." 如果您还没有绑定手机 请在下方发送信息处输入您的手机号以便继续使用";

    }



    //发送验证码方法  第一个参数 电话号 第二个参数 oppenid
    public function sendsms($tel,$oppenid){

           $ttt= db("user")->where("tel",$tel)->find();

           if($ttt){
               return "该手机号已经注册,请换个手机号再注册吧";
           }

            $test=db("validate")->where("oppenid='{$oppenid}'")->find();

            if(!$test||time()-$test["time"]>300){
                        $datas[]=createRandomStr(4);
                        $datas[]="5分钟";
                        $res= sendTemplateSMS($tel,$datas,"183859");


                        $data['tel']=$tel;
                        $data['oppenid']=$oppenid;
                        $data['validate']=$datas[0];
                        $data['time']=time();

                        if($test['id'])
                        {
                            $data['id']=$test['id'];
                            db("validate")->update($data);
                        }else{
                            db("validate")->insert($data);
                        }


                       if($res){
                         if(strpos($res,"error") !== false){
                                return $res;
                         }
                         else{
                             return "验证码发送成功,请在收到验证码后发送给我们";
                         }



                       }else{
                           return "验证码发送失败";
                       }
            }
            else{
                return "您获取验证码过于频繁 请5分钟后重试";
            }





    }

    public function checkvalidate($validate,$oppenid){
        //获得用户信息
       $res= db("validate")->where("oppenid='{$oppenid}'")->find();

//       if($res['validate']==$validate&&time()-$res['time']<240){
     if($res['validate']==$validate){
           $data=getweixinuser($oppenid);

           $data["sex"] ==1 ? $data["sex"]="男":$data["sex"]="女";
            $data["tel"]=$res['tel'];
         //去除一些没用的
         unset($data['subscribe']);
         unset($data['language']);
         unset($data['country']);
         unset($data['remark']);
         unset($data['groupid']);
         unset($data['tagid_list']);

        $vali=  db("user")->where("openid='{$oppenid}'")->find();

        if($vali){
            return "您已经通过了验证 无需再次验证了!";

        }
        else{
            $finlly=  db("user")->insert($data);
            if($finlly){
                return "验证成功,赶紧去拼车吧!";
            }
            else{
                return "可能有点小差错 请输入在线客服解决";
            }
        }



       }

       else {
           return "验证失败,您的验证码有误或超出5分钟未验证";
       }


        $s="https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect ";
    }




    public function redirect_uri(){
        $code=input()['code'];
        $returnurl=input()['state'];

       $url= getoppenid($code);
        $res = file_get_contents($url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量

       $openid=$result['openid'];

      $data= db("user")->where("openid='{$openid}'")->find();

      if(empty($data)){
          $this->redirect(url("/index/emptytype/cantfoundid"));
      }

        session('user', $data);

       $this->redirect($returnurl);




    }


    public function getoppenid(){
        $url="http://".$_SERVER["HTTP_HOST"]."/index/wechat/";
    }
}



