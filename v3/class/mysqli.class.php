<?php
class DB {
    public static $sql ;
    
    function __construct() {
        global $_config ;
        self::数据库连接($_config['数据库']['地址'],$_config['数据库']['用户名'],$_config['数据库']['密码'],$_config['数据库']['名称']);
        
    }

    private static function 数据库连接($hostname,$username,$password,$db) {
        $sql = new mysqli($hostname, $username, $password, $db);;
        if ($sql->connect_error) {
            throw new DBException('DBException:'.$sql->connect_errno."\n".$sql->connect_error);
        } 
        self::$sql = $sql;
    }
    
    public static function 插入_弹幕($data){
        $stmt = self::$sql->prepare("INSERT IGNORE INTO danmaku_list (id, type, text, color, videotime, time) VALUES (?, ?, ?, ?, ?, ?)");
        @$stmt->bind_param('iisidi', $data['id'], $data['type'], $data['text'], $data['color'], $data['time'],time());
        if ($stmt->execute() == false){
            throw new DBException($stmt->error_list);
        }
        $stmt->close();
    }
    
    public static function 插入_发送弹幕次数($ip){
       
        $stmt = self::$sql->prepare("INSERT IGNORE INTO danmaku_ip (ip, time) VALUES (?, ?)");
        @$stmt->bind_param('si', $ip, time());
        if ($stmt->execute() == false){
            throw new DBException($stmt->error_list);
        }
        $stmt->close();
    }
    
    public static function 查询_弹幕池($id){
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_list WHERE id=?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute() == false){
            throw new DBException($stmt->error_list);
        }
        $data = self::fetchAll($stmt->get_result());
        $stmt->close();
        return $data;
    }
    
    public static function 查询_发送弹幕次数($ip){
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_ip WHERE ip = ? LIMIT 1");
        $stmt->bind_param('s', $ip);
        if ($stmt->execute() == false){
            throw new DBException($stmt->error_list);
        }
        $data = self::fetchAll($stmt->get_result());
        $stmt->close();
        return $data;

    }
    
    public static function 更新_发送弹幕次数($ip,$time = 'time'){
        $query = "UPDATE danmaku_ip SET c=c+1,time=$time WHERE ip = ?";
        if (is_int($time)) $query = "UPDATE danmaku_ip SET c=1,time=$time WHERE ip = ?";
        $stmt = self::$sql->prepare($query);
        $stmt->bind_param('s', $ip);
        if ($stmt->execute() == false) {
            throw new DBException($stmt->error_list);
        }
        $stmt->close();
    }

    public static function 查询_管理员($id)
    {
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_admin WHERE id = ?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute() == false) {
            throw new DBException($stmt->error_list);
        }
        $data = self::fetchAll($stmt->get_result());
        $stmt->close();
        return $data;
    }

    public static function 查询_管理员登录($name)
    {
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_admin WHERE name = ? LIMIT 1");
        $stmt->bind_param('s', $name);
        if ($stmt->execute() == false) {
            throw new DBException($stmt->error_list);
        }
        $data = self::fetchAll($stmt->get_result());
        $stmt->close();
        return $data;
    }

    private static function fetchAll($obj)
    {
        $data = [];
        if ($obj->num_rows > 0) {
            while ($arr = $obj->fetch_assoc()) {
                $data[] = $arr;
            }
        }
        $obj->free();
        return $data;
    }
} 