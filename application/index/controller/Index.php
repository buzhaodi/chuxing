<?php
namespace app\index\controller;

class Index extends \think\Controller
{

		public function index()
		    {
		    	return $this->fetch();
		    }


    public function creatline()
    {
        return $this->fetch();
    }
		
}
