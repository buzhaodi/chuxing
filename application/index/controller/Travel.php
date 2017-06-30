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
           $lines=db("schedule")->where(" block>0 AND time>".time())->order("creattime DESC ")->select();
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


        $location=db("reserve")->order("time DESC")->limit("0","5")->select();


        $this->assign("location",$location);
        $this->assign("lines",$lines);
        return $this->fetch();
    }

//创建预定订单
    public function creatreserve(){
        $data=input();
        $model=db("reserve");
        $data['userid']=session("user")['id'];

        $validate = new Validate([
            'lineid'  => 'require|max:25',
            'userid' => 'require',
            'tel'=>'require|/^1[3-8]{1}[0-9]{9}$/',
            'seat'=>'require',
            'location'=>'require|min:10'
        ],[
            'lineid.require' => '出错了请联系管理员',
            'userid.require'     => '请登录',
            'tel.require'   => '联系电话必填',
            'tel./^1[3-8]{1}[0-9]{9}$/'   => '请输入正确的手机号码',
            'age.seat'  => '座位数必填',
            'location.require'        => '上车地点必填,请必须超过10个数子',
            'location.min'        => '上车地点必填,请必须超过10个数子!',
        ]);


        if (!$validate->check($data)) {
           echo "<script> alert('".$validate->getError()."');history.go(-1)</script>";
            die();
        }

            $data['time']=time();



        $res=$model->insertGetId($data);
        if($res){
          $ress=  db("schedule")->where('id',$data['lineid'])->setDec('block',$data['seat']);
            if($ress){
                $reres=db("user")->alias("u")->join("schedule s","u.id=s.preson")->where("s.id={$data['lineid']}")->find();

                $senddata=[
                    'touser'=>session("user")['openid'],
                    'template_id'=>config("wxyudingchezhulinetemid"),
                    'url'=>$_SERVER['HTTP_HOST']."/index/line/reserve?id={$data['lineid']}",

                    'data'=>[
                        'name' => array(
                            'value' => ' 您好！',
                            'color' => '#FF0000'
                        ),

                        'chengkename' => array(
                            'value' => session("user")['nickname'],
                            'color' => '#FF0000'
                        ),

                        'tel' => array(
                            'value' =>  $data['tel'],
                            'color' => '#FF0000'
                        ),
                        'location' => array(
                            'value' =>  $data['location'],
                            'color' => '#FF0000'
                        )
                    ],

                ];

                sendpost($senddata);




                $senddata=[
                    'touser'=>$reres['openid'],
                    'template_id'=>config("wxyudingchengkelinetemid"),
                    'url'=>$_SERVER['HTTP_HOST']."/index/line/reservepassenger?id={$res}",

                    'data'=>[
                        'name' => array(
                            'value' => session("user")['nickname'].' 您好！',
                            'color' => '#FF0000'
                        ),

                        'chezhuname' => array(
                            'value' => $reres['nickname'],
                            'color' => '#FF0000'
                        ),

                        'tel' => array(
                            'value' =>  $reres['tel'],
                            'color' => '#FF0000'
                        )
                    ],

                ];

                sendpost($senddata);

















            $this->redirect("/index/line/reservepassenger/id/{$res}");


            }



        }else{
            echo "<script> alert('预定失败!');history.go(-1)</script>";
            die;
            }



    }








}
