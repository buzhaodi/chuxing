<?php
namespace app\admin\controller;

class Index extends Publiccon
{

		public function index()
		    {

		    	return $this->fetch();
		    }






    public function signout(){
        session("user",null);
        echo "<script>document.location=document.referrer</script>";
    }
		
}
