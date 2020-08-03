<?php
class DMain {
    private $response = [];
    private $method = [];
    public static $errorResponse = [];

    public function run(){
        try {
            $setResponse = function($response){
                $this->response = $response;
                return true;
            };

            $requestMethod  = $_SERVER['REQUEST_METHOD'];
            $function = $this->method[$requestMethod] ?: function(){ self::$errorResponse = ['code'=> 400, 'data'=>'Invalid request method'];};
            $function($setResponse);

            return $this->returnResponse();
        } catch (DBException $e) {
            $this->errorResponse = [
                'code'=> 500,
                'msg'=> 'DBException: '. $e->getMessage()
            ];

            return $this->returnResponse();
        } catch (PDOException $e) {
            $this->errorResponse = [
                'code'=> 500,
                'msg'=> 'DBException: '. $e->getMessage()
            ];
            
            return $this->returnResponse();
        }
    }

    public function addMethod($requestMethod, $function){
        $this->method[$requestMethod] = $function;
    }


    private function returnResponse(){
        if(!empty(self::$errorResponse)){
            return json_encode(self::$errorResponse, true);
        }

        return json_encode($this->response, true);
    }
}

class Danmaku
{
    public function 检查频率(){
        global $_config;

        $lock = true;
        $ip = getIp();
        $count = DB::查询_发送弹幕次数($ip);

        if (empty($count)){
            DB::插入_发送弹幕次数($ip);
            $lock = false;

        } else {
            $count = $count[0];

            if ($count['time'] + $_config['限制时间'] > time()){
                if($count['c'] < $_config['限制次数']){
                    $lock = false ; 
                    DB::更新_发送弹幕次数($ip);
                };
            }
        
            if ($count['time'] + $_config['限制时间'] < time()){
                DB::更新_发送弹幕次数($ip,time());
                $lock = false;
            }
        }

        return $lock;
    }

    public function 添加弹幕($data = [])
    {
        $inspection = function ($data){
            if (empty($data)) return false;
            if (empty($data['id'])) return false;
            //if (empty($data['author'])) return false;; 暂时无用
            if (!is_float($data['time']) and !is_int($data['time'])) return false;;
            if (empty($data['text'])) return false;;
            if (!is_int($data['color'])) return false;;
            return !(($data['type'] < 0) or ($data['type'] > 3));
        };

        if($inspection($data) === false){
            return DMain::$errorResponse = [
                'code'=> 400, 
                'msg' => 'Param error'
            ];
        } else {
            DB::插入_弹幕($data);
        }
    }

    public function 弹幕池($id)
    {
        $data = DB::查询_弹幕池($id);
        //print_r($data);
        if (empty($data)) return null;

        $arr = [];
        foreach ($data as $k => $v) {
            $arr[$k][] = (float)$v['videotime'];  //弹幕出现时间(s)
            $arr[$k][] = (int)$v['type'];   //弹幕样式  
            $arr[$k][] = (int)$v['color']; //字体的颜色
            $arr[$k][] = (string)$v['cid'];  //现在是弹幕id，以后可能是发送者id了
            $arr[$k][] = (string)$v['text'];  //弹幕文本
        }
        
        return $arr;
    }
    
    function __destruct() {
        //TODO: 长连接
        global $_config ;
        $type = $_config['数据库']['方式'];
        if($type === 'pdo') DB::$sql = null ;
        if($type === 'sqlite3' or $type === 'mysqli' ) DB::$sql->close() ;
    }
}
