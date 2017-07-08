<?php
namespace app\admin\controller;

class Complain extends Publiccon
{
            //拒绝投诉
		public function nodetail($id){
             $res=   db("complain")->where("id",$id)->setField("status",2);
            if($res){
                return $this->success("操作成功");
            } else{
                return $this->error("操作失败");
            }

        }
        //处理投诉
        public function docomplain(){
            $data=input();
            $id=$data['id'];
          $res=  db("complain")->find($id);

          $ftime=time()+60*60*24*$data['fengtime'];
           $res= db('user')->where("id",$res['complainided'])->setField("ftime",$ftime);

           if($res){
               db("complain")->where("id",$id)->setField("res","封号{$data['fengtime']}天");
               db("complain")->where("id",$id)->setField("status",2);
               return $this->success("操作成功");
           }
           else{
               return $this->error("操作失败");
           }



            dump($data);




        }
		
}
