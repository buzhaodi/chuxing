<?php
namespace app\index\controller;

class Seting extends Publiccon
{

		public function index()
		    {
		    	return $this->fetch();
		    }

        public function auth(){
            return $this->fetch();
        }
}
