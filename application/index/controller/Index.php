<?php
namespace app\index\controller;

class Index extends Publiccon
{

		public function index()
		    {
//           $url= getcodeurl("http://xiaoguaishou.yunshanjs.com");
//           dump($url) ;
//          短信发送示例
//		       $datas[]=createRandomStr(4);
//               $datas[]="5分钟";
//
//                sendTemplateSMS("13393758290",$datas,"183859");

            $jssdk=new \wechat\phpsdk\jssdk();

            $signPackage= $jssdk->GetSignPackage();





            $this->assign("signPackage",$signPackage);


		    	return $this->fetch();
		    }




    public function test(){
		    return $this->fetch();
    }

    public function signout(){
        session("user",null);
        echo "<script>document.location=document.referrer</script>";
    }
		
}
