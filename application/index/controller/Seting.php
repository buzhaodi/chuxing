<?php
namespace app\index\controller;

use think\Session;

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

		    public function changeuser(){
		    $data=input();
		    $data=array_filter($data);
		    $res=db("user")->update($data);
		    if($res){
		        Session::clear();
                return $this->success("修改成功");
            }
            else{
                return $this->success("修改失败");
            }

            }

            public function getheadimg(){
                $file = request()->file('headimg');
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg

                  $src="/public/uploads/".$info->getSaveName();
                    $src=str_replace("\\","/",$src);
                  return json(array("status"=>'success','url'=>$src));



                }else{
                    // 上传失败获取错误信息

                    return json(array("status"=>'error','msg'=>$file->getError()));
                }
            }



        public function auth(){
            return $this->fetch();
        }
}
