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


        return $this->fetch();
    }






    public function usermanger(){

        echo "用户管理";
        return $this->fetch();
    }


		
}
