<?php
namespace app\index\controller;
use think\Validate;
class Line extends Publiccon
{



    public function creatline()
    {
        $data=input();
        if(!empty($data)){


          $temp=  $this->chai($data['startcity']);
          //拆分出发地三级
            unset($data['startcity']);
            $data['startprovince']=$temp['province'];
            $data['startcity']=$temp['city'];
            $data['startcounty']=$temp['county'];
            //拆分到达地三级
            $temp=  $this->chai($data['endcity']);
            unset($data['endcity']);
            $data['toprovince']=$temp['province'];
            $data['tocity']=$temp['city'];
            $data['tocounty']=$temp['county'];
            //替换出发地
            $data['placeofdeparture']=$data['start'];
            unset($data['start']);
            //替换目的地
            $data['destination']=$data['to'];
            unset($data['to']);

            //修改时间
            $temp=explode("日", $data['time']);
            $temp[1]=str_replace("-","",$temp[1]);
            $temp[1]=str_replace("点",":",$temp[1]);
            $temp[1]=str_replace("分","",$temp[1]);
            $temp[1]=explode(":",$temp[1]);

            foreach ($temp[1] as $k=>$v){
                if(strlen($v)==1){
                    $temp[1][$k]="0".$v;
                }
            }
            $temp[1]=implode(":",$temp[1]);




            $data['time']=$temp[0]." ".$temp[1];
            $data['time']=strtotime($data['time']);

            $data['preson']="ddd";


            dump($data);


          














//
//            $data = [
//                'name'=>'thinkphp1112的萨芬撒地方的萨hinkphp1112的萨芬撒地方的萨hinkphp1112的萨芬撒地方的萨hinkphp1112的萨芬撒地方的萨芬',
//                'email'=>'thinkphp@qq.com'
//            ];
//
//            $validate = validate('User');
//
//            if(!$validate->check($data)){
//                dump($validate->getError());
//            }




            exit();
        }

        return $this->fetch();
    }



   private function chai($str){
        $temp=explode("省",$str);
        if(!empty($temp[1])){
            $province=$temp[0];
            $temp=explode("市",$temp[1]);

            $city=$temp[0];
            $county=$temp[1];
            return ['province'=>$province,'city'=>$city,'county'=>$county];
        }else{

            $temp=explode("市",$temp[0]);

            $city=$temp[0];
            $county=$temp[1];
            return ['province'=>"",'city'=>$city,'county'=>$county];
        }


    }
		
}
