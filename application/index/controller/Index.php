<?php
namespace app\index\controller;

class Index extends \think\Controller
{

		public function index()
		    {

//          短信发送示例
//		        $datas=["45611"];
//
//                sendTemplateSMS("13393758290",$datas,"29760");



		    	return $this->fetch();
		    }




    public function test(){
		    return $this->fetch();
    }
		
}
