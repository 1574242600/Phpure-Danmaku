<?php

if (empty($_COOKIE['csrf'])) {
    setcookie('csrf', md5(hash('sha256', mt_rand()) . mt_rand()), time() + 864000);
}

class admin
{
    public static $id;
    public static $pwd;
    public static $rand;

    public static function 登录($name, $pwd)
    {
        $user = sql::查询_管理员登录($name);
        if (empty($user)) showmessage(-1, '账户不存在或密码错误');
        self::$id = $user[0]['id'];
        self::$rand = $user[0]['rand'];

        self::$pwd = md5(md5($pwd) . $user[0]['rand']);
        if (self::$pwd == $user[0]['pwd']) {
            self::设置cookies(864000);
            return true;
        } else {
            return false;
        }
    }

    private static function 设置cookies($s)
    {
        setcookie('id', self::$id, time() + $s);
        setcookie('pwd-hash', md5(hash('sha256', self::$pwd) . self::$rand), time() + $s);
    }

    public static function is_登录()
    {
        if (!is_numeric($_COOKIE['id'])) return false;
        $user = sql::查询_管理员($_COOKIE['id']);

        print_r($user);
        if (md5(hash('sha256', $user[0]['pwd']) . $user[0]['rand']) == $_COOKIE['pwd-hash']) {
            return true;
        } else {
            return false;
        }
    }

    public static function csrf()
    {
        if ($_COOKIE['csrf'] == $_GET['csrf']) {
            return true;
        }
        return false;
    }

    public static function 删除弹幕($cid)
    {
        //sql::插入_弹幕($data);

    }

    function __destruct()
    {
        global $_config;
        $type = $_config['数据库']['方式'];
        if ($type === 'pdo') sql::$sql = null;
        if ($type === 'sqlite3' or $type === 'mysqli') sql::$sql->close();
    }

}