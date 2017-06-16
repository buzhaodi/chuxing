<?php
namespace app\index\controller;

class Seting extends \think\Controller
{

		public function index()
		    {
		    	return $this->fetch();
		    }

        public function auth(){
            return $this->fetch();
        }
}
