<?php
namespace app\index\controller;

class Seting extends Publiccon
{

		public function index()
		    {

		        $imchezhu=db("schedule")->where("preson",session("user")['id'])->order("time DESC")->select();
		        foreach ($imchezhu as $key=>$value){
                    $imchezhu[$key]['time']=date("Y-m-d",  $imchezhu[$key]['time']) ;
                }


                $imchengkes=db("schedule")->field("s.*,c.location,c.id as relid")->alias("s")->join("chuxing_reserve c","s.id=c.lineid")->where("userid",session("user")['id'])->order("s.time DESC")->select();

                foreach ($imchengkes as $key=>$value){
                    $imchengkes[$key]['time']=date("Y-m-d",  $imchengkes[$key]['time']) ;
                }

//                echo db("schedule")->getLastSql();

                $this->assign("imchengkes",$imchengkes);

		        $this->assign("imchezhus",$imchezhu);
		    	return $this->fetch();
		    }

        public function auth(){
            return $this->fetch();
        }
}
