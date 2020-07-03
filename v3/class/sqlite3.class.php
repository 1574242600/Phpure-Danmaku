<?php
class DB {
    public static $sql ;
    
    function __construct() {
        global $_config ;
        self::数据库连接($_config['数据库']['地址']);
    }

    private static function 数据库连接($path) {
        $sqlite = new SQLite3($path,SQLITE3_OPEN_READWRITE);
        $sqlite->enableExceptions(true);
        self::$sql = $sqlite;
    }
    
    public static function 插入_弹幕($data){
        $stmt = self::$sql->prepare("INSERT OR IGNORE INTO danmaku_list (id, type, text, color, videotime, time) VALUES (:id, :type, :text, :color, :videotime, :time)");
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':text', $data['text']);
        $stmt->bindValue(':color', $data['color']);
        $stmt->bindValue(':videotime', $data['time']);
        @$stmt->bindValue(':time', time());
        $stmt->execute();
    }
    
    public static function 插入_发送弹幕次数($ip){
        $stmt = self::$sql->prepare("INSERT OR IGNORE INTO danmaku_ip (ip, time) VALUES (:ip, :time)");
        $stmt->bindValue(':ip', $ip);
        @$stmt->bindValue(':time', time());
        $stmt->execute();
    }
    
    public static function 查询_弹幕池($id){
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_list WHERE id=:id");
        $stmt->bindValue(':id', $id);
        $data = $stmt->execute();
        $data = self::fetchAll($data);
        return $data;
    }
    
    public static function 查询_发送弹幕次数($ip){
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_ip WHERE ip=:ip LIMIT 1");
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
        $data = $stmt->execute();
        $data = self::fetchAll($data);
        return $data;
    }
    
    public static function 更新_发送弹幕次数($ip,$time = 'time'){
        $query = "UPDATE danmaku_ip SET c=c+1,time=$time WHERE ip = :ip";
        if (is_int($time)) $query = "UPDATE danmaku_ip SET c=1,time=$time WHERE ip = :ip";
        $stmt = self::$sql->prepare($query);
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
    }


    public static function 查询_管理员($id)
    {
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_admin WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $data = $stmt->execute();
        $data = self::fetchAll($data);
        return $data;
    }

    public static function 查询_管理员登录($name)
    {
        $stmt = self::$sql->prepare("SELECT * FROM danmaku_admin WHERE name = :name");
        $stmt->bindValue(':name', $name);
        $data = $stmt->execute();
        $data = self::fetchAll($data);
        return $data;
    }


    private static function fetchAll($obj)
    {
        $data = [];
        while ($arr = $obj->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $arr;
        }
        return $data;
    }
} 