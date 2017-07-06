<?php
namespace app\admin\controller;

class Setting extends Publiccon
{

		public function index()
		    {

		    	return $this->fetch();
		    }

        public function auth(){
            $user=db("user")->where("status=2")->select();

            $this->assign("users",$user);
            return $this->fetch();
        }


    public function usermanger(){

        echo "用户管理";
        return $this->fetch();
    }


		
}
