<?php

namespace app\index\controller;

use think\Validate;

class Travel extends Publiccon
{
    public function index(){

        $data=input();
        if(array_key_exists("start",$data)){
            empty($data['start'])? $start=$data['start']:$start=chai($data['start']);
        }else{
            $start="";
        }

        if(array_key_exists("end",$data)){
            empty($data['end'])? $end=$data['end']:$end=chai($data['end']);
        }else{
            $end="";
        }



       if( is_array($start)&&is_array($end) ){
//            echo 1;
           $lines=db("schedule")->where("startcity='{$start["city"]}' AND tocity='{$end['city']}' AND block>0 AND time>".time())->order("(startcounty='{$start['county']}') DESC ,(tocounty='{$end['county']}') DESC,creattime DESC ")->select();

       }
       else if(is_array($start)&&!is_array($end)){
//           echo 2;
           $lines=db("schedule")->where("startcity='{$start["city"]}' AND block>0 AND time>".time())->order("(startcounty='{$start['county']}') DESC,creattime DESC ")->select();
       }
       else if(!is_array($start)&&!is_array($end)){
//           echo 3;
           $lines=db("schedule")->where("time>".time())->order("creattime DESC ")->select();
       }
       else if(!is_array($start)&&is_array($end)){
//           echo 4;
           $lines=db("schedule")->where("startcity='{$end["city"]}' AND block>0 AND time>".time())->order("(startcounty='{$end['county']}')  DESC,creattime DESC ")->select();
       }

//        echo db()->getLastSql();
//时间戳变今天明天后天
        foreach ($lines as $k=>$v){
            $tempday=date("Y-m-d",$lines[$k]['time']);
            $temphoues=date("H:i",$lines[$k]['time']);

            $tempday=strtotime($tempday);
            $tody=$tempday-strtotime("now");
            switch ($tody){
                case $tody<0;
                $tody="今天 "."$temphoues";

                break;
                case $tody>0&&$tody<60*60*24;
                    $tody="明天 "."$temphoues";
                    break;
                case $tody>60*60*24&&$tody<60*60*24*2;

                    $tody="后天 "."$temphoues";
                    break;
            }
            $lines[$k]['distance']=round( $lines[$k]['distance']/1000,1);
           $lines[$k]['time']=$tody;
        }



        $this->assign("start",$start);
        $this->assign("end",$end);
        if(array_key_exists("end",$data)){
            $this->assign("endcity",$data['end']);
        }
        else{
            $this->assign("endcity","");
        }
        if(array_key_exists("start",$data)){
            $this->assign("startcity",$data['start']);
        }else{
            $this->assign("startcity","");
        }




        $this->assign("lines",$lines);
        return $this->fetch();
    }


    public function creatreserve(){
        $data=input();
        $model=db("reserve");
        $data['userid']=session("user")['id'];



        $res=$model->insert($data);
        if($res){
          $res=  db("schedule")->where('id',$data['lineid'])->setDec('block',$data['seat']);
            if($res){






            }



        }else{
            echo "预定失败";
            die;
            }



    }








}
