<?php

namespace app\index\controller;

use think\Validate;

class Line extends Publiccon
{

//创建行程
    public function creatline()
    {



        $data = input();



        if (!empty($data)) {
            $userid= session("user")['id'] ;
            $data = array_filter($data);
         $creattime=db("schedule")->where("preson={$userid}")->field("creattime")->order("creattime DESC")->find();
        //$creattime=156156156;
         if(time()-$creattime['creattime']<2400){
             return json(['status' => "error", "msg" => "亲 40分钟以内只能只能发布一个订单,您可以到 个人中心->我的订单中修改 "]);
         }


            if (!empty($data['startcity'])) {
                $data['startcity'] = str_replace("-", "", $data['startcity']);
                $temp = $this->chai($data['startcity']);
                //拆分出发地三级
                unset($data['startcity']);
                $data['startprovince'] = $temp['province'];
                $data['startcity'] = $temp['city'];
                $data['startcounty'] = $temp['county'];
            }
            if (!empty($data['endcity'])) {
                $data['endcity'] = str_replace("-", "", $data['endcity']);
                //拆分到达地三级
                $temp = $this->chai($data['endcity']);
                unset($data['endcity']);
                $data['toprovince'] = $temp['province'];
                $data['tocity'] = $temp['city'];
                $data['tocounty'] = $temp['county'];
            }


            //替换出发地
            $data['placeofdeparture'] = empty($data['start']) ? "" : $data['start'];
            unset($data['start']);
            //替换目的地

            $data['destination'] = empty($data['to']) ? "" : $data['to'];
            unset($data['to']);


            //修改时间
            if (!empty($data['time'])) {
                $oldtime=$data['time'];


                $temp = explode("日", $data['time']);
                $temp[1] = str_replace("-", "", $temp[1]);
                $temp[1] = str_replace("点", ":", $temp[1]);
                $temp[1] = str_replace("分", "", $temp[1]);
                $temp[1] = explode(":", $temp[1]);

                foreach ($temp[1] as $k => $v) {
                    if (strlen($v) == 1) {
                        $temp[1][$k] = "0" . $v;
                    }
                }
                $temp[1] = implode(":", $temp[1]);

                $data['time'] = $temp[0] . " " . $temp[1];
                $data['time'] = strtotime($data['time']);

                if ($data['time'] - time() < 600) {
                    return json(['status' => "error", "msg" => "亲 您晚也要提前十分钟发布 改下时间吧"]);
                }

            }
            //哪个人发布的
            $data['preson'] = session("user")['id'] ? session("user")['id'] : "";
            //创建时间
            $data['creattime'] = time();
            //设置余座
            $data['block']=$data['seat'];
     //       $data['preson'] ="ddd";
//            dump($data);

            $validate = validate('Schedule');

            if (!$validate->check($data)) {
                return json(['status' => "error", "msg" => $validate->getError()]);
            } else {
                $res = db("schedule")->insertGetId($data);
                if ($res) {

                    $senddata=[
                        'touser'=>session("user")['openid'],
                        'template_id'=>config("wxcreatlinetemid"),
                        'url'=>$_SERVER['HTTP_HOST']."/index/line/linedetail?id={$res}",

                        'data'=>[
                            'name' => array(
                                'value' => session("user")['nickname'].' 您好！',
                                'color' => '#FF0000'
                            ),

                            'start' => array(
                                'value' => $data['placeofdeparture'],
                                'color' => '#FF0000'
                            ),

                            'end' => array(
                                'value' =>  $data['destination'],
                                'color' => '#FF0000'
                            ),

                            'tel' => array(
                                'value' =>session("user")['tel'],
                                'color' => '#FF0000'
                            ),

                            'time' => array(
                                'value' => $oldtime,
                                'color' => '#FF0000'
                            ),




                        ],

                    ];

                           sendpost($senddata);

                    return json(['status' => "success", "msg" => "发布成了别","id"=>$res]);
                }
            }


            exit();
        }






        $lines=db("schedule")->where("preson",session("user")['id'])->order("creattime DESC")->limit("0","5")->select();



        $this->assign("lines",$lines);

        return $this->fetch();
    }


    //订单详情页面
    public function linedetail(){
        $data=input();
        $id=$data['id'];
        $res=db("schedule")->find($id);
        $res['time']= date('Y-m-d日 G点:i分',$res['time']);


        $this->assign("data",$res);
        $this->assign("id",$id);

        return $this->fetch();
    }

    //预定详情页面
    public function reserve($id){
        $res=db("schedule")
            ->field("s.time,s.seat,s.block,r.tel,r.location,u.nickname,u.headimgurl,r.seat")
            ->alias('s')
            ->join("chuxing_reserve r","s.id=r.lineid")
            ->join("chuxing_user u","u.id=r.userid")
            ->where("s.id={$id}")
            ->select();




        foreach ( $res as $k=>$v){
            $res[$k]['time']= date('Y-m-d日 G点:i分',$res[$k]['time']);
        }
        $msg=db("schedule")->find($id);



        $this->assign("msg",$msg);
        $this->assign("datas",$res);
        return $this->fetch();
    }

//预定详情乘客页
    public function reservepassenger($id){
//       echo $id;
        $data=db("user")->alias('a')
            ->field('a.nickname,a.tel,a.sex,r.seat,a.headimgurl')
            ->join('chuxing_schedule s','a.id = s.preson')
            ->join('chuxing_reserve r','s.id = r.lineid')
            ->where("r.id={$id}")
            ->find();

//       echo db("user")->getLastSql();


        $this->assign("data",$data);
        return $this->fetch();
    }


    //生成短连接
    public function getduan(){
        $url=input()['url'];
       return creatshorta($url);
    }








//拆分字符串 省市区

    private function chai($str)
    {
        $str=str_replace("-","",$str);
        $temp = explode("省", $str);
        if (!empty($temp[1])) {
            $province = $temp[0];
            $temp = explode("市", $temp[1]);

            $city = $temp[0];
            $county = $temp[1];
            return ['province' => $province, 'city' => $city, 'county' => $county];
        } else {

            $temp = explode("市", $temp[0]);

            $city = $temp[0];
            $county = $temp[1];
            return ['province' => "", 'city' => $city, 'county' => $county];
        }


    }

//获得路线
    public function getline()
    {

        $data = input();
        if (!empty($data)) {
            switch ($data['type']) {
                //传的值是id
                case 1:
                   $line= db("schedule")->where("preson",session("user")['id'])->find($data['datas']);
                   return json($line);

                    break;
                //传的值是地址
                case 2:

                    $start=$this->chai($data['datas'][0]);
                    $end=$this->chai($data['datas'][1]);

                   $line= db("schedule")->where("preson",session("user")['id'])->where("startcity",$start["city"])->where("tocity",$end["city"])->order("(startcounty='{$start['county']}') DESC ,(tocounty='{$end['county']}') DESC,creattime DESC ")->find();

//                    $line['time']= date('Y-m-d日-G点-i分',$line['time']);

                    return json($line);
                    break;
                //获取最新一次
                default:
                    $line= db("schedule")->where("preson",session("user")['id'])->order("creattime","DESC")->find();
                    return json($line);
                    break;
            }
        }

    }

}
