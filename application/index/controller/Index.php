<?php
namespace app\index\controller;

class Index extends \think\Controller
{

		public function index()
		    {

//          短信发送示例
//		       $datas[]=createRandomStr(4);
//               $datas[]="5分钟";
//
//                sendTemplateSMS("13393758290",$datas,"183859");




		    	return $this->fetch();
		    }




    public function test(){
		    return $this->fetch();
    }
		
}
