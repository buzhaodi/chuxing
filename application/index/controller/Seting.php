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

    public function getcardimg(){
        $file = request()->file('headimg');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/card');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg

            $src="/public/uploads/card/".$info->getSaveName();
            $src=str_replace("\\","/",$src);
            return json(array("status"=>'success','url'=>$src));



        }else{
            // 上传失败获取错误信息

            return json(array("status"=>'error','msg'=>$file->getError()));
        }
    }

        public function auth(){
                $data=input();
                if($data){
                    $data=array_filter($data);

                    if(count($data)!=4){
                        return $this->error("所有选项必填.包括身份证正/反面照片,姓名和身份证号");
                    }else{
                        $data['status']=2;
                      $res=  db("user")->where("id",session("user")['id'])->update($data);
                        if($res){
                            session::clear();
                            return $this->success("已经上传成功,等待管理员审核",url("/index/seting/index"));
                        }

                    }

                    exit();
                }




            return $this->fetch();
        }

        public function inreview(){
            return $this->fetch();
        }



    public function complain($id,$oid){


        $this->assign("id",$id);
        $this->assign("oid",$oid);
        return $this->fetch();
    }


    public function delcomplain(){
        $data=input();
        $data['time']=time();

        $res= db("complain")->insert($data);
        if($res){
            return $this->success("提交投诉成功,等待管理员审核后会给您反馈");
        }else{
            return $this->error("提交投诉失败,请联系管理员");
        }


    }


}


class WS {
    var $master;  // 连接 server 的 client
    var $sockets = array(); // 不同状态的 socket 管理
    var $handshake = false; // 判断是否握手

    function __construct($address, $port){
        // 建立一个 socket 套接字
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
        or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1)
        or die("socket_option() failed");
        socket_bind($this->master, $address, $port)
        or die("socket_bind() failed");
        socket_listen($this->master, 2)
        or die("socket_listen() failed");

        $this->sockets[] = $this->master;

        // debug
        echo("Master socket  : ".$this->master."\n");

        while(true) {
            //自动选择来消息的 socket 如果是握手 自动选择主机
            $write = NULL;
            $except = NULL;
            socket_select($this->sockets, $write, $except, NULL);

            foreach ($this->sockets as $socket) {
                //连接主机的 client
                if ($socket == $this->master){
                    $client = socket_accept($this->master);
                    if ($client < 0) {
                        // debug
                        echo "socket_accept() failed";
                        continue;
                    } else {
                        //connect($client);
                        array_push($this->sockets, $client);
                        echo "connect client\n";
                    }
                } else {
                    $bytes = @socket_recv($socket,$buffer,2048,0);
                    print_r($buffer);
                    if($bytes == 0) return;
                    if (!$this->handshake) {
                        // 如果没有握手，先握手回应
                        $this->doHandShake($socket, $buffer);
                        echo "shakeHands\n";
                    } else {

                        // 如果已经握手，直接接受数据，并处理
                        $buffer = $this->decode($buffer);
                        //process($socket, $buffer);
                        echo "send file\n";
                    }
                }
            }
        }
    }

    function dohandshake($socket, $req)
    {
        // 获取加密key
        $acceptKey = $this->encry($req);
        $upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $acceptKey . "\r\n" .
            "\r\n";

        echo "dohandshake ".$upgrade.chr(0);
        // 写入socket
        socket_write($socket,$upgrade.chr(0), strlen($upgrade.chr(0)));
        // 标记握手已经成功，下次接受数据采用数据帧格式
        $this->handshake = true;
    }


    function encry($req)
    {
        $key = $this->getKey($req);
        $mask = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";

        return base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
    }

    function getKey($req)
    {
        $key = null;
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) {
            $key = $match[1];
        }
        return $key;
    }

    // 解析数据帧
    function decode($buffer)
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;

        if ($len === 126)  {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127)  {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else  {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    // 返回帧信息处理
    function frame($s)
    {
        $a = str_split($s, 125);
        if (count($a) == 1) {
            return "\x81" . chr(strlen($a[0])) . $a[0];
        }
        $ns = "";
        foreach ($a as $o) {
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
    }

    // 返回数据
    function send($client, $msg)
    {
        $msg = $this->frame($msg);
        socket_write($client, $msg, strlen($msg));
    }
}


