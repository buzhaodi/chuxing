<?php
namespace app\index\controller;

class Index extends \think\Controller
{

		public function index()
		    {
		        echo 111;
		    	return $this->fetch();
		    }

		
}
