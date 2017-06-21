<?php

namespace app\index\controller;

use think\Validate;

class Line extends Publiccon
{


    public function creatline()
    {



        $data = input();


        if (!empty($data)) {
            $data = array_filter($data);

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
            $data['preson'] = session("user")['id'] ? session("user")['id'] : "";

            $data['creattime'] = time();

//            $data['preson'] ="ddd";
//            dump($data);


            $validate = validate('Schedule');

            if (!$validate->check($data)) {
                return json(['status' => "error", "msg" => $validate->getError()]);
            } else {
                $res = db("schedule")->insert($data);
                if ($res) {
                    return json(['status' => "success", "msg" => "发布成了别"]);
                }
            }


            exit();
        }

        return $this->fetch();
    }


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
