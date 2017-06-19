<?php
namespace app\index\controller;

class Index extends \think\Controller
{

		public function index()
		    {
               

		        $datas=["45611"];

                sendTemplateSMS("13393758290",$datas,"29760");



		    	return $this->fetch();
		    }




    public function test(){
		    return $this->fetch();
    }
		
}
