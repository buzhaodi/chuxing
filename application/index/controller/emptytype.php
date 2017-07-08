<?php
namespace app\index\controller;

class Emptytype extends \think\Controller
{

		public function cantfoundid()
		    {
		    	return $this->fetch();
		    }

		    public function feng(){

		            $time=session("user")['ftime'];
		            $time= date("Y-m-d h:i:s", $time);

                $this->assign("time",$time);
                return $this->fetch();
            }



    public function signout(){
        session("user",null);
        echo "<script>document.location=document.referrer</script>";
    }






}





