<?php
namespace app\admin\controller;

class Setting extends Publiccon
{

		public function index()
		    {

		    	return $this->fetch();
		    }

        public function auth(){
		    $data=input();
		    if($data){
		        $id=$data['id'];
                $res=db("user")->update(array("id"=>$id,"status"=>3));

                if($res){
                    return $this->success("操作成功");
                }
                else{
                    return $this->error("操作失败");
                }


		        exit();
            }



            $user=db("user")->where("status=2")->select();

            $this->assign("users",$user);
            return $this->fetch();
        }


    public function authrefuse(){
      $data=input();

     $res= db("user")->update(array("id"=>$data['id'],"name"=>"","cardid"=>"","zhengcardurl"=>"","fancardurl"=>"","status"=>"1"));

    if($res){
        return $this->success("操作成功");
    }else{
        return $this->error("操作失败");
    }
    }





    public function complain(){

        $com=db("complain")->field("c.id,u.name as complainername,u.zhengcardurl as complainercard,u.tel as complainertel,u.id as complainerid,u1.name as complainedname,u1.tel as complainedtel,u1.zhengcardurl as complainedcard,u1.id as complainedid,c.content,c.time,c.orderid")
            ->alias('c')
            ->join("chuxing_user u","c.complainid=u.id")
            ->join("chuxing_user u1","c.complainided=u1.id")
            ->where("chuxing_complain.status",1)
            ->select();

        $this->assign("coms",$com);
        return $this->fetch();
    }






    public function usermanger(){

        echo "用户管理";
        return $this->fetch();
    }


		
}
