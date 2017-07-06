<?php
namespace app\admin\controller;
use think\Session;

class Publiccon extends \think\Controller
{

	public function _initialize(){
	   if(!session::get('user')){
           $url="http://".$_SERVER["HTTP_HOST"]."/index/wechat/redirect_uri";
           $returnurl="http://".$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
           $url= getcodeurl($url,$returnurl);


	       $this->redirect($url);
       }else{
	       if(session("user")['isadmin']!=2){
                return $this->error("管理员才能进哟");
           }
       }


    }
		
}
