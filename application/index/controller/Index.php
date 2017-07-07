<?php
namespace app\index\controller;

class Index extends Publiccon
{

		public function index()
		    {
//           $url= getcodeurl("http://xiaoguaishou.yunshanjs.com");
//           dump($url) ;
//          短信发送示例
//		       $datas[]=createRandomStr(4);
//               $datas[]="5分钟";
//
//                sendTemplateSMS("13393758290",$datas,"183859");

            $jssdk=new \wechat\phpsdk\jssdk();

            $signPackage= $jssdk->GetSignPackage();

            $ulines=db("schedule")
                ->field("s.startcounty, s.tocounty,s.startcity,s.tocity")
                ->alias("s")
                ->join("chuxing_reserve r","s.id=r.lineid")
                ->where("r.userid=".session("user")['id'])
                ->group("placeofdeparture,destination")
                ->limit(0,4)
                ->select();

        
//               echo  db("schedule")->getLastSql();


            $todaylines=db("schedule")
                ->field("startcounty, tocounty,startcity,tocity,count(tocounty) as toto")
                ->where("time>".time())
                ->group("startcity,tocity")
                ->select();





            $this->assign("todaylines",$todaylines);
            $this->assign("ulines",$ulines);
            $this->assign("signPackage",$signPackage);


		    	return $this->fetch();
		    }




    public function test(){
		    return $this->fetch();
    }

    public function signout(){
        session("user",null);
        echo "<script>document.location=document.referrer</script>";
    }
		
}
